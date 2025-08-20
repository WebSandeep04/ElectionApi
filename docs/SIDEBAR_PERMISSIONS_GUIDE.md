# Sidebar Permissions Implementation Guide

This guide explains how to implement the sidebar permission system for your frontend application.

## Overview

The sidebar is organized into 6 main categories with 22 sub-items. Each item requires specific permissions to be visible. Users only see sidebar items they have permissions for.

## Permission Structure

### Permission Naming Convention
- `view_*` - Read-only access to view data
- `manage_*` - Full CRUD access to manage data

### Sidebar Structure with Permissions

#### 📋 Dashboard
- **Dashboard** → `view_dashboard`

#### 📋 Master Data (Setup & Configuration)
- **👥 Employee Management** → `view_employee_management`
- **🏷️ Add Caste** → `view_caste_management`
- **📊 Caste Ratio** → `view_caste_ratio`
- **🏘️ Village Description** → `view_village_description`
- **🎓 Add Educations** → `view_education_management`
- **📂 Category** → `view_category_management`
- **🏷️ Employee Types** → `view_employee_types`

#### 👤 User Management (Users & Roles Management)
- **👥 Users** → `view_user_management`
- **🔐 Role Management** → `view_role_management`
- **🛡️ Permission Management** → `view_permission_management`

#### 🏛️ Parliament (Parliamentary Management)
- **🏛️ Add Parliament** → `view_parliament_management`
- **🏛️ Add Lok Sabha** → `view_lok_sabha`
- **🏛️ Add Vidhan Sabha** → `view_vidhan_sabha`
- **🏛️ Add Block** → `view_blocks`
- **🏛️ Add Panchayat** → `view_panchayats`
- **🏘️ Add Village** → `view_villages`
- **📊 Add Booth** → `view_booths`

#### 📝 Data Collection & Forms (Forms & Information Gathering)
- **🔨 Form Builder** → `view_form_builder`
- **📋 Form List** → `view_form_list`
- **📋 Respondent Table** → `view_respondent_table`
- **👥 Teams** → `view_teams`

#### 📊 Analysis & Tools (Reports & Performance Tracking)
- **👥 Employee Analysis** → `view_employee_analysis`
- **🔍 Analysis** → `view_analysis`
- **🗑️ Cache Clear** → `view_cache_clear`

## Frontend Implementation

### 1. Permission Checking Utility

```javascript
// utils/permissions.js
export const hasPermission = (user, permissionName) => {
  if (!user?.role?.permissions) return false;
  return user.role.permissions.some(perm => perm.name === permissionName);
};

export const hasAnyPermission = (user, permissionNames) => {
  if (!user?.role?.permissions) return false;
  return user.role.permissions.some(perm => permissionNames.includes(perm.name));
};

export const hasAllPermissions = (user, permissionNames) => {
  if (!user?.role?.permissions) return false;
  return permissionNames.every(permName => 
    user.role.permissions.some(perm => perm.name === permName)
  );
};
```

### 2. Sidebar Configuration

