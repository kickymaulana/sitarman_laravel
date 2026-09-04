# TASK LIST — sitarman_laravel

Berdasarkan analisis `.agents/4-LEGACY-DECODER.md`. Grup: [KEAMANAN] → [BUG] → [REFACTOR] → [CLEANUP DEAD CODE] → [FRONTEND]. Tiap task: cek & uji setelah implementasi.

## A. KEAMANAN (kerjakan pertama)

- [ ] **A1** Batasi role pada register publik. `RegisterController.php:20,36` izinkan `Manager/Supervisor` untuk siapa pun → ganti whitelist ke role aman (mis. `Operator`) atau butuh approval; hapus auto-login bila belum disetujui. Hindari eskalasi privilege.
- [ ] **A2** Tambah rate limit login: web (`throttle`) + API (`throttleApi` di bootstrap/app.php api group, saat ini kosong). Login brute-force terbuka.
- [ ] **A3** Pindahkan API key + URL ke config/.env. `CustomerController.php:98-99`: `RahasiaFQC2026` + `http://192.168.10.216/...` hardcode di source; gunakan HTTPS. Taruh di `config/services.php` + `.env`.
- [ ] **A4** Hentikan kebocoran exception DB mentah. `Api/ThermalShockController.php:55,94,107` kirim `$e->getMessage()` → hapus atau mask; tambah global exception handler di `bootstrap/app.php`.
- [ ] **A5** Proteksi rename role `admin`. `RoleController.php:69-77` update bebas rename → prevent nama `admin` diganti (lockout seluruh route `role:admin`).
- [ ] **A6** Matikan/amankan route `testing` publik (`web.php:31`) — hapus route + file demo atau beri middleware auth.
- [ ] **A7** Perbaiki `/api/register` (`Api/AuthController.php:12-33`): tambah `username`+`whatsapp` (NOT NULL di DB) atau pastikan default; pertimbangkan hapus jika tak dipakai.

## B. BUG FUNGSIONAL

- [ ] **B1** Fix rule unique Customer patah. `CustomerController.php:73`: string concat `'required|string|max:255' . $customer->id` → rule jadi `max:2555`, `unique` tak pernah aktif. Susun rule benar (`unique:customer,customer,'.$id`).
- [ ] **B2** Masukkan `hasil_180` & `hasil_200` ke `$fillable` model `ThermalShock` (ThermalShock.php:11-53). Saat ini edit tunggal & seed buang nilai; tambah `casts()`.
- [ ] **B3** Perbaiki watcher `Pecah 180` (BulkEditHasil.vue:127-129): pilih `Pecah 180` manual selalu revert ke `Belum Tes` → ganti logic agar pilihan manual sah.
- [ ] **B4** Berhenti rewrite `user_id` pada bulk update (`ThermalShockController.php:387,533,594`). Bila perlu pencatat edit terakhir → kolom baru `updated_by`.
- [ ] **B5** `bulkReplicate` reset penuh (`ThermalShockController.php:281-287`): reset `hasil_180/200`, jam 200, `tanggal_selesai_tembak_200` agar duplikat tak ikut range export lama.
- [ ] **B6** Hindari URL query raksasa struk (`ThermalShockController.php:700-702` pluck semua id): kirim kriteria filter ke strukRingkasan (query berdasar tanggal/oven/kode_bakar/sampel) atau simpan id sesi.
- [ ] **B7** Selaraskan tooltip export (Index.vue:319,330 bilang `updated_at`) dengan filter nyata `tanggal_selesai_tembak_200`; atau ganti filter export.
- [ ] **B8** Bungkus rantai search `orWhere`/`orWhereHas` dengan `where(function(){})` (ThermalShockController.php:23-65); hindari LIKE ke kolom date/int.
- [ ] **B9** Fix model `Customer::$incrementing=false` (Customer.php:14) — id tak terisi balik saat create; selaraskan dengan sync id eksternal.
- [ ] **B10** Selaraskan enum & penanganan `sesi` NULL vs literal `"Sesi Default"` antara `menuTembak` (:638) dan `pintuAntrean` (:666-670).

## C. REFACTOR BACKEND

