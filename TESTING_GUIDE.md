# API Testing Guide

## Using Postman

### 1. Import Configuration
Create a new Postman collection with these environment variables:
```
base_url: http://localhost:8000
token: (will be set after login)
```

### 2. Authentication Flow

#### Step 1: Register a New User
```
POST {{base_url}}/api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "status": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user",
      "created_at": "2025-11-28T10:00:00.000000Z",
      "updated_at": "2025-11-28T10:00:00.000000Z"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz"
  }
}
```

Save the token for subsequent requests.

#### Step 2: Login
```
POST {{base_url}}/api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:** Same as register, returns user and token

#### Step 3: Get Current User
```
GET {{base_url}}/api/me
Authorization: Bearer {{token}}
```

---

## Post Management

### Create a Post
```
POST {{base_url}}/api/posts
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "title": "My First Post",
  "description": "This is a detailed description of my first post"
}
```

**Response:**
```json
{
  "status": true,
  "message": "Post created successfully",
  "data": {
    "post": {
      "id": 1,
      "user_id": 1,
      "title": "My First Post",
      "description": "This is a detailed description of my first post",
      "created_at": "2025-11-28T10:00:00.000000Z",
      "updated_at": "2025-11-28T10:00:00.000000Z",
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user"
      }
    }
  }
}
```

### Get All Posts
```
GET {{base_url}}/api/posts
Authorization: Bearer {{token}}
```

### Get Single Post
```
GET {{base_url}}/api/posts/1
Authorization: Bearer {{token}}
```

### Update Post
```
PUT {{base_url}}/api/posts/1
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "title": "Updated Title",
  "description": "Updated description"
}
```

### Delete Post
```
DELETE {{base_url}}/api/posts/1
Authorization: Bearer {{token}}
```

---

## User Management (Admin Only)

### Create Admin User (via database first)

Use Tinker to create an admin user:
```bash
php artisan tinker
```

Then in Tinker console:
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$admin = User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => Hash::make('adminpass'),
    'role' => 'admin'
]);
```

### Login as Admin
```
POST {{base_url}}/api/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "adminpass"
}
```

Save the admin token.

### Get All Users
```
GET {{base_url}}/api/users
Authorization: Bearer {{admin_token}}
```

### Create New User (as Admin)
```
POST {{base_url}}/api/users
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "password123",
  "role": "user"
}
```

### Update User (as Admin)
```
PUT {{base_url}}/api/users/1
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
  "name": "Updated Name",
  "role": "admin"
}
```

### Delete User (as Admin)
```
DELETE {{base_url}}/api/users/1
Authorization: Bearer {{admin_token}}
```

---

## Error Scenarios

### 1. Invalid Credentials
```
POST {{base_url}}/api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "wrongpassword"
}
```

**Response (401):**
```json
{
  "status": false,
  "message": "Invalid credentials"
}
```

### 2. Missing Required Field
```
POST {{base_url}}/api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com"
}
```

**Response (422):**
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "password": ["The password field is required."]
  }
}
```

### 3. Unauthorized (Missing Token)
```
GET {{base_url}}/api/posts
```

**Response (401):**
```json
{
  "status": false,
  "message": "Unauthenticated"
}
```

### 4. Forbidden (User trying to access admin endpoint)
```
GET {{base_url}}/api/users
Authorization: Bearer {{user_token}}
```

**Response (403):**
```json
{
  "status": false,
  "message": "Unauthorized"
}
```

### 5. Not Found
```
GET {{base_url}}/api/posts/999
Authorization: Bearer {{token}}
```

**Response (404):**
```json
{
  "status": false,
  "message": "Post not found"
}
```

---

## cURL Examples

### Register
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Get Posts (replace TOKEN with actual token)
```bash
curl -X GET http://localhost:8000/api/posts \
  -H "Authorization: Bearer TOKEN"
```

### Create Post
```bash
curl -X POST http://localhost:8000/api/posts \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "My Post",
    "description": "Post description"
  }'
```

### Update Post
```bash
curl -X PUT http://localhost:8000/api/posts/1 \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated Title"
  }'
```

### Delete Post
```bash
curl -X DELETE http://localhost:8000/api/posts/1 \
  -H "Authorization: Bearer TOKEN"
```

---

## JavaScript/Fetch Examples

### Register
```javascript
fetch('http://localhost:8000/api/register', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    name: 'John Doe',
    email: 'john@example.com',
    password: 'password123'
  })
})
.then(response => response.json())
.then(data => {
  console.log('Token:', data.data.token);
  localStorage.setItem('token', data.data.token);
})
```

### Get Posts
```javascript
const token = localStorage.getItem('token');

fetch('http://localhost:8000/api/posts', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data.data.posts))
```

### Create Post
```javascript
const token = localStorage.getItem('token');

fetch('http://localhost:8000/api/posts', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    title: 'My Post',
    description: 'Post description'
  })
})
.then(response => response.json())
.then(data => console.log(data.data.post))
```

---

## Python Examples

### Using requests library
```python
import requests

BASE_URL = 'http://localhost:8000/api'

# Register
response = requests.post(f'{BASE_URL}/register', json={
    'name': 'John Doe',
    'email': 'john@example.com',
    'password': 'password123'
})
data = response.json()
token = data['data']['token']

# Get Posts
headers = {'Authorization': f'Bearer {token}'}
response = requests.get(f'{BASE_URL}/posts', headers=headers)
posts = response.json()['data']['posts']
print(posts)

# Create Post
post_data = {
    'title': 'My Post',
    'description': 'Post description'
}
response = requests.post(f'{BASE_URL}/posts', json=post_data, headers=headers)
print(response.json())
```

---

## Postman Collection JSON

Save this as `postman_collection.json` and import into Postman:

```json
{
  "info": {
    "name": "Laravel 12 API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Authentication",
      "item": [
        {
          "name": "Register",
          "request": {
            "method": "POST",
            "url": "{{base_url}}/api/register",
            "header": [
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\"name\": \"John Doe\", \"email\": \"john@example.com\", \"password\": \"password123\"}"
            }
          }
        },
        {
          "name": "Login",
          "request": {
            "method": "POST",
            "url": "{{base_url}}/api/login",
            "header": [
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\"email\": \"john@example.com\", \"password\": \"password123\"}"
            }
          }
        }
      ]
    }
  ]
}
```
