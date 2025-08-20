# Users API Documentation

## Overview
The Users API provides a complete user management system with role integration and active/inactive status management. It follows the same authentication pattern as other APIs: **public read access** and **protected write access** requiring authentication.

- **Base URL**: `${API_BASE_URL}/api`
- **Authentication**: Bearer token (for write operations)
- **Content-Type**: `application/json`

## Data Model

### User Object
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "email_verified_at": "2024-01-01T12:00:00Z",
  "role_id": 1,
  "is_active": true,
  "created_at": "2024-01-01T12:00:00Z",
  "updated_at": "2024-01-02T12:00:00Z",
  "role": {
    "id": 1,
    "name": "admin",
    "display_name": "Administrator",
    "description": "Full system access with all permissions",
    "is_active": true
  }
}
```

### Field Descriptions
- `id`: Unique identifier (auto-generated)
- `name`: User's full name
- `email`: Unique email address
- `email_verified_at`: Email verification timestamp (nullable)
- `role_id`: Foreign key to roles table (nullable)
- `is_active`: Boolean indicating if user is active
- `created_at`, `updated_at`: ISO timestamp strings
- `role`: Role object (when loaded)

## Endpoints

### 🔓 Public Read Endpoints (No Auth Required)

#### GET `/api/users`
List all users with pagination, search, filtering, and sorting.

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 10, max: 100)
- `search`: Search in name and email
- `is_active`: Filter by active status (0/1)
- `role_id`: Filter by role ID
- `sort_by`: Sort field (default: created_at)
- `sort_order`: Sort direction (asc/desc, default: desc)

**Response 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role_id": 1,
      "is_active": true,
      "created_at": "2024-01-01T12:00:00Z",
      "updated_at": "2024-01-02T12:00:00Z",
      "role": {
        "id": 1,
        "name": "admin",
        "display_name": "Administrator"
      }
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

#### GET `/api/users/{id}`
Get a specific user by ID.

**Response 200:**
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role_id": 1,
    "is_active": true,
    "created_at": "2024-01-01T12:00:00Z",
    "updated_at": "2024-01-02T12:00:00Z",
    "role": {
      "id": 1,
      "name": "admin",
      "display_name": "Administrator"
    }
  }
}
```

#### GET `/api/users/active`
Get only active users.

