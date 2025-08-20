# Employee Management API Documentation

## Overview

The Employee Management API provides comprehensive CRUD operations for managing employees and their documents. The API includes features like search, filtering, pagination, and document management with file upload capabilities.

## Base URL
```
http://localhost:8000/api
```

## Authentication

- **Public Endpoints**: GET operations (index, show) are publicly accessible
- **Protected Endpoints**: POST, PUT, DELETE operations require authentication
- **Authentication Method**: Bearer Token (Laravel Sanctum)

## Employee API Endpoints

### 1. Get All Employees (Public)
```http
GET /api/employees
```

**Query Parameters:**
- `search` (string): Search by name, email, code, phone, or designation
- `status` (string): Filter by status (active, inactive, terminated, on_leave)
- `employee_type_id` (integer): Filter by employee type
- `sort_by` (string): Sort field (default: created_at)
- `sort_order` (string): Sort direction (asc, desc, default: desc)
- `page` (integer): Page number for pagination

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "employee_type_id": 1,
      "emp_name": "John Doe",
      "emp_email": "john.doe@example.com",
      "emp_phone": "+91-9876543210",
      "emp_address": "123 Main Street, City, State 12345",
      "emp_wages": "50000.00",
      "formatted_wages": "₹50,000.00",
      "emp_date": "2024-01-15",
      "is_active": true,
      "emp_code": "EMP001",
      "emp_designation": "Software Developer",
      "joining_date": "2024-01-15",
      "termination_date": null,
      "emp_status": "active",
      "created_at": "2024-01-15T10:00:00.000000Z",
      "updated_at": "2024-01-15T10:00:00.000000Z",
      "employee_type": {
        "id": 1,
        "type_name": "Full Time",
        "status": "1",
        "created_at": "2024-01-15T10:00:00.000000Z",
        "updated_at": "2024-01-15T10:00:00.000000Z"
      },
      "documents": []
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 5
  }
}
```

### 2. Get Employee by ID (Public)
```http
GET /api/employees/{id}
```

**Response:**
```json
{
  "data": {
    "id": 1,
    "employee_type_id": 1,
    "emp_name": "John Doe",
    "emp_email": "john.doe@example.com",
    "emp_phone": "+91-9876543210",
    "emp_address": "123 Main Street, City, State 12345",
    "emp_wages": "50000.00",
    "formatted_wages": "₹50,000.00",
    "emp_date": "2024-01-15",
    "is_active": true,
    "emp_code": "EMP001",
    "emp_designation": "Software Developer",
    "joining_date": "2024-01-15",
    "termination_date": null,
    "emp_status": "active",
    "created_at": "2024-01-15T10:00:00.000000Z",
    "updated_at": "2024-01-15T10:00:00.000000Z",
    "employee_type": {
      "id": 1,
      "type_name": "Full Time",
      "status": "1",
      "created_at": "2024-01-15T10:00:00.000000Z",
      "updated_at": "2024-01-15T10:00:00.000000Z"
    },
    "documents": []
  }
}
```

### 3. Create Employee (Protected)
```http
POST /api/employees
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "employee_type_id": 1,
  "emp_name": "New Employee",
  "emp_email": "new.employee@example.com",
  "emp_password": "password123",
  "emp_phone": "+91-9876543215",
  "emp_address": "New Address, City, State",
  "emp_wages": 45000.00,
  "emp_date": "2024-06-01",
  "is_active": true,
  "emp_code": "EMP006",
  "emp_designation": "Junior Developer",
  "joining_date": "2024-06-01",
  "termination_date": null,
  "emp_status": "active"
}
```

**Response:**
```json
{
  "data": {
    "id": 6,
    "employee_type_id": 1,
    "emp_name": "New Employee",
    "emp_email": "new.employee@example.com",
    "emp_phone": "+91-9876543215",
    "emp_address": "New Address, City, State",
    "emp_wages": "45000.00",
    "formatted_wages": "₹45,000.00",
    "emp_date": "2024-06-01",
    "is_active": true,
    "emp_code": "EMP006",
    "emp_designation": "Junior Developer",
    "joining_date": "2024-06-01",
    "termination_date": null,
    "emp_status": "active",
    "created_at": "2024-06-01T10:00:00.000000Z",
    "updated_at": "2024-06-01T10:00:00.000000Z",
    "employee_type": {
      "id": 1,
      "type_name": "Full Time",
      "status": "1",
      "created_at": "2024-01-15T10:00:00.000000Z",
      "updated_at": "2024-01-15T10:00:00.000000Z"
    },
    "documents": []
  },
  "message": "Employee created successfully"
}
```

### 4. Update Employee (Protected)
```http
PUT /api/employees/{id}
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "emp_name": "Updated Employee Name",
  "emp_wages": 50000.00,
  "emp_designation": "Senior Developer"
}
```

**Response:**
```json
{
  "data": {
    "id": 6,
    "employee_type_id": 1,
    "emp_name": "Updated Employee Name",
    "emp_email": "new.employee@example.com",
    "emp_phone": "+91-9876543215",
    "emp_address": "New Address, City, State",
    "emp_wages": "50000.00",
    "formatted_wages": "₹50,000.00",
    "emp_date": "2024-06-01",
    "is_active": true,
    "emp_code": "EMP006",
    "emp_designation": "Senior Developer",
    "joining_date": "2024-06-01",
    "termination_date": null,
    "emp_status": "active",
    "created_at": "2024-06-01T10:00:00.000000Z",
    "updated_at": "2024-06-01T10:00:00.000000Z",
    "employee_type": {
      "id": 1,
      "type_name": "Full Time",
      "status": "1",
      "created_at": "2024-01-15T10:00:00.000000Z",
      "updated_at": "2024-01-15T10:00:00.000000Z"
    },
    "documents": []
  },
  "message": "Employee updated successfully"
}
```

### 5. Delete Employee (Protected)
```http
DELETE /api/employees/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Employee deleted successfully"
}
```

### 6. Get Active Employees (Public)
```http
GET /api/employees/active
```

### 7. Get Inactive Employees (Public)
```http
GET /api/employees/inactive
```

### 8. Get Employees by Type (Public)
```http
GET /api/employees/type/{employeeTypeId}
```

## Employee Document API Endpoints

### 1. Get All Documents (Public)
```http
GET /api/employee-documents
```

**Query Parameters:**
- `employee_id` (integer): Filter by employee
- `document_type` (string): Filter by document type
- `is_verified` (boolean): Filter by verification status

### 2. Get Document by ID (Public)
```http
GET /api/employee-documents/{id}
```

### 3. Upload Document (Protected)
```http
POST /api/employee-documents
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Form Data:**
- `employee_id` (integer, required): Employee ID
- `document_type` (string, required): Document type (aadhar, pan, photo, resume, etc.)
- `document_name` (string, required): Document name
- `file` (file, required): Document file (pdf, jpg, jpeg, png, doc, docx, max 10MB)
- `description` (string, optional): Document description
- `expiry_date` (date, optional): Document expiry date

