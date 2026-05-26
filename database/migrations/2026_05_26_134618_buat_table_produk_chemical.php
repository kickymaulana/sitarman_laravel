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
            $table->string('sample')->nullable()->default('-');
            $table->decimal('ketebalan', 6, 2)->default(0.00);
            $table->decimal('berat_awal', 6, 3)->default(0.000);
            $table->decimal('berat_akhir', 6, 3)->default(0.000);
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
