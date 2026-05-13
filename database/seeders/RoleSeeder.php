<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menghapus cache permission agar role baru langsung terdeteksi
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['id' => 1], ['name' => 'admin']);
        Role::firstOrCreate(['id' => 2], ['name' => 'Quality Control']);
        Role::firstOrCreate(['id' => 3], ['name' => 'General Manager']);
        Role::firstOrCreate(['id' => 4], ['name' => 'Factory Manager']);
        Role::firstOrCreate(['id' => 5], ['name' => 'QC Manager']);
        Role::firstOrCreate(['id' => 6], ['name' => 'Manager']);
        Role::firstOrCreate(['id' => 7], ['name' => 'Supervisor']);
        Role::firstOrCreate(['id' => 8], ['name' => 'Leader']);
        Role::firstOrCreate(['id' => 9], ['name' => 'Operator']);

    }
}
