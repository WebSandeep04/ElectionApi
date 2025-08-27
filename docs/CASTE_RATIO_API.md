# Cast Ratio API Documentation

## Overview
The Cast Ratio API provides comprehensive functionality for managing caste ratios across different geographical hierarchies (Lok Sabha, Vidhan Sabha, Block, Panchayat, Village, Booth) with support for caste categories. Each cast ratio can be assigned to a category (General, OBC, SC, ST, EWS) or left unassigned.

## Base URL
```
/api/cast-ratios
```

## Authentication
- **Public endpoints**: `GET` operations (list, show, filter)
- **Protected endpoints**: `POST`, `PUT`, `DELETE` operations require authentication and appropriate permissions

## Models and Relationships

### CastRatio Model
```php
// App\Models\CastRatio
protected $fillable = [
    'loksabha_id',
    'vidhansabha_id',
    'block_id',
    'panchayat_choosing_id',
    'panchayat_id',
    'village_choosing_id',
    'village_id',
    'booth_id',
    'caste_id',
    'category_id',
    'caste_ratio',
];

// Relationships
public function category(): BelongsTo
{
    return $this->belongsTo(CasteCategory::class, 'category_id');
}
```

## API Endpoints

### 1. List All Cast Ratios
**GET** `/api/cast-ratios`

Returns a paginated list of all cast ratios with their category information.

#### Query Parameters
- `category_id` (optional): Filter cast ratios by category ID
- `category_name` (optional): Search cast ratios by category name (partial match)
- `caste_id` (optional): Filter by specific caste ID
- `loksabha_id` (optional): Filter by Lok Sabha ID
- `vidhansabha_id` (optional): Filter by Vidhan Sabha ID
- `block_id` (optional): Filter by Block ID
- `panchayat_id` (optional): Filter by Panchayat ID
- `panchayat_choosing_id` (optional): Filter by Panchayat Choosing ID
- `village_id` (optional): Filter by Village ID
- `village_choosing_id` (optional): Filter by Village Choosing ID
- `booth_id` (optional): Filter by Booth ID
- `search` (optional): Search by caste name (partial match)
- `sort_by` (optional): Sort field (default: created_at)
- `sort_order` (optional): Sort direction (asc/desc, default: desc)
- `per_page` (optional): Items per page (default: 10)
- `page` (optional): Page number for pagination

#### Example Request
```bash
GET /api/cast-ratios?category_id=1&loksabha_id=1&per_page=15
```

#### Example Response
```json
{
    "cast_ratios": [
        {
            "caste_ratio_id": 1,
            "loksabha_id": 1,
            "vidhansabha_id": 1,
            "block_id": 1,
            "panchayat_choosing_id": 1,
            "panchayat_id": 1,
            "village_choosing_id": 1,
            "village_id": 1,
            "booth_id": 1,
            "caste_id": 1,
            "category_id": 1,
            "caste_ratio": 25,
            "created_at": "2025-01-20T10:00:00.000000Z",
            "updated_at": "2025-01-20T10:00:00.000000Z",
            "loksabha": {
                "id": 1,
                "loksabha_name": "Lok Sabha 1"
            },
            "vidhansabha": {
                "id": 1,
                "vidhansabha_name": "Vidhan Sabha 1"
            },
            "block": {
                "id": 1,
                "block_name": "Block 1"
            },
            "panchayat": {
                "id": 1,
                "panchayat_name": "Panchayat 1"
            },
            "panchayat_choosing_data": {
                "id": 1,
                "name": "Panchayat Choosing 1",
                "status": 1
            },
            "village_choosing_data": {
                "id": 1,
                "name": "Village Choosing 1",
                "status": 1
            },
            "village_data": {
                "id": 1,
                "village_name": "Village 1"
            },
            "booth_data": {
                "id": 1,
                "booth_name": "Booth 1"
            },
            "caste": {
                "id": 1,
                "caste_name": "Brahmin"
            },
            "category_data": {
                "id": 1,
                "name": "General",
                "description": "General category for unreserved castes"
            },
            "village_choosing_label": "Ward"
        }
    ],
    "pagination": {
        "total": 50,
        "per_page": 15,
        "current_page": 1,
        "last_page": 4,
        "from": 1,
        "to": 15,
        "has_more_pages": true
    }
}
```

### 2. Get Specific Cast Ratio
**GET** `/api/cast-ratios/{id}`

