<?php

echo "=== Caste Category API cURL Test ===\n\n";

// Base URL for the API
$baseUrl = "http://localhost:8000/api";

echo "1. First, let's create some test data using the demo script...\n";
echo "   Run: php test_caste_category_api_simple.php\n\n";

echo "2. Now let's test the API endpoints:\n\n";

echo "=== API Endpoint 1: Get All Categories ===\n";
echo "cURL Command:\n";
echo "curl -X GET '{$baseUrl}/caste-categories'\n\n";

echo "Expected Response:\n";
echo "{\n";
echo "  \"data\": [\n";
echo "    {\n";
echo "      \"id\": 1,\n";
echo "      \"name\": \"General Category\",\n";
echo "      \"description\": \"General category for castes\",\n";
echo "      \"castes\": [\n";
echo "        {\n";
echo "          \"id\": 1,\n";
echo "          \"caste\": \"General\"\n";
echo "        },\n";
echo "        {\n";
echo "          \"id\": 2,\n";
echo "          \"caste\": \"Brahmin\"\n";
echo "        }\n";
echo "      ],\n";
echo "      \"castes_count\": 2\n";
echo "    }\n";
echo "  ]\n";
echo "}\n\n";

echo "=== API Endpoint 2: Get Specific Category with Castes ===\n";
echo "cURL Command:\n";
echo "curl -X GET '{$baseUrl}/caste-categories/1'\n\n";

echo "Expected Response:\n";
echo "{\n";
echo "  \"success\": true,\n";
echo "  \"data\": {\n";
echo "    \"id\": 1,\n";
echo "    \"name\": \"General Category\",\n";
echo "    \"description\": \"General category for castes\",\n";
echo "    \"castes\": [\n";
echo "      {\n";
echo "        \"id\": 1,\n";
echo "        \"caste\": \"General\"\n";
echo "      },\n";
echo "      {\n";
echo "        \"id\": 2,\n";
echo "        \"caste\": \"Brahmin\"\n";
echo "      }\n";
echo "    ],\n";
echo "    \"castes_count\": 2\n";
echo "  }\n";
echo "}\n\n";

echo "=== API Endpoint 3: Get Castes by Category ID ===\n";
echo "cURL Command:\n";
echo "curl -X GET '{$baseUrl}/caste-categories/1/castes'\n\n";

echo "Expected Response:\n";
echo "{\n";
echo "  \"success\": true,\n";
echo "  \"data\": {\n";
echo "    \"category\": {\n";
echo "      \"id\": 1,\n";
echo "      \"name\": \"General Category\",\n";
echo "      \"description\": \"General category for castes\",\n";
echo "      \"castes\": [...],\n";
echo "      \"castes_count\": 2\n";
echo "    },\n";
echo "    \"castes\": [\n";
echo "      {\n";
echo "        \"id\": 1,\n";
echo "        \"caste\": \"General\",\n";
echo "        \"category_id\": 1\n";
echo "      },\n";
echo "      {\n";
echo "        \"id\": 2,\n";
echo "        \"caste\": \"Brahmin\",\n";
echo "        \"category_id\": 1\n";
echo "      }\n";
echo "    ]\n";
echo "  }\n";
echo "}\n\n";

echo "=== Frontend JavaScript Examples ===\n\n";

echo "// Example 1: Get all categories with castes\n";
echo "const getAllCategories = async () => {\n";
echo "  try {\n";
echo "    const response = await fetch('/api/caste-categories');\n";
echo "    const data = await response.json();\n";
echo "    return data.data;\n";
echo "  } catch (error) {\n";
echo "    console.error('Error:', error);\n";
echo "  }\n";
echo "};\n\n";

echo "// Example 2: Get castes by specific category\n";
echo "const getCastesByCategory = async (categoryId) => {\n";
echo "  try {\n";
echo "    const response = await fetch(`/api/caste-categories/\${categoryId}/castes`);\n";
echo "    const data = await response.json();\n";
echo "    if (data.success) {\n";
echo "      return data.data.castes;\n";
echo "    }\n";
echo "  } catch (error) {\n";
echo "    console.error('Error:', error);\n";
echo "  }\n";
echo "};\n\n";

echo "// Example 3: Create a category (requires authentication)\n";
echo "const createCategory = async (categoryData, token) => {\n";
echo "  try {\n";
echo "    const response = await fetch('/api/caste-categories', {\n";
echo "      method: 'POST',\n";
echo "      headers: {\n";
echo "        'Content-Type': 'application/json',\n";
echo "        'Authorization': `Bearer \${token}`\n";
echo "      },\n";
echo "      body: JSON.stringify(categoryData)\n";
echo "    });\n";
echo "    const data = await response.json();\n";
echo "    return data;\n";
echo "  } catch (error) {\n";
echo "    console.error('Error:', error);\n";
echo "  }\n";
echo "};\n\n";

echo "=== Usage Examples ===\n\n";

echo "// Get all categories and display them\n";
echo "getAllCategories().then(categories => {\n";
echo "  categories.forEach(category => {\n";
echo "    console.log(`Category: \${category.name} (\${category.castes_count} castes)`);\n";
echo "    category.castes.forEach(caste => {\n";
echo "      console.log(`  - \${caste.caste}`);\n";
echo "    });\n";
echo "  });\n";
echo "});\n\n";

echo "// Get castes for a specific category (e.g., OBC Category)\n";
echo "getCastesByCategory(2).then(castes => {\n";
echo "  console.log('OBC Category Castes:');\n";
echo "  castes.forEach(caste => {\n";
echo "    console.log(`  - \${caste.caste}`);\n";
echo "  });\n";
echo "});\n\n";

echo "=== Summary ===\n\n";
echo "The API provides these key endpoints:\n\n";
echo "1. GET /api/caste-categories - List all categories with their castes\n";
echo "2. GET /api/caste-categories/{id} - Get specific category with castes\n";
echo "3. GET /api/caste-categories/{id}/castes - Get castes by category ID\n\n";

echo "This allows you to:\n";
echo "- See all categories and their associated castes\n";
echo "- Select a specific category and get all its castes\n";
echo "- Organize castes into logical groups\n";
echo "- Build dropdown menus or selection interfaces\n\n";

echo "The relationship is: One Category can have Many Castes\n";
echo "Each Caste belongs to One Category (optional - can be null)\n";
