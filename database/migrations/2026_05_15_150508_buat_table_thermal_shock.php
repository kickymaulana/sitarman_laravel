<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('thermal_shock', function (Blueprint $table) {
            $table->id();
            $table->date('hari_tgl'); // Menggunakan date agar formatnya YYYY-MM-DD
            $table->integer('suhu_testing');
            $table->string('suhu_motor')->nullable();
            $table->integer('suhu_display');
            $table->integer('suhu_actual');
            $table->time('jam_awal_proses');
            $table->time('jam_capai_suhu');
            $table->integer('suhu_awal');
            $table->string('suhu_air'); // Misal: "31/32"
            $table->time('jam_mulai_tembak');
            $table->time('jam_selesai_tembak');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thermal_shock');
    }
};
