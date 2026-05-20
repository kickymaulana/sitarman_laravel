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
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thermal_shock_id')->constrained('thermal_shock')->onDelete('cascade');
            $table->integer('kode_bakar')->nullable();
            $table->string('kode_tanah')->nullable();
            $table->foreignId('oven_id')->constrained('oven');
            $table->foreignId('customer_id')->constrained('customer');
            $table->foreignId('modelsize_id')->constrained('modelsize');
            $table->foreignId('spesifikasi_id')->constrained('spesifikasi');
            $table->foreignId('tinggi_former_id')->constrained('tinggi_former');
            $table->foreignId('jam_keluar_oven_id')->constrained('jam_keluar_oven');
            $table->string('sampel')->nullable()->default('-');
            $table->integer('berat_former');
            $table->date('tanggal_keluar_oven');
            $table->date('tgl_produksi');
            $table->integer('posisi_former')->default(1);
            $table->enum('hasil_test', ['OK', 'NG', 'Belum Tes'])->default('Belum Tes');
            $table->integer('suhu_actual')->nullable()->default(0);
            $table->string('keterangan')->nullable()->default('-');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
