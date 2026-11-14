<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('density_water_sample', function (Blueprint $table) {
            $table->string('no_open')->nullable()->change();
            $table->dropColumn('temp');
        });
    }

    public function down(): void
    {
        Schema::table('density_water_sample', function (Blueprint $table) {
            $table->string('no_open')->nullable(false)->change();
            $table->integer('temp')->nullable()->after('perlakuan');
        });
    }
};
