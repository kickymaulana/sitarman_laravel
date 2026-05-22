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
            $table->date('tanggal_keluar_oven')->nullable();
            $table->foreignId('oven_id')->nullable()->constrained('oven')->onDelete('set null');
            $table->foreignId('jam_keluar_oven_id')->nullable()->constrained('jam_keluar_oven')->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained('customer')->onDelete('set null');
            $table->foreignId('modelsize_id')->nullable()->constrained('modelsize')->onDelete('set null');
            $table->foreignId('tinggi_former_id')->nullable()->constrained('tinggi_former')->onDelete('set null');
            $table->foreignId('spesifikasi_id')->nullable()->constrained('spesifikasi')->onDelete('set null');

            $table->string('kode_tanah')->nullable()->default('-');
            $table->enum('suhu_180', ['OK', 'NG', 'Belum Tes'])->default('Belum Tes');
            $table->enum('suhu_200', ['OK', 'NG', 'Belum Tes'])->default('Belum Tes');
            $table->integer('suhu')->default(0);
            $table->integer('berat_former')->default(0);

            // Nilai-nilai fisik dari DWA atau input manual
            $table->decimal('thickness', 6, 2)->default(0.00)->nullable();
            $table->decimal('chemical', 6, 2)->default(0.00)->nullable();
            $table->decimal('wa_palm', 6, 3)->default(0.000)->nullable();
            $table->decimal('wa_mc', 6, 3)->default(0.000)->nullable();
            $table->decimal('wa_sli', 6, 3)->default(0.000)->nullable();
            $table->decimal('density', 6, 2)->default(0.00)->nullable();

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
