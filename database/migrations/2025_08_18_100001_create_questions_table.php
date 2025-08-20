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
		Schema::create('questions', function (Blueprint $table) {
			$table->id();
			$table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
			$table->text('question_text');
			$table->enum('question_type', ['single_choice', 'multiple_choice', 'long_text']);
			$table->boolean('is_required')->default(false);
			$table->text('placeholder_text')->nullable();
			$table->integer('question_order');
			$table->timestamps();
			$table->index('form_id', 'idx_form_id');
			$table->index('question_order', 'idx_question_order');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('questions');
	}
};


