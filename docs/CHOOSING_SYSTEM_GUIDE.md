# Panchayat and Village Choosing System Guide

## Overview

The choosing system provides structured options for panchayat and village types. This system replaces the previous string-based approach with a more robust foreign key relationship system while maintaining backward compatibility.

## Database Structure

### Panchayat Choosings Table
- **ID**: Primary key
- **Name**: Display name (e.g., "Mahanager pallika", "Gram panchayat")
- **Status**: Active status (1 = active, 0 = inactive)

### Village Choosings Table
- **ID**: Primary key
- **Name**: Display name (e.g., "Ward", "Village")
- **Status**: Active status (1 = active, 0 = inactive)

### Updated Tables

#### Panchayats Table
- Added `panchayat_choosing_id` (FK to panchayat_choosings)
- Kept `panchayat_choosing` (string) for backward compatibility

#### Villages Table
- Added `village_choosing_id` (FK to village_choosings)
- Kept `village_choosing` (string) for backward compatibility

#### Booths Table
- Added `panchayat_choosing_id` (FK to panchayat_choosings)
- Added `village_choosing_id` (FK to village_choosings)
- Kept `village_choosing` (string) for backward compatibility

## API Endpoints

### Panchayat Choosings

#### Get All Panchayat Choosings
```
GET /api/panchayat-choosings
```

#### Get Active Panchayat Choosings (for dropdowns)
```
GET /api/panchayat-choosings/active
```

#### Get Specific Panchayat Choosing
```
GET /api/panchayat-choosings/{id}
```

### Village Choosings

#### Get All Village Choosings
```
GET /api/village-choosings
```

#### Get Active Village Choosings (for dropdowns)
```
GET /api/village-choosings/active
```

#### Get Specific Village Choosing
```
GET /api/village-choosings/{id}
```

## Usage Examples

### Creating a Panchayat with Choosing ID

```json
POST /api/panchayats
{
    "loksabha_id": 1,
    "vidhansabha_id": 1,
    "block_id": 1,
    "panchayat_choosing_id": 1,  // Mahanager pallika
    "panchayat_choosing": "Mahanager pallika",  // Backward compatibility
    "panchayat_name": "Test Panchayat",
    "panchayat_status": "1"
}
```

### Creating a Village with Choosing ID

```json
POST /api/villages
{
    "loksabha_id": 1,
    "vidhansabha_id": 1,
    "block_id": 1,
    "panchayat_id": 1,
    "village_choosing_id": 2,  // Village
    "village_choosing": "Village",  // Backward compatibility
    "village_name": "Test Village",
    "village_status": "1"
}
```

### Creating a Booth with Choosing IDs

```json
POST /api/booths
{
    "loksabha_id": 1,
    "vidhansabha_id": 1,
    "block_id": 1,
    "panchayat_id": 1,
    "panchayat_choosing_id": 1,  // Mahanager pallika
    "panchayat_choosing": "Mahanager pallika",  // Backward compatibility
    "village_id": 1,
    "village_choosing_id": 2,  // Village
    "village_choosing": "Village",  // Backward compatibility
    "booth_name": "Test Booth",
    "booth_status": "1"
}
```

## Response Format

### Panchayat Response
```json
{
    "id": 1,
    "loksabha_id": 1,
    "vidhansabha_id": 1,
    "block_id": 1,
    "panchayat_choosing_id": 1,
    "panchayat_choosing": "Mahanager pallika",
    "panchayat_name": "Test Panchayat",
    "panchayat_status": "1",
    "panchayat_choosing_data": {
        "id": 1,
        "name": "Mahanager pallika",
        "status": "1"
    },
    "created_at": "2025-08-22T10:00:00.000000Z",
    "updated_at": "2025-08-22T10:00:00.000000Z"
}
```

### Village Response
```json
{
    "id": 1,
    "loksabha_id": 1,
    "vidhansabha_id": 1,
    "block_id": 1,
    "panchayat_id": 1,
    "village_choosing_id": 2,
    "village_choosing": "Village",
    "village_name": "Test Village",
    "village_status": "1",
    "village_choosing_data": {
        "id": 2,
        "name": "Village",
        "status": "1"
    },
    "created_at": "2025-08-22T10:00:00.000000Z",
    "updated_at": "2025-08-22T10:00:00.000000Z"
}
```

### Booth Response
```json
{
    "id": 1,
    "loksabha_id": 1,
    "vidhansabha_id": 1,
    "block_id": 1,
    "panchayat_id": 1,
    "panchayat_choosing_id": 1,
    "village_choosing": "Village",
    "village_choosing_id": 2,
    "village_id": 1,
    "booth_name": "Test Booth",
    "booth_status": "1",
    "panchayat_choosing_data": {
        "id": 1,
        "name": "Mahanager pallika",
        "status": "1"
    },
    "village_choosing_data": {
        "id": 2,
        "name": "Village",
        "status": "1"
    },
    "created_at": "2025-08-22T10:00:00.000000Z",
    "updated_at": "2025-08-22T10:00:00.000000Z"
}
```

## Default Seed Data

### Panchayat Choosings
1. **ID: 1** - Mahanager pallika
2. **ID: 2** - Gram panchayat

### Village Choosings
1. **ID: 1** - Ward
2. **ID: 2** - Village

## Migration and Setup

The choosing system is automatically set up when you run:

```bash
php artisan migrate
```

This will:
1. Create the choosing tables
2. Seed default data
3. Add foreign key columns to existing tables
4. Maintain backward compatibility

## Backward Compatibility

The system maintains full backward compatibility:
- Existing string fields (`panchayat_choosing`, `village_choosing`) continue to work
- New ID fields (`panchayat_choosing_id`, `village_choosing_id`) are optional
- Both fields can be used simultaneously
- API responses include both old and new data

## Frontend Integration

### Dropdown Population
Use the `/active` endpoints to populate dropdowns:

```javascript
// Get panchayat choosings for dropdown
fetch('/api/panchayat-choosings/active')
    .then(response => response.json())
    .then(data => {
        // Populate dropdown with data.panchayat_choosings
    });

// Get village choosings for dropdown
fetch('/api/village-choosings/active')
    .then(response => response.json())
    .then(data => {
        // Populate dropdown with data.village_choosings
    });
```

### Form Submission
Submit both ID and string values for maximum compatibility:

```javascript
const formData = {
    panchayat_choosing_id: selectedId,
    panchayat_choosing: selectedName,  // For backward compatibility
    // ... other fields
};
```

## Benefits

1. **Structured Data**: Consistent options across the system
2. **Validation**: Foreign key constraints ensure data integrity
3. **Flexibility**: Easy to add new options without code changes
4. **Backward Compatibility**: Existing code continues to work
5. **Performance**: Efficient queries with indexed foreign keys
6. **Maintainability**: Centralized management of choosing options
