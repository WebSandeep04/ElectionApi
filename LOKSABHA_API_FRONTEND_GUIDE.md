# Lok Sabha API - Frontend Developer Guide

## 🚀 Quick Start for Vite React Implementation

### Base Configuration
```javascript
// config/api.js
const API_BASE_URL = 'http://localhost:8000/api';

export const API_ENDPOINTS = {
  // Authentication
  LOGIN: `${API_BASE_URL}/login`,
  REGISTER: `${API_BASE_URL}/register`,
  LOGOUT: `${API_BASE_URL}/logout`,
  
  // Lok Sabha CRUD
  LOK_SABHAS: `${API_BASE_URL}/lok-sabhas`,
  LOK_SABHA_BY_ID: (id) => `${API_BASE_URL}/lok-sabhas/${id}`,
};

export const getAuthHeaders = (token) => ({
  'Content-Type': 'application/json',
  'Accept': 'application/json',
  'Authorization': `Bearer ${token}`,
});
```

### Authentication Setup
```javascript
// hooks/useAuth.js
import { useState, useEffect } from 'react';

export const useAuth = () => {
  const [token, setToken] = useState(localStorage.getItem('auth_token'));
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(false);

  const login = async (email, password) => {
    setLoading(true);
    try {
      const response = await fetch(API_ENDPOINTS.LOGIN, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json();
      
      if (response.ok) {
        setToken(data.token);
        setUser(data.user);
        localStorage.setItem('auth_token', data.token);
        return { success: true, data };
      } else {
        return { success: false, error: data.message };
      }
    } catch (error) {
      return { success: false, error: 'Network error' };
    } finally {
      setLoading(false);
    }
  };

  const logout = async () => {
    if (token) {
      await fetch(API_ENDPOINTS.LOGOUT, {
        method: 'POST',
        headers: getAuthHeaders(token),
      });
    }
    setToken(null);
    setUser(null);
    localStorage.removeItem('auth_token');
  };

  return { token, user, login, logout, loading };
};
```

## 📋 API Endpoints Reference

### 1. **List All Lok Sabhas** (Public)
```javascript
// GET /api/lok-sabhas
const getLokSabhas = async (page = 1) => {
  const response = await fetch(`${API_ENDPOINTS.LOK_SABHAS}?page=${page}`);
  const data = await response.json();
  
  return {
    lokSabhas: data.lok_sabhas,
    pagination: data.pagination,
  };
};
```

**Response Format:**
```json
{
  "lok_sabhas": [
    {
      "id": 1,
      "loksabha_name": "17th Lok Sabha",
      "status": "1",
      "created_at": "2025-08-13T10:36:06.000000Z",
      "updated_at": "2025-08-13T10:36:06.000000Z"
    }
  ],
  "pagination": {
    "total": 4,
    "per_page": 10,
    "current_page": 1,
    "last_page": 1,
    "from": 1,
    "to": 4,
    "has_more_pages": false
  }
}
```

### 2. **Get Specific Lok Sabha** (Public)
```javascript
// GET /api/lok-sabhas/{id}
const getLokSabha = async (id) => {
  const response = await fetch(API_ENDPOINTS.LOK_SABHA_BY_ID(id));
  const data = await response.json();
  return data.lok_sabha;
};
```

### 3. **Create Lok Sabha** (Protected - Requires Auth)
```javascript
// POST /api/lok-sabhas
const createLokSabha = async (lokSabhaData, token) => {
  const response = await fetch(API_ENDPOINTS.LOK_SABHAS, {
    method: 'POST',
    headers: getAuthHeaders(token),
    body: JSON.stringify(lokSabhaData),
  });
  
  const data = await response.json();
  
  if (response.ok) {
    return { success: true, data: data.lok_sabha };
  } else {
    return { success: false, error: data.message || data.errors };
  }
};

// Usage
const newLokSabha = await createLokSabha({
  loksabha_name: "20th Lok Sabha",
  status: "1" // optional, defaults to "1"
}, token);
```

