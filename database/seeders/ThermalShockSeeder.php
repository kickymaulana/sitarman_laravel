<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ThermalShock;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ThermalShockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil ID yang benar-benar ada di database secara dinamis
        $userId = DB::table('users')->value('id') ?? 1; // Mengambil ID user pertama yang ada
        $ovenId = DB::table('oven')->value('id') ?? 2;  // Mengambil ID oven pertama yang ada

        // Ambil semua kumpulan ID yang valid dari tabel master hasil seeder sebelumnya
        $customerIds = DB::table('customer')->pluck('id')->toArray();
        $tinggiFormerIds = DB::table('tinggi_former')->pluck('id')->toArray();
        $jamKeluarOvenIds = DB::table('jam_keluar_oven')->pluck('id')->toArray();

        // Antisipasi jika database master ternyata masih kosong (fallback data aman)
        if (empty($customerIds)) { $customerIds = [1]; }
        if (empty($tinggiFormerIds)) { $tinggiFormerIds = [1]; }
        if (empty($jamKeluarOvenIds)) { $jamKeluarOvenIds = [1]; }

        // 2. Lakukan looping untuk Pintu 1 sampai Pintu 4
        for ($pintuId = 1; $pintuId <= 4; $pintuId++) {

            // Lakukan looping untuk mengisi tepat 10 data per pintu
            for ($posisi = 1; $posisi <= 10; $posisi++) {

                // Variasi tanggal pengujian & produksi
                $hariTgl = Carbon::now()->format('Y-m-d');
                $tglProduksi = Carbon::now()->subDay()->format('Y-m-d');

                // Variasi hasil test tiruan (OK / NG / Belum Tes)
                $hasil180Option = ['OK', 'NG', 'Belum Tes'][array_rand(['OK', 'NG', 'Belum Tes'])];
                $hasil200Option = ($hasil180Option === 'OK') ? ['OK', 'NG', 'Belum Tes'][array_rand(['OK', 'NG', 'Belum Tes'])] : 'Belum Tes';

                ThermalShock::create([
                    'user_id' => $userId,
                    'thermal_pintu_id' => $pintuId,
                    'hari_tgl' => $hariTgl,

                    // Parameter Pengujian 180°C
                    'suhu_display_180' => 185,
                    'suhu_actual_180' => 185,
                    'suhu_awal_180' => rand(65, 77),
                    'suhu_air_180' => '32/32',
                    'jam_awal_proses_180' => '00:18:00',
                    'jam_capai_suhu_180' => '00:48:00',
                    'jam_mulai_tembak_180' => '00:58:00',
                    'jam_selesai_tembak_180' => '01:02:00',

                    // Parameter Pengujian 200°C
                    'suhu_display_200' => 210,
                    'suhu_actual_200' => 210,
                    'suhu_awal_200' => rand(70, 78),
                    'suhu_air_200' => '32/32',
                    'jam_awal_proses_200' => '01:07:00',
                    'jam_capai_suhu_200' => '01:37:00',
                    'jam_mulai_tembak_200' => '01:47:00',
                    'jam_selesai_tembak_200' => '01:51:00',

                    // Data Produk & Manufaktur (Menggunakan array_rand dari data asli DB)
                    'kode_bakar' => 0,
                    'kode_tanah' => (string) rand(1884, 1896),
                    'oven_id' => $ovenId,
                    'customer_id' => $customerIds[array_rand($customerIds)],
                    'tinggi_former_id' => $tinggiFormerIds[array_rand($tinggiFormerIds)],
                    'jam_keluar_oven_id' => $jamKeluarOvenIds[array_rand($jamKeluarOvenIds)],
                    'sampel' => '-',
                    'berat_former' => rand(634, 955),
                    'tanggal_keluar_oven' => $hariTgl,
                    'tgl_produksi' => $tglProduksi,
                    'posisi_former' => $posisi,

                    // Logika Pengisian Hasil Skor Uji
                    'hasil_test_180' => $hasil180Option,
                    'hasil_180' => ($hasil180Option === 'OK') ? 180 : (($hasil180Option === 'NG') ? 44 : 0),

                    'hasil_test_200' => $hasil200Option,
                    'hasil_200' => ($hasil200Option === 'OK') ? 200 : 0,

                    'keterangan' => '-',
                ]);
            }
        }
    }
}
