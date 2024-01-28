<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'max:255', 'email', 'unique:users'],
                'password' => ['required', 'string']
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'User'
            ]);

            $user = User::where('email', $request->email)->first();
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch (Exception $error) {

            if ($error->getMessage() == 'The email has already been taken.') {
                return ResponseFormatter::error([
                    'message' => 'ada yang eror',
                    'error' => $error->getMessage(),
                ], 'Email telah digunakan', 444);
            } else if ($error->getMessage() == 'The email field must be a valid email address.') {
                return ResponseFormatter::error([
                    'message' => 'ada yang eror',
                    'error' => $error->getMessage(),
                ], 'format email tidak sesuai', 401);
            } else {
                return ResponseFormatter::error([
                    'message' => 'ada yang eror',
                    'error' => $error->getMessage(),
                ], 'Terjadi Kesalahan sistem', 500);
            }
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate(['email' => 'email|required', 'password' => 'required']);

            $credentials = request(['email', 'password']);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return ResponseFormatter::error(['message' => 'Unauthorized'], 'Email not found', 404);
            }

            if (!Hash::check($request->password, $user->password)) {
                return ResponseFormatter::error(['message' => 'Unauthorized'], 'Invalid password', 401);
            }

            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error(['message' => 'Unauthorized'], 'Authentacition Failed', 500);
            }

            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch (Exception $error) {
            return ResponseFormatter::error(['message' => 'Something went wrong', 'error' => $error->getMessage()], 'Authentacition Failed', 500);
        }
    }

    public function fetch(Request $request)
    {
        return ResponseFormatter::success($request->user(), 'Data Profile User');
    }
}
