# Form API Documentation

## Overview
- Base URL: `${API_BASE_URL}/api`
- Auth: Bearer token required
- Headers:
  - `Authorization: Bearer <token>`
  - `Content-Type: application/json`
  - `Accept: application/json`
- A Form contains nested Questions and Options (for choice questions)

## Data Model (client-facing)
- Form
  - `id`: number
  - `name`: string
  - `user_id`: number
  - `created_at`, `updated_at`: ISO strings
  - `questions`: `Question[]`
- Question
  - `id`: number
  - `question`: string
  - `type`: `single_choice | multiple_choice | long_text`
  - `required`: boolean
  - `placeholder?`: string (only for `long_text`)
  - `options?`: `string[]` (required for choice questions; min 2 non-empty)
  - `order`: number

## Endpoints

### GET `/api/forms`
- Query
  - `page`: number (default 1)
  - `per_page`: number (default 10, max 100)
  - `search`: string (optional; matches `name`)
- Response 200
```json
{
  "data": [
    { "id": 1, "name": "Customer Feedback", "created_at": "2024-01-01T12:00:00Z", "updated_at": "2024-01-02T12:00:00Z" }
  ],
  "meta": { "page": 1, "per_page": 10, "total": 1, "total_pages": 1 }
}
```

### POST `/api/forms`
- Body
```json
{
  "name": "Customer Feedback",
  "questions": [
    {"question": "Overall experience?", "type": "single_choice", "required": true, "options": ["Excellent", "Good", "Average", "Poor"]},
    {"question": "Issues you faced?", "type": "multiple_choice", "options": ["Price", "Support", "Quality", "Delivery"]},
    {"question": "Additional comments", "type": "long_text", "placeholder": "Write here..."}
  ]
}
```
- Response 201: Full Form object with nested questions and options

### GET `/api/forms/{id}`
- Response 200: Full Form object with nested questions and options

### PUT `/api/forms/{id}`
- Body: same shape as POST (full replace)
- Response 200: Updated Form object

### DELETE `/api/forms/{id}`
- Response 200
```json
{ "message": "deleted" }
```

## Validation Rules
- Form
  - `name`: required, 3..255 chars
  - `questions`: required, array, min 1
- Question
  - `question`: required, 3..1000 chars
  - `type`: one of `single_choice`, `multiple_choice`, `long_text`
  - `required`: boolean
  - `placeholder`: only allowed for `long_text`
  - `options`: required for `single_choice`/`multiple_choice`; array of strings, min 2 non-empty, each 1..500 chars

## Error Format
- Status codes: `400|401|403|404|422|500`
```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "details": { "questions.0.options": ["At least two non-empty options are required"] }
  }
}
```

## Transactional Behavior
- POST/PUT: Create or update the `form`, then upsert questions in order; for choice types, replace options. All within a single DB transaction.
- DELETE: Cascade deletes questions and options.

## Examples

### cURL
```bash
# Create
curl -X POST "$API_BASE_URL/api/forms" \
 -H "Authorization: Bearer <token>" -H "Content-Type: application/json" \
 -d '{
  "name": "Customer Feedback",
  "questions": [
    {"question": "Overall experience?", "type": "single_choice", "required": true, "options": ["Excellent","Good","Average","Poor"]},
    {"question": "Issues you faced?", "type": "multiple_choice", "options": ["Price","Support","Quality","Delivery"]},
    {"question": "Additional comments", "type": "long_text", "placeholder": "Write here..."}
  ]
}'

# Update
curl -X PUT "$API_BASE_URL/api/forms/123" \
 -H "Authorization: Bearer <token>" -H "Content-Type: application/json" \
 -d '{
  "name": "Customer Feedback (v2)",
  "questions": [
    {"question": "Overall experience?", "type": "single_choice", "required": true, "options": ["Excellent","Good","Average","Poor"]},
    {"question": "Any comments?", "type": "long_text", "placeholder": "Write here..."}
  ]
}'

# List
curl "$API_BASE_URL/api/forms?page=1&per_page=10&search=feedback" -H "Authorization: Bearer <token>"

# Get
curl "$API_BASE_URL/api/forms/123" -H "Authorization: Bearer <token>"

# Delete
curl -X DELETE "$API_BASE_URL/api/forms/123" -H "Authorization: Bearer <token>"
```

