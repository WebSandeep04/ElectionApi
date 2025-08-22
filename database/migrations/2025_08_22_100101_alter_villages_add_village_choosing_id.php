<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->foreignId('village_choosing_id')->nullable()->after('panchayat_id')->constrained('village_choosings')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('village_choosing_id');
        });
    }
};


