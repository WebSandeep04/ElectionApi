# Booth Choosing API Documentation

## Overview

The booth table now supports both **panchayat choosing** and **village choosing** functionality, similar to how these choosing systems work in the panchayat and village tables. This allows for flexible booth assignment based on different choosing criteria.

## Database Structure

### Booth Table Fields

The booth table includes the following choosing-related fields:

- `panchayat_choosing_id` - Foreign key to `panchayat_choosings` table
- `village_choosing_id` - Foreign key to `village_choosings` table

### Relationships

```php
// In Booth Model
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

### 1. Create Booth with Choosing

**POST** `/api/booths`

```json
{
    "booth_name": "Test Booth",
    "panchayat_choosing_id": 1,
    "village_choosing_id": 2,
    "booth_status": "1"
}
```

### 2. Get Booths by Panchayat Choosing

**GET** `/api/booths/panchayat-choosing/{panchayatChoosingId}`

Returns all booths associated with a specific panchayat choosing.

### 3. Get Booths by Village Choosing

**GET** `/api/booths/village-choosing/{villageChoosingId}`

Returns all booths associated with a specific village choosing.

### 4. Get Booth Details with Choosing Data

**GET** `/api/booths/{id}`

Returns booth details including the related choosing data:

```json
{
    "data": {
        "id": 1,
        "booth_name": "Test Booth",
        "panchayat_choosing_id": 1,
        "village_choosing_id": 2,
        "panchayat_choosing_data": {
            "id": 1,
            "name": "Mahanager Pallika",
            "status": "1"
        },
        "village_choosing_data": {
            "id": 2,
            "name": "Test Village Choosing",
            "status": "1"
        }
    }
}
```

## Usage Examples

### Creating a Booth with Choosing

```php
$boothData = [
    'booth_name' => 'Booth A',
    'panchayat_choosing_id' => 1, // Mahanager Pallika
    'village_choosing_id' => 2,   // Specific village choosing
    'booth_status' => '1'
];

$booth = Booth::create($boothData);
```

### Filtering Booths by Choosing

```php
// Get all booths for a specific panchayat choosing
$booths = Booth::where('panchayat_choosing_id', 1)->get();

// Get all booths for a specific village choosing
$booths = Booth::where('village_choosing_id', 2)->get();

// Get booths with both choosing criteria
$booths = Booth::where('panchayat_choosing_id', 1)
    ->where('village_choosing_id', 2)
    ->get();
```

### Loading Relationships

```php
// Load booth with choosing data
$booth = Booth::with(['panchayatChoosing', 'villageChoosing'])->find(1);

// Access choosing data
echo $booth->panchayatChoosing->name;
echo $booth->villageChoosing->name;
```

## Migration History

The choosing functionality was added to the booth table through the following migration:

**File:** `database/migrations/2025_08_22_100102_alter_booths_add_choosing_ids.php`

```php
public function up(): void
{
    Schema::table('booths', function (Blueprint $table) {
        $table->foreignId('panchayat_choosing_id')->nullable()->after('panchayat_id')->constrained('panchayat_choosings')->nullOnDelete();
        $table->foreignId('village_choosing_id')->nullable()->after('village_choosing')->constrained('village_choosings')->nullOnDelete();
    });
}
```

## Benefits

1. **Flexible Assignment**: Booths can be assigned based on different choosing criteria
2. **Hierarchical Organization**: Supports both panchayat-level and village-level choosing
3. **Data Consistency**: Maintains referential integrity with foreign key constraints
4. **API Integration**: Full REST API support for choosing-based operations
5. **Backward Compatibility**: Existing booth data remains unaffected

## Testing

Use the provided test file `test_booth_choosing_api.php` to verify the functionality:

```bash
php test_booth_choosing_api.php
```

This test file demonstrates:
- Creating panchayat and village choosings
- Creating booths with choosing IDs
- Filtering booths by choosing criteria
- Retrieving booth data with choosing information

## Related Documentation

- [Panchayat Choosing API](./CHOOSING_SYSTEM_GUIDE.md)
- [Village Choosing API](./CHOOSING_SYSTEM_GUIDE.md)
- [Booth API](./BOOTH_API.md)
