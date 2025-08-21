<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('village_descriptions', function (Blueprint $table) {
			$table->id();
			$table->foreignId('loksabha_id')->constrained('lok_sabhas')->onDelete('cascade');
			$table->foreignId('vidhansabha_id')->constrained('vidhan_sabhas')->onDelete('cascade');
			$table->foreignId('block_id')->constrained('blocks')->onDelete('cascade');
			$table->foreignId('panchayat_id')->constrained('panchayats')->onDelete('cascade');
			$table->foreignId('village_choosing')->constrained('villages')->onDelete('cascade');
			$table->foreignId('village_id')->constrained('villages')->onDelete('cascade');
			$table->text('description');
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('village_descriptions');
	}
};
