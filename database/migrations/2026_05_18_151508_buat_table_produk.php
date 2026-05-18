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
            $table->string('kode_tanah');
            $table->foreignId('oven_id')->constrained('oven');
            $table->foreignId('customer_id')->constrained('customer');
            $table->foreignId('modelsize_id')->constrained('modelsize');
            $table->foreignId('spesifikasi_id')->constrained('spesifikasi');
            $table->string('sampel')->default('-');
            $table->integer('berat_former');
            $table->date('tanggal_keluar_oven');
            $table->date('tgl_produksi');
            $table->integer('posisi_former');
            $table->enum('hasil_test', ['OK', 'NG']);
            $table->integer('suhu_actual');
            $table->string('keterangan')->nullable();
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
