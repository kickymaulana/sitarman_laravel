<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id' => 1, 'nama' => 'MOULD'],
            ['id' => 2, 'nama' => 'FILLING'],
            ['id' => 3, 'nama' => 'WASHING'],
            ['id' => 4, 'nama' => 'SPRAY ON KASAR'],
            ['id' => 5, 'nama' => 'CUCI CELUP'],
            ['id' => 6, 'nama' => 'SPRAY ON HALUS'],
            ['id' => 7, 'nama' => 'OVEN'],
            ['id' => 8, 'nama' => 'ASAH / GRATING'],
            ['id' => 9, 'nama' => 'FQC'],
            ['id' => 9, 'nama' => 'OFFICE QC'],
        ];

        foreach ($data as $item) {
            // Menggunakan updateOrInsert agar aman jika dijalankan berkali-kali
            DB::table('departemen')->updateOrInsert(
                ['id' => $item['id']], // Kondisi pengecekan (berdasarkan ID)
                [
                    'nama' => $item['nama'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
