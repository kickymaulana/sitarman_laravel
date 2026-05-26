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
        Schema::create('chemical', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_test');
            $table->string('kode_alkali')->nullable()->default('-');
            $table->time('alkali_jam_mulai');
            $table->time('alkali_jam_selesai');
            $table->string('kode_acid')->nullable()->default('-');
            $table->time('acid_jam_mulai');
            $table->time('acid_jam_selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chemical');
    }
};
