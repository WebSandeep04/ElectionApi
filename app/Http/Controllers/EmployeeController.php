<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees with pagination and search.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Employee::with(['employeeType', 'documents']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('emp_name', 'like', "%{$search}%")
                  ->orWhere('emp_email', 'like', "%{$search}%")
                  ->orWhere('emp_code', 'like', "%{$search}%")
                  ->orWhere('emp_phone', 'like', "%{$search}%")
                  ->orWhere('emp_designation', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->active();
            } elseif ($status === 'inactive') {
                $query->inactive();
            } else {
                $query->where('emp_status', $status);
            }
        }

        // Filter by employee type
        if ($request->has('employee_type_id')) {
            $query->where('employee_type_id', $request->employee_type_id);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $employees = $query->paginate(10);

        return response()->json([
            'data' => EmployeeResource::collection($employees),
            'meta' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ]
        ]);
    }

    /**
     * Store a newly created employee.
     */
    public function store(EmployeeStoreRequest $request): JsonResponse
    {
        $employee = Employee::create($request->validated());

        return response()->json([
            'data' => new EmployeeResource($employee->load(['employeeType', 'documents'])),
            'message' => 'Employee created successfully'
        ], 201);
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee): JsonResponse
    {
        return response()->json([
            'data' => new EmployeeResource($employee->load(['employeeType', 'documents']))
        ]);
    }

    /**
     * Update the specified employee.
     */
    public function update(EmployeeUpdateRequest $request, Employee $employee): JsonResponse
    {
        $employee->update($request->validated());

        return response()->json([
            'data' => new EmployeeResource($employee->load(['employeeType', 'documents'])),
            'message' => 'Employee updated successfully'
        ]);
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee): JsonResponse
    {
        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully'
        ]);
    }

    /**
     * Get employees by employee type.
     */
    public function getByEmployeeType($employeeTypeId): JsonResponse
    {
        $employees = Employee::with(['employeeType', 'documents'])
            ->where('employee_type_id', $employeeTypeId)
            ->paginate(10);

        return response()->json([
            'data' => EmployeeResource::collection($employees),
            'meta' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ]
        ]);
    }

    /**
     * Get active employees.
     */
    public function getActive(): JsonResponse
    {
        $employees = Employee::with(['employeeType', 'documents'])
            ->active()
            ->paginate(10);

        return response()->json([
            'data' => EmployeeResource::collection($employees),
            'meta' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ]
        ]);
    }

    /**
     * Get inactive employees.
     */
    public function getInactive(): JsonResponse
    {
        $employees = Employee::with(['employeeType', 'documents'])
            ->inactive()
            ->paginate(10);

        return response()->json([
            'data' => EmployeeResource::collection($employees),
            'meta' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ]
        ]);
    }
}
