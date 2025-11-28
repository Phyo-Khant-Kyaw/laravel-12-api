<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;

class ApiExceptionHandler
{
    public function __invoke(Throwable $e)
    {
        // Validation Error
        if ($e instanceof ValidationException) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        // Authentication Error
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Authorization Error
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Sanctum Invalid Ability Error
        if ($e instanceof \BadMethodCallException && str_contains($e->getMessage(), 'Invalid ability')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Not Found (Model)
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'status' => false,
                'message' => 'Resource not found',
            ], 404);
        }

        // Fallback 500 error
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized',
        ], 500);
    }
}
