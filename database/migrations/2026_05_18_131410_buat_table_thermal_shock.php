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
            // Data Utama/Header Thermal Shock
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('thermal_pintu_id')->constrained('thermal_pintu')->onDelete('cascade');
            $table->date('hari_tgl');
            $table->integer('suhu_display_180')->default(0);
            $table->integer('suhu_display_200')->default(0);
            $table->integer('suhu_actual_180')->default(0);
            $table->integer('suhu_actual_200')->default(0);
            $table->time('jam_awal_proses_180')->default('00:00:00');
            $table->time('jam_awal_proses_200')->default('00:00:00');
            $table->time('jam_capai_suhu_180')->default('00:00:00');
            $table->time('jam_capai_suhu_200')->default('00:00:00');
            $table->integer('suhu_awal_180')->default(0);
            $table->integer('suhu_awal_200')->default(0);
            $table->string('suhu_air_180')->default('-');
            $table->string('suhu_air_200')->default('-');
            $table->time('jam_mulai_tembak_180')->default('00:00:00');
            $table->time('jam_mulai_tembak_200')->default('00:00:00');
            $table->time('jam_selesai_tembak_180')->default('00:00:00');
            $table->time('jam_selesai_tembak_200')->default('00:00:00');

            // Data Produk yang disatukan
            $table->integer('kode_bakar')->nullable()->default(0);
            $table->string('kode_tanah')->nullable()->default('-');
            $table->foreignId('oven_id')->constrained('oven');
            $table->foreignId('customer_id')->constrained('customer');
            $table->foreignId('tinggi_former_id')->constrained('tinggi_former');
            $table->foreignId('jam_keluar_oven_id')->constrained('jam_keluar_oven');
            $table->string('sampel')->nullable()->default('-');
            $table->integer('berat_former');
            $table->date('tanggal_keluar_oven');
            $table->date('tgl_produksi');
            $table->integer('posisi_former')->default(1);
            $table->enum('hasil_test_180', ['OK', 'NG', 'Belum Tes'])->default('Belum Tes');
            $table->enum('hasil_test_200', ['OK', 'NG', 'Belum Tes'])->default('Belum Tes');
            $table->string('keterangan')->nullable()->default('-');
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
