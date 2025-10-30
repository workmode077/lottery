<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

trait ResponseHandlerTrait
{
    /**
     * Success JSON response
     */
    protected function successResponse(
        $data = null,
        string $message = 'Success',
        int $status = 200,
        array $meta = []
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge($this->baseMeta(), $meta)
        ], $status);
    }

    /**
     * Error JSON response
     */
    protected function errorResponse(
        string $message = 'An error occurred',
        int $status = 400,
        array $errors = [],
        array $meta = []
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => $message,
            'errors' => $errors,
            'meta' => array_merge($this->baseMeta(), $meta)
        ], $status);
    }

    /**
     * Paginated JSON response
     */
    protected function paginatedResponse(
        $paginatedData,
        string $message = 'Data retrieved successfully',
        array $meta = []
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => $message,
            'data' => $paginatedData->items(),
            'pagination' => [
                'total' => $paginatedData->total(),
                'per_page' => $paginatedData->perPage(),
                'current_page' => $paginatedData->currentPage(),
                'last_page' => $paginatedData->lastPage(),
                'from' => $paginatedData->firstItem(),
                'to' => $paginatedData->lastItem()
            ],
            'meta' => array_merge($this->baseMeta(), $meta)
        ]);
    }

    /**
     * Empty/No content response
     */
    protected function emptyResponse(
        string $message = 'No content',
        array $meta = []
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'status' => 204,
            'message' => $message,
            'data' => null,
            'meta' => array_merge($this->baseMeta(), $meta)
        ], 204);
    }

    /**
     * Base meta information for responses
     */
    private function baseMeta(): array
    {
        return [
            'api_version' => config('app.api_version', 'v1'),
            'request_id' => Str::uuid(),
            'response_time' => round(microtime(true) - LARAVEL_START, 4),
            'timestamp' => now()->toISOString()
        ];
    }
}