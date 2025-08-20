<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeDocumentResource;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmployeeDocumentController extends Controller
{
    /**
     * Display a listing of employee documents.
     */
    public function index(Request $request): JsonResponse
    {
        $query = EmployeeDocument::with('employee');

        // Filter by employee
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by document type
        if ($request->has('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        // Filter by verification status
        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->boolean('is_verified'));
        }

        $documents = $query->paginate(10);

        return response()->json([
            'data' => EmployeeDocumentResource::collection($documents),
            'meta' => [
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
                'per_page' => $documents->perPage(),
                'total' => $documents->total(),
            ]
        ]);
    }

    /**
     * Store a newly created employee document.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employees,id',
            'document_type' => 'required|string|max:255',
            'document_name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB max
            'description' => 'nullable|string',
            'expiry_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('employee_documents', $fileName, 'public');

        $document = EmployeeDocument::create([
            'employee_id' => $request->employee_id,
            'document_type' => $request->document_type,
            'document_name' => $request->document_name,
            'file_path' => $filePath,
            'file_extension' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'description' => $request->description,
            'expiry_date' => $request->expiry_date,
        ]);

        return response()->json([
            'data' => new EmployeeDocumentResource($document->load('employee')),
            'message' => 'Document uploaded successfully'
        ], 201);
    }

    /**
     * Display the specified employee document.
     */
    public function show(EmployeeDocument $document): JsonResponse
    {
        return response()->json([
            'data' => new EmployeeDocumentResource($document->load('employee'))
        ]);
    }

    /**
     * Update the specified employee document.
     */
    public function update(Request $request, EmployeeDocument $document): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'sometimes|string|max:255',
            'document_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_verified' => 'boolean',
            'expiry_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $document->update($validator->validated());

        return response()->json([
            'data' => new EmployeeDocumentResource($document->load('employee')),
            'message' => 'Document updated successfully'
        ]);
    }

    /**
     * Remove the specified employee document.
     */
    public function destroy(EmployeeDocument $document): JsonResponse
    {
        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'message' => 'Document deleted successfully'
        ]);
    }

    /**
     * Get documents by employee.
     */
    public function getByEmployee($employeeId): JsonResponse
    {
        $documents = EmployeeDocument::with('employee')
            ->where('employee_id', $employeeId)
            ->paginate(10);

        return response()->json([
            'data' => EmployeeDocumentResource::collection($documents),
            'meta' => [
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
                'per_page' => $documents->perPage(),
                'total' => $documents->total(),
            ]
        ]);
    }

    /**
     * Get documents by type.
     */
    public function getByType($documentType): JsonResponse
    {
        $documents = EmployeeDocument::with('employee')
            ->where('document_type', $documentType)
            ->paginate(10);

        return response()->json([
            'data' => EmployeeDocumentResource::collection($documents),
            'meta' => [
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
                'per_page' => $documents->perPage(),
                'total' => $documents->total(),
            ]
        ]);
    }

    /**
     * Stream document inline to the browser to avoid direct /storage access.
     */
    public function download(EmployeeDocument $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return response()->json([
                'message' => 'File not found'
            ], 404);
        }

        $stream = Storage::disk('public')->readStream($document->file_path);
        if ($stream === false) {
            return response()->json([
                'message' => 'Unable to read file'
            ], 500);
        }

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $document->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.addslashes($document->document_name).'"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }

    /**
     * Verify document.
     */
    public function verify(EmployeeDocument $document): JsonResponse
    {
        $document->update(['is_verified' => true]);

        return response()->json([
            'data' => new EmployeeDocumentResource($document->load('employee')),
            'message' => 'Document verified successfully'
        ]);
    }

    /**
     * Unverify document.
     */
    public function unverify(EmployeeDocument $document): JsonResponse
    {
        $document->update(['is_verified' => false]);

        return response()->json([
            'data' => new EmployeeDocumentResource($document->load('employee')),
            'message' => 'Document unverified successfully'
        ]);
    }
}
