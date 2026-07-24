<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thermal_shock', function (Blueprint $table) {
            $table->string('sesi')->nullable()->after('thermal_pintu_id');
        });
    }

    public function down(): void
    {
        Schema::table('thermal_shock', function (Blueprint $table) {
            $table->dropColumn('sesi');
        });
    }
};
