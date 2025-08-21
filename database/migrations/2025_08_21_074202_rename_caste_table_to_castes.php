<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		// Rename main table
		if (Schema::hasTable('caste') && !Schema::hasTable('castes')) {
			Schema::rename('caste', 'castes');
		}

		// Fix foreign keys that reference old table name
		if (Schema::hasTable('cast_ratios')) {
			Schema::table('cast_ratios', function (Blueprint $table) {
				// Drop old FK if exists
				try { $table->dropForeign(['caste_id']); } catch (\Throwable $e) {}
				// Recreate FK to new table name
				$table->foreign('caste_id')->references('id')->on('castes')->onDelete('cascade');
			});
		}
	}

	public function down(): void
	{
		// Revert foreign keys
		if (Schema::hasTable('cast_ratios')) {
			Schema::table('cast_ratios', function (Blueprint $table) {
				try { $table->dropForeign(['caste_id']); } catch (\Throwable $e) {}
				$table->foreign('caste_id')->references('id')->on('caste')->onDelete('cascade');
			});
		}

		// Rename back
		if (Schema::hasTable('castes') && !Schema::hasTable('caste')) {
			Schema::rename('castes', 'caste');
		}
	}
};
