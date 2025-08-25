# Caste API Documentation

## Overview
The Caste API provides comprehensive functionality for managing castes and their relationships with caste categories. Each caste can be assigned to a category (General, OBC, SC, ST, EWS) or left unassigned.

## Base URL
```
/api/castes
```

## Authentication
- **Public endpoints**: `GET` operations (list, show, filter)
- **Protected endpoints**: `POST`, `PUT`, `DELETE` operations require authentication and appropriate permissions

## Models and Relationships

### Caste Model
```php
// App\Models\Caste
protected $fillable = [
    'caste',
    'category_id',
];

// Relationship
public function category()
{
    return $this->belongsTo(CasteCategory::class, 'category_id');
}
```

### CasteCategory Model
```php
// App\Models\CasteCategory
protected $fillable = [
    'name',
    'description',
];

// Relationship
public function castes()
{
    return $this->hasMany(Caste::class, 'category_id');
}
```

## API Endpoints

### 1. List All Castes
**GET** `/api/castes`

Returns a paginated list of all castes with their category information.

#### Query Parameters
- `category_id` (optional): Filter castes by category ID
- `caste` (optional): Search castes by name (partial match)
- `category_name` (optional): Search castes by category name (partial match)
- `page` (optional): Page number for pagination

#### Example Request
```bash
GET /api/castes?category_id=1&page=1
```

#### Example Response
```json
{
    "castes": [
        {
            "id": 1,
            "caste": "Brahmin",
            "category_id": 1,
            "category_data": {
                "id": 1,
                "name": "General",
                "description": "General category for unreserved castes"
            },
            "created_at": "2025-01-20T10:00:00.000000Z",
            "updated_at": "2025-01-20T10:00:00.000000Z"
        }
    ],
    "pagination": {
        "total": 19,
        "per_page": 10,
        "current_page": 1,
        "last_page": 2,
        "from": 1,
        "to": 10,
        "has_more_pages": true
    }
}
```

### 2. Get Specific Caste
**GET** `/api/castes/{id}`

Returns a specific caste with its category information.

#### Example Request
```bash
GET /api/castes/1
```

#### Example Response
```json
{
    "caste": {
        "id": 1,
        "caste": "Brahmin",
        "category_id": 1,
        "category_data": {
            "id": 1,
            "name": "General",
            "description": "General category for unreserved castes"
        },
        "created_at": "2025-01-20T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z"
    }
}
```

### 3. Get Castes by Category
**GET** `/api/castes/category/{categoryId}`

Returns all castes belonging to a specific category.

#### Example Request
```bash
GET /api/castes/category/1
```

#### Example Response
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "caste": "Brahmin",
            "category_id": 1,
            "category_data": {
                "id": 1,
                "name": "General",
                "description": "General category for unreserved castes"
            },
            "created_at": "2025-01-20T10:00:00.000000Z",
            "updated_at": "2025-01-20T10:00:00.000000Z"
        }
    ]
}
```

### 4. Get Unassigned Castes
**GET** `/api/castes/unassigned`

Returns all castes that are not assigned to any category.

#### Example Request
```bash
GET /api/castes/unassigned
```

#### Example Response
```json
{
    "success": true,
    "data": [
        {
            "id": 20,
            "caste": "Unassigned Caste",
            "category_id": null,
            "category_data": null,
            "created_at": "2025-01-20T10:00:00.000000Z",
            "updated_at": "2025-01-20T10:00:00.000000Z"
        }
    ]
}
```

### 5. Create New Caste
**POST** `/api/castes`

Creates a new caste with optional category assignment.

**Permission Required**: `manage_castes`

#### Request Body
```json
{
    "caste": "New Caste Name",
    "category_id": 1
}
```

#### Validation Rules
- `caste`: required, string, max 255 characters
- `category_id`: optional, must exist in caste_categories table

#### Example Request
```bash
POST /api/castes
Content-Type: application/json
Authorization: Bearer {token}

{
    "caste": "New Caste",
    "category_id": 2
}
```

#### Example Response
```json
{
    "message": "Caste created successfully",
    "caste": {
        "id": 21,
        "caste": "New Caste",
        "category_id": 2,
        "category_data": {
            "id": 2,
            "name": "OBC",
            "description": "Other Backward Classes category"
        },
        "created_at": "2025-01-20T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z"
    }
}
```

### 6. Update Caste
**PUT** `/api/castes/{id}`

Updates an existing caste, including category assignment.

**Permission Required**: `manage_castes`

#### Request Body
```json
{
    "caste": "Updated Caste Name",
    "category_id": 3
}
```

#### Validation Rules
- `caste`: sometimes required, string, max 255 characters
- `category_id`: optional, must exist in caste_categories table

#### Example Request
```bash
PUT /api/castes/1
Content-Type: application/json
Authorization: Bearer {token}

{
    "caste": "Updated Brahmin",
    "category_id": 2
}
```

#### Example Response
```json
{
    "message": "Caste updated successfully",
    "caste": {
        "id": 1,
        "caste": "Updated Brahmin",
        "category_id": 2,
        "category_data": {
            "id": 2,
            "name": "OBC",
            "description": "Other Backward Classes category"
        },
        "created_at": "2025-01-20T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z"
    }
}
```

### 7. Delete Caste
**DELETE** `/api/castes/{id}`

Deletes a caste.

**Permission Required**: `manage_castes`

#### Example Request
```bash
DELETE /api/castes/1
Authorization: Bearer {token}
```

#### Example Response
```json
{
    "message": "Caste deleted successfully"
}
```

### 8. Assign Caste to Category
**POST** `/api/castes/{id}/assign-category`

Assigns a caste to a specific category.

**Permission Required**: `manage_castes`

#### Request Body
```json
{
    "category_id": 1
}
```

#### Validation Rules
- `category_id`: required, must exist in caste_categories table

#### Example Request
```bash
POST /api/castes/1/assign-category
Content-Type: application/json
Authorization: Bearer {token}

