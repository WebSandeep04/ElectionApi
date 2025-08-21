# Educations API

## Overview
The Educations API manages education records used across the system (e.g., for user or employee profiles). It supports public read access and permission-protected write operations.

- Base path: `/api/educations`
- Authentication: Required for write operations (Laravel Sanctum)
- Permissions: Write operations require `manage_educations`

## Data Model
Education
```
{
  id: number,
  education_name: string,
  created_at: string (ISO datetime),
  updated_at: string (ISO datetime)
}
```

## Endpoints

### List Educations (Public Read)
- Method: GET
- URL: `/api/educations`
- Query params:
  - `search` (string, optional) - filter by `education_name`
  - `sort_by` (string, optional, default: `created_at`)
  - `sort_order` (string, optional, `asc` | `desc`, default: `desc`)
  - `per_page` (number, optional, default: `10`)
- Response (200):
```
{
  "educations": [
    { "id": 1, "education_name": "Graduate", "created_at": "...", "updated_at": "..." }
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

### Get Education (Public Read)
- Method: GET
- URL: `/api/educations/{id}`
- Response (200):
```
{
  "data": { "id": 1, "education_name": "Graduate", "created_at": "...", "updated_at": "..." }
}
```

### Create Education (Protected Write)
- Method: POST
- URL: `/api/educations`
- Auth: Bearer token (Sanctum)
- Permission: `manage_educations`
- Body (JSON):
```
{
  "education_name": "Graduate"
}
```
- Validation:
  - `education_name`: required, string, max:255
- Response (201):
```
{
  "data": { "id": 10, "education_name": "Graduate", "created_at": "...", "updated_at": "..." },
  "message": "Education created successfully"
}
```

### Update Education (Protected Write)
- Method: PUT/PATCH
- URL: `/api/educations/{id}`
- Auth: Bearer token (Sanctum)
- Permission: `manage_educations`
- Body (JSON):
```
{
  "education_name": "Post Graduate"
}
```
- Validation:
  - `education_name`: sometimes|required, string, max:255
- Response (200):
```
{
  "data": { "id": 10, "education_name": "Post Graduate", "created_at": "...", "updated_at": "..." },
  "message": "Education updated successfully"
}
```

### Delete Education (Protected Write)
- Method: DELETE
- URL: `/api/educations/{id}`
- Auth: Bearer token (Sanctum)
- Permission: `manage_educations`
- Response (200):
```
{
  "message": "Education deleted successfully"
}
```

## Examples

### List (search + sort + pagination)
```
GET /api/educations?search=grad&sort_by=education_name&sort_order=asc&per_page=20
```

### Create (with token)
```
curl -X POST \
  /api/educations \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{ "education_name": "Graduate" }'
```

## Errors
- 401 Unauthorized: missing/invalid auth for write operations
- 403 Forbidden: missing `manage_educations` permission
- 404 Not Found: resource not found
- 422 Unprocessable Entity: validation errors

## Notes
- Reads are public to support dropdown/populate use cases.
- Writes are gated by `manage_educations` and require authentication.
- Sorting defaults to `created_at desc`; use `sort_by` and `sort_order` to override.
