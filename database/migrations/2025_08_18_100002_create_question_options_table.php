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
		Schema::create('question_options', function (Blueprint $table) {
			$table->id();
			$table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
			$table->string('option_text', 500);
			$table->integer('option_order');
			$table->timestamp('created_at')->useCurrent();
			$table->index('question_id', 'idx_question_id');
			$table->index('option_order', 'idx_option_order');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('question_options');
	}
};


