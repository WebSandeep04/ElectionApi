# Cast Ratio API Documentation

## Overview
The Cast Ratio API provides endpoints to manage cast ratio data across different administrative levels (Lok Sabha, Vidhan Sabha, Block, Panchayat, Village, Booth).

## Data Model

### CastRatio Model
```php
{
    "caste_ratio_id": "bigint",
    "loksabha_id": "bigint|null",
    "vidhansabha_id": "bigint|null", 
    "block_id": "bigint|null",
    "panchayat_id": "bigint|null",
    "village_choosing": "bigint|null",
    "village_id": "bigint|null",
    "booth_id": "bigint|null",
    "caste_id": "bigint",
    "caste_ratio": "integer",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```

### Relationships
- `lokSabha` - Belongs to LokSabha
- `vidhanSabha` - Belongs to VidhanSabha  
- `block` - Belongs to Block
- `panchayat` - Belongs to Panchayat
- `villageChoosing` - Belongs to Village
- `village` - Belongs to Village
- `booth` - Belongs to Booth
- `caste` - Belongs to Caste

## API Endpoints

### 1. List Cast Ratios (Public Read)
**GET** `/api/cast-ratios`

#### Query Parameters
- `search` (string, optional) - Search by caste name
- `caste_id` (integer, optional) - Filter by specific caste
- `loksabha_id` (integer, optional) - Filter by Lok Sabha
- `vidhansabha_id` (integer, optional) - Filter by Vidhan Sabha
- `block_id` (integer, optional) - Filter by Block
- `panchayat_id` (integer, optional) - Filter by Panchayat
- `village_id` (integer, optional) - Filter by Village
- `booth_id` (integer, optional) - Filter by Booth
- `sort_by` (string, optional) - Sort field (default: created_at)
- `sort_order` (string, optional) - Sort direction: asc/desc (default: desc)
- `per_page` (integer, optional) - Items per page (default: 10)

