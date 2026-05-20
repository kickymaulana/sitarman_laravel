<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModelSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            ['id' => 1, 'customer_id' => 1, 'modelsize' => 'SAMPLE'],
            ['id' => 2, 'customer_id' => 2, 'modelsize' => '36/S'],
            ['id' => 3, 'customer_id' => 2, 'modelsize' => '36/M'],
            ['id' => 4, 'customer_id' => 3, 'modelsize' => 'HA03/S S2.5'],
        ];

        foreach ($datas as $data) {
            \DB::table('modelsize')->updateOrInsert(
                ['id' => $data['id']],
                [
                    'customer_id' => $data['customer_id'],
                    'modelsize' => $data['modelsize'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
