<?php

namespace App\Http\Controllers\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function authenticateUser(Request $request): JsonResponse
    {  
        try{
            $credentials = $request->validate([ 
                'name' => ['required'],
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (! Auth::attempt($credentials)) {
                return new JsonResponse([
                    'message' => 'Invalid credentials',
                ], 401);
            }  

            $user = $request->user();
            $token = $user->createToken('inventory-api')->plainTextToken;


            return new JsonResponse([
                'message' => 'Logged in', 
                'token' => $token,
                'user' => $user,
            ]); 
        }
        catch(Exception $e){
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function registerUser(Request $request)
    {
        try{
            $validated = $request->validate([
                'name' => ['required'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'min:6'],
            ]); 
        }
        catch(Exception $e){
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 400);
        }

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return new JsonResponse([
                'message' => 'Registered successfully',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 500);
        }

    }
}
