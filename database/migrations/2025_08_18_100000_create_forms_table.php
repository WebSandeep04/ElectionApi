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
		Schema::create('forms', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->integer('user_id');
			$table->timestamps();
			$table->index('user_id', 'idx_user_id');
			$table->index('created_at', 'idx_created_at');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('forms');
	}
};