Returns a specific cast ratio with its category information.

#### Example Request
```bash
GET /api/cast-ratios/1
```

#### Example Response
```json
{
    "data": {
        "caste_ratio_id": 1,
        "loksabha_id": 1,
        "vidhansabha_id": 1,
        "block_id": 1,
        "panchayat_choosing_id": 1,
        "panchayat_id": 1,
        "village_choosing_id": 1,
        "village_id": 1,
        "booth_id": 1,
        "caste_id": 1,
        "category_id": 1,
        "caste_ratio": 25,
        "created_at": "2025-01-20T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z",
        "loksabha": {
            "id": 1,
            "loksabha_name": "Lok Sabha 1"
        },
        "vidhansabha": {
            "id": 1,
            "vidhansabha_name": "Vidhan Sabha 1"
        },
        "block": {
            "id": 1,
            "block_name": "Block 1"
        },
        "panchayat": {
            "id": 1,
            "panchayat_name": "Panchayat 1"
        },
        "panchayat_choosing_data": {
            "id": 1,
            "name": "Panchayat Choosing 1",
            "status": 1
        },
        "village_choosing_data": {
            "id": 1,
            "name": "Village Choosing 1",
            "status": 1
        },
        "village_data": {
            "id": 1,
            "village_name": "Village 1"
        },
        "booth_data": {
            "id": 1,
            "booth_name": "Booth 1"
        },
        "caste": {
            "id": 1,
            "caste_name": "Brahmin"
        },
        "category_data": {
            "id": 1,
            "name": "General",
            "description": "General category for unreserved castes"
        }
    }
}
```

### 3. Get Cast Ratios by Category
**GET** `/api/cast-ratios/category/{categoryId}`

Returns all cast ratios belonging to a specific category.

#### Example Request
```bash
GET /api/cast-ratios/category/1
```

#### Example Response
```json
{
    "success": true,
    "data": [
        {
            "caste_ratio_id": 1,
            "loksabha_id": 1,
            "vidhansabha_id": 1,
            "block_id": 1,
            "panchayat_choosing_id": 1,
            "panchayat_id": 1,
            "village_choosing_id": 1,
            "village_id": 1,
            "booth_id": 1,
            "caste_id": 1,
            "category_id": 1,
            "caste_ratio": 25,
            "created_at": "2025-01-20T10:00:00.000000Z",
            "updated_at": "2025-01-20T10:00:00.000000Z",
            "caste": {
                "id": 1,
                "caste_name": "Brahmin"
            },
            "category_data": {
                "id": 1,
                "name": "General",
                "description": "General category for unreserved castes"
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 1,
        "from": 1,
        "to": 1
    }
}
```

### 4. Get Unassigned Cast Ratios
**GET** `/api/cast-ratios/unassigned`

Returns all cast ratios that are not assigned to any category.

#### Example Request
```bash
GET /api/cast-ratios/unassigned
```

#### Example Response
```json
{
    "success": true,
    "data": [
        {
            "caste_ratio_id": 2,
            "loksabha_id": 1,
            "vidhansabha_id": 1,
            "block_id": 1,
            "panchayat_choosing_id": 1,
            "panchayat_id": 1,
            "village_choosing_id": 1,
            "village_id": 1,
            "booth_id": 1,
            "caste_id": 2,
            "category_id": null,
            "caste_ratio": 30,
            "created_at": "2025-01-20T10:00:00.000000Z",
            "updated_at": "2025-01-20T10:00:00.000000Z",
            "caste": {
                "id": 2,
                "caste_name": "Rajput"
            },
            "category_data": null
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 1,
        "from": 1,
        "to": 1
    }
}
```

### 5. Get Cast Ratios by Panchayat Choosing
**GET** `/api/cast-ratios/panchayat-choosing/{panchayatChoosingId}`

Returns cast ratios filtered by panchayat choosing ID.

#### Example Request
```bash
GET /api/cast-ratios/panchayat-choosing/1
```

### 6. Get Cast Ratios by Village Choosing
**GET** `/api/cast-ratios/village-choosing/{villageChoosingId}`

Returns cast ratios filtered by village choosing ID.

#### Example Request
```bash
GET /api/cast-ratios/village-choosing/1
```

### 7. Create New Cast Ratio
**POST** `/api/cast-ratios`

Creates a new cast ratio with optional category assignment.

