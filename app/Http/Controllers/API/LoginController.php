<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LoginController extends Controller
{
    public function login(Request $req)
    {
        $loginData = $req->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if(!$loginData) {
            return response()->json(['message' => 'Некорректный ввод', 'errors' => $loginData->errors(), 'code' => 1], 422);
        }

        if (!$token = auth('api')->attempt($loginData)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        return response()->json([
            'token' => $token,
            'type' => 'bearer',
            'expires' => auth('api')->factory()->getTTL() * 60, // time to expiration

        ], 200);
    }
}
