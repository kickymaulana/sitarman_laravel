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
        Schema::create('density_water_absorption', function (Blueprint $table) {
            $table->id();
            $table->foreignId('density_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('water_absoription_user_id')->constrained('users')->onDelete('cascade');
            $table->date('tgl')->nullable();
            $table->string('spec')->nullable()->default('-');
            $table->time('mulai_proses')->default('00:00:00');
            $table->time('selesai_proses')->default('00:00:00');
            $table->integer('temp_air')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('density_water_absorption');
    }
};
