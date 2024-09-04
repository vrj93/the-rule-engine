<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse {
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ], [
            'username.required' => 'The username field is required.',
            'password.required' => 'The password field is required.',
        ]);

        $url = env('API_LOGIN');

        try {
            $response = Http::asForm()->post($url, [
                '_username' => $request->input('username'),
                '_password' => $request->input('password'),
            ]);
        } catch (Exception $ex) {
            return response()->json(['msg' => $ex->getMessage()], 500);
        }

        if ($response->successful()) {
            return response()->json([
                'msg' => 'Authenticated!',
                'auth-token' => $response->json(),
            ], $response->status());
        }

        return response()->json(['msg' => 'Unauthenticated!'], $response->status());
    }
}