### Fetch (JS)
```javascript
const base = `${API_BASE_URL}/api`;
const headers = (token) => ({
  'Authorization': `Bearer ${token}`,
  'Content-Type': 'application/json',
  'Accept': 'application/json'
});

// List
export async function listForms(token, { page = 1, perPage = 10, search = '' } = {}) {
  const q = new URLSearchParams({ page, per_page: perPage, ...(search ? { search } : {}) });
  const res = await fetch(`${base}/forms?${q}`, { headers: headers(token) });
  if (!res.ok) throw await res.json();
  return res.json();
}

// Create
export async function createForm(token, payload) {
  const res = await fetch(`${base}/forms`, {
    method: 'POST',
    headers: headers(token),
    body: JSON.stringify(payload)
  });
  const json = await res.json();
  if (!res.ok) throw json;
  return json;
}

// Get
export async function getForm(token, id) {
  const res = await fetch(`${base}/forms/${id}`, { headers: headers(token) });
  const json = await res.json();
  if (!res.ok) throw json;
  return json;
}

// Update
export async function updateForm(token, id, payload) {
  const res = await fetch(`${base}/forms/${id}`, {
    method: 'PUT',
    headers: headers(token),
    body: JSON.stringify(payload)
  });
  const json = await res.json();
  if (!res.ok) throw json;
  return json;
}

// Delete
export async function deleteForm(token, id) {
  const res = await fetch(`${base}/forms/${id}`, {
    method: 'DELETE',
    headers: headers(token)
  });
  const json = await res.json();
  if (!res.ok) throw json;
  return json;
}
```

## OpenAPI 3.0 (Swagger)
```yaml
openapi: 3.0.3
info:
  title: Form API
  version: 1.0.0
servers:
  - url: https://api.example.com/api
paths:
  /forms:
    get:
      summary: List forms
      parameters:
        - in: query
          name: page
          schema: { type: integer, default: 1 }
        - in: query
          name: per_page
          schema: { type: integer, default: 10, maximum: 100 }
        - in: query
          name: search
          schema: { type: string }
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema: { $ref: '#/components/schemas/PaginatedForms' }
    post:
      summary: Create form
      requestBody:
        required: true
        content:
          application/json:
            schema: { $ref: '#/components/schemas/FormCreate' }
      responses:
        '201':
          description: Created
          content:
            application/json:
              schema: { $ref: '#/components/schemas/Form' }
  /forms/{id}:
    get:
      summary: Get form
      parameters:
        - in: path
          name: id
          required: true
          schema: { type: integer }
      responses:
        '200': { description: OK, content: { application/json: { schema: { $ref: '#/components/schemas/Form' } } } }
        '404': { description: Not Found }
    put:
      summary: Update form
      parameters:
        - in: path
          name: id
          required: true
          schema: { type: integer }
      requestBody:
        required: true
        content:
          application/json:
            schema: { $ref: '#/components/schemas/FormCreate' }
      responses:
        '200': { description: OK, content: { application/json: { schema: { $ref: '#/components/schemas/Form' } } } }
    delete:
      summary: Delete form
      parameters:
        - in: path
          name: id
          required: true
          schema: { type: integer }
      responses:
        '200': { description: Deleted }
components:
  schemas:
    FormCreate:
      type: object
      required: [name, questions]
      properties:
        name: { type: string, minLength: 3, maxLength: 255 }
        questions:
          type: array
          minItems: 1
          items: { $ref: '#/components/schemas/QuestionInput' }
    QuestionInput:
      type: object
      required: [question, type]
      properties:
        question: { type: string, minLength: 3, maxLength: 1000 }
        type: { type: string, enum: [single_choice, multiple_choice, long_text] }
        required: { type: boolean, default: false }
        placeholder: { type: string }
        options:
          type: array
          items: { type: string, minLength: 1, maxLength: 500 }
    Form:
      allOf:
        - $ref: '#/components/schemas/FormCreate'
        - type: object
          required: [id, created_at, updated_at]
          properties:
            id: { type: integer }
            user_id: { type: integer }
            created_at: { type: string, format: date-time }
            updated_at: { type: string, format: date-time }
    PaginatedForms:
      type: object
      properties:
        data:
          type: array
          items:
            type: object
            properties:
              id: { type: integer }
              name: { type: string }
              created_at: { type: string, format: date-time }
              updated_at: { type: string, format: date-time }
        meta:
          type: object
          properties:
            page: { type: integer }
            per_page: { type: integer }
            total: { type: integer }
            total_pages: { type: integer }
```