#### Response
```json
{
    "cast_ratios": [
        {
            "caste_ratio_id": 1,
            "loksabha_id": 1,
            "vidhansabha_id": 1,
            "block_id": 1,
            "panchayat_id": 1,
            "village_choosing": 1,
            "village_id": 1,
            "booth_id": 1,
            "caste_id": 1,
            "caste_ratio": 25,
            "created_at": "2025-08-21T05:16:12.000000Z",
            "updated_at": "2025-08-21T05:16:12.000000Z",
            "loksabha": {
                "id": 1,
                "loksabha_name": "Sample Lok Sabha"
            },
            "vidhansabha": {
                "id": 1,
                "vidhansabha_name": "Sample Vidhan Sabha"
            },
            "block": {
                "id": 1,
                "block_name": "Sample Block"
            },
            "panchayat": {
                "id": 1,
                "panchayat_name": "Sample Panchayat"
            },
            "village_choosing_data": {
                "id": 1,
                "village_name": "Sample Village"
            },
            "village_data": {
                "id": 1,
                "village_name": "Sample Village"
            },
            "booth_data": {
                "id": 1,
                "booth_name": "Sample Booth"
            },
            "caste": {
                "id": 1,
                "caste_name": "Sample Caste"
            }
        }
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

### 2. Get Single Cast Ratio (Public Read)
**GET** `/api/cast-ratios/{id}`

#### Response
```json
{
    "data": {
        "caste_ratio_id": 1,
        "loksabha_id": 1,
        "vidhansabha_id": 1,
        "block_id": 1,
        "panchayat_id": 1,
        "village_choosing": 1,
        "village_id": 1,
        "booth_id": 1,
        "caste_id": 1,
        "caste_ratio": 25,
        "created_at": "2025-08-21T05:16:12.000000Z",
        "updated_at": "2025-08-21T05:16:12.000000Z",
        "loksabha": { ... },
        "vidhansabha": { ... },
        "block": { ... },
        "panchayat": { ... },
        "village_choosing_data": { ... },
        "village_data": { ... },
        "booth_data": { ... },
        "caste": { ... }
    }
}
```

### 3. Create Cast Ratio (Protected Write)
**POST** `/api/cast-ratios`

**Required Permission:** `manage_cast_ratios`

#### Request Body
```json
{
    "loksabha_id": 1,
    "vidhansabha_id": 1,
    "block_id": 1,
    "panchayat_id": 1,
    "village_choosing": 1,
    "village_id": 1,
    "booth_id": 1,
    "caste_id": 1,
    "caste_ratio": 25
}
```

#### Validation Rules
- `loksabha_id` - nullable, exists in lok_sabhas table
- `vidhansabha_id` - nullable, exists in vidhan_sabhas table
- `block_id` - nullable, exists in blocks table
- `panchayat_id` - nullable, exists in panchayats table
- `village_choosing` - nullable, exists in villages table
- `village_id` - nullable, exists in villages table
- `booth_id` - nullable, exists in booths table
- `caste_id` - required, exists in castes table
- `caste_ratio` - required, integer, min: 0, max: 100

#### Response
```json
{
    "data": {
        "caste_ratio_id": 1,
        "loksabha_id": 1,
        "vidhansabha_id": 1,
        "block_id": 1,
        "panchayat_id": 1,
        "village_choosing": 1,
        "village_id": 1,
        "booth_id": 1,
        "caste_id": 1,
        "caste_ratio": 25,
        "created_at": "2025-08-21T05:16:12.000000Z",
        "updated_at": "2025-08-21T05:16:12.000000Z",
        "loksabha": { ... },
        "vidhansabha": { ... },
        "block": { ... },
        "panchayat": { ... },
        "village_choosing_data": { ... },
        "village_data": { ... },
        "booth_data": { ... },
        "caste": { ... }
    },
    "message": "Cast ratio created successfully"
}
```

### 4. Update Cast Ratio (Protected Write)
**PUT/PATCH** `/api/cast-ratios/{id}`

**Required Permission:** `manage_cast_ratios`

#### Request Body
```json
{
    "caste_ratio": 30,
    "booth_id": 2
}
```

#### Validation Rules
Same as create, but all fields are optional (sometimes validation).

#### Response
```json
{
    "data": {
        "caste_ratio_id": 1,
        "loksabha_id": 1,
        "vidhansabha_id": 1,
        "block_id": 1,
        "panchayat_id": 1,
        "village_choosing": 1,
        "village_id": 1,
        "booth_id": 2,
        "caste_id": 1,
        "caste_ratio": 30,
        "created_at": "2025-08-21T05:16:12.000000Z",
        "updated_at": "2025-08-21T05:16:12.000000Z",
        "loksabha": { ... },
        "vidhansabha": { ... },
        "block": { ... },
        "panchayat": { ... },
        "village_choosing_data": { ... },
        "village_data": { ... },
        "booth_data": { ... },
        "caste": { ... }
    },
    "message": "Cast ratio updated successfully"
}
```

### 5. Delete Cast Ratio (Protected Write)
**DELETE** `/api/cast-ratios/{id}`

**Required Permission:** `manage_cast_ratios`

#### Response
```json
{
    "message": "Cast ratio deleted successfully"
}
```

## Error Responses

### Validation Error (422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "caste_id": ["The caste id field is required."],
        "caste_ratio": ["The caste ratio must be an integer."]
    }
}
```

### Permission Denied (403)
```json
{
    "message": "You do not have permission to perform this action."
}
```

### Not Found (404)
```json
{
    "message": "No query results for model [App\\Models\\CastRatio] 1"
}
```

## Usage Examples

### Filter by Caste
```bash
GET /api/cast-ratios?caste_id=1
```

### Search by Caste Name
```bash
GET /api/cast-ratios?search=brahmin
```

### Filter by Administrative Level
```bash
GET /api/cast-ratios?loksabha_id=1&vidhansabha_id=1&block_id=1
```

### Sort by Caste Ratio
```bash
GET /api/cast-ratios?sort_by=caste_ratio&sort_order=desc
```

### Pagination
```bash
GET /api/cast-ratios?per_page=20&page=2
```

## Frontend Integration

### Permission Check
```javascript
// Check if user can manage cast ratios
if (user.hasPermission('manage_cast_ratios')) {
    // Show create/edit/delete buttons
}
```

### API Calls
```javascript
// Fetch cast ratios with filters
const response = await fetch('/api/cast-ratios?caste_id=1&per_page=50');
const data = await response.json();

// Create new cast ratio
const createResponse = await fetch('/api/cast-ratios', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({
        caste_id: 1,
        caste_ratio: 25,
        loksabha_id: 1
    })
});
```

## Notes

1. **Hierarchical Data**: Cast ratios can be set at any administrative level (Lok Sabha, Vidhan Sabha, Block, Panchayat, Village, Booth)
2. **Flexible Structure**: All location fields are optional, allowing for different levels of granularity
3. **Caste Ratio Range**: Caste ratio must be between 0 and 100
4. **Relationships**: All location relationships are loaded when requested for comprehensive data display
5. **Search**: Search functionality works on caste names for easy filtering
6. **Permissions**: Write operations require the `manage_cast_ratios` permission