```javascript
// config/sidebar.js
export const sidebarConfig = [
  {
    id: 'dashboard',
    title: '📋 Dashboard',
    icon: 'dashboard',
    permission: 'view_dashboard',
    items: [
      {
        id: 'dashboard-main',
        title: 'Dashboard',
        path: '/dashboard',
        permission: 'view_dashboard'
      }
    ]
  },
  {
    id: 'master-data',
    title: '📋 Master Data',
    icon: 'settings',
    permission: 'view_employee_management', // Show if user can view any master data
    items: [
      {
        id: 'employee-management',
        title: '👥 Employee Management',
        path: '/employees',
        permission: 'view_employee_management'
      },
      {
        id: 'caste-management',
        title: '🏷️ Add Caste',
        path: '/castes',
        permission: 'view_caste_management'
      },
      {
        id: 'caste-ratio',
        title: '📊 Caste Ratio',
        path: '/caste-ratio',
        permission: 'view_caste_ratio'
      },
      {
        id: 'village-description',
        title: '🏘️ Village Description',
        path: '/village-description',
        permission: 'view_village_description'
      },
      {
        id: 'education-management',
        title: '🎓 Add Educations',
        path: '/educations',
        permission: 'view_education_management'
      },
      {
        id: 'category-management',
        title: '📂 Category',
        path: '/categories',
        permission: 'view_category_management'
      },
      {
        id: 'employee-types',
        title: '🏷️ Employee Types',
        path: '/employee-types',
        permission: 'view_employee_types'
      }
    ]
  },
  {
    id: 'user-management',
    title: '👤 User Management',
    icon: 'users',
    permission: 'view_user_management',
    items: [
      {
        id: 'users',
        title: '👥 Users',
        path: '/users',
        permission: 'view_user_management'
      },
      {
        id: 'roles',
        title: '🔐 Role Management',
        path: '/roles',
        permission: 'view_role_management'
      },
      {
        id: 'permissions',
        title: '🛡️ Permission Management',
        path: '/permissions',
        permission: 'view_permission_management'
      }
    ]
  },
  {
    id: 'parliament',
    title: '🏛️ Parliament',
    icon: 'building',
    permission: 'view_parliament_management',
    items: [
      {
        id: 'parliament-management',
        title: '🏛️ Add Parliament',
        path: '/parliament',
        permission: 'view_parliament_management'
      },
      {
        id: 'lok-sabha',
        title: '🏛️ Add Lok Sabha',
        path: '/lok-sabha',
        permission: 'view_lok_sabha'
      },
      {
        id: 'vidhan-sabha',
        title: '🏛️ Add Vidhan Sabha',
        path: '/vidhan-sabha',
        permission: 'view_vidhan_sabha'
      },
      {
        id: 'blocks',
        title: '🏛️ Add Block',
        path: '/blocks',
        permission: 'view_blocks'
      },
      {
        id: 'panchayats',
        title: '🏛️ Add Panchayat',
        path: '/panchayats',
        permission: 'view_panchayats'
      },
      {
        id: 'villages',
        title: '🏘️ Add Village',
        path: '/villages',
        permission: 'view_villages'
      },
      {
        id: 'booths',
        title: '📊 Add Booth',
        path: '/booths',
        permission: 'view_booths'
      }
    ]
  },
  {
    id: 'data-collection',
    title: '📝 Data Collection & Forms',
    icon: 'clipboard',
    permission: 'view_form_builder',
    items: [
      {
        id: 'form-builder',
        title: '🔨 Form Builder',
        path: '/form-builder',
        permission: 'view_form_builder'
      },
      {
        id: 'form-list',
        title: '📋 Form List',
        path: '/forms',
        permission: 'view_form_list'
      },
      {
        id: 'respondent-table',
        title: '📋 Respondent Table',
        path: '/respondents',
        permission: 'view_respondent_table'
      },
      {
        id: 'teams',
        title: '👥 Teams',
        path: '/teams',
        permission: 'view_teams'
      }
    ]
  },
  {
    id: 'analysis',
    title: '📊 Analysis & Tools',
    icon: 'chart',
    permission: 'view_employee_analysis',
    items: [
      {
        id: 'employee-analysis',
        title: '👥 Employee Analysis',
        path: '/employee-analysis',
        permission: 'view_employee_analysis'
      },
      {
        id: 'analysis',
        title: '🔍 Analysis',
        path: '/analysis',
        permission: 'view_analysis'
      },
      {
        id: 'cache-clear',
        title: '🗑️ Cache Clear',
        path: '/cache-clear',
        permission: 'view_cache_clear'
      }
    ]
  }
];
```

### 3. Sidebar Component

```jsx
// components/Sidebar.jsx
import React from 'react';
import { hasPermission, hasAnyPermission } from '../utils/permissions';
import { sidebarConfig } from '../config/sidebar';

const Sidebar = ({ user }) => {
  const filterSidebarItems = (items) => {
    return items.filter(item => {
      if (item.permission && !hasPermission(user, item.permission)) {
        return false;
      }
      return true;
    });
  };

  const filterCategories = (categories) => {
    return categories.filter(category => {
      // Check if category has permission requirement
      if (category.permission && !hasPermission(user, category.permission)) {
        return false;
      }
      
      // Filter items within category
      const visibleItems = filterSidebarItems(category.items);
      
      // Hide category if no items are visible
      if (visibleItems.length === 0) {
        return false;
      }
      
      // Update category items to only show visible ones
      category.items = visibleItems;
      return true;
    });
  };

  const visibleCategories = filterCategories(sidebarConfig);

  return (
    <nav className="sidebar">
      {visibleCategories.map(category => (
        <div key={category.id} className="sidebar-category">
          <div className="category-header">
            <span className="category-icon">{category.icon}</span>
            <span className="category-title">{category.title}</span>
          </div>
          <ul className="category-items">
            {category.items.map(item => (
              <li key={item.id} className="sidebar-item">
                <a href={item.path} className="sidebar-link">
                  <span className="item-icon">{item.icon}</span>
                  <span className="item-title">{item.title}</span>
                </a>
              </li>
            ))}
          </ul>
        </div>
      ))}
    </nav>
  );
};

export default Sidebar;
```

