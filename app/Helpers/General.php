<?php

if (!function_exists('apiResponse')) {
    function apiResponse($data, $message, $status_code)
    {
        $resource = [
            'data' => $data,
            'message' => $message,
            'status' => in_array($status_code, successCode()),
        ];
        
        return response()->json($resource, $status_code);
    }
}

if (!function_exists('successCode')) {
    function successCode(): array
    {
        return [200, 201, 202];
    }
}
