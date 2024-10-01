<?php

namespace App\Helpers;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class Helpers
{
    /**
     * Return a success response.
     *
     * @param array $data
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = [], $message = 'Success', $status = 200)
    {
        // Ensure the response is clean without additional fields
        return response()->json([
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Return an error response.
     *
     * @param string $message
     * @param array $errors
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($message = 'Error', $errors = [], $status = 400)
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
        ], $status, [], JSON_UNESCAPED_UNICODE);
    }
    public static function logError(Exception $e)
    {
        // Log to the database
        ErrorLog::create([
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        // Optionally log to a file
        Log::error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    }
}
