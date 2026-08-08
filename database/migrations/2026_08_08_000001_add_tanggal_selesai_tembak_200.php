<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thermal_shock', function (Blueprint $table) {
            $table->timestamp('tanggal_selesai_tembak_200')->nullable()->after('jam_selesai_tembak_200');
        });

        // Backfill data lama: ambil date dari hari_tgl + jam_selesai_tembak_200
        DB::table('thermal_shock')
            ->whereNotNull('jam_selesai_tembak_200')
            ->update([
                'tanggal_selesai_tembak_200' => DB::raw("CONCAT(hari_tgl, ' ', jam_selesai_tembak_200)"),
            ]);
    }

    public function down(): void
    {
        Schema::table('thermal_shock', function (Blueprint $table) {
            $table->dropColumn('tanggal_selesai_tembak_200');
        });
    }
};