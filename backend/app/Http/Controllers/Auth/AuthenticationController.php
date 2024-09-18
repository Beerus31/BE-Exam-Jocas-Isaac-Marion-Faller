<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        $login = $request->input('usernameOrEmail');
        $password = $request->input('password');

        // Check if the input is an email or username
        $credentials = filter_var($login, FILTER_VALIDATE_EMAIL) 
                        ? ['email' => $login, 'password' => $password] 
                        : ['username' => $login, 'password' => $password];

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token
            ]);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout successful']);
    }
}

