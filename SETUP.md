# Laravel Authentication API - Quick Setup

## ✅ What's Been Created

A complete, working authentication API with the following features:

- **User Registration** - Create new user accounts
- **User Login** - Authenticate users and get access tokens
- **User Logout** - Revoke access tokens
- **User Profile** - Get authenticated user information
- **Token-based Authentication** - Using Laravel Sanctum
- **Request Validation** - Comprehensive input validation
- **API Resources** - Consistent JSON responses

## 🚀 Quick Start

### 1. Install Dependencies
```bash
composer install
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup
```bash
php artisan migrate
```

### 4. Start the Server
```bash
php artisan serve
```

### 5. Test the API
```bash
php test_api.php
```

## 📋 API Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/register` | Register new user | No |
| POST | `/api/login` | Login user | No |
| GET | `/api/user` | Get user profile | Yes |
| POST | `/api/logout` | Logout user | Yes |

## 🧪 Testing

### Using the Test Script
```bash
php test_api.php
```

### Using Postman
1. Import `postman_collection.json`
2. Set `base_url` variable to `http://localhost:8000`
3. Test endpoints in order

### Using cURL
```bash
# Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'

# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'
```

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/Auth/AuthController.php    # Main auth logic
│   ├── Requests/Auth/                         # Request validation
│   │   ├── LoginRequest.php
│   │   └── RegisterRequest.php
│   └── Resources/UserResource.php             # API response formatting
├── Models/User.php                            # User model with Sanctum
routes/
└── api.php                                    # API routes
```

## 🔧 Configuration

### Laravel Sanctum
- Token-based authentication
- Automatic token revocation on login
- Configurable token expiration

### Validation Rules
- **Registration**: Name, email (unique), password (confirmed, min 8 chars)
- **Login**: Email, password (min 8 chars)

### Security Features
- Password hashing
- CSRF protection
- Input sanitization
- Token-based auth

## 📚 Documentation

For detailed API documentation, see `API_DOCUMENTATION.md`

## 🎯 Next Steps

You can extend this API by:

1. **Adding Email Verification**
   - Implement `MustVerifyEmail` interface
   - Add email verification routes

2. **Adding Password Reset**
   - Use Laravel's built-in password reset
   - Add reset routes to API

3. **Adding More User Fields**
   - Update validation rules
   - Modify UserResource
   - Add database migrations

4. **Adding Rate Limiting**
   - Configure rate limiting middleware
   - Protect against brute force attacks

## 🐛 Troubleshooting

### Common Issues

1. **Routes not found**: Make sure `bootstrap/app.php` includes API routes
2. **Database connection**: Check `.env` database configuration
3. **Token not working**: Include `Bearer` prefix in Authorization header
4. **Validation errors**: Check request format matches expected schema

### Debug Mode
Set `APP_DEBUG=true` in `.env` for detailed error messages.

---

**🎉 Your authentication API is ready to use!**
