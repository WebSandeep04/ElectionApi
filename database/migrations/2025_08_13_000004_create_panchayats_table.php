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
        Schema::create('panchayats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loksabha_id')->nullable()->constrained('lok_sabhas')->onDelete('cascade');
            $table->foreignId('vidhansabha_id')->nullable()->constrained('vidhan_sabhas')->onDelete('cascade');
            $table->foreignId('block_id')->nullable()->constrained('blocks')->onDelete('cascade');
            $table->string('panchayat_choosing')->nullable();
            $table->string('panchayat_name')->nullable();
            $table->string('panchayat_status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panchayats');
    }
};