{
    "category_id": 1
}
```

#### Example Response
```json
{
    "success": true,
    "message": "Caste assigned to category successfully",
    "caste": {
        "id": 1,
        "caste": "Brahmin",
        "category_id": 1,
        "category_data": {
            "id": 1,
            "name": "General",
            "description": "General category for unreserved castes"
        },
        "created_at": "2025-01-20T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z"
    }
}
```

### 9. Remove Caste from Category
**POST** `/api/castes/{id}/remove-category`

Removes a caste from its current category (sets category_id to null).

**Permission Required**: `manage_castes`

#### Example Request
```bash
POST /api/castes/1/remove-category
Authorization: Bearer {token}
```

#### Example Response
```json
{
    "success": true,
    "message": "Caste removed from category successfully",
    "caste": {
        "id": 1,
        "caste": "Brahmin",
        "category_id": null,
        "category_data": null,
        "created_at": "2025-01-20T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z"
    }
}
```

## Frontend Integration Examples

### JavaScript/Fetch API

#### Get All Castes with Categories
```javascript
fetch('/api/castes')
    .then(response => response.json())
    .then(data => {
        data.castes.forEach(caste => {
            const categoryName = caste.category_data ? caste.category_data.name : 'Unassigned';
            console.log(`${caste.caste} -> ${categoryName}`);
        });
    });
```

#### Get Castes by Category
```javascript
function getCastesByCategory(categoryId) {
    fetch(`/api/castes/category/${categoryId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Castes in category:', data.data);
            }
        });
}
```

#### Create New Caste
```javascript
function createCaste(casteName, categoryId, token) {
    fetch('/api/castes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
            caste: casteName,
            category_id: categoryId
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Caste created:', data.caste);
    });
}
```

#### Assign Caste to Category
```javascript
function assignCasteToCategory(casteId, categoryId, token) {
    fetch(`/api/castes/${casteId}/assign-category`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({ category_id: categoryId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Caste assigned successfully');
        }
    });
}
```

### React Example
```jsx
import React, { useState, useEffect } from 'react';

function CasteManager() {
    const [castes, setCastes] = useState([]);
    const [categories, setCategories] = useState([]);
    const [selectedCategory, setSelectedCategory] = useState('');

    useEffect(() => {
        // Load castes
        fetch('/api/castes')
            .then(response => response.json())
            .then(data => setCastes(data.castes));

        // Load categories
        fetch('/api/caste-categories')
            .then(response => response.json())
            .then(data => setCategories(data.data));
    }, []);

    const handleCategoryChange = (categoryId) => {
        setSelectedCategory(categoryId);
        if (categoryId) {
            fetch(`/api/castes/category/${categoryId}`)
                .then(response => response.json())
                .then(data => setCastes(data.data));
        } else {
            fetch('/api/castes')
                .then(response => response.json())
                .then(data => setCastes(data.castes));
        }
    };

    return (
        <div>
            <select onChange={(e) => handleCategoryChange(e.target.value)}>
                <option value="">All Categories</option>
                {categories.map(category => (
                    <option key={category.id} value={category.id}>
                        {category.name}
                    </option>
                ))}
            </select>

            <ul>
                {castes.map(caste => (
                    <li key={caste.id}>
                        {caste.caste} - {caste.category_data?.name || 'Unassigned'}
                    </li>
                ))}
            </ul>
        </div>
    );
}
```

## Error Handling

### Common Error Responses

#### Validation Error (422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "caste": ["The caste field is required."],
        "category_id": ["The selected category id is invalid."]
    }
}
```

#### Not Found Error (404)
```json
{
    "message": "No query results for model [App\\Models\\Caste] 999"
}
```

#### Unauthorized Error (401)
```json
{
    "message": "Unauthenticated."
}
```

#### Forbidden Error (403)
```json
{
    "message": "This action is unauthorized."
}
```

## Database Schema

### castes Table
```sql
CREATE TABLE castes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    caste VARCHAR(255) NOT NULL,
    category_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES caste_categories(id) ON DELETE SET NULL,
    INDEX (category_id)
);
```

### caste_categories Table
```sql
CREATE TABLE caste_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX (name)
);
```

## Seeded Data

The system comes with pre-seeded caste categories and castes:

### Caste Categories
1. **General** - General category for unreserved castes
2. **OBC** - Other Backward Classes category
3. **SC** - Scheduled Caste category
4. **ST** - Scheduled Tribe category
5. **EWS** - Economically Weaker Section category

### Sample Castes
- **General**: Brahmin, Rajput, Bania, Kayastha
- **OBC**: Yadav, Kurmi, Lodhi, Gujjar, Jat
- **SC**: Chamar, Pasi, Dhobi, Balmiki
- **ST**: Gond, Bhil, Santhal, Munda
- **EWS**: General EWS, OBC EWS

## Testing

Use the provided test scripts to verify functionality:
- `test_caste_controller_api.php` - Tests the updated CasteController
- `test_caste_category_api.php` - Tests the CasteCategory API
- `test_seeded_caste_categories.php` - Demonstrates seeded data
