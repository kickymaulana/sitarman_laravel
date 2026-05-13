<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubDepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Sub untuk WORKING MOULD (ID: 1)
            ['id' => 1, 'departemen_id' => 1, 'nama' => 'WORKING MOULD'],
            ['id' => 2, 'departemen_id' => 2, 'nama' => 'FILLING'],
            ['id' => 3, 'departemen_id' => 3, 'nama' => 'WASHING'],
            ['id' => 4, 'departemen_id' => 4, 'nama' => 'SPRAY ON KASAR'],
            ['id' => 5, 'departemen_id' => 5, 'nama' => 'CUCI CELUP'],
            ['id' => 6, 'departemen_id' => 6, 'nama' => 'SPRAY ON HALUS'],
            ['id' => 7, 'departemen_id' => 7, 'nama' => 'QS / SUSUN'],
            ['id' => 8, 'departemen_id' => 7, 'nama' => 'OVEN'],
            ['id' => 9, 'departemen_id' => 7, 'nama' => 'BONGKAR'],
            ['id' => 10, 'departemen_id' => 8, 'nama' => 'ASAH / GRATING'],
            ['id' => 11, 'departemen_id' => 9, 'nama' => 'FQC'],
        ];

        foreach ($data as $key => $item) {
            DB::table('sub_departemen')->updateOrInsert(
                ['id' => $item['id']], // Unik berdasarkan ID Sub
                [
                    'departemen_id' => $item['departemen_id'],
                    'nama'          => $item['nama'],
                    'urutan'        => $key + 1, // Otomatis berurut 1, 2, 3...
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ]
            );
        }
    }
}
