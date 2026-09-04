# LEGACY DECODER — Analisis Codebase `sitarman_laravel`

> Hasil reverse-engineering skill **legacy-decoder**. Tanggal analisis: 2026-09-04.
> Nama aplikasi (dugaan): **SITARMAN** — sistem pencatatan & pengendalian proses **thermal shock / tembak produk** (proses pembakaran, kemungkinan industri bata/keramik).

---

## FASE 1 — DISCOVERY: Stack & Struktur

### Teknologi
| Lapisan | Teknologi |
|---|---|
| Backend | Laravel 13 (`laravel/framework ^13.0`), PHP ^8.3 |
| Frontend | Vue 3 + Inertia.js v3 + TypeScript, Tailwind CSS v4, shadcn-vue (reka-ui), TanStack Table |
| Auth | Session (guard `web`) + Sanctum token (`auth:sanctum`) |
| Otorisasi | spatie/laravel-permission v7 (teams nonaktif, guard `web`) |
| DB aktif | MariaDB (`127.0.0.1:3306`, db `sitarman`) dari `.env` |
| Paket lain | tightenco/ziggy, barryvdh/laravel-dompdf (PDF tak terpakai), league/flysystem-aws-s3-v3 |
| Tooling | Vite 8, @tabler/icons-vue, vue-sonner, zod, laravel/pint, phpunit |

### Struktur folder penting
```
app/
├── Http/Controllers/          24 controller (7 root + Api 3 + Auth 2 + Master 8)
├── Http/Middleware/HandleInertiaRequests.php
├── Http/Resources/            UserResource, ThermalShockResource, ThermalShockDetailResource
├── Models/                    8 model: User, ThermalShock, ThermalPintu, ThermalOven, Oven, JamKeluarOven, TinggiFormer, Customer
└── Providers/AppServiceProvider.php
config/ database/ (17 migration, 14 seeder)
resources/js/  app.ts + components/ui (shadcn) + Pages (Vue per halaman) + Layouts
routes/  web.php (Inertia), api.php (Sanctum), console.php
```

### Model mental
Satu baris `thermal_shock` menyimpan: header batch (hari_tgl, oven, customer, tinggi_former, jam keluar oven) + **dua siklus uji independen** (180°C dan 200°C: suhu display/actual/awal, suhu air, 4 jam proses masing-masing) + data produk (kode_bakar, kode_tanah, sampel, berat, posisi, hasil, keterangan). Total **40 kolom** — "tabel dewa".

---

## FASE 2 — CODEBASE MAPPING

### Entry point & alur request
- Web: `web.php` → Inertia controller → render Vue page di `resources/js/Pages/**`. Semua POST/PUT/DELETE redirect balik + flash.
- API: `api.php` → Sanctum → JSON (`routes/api.php:10-19`), **tanpa throttle** (bootstrap api group tidak memanggil `throttleApi()`).
- Middleware global: `HandleInertiaRequests` di-append (bootstrap/app.php:16-18); alias Spatie `role`/`permission` terdaftar. Provider hanya `AppServiceProvider`. Tanpa global exception handler.
- Inertia share (HandleInertiaRequests.php:38-48): `auth.user` (model penuh, password/remember_token hidden), `auth.roles`, `flash.success/error`. Tidak ada Ziggy props (di-inject via blade `@routes`), tidak ada daftar permission.
- `HandleInertiaRequests` perlu verifikasi ulang — dibaca sekilas riset routing, bukan dibaca penuh (catatan: total 45 baris).

### Frontend mapping (Vue)
- Entry: `app.ts` — Inertia + ZiggyVue plugin; pages lazy-load `import.meta.glob`.
- Sidebar (`AppSidebar.vue`): 3 menu Thermal (Index, MenuTembak, StrukFilter) untuk semua user auth + menu Master ber-gate role (backend `role:admin`; Customer juga boleh Operator).
- Menu → route:
  - Thermal Shock → `thermalshock.index`, Test Tembak → `thermalshock.menuTembak`, Struk Filter → `thermalshock.strukFilter`
  - Master: users/roles/oven/thermal-oven/thermal-pintu/tinggi-former/jam-keluar-oven (admin) + customer (admin/Operator)
