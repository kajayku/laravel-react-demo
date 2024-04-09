<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class AuthController extends Controller
{
   public function __construct()
   {
    $this->middleware('auth:api',['except' => ['login','register']]);
   }


   public function login(Request $request){
    $validator = Validator::make($request->all(),[
        'email' => 'required',
        'password' => 'required',
    ]);

    if($validator->fails()){
        return response()->json($validator->errors()->toJson(),422);
    }
    if(! $token = auth()->attempt($validator->validated())){
        return response()->json(['error'=>'Unauthorized']);
    }

    return $this->createNewToken($token);
   }

public function register(Request $request){
    $validator = Validator::make($request->all(),[
        'name' => 'required',
        'email' => 'required',
        'password' => 'required',
    ]);

    if($validator->fails()){
        return response()->json($validator->errors()->toJson(),400);
    }
    $user = User::create(array_merge(
        $validator->validated(),
        ['password'=> bcrypt($request->password)]
    ));

    return response()->json([
        'message'=>'User successfully register',
        'user' => $user
    ], 201);
}


protected function createNewToken($token){
    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory()->getTTL()*60,
        'user' => auth()->user()
    ]);
}


}
