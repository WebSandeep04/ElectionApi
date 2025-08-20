# Employee Types API

## Overview
- Base URL: `${API_BASE_URL}/api`
- Auth: Bearer token required
- Headers: `Authorization: Bearer <token>`, `Content-Type: application/json`, `Accept: application/json`

## Data model
- EmployeeType
  - `id`: number
  - `type_name`: string
  - `status`: string (default `"1"`)
  - `created_at`, `updated_at`: ISO strings

## Endpoints

### GET `/api/employee-types`
- Query: `page` (default 1), `per_page` (fixed 10 currently)
- Response 200
```json
{
  "employee_types": [
    { "id": 1, "type_name": "Contractor", "status": "1", "created_at": "2025-08-18T...", "updated_at": "2025-08-18T..." }
  ],
  "pagination": {
    "total": 1,
    "per_page": 10,
    "current_page": 1,
    "last_page": 1,
    "from": 1,
    "to": 1,
    "has_more_pages": false
  }
}
```

### POST `/api/employee-types`
- Body
```json
{ "type_name": "Contractor", "status": "1" }
```
- Response 201
```json
{
  "message": "Employee type created successfully",
  "employee_type": { "id": 12, "type_name": "Contractor", "status": "1", "created_at": "...", "updated_at": "..." }
}
```

### GET `/api/employee-types/{id}`
- Response 200
```json
{ "employee_type": { "id": 12, "type_name": "Contractor", "status": "1", "created_at": "...", "updated_at": "..." } }
```

### PUT `/api/employee-types/{id}`
- Body (partial allowed for fields marked `sometimes`)
```json
{ "type_name": "Full-Time", "status": "active" }
```
- Response 200
```json
{
  "message": "Employee type updated successfully",
  "employee_type": { "id": 12, "type_name": "Full-Time", "status": "active", "created_at": "...", "updated_at": "..." }
}
```

### DELETE `/api/employee-types/{id}`
- Response 200
```json
{ "message": "Employee type deleted successfully" }
```

## Validation
- POST
  - `type_name`: required, string, max 255
  - `status`: optional, string, max 255 (defaults to `"1"`)
- PUT
  - `type_name`: sometimes|required, string, max 255
  - `status`: sometimes|string, max 255

## Errors
- Uses Laravel validation responses (422) or 401 for missing/invalid token.
- Example 422
```json
{ "message": "The given data was invalid.", "errors": { "type_name": ["The type name field is required."] } }
```

## Examples

### cURL
```bash
# Create
curl -X POST "$API_BASE_URL/api/employee-types" \
 -H "Authorization: Bearer <token>" -H "Content-Type: application/json" \
 -d '{"type_name":"Contractor","status":"1"}'

# List
curl "$API_BASE_URL/api/employee-types" -H "Authorization: Bearer <token>"

# Get
curl "$API_BASE_URL/api/employee-types/12" -H "Authorization: Bearer <token>"

# Update
o=$(cat <<JSON
{"type_name":"Full-Time","status":"active"}
JSON
)
curl -X PUT "$API_BASE_URL/api/employee-types/12" \
 -H "Authorization: Bearer <token>" -H "Content-Type: application/json" -d "$o"

# Delete
curl -X DELETE "$API_BASE_URL/api/employee-types/12" -H "Authorization: Bearer <token>"
```

### JavaScript (fetch)
```javascript
const base = `${API_BASE_URL}/api`;
const headers = (token) => ({
  Authorization: `Bearer ${token}`,
  'Content-Type': 'application/json',
  Accept: 'application/json'
});

export async function listEmployeeTypes(token, page = 1) {
  const res = await fetch(`${base}/employee-types?page=${page}`, { headers: headers(token) });
  const json = await res.json();
  if (!res.ok) throw json;
  return json;
}

export async function createEmployeeType(token, payload) {
  const res = await fetch(`${base}/employee-types`, { method: 'POST', headers: headers(token), body: JSON.stringify(payload) });
  const json = await res.json();
  if (!res.ok) throw json;
  return json.employee_type;
}

export async function getEmployeeType(token, id) {
  const res = await fetch(`${base}/employee-types/${id}`, { headers: headers(token) });
  const json = await res.json();
  if (!res.ok) throw json;
  return json.employee_type;
}

export async function updateEmployeeType(token, id, payload) {
  const res = await fetch(`${base}/employee-types/${id}`, { method: 'PUT', headers: headers(token), body: JSON.stringify(payload) });
  const json = await res.json();
  if (!res.ok) throw json;
  return json.employee_type;
}

export async function deleteEmployeeType(token, id) {
  const res = await fetch(`${base}/employee-types/${id}`, { method: 'DELETE', headers: headers(token) });
  const json = await res.json();
  if (!res.ok) throw json;
  return json.message;
}
```
