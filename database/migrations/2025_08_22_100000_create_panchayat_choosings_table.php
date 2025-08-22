<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('panchayat_choosings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('1');
            $table->timestamps();
        });

        // Seed default choices
        DB::table('panchayat_choosings')->insert([
            ['id' => 1, 'name' => 'Mahanager pallika', 'status' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Gram panchayat', 'status' => '1', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panchayat_choosings');
    }
};


