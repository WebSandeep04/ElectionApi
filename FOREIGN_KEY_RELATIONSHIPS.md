# Foreign Key Relationships Documentation

## Overview
This document outlines the complete foreign key relationships built between all tables in the hierarchical structure: **Lok Sabha → Vidhan Sabha → Block → Panchayat → Village → Booth**.

## Database Schema

### 1. Lok Sabha (lok_sabhas)
- **Primary Key**: `id` (bigint, auto-increment)
- **Fields**: `loksabha_name`, `status`, `created_at`, `updated_at`
- **Relationships**: Parent to all other tables

### 2. Vidhan Sabha (vidhan_sabhas)
- **Primary Key**: `id` (bigint, auto-increment)
- **Foreign Keys**:
  - `loksabha_id` → `lok_sabhas.id` (CASCADE DELETE)
- **Fields**: `vidhansabha_name`, `vidhan_status`, `created_at`, `updated_at`

### 3. Block (blocks)
- **Primary Key**: `id` (bigint, auto-increment)
- **Foreign Keys**:
  - `loksabha_id` → `lok_sabhas.id` (CASCADE DELETE)
  - `vidhansabha_id` → `vidhan_sabhas.id` (CASCADE DELETE)
- **Fields**: `block_name`, `block_status`, `created_at`, `updated_at`

### 4. Panchayat (panchayats)
- **Primary Key**: `id` (bigint, auto-increment)
- **Foreign Keys**:
  - `loksabha_id` → `lok_sabhas.id` (CASCADE DELETE)
  - `vidhansabha_id` → `vidhan_sabhas.id` (CASCADE DELETE)
  - `block_id` → `blocks.id` (CASCADE DELETE)
- **Fields**: `panchayat_choosing`, `panchayat_name`, `panchayat_status`, `created_at`, `updated_at`

### 5. Village (villages)
- **Primary Key**: `id` (bigint, auto-increment)
- **Foreign Keys**:
  - `loksabha_id` → `lok_sabhas.id` (CASCADE DELETE)
  - `vidhansabha_id` → `vidhan_sabhas.id` (CASCADE DELETE)
  - `block_id` → `blocks.id` (CASCADE DELETE)
  - `panchayat_id` → `panchayats.id` (CASCADE DELETE)
- **Fields**: `village_choosing`, `village_name`, `village_status`, `created_at`, `updated_at`

### 6. Booth (booths)
- **Primary Key**: `id` (bigint, auto-increment)
- **Foreign Keys**:
  - `loksabha_id` → `lok_sabhas.id` (CASCADE DELETE)
  - `vidhansabha_id` → `vidhan_sabhas.id` (CASCADE DELETE)
  - `block_id` → `blocks.id` (CASCADE DELETE)
  - `panchayat_id` → `panchayats.id` (CASCADE DELETE)
  - `village_id` → `villages.id` (CASCADE DELETE)
- **Fields**: `village_choosing`, `booth_name`, `booth_status`, `created_at`, `updated_at`

## Eloquent Relationships

### LokSabha Model
```php
// One-to-Many Relationships
public function vidhanSabhas()
{
    return $this->hasMany(VidhanSabha::class, 'loksabha_id');
}

public function blocks()
{
    return $this->hasMany(Block::class, 'loksabha_id');
}

public function panchayats()
{
    return $this->hasMany(Panchayat::class, 'loksabha_id');
}

public function villages()
{
    return $this->hasMany(Village::class, 'loksabha_id');
}

public function booths()
{
    return $this->hasMany(Booth::class, 'loksabha_id');
}
```

### VidhanSabha Model
```php
// Belongs To
public function lokSabha()
{
    return $this->belongsTo(LokSabha::class, 'loksabha_id');
}

// One-to-Many Relationships
public function blocks()
{
    return $this->hasMany(Block::class, 'vidhansabha_id');
}

public function panchayats()
{
    return $this->hasMany(Panchayat::class, 'vidhansabha_id');
}

public function villages()
{
    return $this->hasMany(Village::class, 'vidhansabha_id');
}

public function booths()
{
    return $this->hasMany(Booth::class, 'vidhansabha_id');
}
```

### Block Model
```php
// Belongs To Relationships
public function lokSabha()
{
    return $this->belongsTo(LokSabha::class, 'loksabha_id');
}

public function vidhanSabha()
{
    return $this->belongsTo(VidhanSabha::class, 'vidhansabha_id');
}

// One-to-Many Relationships
public function panchayats()
{
    return $this->hasMany(Panchayat::class, 'block_id');
}

public function villages()
{
    return $this->hasMany(Village::class, 'block_id');
}

public function booths()
{
    return $this->hasMany(Booth::class, 'block_id');
}
```

