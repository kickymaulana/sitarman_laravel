<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar user tanpa kolom departemen_id
        $users = [
            ['id' => 1, 'username' => 'triyudakhalid', 'name' => 'Tri Yuda Khalid'],
        ];

        foreach ($users as $user) {
            \DB::table('users')->updateOrInsert(
                ['username' => $user['username']],
                [
                    'name' => $user['name'],
                    'whatsapp' => '6280000000' . $user['id'], // Nomor dummy unik (format 628...)
                    'email' => strtolower($user['username']) . '@sitarman.com',
                    'email_verified_at' => now(),
                    'password' => \Hash::make('password123'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
