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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('thermal_oven_id')->constrained('thermal_oven')->onDelete('cascade');
            $table->foreignId('thermal_pintu_id')->constrained('thermal_pintu')->onDelete('cascade');
            $table->date('hari_tgl'); // Menggunakan date agar formatnya YYYY-MM-DD
            $table->integer('suhu_testing')->default(0);
            $table->integer('suhu_display')->default(0);
            $table->integer('suhu_actual')->default(0);
            $table->time('jam_awal_proses')->default('00:00:00');
            $table->time('jam_capai_suhu')->default('00:00:00');
            $table->integer('suhu_awal')->default(0);
            $table->string('suhu_air')->default('-'); // Misal: "31/32"
            $table->time('jam_mulai_tembak')->default('00:00:00');
            $table->time('jam_selesai_tembak')->default('00:00:00');
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
