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
            ['id' => 1, 'username' => 'D260065', 'name' => 'Admin'],
            ['id' => 2, 'username' => 'K110011', 'name' => 'Afrida Hanum'],
            ['id' => 3, 'username' => '200064',  'name' => 'Parinton Silaen'],
            ['id' => 4, 'username' => '200344',  'name' => 'Marzuki Ilham'],
            ['id' => 5, 'username' => 'K190809', 'name' => 'Sarah halawa'],
            ['id' => 6, 'username' => '130225',  'name' => 'Jamal Mirdad Purba'],
            ['id' => 7, 'username' => 'D110020', 'name' => 'Fahri irawan'],
            ['id' => 8, 'username' => 'K100003', 'name' => 'Sendiana'],
            ['id' => 9, 'username' => 'B230299', 'name' => 'Yuni Arita Panjaitan'],
            ['id' => 10, 'username' => '130049', 'name' => 'Hasan makmur'],
            ['id' => 11, 'username' => 'k080005', 'name' => 'Yudi pratomo'],
            ['id' => 12, 'username' => '110010', 'name' => 'Saut Maruli segala'],
            ['id' => 13, 'username' => 'D100007', 'name' => 'Edi syahputra'],
            ['id' => 14, 'username' => 'D230222', 'name' => 'Muhammad adji irawan'],
            ['id' => 15, 'username' => '6250002', 'name' => 'RAHMAD'],
            ['id' => 16, 'username' => '6290021', 'name' => 'Sahrul usman'],
            ['id' => 17, 'username' => 'K060002', 'name' => 'Khairul rizal'],
            ['id' => 18, 'username' => 'K10008',  'name' => 'Lara Safiti'],
            ['id' => 19, 'username' => 'K120003', 'name' => 'Hariyati'],
            ['id' => 20, 'username' => 'D260061', 'name' => 'Angga Riyandi. S'],
            ['id' => 21, 'username' => 'D250369', 'name' => 'Bahtra Prima Munthe'],
            ['id' => 22, 'username' => 'K160162', 'name' => 'Hasnul Arifin Nasution'],
            ['id' => 23, 'username' => 'D170126', 'name' => 'irfan sahputra'],
            ['id' => 24, 'username' => 'k210969', 'name' => 'hendra'],
            ['id' => 25, 'username' => 'D240179', 'name' => 'Muhammad irwansyah'],
            ['id' => 26, 'username' => 'A180835', 'name' => 'Sayuti pazril silalahi'],
            ['id' => 27, 'username' => 'D250321', 'name' => 'M. Iqbal Nurmansyah Nst'],
            ['id' => 28, 'username' => 'K180653', 'name' => 'Sundy Tjaja'],
            ['id' => 29, 'username' => 'DN110052', 'name' => 'Nursaadah'],
            ['id' => 30, 'username' => 'KA180685', 'name' => 'Yana_Mariana'],
            ['id' => 31, 'username' => 'D260120', 'name' => 'Edinanta Efrata Limbong'],
            ['id' => 32, 'username' => 'K100006', 'name' => 'Dina'],
            ['id' => 33, 'username' => 'K090008', 'name' => 'budiarto'],
            ['id' => 34, 'username' => 'D100005', 'name' => 'MUHAMMAD YAMIN'],
            ['id' => 35, 'username' => 'K140009', 'name' => 'Puspa Indah Sari'],
            ['id' => 36, 'username' => 'D231004', 'name' => 'jefri syahputra'],
            ['id' => 37, 'username' => 'D100002', 'name' => 'Ramadan Syah'],
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
