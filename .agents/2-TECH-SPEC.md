# TECH SPEC — SITARMAN (Thermal Shock Monitoring)

Dokumen spesifikasi teknis hasil analisis kode existing (reverse-engineered), bukan desain baru. Status per 2026-09-04.

## 1. TECH STACK

| Aspek | Pilihan | Catatan |
|---|---|---|
| Runtime | PHP ^8.3 | |
| Framework | Laravel 13 (`laravel/framework ^13.0`) | Skeleton default; tanpada kontrak domain |
| Frontend | Vue 3 (Composition API, `<script setup>`) + Inertia v3 + TS | Pages lazy via `import.meta.glob` |
| Styling | Tailwind CSS v4 (PostCSS) | |
| UI Kit | shadcn-vue (reka-ui): button, card, dialog, alert-dialog, dropdown, checkbox, dsb | |
| Table | TanStack `@tanstack/vue-table` | Terpakai hanya demo `Dashboard/Testing` |
| Icons | @tabler/icons-vue + lucide-vue-next | lucide tidak jelas terpakai di halaman produksi |
| State/Toast | vue-sonner (`<Toaster>` di AuthenticatedLayout) | UI lama masih banyak `alert()`/`confirm()` |
| Auth session | Laravel guard `web` (session), login via username+password | |
| Auth API | Sanctum token (`device_name`) | Duplikasi dengan web; skema lama |
| Otorisasi | spatie/laravel-permission v7 (`HasRoles`), guard `web`, teams off | Hanya role `admin` di-seed |
| Validasi | Inline `$request->validate()` | Tidak ada FormRequest; duplikasi store/update |
| Route helper | tightenco/ziggy (`@routes` blade → `window.Ziggy`) | File `resources/js/ziggy.js` stale, tak dipakai |
| Build | Vite 8 + laravel-vite-plugin + @vitejs/plugin-vue | |
| PDF | barryvdh/laravel-dompdf (dependency) | Tidak ada kode pemakai (PdfController/view telah dihapus) |
| Storage | DB MySQL/MariaDB (driver `mariadb` per .env) | Session/jobs/cache juga DB (`SESSION_DRIVER=database`, dsb.) |
| Lain | axios (via bootstrap), @vueuse/core, zod (terpasang, tak jelas dipakai) | |

## 2. DATABASE DESIGN

DB: MariaDB `sitarman`. Skema aktual 21 tabel dari 17 migration.

### Tabel domain (produktif)
| Tabel | Kolom inti | Keterangan |
|---|---|---|
| `thermal_shock` | 40 kolom (lihat .agents/4-LEGACY-DECODER.md FASE 4) | Tabel dewa: header + 2 siklus uji (180/200) + data produk + hasil |
| `users` | nik (uniq, null), name, username, whatsapp, email, password | `username` **tidak unik**; `departemen_id` di fillable tapi kolom tak ada |
| `customer` | customer, model, spesifikasi, size (semua string) | Denormalisasi; `$incrementing=false` (aneh) |
| `oven` | oven | Master; tanpa relasi balik di model |
| `thermal_pintu` | thermal_pintu | Master; punya `hasMany ThermalShock` |
| `thermal_oven` | thermal_oven | **Yatim** — tak dipakai FK mana pun |
| `tinggi_former` | tinggi_former (int) | Master; seeder 360/380/400/420 |
| `jam_keluar_oven` | jam_keluar_oven (time) | Master; seeder berisi duplikat `05:30:00` (id 7&8) |
| spatie: `permissions`, `roles`, `model_has_roles`, `model_has_permissions`, `role_has_permissions` | | `role_has_permissions` kosong; cache 24 jam |
| framework: users/sessions/cache/cache_locks/jobs/job_batches/failed_jobs/password_reset_tokens/personal_access_tokens | | Standard |

### Relasi kunci (FK nyata)
`thermal_shock.user_id → users` (cascade), `.thermal_pintu_id → thermal_pintu` (cascade), `.oven_id`, `.customer_id`, `.tinggi_former_id`, `.jam_keluar_oven_id` (tanpa cascade). Spatie tables FK cascade. `sessions.user_id` tanpa FK.

### Konsep tak ada di DB (hantu)
`produk`, `modelsize`, `spesifikasi`, `density`, `density_water_absorption`, `hasil_thermal_shock`, `departemen` — dirujuk controller/seed/model tapi **tanpa migration**. Dua seeder orphan (`ModelSize`, `Spesifikasi`) tak dicall DatabaseSeeder.

### Denormalisasi & anomali
- `tanggal_selesai_tembak_200` = `hari_tgl` + `jam_selesai_tembak_200`; dipelihara 3 mekanisme app-layer (backfill migration + event `saving` + helper manual) — tanpa trigger DB.
- Enum berubah via `DB::statement` MODIFY (raw); nilai `Pecah 180` bocor ke enum 200.
- Kolom jam varian 200 nullable; varian 180 NOT NULL `00:00:00`; pasangan kolom 180/200 di-interleave di skema.

## 3. INTERFACE

### 3.1 Backend interface
- **Web**: Inertia. GET → `Inertia::render('Pages/...', props)`; mutasi → `redirect()->route(...)->with(flash)`. Flash key campur `message`/`success`.
- **API** (`routes/api.php`, guard `auth:sanctum`): JSON + `JsonResource`/manual `response()->json`. Tanpa throttle. Modul API dipakai skema **lama** → rusak.
- Resources: `UserResource` (tanpa role/nik), `ThermalShockResource` + `ThermalShockDetailResource` (skema lama → null/broken).

