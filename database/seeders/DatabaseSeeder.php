<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(UserSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(ModelHasRoleSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(ModelSizeSeeder::class);
        $this->call(OvenSeeder::class);
        $this->call(SpesifikasiSeeder::class);
        $this->call(ThermalOvenSeeder::class);
        $this->call(ThermalPintuSeeder::class);
        $this->call(TinggiFormerSeeder::class);
        $this->call(JamKeluarOvenSeeder::class);
    }
}