**Response 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "is_active": true,
      "role": {
        "id": 1,
        "name": "admin",
        "display_name": "Administrator"
      }
    }
  ]
}
```

#### GET `/api/users/inactive`
Get only inactive users.

**Response 200:**
```json
{
  "data": [
    {
      "id": 2,
      "name": "Jane Smith",
      "email": "jane@example.com",
      "is_active": false,
      "role": {
        "id": 2,
        "name": "employee",
        "display_name": "Employee"
      }
    }
  ]
}
```

#### GET `/api/users/role/{roleId}`
Get users by specific role ID.

**Response 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role_id": 1,
      "is_active": true,
      "role": {
        "id": 1,
        "name": "admin",
        "display_name": "Administrator"
      }
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

### 🔒 Protected Write Endpoints (Auth Required)

**Headers Required:**
```
Authorization: Bearer <your_token>
Content-Type: application/json
```

#### POST `/api/users`
Create a new user.

**Request Body:**
```json
{
  "name": "New User",
  "email": "newuser@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role_id": 2,
  "is_active": true
}
```

**Validation Rules:**
- `name`: Required, string, max 255 chars
- `email`: Required, valid email, max 255 chars, unique
- `password`: Required, confirmed, minimum 8 chars
- `password_confirmation`: Required, must match password
- `role_id`: Optional, integer, must exist in roles table
- `is_active`: Optional, boolean (default: true)

**Response 201:**
```json
{
  "data": {
    "id": 3,
    "name": "New User",
    "email": "newuser@example.com",
    "role_id": 2,
    "is_active": true,
    "created_at": "2024-01-01T12:00:00Z",
    "updated_at": "2024-01-01T12:00:00Z",
    "role": {
      "id": 2,
      "name": "employee",
      "display_name": "Employee"
    }
  },
  "message": "User created successfully"
}
```

#### PUT `/api/users/{id}`
Update an existing user.

**Request Body:**
```json
{
  "name": "Updated User Name",
  "email": "updated@example.com",
  "role_id": 3,
  "is_active": false
}
```

**Validation Rules:**
- `name`: Optional, string, max 255 chars
- `email`: Optional, valid email, max 255 chars, unique (ignores current user)
- `password`: Optional, confirmed, minimum 8 chars
- `password_confirmation`: Required if password provided
- `role_id`: Optional, integer, must exist in roles table
- `is_active`: Optional, boolean

**Response 200:**
```json
{
  "data": {
    "id": 3,
    "name": "Updated User Name",
    "email": "updated@example.com",
    "role_id": 3,
    "is_active": false,
    "created_at": "2024-01-01T12:00:00Z",
    "updated_at": "2024-01-02T12:00:00Z",
    "role": {
      "id": 3,
      "name": "manager",
      "display_name": "Manager"
    }
  },
  "message": "User updated successfully"
}
```

#### DELETE `/api/users/{id}`
Delete a user.

**Response 200:**
```json
{
  "message": "User deleted successfully"
}
```

**Response 422 (if last admin):**
```json
{
  "message": "Cannot delete the last admin user."
}
```

#### POST `/api/users/{id}/activate`
Activate a user.

**Response 200:**
```json
{
  "data": {
    "id": 3,
    "name": "User Name",
    "email": "user@example.com",
    "is_active": true,
    "role": {
      "id": 2,
      "name": "employee",
      "display_name": "Employee"
    }
  },
  "message": "User activated successfully"
}
```

#### POST `/api/users/{id}/deactivate`
Deactivate a user.

**Response 200:**
```json
{
  "data": {
    "id": 3,
    "name": "User Name",
    "email": "user@example.com",
    "is_active": false,
    "role": {
      "id": 2,
      "name": "employee",
      "display_name": "Employee"
    }
  },
  "message": "User deactivated successfully"
}
```

**Response 422 (if last active admin):**
```json
{
  "message": "Cannot deactivate the last active admin user."
}
```

## Error Responses

### Validation Error (422)
```json
{
  "message": "Validation failed",
  "errors": {
    "name": ["Name is required."],
    "email": ["This email address is already registered."],
    "password": ["Password confirmation does not match."],
    "role_id": ["Selected role does not exist."]
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
  "message": "No query results for model [App\\Models\\User] 999"
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

#### 1. List Users with Search and Pagination
```javascript
// Get users with search and pagination
const getUsers = async (page = 1, search = '', isActive = null, roleId = null) => {
  const params = new URLSearchParams({
    page,
    per_page: 10,
    ...(search && { search }),
    ...(isActive !== null && { is_active: isActive ? 1 : 0 }),
    ...(roleId && { role_id: roleId })
  });
  
  const response = await fetch(`/api/users?${params}`);
  const data = await response.json();
  
  return {
    users: data.data,
    pagination: data.meta
  };
};
```

#### 2. Create New User
```javascript
const createUser = async (userData, token) => {
  const response = await fetch('/api/users', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify(userData)
  });
  
  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || 'Failed to create user');
  }
  
  return await response.json();
};

// Usage
const newUser = await createUser({
  name: 'John Doe',
  email: 'john@example.com',
  password: 'password123',
  password_confirmation: 'password123',
  role_id: 2,
  is_active: true
}, token);
```

#### 3. Update User
```javascript
const updateUser = async (userId, updateData, token) => {
  const response = await fetch(`/api/users/${userId}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify(updateData)
  });
  
  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || 'Failed to update user');
  }
  
  return await response.json();
};

// Usage
const updatedUser = await updateUser(1, {
  name: 'Updated Name',
  role_id: 3,
  is_active: false
}, token);
```

#### 4. Toggle User Status
```javascript
const toggleUserStatus = async (userId, activate, token) => {
  const endpoint = activate ? 'activate' : 'deactivate';
  
  const response = await fetch(`/api/users/${userId}/${endpoint}`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || 'Failed to toggle user status');
  }
  
  return await response.json();
};
```

#### 5. Delete User
```javascript
const deleteUser = async (userId, token) => {
  const response = await fetch(`/api/users/${userId}`, {
    method: 'DELETE',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || 'Failed to delete user');
  }
  
  return await response.json();
};
```

#### 6. Get Users by Role
```javascript
const getUsersByRole = async (roleId, page = 1) => {
  const params = new URLSearchParams({
    page,
    per_page: 10
  });
  
  const response = await fetch(`/api/users/role/${roleId}?${params}`);
  const data = await response.json();
  
  return {
    users: data.data,
    pagination: data.meta
  };
};
```

### React Component Example
```jsx
import React, { useState, useEffect } from 'react';

