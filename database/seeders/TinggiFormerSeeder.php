<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TinggiFormerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            ['id' => 1, 'tinggi_former' => 360],
            ['id' => 2, 'tinggi_former' => 380],
            ['id' => 3, 'tinggi_former' => 400],
            ['id' => 4, 'tinggi_former' => 420],
        ];

        foreach ($datas as $data) {
            \DB::table('tinggi_former')->updateOrInsert(
                ['id' => $data['id']],
                [
                    'tinggi_former' => $data['tinggi_former'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
