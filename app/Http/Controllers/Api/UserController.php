<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="User management endpoints (Admin only)"
 * )
 */
class UserController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Get all users",
     *     description="Retrieve a list of all users (Admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Users retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Users retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="users", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden - Admin access required"),
     * )
     */
    public function index()
    {
        try {
            $users = User::all();
            return $this->success([
                'users' => $users
            ], 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     description="Create a new user (Admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User data",
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="string", enum={"user","admin"}, example="user"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden - Admin access required"),
     *     @OA\Response(response=422, description="Validation failed"),
     * )
     */
    public function store()
    {
        try {
            $data = request()->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'role'     => 'sometimes|in:user,admin'
            ]);

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => $data['role'] ?? 'user',
            ]);

            return $this->success([
                'user' => $user
            ], 'User created successfully', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Get user by ID",
     *     description="Retrieve a specific user by ID (Admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden - Admin access required"),
     *     @OA\Response(response=404, description="User not found"),
     * )
     */
    public function getById($id)
    {
        try {
            $user = User::findOrFail($id);
            return $this->success([
                'user' => $user
            ], 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('User not found', 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Update a user",
     *     description="Update a user's information (Admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated user data",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Jane Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
     *             @OA\Property(property="role", type="string", enum={"user","admin"}, example="user"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden - Admin access required"),
     *     @OA\Response(response=404, description="User not found"),
     * )
     */
    public function update($id)
    {
        try {
            $user = User::findOrFail($id);

            $data = request()->validate([
                'name'  => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'role'  => 'sometimes|in:user,admin'
            ]);

            $user->update($data);

            return $this->success([
                'user' => $user
            ], 'User updated successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Delete a user",
     *     description="Delete a user (Admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User deleted successfully"),
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden - Admin access required"),
     *     @OA\Response(response=404, description="User not found"),
     * )
     */
    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return $this->success([], 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->error('User not found', 404);
        }
    }
}
