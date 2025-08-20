# Postman Manual Testing Steps - Lok Sabha API

## 🚀 Quick Setup

### 1. **Environment Setup**
- Create environment: "Lok Sabha API"
- Add variables:
  - `base_url`: `http://localhost:8000`
  - `auth_token`: (leave empty)

---

## 📋 **Step-by-Step Testing**

### **Step 1: Authentication**
1. **Register User**
   - Method: `POST`
   - URL: `{{base_url}}/api/register`
   - Headers: `Content-Type: application/json`
   - Body:
   ```json
   {
     "name": "Test User",
     "email": "test@example.com", 
     "password": "password123",
     "password_confirmation": "password123"
   }
   ```
   - **Expected**: 201 status with token

2. **Copy token** from response and set `auth_token` variable

### **Step 2: Create Lok Sabha**
1. **Create Request**
   - Method: `POST`
   - URL: `{{base_url}}/api/lok-sabhas`
   - Headers: 
     - `Content-Type: application/json`
     - `Authorization: Bearer {{auth_token}}`
   - Body:
   ```json
   {
     "loksabha_name": "17th Lok Sabha",
     "status": "1"
   }
   ```
   - **Expected**: 201 status

2. **Note the ID** from response (e.g., `"id": 1`)

### **Step 3: List All Lok Sabhas**
1. **List Request**
   - Method: `GET`
   - URL: `{{base_url}}/api/lok-sabhas`
   - Headers: `Accept: application/json`
   - **Expected**: 200 status with pagination

### **Step 4: Get Specific Lok Sabha**
1. **Get Request**
   - Method: `GET`
   - URL: `{{base_url}}/api/lok-sabhas/1` (use ID from step 2)
   - Headers: `Accept: application/json`
   - **Expected**: 200 status with single record

### **Step 5: Update Lok Sabha**
1. **Update Request**
   - Method: `PUT`
   - URL: `{{base_url}}/api/lok-sabhas/1`
   - Headers:
     - `Content-Type: application/json`
     - `Authorization: Bearer {{auth_token}}`
   - Body:
   ```json
   {
     "loksabha_name": "17th Lok Sabha (Updated)",
     "status": "0"
   }
   ```
   - **Expected**: 200 status

### **Step 6: Delete Lok Sabha**
1. **Delete Request**
   - Method: `DELETE`
   - URL: `{{base_url}}/api/lok-sabhas/1`
   - Headers: `Authorization: Bearer {{auth_token}}`
   - **Expected**: 200 status

2. **Verify Deletion**
   - Method: `GET`
   - URL: `{{base_url}}/api/lok-sabhas/1`
   - **Expected**: 404 status

---

## 🚨 **Error Testing**

### **Unauthorized Access**
- Method: `POST`
- URL: `{{base_url}}/api/lok-sabhas`
- Body: `{"loksabha_name": "Test"}`
- **Expected**: 401 status

### **Validation Error**
- Method: `POST`
- URL: `{{base_url}}/api/lok-sabhas`
- Headers: `Authorization: Bearer {{auth_token}}`
- Body: `{"loksabha_name": ""}`
- **Expected**: 422 status

### **Not Found**
- Method: `GET`
- URL: `{{base_url}}/api/lok-sabhas/99999`
- **Expected**: 404 status

---

## ✅ **Expected Results**

| Operation | Status | Response |
|-----------|--------|----------|
| Register | 201 | Token + User data |
| Create | 201 | Created Lok Sabha |
| List | 200 | Array + Pagination |
| Get One | 200 | Single record |
| Update | 200 | Updated record |
| Delete | 200 | Success message |
| Unauthorized | 401 | "Unauthenticated" |
| Validation | 422 | Error details |
| Not Found | 404 | Error message |

---

## 🎯 **Quick Test Checklist**

- [ ] Register user (201)
- [ ] Create Lok Sabha (201)
- [ ] List all (200)
- [ ] Get specific (200)
- [ ] Update (200)
- [ ] Delete (200)
- [ ] Test unauthorized (401)
- [ ] Test validation (422)
- [ ] Test not found (404)

**All tests pass = API is working perfectly! 🚀**
