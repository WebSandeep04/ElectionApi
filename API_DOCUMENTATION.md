# Laravel Authentication API Documentation

This is a complete authentication API built with Laravel and Laravel Sanctum for token-based authentication.

## Features

- ✅ User Registration
- ✅ User Login
- ✅ User Logout
- ✅ Get User Profile
- ✅ Token-based Authentication
- ✅ Request Validation
- ✅ API Resources for consistent responses

## API Endpoints

### Base URL
```
http://localhost:8000/api
```

### 1. Register User

**Endpoint:** `POST /api/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
}
```

### 2. Login User

**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "message": "User logged in successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "token": "2|def456...",
    "token_type": "Bearer"
}
```

### 3. Get User Profile

**Endpoint:** `GET /api/user`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 4. Logout User

**Endpoint:** `POST /api/logout`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
    "message": "User logged out successfully"
}
```

## Error Responses

### Validation Errors (422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The email field is required."
        ],
        "password": [
            "The password field is required."
        ]
    }
}
```

### Authentication Error (401)
```json
{
    "message": "Unauthenticated."
}
```

### Invalid Credentials (422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The provided credentials are incorrect."
        ]
    }
}
```

## Testing the API

### Using Postman

1. Import the `postman_collection.json` file into Postman
2. Set the `base_url` variable to `http://localhost:8000`
3. Start your Laravel server: `php artisan serve`
4. Test the endpoints in order:
   - Register a new user
   - Copy the token from the response
   - Set the `auth_token` variable in Postman
   - Test login, user profile, and logout

### Using cURL

#### Register
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

#### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

#### Get User Profile
```bash
curl -X GET http://localhost:8000/api/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

#### Logout
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Setup Instructions

1. **Install Dependencies:**
   ```bash
   composer install
   ```

2. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup:**
   ```bash
   php artisan migrate
   ```

4. **Start Server:**
   ```bash
   php artisan serve
   ```

## Security Features

- **Password Hashing:** All passwords are hashed using Laravel's built-in hashing
- **Token Revocation:** Previous tokens are revoked on login for security
- **Input Validation:** All inputs are validated with custom error messages
- **CSRF Protection:** API routes are protected from CSRF attacks
- **Rate Limiting:** Can be easily added using Laravel's rate limiting middleware

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Auth/
│   │       └── AuthController.php
│   ├── Requests/
│   │   └── Auth/
│   │       ├── LoginRequest.php
│   │       └── RegisterRequest.php
│   └── Resources/
│       └── UserResource.php
├── Models/
│   └── User.php
routes/
└── api.php
```

## Customization

### Adding More Fields
To add more fields to the user registration, update:
1. `RegisterRequest.php` - Add validation rules
2. `AuthController.php` - Add fields to the create method
3. `UserResource.php` - Add fields to the response
4. User migration - Add database columns

### Adding Email Verification
1. Implement `MustVerifyEmail` interface in User model
2. Add email verification routes
3. Configure mail settings in `.env`

### Adding Password Reset
1. Use Laravel's built-in password reset features
2. Add password reset routes to API
3. Configure mail settings

## Troubleshooting

### Common Issues

1. **Token not working:** Make sure to include `Bearer` prefix in Authorization header
2. **CORS issues:** Configure CORS in `config/cors.php` if needed
3. **Database connection:** Check your `.env` file database configuration
4. **Validation errors:** Check the request format matches the expected schema

### Debug Mode
For development, ensure `APP_DEBUG=true` in your `.env` file for detailed error messages.
