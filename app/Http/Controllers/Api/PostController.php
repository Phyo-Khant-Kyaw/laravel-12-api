<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Posts",
 *     description="Post management endpoints"
 * )
 */
class PostController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     tags={"Posts"},
     *     summary="Get all posts",
     *     description="Retrieve a list of all posts",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Posts retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Posts retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="posts", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     * )
     */
    public function index()
    {
        try {
            $posts = Post::with('user')->get();
            return $this->success([
                'posts' => $posts
            ], 'Posts retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     tags={"Posts"},
     *     summary="Create a new post",
     *     description="Create a new post for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Post data",
     *         @OA\JsonContent(
     *             required={"title","description"},
     *             @OA\Property(property="title", type="string", example="Post Title"),
     *             @OA\Property(property="description", type="string", example="Post description"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="post", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation failed"),
     * )
     */
    public function store()
    {
        try {
            $data = request()->validate([
                'title'       => 'required|string|max:255',
                'description' => 'required|string'
            ]);

            $post = Post::create([
                'user_id'     => Auth::id(),
                'title'       => $data['title'],
                'description' => $data['description'],
            ]);

            $post->load('user');

            return $this->success([
                'post' => $post
            ], 'Post created successfully', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Get post by ID",
     *     description="Retrieve a specific post by its ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="post", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Post not found"),
     * )
     */
    public function getById($id)
    {
        try {
            $post = Post::with('user')->findOrFail($id);
            return $this->success([
                'post' => $post
            ], 'Post retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Post not found', 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Update a post",
     *     description="Update an existing post (only owner can update)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated post data",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Title"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="post", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden - not post owner"),
     *     @OA\Response(response=404, description="Post not found"),
     * )
     */
    public function update($id)
    {
        try {
            $post = Post::findOrFail($id);

            // Check if user is the owner of the post
            if ($post->user_id !== Auth::id()) {
                return $this->error('Unauthorized to update this post', 403);
            }

            $data = request()->validate([
                'title'       => 'sometimes|string|max:255',
                'description' => 'sometimes|string'
            ]);

            $post->update($data);
            $post->load('user');

            return $this->success([
                'post' => $post
            ], 'Post updated successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Delete a post",
     *     description="Delete a post (only owner can delete)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post deleted successfully"),
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden - not post owner"),
     *     @OA\Response(response=404, description="Post not found"),
     * )
     */
    public function delete($id)
    {
        try {
            $post = Post::findOrFail($id);

            // Check if user is the owner of the post
            if ($post->user_id !== Auth::id()) {
                return $this->error('Unauthorized to delete this post', 403);
            }

            $post->delete();

            return $this->success([], 'Post deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Post not found', 404);
        }
    }
}
