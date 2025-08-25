# Caste Category API Documentation

## Overview
The Caste Category API provides endpoints to manage caste categories. Castes now belong to categories (one-to-many relationship), allowing for better organization and classification of caste data.

## Database Schema

### Table: `caste_categories`
- `id` - Primary key (auto-increment)
- `name` - Category name (string, required)
- `description` - Category description (text, nullable)
- `created_at` - Timestamp
- `updated_at` - Timestamp

### Table: `castes` (Updated)
- `id` - Primary key (auto-increment)
- `caste` - Caste name (string, required)
- `category_id` - Foreign key to `caste_categories.id` (nullable)
- `created_at` - Timestamp
- `updated_at` - Timestamp

### Foreign Key Relationships
- `castes.category_id` → `caste_categories.id` (SET NULL on delete)

### Indexes
- `caste_categories.name` - For efficient name searches
- `castes.category_id` - For efficient lookups by category

## API Endpoints

### Public Endpoints (No Authentication Required)

#### 1. List Caste Categories
```
GET /api/caste-categories
```

**Query Parameters:**
- `name` (optional) - Filter by name (partial match)
- `page` (optional) - Page number for pagination

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "General Category",
            "description": "General category description",
            "castes": [
                {
                    "id": 1,
                    "caste": "General"
                }
            ],
            "castes_count": 1,
            "created_at": "2025-08-25T10:00:00.000000Z",
            "updated_at": "2025-08-25T10:00:00.000000Z"
        }
    ],
    "links": {...},
    "meta": {...}
}
```

#### 2. Get Caste Category by ID
```
GET /api/caste-categories/{id}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "General Category",
        "description": "General category description",
        "castes": [
            {
                "id": 1,
                "caste": "General"
            }
        ],
        "castes_count": 1,
        "created_at": "2025-08-25T10:00:00.000000Z",
        "updated_at": "2025-08-25T10:00:00.000000Z"
    }
}
```

#### 3. Get Castes by Category ID
```
GET /api/caste-categories/{categoryId}/castes
```

**Response:**
```json
{
    "success": true,
    "data": {
        "category": {
            "id": 1,
            "name": "General Category",
            "description": "General category description",
            "castes": [...],
            "castes_count": 1,
            "created_at": "2025-08-25T10:00:00.000000Z",
            "updated_at": "2025-08-25T10:00:00.000000Z"
        },
        "castes": [
            {
                "id": 1,
                "caste": "General",
                "category_id": 1,
                "created_at": "2025-08-25T10:00:00.000000Z",
                "updated_at": "2025-08-25T10:00:00.000000Z"
            }
        ]
    }
}
```

### Protected Endpoints (Authentication Required)

#### 4. Create Caste Category
```
POST /api/caste-categories
```

**Required Permission:** `manage_caste_categories`

**Request Body:**
```json
{
    "name": "New Category",
    "description": "Category description"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Caste category created successfully",
    "data": {
        "id": 2,
        "name": "New Category",
        "description": "Category description",
        "castes": [],
        "castes_count": 0,
        "created_at": "2025-08-25T10:00:00.000000Z",
        "updated_at": "2025-08-25T10:00:00.000000Z"
    }
}
```

#### 5. Update Caste Category
```
PUT /api/caste-categories/{id}
```

**Required Permission:** `manage_caste_categories`

**Request Body:**
```json
{
    "name": "Updated Category",
    "description": "Updated description"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Caste category updated successfully",
    "data": {
        "id": 1,
        "name": "Updated Category",
        "description": "Updated description",
        "castes": [...],
        "castes_count": 1,
        "created_at": "2025-08-25T10:00:00.000000Z",
        "updated_at": "2025-08-25T10:00:00.000000Z"
    }
}
```

#### 6. Delete Caste Category
```
DELETE /api/caste-categories/{id}
```

**Required Permission:** `manage_caste_categories`

**Response:**
```json
{
    "success": true,
    "message": "Caste category deleted successfully"
}
```

## Validation Rules

### Create/Update Caste Category
- `name`: Required, string, max 255 characters
- `description`: Optional, string

### Create/Update Caste (when assigning to category)
- `caste`: Required, string, max 255 characters
- `category_id`: Optional, must exist in `caste_categories` table

## Error Responses

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name": ["The name field is required."]
    }
}
```

### Not Found Error (404)
```json
{
    "success": false,
    "message": "Caste category not found"
}
```

### Permission Error (403)
```json
{
    "message": "User does not have the right permissions."
}
```

## Model Relationships

### CasteCategory Model
```php
class CasteCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function castes()
    {
        return $this->hasMany(Caste::class, 'category_id');
    }
}
```

### Caste Model
```php
class Caste extends Model
{
    protected $fillable = [
        'caste',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(CasteCategory::class, 'category_id');
    }
}
```

## Usage Examples

### Frontend Integration

#### 1. Fetch Categories with Castes
```javascript
const fetchCategories = async () => {
    try {
        const response = await fetch('/api/caste-categories');
        const data = await response.json();
        
        if (data.data) {
            return data.data;
        }
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
};
```

#### 2. Create New Category
```javascript
const createCategory = async (categoryData) => {
    try {
        const response = await fetch('/api/caste-categories', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
            body: JSON.stringify(categoryData)
        });
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error creating category:', error);
    }
};
```

#### 3. Get Castes by Category
```javascript
const getCastesByCategory = async (categoryId) => {
    try {
        const response = await fetch(`/api/caste-categories/${categoryId}/castes`);
        const data = await response.json();
        
        if (data.success) {
            return data.data;
        }
    } catch (error) {
        console.error('Error fetching castes:', error);
    }
};
```

### Backend Integration

#### 1. Access Castes from Category Model
```php
$category = CasteCategory::find(1);
$castes = $category->castes; // Returns collection of Caste models
```

#### 2. Access Category from Caste Model
```php
$caste = Caste::find(1);
$category = $caste->category; // Returns CasteCategory model or null
```

#### 3. Create Caste with Category
```php
$caste = Caste::create([
    'caste' => 'New Caste',
    'category_id' => 1
]);
```

## Migration History

### Migration: `2025_08_25_050825_create_caste_categories_table`
- Creates the `caste_categories` table with basic fields
- Adds index on name for performance

### Migration: `2025_08_25_052020_add_category_id_to_castes_table`
- Adds `category_id` field to `castes` table
- Creates foreign key constraint to `caste_categories.id`
- Adds index on `category_id` for performance

## Permissions

### Required Permissions
- `view_caste_categories` - View caste categories
- `manage_caste_categories` - Create, update, delete caste categories

### Role Assignment
These permissions are automatically assigned to:
- **Admin Role**: All permissions (including new ones)
- **Manager Role**: All caste-related permissions
- **Employee Role**: View permissions only
- **Viewer Role**: View permissions only
- **Guest Role**: No permissions

## Testing

Run the test script to verify functionality:
```bash
php test_caste_category_api.php
```

This will check:
- Database table structure
- Foreign key constraints
- Model relationships
- API routes
- Basic functionality with test data creation/deletion

## Key Changes from Previous Version

1. **Relationship Reversed**: Castes now belong to categories instead of categories belonging to castes
2. **Simplified Categories**: Categories no longer need a `caste_id` field
3. **Flexible Caste Assignment**: Castes can optionally belong to a category
4. **Better Organization**: Categories can contain multiple castes for logical grouping
5. **Cleaner API**: Simpler category management with castes as nested data
