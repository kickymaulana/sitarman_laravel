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
        Schema::create('thermal_shock_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thermal_shock_id')->constrained('thermal_shock')->onDelete('cascade');

            // Pastikan dua baris ini ada:
            $table->foreignId('customer_id')->constrained('customer');
            $table->foreignId('modelsize_id')->constrained('modelsize');

            $table->string('oven_kode_tanah');
            $table->string('spesifikasi');
            $table->integer('berat_former');
            $table->date('tanggal_keluar_oven');
            $table->date('tgl_produksi');
            $table->integer('posisi_former');
            $table->enum('hasil_test', ['OK', 'NG']);
            $table->text('keterangan')->nullable();
            $table->string('paraf_qc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thermal_shock_detail');
    }
};
