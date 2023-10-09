<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
class ApiController extends Controller
{
    //
    /**
     * Summary of register
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function register(Request $request){
      $validator = Validator::make($request->all(),[
        'name'=>'required|string|min:3|max:100',
        'email'=>'required|string|email|max:100|unique:users',
        'password'=>'required|string|min:6|confirmed',
      ]);
        
      if($validator->fails()){
        return response()->json($validator->errors(),400);
      }

      User::create([
        'name'=> $request->name,
        'email'=> $request->email,
        'password'=> Hash::make($request->password),
      ]);


      return response()->json([
        "status"=>true,
        'message'=> 'User Registered Successfully'

      ]);
    }

    public function login(Request $request){
        $request-> validate([
            'email'=>'required|string|email',
            'password'=>'required|string|min:6',
          ]);


          //JWTAuth
          $token = JWTAuth::attempt([
          "email"=> $request->email,
          "password"=> $request->password
        ]);

        if(!empty($token)){
            return response()->json([
                "status"=> true,
                "message"=> "User logged in successfully",
                "token"=>$token
            ]);
        }
        return response()->json([
            "status" => false,
            "message" => "Invalid details"
        ]);
    }

    public function profile(){
        return response()->json(auth()->user());
    }

    public function refreshToken()
    {
        
        $token = JWTAuth::getToken();
    
        try {
            $newToken = JWTAuth::refresh($token);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        }
    
        return response()->json(['access_token' => $newToken]);
    }

public function logout(){
    auth()->logout();

    return response()-> json('message => User Logout Successfully '); 
}

}