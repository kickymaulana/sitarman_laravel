<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelHasRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $assignments = [
            ['role_id' => 1, 'model_id' => 1],
            ['role_id' => 2, 'model_id' => 4],
            ['role_id' => 2, 'model_id' => 5],
            ['role_id' => 4, 'model_id' => 3],
            ['role_id' => 3, 'model_id' => 3],
            ['role_id' => 5, 'model_id' => 2],
            ['role_id' => 6, 'model_id' => 12],
            ['role_id' => 6, 'model_id' => 15],
            ['role_id' => 6, 'model_id' => 21],
            ['role_id' => 6, 'model_id' => 20],
            ['role_id' => 6, 'model_id' => 22],
            ['role_id' => 6, 'model_id' => 28],
            ['role_id' => 6, 'model_id' => 31],
            ['role_id' => 7, 'model_id' => 17],
            ['role_id' => 7, 'model_id' => 16],
            ['role_id' => 7, 'model_id' => 18],
            ['role_id' => 7, 'model_id' => 10],
            ['role_id' => 7, 'model_id' => 8],
            ['role_id' => 7, 'model_id' => 23],
            ['role_id' => 7, 'model_id' => 30],
            ['role_id' => 7, 'model_id' => 37],
            ['role_id' => 7, 'model_id' => 32],
            ['role_id' => 7, 'model_id' => 33],
            ['role_id' => 7, 'model_id' => 6],
            ['role_id' => 8, 'model_id' => 27],
            ['role_id' => 8, 'model_id' => 25],
            ['role_id' => 8, 'model_id' => 14],
            ['role_id' => 8, 'model_id' => 19],
            ['role_id' => 8, 'model_id' => 13],
            ['role_id' => 8, 'model_id' => 11],
            ['role_id' => 8, 'model_id' => 9],
            ['role_id' => 8, 'model_id' => 24],
            ['role_id' => 8, 'model_id' => 29],
            ['role_id' => 8, 'model_id' => 34],
            ['role_id' => 8, 'model_id' => 36],
            ['role_id' => 8, 'model_id' => 26],
            ['role_id' => 8, 'model_id' => 35],
            ['role_id' => 8, 'model_id' => 7],
        ];

        foreach ($assignments as $assign) {
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id'    => $assign['role_id'],
                    'model_id'   => $assign['model_id'],
                    'model_type' => 'App\Models\User'
                ],
                [] // Tidak ada kolom tambahan yang perlu diupdate
            );
        }
    }
}
