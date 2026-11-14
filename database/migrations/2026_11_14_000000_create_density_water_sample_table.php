<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('density_water_sample', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_test');
            $table->unsignedInteger('urutan');
            $table->string('kode_sample');
            $table->string('no_open');
            $table->decimal('ketebalan', 12, 2)->nullable();
            $table->decimal('berat_awal', 12, 2)->nullable();
            $table->decimal('berat_akhir', 12, 2)->nullable();
            $table->decimal('volume', 12, 2)->nullable();
            $table->decimal('density', 12, 2)->nullable();
            $table->string('perlakuan')->nullable();
            $table->integer('temp')->nullable();
            $table->decimal('palm_wo', 12, 3)->nullable();
            $table->decimal('palm_wa', 12, 3)->nullable();
            $table->decimal('palm_water', 12, 3)->nullable();
            $table->decimal('mc_wo', 12, 3)->nullable();
            $table->decimal('mc_wa', 12, 3)->nullable();
            $table->decimal('mc_water', 12, 3)->nullable();
            $table->decimal('sl_wo', 12, 3)->nullable();
            $table->decimal('sl_wa', 12, 3)->nullable();
            $table->decimal('sl_water', 12, 3)->nullable();
            $table->string('spec')->nullable();
            $table->time('mulai_proses')->nullable();
            $table->time('selesai_proses')->nullable();
            $table->integer('temp_air')->nullable();
            $table->string('status')->default('belum_lengkap');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tgl_test', 'urutan']);
            $table->index(['tgl_test', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('density_water_sample');
    }
};
