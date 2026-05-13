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
        // Perhatikan penambahan kurung siku [ di awal dan ] di akhir daftar
        $users = [
            ['id' => 1, 'username' => 'D260065', 'name' => 'Admin', 'departemen_id' => 9],
            ['id' => 2, 'username' => 'K110011', 'name' => 'Afrida Hanum', 'departemen_id' => 9],
            ['id' => 3, 'username' => '200064',  'name' => 'Parinton Silaen', 'departemen_id' => 9],
            ['id' => 4, 'username' => '200344',  'name' => 'Marzuki Ilham', 'departemen_id' => 9],
            ['id' => 5, 'username' => 'K190809', 'name' => 'Sarah halawa', 'departemen_id' => 9],
            ['id' => 6, 'username' => '130225',  'name' => 'Jamal Mirdad Purba', 'departemen_id' => 1],
            ['id' => 7, 'username' => 'D110020', 'name' => 'Fahri irawan', 'departemen_id' => 1],
            ['id' => 8, 'username' => 'K100003', 'name' => 'Sendiana', 'departemen_id' => 5],
            ['id' => 9, 'username' => 'B230299', 'name' => 'Yuni Arita Panjaitan', 'departemen_id' => 5],
            ['id' => 10, 'username' => '130049', 'name' => 'Hasan makmur', 'departemen_id' => 4],
            ['id' => 11, 'username' => 'k080005', 'name' => 'Yudi pratomo', 'departemen_id' => 4],
            ['id' => 12, 'username' => '110010', 'name' => 'Saut Maruli segala', 'departemen_id' => 1],
            ['id' => 13, 'username' => 'D100007', 'name' => 'Edi syahputra', 'departemen_id' => 4],
            ['id' => 14, 'username' => 'D230222', 'name' => 'Muhammad adji irawan', 'departemen_id' => 2],
            ['id' => 15, 'username' => '6250002', 'name' => 'RAHMAD', 'departemen_id' => 2],
            ['id' => 16, 'username' => '6290021', 'name' => 'Sahrul usman', 'departemen_id' => 2],
            ['id' => 17, 'username' => 'K060002', 'name' => 'Khairul rizal', 'departemen_id' => 1],
            ['id' => 18, 'username' => 'K10008',  'name' => 'Lara Safiti', 'departemen_id' => 3],
            ['id' => 19, 'username' => 'K120003', 'name' => 'Hariyati', 'departemen_id' => 3],
            ['id' => 20, 'username' => 'D260061', 'name' => 'Angga Riyandi. S', 'departemen_id' => 5],
            ['id' => 21, 'username' => 'D250369', 'name' => 'Bahtra Prima Munthe', 'departemen_id' => 4],
            ['id' => 22, 'username' => 'K160162', 'name' => 'Hasnul Arifin Nasution', 'departemen_id' => 6],
            ['id' => 23, 'username' => 'D170126', 'name' => 'irfan sahputra', 'departemen_id' => 6],
            ['id' => 24, 'username' => 'k210969', 'name' => 'hendra', 'departemen_id' => 6],
            ['id' => 25, 'username' => 'D240179', 'name' => 'Muhammad irwansyah', 'departemen_id' => 1],
            ['id' => 26, 'username' => 'A180835', 'name' => 'Sayuti pazril silalahi', 'departemen_id' => 8],
            ['id' => 27, 'username' => 'D250321', 'name' => 'M. Iqbal Nurmansyah Nst', 'departemen_id' => 8], // Perbaikan di sini
            ['id' => 28, 'username' => 'K180653', 'name' => 'Sundy Tjaja', 'departemen_id' => 7],
            ['id' => 29, 'username' => 'DN110052', 'name' => 'Nursaadah', 'departemen_id' => 7],
            ['id' => 30, 'username' => 'KA180685', 'name' => 'Yana_Mariana', 'departemen_id' => 7],
            ['id' => 31, 'username' => 'D260120', 'name' => 'Edinanta Efrata Limbong', 'departemen_id' => 8],
            ['id' => 32, 'username' => 'K100006', 'name' => 'Dina', 'departemen_id' => 9],
            ['id' => 33, 'username' => 'K090008', 'name' => 'budiarto', 'departemen_id' => 7],
            ['id' => 34, 'username' => 'D100005', 'name' => 'MUHAMMAD YAMIN', 'departemen_id' => 7],
            ['id' => 35, 'username' => 'K140009', 'name' => 'Puspa Indah Sari', 'departemen_id' => 9],
            ['id' => 36, 'username' => 'D231004', 'name' => 'jefri syahputra', 'departemen_id' => 8],
            ['id' => 37, 'username' => 'D100002', 'name' => 'Ramadan Syah', 'departemen_id' => 8],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['username' => $user['username']],
                [
                    'name' => $user['name'],
                    'departemen_id' => $user['departemen_id'],
                    'whatsapp' => '08000000000',
                    'email' => strtolower($user['username']) . '@perusahaan.com',
                    'email_verified_at' => Carbon::now(),
                    'password' => Hash::make('password123'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
