<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use DB;
use Illuminate\Http\Response;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password // it hashed using the cast of model
            ]);

            $token = $user->createToken('LaravelPassportToken')->accessToken;

            DB::commit();
            return (new UserResource($user))->additional(['token' => $token])->response()->setStatusCode(Response::HTTP_CREATED);


        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
