# Roles API Documentation

## Overview
The Roles API provides a complete role management system for user access control. It follows the same authentication pattern as other APIs: **public read access** and **protected write access** requiring authentication.

- **Base URL**: `${API_BASE_URL}/api`
- **Authentication**: Bearer token (for write operations)
- **Content-Type**: `application/json`

## Data Model

### Role Object
```json
{
  "id": 1,
  "name": "admin",
  "display_name": "Administrator",
  "description": "Full system access with all permissions",
  "is_active": true,
  "created_at": "2024-01-01T12:00:00Z",
  "updated_at": "2024-01-02T12:00:00Z"
}
```

### Field Descriptions
- `id`: Unique identifier (auto-generated)
- `name`: Unique role identifier (used in code)
- `display_name`: Human-readable role name
- `description`: Optional role description
- `is_active`: Boolean indicating if role is active
- `created_at`, `updated_at`: ISO timestamp strings

## Endpoints

### 🔓 Public Read Endpoints (No Auth Required)

#### GET `/api/roles`
List all roles with pagination, search, filtering, and sorting.

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 10, max: 100)
- `search`: Search in name and display_name
- `is_active`: Filter by active status (0/1)
- `sort_by`: Sort field (default: created_at)
- `sort_order`: Sort direction (asc/desc, default: desc)

**Response 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "admin",
      "display_name": "Administrator",
      "description": "Full system access with all permissions",
      "is_active": true,
      "created_at": "2024-01-01T12:00:00Z",
      "updated_at": "2024-01-02T12:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

#### GET `/api/roles/{id}`
Get a specific role by ID.

**Response 200:**
```json
{
  "data": {
    "id": 1,
    "name": "admin",
    "display_name": "Administrator",
    "description": "Full system access with all permissions",
    "is_active": true,
    "created_at": "2024-01-01T12:00:00Z",
    "updated_at": "2024-01-02T12:00:00Z"
  }
}
```

#### GET `/api/roles/active`
Get only active roles.

