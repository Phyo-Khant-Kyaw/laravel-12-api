# Swagger UI Setup - Complete

## What's Been Installed

✅ **L5 Swagger Package** - Swagger PHP and Swagger UI integration for Laravel
✅ **OpenAPI Annotations** - Added to all API controllers
✅ **Swagger Configuration** - Config file at `config/swagger.php`
✅ **Documentation Generated** - JSON documentation at `storage/api-docs/api-docs.json`

## Accessing Swagger UI

### Local Development
```
http://localhost:8000/api/documentation
```

Simply navigate to this URL in your browser to see the interactive API documentation.

## Features Available in Swagger UI

✅ View all API endpoints
✅ See request/response examples
✅ Try out endpoints directly from the browser
✅ Test authentication with Bearer tokens
✅ View detailed parameter and response schemas
✅ Filter endpoints by tag (Authentication, Posts, Users)

## Regenerating Documentation

When you make changes to the API (add new endpoints, update descriptions, etc.), regenerate the documentation:

```bash
php artisan l5-swagger:generate
```

## Swagger Configuration

Edit `config/swagger.php` to customize:
- API title and description
- API version
- Base path
- Security schemes
- And more

## Example: Trying an Endpoint in Swagger UI

1. Navigate to `http://localhost:8000/api/documentation`
2. Click on **Authentication** section to expand
3. Click on **POST /login**
4. Click the **"Try it out"** button
5. Enter your credentials in the request body
6. Click **Execute**
7. View the response with status code and token

## Adding Authorization to Requests

For protected endpoints:

1. Expand any endpoint that requires authentication
2. Look for a **lock icon** or **"Authorization"** section
3. Click **"Try it out"**
4. Click the **Authorize** button (top right)
5. Paste your Bearer token
6. Click **Authorize**
7. Now all requests will include the token

## Customizing Swagger Documentation

### Example: Adding a custom endpoint annotation

```php
/**
 * @OA\Get(
 *     path="/my-endpoint",
 *     tags={"MyTag"},
 *     summary="Short description",
 *     description="Longer description of what this does",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Item ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success response",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=404, description="Not found"),
 * )
 */
public function myEndpoint($id)
{
    // Implementation
}
```

## Security Scheme

The API uses Bearer token authentication. Swagger is configured with:

```php
@OA\SecurityScheme(
    type="http",
    scheme="bearer",
    bearerFormat="token",
    name="token",
    in="header",
    securityScheme="bearerAuth",
)
```

This allows testing authenticated endpoints directly in Swagger UI.

## Files Structure

```
project/
├── config/
│   └── swagger.php              # Swagger configuration
├── app/Http/Controllers/Api/
│   ├── AuthController.php       # With @OA\Post, @OA\Get annotations
│   ├── PostController.php       # With @OA\Get, @OA\Post, @OA\Put, @OA\Delete annotations
│   └── UserController.php       # With @OA\Get, @OA\Post, @OA\Put, @OA\Delete annotations
├── storage/
│   └── api-docs/
│       └── api-docs.json        # Generated Swagger documentation
└── routes/
    └── web.php                  # Swagger UI route at /api/documentation
```

## Troubleshooting

### Documentation not showing up?
1. Regenerate: `php artisan l5-swagger:generate`
2. Clear cache: `php artisan cache:clear`
3. Make sure `storage/api-docs/` directory exists and is writable

### Bearer token not working in Swagger?
1. Go to top-right and click **Authorize**
2. Paste your token without "Bearer " prefix (just the token)
3. Click **Authorize**
4. Close the modal and try an endpoint

### Swagger UI returns 404?
1. Make sure routes are registered in `routes/web.php`
2. Check Laravel is running: `php artisan serve`
3. Clear routes cache: `php artisan route:clear`

## Next Steps

1. **Test the API** - Go to `http://localhost:8000/api/documentation` and test endpoints
2. **Generate tokens** - Register or login to get a token
3. **Try authenticated endpoints** - Use the token to access protected endpoints
4. **Share with team** - The Swagger UI can be shared with your team for testing

## Additional Resources

- [Swagger UI Documentation](https://swagger.io/tools/swagger-ui/)
- [OpenAPI 3.0 Specification](https://spec.openapis.org/oas/v3.0.3)
- [L5 Swagger Laravel Package](https://github.com/DarkaOnline/L5-Swagger)

---

**Swagger UI is now ready to use!** Navigate to `http://localhost:8000/api/documentation` to get started.
