<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *    title="Laravel 12 API",
 *    version="1.0.0",
 *    description="API documentation with Sanctum authorization",
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="token",
 *     name="token",
 *     in="header",
 *     securityScheme="bearerAuth",
 * )
 */
class AuthController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Create a new user account",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User registration data",
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="token", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *     ),
     * )
     */
    public function register()
    {
        $data = request()->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $abilities = ['view-posts', 'create-posts', 'update-posts', 'delete-posts'];

        $token = $user->createToken('api-token', $abilities)->plainTextToken;

        return $this->success([
            'user'  => $user,
            'token' => $token,
        ], 'User registered successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="User login",
     *     description="Authenticate user and return API token",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Login credentials",
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User login successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User login successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="token", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *     ),
     * )
     */
    // LOGIN
    public function login()
    {
        $data = request()->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($data)) {
            return $this->error('Invalid credentials', 401);
        }
        $user = Auth::user();

        $abilities = $user->isAdmin()
            ? ['view-users', 'create-users', 'update-users', 'delete-users', 'view-posts', 'create-posts', 'update-posts', 'delete-posts']
            : ['view-posts', 'create-posts', 'update-posts', 'delete-posts'];

        $token = $user->createToken('api-token', $abilities)->plainTextToken;
        return $this->success([
            'user'  => $user,
            'token' => $token,
        ], 'User login successfully');
    }

    /**
     * @OA\Get(
     *     path="/me",
     *     tags={"Authentication"},
     *     summary="Get current user profile",
     *     description="Get the authenticated user's profile information",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User profile retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     * )
     */
    public function me()
    {
        $user = Auth::user();

        if (!$user) {
            return $this->error('Unauthorized', 401);
        }

        return $this->success([
            'user' => $user
        ], 'User profile retrieved successfully');
    }
}