### 4. **Update Lok Sabha** (Protected - Requires Auth)
```javascript
// PUT /api/lok-sabhas/{id} (Full Update)
const updateLokSabha = async (id, lokSabhaData, token) => {
  const response = await fetch(API_ENDPOINTS.LOK_SABHA_BY_ID(id), {
    method: 'PUT',
    headers: getAuthHeaders(token),
    body: JSON.stringify(lokSabhaData),
  });
  
  const data = await response.json();
  
  if (response.ok) {
    return { success: true, data: data.lok_sabha };
  } else {
    return { success: false, error: data.message || data.errors };
  }
};

// PATCH /api/lok-sabhas/{id} (Partial Update)
const partialUpdateLokSabha = async (id, lokSabhaData, token) => {
  const response = await fetch(API_ENDPOINTS.LOK_SABHA_BY_ID(id), {
    method: 'PATCH',
    headers: getAuthHeaders(token),
    body: JSON.stringify(lokSabhaData),
  });
  
  const data = await response.json();
  
  if (response.ok) {
    return { success: true, data: data.lok_sabha };
  } else {
    return { success: false, error: data.message || data.errors };
  }
};
```

### 5. **Delete Lok Sabha** (Protected - Requires Auth)
```javascript
// DELETE /api/lok-sabhas/{id}
const deleteLokSabha = async (id, token) => {
  const response = await fetch(API_ENDPOINTS.LOK_SABHA_BY_ID(id), {
    method: 'DELETE',
    headers: getAuthHeaders(token),
  });
  
  if (response.ok) {
    return { success: true };
  } else {
    const data = await response.json();
    return { success: false, error: data.message };
  }
};
```

## 🎯 React Components Implementation

### 1. **Lok Sabha List Component**
```jsx
// components/LokSabhaList.jsx
import { useState, useEffect } from 'react';
import { getLokSabhas } from '../services/lokSabhaService';

const LokSabhaList = () => {
  const [lokSabhas, setLokSabhas] = useState([]);
  const [pagination, setPagination] = useState({});
  const [loading, setLoading] = useState(true);
  const [currentPage, setCurrentPage] = useState(1);

  useEffect(() => {
    fetchLokSabhas();
  }, [currentPage]);

  const fetchLokSabhas = async () => {
    setLoading(true);
    try {
      const data = await getLokSabhas(currentPage);
      setLokSabhas(data.lokSabhas);
      setPagination(data.pagination);
    } catch (error) {
      console.error('Error fetching Lok Sabhas:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <div>Loading...</div>;

  return (
    <div className="lok-sabha-list">
      <h2>Lok Sabha List</h2>
      
      <div className="lok-sabhas-grid">
        {lokSabhas.map((lokSabha) => (
          <div key={lokSabha.id} className="lok-sabha-card">
            <h3>{lokSabha.loksabha_name}</h3>
            <p>Status: {lokSabha.status === '1' ? 'Active' : 'Inactive'}</p>
            <p>Created: {new Date(lokSabha.created_at).toLocaleDateString()}</p>
          </div>
        ))}
      </div>

      {/* Pagination */}
      {pagination.last_page > 1 && (
        <div className="pagination">
          <button 
            onClick={() => setCurrentPage(currentPage - 1)}
            disabled={currentPage === 1}
          >
            Previous
          </button>
          <span>Page {currentPage} of {pagination.last_page}</span>
          <button 
            onClick={() => setCurrentPage(currentPage + 1)}
            disabled={currentPage === pagination.last_page}
          >
            Next
          </button>
        </div>
      )}
    </div>
  );
};

export default LokSabhaList;
```

### 2. **Create Lok Sabha Form**
```jsx
// components/CreateLokSabhaForm.jsx
import { useState } from 'react';
import { useAuth } from '../hooks/useAuth';
import { createLokSabha } from '../services/lokSabhaService';

const CreateLokSabhaForm = ({ onSuccess }) => {
  const { token } = useAuth();
  const [formData, setFormData] = useState({
    loksabha_name: '',
    status: '1'
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    try {
      const result = await createLokSabha(formData, token);
      
      if (result.success) {
        setFormData({ loksabha_name: '', status: '1' });
        onSuccess && onSuccess(result.data);
      } else {
        setError(result.error);
      }
    } catch (error) {
      setError('Network error occurred');
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="create-form">
      <h3>Create New Lok Sabha</h3>
      
      {error && <div className="error">{error}</div>}
      
      <div className="form-group">
        <label htmlFor="loksabha_name">Lok Sabha Name *</label>
        <input
          type="text"
          id="loksabha_name"
          value={formData.loksabha_name}
          onChange={(e) => setFormData({...formData, loksabha_name: e.target.value})}
          required
        />
      </div>

      <div className="form-group">
        <label htmlFor="status">Status</label>
        <select
          id="status"
          value={formData.status}
          onChange={(e) => setFormData({...formData, status: e.target.value})}
        >
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>

      <button type="submit" disabled={loading}>
        {loading ? 'Creating...' : 'Create Lok Sabha'}
      </button>
    </form>
  );
};

export default CreateLokSabhaForm;
```

