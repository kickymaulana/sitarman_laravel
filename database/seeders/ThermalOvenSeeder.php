<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThermalOvenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            ['id' => 1, 'thermal_oven' => 'MESIN 1'],
            ['id' => 2, 'thermal_oven' => 'MESIN 2'],
        ];

        foreach ($datas as $data) {
            \DB::table('thermal_oven')->updateOrInsert(
                ['id' => $data['id']],
                [
                    'thermal_oven' => $data['thermal_oven'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
