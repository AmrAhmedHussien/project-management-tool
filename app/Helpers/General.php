<?php

use App\Models\ProjectApproveChain;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

if (!function_exists('checkIfUserCanApproveProject')) {
    function checkIfUserCanApproveProject($project_id)
    {
        $projectApproveChain = ProjectApproveChain::query()->where('project_id', $project_id)->where('user_id', Auth::id())->first();
            if ($projectApproveChain && $projectApproveChain->nextUser()?->id == Auth::id())
                return true;
            else
                return false;
    }
}

if (!function_exists('getAvailableUsersToAddToChain')) {
    function getAvailableUsersToAddToChain($project_id)
    {
        $added_users = ProjectApproveChain::query()->where('project_id',$project_id)->select('user_id')->pluck('user_id')->toArray();
        return User::query()->whereNotIn('id',$added_users)->select('id','name')->pluck('name', 'id');
    }
}