### 3. **Edit Lok Sabha Modal**
```jsx
// components/EditLokSabhaModal.jsx
import { useState, useEffect } from 'react';
import { useAuth } from '../hooks/useAuth';
import { updateLokSabha } from '../services/lokSabhaService';

const EditLokSabhaModal = ({ lokSabha, isOpen, onClose, onSuccess }) => {
  const { token } = useAuth();
  const [formData, setFormData] = useState({
    loksabha_name: '',
    status: '1'
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    if (lokSabha) {
      setFormData({
        loksabha_name: lokSabha.loksabha_name,
        status: lokSabha.status
      });
    }
  }, [lokSabha]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    try {
      const result = await updateLokSabha(lokSabha.id, formData, token);
      
      if (result.success) {
        onSuccess && onSuccess(result.data);
        onClose();
      } else {
        setError(result.error);
      }
    } catch (error) {
      setError('Network error occurred');
    } finally {
      setLoading(false);
    }
  };

  if (!isOpen) return null;

  return (
    <div className="modal-overlay">
      <div className="modal">
        <h3>Edit Lok Sabha</h3>
        
        {error && <div className="error">{error}</div>}
        
        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label htmlFor="edit_loksabha_name">Lok Sabha Name *</label>
            <input
              type="text"
              id="edit_loksabha_name"
              value={formData.loksabha_name}
              onChange={(e) => setFormData({...formData, loksabha_name: e.target.value})}
              required
            />
          </div>

          <div className="form-group">
            <label htmlFor="edit_status">Status</label>
            <select
              id="edit_status"
              value={formData.status}
              onChange={(e) => setFormData({...formData, status: e.target.value})}
            >
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>

          <div className="modal-actions">
            <button type="button" onClick={onClose}>Cancel</button>
            <button type="submit" disabled={loading}>
              {loading ? 'Updating...' : 'Update'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default EditLokSabhaModal;
```

## 🔧 Service Layer

```javascript
// services/lokSabhaService.js
import { API_ENDPOINTS, getAuthHeaders } from '../config/api';

export const lokSabhaService = {
  // Get all Lok Sabhas with pagination
  async getAll(page = 1) {
    const response = await fetch(`${API_ENDPOINTS.LOK_SABHAS}?page=${page}`);
    if (!response.ok) throw new Error('Failed to fetch Lok Sabhas');
    return response.json();
  },

  // Get single Lok Sabha
  async getById(id) {
    const response = await fetch(API_ENDPOINTS.LOK_SABHA_BY_ID(id));
    if (!response.ok) throw new Error('Lok Sabha not found');
    const data = await response.json();
    return data.lok_sabha;
  },

  // Create new Lok Sabha
  async create(lokSabhaData, token) {
    const response = await fetch(API_ENDPOINTS.LOK_SABHAS, {
      method: 'POST',
      headers: getAuthHeaders(token),
      body: JSON.stringify(lokSabhaData),
    });
    
    const data = await response.json();
    
    if (!response.ok) {
      throw new Error(data.message || 'Failed to create Lok Sabha');
    }
    
    return data.lok_sabha;
  },

  // Update Lok Sabha
  async update(id, lokSabhaData, token) {
    const response = await fetch(API_ENDPOINTS.LOK_SABHA_BY_ID(id), {
      method: 'PUT',
      headers: getAuthHeaders(token),
      body: JSON.stringify(lokSabhaData),
    });
    
    const data = await response.json();
    
    if (!response.ok) {
      throw new Error(data.message || 'Failed to update Lok Sabha');
    }
    
    return data.lok_sabha;
  },

  // Delete Lok Sabha
  async delete(id, token) {
    const response = await fetch(API_ENDPOINTS.LOK_SABHA_BY_ID(id), {
      method: 'DELETE',
      headers: getAuthHeaders(token),
    });
    
    if (!response.ok) {
      const data = await response.json();
      throw new Error(data.message || 'Failed to delete Lok Sabha');
    }
    
    return true;
  },
};
```

