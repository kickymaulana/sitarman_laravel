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
        Schema::create('produk_wa', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->default(1);
            $table->foreignId('water_absorption_id')->constrained('water_absorption')->onDelete('cascade');
            $table->date('tgl_produksi')->nullable();
            $table->foreignId('customer_id')->constrained('customer');
            $table->foreignId('modelsize_id')->constrained('modelsize');
            $table->foreignId('spesifikasi_id')->constrained('spesifikasi');
            $table->string('sampel')->nullable()->default('-');
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
        Schema::dropIfExists('produk_wa');
    }
};
