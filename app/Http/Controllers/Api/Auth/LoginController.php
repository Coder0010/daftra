<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            $user = Auth::user();
            $token = $user->createToken('LaravelPassportToken')->accessToken;

            return (new UserResource($user))->additional(['token' => $token])->response()->setStatusCode(Response::HTTP_CREATED);
        } else {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
    }
}
