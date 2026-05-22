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
        Schema::create('hasil_thermalshock', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_keluar_oven');
            $table->foreignId('oven_id')->constrained('oven');
            $table->foreignId('jam_keluar_oven_id')->constrained('jam_keluar_oven');
            $table->foreignId('customer_id')->constrained('customer');
            $table->foreignId('modelsize_id')->constrained('modelsize');
            $table->foreignId('tinggi_former_id')->constrained('tinggi_former');
            $table->foreignId('spesifikasi_id')->constrained('spesifikasi');
            $table->string('kode_tanah')->nullable()->default('-');
            $table->enum('180', ['OK', 'NG', 'Belum Tes'])->default('Belum Tes');
            $table->enum('200', ['OK', 'NG', 'Belum Tes'])->default('Belum Tes');
            $table->integer('suhu')->default(0);
            $table->integer('berat_former');
            $table->decimal('thickness', 6, 2)->default(0.00);
            $table->decimal('chemical', 6, 2)->default(0.00);
            $table->decimal('wa_palm', 6, 3)->default(0.000);
            $table->decimal('wa_mc', 6, 3)->default(0.000);
            $table->decimal('wa_sli', 6, 3)->default(0.000);
            $table->decimal('density', 6, 2)->default(0.00);
            $table->decimal('luas_area', 6, 2)->default(0.00);
            $table->integer('visual')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_thermalshock');
    }
};
