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
        Schema::create('produk_dwa', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->default(1);
            $table->foreignId('density_water_absorption_id')->constrained('density_water_absorption')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customer');
            $table->foreignId('modelsize_id')->constrained('modelsize');
            $table->foreignId('spesifikasi_id')->constrained('spesifikasi');
            $table->date('tgl_produksi');
            $table->string('sample')->nullable()->default('-');
            $table->foreignId('oven_id')->constrained('oven');
            $table->foreignId('jam_keluar_oven_id')->constrained('jam_keluar_oven');
            $table->decimal('ketebalan', 6, 2)->default(0.00);
            $table->decimal('berat_awal', 6, 2)->default(0.00);
            $table->decimal('berat_akhir', 6, 2)->default(0.00);
            $table->decimal('volume', 6, 2)->default(0.00);
            $table->decimal('density', 6, 2)->default(0.00);
            $table->integer('temp')->default(0);
            $table->decimal('palm_wo', 6, 3)->default(0.000);
            $table->decimal('palm_wa', 6, 3)->default(0.000);
            $table->decimal('palm_water', 6, 3)->default(0.000);
            $table->decimal('mc_wo', 6, 3)->default(0.000);
            $table->decimal('mc_wa', 6, 3)->default(0.000);
            $table->decimal('mc_water', 6, 3)->default(0.000);
            $table->decimal('sl_wo', 6, 3)->default(0.000);
            $table->decimal('sl_wa', 6, 3)->default(0.000);
            $table->decimal('sl_water', 6, 3)->default(0.000);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_dwa');
    }
};