### 3.2 Halaman web (Pages/)
```
Auth: Login.vue, Register.vue
Dashboard: Index.vue (statistik semua role auth)
DaftarPengguna: Index.vue
ThermalShock: Index (list+pilih massal), Create, Edit,
              MenuTembak (antrean/pintu), BulkEditHasil (grid hasil),
              BulkEditSuhu180, BulkEditSuhu200, StrukFilter, StrukRingkasan (cetak)
Master: Customer, Oven, ThermalOven, ThermalPintu, TinggiFormer,
        JamKeluarOven, Roles, Users (semua CRUD Create/Edit/Index)
```

### 3.3 Data kontrak halaman inti
| Page | Props utama |
|---|---|
| Index | `thermalshocks` (paginated), `filters.search` |
| Create/Edit | `lastRecord`/`thermalshock`, 5 list master (`thermalPintus`, `ovens`, `customers`, `tinggiFormers`, `jamKeluarOvens`) |
| BulkEditHasil | `thermalshocks[]` lengkap, `selectedIds` (tak terpakai) |
| BulkEditSuhu180/200 | `thermalshocks` (ringkas), `selectedIds` |
| MenuTembak | `antreanPintu[]` {id, thermal_pintu, sesi_list[], total_antrean} |
| StrukFilter | `ovens` |
| StrukRingkasan | `records[]`, `tanggal` |

### 3.4 Export CSV
Frontend (Index.vue): filter rentang `tanggal_keluar_oven`+jam → GET `thermalshock.getExportData` → backend `whereBetween('tanggal_selesai_tembak_200')` → frontend susun 33 kolom, delimiter `;`, prefix BOM, `text/csv`, nama `Rekap_ThermalShock_{start}_to_{end}.csv`.

## 4. ALUR (FLOW)

### 4.1 Autentikasi
- Web: `Auth::attempt(username+password)` → regenerate session → redirect intended. Register publik → pilih role → assign → auto-login (hole keamanan, lihat §5).
- API: `POST /api/register|/login` → `createToken(device_name)` → header Bearer.

### 4.2 Otorisasi
- Master user/role/oven/thermal-*: middleware `role:admin` di route group (web.php:42).
- Customer: cek `hasAnyRole(['admin','Operator'])` via constructor closure → 403.
- ThermalShock: semua `auth` saja, tanpa ownership check.
- Frontend menampilkan menu berdasarkan `auth.roles` — **tidak sinkron** dgn backend (QC lihat menu admin → 403).

### 4.3 Alur operasi harian
```
Entri baru (Create, prefill lastRecord)          → record ThermalShock lengkap 2 siklus
Antrean (MenuTembak): pilih pintu+sesi           → kumpul id "Belum Tes" → BulkEditHasil
Hasil massal (BulkEditHasil): header global + hasil per row
    watcher: hasil_180=NG → hasil_200='Pecah 180'  → PUT bulkUpdate (Query Builder)
Bulk set parameter sesi 180/200                  → bulkUpdate180/200 (halaman tanpa tombol)
Cetak struk: StrukFilter → process → pluck id → StrukRingkasan → print
Export CSV: filter rentang → getExportData → susun csv di frontend → download
```

### 4.4 Denormalisasi timestamp (jalur tulis)
- Eloquent create/update → event `saving` set `tanggal_selesai_tembak_200`.
- Query Builder bulk (`bulkUpdate`/`bulkUpdate200`) → melewati event → kompensasi manual `syncTanggalSelesai200(ids)`.
- `bulkUpdate180` tidak menyentuh kolom 200 → aman tapi asimetris.
- `bulkReplicate` menyalin nilai lama → duplikat bisa salah ikut range export.

## 5. KEAMANAN

### Risiko aktif (status saat ini)
| Risiko | Lokasi | Catatan |
|---|---|---|
| Eskalasi role via register publik | RegisterController.php:20,36 | Whitelist role tinggi + auto-login; tanpa approval/throttle |
| Login brute-force | web & api | Tanpa rate limit; api group tanpa `throttleApi` |
| Secret hardcode + HTTP | CustomerController.php:98-99 | `RahasiaFQC2026`, `192.168.10.216`, plain HTTP |
| Leak pesan DB | Api/ThermalShockController.php:55,94,107 | `$e->getMessage()` → JSON; tanpa global handler |
| Rename role admin → lockout | RoleController.php update | Tanpa proteksi (destroy sudah diproteksi) |
| Route publik `testing` | web.php:31 | Tanpa auth |
| `/api/register` broken/publik | Api/AuthController.php | Akun penuh + token langsung; kolom NOT NULL kosong |
| Mass assignment | store/update via `$request->all()` | Bergantung `$fillable`; `hasil_180/200` malah tak ada di fillable |
| Inertia share | HandleInertiaRequests | `auth.user` model penuh (password hidden) — wajar; tanpa permission list |

### Postur umum
CSRF aktif default web; password hashed (`casts hashed`); `User` hidden password/remember_token; no global exception handler; HTTP di non-prod (https dipaksa hanya `production`).

## CATATAN TRANSISI
Skema sekarang adalah **dua generasi hidup berdampingan**: generasi 1 (API/Resources/Produk/Density, satu produk per row, kolom `oven/pintu/suhu_testing`) dan generasi 2 (web terpadu 180/200, single-table). Refactor diarahkan ke generasi 2; artefak generasi 1 dibersihkan (lihat `.agents/3-TASKS.md` grup D). Daftar tugas eksekusi: `.agents/3-TASKS.md`.