- Halaman produksi: Index, Create, Edit, MenuTembak, BulkEditHasil, BulkEditSuhu180, BulkEditSuhu200, StrukFilter, StrukRingkasan (tanpa layout, mode cetak).
- `resources/js/ziggy.js` = **artefak stale** (base `http://localhost/shadcn/public`, tak di-import). Route asli runtime dari blade.

### Dependency graph backend
```
thermal_shock ──belongsTo──► user, thermal_pintu, oven, customer, tinggi_former, jam_keluar_oven
users ──HasRoles (spatie)──► roles ∞──∞ permissions
Master tabel (oven, tinggi_former, dll) = tabel referensi; CRUD repetitive.
API (Api\*) = salinan skema LAMA (produk single-temperature), bertentangan dgn web.
Dead: ProdukController, DensityController, Resources ThermalShock*, Api\ThermalShockController.
```

---

## FASE 3 — BUSINESS LOGIC EXTRACTION

### Terminologi domain (dari kode)
| Istilah | Arti |
|---|---|
| Tembak | proses pembakaran produk |
| Thermal shock 180 / 200 | dua siklus uji suhu (180°C & 200°C) pada satu produk; tiap siklus punya suhu & 4 jam proses sendiri |
| sesi | kolom string nullable = gelombang informal antrean per pintu; NULL tampil "Sesi Default" |
| hasil_test_180/200 | enum status uji: `OK/NG/Belum Tes` + `Tidak Test`; 200 juga punya `Pecah 180` |
| hasil_180/hasil_200 | integer (default 0) — jumlah/angka hasil (bukan enum) |
| tanggal_selesai_tembak_200 | timestamp denormalisasi = `hari_tgl` + `jam_selesai_tembak_200`; basis filter export |
| menuTembak | papan antrean harian per pintu: produk berstatus "Belum Tes" |
| struk | nota/ringkasan cetak per pintu/oven/hari |

### Endpoint web (routes/web.php:114-135) — modul inti
| Route | Method | Fungsi | Render |
|---|---|---|---|
| `thermalshock.index` | GET | list, search 25 kolom, latest, paginate 10 | Index.vue |
| `thermalshock.create` | GET | form; prefill `lastRecord` milik user | Create.vue |
| `thermalshock.store` | POST | validasi + normalisasi jam/suhu; mass create | redirect |
| `thermalshock.edit` / `update` | GET/PUT | form tunggal (tanpa `sesi`!) | Edit.vue |
| `thermalshock.destroy` | DELETE | hapus tunggal | redirect |
| `thermalshock.bulkReplicate` | POST | duplikat row terpilih, reset hasil → antrean ulang | redirect |
| `thermalshock.bulkEdit` | GET | grid hasil massal (`ids` CSV) | BulkEditHasil.vue |
| `thermalshock.bulkUpdate` | PUT | update hasil per row + header global; **Query Builder** | redirect |
| `thermalshock.getExportData` | GET | JSON utk CSV (filter range `tanggal_selesai_tembak_200`) | — |
| `thermalshock.bulkEdit200/bulkUpdate200` | GET/PUT | set suhu+jam sesi 200 massal (sama utk `180`) | BulkEditSuhu* |
| `thermalshock.bulkDestroy` | POST | hapus massal | redirect |
| `thermalshock.menuTembak` | GET | antrean per pintu, group by sesi | MenuTembak.vue |
| `thermalshock.pintuAntrean` | GET | kumpul id belum tes → redirect ke bulkEdit | — |
| `thermalshock.strukFilter` / `strukFilterProcess` | GET/POST | filter cetak (tanggal_keluar_oven/oven/kode_bakar/sampel) → pluck id → redirect struk | StrukFilter.vue |
| `thermalshock.strukRingkasan` | GET | render nota; leftJoin jam_keluar_oven utk urut | StrukRingkasan.vue |

