<?php

namespace App\Http\Controllers;

use App\Models\Preference;
use Validator;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    //add preference 

    public function addPreference(Request $request)
    {
      $user=$request->user();

      $validator = Validator::make($request->all(), [

        'college1' => 'required',
        'course1' => 'required',
        'address' => 'required',
    ]);
    if ($validator->fails()) {
        $response = [
            'success' => false,
            'message' => $validator->errors()

        ];
        return response()->json($response, 200);
    }

      $preference=Preference::create([
        'student_id'=>$user['id'],
        'college1'=>$request->college1,
        'college2'=>$request->college2,
        'college3'=>$request->college3,
        'course1'=>$request->course1,
        'course2'=>$request->course2,
        'course3'=>$request->course3,
        'depertment1'=>$request->depertment1,
        'depertment2'=>$request->depertment2,
        'depertment3'=>$request->depertment3,
        'address'=>$request->address,
        
       
      ]);

      $response = [
        'success' => true,
        'message' => "Preference add success",    
        'preference'=>$preference 
    ];
    return response()->json($response, 201);

    }
}
