<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_type_id' => $this->employee_type_id,
            'emp_name' => $this->emp_name,
            'emp_email' => $this->emp_email,
            'emp_phone' => $this->emp_phone,
            'emp_address' => $this->emp_address,
            'emp_wages' => $this->emp_wages,
            'formatted_wages' => $this->formatted_wages,
            'emp_date' => $this->emp_date,
            'is_active' => $this->is_active,
            'emp_code' => $this->emp_code,
            'emp_designation' => $this->emp_designation,
            'joining_date' => $this->joining_date,
            'termination_date' => $this->termination_date,
            'emp_status' => $this->emp_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'employee_type' => $this->whenLoaded('employeeType', function () {
                return new EmployeeTypeResource($this->employeeType);
            }),
            'documents' => $this->whenLoaded('documents', function () {
                return EmployeeDocumentResource::collection($this->documents);
            }),
        ];
    }
}
