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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_type_id')->constrained('employee_types')->onDelete('cascade');
            $table->string('emp_name');
            $table->string('emp_email')->unique();
            $table->string('emp_password');
            $table->string('emp_phone')->nullable();
            $table->text('emp_address')->nullable();
            $table->decimal('emp_wages', 10, 2)->nullable();
            $table->date('emp_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('emp_code')->unique()->nullable();
            $table->string('emp_designation')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->enum('emp_status', ['active', 'inactive', 'terminated', 'on_leave'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