### Alur bisnis utama
1. **Entri data**: Create → `posisi_former` auto-increment (max+1) → record gabungan 2 sesi suhu.
2. **Operasional harian (MenuTembak)**: pilih pintu+sesi → backend kumpul semua id berstatus "Belum Tes" → redirect BulkEditHasil → operator isi hasil tiap produk + header global suhu/jam.
3. **Watcher bisnis (frontend BulkEditHasil.vue:120-133)**: hasil 180 = NG → hasil 200 otomatis `Pecah 180`.
4. **Bulk edit suhu 180/200**: jalur terpisah set parameter satu sesi ke banyak produk. Halaman hanya lewat URL (tombol tidak dirender).
5. **Export CSV**: dibuat **di frontend** (Index.vue:124-247): filter tanggal+jam → GET `getExportData` → susun 33 kolom → Blob + BOM + delimiter `;`. Filter backend `whereBetween('tanggal_selesai_tembak_200', [$from,$to])`.
6. **Struk**: StrukFilter → proses → pluck semua id di query string → strukRingkasan sort jam keluar oven + posisi → `window.print()`.

### Aturan & validasi penting
- `hasil_test_180/200` di store: `required|in:OK,NG,Belum Tes,Tidak Test`. Bulk 200 tambah nilai `Pecah 180`.
- Normalisasi manual store/update: suhu null→0, `suhu_air_*` kosong→`-`, jam kosong→`00:00:00`, jam `HH:mm`→append `:00`.
- Semua CRUD Master pakai validasi inline + `unique` + flash; tidak ada FormRequest (folder `app/Http/Requests` tidak ada), tidak ada Policy.
- Customer master: cek role `admin|Operator` via closure constructor (CustomerController.php:14-23).

### Endpoint API (api.php)
`/api/register`, `/api/login` publik; `auth:sanctum`: `apiResource users`, `apiResource thermal-shocks`, `/logout`.
- **Api\ThermalShockController rusak**: validasi & penulisan kolom skema lama (`oven`, `pintu`, `suhu_testing`, dst.) yang **tidak ada** di tabel → store/update selalu `QueryException`; pesan DB mentah dibocorkan (`$e->getMessage()`, Api/ThermalShockController.php:55).
- **Api\AuthController register broken**: tanpa `username`/`whatsapp` (kolom NOT NULL) → QueryException tak tertangkap.

### Resources
- `UserResource`: id/name/username/whatsapp/email/created_at — tanpa role, tanpa nik.
- `ThermalShockResource` & `ThermalShockDetailResource`: skema **lama** (oven, pintu, details, modelsize) — tak cocok tabel saat ini, akan null/broken.

---

## FASE 4 — DATABASE REVERSE

### Skema aktual (21 tabel dari 17 migration)
**Tabel inti `thermal_shocks` (40 kolom)** — urutan setelah semua migration:
id, user_id(FK cascade), thermal_pintu_id(FK cascade), sesi(null), hari_tgl, suhu_display_180, suhu_display_200(null), suhu_actual_180, suhu_actual_200(null), jam_awal_proses_180, jam_awal_proses_200(null), jam_capai_suhu_180, jam_capai_suhu_200(null), suhu_awal_180, suhu_awal_200(null), suhu_air_180, suhu_air_200(null), jam_mulai_tembak_180, jam_mulai_tembak_200(null), jam_selesai_tembak_180, jam_selesai_tembak_200(null), tanggal_selesai_tembak_200(null), kode_bakar(int), kode_tanah(null), oven_id(FK), customer_id(FK), tinggi_former_id(FK), jam_keluar_oven_id(FK), sampel(null), berat_former(int NOT NULL), tanggal_keluar_oven, tgl_produksi, posisi_former(default 1), hasil_test_180(enum), hasil_180(int null def 0), hasil_test_200(enum), hasil_200(int null def 0), keterangan(null), created_at, updated_at.
Catatan kolom jam varian 200 nullable; varian 180 NOT NULL default `00:00:00`. Kolom 180/200 di-**interleave** per pasangan metrik (susah dibaca).