- [ ] **C1** Pecah `ThermalShockController` (725 baris, 20 method): pindah validasi → FormRequest; logika bulk/normalisasi jam → Service/Action; hapus duplikasi validasi store vs update.
- [ ] **C2** Kelola `tanggal_selesai_tembak_200` via satu mekanisme (observer `saving`/`updated` + guard) sehingga `syncTanggalSelesai200()` manual & backfill migration tidak triplikat dan jalur raw update tak bisa terlewat.
- [ ] **C3** Seragamkan role. Hanya `admin` yang di-seed; kode campur `Manager/Supervisor/Leader/Operator` (register), `Quality Control` (sidebar), `QC Manager/Factory Manager` (dashboard). Tetapkan satu master role + seeder lengkap.
- [ ] **C4** Fix redirect role di `DashboardController.php:20,24` (`persetujuan.manager.index`/`tugas.produksi.index` tak ada) — buang atau ganti rute valid.
- [ ] **C5** Seragamkan flash key (`message` vs `success`) + konsisten di seluruh controller.
- [ ] **C6** Bereskan relasi rusak: `User::departemen()` (model & kolom tak ada), `Customer::hasMany ThermalShockDetail` (model tak ada) → hapus atau bangun.
- [ ] **C7** `bulkUpdate200` normalisasi `suhu_air_200` kontradiksi rule `required` (:528) — samakan pola dengan bulk generic (`filled()`-conditional).
- [ ] **C8** Tambahkan cek kepemilikan/otorisasi konsisten (Policy) utk resource ThermalShock (saat ini semua `auth` tanpa ownership check).
- [ ] **C9** Tabel yatim / duplikat seeder: `thermal_oven` (pakai di domain atau drop), `JamKeluarOvenSeeder` id 7&8 duplikat `05:30:00`, `PermissionSeeder` non-idempotent (ganti firstOrCreate + forgetCachedPermissions).

## D. CLEANUP DEAD CODE

- [ ] **D1** Hapus 8 import controller hantu di `routes/web.php:10-15,19,21` (Sample, Formulir, DepartemenTerlibat, TugasProduksi, PersetujuanManager, Pdf, ModelSize, Spesifikasi).
- [ ] **D2** Hapus atau hidupkan `ProdukController` (373 baris) + `DensityController` (131 baris): model & tabel pendukungnya (`produk`, `modelsize`, `spesifikasi`, `density*`, `hasil_thermal_shock`) tidak ada; redirect-nya ke route yang tak terdefinisi.
- [ ] **D3** Hapus/migrasi `Api\ThermalShockController` (skema lama `oven/pintu/suhu_testing`) yang bertentangan dengan skema terpadu web.
- [ ] **D4** Hapus/migrasi `ThermalShockResource` + `ThermalShockDetailResource` (field skema lama) + `Api\UserController` bila API users tak dipakai.
- [ ] **D5** Hapus `LoginController::list_user` (duplikat destroy), `resources/js/ziggy.js` (stale), `resources/views/pdf.blade.php` (tanpa pemakai), `Pages/Event/Show.vue` (orphan).
- [ ] **D6** Hapus komponen frontend demo yang tak dirender: `NavDocuments.vue`, `NavSecondary.vue`, `ChartAreaInteractive.vue`, `SectionCards.vue`, halaman `Dashboard/Testing.vue` + `DataTable.vue` + `DraggableRow/DragHandle` bila testing dihapus (A6). Lepas dependency dnd-kit-vue bila tak terpakai.

## E. FRONTEND

- [ ] **E1** Selaraskan sidebar & backend: menu QC (`AppSidebar.vue:40-42`) vs route `role:admin` → tampilkan menu sesuai role riil atau buka role backend.
- [ ] **E2** Fix NavMain active state: root `'Thermalshock'` vs component `'ThermalShock/...'` → highlight tak pernah nyala.
- [ ] **E3** Hapus `console.log` debug (AuthenticatedLayout.vue:13); hapus computed mati `canAccessTugasProduksi`/`canAccessManagerApproval` (AppSidebar.vue:52-64).
- [ ] **E4** Ekstrak duplikasi: `formatTimeInput` (5×), `useDropdown` (2×), blok form 180/200 → composable/komponen bersama; gabung `BulkEditSuhu180` & `BulkEditSuhu200` (hampir identik) jadi satu komponen param.
- [ ] **E5** Bersihkan `@ts-ignore` (17×) dengan tipe Inertia yang benar; lengkapi interface `BulkEditHasil` (field `kode_bakar/kode_tanah/sampel/berat_former`).
- [ ] **E6** Konsistenkan Create vs Edit: `sesi` hilang di Edit; `v-model.number` hanya blok 200; `posisi_former` label "Auto increment" tapi editable.
- [ ] **E7** Rendermkan/matikan tombol `bulkEdit180/200` di Index.vue:97-109 (dead; halaman cuma bisa lewat URL).
- [ ] **E8** Ganti `alert()`/`confirm()` di Index.vue dengan sonner/komponen dialog yang sudah tersedia.

## PRIORITAS EKSEKUSI
1. A1-A7 (keamanan)
2. B1-B6 (bug yang menyentuh data)
3. D1-D5 (hanya setelah fitur dipastikan tak dipakai; tanya user untuk hapus-vs-arsip Produk/Density/API)
4. C + E sisanya bertahap per sprint

> Sebelum hapus Produk/Density/API/Resources lama (D2-D4): konfirmasi ke user bahwa tak ada konsumen eksternal/mobile yang masih memakai API.
