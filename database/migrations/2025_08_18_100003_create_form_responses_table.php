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
		Schema::create('form_responses', function (Blueprint $table) {
			$table->id();
			$table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
			$table->integer('user_id');
			$table->json('response_data')->nullable();
			$table->timestamp('created_at')->useCurrent();
			$table->index('form_id', 'idx_form_id');
			$table->index('user_id', 'idx_user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('form_responses');
	}
};