### Panchayat Model
```php
// Belongs To Relationships
public function lokSabha()
{
    return $this->belongsTo(LokSabha::class, 'loksabha_id');
}

public function vidhanSabha()
{
    return $this->belongsTo(VidhanSabha::class, 'vidhansabha_id');
}

public function block()
{
    return $this->belongsTo(Block::class, 'block_id');
}

// One-to-Many Relationships
public function villages()
{
    return $this->hasMany(Village::class, 'panchayat_id');
}

public function booths()
{
    return $this->hasMany(Booth::class, 'panchayat_id');
}
```

### Village Model
```php
// Belongs To Relationships
public function lokSabha()
{
    return $this->belongsTo(LokSabha::class, 'loksabha_id');
}

public function vidhanSabha()
{
    return $this->belongsTo(VidhanSabha::class, 'vidhansabha_id');
}

public function block()
{
    return $this->belongsTo(Block::class, 'block_id');
}

public function panchayat()
{
    return $this->belongsTo(Panchayat::class, 'panchayat_id');
}

// One-to-Many Relationships
public function booths()
{
    return $this->hasMany(Booth::class, 'village_id');
}
```

### Booth Model
```php
// Belongs To Relationships
public function lokSabha()
{
    return $this->belongsTo(LokSabha::class, 'loksabha_id');
}

public function vidhanSabha()
{
    return $this->belongsTo(VidhanSabha::class, 'vidhansabha_id');
}

public function block()
{
    return $this->belongsTo(Block::class, 'block_id');
}

public function panchayat()
{
    return $this->belongsTo(Panchayat::class, 'panchayat_id');
}

public function village()
{
    return $this->belongsTo(Village::class, 'village_id');
}
```

## Cascade Delete Behavior

All foreign key relationships are configured with `ON DELETE CASCADE`, which means:

1. **Deleting a Lok Sabha** will automatically delete all related:
   - Vidhan Sabhas
   - Blocks
   - Panchayats
   - Villages
   - Booths

2. **Deleting a Vidhan Sabha** will automatically delete all related:
   - Blocks
   - Panchayats
   - Villages
   - Booths

3. **Deleting a Block** will automatically delete all related:
   - Panchayats
   - Villages
   - Booths

4. **Deleting a Panchayat** will automatically delete all related:
   - Villages
   - Booths

5. **Deleting a Village** will automatically delete all related:
   - Booths

## API Validation Rules

All controllers now use proper foreign key validation:

```php
// Example validation rules for Booth creation
$validated = $request->validate([
    'loksabha_id' => 'nullable|integer|exists:lok_sabhas,id',
    'vidhansabha_id' => 'nullable|integer|exists:vidhan_sabhas,id',
    'block_id' => 'nullable|integer|exists:blocks,id',
    'panchayat_id' => 'nullable|integer|exists:panchayats,id',
    'village_id' => 'nullable|integer|exists:villages,id',
    'booth_name' => 'nullable|string|max:255',
    'booth_status' => 'nullable|string|max:255',
]);
```

## Benefits of Foreign Key Relationships

1. **Data Integrity**: Ensures referential integrity between tables
2. **Cascade Operations**: Automatic cleanup of related records
3. **Performance**: Better query performance with proper indexing
4. **Validation**: Prevents orphaned records
5. **Consistency**: Maintains data consistency across the application

## Migration Files

The following migration files contain the foreign key definitions:

- `2025_08_13_000002_create_vidhan_sabhas_table.php`
- `2025_08_13_000003_create_blocks_table.php`
- `2025_08_13_000004_create_panchayats_table.php`
- `2025_08_13_000005_create_villages_table.php`
- `2025_08_13_000006_create_booths_table.php`

## Testing

Use the `test_hierarchical_api.php` script to test:
- Creation of hierarchical data
- Foreign key validation
- Cascade deletion behavior
- Relationship loading

## API Endpoints

All API endpoints support the hierarchical relationships:

- **List with relationships**: `GET /api/{resource}?with=relationships`
- **Filter by parent**: `GET /api/{resource}/{parent_type}/{parent_id}`
- **Create with validation**: `POST /api/{resource}` (validates foreign keys)
- **Update with validation**: `PUT /api/{resource}/{id}` (validates foreign keys)
- **Delete with cascade**: `DELETE /api/{resource}/{id}` (cascades to children)

## Complete Hierarchy

```
Lok Sabha (1)
├── Vidhan Sabha (1)
│   ├── Block (1)
│   │   ├── Panchayat (1)
│   │   │   ├── Village (1)
│   │   │   │   └── Booth (1)
│   │   │   └── Village (2)
│   │   │       └── Booth (2)
│   │   └── Panchayat (2)
│   │       └── Village (3)
│   │           └── Booth (3)
│   └── Block (2)
│       └── Panchayat (3)
│           └── Village (4)
│               └── Booth (4)
└── Vidhan Sabha (2)
    └── Block (3)
        └── Panchayat (4)
            └── Village (5)
                └── Booth (5)
```

This structure ensures complete data integrity and proper hierarchical relationships throughout the application.
