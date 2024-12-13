<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try{
            $user = User::query()->where('email',$request->email)->first();
            $password_check = Hash::check($request->password, $user->password);
            if($user && $password_check)
            {
                Auth::login($user);
                $accessToken = $user->createToken('user_access_token');
                $data = [
                    'user' => new UserResource($user),
                    'access_token' => $accessToken->plainTextToken
                ];
                return apiResponse($data,'User Logged In Successfully',200);
            }
            else{
                return apiResponse(null,'Incorrect Username Or Password',401);
            }
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return apiResponse(null,'Someting Went Wrong, Please Contact Support',400);
        }
    }
}
