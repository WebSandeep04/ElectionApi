# Roles & Permissions API

This document explains how frontend can manage roles and permissions and enforce what each role can or cannot do.

## Base
- Base URL: `/api`
- Auth: Bearer token for all write operations

## Data Models

### Role
- id, name, display_name, description, is_active, timestamps
- relations: permissions[] (when loaded)

### Permission
- id, name, display_name, description, is_active, timestamps

## Endpoints

### Roles (public read)
- GET `/roles`
  - query: page, per_page, search, is_active, sort_by, sort_order
  - returns `{ data: Role[], meta: {...} }`
- GET `/roles/{id}`
  - returns `{ data: Role }` (includes `permissions`)
- GET `/roles/active`
- GET `/roles/inactive`

### Roles (protected write, requires `manage_roles`)
- POST `/roles`
  - body: `{ name, display_name, description?, is_active? }`
  - returns created role
- PUT `/roles/{id}`
  - body: partial role fields
- DELETE `/roles/{id}`
  - fails if role has users
- POST `/roles/{id}/activate`
- POST `/roles/{id}/deactivate`
- POST `/roles/{id}/permissions`
  - body: `{ permission_ids: number[] }` (syncs role permissions)
  - returns role with permissions

### Permissions (public read)
- GET `/permissions`
  - query: page, per_page, search, is_active, sort_by, sort_order
- GET `/permissions/{id}`

### Permissions (protected write, requires `manage_permissions`)
- POST `/permissions`
  - body: `{ name, display_name, description?, is_active? }`
- PUT `/permissions/{id}`
- DELETE `/permissions/{id}`

## Authenticated User Payload
On login and GET `/api/user`, backend returns the user with role and permissions loaded.

Example:
```json
{
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@example.com",
    "role_id": 1,
    "is_active": true,
    "role": {
      "id": 1,
      "name": "admin",
      "display_name": "Administrator",
      "permissions": [
        { "id": 1, "name": "manage_roles", "display_name": "Manage Roles" },
        { "id": 2, "name": "manage_permissions", "display_name": "Manage Permissions" }
      ]
    }
  }
}
```

## Frontend Guidance

- Use `GET /roles` to populate role selectors.
- Use `GET /permissions` to show available permissions when editing a role.
- To assign permissions to a role, submit selected permission IDs to `POST /roles/{id}/permissions`.
- Gate UI by permissions:
  - If user.role.permissions contains `manage_roles` → show role management UI.
  - If contains `manage_permissions` → show permission management UI.

### Sample Checks (JS)
```javascript
const hasPermission = (user, perm) =>
  user?.role?.permissions?.some(p => p.name === perm);

if (hasPermission(currentUser, 'manage_roles')) {
  // show roles admin UI
}
```

### Typical Flows
- Create role → assign permissions → assign role to users.
- Toggle role active/inactive to enable/disable across the app.

## Error States
- 401 Unauthenticated: missing/invalid token
- 403 Forbidden: user lacks required permission
- 422 Validation errors: invalid payloads

## Seeded Defaults
- Permissions: `manage_roles`, `manage_permissions`, `manage_users`, `manage_employees` (active)
- `admin` role has all default permissions

## Notes
- Users have one role; permissions are granted via role.
- Public read for roles/permissions allows building pickers without auth.
- Backend middleware: `permission:<name>` guards write routes.


