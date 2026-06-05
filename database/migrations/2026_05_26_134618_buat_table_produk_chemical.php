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
        Schema::create('produk_chemical', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chemical_id')->constrained('chemical')->onDelete('cascade');
            $table->date('tgl_produksi');
            $table->foreignId('customer_id')->constrained('customer');
            $table->foreignId('modelsize_id')->constrained('modelsize');
            $table->foreignId('oven_id')->constrained('oven');
            $table->date('tanggal_keluar_oven');
            $table->foreignId('jam_keluar_oven_id')->constrained('jam_keluar_oven');
            $table->string('gambar')->nullable();
            $table->string('sample')->nullable()->default('-');
            $table->decimal('ketebalan_mm', 6, 2)->default(0.00);//diisi user
            $table->decimal('ketebalan_dm', 6, 4)->default(0.0000);
            $table->decimal('berat_awal', 6, 3)->default(0.000);//diisi user
            $table->decimal('berat_akhir', 6, 3)->default(0.000);//diisi user
            $table->decimal('density', 6, 2)->default(0.00);//disi user
            $table->decimal('berat_hilang', 6, 3)->default(0.000);
            $table->decimal('volume', 6, 6)->default(0.000000);
            $table->decimal('metode_biasa', 6, 2)->default(0.00);
            $table->decimal('luas_permukaan', 6, 2)->default(0.00);
            $table->decimal('hasil_akhir', 6, 2)->default(0.00);
            //metode_biasa = (berat_hilang / berat_awal) * 100
            //berat_hilang = berat_awal - berat_akhir
            //volume = (berat_awal / density)/1000
            //ketebalan_dm = ketebalan_mm / 100
            //luas_permukaan = volume / ketebalan_dm
            //hasil akhir = berat_hilang / luas_permukaan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_chemical');
    }
};
