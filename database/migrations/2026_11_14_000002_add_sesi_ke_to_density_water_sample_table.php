<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('density_water_sample', function (Blueprint $table) {
            $table->unsignedInteger('sesi_ke')->default(1)->after('tgl_test');
        });

        DB::table('density_water_sample')->update(['sesi_ke' => 1]);

        Schema::table('density_water_sample', function (Blueprint $table) {
            $table->dropUnique('density_water_sample_tgl_test_urutan_unique');
            $table->unique(['tgl_test', 'sesi_ke', 'urutan']);
            $table->index(['tgl_test', 'sesi_ke', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('density_water_sample', function (Blueprint $table) {
            $table->dropIndex('density_water_sample_tgl_test_sesi_ke_status_index');
            $table->dropUnique('density_water_sample_tgl_test_sesi_ke_urutan_unique');
            $table->unique(['tgl_test', 'urutan']);
            $table->dropColumn('sesi_ke');
        });
    }
};
