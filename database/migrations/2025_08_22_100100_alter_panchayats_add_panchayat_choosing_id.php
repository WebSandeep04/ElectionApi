<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('panchayats', function (Blueprint $table) {
            $table->foreignId('panchayat_choosing_id')->nullable()->after('block_id')->constrained('panchayat_choosings')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('panchayats', function (Blueprint $table) {
            $table->dropConstrainedForeignId('panchayat_choosing_id');
        });
    }
};


