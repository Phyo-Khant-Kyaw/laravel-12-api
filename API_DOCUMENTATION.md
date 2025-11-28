# Laravel 12 API Documentation

A complete REST API built with Laravel 12 and Sanctum token-based authentication with role-based access control.

## Features

- ✅ User authentication with Sanctum tokens
- ✅ Role-based access control (Admin & User roles)
- ✅ Ability-based authorization at route level
- ✅ CRUD operations for Users and Posts
- ✅ Swagger UI API documentation
- ✅ Exception handling with JSON responses
- ✅ Input validation and error messages

## Installation

### Prerequisites
- PHP 8.3+
- Composer
- MySQL/MariaDB
- Node.js (optional, for frontend)

### Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd laravel-12-api
```

2. **Install dependencies**
```bash
composer install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
Edit `.env` file with your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_api
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Generate Swagger documentation**
```bash
php artisan l5-swagger:generate
```

7. **Start the development server**
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## API Documentation

### Accessing Swagger UI
Navigate to: `http://localhost:8000/api/documentation`

### Authentication

All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer <your-token>
```

## Endpoints

### Authentication (Public)

#### Register
- **POST** `/api/register`
- **Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}
```
- **Response:** User object with API token

#### Login
- **POST** `/api/login`
- **Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```
- **Response:** User object with API token

#### Get Current User
- **GET** `/api/me`
- **Auth:** Required
- **Response:** Current authenticated user profile

---

### Posts (Authenticated Users)

#### Get All Posts
- **GET** `/api/posts`
- **Auth:** Required (ability: `view-posts`)
- **Response:** Array of posts with user data

#### Create Post
- **POST** `/api/posts`
- **Auth:** Required (ability: `create-posts`)
- **Body:**
```json
{
  "title": "Post Title",
  "description": "Post description"
}
```
- **Response:** Created post object

#### Get Post by ID
- **GET** `/api/posts/{id}`
- **Auth:** Required (ability: `view-posts`)
- **Response:** Post object with user data

#### Update Post
- **PUT** `/api/posts/{id}`
- **Auth:** Required (ability: `update-posts`)
- **Note:** Only post owner can update
- **Body:**
```json
{
  "title": "Updated Title",
  "description": "Updated description"
}
```
- **Response:** Updated post object

#### Delete Post
- **DELETE** `/api/posts/{id}`
- **Auth:** Required (ability: `delete-posts`)
- **Note:** Only post owner can delete
- **Response:** Success message

---

### Users (Admin Only)

#### Get All Users
- **GET** `/api/users`
- **Auth:** Required (ability: `view-users`)
- **Response:** Array of users

#### Create User
- **POST** `/api/users`
- **Auth:** Required (ability: `create-users`)
- **Body:**
```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "password123",
  "role": "user"
}
```
- **Response:** Created user object

#### Get User by ID
- **GET** `/api/users/{id}`
- **Auth:** Required (ability: `view-users`)
- **Response:** User object

#### Update User
- **PUT** `/api/users/{id}`
- **Auth:** Required (ability: `update-users`)
- **Body:**
```json
{
  "name": "Updated Name",
  "email": "updated@example.com",
  "role": "admin"
}
```
- **Response:** Updated user object

#### Delete User
- **DELETE** `/api/users/{id}`
- **Auth:** Required (ability: `delete-users`)
- **Response:** Success message

---

## Authorization

### Roles & Abilities

#### Admin Role
- `view-users` - View all users
- `create-users` - Create new users
- `update-users` - Update user information
- `delete-users` - Delete users
- `view-posts` - View all posts
- `create-posts` - Create posts
- `update-posts` - Update own posts
- `delete-posts` - Delete own posts

#### User Role
- `view-posts` - View all posts
- `create-posts` - Create posts
- `update-posts` - Update own posts
- `delete-posts` - Delete own posts

### How Authorization Works

1. User registers or logs in
2. Token is created with abilities based on user's role
3. When accessing protected endpoints, Sanctum middleware checks if token has required ability
4. If ability is missing, returns 403 Forbidden
5. If successful, request proceeds to controller

## Error Handling

All API responses follow a consistent JSON format:

### Success Response
```json
{
  "status": true,
  "message": "Operation successful",
  "data": { }
}
```

### Error Response
```json
{
  "status": false,
  "message": "Error description"
}
```

### Validation Error Response
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

## HTTP Status Codes

- `200` - OK
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized (Missing or invalid token)
- `403` - Forbidden (Insufficient permissions)
- `404` - Not Found
- `422` - Unprocessable Entity (Validation error)
- `500` - Internal Server Error

## Testing with cURL

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

### Get Posts (with token)
```bash
curl -X GET http://localhost:8000/api/posts \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Create Post
```bash
curl -X POST http://localhost:8000/api/posts \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "My First Post",
    "description": "This is my first post"
  }'
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── AuthController.php
│   │   │   ├── PostController.php
│   │   │   ├── UserController.php
│   │   │   └── BaseController.php
│   │   └── Controller.php
│   ├── Middleware/
│   │   └── AdminMiddleware.php
├── Models/
│   ├── User.php
│   └── Post.php
├── ApiResponse.php
├── Providers/
│   └── AppServiceProvider.php
└── Exceptions/
    └── ApiExceptionHandler.php

routes/
├── api.php (API routes with Sanctum middleware)
└── web.php (Web routes, includes Swagger UI)

database/
├── migrations/
│   ├── create_users_table.php
│   ├── create_posts_table.php
│   ├── add_role_to_users_table.php
│   └── ...
└── factories/
    └── UserFactory.php
```

## Database Schema

### Users Table
```sql
id (Primary Key)
name
email (Unique)
password (Hashed)
role (enum: 'user', 'admin') - Default: 'user'
created_at
updated_at
```

### Posts Table
```sql
id (Primary Key)
user_id (Foreign Key to users)
title
description
created_at
updated_at
```

### Personal Access Tokens Table
```sql
id
tokenable_type
tokenable_id
name
token (Hashed)
abilities (JSON array of abilities)
last_used_at
expires_at
created_at
updated_at
```

## Development Tips

### Create an Admin User
```bash
php artisan tinker
>>> $user = App\Models\User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password'), 'role' => 'admin']);
>>> exit
```

### Regenerate Swagger Documentation
```bash
php artisan l5-swagger:generate
```

### Clear Application Cache
```bash
php artisan cache:clear
```

### Run Tests
```bash
php artisan test
```

## Security Considerations

- ✅ Passwords are hashed using bcrypt
- ✅ API tokens are hashed in database
- ✅ Role-based access control at route level
- ✅ Ability-based authorization for granular control
- ✅ Exception handling prevents data leakage
- ✅ CORS can be configured if needed
- ✅ Rate limiting can be implemented

## License

This project is open-source software licensed under the MIT license.

## Support

For issues and questions, please create an issue in the repository.