### 4. Update Document (Protected)
```http
PUT /api/employee-documents/{id}
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "document_type": "aadhar",
  "document_name": "Updated Aadhar Card",
  "description": "Updated Aadhar card for employee",
  "is_verified": true,
  "expiry_date": "2030-12-31"
}
```

### 5. Delete Document (Protected)
```http
DELETE /api/employee-documents/{id}
```

### 6. Get Documents by Employee (Public)
```http
GET /api/employee-documents/employee/{employeeId}
```

### 7. Get Documents by Type (Public)
```http
GET /api/employee-documents/type/{documentType}
```

### 8. Download Document (Public)
```http
GET /api/employee-documents/{id}/download
```

**Response:**
```json
{
  "download_url": "http://localhost:8000/storage/employee_documents/1234567890_document.pdf",
  "file_name": "Aadhar Card",
  "file_size": 1024000,
  "mime_type": "application/pdf"
}
```

### 9. Verify Document (Protected)
```http
POST /api/employee-documents/{id}/verify
```

### 10. Unverify Document (Protected)
```http
POST /api/employee-documents/{id}/unverify
```

## Validation Rules

### Employee Validation
- `employee_type_id`: Required, integer, exists in employee_types table
- `emp_name`: Required, string, max 255 characters
- `emp_email`: Required, email, unique
- `emp_password`: Required, string, min 8 characters
- `emp_phone`: Optional, string, max 20 characters
- `emp_address`: Optional, string
- `emp_wages`: Optional, numeric, min 0
- `emp_date`: Optional, date
- `is_active`: Optional, boolean
- `emp_code`: Optional, string, max 50 characters, unique
- `emp_designation`: Optional, string, max 255 characters
- `joining_date`: Optional, date
- `termination_date`: Optional, date, after joining_date
- `emp_status`: Optional, in: active, inactive, terminated, on_leave

