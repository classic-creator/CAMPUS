<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use Carbon\Carbon;      
use Validator;
class PasswordResetController extends Controller
{
    // forget password
    public function forgetPassword(Request $request){

        
        $validator=Validator::make($request->all(),[
            'forgetEmail'=>'required|email'
        ]);
      
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()

            ];
            return response()->json($response, 200);
        }
        
        $email=$request->forgetEmail;
        // check user exist or not
        $user=User::where('email',$email)->first();

        if(!$user){
            $response=[
                'success'=>false,
                'message'=>'user not found'
            ];
            return response()->json($response,200);
        }

        //generate token 
        $token=Str::random(60);
        //save data to password reset table
        PasswordReset::create([
            'email'=>$email,
            'token'=>$token,
            'created_at'=>Carbon::now()
        ]);

        // dump("http://127.0.0.1:3000/api/reset/".$token);
        // sending email for reset email 
      

        //    $host = request()->getHost();
      

        Mail::send('reset',['token'=>$token],function(Message $message)use($email){
            $message->subject('Reset your password');
            $message->to($email);

         
        });

        $response=[
            'success'=>true,
            'message'=>"email sent successfully ... check your mail"
        ];
        return response()->json($response,200);

    }
//reset password with mail
    public function resetPassword(Request $request ,$token){

        $formated=Carbon::now()->subMinutes(15)->toDateString();
        PasswordReset::where('created_at','<=',$formated)->delete();

        $validator=Validator::make($request->all(),[
            'password'=>'required',
            'confirm_password'=>'required|same:password'
        ]);
        if($validator->fails()){
            $response=[
                'success'=>false,
                'message'=>$validator->errors()
            ];
            return response()->json($response,200);
        };

        $passwordreset=PasswordReset::where('token',$token)->first();

        if(!$passwordreset){
            $response=[
                'success'=>false,
                'message'=>'user not found'
            ];
            return response()->json($response,200);
        }

        $user=User::where('email',$passwordreset->email)->first();
        $user->password=Hash::make($request->password);
        $user->save();
        
        PasswordReset::where('email',$user->email)->delete();

        $response=[
            'success'=>true,
            'message'=>'Password reset success'
        ];
        return response()->json($response,200);


    }
}
