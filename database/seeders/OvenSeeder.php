<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OvenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            ['id' => 1, 'oven' => 'SAMPLE'],
            ['id' => 2, 'oven' => 'PANJANG 1'],
            ['id' => 3, 'oven' => 'PANJANG 2'],
            ['id' => 4, 'oven' => 'PANJANG 3'],
            ['id' => 5, 'oven' => 'PENDEK 1'],
        ];

        foreach ($datas as $data) {
            \DB::table('oven')->updateOrInsert(
                ['id' => $data['id']],
                [
                    'oven' => $data['oven'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
