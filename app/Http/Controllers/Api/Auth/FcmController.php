<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmController extends Controller
{
    public function storeToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = auth()->user();

        FcmToken::firstOrCreate(
            ['user_id' => $user->id, 'token' => $request->fcm_token]
        );

        return response()->json([
            'status' => "success",
            'message' => 'Token saved'
        ]);
    }

    public function deleteToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = auth()->user();

        FcmToken::where('user_id', $user->id)
            ->where('token', $request->fcm_token)
            ->delete();

        return response()->json(['status' => "success", 'message' => 'Token deleted']);
    }

}
