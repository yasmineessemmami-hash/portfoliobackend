<?php

if (!function_exists('successResponse')) {
    function successResponse($data = null, $message = 'Success', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse($message = 'Error', $status = 400, $errors = "UNKNOWN ERROR")
    {

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
