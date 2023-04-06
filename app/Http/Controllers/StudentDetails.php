<?php

namespace App\Http\Controllers;

use App\Models\StudentEducationalDetails;
use App\Models\StudentPersonalDetails;
use App\Models\Address;
use Illuminate\Http\Request;
use Validator;

class StudentDetails extends Controller
{
    //student personal details
    public function RegisterStudentPersonalDetails(Request $request)
    {


        $user = $request->user();

        $validator = Validator::make($request->all(), [

            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'dob' => 'required|date',
            'father_name' => 'required',
            'mother_name' => 'required',
            'phon_no' => 'required',
            'identification' => 'required',
            'identification_no' => 'required',
            'qualification' => 'required',
            'mark_obtain_lastExam' => 'required',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        ;


        StudentPersonalDetails::create([
            'student_id' => $user['id'],
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'dob' => $request->dob,
            'phon_no' => $request->phon_no,
            'identification' => $request->identification,
            'identification_no' => $request->identification_no,
            'qualification' => $request->qualification,
            'mark_obtain_lastExam' => $request->mark_obtain_lastExam
        ]);

        $response = [
            'success' => true,
            'message' => 'register Personal details Successfull'
        ];

        return response()->json($response, 201);


    }

    //get student personal data


    public function getStudentPersonalDetails(Request $request)
    {


        $user = $request->user();

        $studentPersonalData = StudentPersonalDetails::where('student_id', $user['id'])->latest('created_at')->first();

        if (!$studentPersonalData) {

            $response = [
                'success' => false,
                'message' => 'please enter details'
            ];

            return response()->json($response, 404);
        }


        $response = [
            'success' => true,
            'studentPersonalData' => $studentPersonalData
        ];

        return response()->json($response, 200);


    }


    //student Educational details
    public function registerStudentEducationalDetails(Request $request) {


        $user = $request->user();

        $validator = Validator::make($request->all(), [

            'class10_passingYear' => 'required',
            'class10_roll' => 'required',
            'class10_no' => 'required',
            'class10_board' => 'required',
            'class10_school' => 'required',
            'class10_totalMark' => 'required',
            'class10_markObtain' => 'required',

            'class12_passingYear' => 'required',
            'class12_roll' => 'required',
            'class12_no' => 'required',
            'class12_board' => 'required',
            'class12_college' => 'required',
            'class12_strem' => 'required',
            'class12_totalMark' => 'required',
            'class12_markObtain' => 'required',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        ;

        StudentEducationalDetails::create([

            'student_id' => $user['id'],
            'class10_passingYear' => $request->class10_passingYear,
            'class10_roll' => $request->class10_roll,
            'class10_no' => $request->class10_no,
            'class10_board' => $request->class10_board,
            'class10_school' => $request->class10_school,
            'class10_totalMark' => $request->class10_totalMark,
            'class10_markObtain' => $request->class10_markObtain,

            'class12_passingYear' => $request->class12_passingYear,
            'class12_roll' => $request->class12_roll,
            'class12_no' => $request->class12_no,
            'class12_board' => $request->class12_board,
            'class12_college' => $request->class12_college,
            'class12_strem' => $request->class12_strem,
            'class12_totalMark' => $request->class12_totalMark,
            'class12_markObtain' => $request->class12_markObtain,



        ]);

        $response = [
            'success' => true,
            'message' => 'register Student Educational Data Successfully'
        ];

        return response()->json($response, 200);

    }

    //get educetional details 


    public function GetStudentEducation(Request $request){

        $user=$request->user();

        $studenteducation = StudentEducationalDetails::where('student_id', $user['id'])->latest('created_at')->first();

        if (!$studenteducation) {

            $response = [
                'success' => false,
                'message' => 'please enter details'
            ];

            return response()->json($response, 404);
        }


        $response = [
            'success' => true,
            'studentEducation' => $studenteducation
        ];

        return response()->json($response, 200);

    }
    //student Address

    public function registerStudentAddress(Request $request) {


        $user = $request->user();

        $validator = Validator::make($request->all(), [

            'state' => 'required',
            'district' => 'required',
            'sub_district' => 'required',
            'circle_office' => 'required',
            'police_station' => 'required',
            'post_office' => 'required',
            'pin_no' => 'required',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        ;

        Address::create([
            'user_id' => $user['id'],
            'state' => $request->state,
            'district' => $request->district,
            'sub_district' => $request->sub_district,
            'circle_office' => $request->circle_office,
            'police_station' => $request->police_station,
            'post_office' => $request->post_office,
            'pin_no' => $request->pin_no,
        ]);

        $response = [
            'success' => true,
            'message' => 'register Student Address  Successfully'
        ];

        return response()->json($response, 200);

    }

    //get address

    public function getStudentAddress(Request $request){

        $user=$request->user();

        $studentAddress = Address::where('user_id', $user['id'])->latest('created_at')->first();

        if (!$studentAddress) {

            $response = [
                'success' => false,
                'message' => 'please enter details'
            ];

            return response()->json($response, 404);
        }


        $response = [
            'success' => true,
            'studentAddress' => $studentAddress
        ];

        return response()->json($response, 200);

    }

}