### 4. Route Protection

```jsx
// components/ProtectedRoute.jsx
import React from 'react';
import { hasPermission } from '../utils/permissions';

const ProtectedRoute = ({ user, requiredPermission, children, fallback = null }) => {
  if (!user) {
    return <div>Please log in</div>;
  }

  if (!user.is_active) {
    return <div>Account is inactive</div>;
  }

  if (requiredPermission && !hasPermission(user, requiredPermission)) {
    return fallback || <div>Access denied</div>;
  }

  return children;
};

export default ProtectedRoute;
```

### 5. Usage in Routes

```jsx
// App.jsx or router configuration
import ProtectedRoute from './components/ProtectedRoute';

const App = () => {
  const [user, setUser] = useState(null);

  return (
    <Router>
      <div className="app">
        <Sidebar user={user} />
        <main className="main-content">
          <Routes>
            <Route 
              path="/dashboard" 
              element={
                <ProtectedRoute user={user} requiredPermission="view_dashboard">
                  <Dashboard />
                </ProtectedRoute>
              } 
            />
            <Route 
              path="/employees" 
              element={
                <ProtectedRoute user={user} requiredPermission="view_employee_management">
                  <EmployeeManagement />
                </ProtectedRoute>
              } 
            />
            <Route 
              path="/roles" 
              element={
                <ProtectedRoute user={user} requiredPermission="view_role_management">
                  <RoleManagement />
                </ProtectedRoute>
              } 
            />
            {/* Add more routes as needed */}
          </Routes>
        </main>
      </div>
    </Router>
  );
};
```

## Role-Based Access

### Admin Role (ID: 1)
- **Access**: All sidebar items
- **Permissions**: All permissions

### Manager Role
- **Access**: Most items except permission management
- **Can**: Manage employees, parliament data, forms, analysis
- **Cannot**: Manage roles, permissions, cache clear

### Employee Role
- **Access**: View most data, limited management
- **Can**: View all data, manage forms
- **Cannot**: Manage users, roles, permissions, cache clear

### Viewer Role
- **Access**: Read-only access to most data
- **Can**: View data, no management capabilities
- **Cannot**: Any management operations

### Guest Role
- **Access**: Minimal access, basic parliament data
- **Can**: View basic parliament structure
- **Cannot**: Most features

## API Integration

### Getting User Permissions
```javascript
// After login, user object includes permissions
const user = {
  id: 1,
  name: "Admin User",
  role: {
    id: 1,
    name: "admin",
    permissions: [
      { id: 1, name: "view_dashboard", display_name: "View Dashboard" },
      { id: 2, name: "manage_employees", display_name: "Manage Employees" },
      // ... more permissions
    ]
  }
};
```

### Checking Permissions in Components
```javascript
// In any component
const MyComponent = ({ user }) => {
  if (!hasPermission(user, 'manage_employees')) {
    return <div>Access denied</div>;
  }

  return (
    <div>
      <h1>Employee Management</h1>
      {/* Component content */}
    </div>
  );
};
```

## Testing

### Test Different Roles
1. **Admin**: Should see all sidebar items
2. **Manager**: Should see most items, no permission management
3. **Employee**: Should see view items, limited management
4. **Viewer**: Should see only view items
5. **Guest**: Should see minimal items

### Test Permission Changes
1. Update role permissions via API
2. Refresh user data
3. Verify sidebar updates accordingly

## Best Practices

1. **Always check permissions** before rendering sensitive content
2. **Use ProtectedRoute** for route-level protection
3. **Cache user permissions** to avoid repeated API calls
4. **Handle loading states** while fetching user data
5. **Provide fallback UI** for unauthorized access
6. **Test with different roles** to ensure proper access control

## Troubleshooting

### Common Issues
1. **Sidebar not updating**: Check if user data is refreshed after permission changes
2. **Routes not protected**: Ensure ProtectedRoute is used for all sensitive routes
3. **Permission checks failing**: Verify permission names match exactly
4. **Categories showing empty**: Check if category permission logic is correct

### Debug Tips
```javascript
// Add this to debug permission issues
console.log('User permissions:', user?.role?.permissions);
console.log('Required permission:', requiredPermission);
console.log('Has permission:', hasPermission(user, requiredPermission));
```