**Permission Required**: `manage_cast_ratios`

#### Request Body
```json
{
    "loksabha_id": 1,
    "vidhansabha_id": 1,
    "block_id": 1,
    "panchayat_choosing_id": 1,
    "panchayat_id": 1,
    "village_choosing_id": 1,
    "village_id": 1,
    "booth_id": 1,
    "caste_id": 1,
    "category_id": 1,
    "caste_ratio": 25
}
```

#### Validation Rules
- `loksabha_id`: optional, must exist in lok_sabhas table
- `vidhansabha_id`: optional, must exist in vidhan_sabhas table
- `block_id`: optional, must exist in blocks table
- `panchayat_choosing_id`: optional, must exist in panchayat_choosings table
- `panchayat_id`: optional, must exist in panchayats table
- `village_choosing_id`: optional, must exist in village_choosings table
- `village_id`: optional, must exist in villages table
- `booth_id`: optional, must exist in booths table
- `caste_id`: required, must exist in castes table
- `category_id`: optional, must exist in caste_categories table
- `caste_ratio`: required, integer between 0 and 100

#### Example Request
```bash
POST /api/cast-ratios
Content-Type: application/json
Authorization: Bearer {token}

{
    "loksabha_id": 1,
    "vidhansabha_id": 1,
    "block_id": 1,
    "panchayat_id": 1,
    "village_id": 1,
    "booth_id": 1,
    "caste_id": 1,
    "category_id": 1,
    "caste_ratio": 25
}
```

#### Example Response
```json
{
    "data": {
        "caste_ratio_id": 3,
        "loksabha_id": 1,
        "vidhansabha_id": 1,
        "block_id": 1,
        "panchayat_choosing_id": null,
        "panchayat_id": 1,
        "village_choosing_id": null,
        "village_id": 1,
        "booth_id": 1,
        "caste_id": 1,
        "category_id": 1,
        "caste_ratio": 25,
        "created_at": "2025-01-20T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z",
        "loksabha": {
            "id": 1,
            "loksabha_name": "Lok Sabha 1"
        },
        "vidhansabha": {
            "id": 1,
            "vidhansabha_name": "Vidhan Sabha 1"
        },
        "block": {
            "id": 1,
            "block_name": "Block 1"
        },
        "panchayat": {
            "id": 1,
            "panchayat_name": "Panchayat 1"
        },
        "village_data": {
            "id": 1,
            "village_name": "Village 1"
        },
        "booth_data": {
            "id": 1,
            "booth_name": "Booth 1"
        },
        "caste": {
            "id": 1,
            "caste_name": "Brahmin"
        },
        "category_data": {
            "id": 1,
            "name": "General",
            "description": "General category for unreserved castes"
        }
    },
    "message": "Cast ratio created successfully"
}
```

### 8. Update Cast Ratio
**PUT** `/api/cast-ratios/{id}`

Updates an existing cast ratio, including category assignment.

**Permission Required**: `manage_cast_ratios`

#### Request Body
```json
{
    "caste_ratio": 30,
    "category_id": 2
}
```

#### Validation Rules
- All fields are optional (use `sometimes` validation)
- Same validation rules as create endpoint

#### Example Request
```bash
PUT /api/cast-ratios/1
Content-Type: application/json
Authorization: Bearer {token}

{
    "caste_ratio": 30,
    "category_id": 2
}
```

#### Example Response
```json
{
    "data": {
        "caste_ratio_id": 1,
        "loksabha_id": 1,
        "vidhansabha_id": 1,
        "block_id": 1,
        "panchayat_choosing_id": 1,
        "panchayat_id": 1,
        "village_choosing_id": 1,
        "village_id": 1,
        "booth_id": 1,
        "caste_id": 1,
        "category_id": 2,
        "caste_ratio": 30,
        "created_at": "2025-01-20T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z",
        "caste": {
            "id": 1,
            "caste_name": "Brahmin"
        },
        "category_data": {
            "id": 2,
            "name": "OBC",
            "description": "Other Backward Classes category"
        }
    },
    "message": "Cast ratio updated successfully"
}
```

### 9. Delete Cast Ratio
**DELETE** `/api/cast-ratios/{id}`

Deletes a cast ratio.

**Permission Required**: `manage_cast_ratios`

#### Example Request
```bash
DELETE /api/cast-ratios/1
Authorization: Bearer {token}
```

