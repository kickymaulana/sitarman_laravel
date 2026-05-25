<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JamKeluarOvenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            ['id' => 1, 'jam_keluar_oven' => '00:00:00'],
            ['id' => 2, 'jam_keluar_oven' => '00:30:00'],
            ['id' => 3, 'jam_keluar_oven' => '01:30:00'],
            ['id' => 4, 'jam_keluar_oven' => '02:30:00'],
            ['id' => 5, 'jam_keluar_oven' => '03:30:00'],
            ['id' => 6, 'jam_keluar_oven' => '04:30:00'],
            ['id' => 7, 'jam_keluar_oven' => '05:30:00'],
            ['id' => 8, 'jam_keluar_oven' => '05:30:00'],
        ];

        foreach ($datas as $data) {
            \DB::table('jam_keluar_oven')->updateOrInsert(
                ['id' => $data['id']],
                [
                    'jam_keluar_oven' => $data['jam_keluar_oven'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
