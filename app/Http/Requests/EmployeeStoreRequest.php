<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_type_id' => 'required|integer|exists:employee_types,id',
            'emp_name' => 'required|string|max:255',
            'emp_email' => 'required|email|unique:employees,emp_email',
            'emp_password' => 'required|string|min:8',
            'emp_phone' => 'nullable|string|max:20',
            'emp_address' => 'nullable|string',
            'emp_wages' => 'nullable|numeric|min:0',
            'emp_date' => 'nullable|date',
            'is_active' => 'boolean',
            'emp_code' => 'nullable|string|max:50|unique:employees,emp_code',
            'emp_designation' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'termination_date' => 'nullable|date|after:joining_date',
            'emp_status' => 'nullable|in:active,inactive,terminated,on_leave',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'employee_type_id.required' => 'Employee type is required.',
            'employee_type_id.exists' => 'Selected employee type does not exist.',
            'emp_name.required' => 'Employee name is required.',
            'emp_email.required' => 'Email is required.',
            'emp_email.email' => 'Please provide a valid email address.',
            'emp_email.unique' => 'This email is already registered.',
            'emp_password.required' => 'Password is required.',
            'emp_password.min' => 'Password must be at least 8 characters.',
            'emp_code.unique' => 'This employee code is already in use.',
            'termination_date.after' => 'Termination date must be after joining date.',
            'emp_status.in' => 'Invalid employee status.',
        ];
    }
}
