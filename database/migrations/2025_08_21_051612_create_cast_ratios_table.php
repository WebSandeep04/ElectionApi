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
        Schema::create('cast_ratios', function (Blueprint $table) {
            $table->id('caste_ratio_id');
            $table->foreignId('loksabha_id')->nullable()->constrained('lok_sabhas')->onDelete('set null');
            $table->foreignId('vidhansabha_id')->nullable()->constrained('vidhan_sabhas')->onDelete('set null');
            $table->foreignId('block_id')->nullable()->constrained('blocks')->onDelete('set null');
            $table->foreignId('panchayat_id')->nullable()->constrained('panchayats')->onDelete('set null');
            $table->foreignId('village_choosing')->nullable()->constrained('villages')->onDelete('set null');
            $table->foreignId('village_id')->nullable()->constrained('villages')->onDelete('set null');
            $table->foreignId('booth_id')->nullable()->constrained('booths')->onDelete('set null');
            $table->foreignId('caste_id')->constrained('castes')->onDelete('cascade');
            $table->integer('caste_ratio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cast_ratios');
    }
};
