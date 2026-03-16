<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{

public function register(Request $request)
{
    $user = User::create([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=>Hash::make($request->password)
    ]);

    return response()->json([
        "message"=>"User registered successfully"
    ]);
}

public function login(Request $request){
    if(!Auth::attempt($request->only('email','password')))
        {
        return response()->json(
            [ "message"=>"Invalid Credentials" ],401
            );
        }
    $user = Auth::user();
    $user->tokens()->delete();
    $token = $user-> createToken('api-token')->plainTextToken;
    return response()->json([
        "token"=>$token
    ]);
}

public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete(); //current device

    return response()->json([
        "message" => "Logged out successfully"
    ]);
}

}
