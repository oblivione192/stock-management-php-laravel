<?php

namespace App\Http\Controllers\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        try{
            $user = $request->user();

            if (! $user) {
                return response()->json([
                    'message' => 'Unauthenticated',
                ], 401);
            }

            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }
        catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

    }
}