#### Example Response
```json
{
    "message": "Cast ratio deleted successfully"
}
```

### 10. Assign Cast Ratio to Category
**POST** `/api/cast-ratios/{id}/assign-category`

Assigns a cast ratio to a specific category.

**Permission Required**: `manage_cast_ratios`

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
POST /api/cast-ratios/1/assign-category
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
    "message": "Cast ratio assigned to category successfully",
    "data": {
        "caste_ratio_id": 1,
        "loksabha_id": 1,
        "vidhansabha_id": 1,
        "block_id": 1,
        "panchayat_choosing_id": 1,
        "panchayat_id": 1,
        "village_choosing_id": 1,
        "village_id": 1,
        "booth_id": 1,
        "caste_id": 1,
        "category_id": 1,
        "caste_ratio": 25,
        "created_at": "2025-01-20T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z",
        "caste": {
            "id": 1,
            "caste_name": "Brahmin"
        },
        "category_data": {
            "id": 1,
            "name": "General",
            "description": "General category for unreserved castes"
        }
    }
}
```

### 11. Remove Cast Ratio from Category
**POST** `/api/cast-ratios/{id}/remove-category`

Removes a cast ratio from its current category (sets category_id to null).

**Permission Required**: `manage_cast_ratios`

#### Example Request
```bash
POST /api/cast-ratios/1/remove-category
Authorization: Bearer {token}
```

#### Example Response
```json
{
    "success": true,
    "message": "Cast ratio removed from category successfully",
    "data": {
        "caste_ratio_id": 1,
        "loksabha_id": 1,
        "vidhansabha_id": 1,
        "block_id": 1,
        "panchayat_choosing_id": 1,
        "panchayat_id": 1,
        "village_choosing_id": 1,
        "village_id": 1,
        "booth_id": 1,
        "caste_id": 1,
        "category_id": null,
        "caste_ratio": 25,
        "created_at": "2025-01-20T10:00:00.000000Z",
        "updated_at": "2025-01-20T10:00:00.000000Z",
        "caste": {
            "id": 1,
            "caste_name": "Brahmin"
        },
        "category_data": null
    }
}
```

## Frontend Integration Examples

### JavaScript/Fetch API

#### Get All Cast Ratios with Categories
```javascript
fetch('/api/cast-ratios')
    .then(response => response.json())
    .then(data => {
        data.cast_ratios.forEach(ratio => {
            const categoryName = ratio.category_data ? ratio.category_data.name : 'Unassigned';
            console.log(`${ratio.caste.caste_name}: ${ratio.caste_ratio}% -> ${categoryName}`);
        });
    });