**Tabel lain**: users (nik unik nullable ditambah belakangan), password_reset_tokens, sessions, cache(+locks), jobs(+batches+failed), customer (denormalisasi: customer/model/spesifikasi/size string), oven, thermal_oven (yatim — tak direferensikan FK), thermal_pintu, tinggi_former, jam_keluar_oven, 5 tabel spatie (permissions, roles, model_has_*, role_has_permissions), personal_access_tokens.

### Relasi (FK nyata)
- thermal_shock → user (cascade), thermal_pintu (cascade), oven, customer, tinggi_former, jam_keluar_oven (tanpa onDelete).
- `sessions.user_id`: foreignId TANPA constrained → tanpa FK. model_has_*/role_has_permissions: FK cascade.
- Master (Oven, TinggiFormer, JamKeluarOven, ThermalOven) model **kosong** — tanpa `hasMany` balik; hanya ThermalPintu punya `hasMany`.

### ER ringkas
```
users 1──∞ thermal_shock        roles ∞──∞ permissions (spatie)
pintu 1──∞ thermal_shock        roles ∞──∞ users (morph)
oven / customer / tinggi_former / jam_keluar_oven  1──∞ thermal_shock
thermal_oven : YATIM (tanpa FK masuk/keluar)
```

### Index / constraint
Unique: users.email & users.nik; spatie (name,guard); failed_jobs.uuid; token dll. **Trigger DB: tidak ada** — denormalisasi `tanggal_selesai_tembak_200` dijaga triplikat di app-layer (backfill migration raw SQL + event `saving` model + helper `syncTanggalSelesai200()` manual pasca bulk raw update).

### Isi seeder
- **RoleSeeder**: 1 role `admin`. **PermissionSeeder**: 8 permission `user-*`/`role-*` via `Permission::create` (bukan firstOrCreate → re-seed error duplicate; tak panggil forgetCachedPermissions). **ModelHasRoleSeeder**: admin→user id 1. **role_has_permissions: KOSONG**.
- **UserSeeder**: user id1 `triyudakhalid` (password123), tanpa nik.
- Master: CustomerSeeder 23, OvenSeeder 5, JamKeluarOvenSeeder 8 (duplikat `05:30:00` id 7&8), ThermalOvenSeeder 2, ThermalPintuSeeder 4, TinggiFormerSeeder 4 (360/380/400/420).
- **ThermalShockSeeder**: 40 baris (4 pintu × 10 posisi). Menulis `hasil_180/200` via `::create` tapi kolom tsb **tak di-fillable** → nilai buang ke default 0.
- **ModelSizeSeeder & SpesifikasiSeeder TIDAK dipanggil** DatabaseSeeder; seed tabel `modelsize`/`spesifikasi` yang **tak pernah di-create migration**.

### Anomali skema
1. Enum `hasil_test_200` awalnya memuat `Pecah 180` — kategori suhu 180 bocor silang ke enum 200.
2. Perubahan enum via `DB::statement` MODIFY (drop-tambah raw SQL, daftar nilai hardcode ganda utk up/down) — rawan typo.
3. `hasil_180`/`hasil_200` ada di DB + dibaca (search/export) tapi **tidak ada di `$fillable`** model → semua jalur Eloquent diam-diam buang nilainya; tidak ada `casts()`.
4. Model `Customer` set `$incrementing=false` padahal PK AI (sisa pola id eksplisit) — id tidak terisi balik saat create.
5. `thermal_oven` tabel hidup tapi yatim; `customer` denormalisasi (string) menggantikan konsep normalisasi `modelsize`/`spesifikasi` yang ditinggalkan.
6. Kolom `tanggal_selesai_tembak_200` redundan (derivasi), dipelihara app-layer dengan 3 mekanisme yang bisa lepas satu sama lain.
7. Model User punya fillable `departemen_id` + relasi `belongsTo Departemen` — **model & kolom `departemen` tidak ada**.

---

## FASE 5a — DEAD CODE AUDIT

### (a) Import di web.php — controller/file TIDAK ADA
`SampleController, FormulirController, DepartemenTerlibatController, TugasProduksiController, PersetujuanManagerController, PdfController` (web.php:10-15), `Master\ModelSizeController` (:19), `Master\SpesifikasiController` (:21). Aman runtime (use tak terpakai) tapi menyisakan kebingungan.