### Document Validation
- `employee_id`: Required, integer, exists in employees table
- `document_type`: Required, string, max 255 characters
- `document_name`: Required, string, max 255 characters
- `file`: Required, file, mimes: pdf,jpg,jpeg,png,doc,docx, max 10MB
- `description`: Optional, string
- `expiry_date`: Optional, date

## Error Responses

### Validation Error (422)
```json
{
  "message": "Validation failed",
  "errors": {
    "emp_email": [
      "The emp email field is required."
    ],
    "emp_name": [
      "The emp name field is required."
    ]
  }
}
```

### Not Found Error (404)
```json
{
  "message": "Employee not found"
}
```

### Unauthorized Error (401)
```json
{
  "message": "Unauthenticated"
}
```

## JavaScript Examples

### Get All Employees
```javascript
fetch('http://localhost:8000/api/employees')
  .then(response => response.json())
  .then(data => {
    console.log('Employees:', data.data);
    console.log('Pagination:', data.meta);
  })
  .catch(error => console.error('Error:', error));
```

### Create Employee
```javascript
const token = 'your-auth-token';

fetch('http://localhost:8000/api/employees', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}`
  },
  body: JSON.stringify({
    employee_type_id: 1,
    emp_name: 'New Employee',
    emp_email: 'new.employee@example.com',
    emp_password: 'password123',
    emp_phone: '+91-9876543215',
    emp_address: 'New Address, City, State',
    emp_wages: 45000.00,
    emp_code: 'EMP006',
    emp_designation: 'Junior Developer',
    joining_date: '2024-06-01',
    emp_status: 'active'
  })
})
.then(response => response.json())
.then(data => {
  console.log('Created Employee:', data.data);
})
.catch(error => console.error('Error:', error));
```

### Upload Document
```javascript
const token = 'your-auth-token';
const formData = new FormData();

formData.append('employee_id', 1);
formData.append('document_type', 'aadhar');
formData.append('document_name', 'Aadhar Card');
formData.append('file', fileInput.files[0]);
formData.append('description', 'Aadhar card for employee');

fetch('http://localhost:8000/api/employee-documents', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`
  },
  body: formData
})
.then(response => response.json())
.then(data => {
  console.log('Uploaded Document:', data.data);
})
.catch(error => console.error('Error:', error));
```

### Search Employees
```javascript
fetch('http://localhost:8000/api/employees?search=John&status=active&page=1')
  .then(response => response.json())
  .then(data => {
    console.log('Search Results:', data.data);
    console.log('Pagination:', data.meta);
  })
  .catch(error => console.error('Error:', error));
```

## Features

### Employee Management
- ✅ Full CRUD operations
- ✅ Search functionality (name, email, code, phone, designation)
- ✅ Status filtering (active, inactive, terminated, on_leave)
- ✅ Employee type filtering
- ✅ Pagination (10 items per page)
- ✅ Sorting (any field, asc/desc)
- ✅ Password hashing
- ✅ Formatted wages display
- ✅ Relationship loading (employee type, documents)

### Document Management
- ✅ File upload (PDF, images, documents)
- ✅ Document type categorization
- ✅ Verification system
- ✅ Expiry date tracking
- ✅ File metadata storage
- ✅ Download functionality
- ✅ Document filtering and search

### Security
- ✅ Public read access
- ✅ Protected write operations
- ✅ Input validation
- ✅ File type restrictions
- ✅ File size limits
- ✅ Unique constraints

### Data Integrity
- ✅ Foreign key relationships
- ✅ Cascade deletion
- ✅ Proper data types
- ✅ Validation rules
- ✅ Error handling

## Notes

1. **File Storage**: Documents are stored in `storage/app/public/employee_documents/`
2. **Password Security**: Passwords are automatically hashed using Laravel's Hash facade
3. **Pagination**: All list endpoints return 10 items per page with pagination metadata
4. **Relationships**: Employee type and documents are loaded when requested
5. **Status Management**: Employees can have multiple statuses (active, inactive, terminated, on_leave)
6. **Document Types**: Common types include aadhar, pan, photo, resume, contract, etc.
7. **Verification**: Documents can be marked as verified/unverified by authorized users
