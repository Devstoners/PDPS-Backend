# Tax Payee API Documentation - PDPS

## 📋 Overview

The Tax Payee API provides comprehensive CRUD operations for managing tax payees in the PDPS system. The API is designed to handle the exact payload structure from your frontend application.

## 🚀 API Endpoints

### **Base URL**: `/api/tax-payees`

| Method | Endpoint | Description | Authentication |
|--------|----------|-------------|----------------|
| GET | `/api/tax-payees` | List all tax payees | Required |
| POST | `/api/tax-payees` | Create new tax payee | Required |
| GET | `/api/tax-payees/{id}` | Get single tax payee | Required |
| PUT | `/api/tax-payees/{id}` | Update tax payee | Required |
| DELETE | `/api/tax-payees/{id}` | Delete tax payee | Required |
| GET | `/api/tax-payees/search/nic` | Search by NIC | Required |

## 📱 Frontend Payload Structure

Your frontend payload structure is perfectly supported:

```json
{
  "title": "1",
  "name": "Asanka Bopegedara",
  "nic": "883323386V",
  "tel": "0778590294",
  "address": "126",
  "email": "bz@gmail.com"
}
```

## 🔧 API Usage Examples

### **1. Create Tax Payee**

```http
POST /api/tax-payees
Content-Type: application/json
Authorization: Bearer {token}

{
  "title": "1",
  "name": "Asanka Bopegedara",
  "nic": "883323386V",
  "tel": "0778590294",
  "address": "126",
  "email": "bz@gmail.com"
}
```

**Response:**
```json
{
  "message": "Tax payee created successfully",
  "data": {
    "id": 1,
    "title": 1,
    "name": "Asanka Bopegedara",
    "nic": "883323386V",
    "tel": "0778590294",
    "address": "126",
    "email": "bz@gmail.com",
    "created_at": "2024-01-27T10:00:00.000000Z",
    "updated_at": "2024-01-27T10:00:00.000000Z"
  }
}
```

### **2. List Tax Payees**

```http
GET /api/tax-payees?search=Asanka
Authorization: Bearer {token}
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": 1,
      "name": "Asanka Bopegedara",
      "nic": "883323386V",
      "tel": "0778590294",
      "address": "126",
      "email": "bz@gmail.com",
      "tax_properties": []
    }
  ],
  "current_page": 1,
  "per_page": 15,
  "total": 1
}
```

### **3. Get Single Tax Payee**

```http
GET /api/tax-payees/1
Authorization: Bearer {token}
```

**Response:**
```json
{
  "id": 1,
  "title": 1,
  "name": "Asanka Bopegedara",
  "nic": "883323386V",
  "tel": "0778590294",
  "address": "126",
  "email": "bz@gmail.com",
  "tax_properties": [],
  "tax_assessments": [],
  "tax_payments": []
}
```

### **4. Update Tax Payee**

```http
PUT /api/tax-payees/1
Content-Type: application/json
Authorization: Bearer {token}

{
  "title": "1",
  "name": "Asanka Bopegedara Updated",
  "nic": "883323386V",
  "tel": "0778590294",
  "address": "126 Updated Address",
  "email": "bz.updated@gmail.com"
}
```

**Response:**
```json
{
  "message": "Tax payee updated successfully",
  "data": {
    "id": 1,
    "title": 1,
    "name": "Asanka Bopegedara Updated",
    "nic": "883323386V",
    "tel": "0778590294",
    "address": "126 Updated Address",
    "email": "bz.updated@gmail.com"
  }
}
```

### **5. Search by NIC**

```http
GET /api/tax-payees/search/nic?nic=883323386V
Authorization: Bearer {token}
```

**Response:**
```json
{
  "id": 1,
  "title": 1,
  "name": "Asanka Bopegedara",
  "nic": "883323386V",
  "tel": "0778590294",
  "address": "126",
  "email": "bz@gmail.com",
  "tax_properties": [],
  "tax_assessments": []
}
```

### **6. Delete Tax Payee**

```http
DELETE /api/tax-payees/1
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Tax payee deleted successfully"
}
```

## 🔒 Validation Rules

### **Required Fields**
- `title` - Integer (1, 2, 3, 4)
- `name` - String (max 255 characters)
- `nic` - String (max 12 characters, unique)
- `tel` - String (max 15 characters)
- `address` - String (max 250 characters)
- `email` - Email (max 255 characters)

### **Title Values**
- `1` - Mr.
- `2` - Mrs.
- `3` - Miss
- `4` - Dr.

## 🔍 Search Functionality

### **General Search**
```http
GET /api/tax-payees?search=Asanka
```

Searches both name and NIC fields.

### **NIC Search**
```http
GET /api/tax-payees/search/nic?nic=883323386V
```

Exact NIC match for quick lookup.

