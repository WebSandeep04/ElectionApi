<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormBuilderSeeder extends Seeder
{
	/**
	 * Seed the application's database with forms, questions, and options.
	 */
	public function run(): void
	{
		// Insert sample forms
		$formIds = [];
		$formIds['Customer Feedback Form'] = DB::table('forms')->insertGetId([
			'name' => 'Customer Feedback Form',
			'user_id' => 1,
			'created_at' => now(),
			'updated_at' => now(),
		]);
		$formIds['Employee Survey'] = DB::table('forms')->insertGetId([
			'name' => 'Employee Survey',
			'user_id' => 1,
			'created_at' => now(),
			'updated_at' => now(),
		]);
		$formIds['Product Review Form'] = DB::table('forms')->insertGetId([
			'name' => 'Product Review Form',
			'user_id' => 1,
			'created_at' => now(),
			'updated_at' => now(),
		]);

		// Helper to insert a question and return its id
		$insertQuestion = function (int $formId, string $text, string $type, bool $required, ?string $placeholder, int $order) {
			return DB::table('questions')->insertGetId([
				'form_id' => $formId,
				'question_text' => $text,
				'question_type' => $type,
				'is_required' => $required,
				'placeholder_text' => $placeholder,
				'question_order' => $order,
				'created_at' => now(),
				'updated_at' => now(),
			]);
		};

		$insertOption = function (int $questionId, string $text, int $order) {
			DB::table('question_options')->insert([
				'question_id' => $questionId,
				'option_text' => $text,
				'option_order' => $order,
				'created_at' => now(),
			]);
		};

		// Questions for Customer Feedback Form (form_id = 1)
		$q1 = $insertQuestion($formIds['Customer Feedback Form'], 'How satisfied are you with our service?', 'single_choice', true, null, 1);
		$q2 = $insertQuestion($formIds['Customer Feedback Form'], 'What aspects of our service need improvement?', 'multiple_choice', false, null, 2);
		$q3 = $insertQuestion($formIds['Customer Feedback Form'], 'Please provide additional comments:', 'long_text', false, 'Share your thoughts here...', 3);

		$insertOption($q1, 'Very Satisfied', 1);
		$insertOption($q1, 'Satisfied', 2);
		$insertOption($q1, 'Neutral', 3);
		$insertOption($q1, 'Dissatisfied', 4);
		$insertOption($q1, 'Very Dissatisfied', 5);

		$insertOption($q2, 'Customer Service', 1);
		$insertOption($q2, 'Product Quality', 2);
		$insertOption($q2, 'Pricing', 3);
		$insertOption($q2, 'Delivery Speed', 4);
		$insertOption($q2, 'Website Experience', 5);

		// Questions for Employee Survey (form_id = 2)
		$q4 = $insertQuestion($formIds['Employee Survey'], 'How would you rate your work environment?', 'single_choice', true, null, 1);
		$q5 = $insertQuestion($formIds['Employee Survey'], 'What benefits would you like to see?', 'multiple_choice', false, null, 2);
		$q6 = $insertQuestion($formIds['Employee Survey'], 'Any suggestions for improvement?', 'long_text', false, 'Your suggestions...', 3);

		$insertOption($q4, 'Excellent', 1);
		$insertOption($q4, 'Good', 2);
		$insertOption($q4, 'Average', 3);
		$insertOption($q4, 'Poor', 4);

		$insertOption($q5, 'Health Insurance', 1);
		$insertOption($q5, 'Paid Time Off', 2);
		$insertOption($q5, 'Flexible Hours', 3);
		$insertOption($q5, 'Remote Work Options', 4);
		$insertOption($q5, 'Professional Development', 5);
	}
}


