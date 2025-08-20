# Postman Testing Guide - Lok Sabha API

## 🚀 Step-by-Step Manual Testing in Postman

### **Prerequisites**
- Postman installed
- Laravel server running on `http://localhost:8000`
- Import the `lok_sabha_postman_collection.json` file

---

## 📋 **Step 1: Set Up Environment Variables**

1. **Create Environment:**
   - Click the gear icon (⚙️) in top right
   - Click "Add" → Name it "Lok Sabha API"
   - Add these variables:
     - `base_url`: `http://localhost:8000`
     - `auth_token`: Leave empty for now

2. **Select Environment:**
   - Choose "Lok Sabha API" from the environment dropdown

---

## 🔐 **Step 2: Authentication Setup**

### **2.1 Register a New User**
1. **Open Postman**
2. **Create new request:**
   - Method: `POST`
   - URL: `{{base_url}}/api/register`
   - Headers:
     ```
     Content-Type: application/json
     Accept: application/json
     ```
   - Body (raw JSON):
     ```json
     {
       "name": "Test User",
       "email": "test@example.com",
       "password": "password123",
       "password_confirmation": "password123"
     }
     ```

3. **Send request** → Should get 201 response with token

4. **Copy the token** from response:
   ```json
   {
     "token": "1|abc123...",
     "token_type": "Bearer"
   }
   ```

### **2.2 Set Auth Token**
1. **Go to Environment Variables**
2. **Set `auth_token`** to the token you copied
3. **Save the environment**

---

## 📝 **Step 3: Test CREATE Operations**

### **3.1 Create First Lok Sabha**
1. **Create new request:**
   - Method: `POST`
   - URL: `{{base_url}}/api/lok-sabhas`
   - Headers:
     ```
     Content-Type: application/json
     Accept: application/json
     Authorization: Bearer {{auth_token}}
     ```
   - Body (raw JSON):
     ```json
     {
       "loksabha_name": "17th Lok Sabha",
       "status": "1"
     }
     ```

2. **Send request** → Should get 201 response
3. **Note the ID** from response (e.g., `"id": 1`)

### **3.2 Create Second Lok Sabha**
1. **Duplicate the previous request**
2. **Change body to:**
   ```json
   {
     "loksabha_name": "18th Lok Sabha",
     "status": "1"
   }
   ```
3. **Send request** → Should get 201 response

### **3.3 Create Without Status (Test Default)**
1. **Duplicate the request**
2. **Change body to:**
   ```json
   {
     "loksabha_name": "19th Lok Sabha"
   }
   ```
3. **Send request** → Should get 201 response with status "1"

---

## 📖 **Step 4: Test READ Operations**

### **4.1 List All Lok Sabhas**
1. **Create new request:**
   - Method: `GET`
   - URL: `{{base_url}}/api/lok-sabhas`
   - Headers:
     ```
     Accept: application/json
     ```

2. **Send request** → Should get 200 response with:
   ```json
   {
     "lok_sabhas": [...],
     "pagination": {
       "total": 3,
       "per_page": 10,
       "current_page": 1,
       "last_page": 1,
       "has_more_pages": false
     }
   }
   ```

### **4.2 Test Pagination**
1. **Add query parameter:**
   - URL: `{{base_url}}/api/lok-sabhas?page=1`
2. **Send request** → Should get same response

### **4.3 Get Specific Lok Sabha**
1. **Create new request:**
   - Method: `GET`
   - URL: `{{base_url}}/api/lok-sabhas/1` (use ID from step 3.1)
   - Headers:
     ```
     Accept: application/json
     ```

2. **Send request** → Should get 200 response:
   ```json
   {
     "lok_sabha": {
       "id": 1,
       "loksabha_name": "17th Lok Sabha",
       "status": "1",
       "created_at": "...",
       "updated_at": "..."
     }
   }
   ```

---

## ✏️ **Step 5: Test UPDATE Operations**

### **5.1 Full Update (PUT)**
1. **Create new request:**
   - Method: `PUT`
   - URL: `{{base_url}}/api/lok-sabhas/1`
   - Headers:
     ```
     Content-Type: application/json
     Accept: application/json
     Authorization: Bearer {{auth_token}}
     ```
   - Body (raw JSON):
     ```json
     {
       "loksabha_name": "17th Lok Sabha (Updated)",
       "status": "0"
     }
     ```

2. **Send request** → Should get 200 response

### **5.2 Partial Update (PATCH)**
1. **Create new request:**
   - Method: `PATCH`
   - URL: `{{base_url}}/api/lok-sabhas/1`
   - Headers:
     ```
     Content-Type: application/json
     Accept: application/json
     Authorization: Bearer {{auth_token}}
     ```
   - Body (raw JSON):
     ```json
     {
       "status": "1"
     }
     ```

2. **Send request** → Should get 200 response

### **5.3 Verify Update**
1. **GET** `{{base_url}}/api/lok-sabhas/1`
2. **Send request** → Should show updated data

---

## 🗑️ **Step 6: Test DELETE Operations**

### **6.1 Delete Lok Sabha**
1. **Create new request:**
   - Method: `DELETE`
   - URL: `{{base_url}}/api/lok-sabhas/1`
   - Headers:
     ```
     Accept: application/json
     Authorization: Bearer {{auth_token}}
     ```

2. **Send request** → Should get 200 response:
   ```json
   {
     "message": "Lok Sabha deleted successfully"
   }
   ```

### **6.2 Verify Deletion**
1. **GET** `{{base_url}}/api/lok-sabhas/1`
2. **Send request** → Should get 404 response

---