## 🎨 CSS Styling Examples

```css
/* styles/lokSabha.css */
.lok-sabha-list {
  padding: 20px;
}

.lok-sabhas-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin: 20px 0;
}

.lok-sabha-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 16px;
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.lok-sabha-card h3 {
  margin: 0 0 12px 0;
  color: #333;
}

.create-form {
  max-width: 500px;
  margin: 20px auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 4px;
  font-weight: 500;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.error {
  color: #d32f2f;
  background: #ffebee;
  padding: 8px 12px;
  border-radius: 4px;
  margin-bottom: 16px;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
  margin-top: 20px;
}

.pagination button {
  padding: 8px 16px;
  border: 1px solid #ddd;
  background: white;
  cursor: pointer;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal {
  background: white;
  padding: 24px;
  border-radius: 8px;
  min-width: 400px;
}

.modal-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 20px;
}
```

## 🚨 Error Handling

```javascript
// utils/errorHandler.js
export const handleApiError = (error) => {
  if (error.response) {
    // Server responded with error status
    const { status, data } = error.response;
    
    switch (status) {
      case 401:
        return 'Authentication required. Please login.';
      case 403:
        return 'You do not have permission to perform this action.';
      case 404:
        return 'Resource not found.';
      case 422:
        return data.message || 'Validation failed.';
      case 500:
        return 'Server error. Please try again later.';
      default:
        return data.message || 'An error occurred.';
    }
  } else if (error.request) {
    // Network error
    return 'Network error. Please check your connection.';
  } else {
    // Other error
    return error.message || 'An unexpected error occurred.';
  }
};
```

## 📱 React Query Integration (Optional)

```javascript
// hooks/useLokSabhas.js
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { lokSabhaService } from '../services/lokSabhaService';
import { useAuth } from './useAuth';

export const useLokSabhas = (page = 1) => {
  return useQuery({
    queryKey: ['lokSabhas', page],
    queryFn: () => lokSabhaService.getAll(page),
  });
};

export const useCreateLokSabha = () => {
  const queryClient = useQueryClient();
  const { token } = useAuth();

  return useMutation({
    mutationFn: (data) => lokSabhaService.create(data, token),
    onSuccess: () => {
      queryClient.invalidateQueries(['lokSabhas']);
    },
  });
};

export const useUpdateLokSabha = () => {
  const queryClient = useQueryClient();
  const { token } = useAuth();

  return useMutation({
    mutationFn: ({ id, data }) => lokSabhaService.update(id, data, token),
    onSuccess: () => {
      queryClient.invalidateQueries(['lokSabhas']);
    },
  });
};

export const useDeleteLokSabha = () => {
  const queryClient = useQueryClient();
  const { token } = useAuth();

  return useMutation({
    mutationFn: (id) => lokSabhaService.delete(id, token),
    onSuccess: () => {
      queryClient.invalidateQueries(['lokSabhas']);
    },
  });
};
```

## 🔐 Authentication Flow

1. **Login** → Get token
2. **Store token** in localStorage
3. **Include token** in Authorization header for protected routes
4. **Handle 401 errors** by redirecting to login
5. **Logout** → Clear token and redirect

## 📋 Implementation Checklist

- [ ] Set up API configuration
- [ ] Implement authentication hooks
- [ ] Create service layer
- [ ] Build list component with pagination
- [ ] Create form components
- [ ] Add error handling
- [ ] Implement loading states
- [ ] Add CSS styling
- [ ] Test all CRUD operations
- [ ] Handle authentication flow

## 🎯 Key Points for Frontend Dev

1. **Authentication**: All write operations require Bearer token
2. **Pagination**: 10 items per page, use `?page=X` parameter
3. **Status**: Use "1" for active, "0" for inactive
4. **Validation**: Handle 422 errors for form validation
5. **Error Handling**: Implement proper error messages
6. **Loading States**: Show loading indicators during API calls

## 📞 Support

If you encounter any issues:
1. Check network tab for API responses
2. Verify authentication token is valid
3. Ensure correct Content-Type headers
4. Handle CORS if needed (backend should be configured)

Good luck with the implementation! 🚀
