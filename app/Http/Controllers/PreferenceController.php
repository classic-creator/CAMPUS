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
    $user = $request->user();
    if (!$user) {
      $response = [
        'success' => false,
        'message' => 'Please login'

      ];
      return response()->json($response, 200);
    }

    $preference = Preference::where('student_id', $user['id'])->first();

    if ($preference) {
      $response = [
        'success' => false,
        'message' => 'Your preference add sucessfully you can update your preferences'
      ];
      return response()->json($response, 302);
    }

    $validator = Validator::make($request->all(), [

      // 'college1' => 'required',
      // 'course1' => 'required',
      // 'address' => 'required',
    ]);
    if ($validator->fails()) {
      $response = [
        'success' => false,
        'message' => $validator->errors()

      ];
      return response()->json($response, 200);
    }

    $preference = Preference::create([
      'student_id' => $user['id'],
      'college1' => $request->college1,

      'course1' => $request->course1,

      'depertment1' => $request->depertment1,



    ]);

    $response = [
      'success' => true,
      'message' => "Preference add success",
      'preference' => $preference
    ];
    return response()->json($response, 201);

  }


  //update Preferences

  public function updatePreference(Request $request)
  {

    $user = $request->user();
    if (!$user) {
      $response = [
        'success' => false,
        'message' => 'Please login'

      ];
      return response()->json($response, 200);
    }

    $preference = Preference::where('student_id', $user['id'])->first();

    if (!$preference) {
      $response = [
        'success' => false,
        'message' => 'Please add preference'
      ];
      return response()->json($response, 200);
    }


    $validator = Validator::make($request->all(), [

      // 'college1' => 'required',
      // 'course1' => 'required',
      // 'depertment1' => 'required',
    ]);
    if ($validator->fails()) {
      $response = [
        'success' => false,
        'message' => $validator->errors()
      ];
      return response()->json($response, 400);
    }
    ;

    $college1 = $request->input('college1') ?: null;
    $course1 = $request->input('course1') ?: null;
    $depertment1 = $request->input('depertment1') ?: null;
    $preference->update([
      'college1' => $college1,

      'course1' => $course1,

      'depertment1' => $depertment1,

      // 'address' => $request->input('address'),

    ]);
    $preference->save();

    $response = [
      'success' => true,
      'message' => 'update Preference successfully',


    ];
    return response()->json($response, 200);
  }


  //get preferences

  public function getPreferences(Request $request)
  {

    $user = $request->user();

    $preference = Preference::where('student_id', $user['id'])->first();

    if (!$preference) {

      $response = [
        'success' => false,
        'message' => 'please add preferences'
      ];
      return response()->json($response, 200);
    }

    $response = [
      'success' => true,
      'preference' => $preference
    ];

    return response()->json($response, 200);
  }

//delete preferences


}