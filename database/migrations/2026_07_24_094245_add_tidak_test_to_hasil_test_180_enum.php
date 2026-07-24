<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE thermal_shock MODIFY COLUMN hasil_test_180 ENUM('OK', 'NG', 'Belum Tes', 'Tidak Test') DEFAULT 'Belum Tes'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE thermal_shock MODIFY COLUMN hasil_test_180 ENUM('OK', 'NG', 'Belum Tes') DEFAULT 'Belum Tes'");
    }
};
