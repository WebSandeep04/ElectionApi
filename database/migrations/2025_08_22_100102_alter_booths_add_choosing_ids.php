<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booths', function (Blueprint $table) {
            $table->foreignId('panchayat_choosing_id')->nullable()->after('panchayat_id')->constrained('panchayat_choosings')->nullOnDelete();
            $table->foreignId('village_choosing_id')->nullable()->after('village_choosing')->constrained('village_choosings')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('booths', function (Blueprint $table) {
            $table->dropConstrainedForeignId('panchayat_choosing_id');
            $table->dropConstrainedForeignId('village_choosing_id');
        });
    }
};