### (b) Controller ADA tapi tak punya route
| Controller | Status | Bukti |
|---|---|---|
| `ProdukController` (373 baris) | dead, broken | import di web.php:25, tanpa route; redirect `route('produk.index')` tak ada; model `Produk/ModelSize/Spesifikasi/HasilThermalShock` + tabel `produk` **tidak ada** |
| `DensityController` (131 baris) | dead, broken | nol referensi route; model `Density/DensityWaterAbsorption` + tabel density* tidak ada; typo kolom `water_absoription_user_id` |
| `LoginController::list_user` | dead | duplikat `destroy` (Auth/LoginController.php:47-54), tak ada route |
| `resources/js/ziggy.js` | dead | stale `localhost/shadcn/public`, tak di-import |
| `resources/views/pdf.blade.php` | dead | `$sampel`, tak ada `return view('pdf')` |
| `Pages/Event/Show.vue` | orphan | `<Switch/>` tanpa import; tanpa route backend |
| `NavDocuments.vue`, `NavSecondary.vue`, `ChartAreaInteractive.vue`, `SectionCards.vue` | dead | tidak dirender siapa pun |
| `resources/js/Pages/Dashboard/Testing.vue` + `DataTable.vue` demo | playground | route `testing` publik; DataTable sort/filter/drag tidak berfungsi |

### (c) Referensi route menunjuk target TIDAK ADA → error runtime
- `DashboardController.php:20,24` redirect ke `route('persetujuan.manager.index')` / `route('tugas.produksi.index')` — RouteNotFoundException untuk user ber-role Manager/QC/Factory/General Manager.
- Semua `.vue` tak pakai route lama (`sample/formulir/produk/density/persetujuan/tugas.produksi/hasilthermalshock.*`) — hasil grep kosong.

### (d) Model dirujuk tapi file tidak ada
`Produk`, `ModelSize`, `Spesifikasi`, `HasilThermalShock`, `Density`, `DensityWaterAbsorption`, `Departemen`, `ThermalShockDetail`. Aman hanya karena pemakainya (controller mati/resource tak dipakai) tak pernah di-load.

---

## FASE 5b — BUG & ANOMALI UTAMA

### Keamanan
1. **Register publik bebas pilih role tinggi** `Manager/Supervisor` lalu auto-login (RegisterController.php:20,36,52) → eskalasi privilege; tanpa throttle.
2. **Login web & API tanpa rate limit** (api group tanpa throttle) → brute-force terbuka.
3. **API key + URL internal hardcoded**: `RahasiaFQC2026`, `http://192.168.10.216/api/tb-spec-fqc1` di CustomerController.php:98-99; transport HTTP polos.
4. **Pesan exception DB mentah bocor ke client** (Api/ThermalShockController.php:55,94,107) tanpa global handler.
5. **Route `testing` publik tanpa auth** (web.php:31).
6. Role admin bisa **di-rename** (RoleController update tanpa proteksi) → seluruh route `role:admin` lockout (web.php:42).
7. `/api/register` publik: akun penuh + token langsung; tanpa username/whatsapp → gagal DB.

### Bugs fungsional
1. **Rule unique Customer patah** — string concat `'required|string|max:255' . $customer->id` (CustomerController.php:73) → jadi `max:2555`, unique tak pernah aktif.
2. **`hasil_180/200` tak di-fillable** → edit tunggal tak pernah menyimpan angka hasil (ThermalShock.php:11-53).
3. **BulkEditHasil watcher bug**: memilih `Pecah 180` manual selalu revert ke `Belum Tes` (BulkEditHasil.vue:127-129).
4. **bulkUpdate rewrite `user_id`** jadi peng-edit (ThermalShockController.php:387,533,594) — semantik rusak.
5. **bulkReplicate tak reset `hasil_180/200` + jam 200 + tanggal_selesai_tembak_200** → duplikat ikut range export lama.
6. **strukFilterProcess pluck semua id → URL query raksasa** (ThermalShockController.php:700-702) — overflow server; strukRingkasan tanpa pagination.
7. Export tooltip bilang filter `updated_at` padahal backend filter `tanggal_selesai_tembak_200` (mismatch).
8. `menuTembak` vs `pintuAntrean` inkonsistensi penanganan sesi NULL vs string literal `"Sesi Default"`.
9. Denormalisasi `tanggal_selesai_tembak_200` NULL bila jam 200 NULL → baris lenyap dari export (mungkin tersembunyi tak sengaja).
10. Search index: rantai `where/orWhere/orWhereHas` tanpa `where(function(){})` (ThermalShockController.php:23-65) — bom saat filter ditambah; LIKE ke kolom date/int → table scan.