**Response 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "admin",
      "display_name": "Administrator",
      "is_active": true
    }
  ]
}
```

#### GET `/api/roles/inactive`
Get only inactive roles.

**Response 200:**
```json
{
  "data": [
    {
      "id": 5,
      "name": "guest",
      "display_name": "Guest",
      "is_active": false
    }
  ]
}
```

### 🔒 Protected Write Endpoints (Auth Required)

**Headers Required:**
```
Authorization: Bearer <your_token>
Content-Type: application/json
```

#### POST `/api/roles`
Create a new role.

**Request Body:**
```json
{
  "name": "moderator",
  "display_name": "Moderator",
  "description": "Content moderation permissions",
  "is_active": true
}
```

**Validation Rules:**
- `name`: Required, string, max 255 chars, unique
- `display_name`: Required, string, max 255 chars
- `description`: Optional, string
- `is_active`: Optional, boolean (default: true)

**Response 201:**
```json
{
  "data": {
    "id": 6,
    "name": "moderator",
    "display_name": "Moderator",
    "description": "Content moderation permissions",
    "is_active": true,
    "created_at": "2024-01-01T12:00:00Z",
    "updated_at": "2024-01-01T12:00:00Z"
  },
  "message": "Role created successfully"
}
```

#### PUT `/api/roles/{id}`
Update an existing role.

**Request Body:**
```json
{
  "display_name": "Senior Moderator",
  "description": "Enhanced content moderation permissions"
}
```

**Validation Rules:**
- `name`: Optional, string, max 255 chars, unique (ignores current role)
- `display_name`: Optional, string, max 255 chars
- `description`: Optional, string
- `is_active`: Optional, boolean

**Response 200:**
```json
{
  "data": {
    "id": 6,
    "name": "moderator",
    "display_name": "Senior Moderator",
    "description": "Enhanced content moderation permissions",
    "is_active": true,
    "created_at": "2024-01-01T12:00:00Z",
    "updated_at": "2024-01-02T12:00:00Z"
  },
  "message": "Role updated successfully"
}
```

#### DELETE `/api/roles/{id}`
Delete a role (only if no users are assigned).

**Response 200:**
```json
{
  "message": "Role deleted successfully"
}
```

**Response 422 (if users assigned):**
```json
{
  "message": "Cannot delete role. It has assigned users."
}
```

#### POST `/api/roles/{id}/activate`
Activate a role.

**Response 200:**
```json
{
  "data": {
    "id": 5,
    "name": "guest",
    "display_name": "Guest",
    "is_active": true
  },
  "message": "Role activated successfully"
}
```

#### POST `/api/roles/{id}/deactivate`
Deactivate a role.

**Response 200:**
```json
{
  "data": {
    "id": 5,
    "name": "guest",
    "display_name": "Guest",
    "is_active": false
  },
  "message": "Role deactivated successfully"
}
```

## Error Responses

### Validation Error (422)
```json
{
  "message": "Validation failed",
  "errors": {
    "name": ["Role name already exists."],
    "display_name": ["Display name is required."]
  }
}
```

### Authentication Error (401)
```json
{
  "message": "Unauthenticated."
}
```

### Not Found Error (404)
```json
{
  "message": "No query results for model [App\\Models\\Role] 999"
}
```

### Server Error (500)
```json
{
  "message": "Server Error"
}
```

## Usage Examples

### Frontend Implementation

#### 1. List Roles with Search and Pagination
```javascript
// Get roles with search and pagination
const getRoles = async (page = 1, search = '', isActive = null) => {
  const params = new URLSearchParams({
    page,
    per_page: 10,
    ...(search && { search }),
    ...(isActive !== null && { is_active: isActive ? 1 : 0 })
  });
  
  const response = await fetch(`/api/roles?${params}`);
  const data = await response.json();
  
  return {
    roles: data.data,
    pagination: data.meta
  };
};
```

#### 2. Create New Role
```javascript
const createRole = async (roleData, token) => {
  const response = await fetch('/api/roles', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify(roleData)
  });
  
  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || 'Failed to create role');
  }
  
  return await response.json();
};
```

#### 3. Update Role
```javascript
const updateRole = async (roleId, updateData, token) => {
  const response = await fetch(`/api/roles/${roleId}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify(updateData)
  });
  
  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || 'Failed to update role');
  }
  
  return await response.json();
};
```

#### 4. Toggle Role Status
```javascript
const toggleRoleStatus = async (roleId, activate, token) => {
  const endpoint = activate ? 'activate' : 'deactivate';
  
  const response = await fetch(`/api/roles/${roleId}/${endpoint}`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || 'Failed to toggle role status');
  }
  
  return await response.json();
};
```

#### 5. Delete Role
```javascript
const deleteRole = async (roleId, token) => {
  const response = await fetch(`/api/roles/${roleId}`, {
    method: 'DELETE',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || 'Failed to delete role');
  }
  
  return await response.json();
};
```

### React Component Example
```jsx
import React, { useState, useEffect } from 'react';

const RolesList = () => {
  const [roles, setRoles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [pagination, setPagination] = useState({});

  useEffect(() => {
    fetchRoles();
  }, [page, search]);

  const fetchRoles = async () => {
    try {
      setLoading(true);
      const params = new URLSearchParams({
        page,
        per_page: 10,
        ...(search && { search })
      });
      
      const response = await fetch(`/api/roles?${params}`);
      const data = await response.json();
      
      setRoles(data.data);
      setPagination(data.meta);
    } catch (error) {
      console.error('Failed to fetch roles:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleSearch = (value) => {
    setSearch(value);
    setPage(1); // Reset to first page
  };

  if (loading) return <div>Loading roles...</div>;

  return (
    <div>
      <input
        type="text"
        placeholder="Search roles..."
        value={search}
        onChange={(e) => handleSearch(e.target.value)}
      />
      
      <div className="roles-list">
        {roles.map(role => (
          <div key={role.id} className="role-item">
            <h3>{role.display_name}</h3>
            <p><strong>Name:</strong> {role.name}</p>
            <p><strong>Description:</strong> {role.description}</p>
            <span className={`status ${role.is_active ? 'active' : 'inactive'}`}>
              {role.is_active ? 'Active' : 'Inactive'}
            </span>
          </div>
        ))}
      </div>
      
      {/* Pagination controls */}
      <div className="pagination">
        <button 
          disabled={page === 1}
          onClick={() => setPage(page - 1)}
        >
          Previous
        </button>
        <span>Page {page} of {pagination.last_page}</span>
        <button 
          disabled={page === pagination.last_page}
          onClick={() => setPage(page + 1)}
        >
          Next
        </button>
      </div>
    </div>
  );
};

export default RolesList;
```

## Default Roles

The system comes with these pre-configured roles:

1. **admin** - Administrator (Full system access)
2. **manager** - Manager (Department management)
3. **employee** - Employee (Standard permissions)
4. **viewer** - Viewer (Read-only access)
5. **guest** - Guest (Limited access, inactive by default)

## Security Notes

- **Public Read Access**: Anyone can view roles, which is useful for role selection in forms
- **Protected Write Access**: Only authenticated users can create, update, or delete roles
- **Role Deletion**: Roles cannot be deleted if they have assigned users
- **Unique Names**: Role names must be unique across the system
- **Soft Deactivation**: Use activate/deactivate instead of delete for temporary role suspension

## Testing

Use the provided test script to verify all endpoints:

```bash
php test_roles_api.php
```

## Integration with User Management

The roles system is integrated with the user management system:

- Users have a `role_id` field that references the `roles` table
- The `User` model has helper methods:
  - `user.hasRole('admin')` - Check if user has specific role
  - `user.hasAnyRole(['admin', 'manager'])` - Check if user has any of multiple roles
  - `user.isAdmin()` - Check if user is admin
  - `user.role` - Get the user's role object

## Support

For any questions or issues with the Roles API, refer to:
- API response codes and error messages
- Validation rules for each endpoint
- Test script for endpoint verification
- User model integration examples
