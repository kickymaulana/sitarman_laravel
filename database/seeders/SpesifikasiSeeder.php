<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpesifikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            ['id' => 1, 'spesifikasi' => 'SAMPLE'],
            ['id' => 2, 'spesifikasi' => 'FINGER SPRAY'],
        ];

        foreach ($datas as $data) {
            \DB::table('spesifikasi')->updateOrInsert(
                ['id' => $data['id']],
                [
                    'spesifikasi' => $data['spesifikasi'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
