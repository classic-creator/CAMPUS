<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StudentPersonalDetails;
use App\Models\Address;
use App\Models\StudentEducationalDetails;
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
        'user_role'=>'required',
        'gender'=>'required',
        'phon_no'=>'required',
        'confirm_password'=>'required|same:password',
        // 'type'=>'numeric'
   ]);
   if ($validator->fails()) {
       $response=[
           'success'=>false,
           'message'=>$validator->errors()->first()
       ];
       return response()->json($response,400);
   } 


   $image_path = $request->file('profile')->store('user_profile');

    $user = User::create([
        'name'=>$request->name,
        'email'=>$request->email,
        'user_role'=>$request->user_role,
        'gender'=>$request->gender,
        'phon_no'=>$request->phon_no,
        'profile'=>$image_path ,
        'password'=>Hash::make($request->password),
        'type'=>$request->input('type', '0')

        
    ]);
    $token = $user->createToken($request->email)->plainTextToken;
    $user->image_url = $user->profile ? url($user->profile) : null;
    $response=[
        'success'=>true,
        'message'=>"registration success",
        'token'=>$token,
        'user'=>$user
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
        $user->image_url = $user->profile ? url($user->profile) : null;
        
        return response([
            'message' => 'Login Success',
            'status'=>'success',
            'user'=>$user,
            'token'=>$token
        ], 200);
    }
     $response=[
        'success'=>false,
        'message' => 'The Provided Credentials are incorrect',
    ];
    return response()->json($response,401);
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
    $user->image_url = $user->profile ? url($user->profile) : null;

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

//get all users --admin
public function getAllUsersAdmin(Request $request){

    $users=User::all();
    $usersCount = $users->count();

    foreach ($users as $photo) {
        $photo->image_url = $photo->profile ? url($photo->profile) : null;
    }

    $response = [
        'success' => true,
        'Total_users' => $usersCount,
        'users' => $users,
    ];
    return response()->json($response, 200);
}

//update users to admin

public function updateUsersAdmin(Request $request,$id){

    $user=User::where('id',$id)->first();

    $validator=Validator::make($request->all(),[
      
        'type'=>'required|numeric',
        
    ]);
    if($validator->fails()){
        $response=[
            'success'=>false,
            'message'=>$validator->errors()
        ];
        return response()->json($response,400);
    };

      
      $user->type=$request->type;
      $user->save();

      $response=[
        'success'=>true,
        'user'=>$user,
    
    ];
    return response()->json($response,200);

}


//update user profile

public function updateProfile(Request $request){

    $validator=Validator::make($request->all(),[
      
        'name'=>'required',
        // 'confirm_password'=>'required|same:password'
    ]);
    if($validator->fails()){
        $response=[
            'success'=>false,
            'message'=>$validator->errors()
        ];
        return response()->json($response,400);
    };
    $user=auth()->user();
    if($request->file('profile')){
    $image_path = $request->file('profile')->store('user_profile');
    $user->name=$request->name;
    $user->profile=$image_path;
    $user->save();


  $user->image_url = $user->profile ? url($user->profile) : null;
    }else{
        $user->name=$request->name;
     
        $user->save();
    }



      $response=[
        'success'=>true,
        'user'=>$user,
    
    ];
    return response()->json($response,200);
}








}