## 📊 Pagination

The list endpoint supports pagination:

```http
GET /api/tax-payees?page=1&per_page=15
```

**Response Structure:**
```json
{
  "data": [...],
  "current_page": 1,
  "per_page": 15,
  "total": 100,
  "last_page": 7,
  "from": 1,
  "to": 15
}
```

## 🔗 Relationships

### **Tax Properties**
Each tax payee can have multiple tax properties:

```json
{
  "tax_properties": [
    {
      "id": 1,
      "property_no": "P001",
      "address": "123 Main Street",
      "division": {
        "id": 1,
        "name": "Colombo Division"
      }
    }
  ]
}
```

### **Tax Assessments**
Each tax payee can have multiple tax assessments:

```json
{
  "tax_assessments": [
    {
      "id": 1,
      "year": 2024,
      "amount": "5000.00",
      "due_date": "2024-03-31",
      "status": "unpaid"
    }
  ]
}
```

### **Tax Payments**
Each tax payee can have multiple tax payments:

```json
{
  "tax_payments": [
    {
      "id": 1,
      "payment": "2500.00",
      "pay_date": "2024-01-15",
      "pay_method": "online",
      "status": "confirmed"
    }
  ]
}
```

## 🚨 Error Handling

### **Validation Errors**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "nic": ["The nic field is required."],
    "email": ["The email must be a valid email address."]
  }
}
```

### **Not Found**
```json
{
  "message": "Tax payee not found"
}
```

### **Cannot Delete**
```json
{
  "message": "Cannot delete payee with existing properties"
}
```

## 🧪 Testing

### **Test Script**
Use the provided test script:

```bash
php test-tax-payee-api.php
```

### **Manual Testing**
```bash
# Start Laravel server
php artisan serve

# Test with curl
curl -X POST http://localhost:8000/api/tax-payees \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "title": "1",
    "name": "Asanka Bopegedara",
    "nic": "883323386V",
    "tel": "0778590294",
    "address": "126",
    "email": "bz@gmail.com"
  }'
```

## 🔐 Authentication

All endpoints require authentication with the appropriate role:

- **Admin**: Full access
- **OfficerTax**: Full access to tax payees

### **Required Headers**
```http
Authorization: Bearer {your_jwt_token}
Content-Type: application/json
Accept: application/json
```

## 📱 Frontend Integration

### **JavaScript Example**
```javascript
// Create tax payee
const createTaxPayee = async (payeeData) => {
  const response = await fetch('/api/tax-payees', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify(payeeData)
  });
  
  return await response.json();
};

// Usage
const payeeData = {
  title: "1",
  name: "Asanka Bopegedara",
  nic: "883323386V",
  tel: "0778590294",
  address: "126",
  email: "bz@gmail.com"
};

createTaxPayee(payeeData);
```

### **React Example**
```jsx
import { useState } from 'react';

const TaxPayeeForm = () => {
  const [formData, setFormData] = useState({
    title: "1",
    name: "",
    nic: "",
    tel: "",
    address: "",
    email: ""
  });

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await fetch('/api/tax-payees', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(formData)
      });
      
      const result = await response.json();
      console.log('Tax payee created:', result);
    } catch (error) {
      console.error('Error:', error);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      {/* Form fields */}
    </form>
  );
};
```

## 🎯 Best Practices

### **1. Error Handling**
Always handle API errors gracefully:

```javascript
try {
  const response = await fetch('/api/tax-payees', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify(payeeData)
  });
  
  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message);
  }
  
  const result = await response.json();
  return result;
} catch (error) {
  console.error('API Error:', error.message);
  throw error;
}
```

### **2. Loading States**
Implement loading states for better UX:

```javascript
const [loading, setLoading] = useState(false);

const createTaxPayee = async (payeeData) => {
  setLoading(true);
  try {
    const result = await fetch('/api/tax-payees', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify(payeeData)
    });
    
    return await result.json();
  } finally {
    setLoading(false);
  }
};
```

### **3. Form Validation**
Validate data before sending:

```javascript
const validatePayeeData = (data) => {
  const errors = {};
  
  if (!data.name) errors.name = 'Name is required';
  if (!data.nic) errors.nic = 'NIC is required';
  if (!data.email) errors.email = 'Email is required';
  if (!data.tel) errors.tel = 'Phone is required';
  
  return Object.keys(errors).length === 0 ? null : errors;
};
```

## ✅ Conclusion

The Tax Payee API is fully configured and ready for use with your frontend application. The API supports:

- ✅ **Complete CRUD operations**
- ✅ **Frontend payload structure support**
- ✅ **Search functionality**
- ✅ **Pagination**
- ✅ **Relationship loading**
- ✅ **Error handling**
- ✅ **Authentication**
- ✅ **Validation**

**Ready for production use!** 🚀