### Konsistensi / kebersihan
- **Flash key campur**: `message` vs `success` antar controller.
- **Validasi duplikat literal** store vs update di tiap controller; tanpa FormRequest.
- Pola jam `HH:mm`→`:00` diulang 4+ tempat; `formatTimeInput` duplikat 5× di Vue; `useDropdown` duplikat; BulkEditSuhu180≈BulkEditSuhu200 copy-paste.
- **Role names inkonsisten 3 lapis**: seeder `admin`; register `Manager/Supervisor/Leader/Operator`; sidebar cek `Quality Control`; dashboard cek `QC Manager/Factory Manager/General Manager`. **Hanya role `admin` yang di-seed**.
- **Sidebar vs backend mismatch**: QC lihat menu admin tapi route `role:admin` → 403.
- NavMain highlight mati: root `'Thermalshock'` vs component `'ThermalShock/...'` (kapital S).
- AuthenticatedLayout.vue:13 `console.log(flashSuccess)` sisa debug.
- Index.vue tombol bulkEdit180/200 = dead (tak dirender); halaman BulkEditSuhu* cuma lewat URL.
- Create vs Edit inkonsisten: `sesi` ada di Create, hilang di Edit; `.number` cuma di blok 200; label "Auto increment" tapi editable.
- Edit.vue: relasi tak ikut props (model polos) → Vue terima id saja.
- strukRingkasan double-load `jamKeluarOven` (eager + leftJoin).
- bulkUpdate200 `suhu_air_200` rule `required` kontradiksi normalisasi `?: '-'`.
- `@ts-ignore` 17× di file ThermalShock.
- Role casing campur (`admin` vs `Quality Control`); string match tanpa normalisasi.

---

## FASE 6 — KESIMPULAN & REKOMENDASI

### Profil proyek
Aplikasi berjalan normal di jalur inti: **ThermalShock terpadu (web) + master CRUD**. Sisa arsitektur fase-1 (Produk/Density/API-skema-lama/resources-lama/Register-Departemen) ditinggalkan sebagian tapi **belum dibersihkan** → repo dipenuhi controller/model/route-reference hantu.

### Titik rawan (complexity)
1. `ThermalShockController` — 725 baris, 20 method, semua concern di satu kelas, tanpa FormRequest/Service.
2. Alur **bulk hasil** (header-global × per-row, update raw vs Eloquent, sync timestamp manual, interplay 2 enum).
3. Alur **struk** (filter→pluck→redirect URL raksasa→join→render, tanpa pagination).
4. Alur **export** (bergantung denormalisasi yang bisa NULL/kadaluarsa).

### Prioritas rekomendasi
1. **P2 tinggi**: tutup lubang keamanan — batasi role registrasi publik, throttle login, pindah API key ke env+HTTPS, hapus exception leak, proteksi rename role `admin`, matikan route `testing`.
2. **P2 bug**: fix unique-rule Customer, masukkan `hasil_180/200` ke fillable, perbaiki watcher `Pecah 180`, berhenti rewrite `user_id`, reset penuh di bulkReplicate, solusi struk tanpa URL raksasa.
3. **P3 refactor**: bersihkan seluruh dead code (import web.php, Produk/Density/API-lama/Resources-lama/ziggy.js/pdf.blade.php/Event-show/file demo), seragamkan role, FormRequest + service layer + observer timestamp.
4. Lanjut ke skill `implement-task` (mengerjakan `.agents/3-TASKS.md`) atau `verify`.
