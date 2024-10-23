<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Get the authenticated user
        $user = Auth::guard('api')->user();

        // Revoke the user's current access token
        if ($user) {
            $user->tokens()->where('id', $request->user()->token()->id)->delete();

            return response()->json(['message' => 'Logged out successfully.'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
    }
}
