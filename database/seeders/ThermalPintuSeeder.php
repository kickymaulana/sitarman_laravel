<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThermalPintuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            ['id' => 1, 'thermal_pintu' => 'PINTU 1'],
            ['id' => 2, 'thermal_pintu' => 'PINTU 2'],
            ['id' => 3, 'thermal_pintu' => 'PINTU 3'],
            ['id' => 4, 'thermal_pintu' => 'PINTU 4'],
        ];

        foreach ($datas as $data) {
            \DB::table('thermal_pintu')->updateOrInsert(
                ['id' => $data['id']],
                [
                    'thermal_pintu' => $data['thermal_pintu'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