```

#### Get Cast Ratios by Category
```javascript
function getCastRatiosByCategory(categoryId) {
    fetch(`/api/cast-ratios/category/${categoryId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Cast ratios in category:', data.data);
            }
        });
}
```

#### Create New Cast Ratio
```javascript
function createCastRatio(castRatioData, token) {
    fetch('/api/cast-ratios', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(castRatioData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Cast ratio created:', data.data);
    });
}
```

#### Assign Cast Ratio to Category
```javascript
function assignCastRatioToCategory(castRatioId, categoryId, token) {
    fetch(`/api/cast-ratios/${castRatioId}/assign-category`, {
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
            console.log('Cast ratio assigned successfully');
        }
    });
}
```

### React Example
```jsx
import React, { useState, useEffect } from 'react';

function CastRatioManager() {
    const [castRatios, setCastRatios] = useState([]);
    const [categories, setCategories] = useState([]);
    const [selectedCategory, setSelectedCategory] = useState('');

    useEffect(() => {
        // Load cast ratios
        fetch('/api/cast-ratios')
            .then(response => response.json())
            .then(data => setCastRatios(data.cast_ratios));

        // Load categories
        fetch('/api/caste-categories')
            .then(response => response.json())
            .then(data => setCategories(data.data));
    }, []);

    const handleCategoryChange = (categoryId) => {
        setSelectedCategory(categoryId);
        if (categoryId) {
            fetch(`/api/cast-ratios/category/${categoryId}`)
                .then(response => response.json())
                .then(data => setCastRatios(data.data));
        } else {
            fetch('/api/cast-ratios')
                .then(response => response.json())
                .then(data => setCastRatios(data.cast_ratios));
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

            <table>
                <thead>
                    <tr>
                        <th>Caste</th>
                        <th>Ratio</th>
                        <th>Category</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    {castRatios.map(ratio => (
                        <tr key={ratio.caste_ratio_id}>
                            <td>{ratio.caste?.caste_name}</td>
                            <td>{ratio.caste_ratio}%</td>
                            <td>{ratio.category_data?.name || 'Unassigned'}</td>
                            <td>
                                {ratio.loksabha?.loksabha_name} > 
                                {ratio.vidhansabha?.vidhansabha_name} > 
                                {ratio.block?.block_name} > 
                                {ratio.panchayat?.panchayat_name} > 
                                {ratio.village_data?.village_name} > 
                                {ratio.booth_data?.booth_name}
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
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
        "caste_id": ["The caste id field is required."],
        "caste_ratio": ["The caste ratio must be between 0 and 100."],
        "category_id": ["The selected category id is invalid."]
    }
}
```

#### Not Found Error (404)
```json
{
    "message": "No query results for model [App\\Models\\CastRatio] 999"
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

### cast_ratios Table
```sql
CREATE TABLE cast_ratios (
    caste_ratio_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    loksabha_id BIGINT UNSIGNED NULL,
    vidhansabha_id BIGINT UNSIGNED NULL,
    block_id BIGINT UNSIGNED NULL,
    panchayat_choosing_id BIGINT UNSIGNED NULL,
    panchayat_id BIGINT UNSIGNED NULL,
    village_choosing_id BIGINT UNSIGNED NULL,
    village_id BIGINT UNSIGNED NULL,
    booth_id BIGINT UNSIGNED NULL,
    caste_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NULL,
    caste_ratio INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (loksabha_id) REFERENCES lok_sabhas(id) ON DELETE SET NULL,
    FOREIGN KEY (vidhansabha_id) REFERENCES vidhan_sabhas(id) ON DELETE SET NULL,
    FOREIGN KEY (block_id) REFERENCES blocks(id) ON DELETE SET NULL,
    FOREIGN KEY (panchayat_choosing_id) REFERENCES panchayat_choosings(id) ON DELETE SET NULL,
    FOREIGN KEY (panchayat_id) REFERENCES panchayats(id) ON DELETE SET NULL,
    FOREIGN KEY (village_choosing_id) REFERENCES village_choosings(id) ON DELETE SET NULL,
    FOREIGN KEY (village_id) REFERENCES villages(id) ON DELETE SET NULL,
    FOREIGN KEY (booth_id) REFERENCES booths(id) ON DELETE SET NULL,
    FOREIGN KEY (caste_id) REFERENCES castes(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES caste_categories(id) ON DELETE SET NULL,
    INDEX (category_id),
    INDEX (caste_id),
    INDEX (loksabha_id),
    INDEX (vidhansabha_id),
    INDEX (block_id),
    INDEX (panchayat_id),
    INDEX (village_id),
    INDEX (booth_id)
);
```

## Geographical Hierarchy

The Cast Ratio system supports a complete geographical hierarchy:

1. **Lok Sabha** (Parliamentary constituency)
2. **Vidhan Sabha** (State legislative assembly constituency)
3. **Block** (Administrative block)
4. **Panchayat** (Local government)
5. **Village** (Rural settlement)
6. **Booth** (Polling station)

Each level can have choosing systems (panchayat_choosing, village_choosing) for flexible categorization.

## Caste Categories

The system supports five main caste categories:

1. **General** - General category for unreserved castes
2. **OBC** - Other Backward Classes category
3. **SC** - Scheduled Caste category
4. **ST** - Scheduled Tribe category
5. **EWS** - Economically Weaker Section category

## Testing

Use the provided test scripts to verify functionality:
- `test_cast_ratio_category_api.php` - Tests the updated CastRatio API with category support
- `test_caste_category_api.php` - Tests the CasteCategory API
- `test_caste_controller_api.php` - Tests the Caste API

## Use Cases

### 1. Electoral Analysis
- Analyze caste distribution across different geographical areas
- Track caste ratios by category for reservation policies
- Generate reports for electoral planning

### 2. Social Welfare Planning
- Identify areas with specific caste category concentrations
- Plan targeted welfare programs based on caste ratios
- Monitor the effectiveness of reservation policies

### 3. Administrative Management
- Manage caste ratio data at multiple administrative levels
- Assign and reassign castes to categories as needed
- Generate hierarchical reports for different administrative units