## 🚨 **Step 7: Test Error Cases**

### **7.1 Unauthorized Access**
1. **Create new request:**
   - Method: `POST`
   - URL: `{{base_url}}/api/lok-sabhas`
   - Headers:
     ```
     Content-Type: application/json
     Accept: application/json
     ```
   - Body:
     ```json
     {
       "loksabha_name": "Test",
       "status": "1"
     }
     ```

2. **Send request** → Should get 401 response:
   ```json
   {
     "message": "Unauthenticated."
   }
   ```

### **7.2 Validation Error**
1. **Create new request:**
   - Method: `POST`
   - URL: `{{base_url}}/api/lok-sabhas`
   - Headers:
     ```
     Content-Type: application/json
     Accept: application/json
     Authorization: Bearer {{auth_token}}
     ```
   - Body:
     ```json
     {
       "loksabha_name": "",
       "status": "invalid"
     }
     ```

2. **Send request** → Should get 422 response:
   ```json
   {
     "message": "The loksabha name field is required.",
     "errors": {
       "loksabha_name": [
         "The loksabha name field is required."
       ]
     }
   }
   ```

### **7.3 Not Found**
1. **Create new request:**
   - Method: `GET`
   - URL: `{{base_url}}/api/lok-sabhas/99999`
   - Headers:
     ```
     Accept: application/json
     ```

2. **Send request** → Should get 404 response

---

## 🔐 **Step 8: Test Authentication**

### **8.1 Login**
1. **Create new request:**
   - Method: `POST`
   - URL: `{{base_url}}/api/login`
   - Headers:
     ```
     Content-Type: application/json
     Accept: application/json
     ```
   - Body:
     ```json
     {
       "email": "test@example.com",
       "password": "password123"
     }
     ```

2. **Send request** → Should get 200 response with new token

### **8.2 Logout**
1. **Create new request:**
   - Method: `POST`
   - URL: `{{base_url}}/api/logout`
   - Headers:
     ```
     Accept: application/json
     Authorization: Bearer {{auth_token}}
     ```

2. **Send request** → Should get 200 response

---

## 📊 **Step 9: Test Pagination Edge Cases**

### **9.1 Empty Page**
1. **GET** `{{base_url}}/api/lok-sabhas?page=999`
2. **Send request** → Should get 200 with empty array

### **9.2 Invalid Page**
1. **GET** `{{base_url}}/api/lok-sabhas?page=abc`
2. **Send request** → Should get 200 with page 1 data

---

## ✅ **Expected Results Summary**

| Operation | Method | URL | Expected Status | Notes |
|-----------|--------|-----|----------------|-------|
| Register | POST | `/api/register` | 201 | Get token |
| Login | POST | `/api/login` | 200 | Get token |
| Create | POST | `/api/lok-sabhas` | 201 | Requires auth |
| List | GET | `/api/lok-sabhas` | 200 | Public |
| Get One | GET | `/api/lok-sabhas/{id}` | 200 | Public |
| Update | PUT | `/api/lok-sabhas/{id}` | 200 | Requires auth |
| Partial Update | PATCH | `/api/lok-sabhas/{id}` | 200 | Requires auth |
| Delete | DELETE | `/api/lok-sabhas/{id}` | 200 | Requires auth |
| Logout | POST | `/api/logout` | 200 | Requires auth |
| Unauthorized | Any | Protected routes | 401 | No token |
| Validation | POST | `/api/lok-sabhas` | 422 | Invalid data |
| Not Found | GET | `/api/lok-sabhas/99999` | 404 | Invalid ID |

---

## 🎯 **Testing Checklist**

- [ ] **Authentication**
  - [ ] Register user
  - [ ] Login user
  - [ ] Set auth token
  - [ ] Logout user

- [ ] **CREATE Operations**
  - [ ] Create with all fields
  - [ ] Create without status (test default)
  - [ ] Create multiple records

- [ ] **READ Operations**
  - [ ] List all with pagination
  - [ ] Get specific by ID
  - [ ] Test pagination parameters

- [ ] **UPDATE Operations**
  - [ ] Full update (PUT)
  - [ ] Partial update (PATCH)
  - [ ] Verify updates

- [ ] **DELETE Operations**
  - [ ] Delete record
  - [ ] Verify deletion

- [ ] **Error Handling**
  - [ ] Unauthorized access
  - [ ] Validation errors
  - [ ] Not found errors

- [ ] **Edge Cases**
  - [ ] Empty pagination
  - [ ] Invalid parameters

---

## 🚨 **Troubleshooting**

### **Common Issues:**

1. **401 Unauthorized:**
   - Check if token is set in environment
   - Verify token format: `Bearer {token}`
   - Try logging in again

2. **404 Not Found:**
   - Check if Laravel server is running
   - Verify URL is correct
   - Check if record exists

3. **422 Validation Error:**
   - Check required fields
   - Verify data format
   - Check field validation rules

4. **Network Error:**
   - Check if server is running on `http://localhost:8000`
   - Verify CORS configuration
   - Check firewall settings

### **Debug Tips:**
- Use Postman's Console to see request/response details
- Check Laravel logs in `storage/logs/laravel.log`
- Use browser's Network tab as alternative
- Test with cURL for comparison

---

## 🎉 **Success Criteria**

All tests pass when:
- ✅ Authentication works (register, login, logout)
- ✅ CRUD operations work (create, read, update, delete)
- ✅ Pagination works correctly
- ✅ Error handling works (401, 404, 422)
- ✅ Validation works properly
- ✅ Authorization works (protected routes)

**Congratulations! Your Lok Sabha API is working perfectly! 🚀**
