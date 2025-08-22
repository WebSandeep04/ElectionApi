# Cast Ratio Choosing API Documentation

## Overview

The cast ratios table now supports both **panchayat choosing** and **village choosing** functionality, similar to how these choosing systems work in other tables. This allows for flexible cast ratio assignment based on different choosing criteria.

## Database Structure

### Cast Ratios Table Fields

The cast ratios table includes the following choosing-related fields:

- `panchayat_choosing_id` - Foreign key to `panchayat_choosings` table
- `village_choosing_id` - Foreign key to `village_choosings` table

### Relationships

```php
// In CastRatio Model
public function panchayatChoosing()
{
    return $this->belongsTo(PanchayatChoosing::class, 'panchayat_choosing_id');
}

public function villageChoosing()
{
    return $this->belongsTo(VillageChoosing::class, 'village_choosing_id');
}
```

## API Endpoints

### 1. Create Cast Ratio with Choosing

**POST** `/api/cast-ratios`

```json
{
    "caste_id": 1,
    "caste_ratio": 25,
    "panchayat_choosing_id": 1,
    "village_choosing_id": 2
}
```

### 2. Get Cast Ratios by Panchayat Choosing

**GET** `/api/cast-ratios/panchayat-choosing/{panchayatChoosingId}`

Returns all cast ratios associated with a specific panchayat choosing.

### 3. Get Cast Ratios by Village Choosing

**GET** `/api/cast-ratios/village-choosing/{villageChoosingId}`

Returns all cast ratios associated with a specific village choosing.

### 4. Get Cast Ratio Details with Choosing Data

**GET** `/api/cast-ratios/{id}`

Returns cast ratio details including the related choosing data:

```json
{
    "data": {
        "caste_ratio_id": 1,
        "caste_id": 1,
        "caste_ratio": 25,
        "panchayat_choosing_id": 1,
        "village_choosing_id": 2,
        "panchayat_choosing_data": {
            "id": 1,
            "name": "Gram Panchayat",
            "status": "1"
        },
        "village_choosing_data": {
            "id": 2,
            "name": "Ward",
            "status": "1"
        }
    }
}
```

## Usage Examples

### Creating a Cast Ratio with Choosing

```php
$castRatioData = [
    'caste_id' => 1,
    'caste_ratio' => 25,
    'panchayat_choosing_id' => 1, // Gram Panchayat
    'village_choosing_id' => 2,   // Ward
];

$castRatio = CastRatio::create($castRatioData);
```

### Filtering Cast Ratios by Choosing

```php
// Get all cast ratios for a specific panchayat choosing
$castRatios = CastRatio::where('panchayat_choosing_id', 1)->get();

// Get all cast ratios for a specific village choosing
$castRatios = CastRatio::where('village_choosing_id', 2)->get();

// Get cast ratios with both choosing criteria
$castRatios = CastRatio::where('panchayat_choosing_id', 1)
    ->where('village_choosing_id', 2)
    ->get();
```

### Loading Relationships

```php
// Load cast ratio with choosing data
$castRatio = CastRatio::with(['panchayatChoosing', 'villageChoosing'])->find(1);

// Access choosing data
echo $castRatio->panchayatChoosing->name;
echo $castRatio->villageChoosing->name;
```

## Migration History

The choosing functionality was added to the cast ratios table through the following migration:

**File:** `database/migrations/2025_08_21_051612_create_cast_ratios_table.php`

```php
public function up(): void
{
    Schema::create('cast_ratios', function (Blueprint $table) {
        $table->id('caste_ratio_id');
        $table->foreignId('loksabha_id')->nullable()->constrained('lok_sabhas')->onDelete('set null');
        $table->foreignId('vidhansabha_id')->nullable()->constrained('vidhan_sabhas')->onDelete('set null');
        $table->foreignId('block_id')->nullable()->constrained('blocks')->onDelete('set null');
        $table->foreignId('panchayat_choosing_id')->nullable()->constrained('panchayat_choosings')->onDelete('set null');
        $table->foreignId('panchayat_id')->nullable()->constrained('panchayats')->onDelete('set null');
        $table->foreignId('village_choosing_id')->nullable()->constrained('village_choosings')->onDelete('set null');
        $table->foreignId('village_id')->nullable()->constrained('villages')->onDelete('set null');
        $table->foreignId('booth_id')->nullable()->constrained('booths')->onDelete('set null');
        $table->foreignId('caste_id')->constrained('castes')->onDelete('cascade');
        $table->integer('caste_ratio');
        $table->timestamps();
    });
}
```

## Benefits

1. **Flexible Assignment**: Cast ratios can be assigned based on different choosing criteria
2. **Hierarchical Organization**: Supports both panchayat-level and village-level choosing
3. **Data Consistency**: Maintains referential integrity with foreign key constraints
4. **API Integration**: Full REST API support for choosing-based operations
5. **Backward Compatibility**: Existing cast ratio data remains unaffected

## Testing

Use the provided test file `test_cast_ratio_choosing_api.php` to verify the functionality:

```bash
php test_cast_ratio_choosing_api.php
```

This test file demonstrates:
- Creating panchayat and village choosings
- Creating cast ratios with choosing IDs
- Filtering cast ratios by choosing criteria
- Retrieving cast ratio data with choosing information

## Related Documentation

- [Panchayat Choosing API](./CHOOSING_SYSTEM_GUIDE.md)
- [Village Choosing API](./CHOOSING_SYSTEM_GUIDE.md)
- [Cast Ratio API](./CASTE_RATIO_API.md)