const UsersList = () => {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [pagination, setPagination] = useState({});
  const [roles, setRoles] = useState([]);
  const [selectedRole, setSelectedRole] = useState('');

  useEffect(() => {
    fetchUsers();
    fetchRoles();
  }, [page, search, selectedRole]);

  const fetchUsers = async () => {
    try {
      setLoading(true);
      const params = new URLSearchParams({
        page,
        per_page: 10,
        ...(search && { search }),
        ...(selectedRole && { role_id: selectedRole })
      });
      
      const response = await fetch(`/api/users?${params}`);
      const data = await response.json();
      
      setUsers(data.data);
      setPagination(data.meta);
    } catch (error) {
      console.error('Failed to fetch users:', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchRoles = async () => {
    try {
      const response = await fetch('/api/roles');
      const data = await response.json();
      setRoles(data.data);
    } catch (error) {
      console.error('Failed to fetch roles:', error);
    }
  };

  const handleSearch = (value) => {
    setSearch(value);
    setPage(1);
  };

  const handleRoleChange = (roleId) => {
    setSelectedRole(roleId);
    setPage(1);
  };

  if (loading) return <div>Loading users...</div>;

  return (
    <div>
      <div className="filters">
        <input
          type="text"
          placeholder="Search users..."
          value={search}
          onChange={(e) => handleSearch(e.target.value)}
        />
        
        <select 
          value={selectedRole} 
          onChange={(e) => handleRoleChange(e.target.value)}
        >
          <option value="">All Roles</option>
          {roles.map(role => (
            <option key={role.id} value={role.id}>
              {role.display_name}
            </option>
          ))}
        </select>
      </div>
      
      <div className="users-list">
        {users.map(user => (
          <div key={user.id} className="user-item">
            <h3>{user.name}</h3>
            <p><strong>Email:</strong> {user.email}</p>
            <p><strong>Role:</strong> {user.role?.display_name || 'No Role'}</p>
            <span className={`status ${user.is_active ? 'active' : 'inactive'}`}>
              {user.is_active ? 'Active' : 'Inactive'}
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

export default UsersList;
```

## Security Features

### Password Security
- **Hashing**: All passwords are automatically hashed using Laravel's bcrypt
- **Confirmation**: Password confirmation required for creation and updates
- **Validation**: Minimum 8 characters, follows Laravel's default password rules

### Role Integration
- **Role Assignment**: Users can be assigned to roles via `role_id`
- **Role Validation**: Role ID must exist in the roles table
- **Role Display**: Role information is included in user responses

### Admin Protection
- **Last Admin Protection**: Cannot delete or deactivate the last admin user
- **Role Validation**: Ensures system always has at least one admin

### Authentication
- **Public Read**: Anyone can view users (useful for role selection)
- **Protected Write**: Only authenticated users can create, update, delete
- **Token Required**: Bearer token required for all write operations

## Integration with Roles API

The Users API is fully integrated with the Roles API:

- **Role Selection**: Use `GET /api/roles` to populate role dropdowns
- **Role Assignment**: Set `role_id` when creating/updating users
- **Role Display**: User responses include full role information
- **Role Filtering**: Filter users by role using `role_id` parameter

## Testing

Use the provided test script to verify all endpoints:

```bash
php test_users_api.php
```

## Best Practices

### Frontend Implementation
1. **Role Selection**: Always fetch roles first to populate dropdowns
2. **Password Handling**: Always require password confirmation
3. **Status Management**: Use activate/deactivate instead of delete for temporary suspension
4. **Error Handling**: Handle validation errors gracefully
5. **Loading States**: Show loading indicators during API calls

### Security Considerations
1. **Token Management**: Store and send Bearer tokens securely
2. **Password Validation**: Implement client-side password strength validation
3. **Role Permissions**: Check user roles before showing admin features
4. **Data Sanitization**: Sanitize user inputs before sending to API

## Support

For any questions or issues with the Users API, refer to:
- API response codes and error messages
- Validation rules for each endpoint
- Test script for endpoint verification
- Role integration examples
- Security best practices
