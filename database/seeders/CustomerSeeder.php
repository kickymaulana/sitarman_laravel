<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            ['id' => 1, 'customer' => 'SAMPLE'],
            ['id' => 2, 'customer' => 'SRITRANG'],
            ['id' => 3, 'customer' => 'HARTALEGA'],
        ];

        foreach ($datas as $data) {
            \DB::table('customer')->updateOrInsert(
                ['id' => $data['id']],
                [
                    'customer' => $data['customer'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
