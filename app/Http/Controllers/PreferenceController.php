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

        'college_preference_1' => 'required',
        'depertment_preference_1' => 'required',
        'address_preference_1' => 'required',
    ]);
    if ($validator->fails()) {
        $response = [
            'success' => false,
            'message' => $validator->errors()

        ];
        return response()->json($response, 404);
    }

      $preference=Preference::create([
          'student_id'=>$user['id'],
        'college_preference_1'=>$request->college_preference_1,
        'college_preference_2'=>$request->college_preference_2,
        'college_preference_3'=>$request->college_preference_3,
        'course_preference_1'=>$request->course_preference_1,
        'course_preference_2'=>$request->course_preference_2,
        'course_preference_3'=>$request->course_preference_3,
        'depertment_preference_1'=>$request->depertment_preference_1,
        'depertment_preference_2'=>$request->depertment_preference_2,
        'depertment_preference_3'=>$request->depertment_preference_3,
        'address_preference_1'=>$request->address_preference_1,
        'address_preference_2'=>$request->address_preference_2,
        'address_preference_3'=>$request->address_preference_3,
      ]);

      $response = [
        'success' => true,
        'message' => "Preference add success",    
        'preference'=>$preference 
    ];
    return response()->json($response, 201);

    }
}
