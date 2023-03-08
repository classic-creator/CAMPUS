<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;
class UserController extends Controller
{
    //register
public function register(Request $request){

    $validator= Validator::make($request->all(),[
        'name'=>'required',
        'email'=>'required|email|unique:users',
        'password'=>'required',
        'confirm_password'=>'required|same:password',
        // 'type'=>'numeric'
   ]);
   if ($validator->fails()) {
       $response=[
           'success'=>false,
           'message'=>$validator->errors()
       ];
       return response()->json($response,400);
   } 

    $user = User::create([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=>Hash::make($request->password),
        'type'=>$request->input('type', '0')

        
    ]);
    $token = $user->createToken($request->email)->plainTextToken;

    $response=[
        'success'=>true,
        'message'=>"registration success",
        'token'=>$token,
        'name'=>$user['name']
    ];
    return response()->json($response,201);
   
}
//user login
public function login(Request $request){

    $validator=Validator::make($request->all(),[
        'email'=>'required|email',
        'password'=>'required',
    ]);
    if($validator->fails()){
        $response=[
            'success'=>false,
            'message'=>$validator->errors()
        ];
        return response()->json($response,400);
    }
    $user = User::where('email', $request->email)->first();

    if($user && Hash::check($request->password, $user->password)){
        $token = $user->createToken($request->email)->plainTextToken;
        return response([
            'token'=>$token,
            'message' => 'Login Success',
            'status'=>'success',
            'type'=>$user['type']
        ], 200);
    }
     $response=[
        'success'=>false,
        'message' => 'The Provided Credentials are incorrect',
    ];
    return response()->json($response,404);
}
//logout user
public function logout(){

    auth()->user()->tokens()->delete();
    $response=[
        'success'=>true,
        'message' => 'logout successfully',

    ];
    return response()->json($response,200);

}

//get profile details

public function getProfile(){

    $user=auth()->user();

    $response=[
        'success'=>true,
        'user'=>$user,
    
    ];
    return response()->json($response,200);

}
//change password
public function changePassword(Request $request){

    $validator=Validator::make($request->all(),[
      
        'password'=>'required',
        'confirm_password'=>'required|same:password'
    ]);
    if($validator->fails()){
        $response=[
            'success'=>false,
            'message'=>$validator->errors()
        ];
        return response()->json($response,400);
    };

      $user=auth()->user();
      $user->password=Hash::make($request->password);
      $user->save();

      $response=[
        'success'=>true,
        'user'=>$user,
    
    ];
    return response()->json($response,200);
}

}
