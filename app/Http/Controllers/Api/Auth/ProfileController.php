<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            return (new UserResource($user))->additional([
                'message' => __('user data retrieved successfully'),

            ])->response()->setStatusCode(Response::HTTP_OK);


        }
        return response()->json(['message' => 'User not authenticated'], 401);
    }
}
