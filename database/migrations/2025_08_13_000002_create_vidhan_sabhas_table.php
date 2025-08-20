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
        Schema::create('vidhan_sabhas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loksabha_id')->nullable()->constrained('lok_sabhas')->onDelete('cascade');
            $table->string('vidhansabha_name')->nullable();
            $table->string('vidhan_status', 244)->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vidhan_sabhas');
    }
};
