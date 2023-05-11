<?php

namespace App\Http\Controllers;

use App\Models\StudentEducationalDetails;
use App\Models\StudentPersonalDetails;
use App\Models\Address;
use App\Models\StudentsFilesDetails;
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

        return response()->json($response, 201);

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
    public function ApplyFileUploadController(Request $request){
        
        $validator = Validator::make($request->all(), [

            
            'profile_photo' => 'required|image|max:2048',
            'signature' => 'required|image|max:2048',
            'prc' => 'required|image|max:2048',
            'aadhar' => 'required|image|max:2048',
            'hslc_admit' => 'required|image|max:2048',
            'hslc_certificate' => 'required|image|max:2048',
            'hslc_marksheet' => 'required|image|max:2048',
            'hslc_registation' => 'required|image|max:2048',
            'hsslc_admit' => 'required|image|max:2048',
            'hsslc_certificate' => 'required|image|max:2048',
            'hsslc_marksheet' => 'required|image|max:2048',
            'hsslc_registation' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        $user = $request->user();

      

       
        $profile_photo = $request->file('profile_photo') ->store('student_files');
        $aadhar = $request->file('aadhar') ->store('student_files');
        $signature = $request->file('signature') ->store('student_files');
        $hslc_registation = $request->file('hslc_registation') ->store('student_files');
        $hslc_marksheet = $request->file('hslc_marksheet') ->store('student_files');
        $hslc_certificate = $request->file('hslc_certificate') ->store('student_files');
        $hslc_admit = $request->file('hslc_admit') ->store('student_files');
        $hsslc_registation = $request->file('hsslc_registation') ->store('student_files');
        $hsslc_marksheet = $request->file('hsslc_marksheet') ->store('student_files');
        $hsslc_certificate = $request->file('hsslc_certificate') ->store('student_files');
        $hsslc_admit = $request->file('hsslc_admit') ->store('student_files');
    
        StudentsFilesDetails::create([
                   'profile_photo'=>$profile_photo,
                   'aadhar'=>$aadhar,
                   'signature'=>$signature,
                   'hslc_registation'=>$hslc_registation,
                   'hslc_marksheet'=>$hslc_marksheet,
                   'hslc_certificate'=>$hslc_certificate,
                   'hslc_admit'=>$hslc_admit,
                   'hsslc_registation'=>$hsslc_registation,
                   'hsslc_marksheet'=>$hsslc_marksheet,
                   'hsslc_certificate'=>$hsslc_certificate,
                   'hsslc_admit'=>$hsslc_admit,
                   'student_id'=>$user['id']
                ]);
            

            $response = [
                'success' => true,
                'message' => "Uploaded file(s) successfully",
            ];
            return response()->json($response, 201);
        } 

    //get applly files 

    
    public function getApplyFilesController(Request $request)
    {


        $user = $request->user();

        $files = StudentsFilesDetails::where('student_id', $user['id'])->latest('created_at')->first();

        if (!$files) {

            $response = [
                'success' => false,
                'message' => 'please enter details'
            ];

            return response()->json($response, 404);
        }

        $files->passport_image_url = $files->profile_photo ? url($files->profile_photo) : null;
        $files->aadhar_image_url = $files->aadhar ? url($files->aadhar) : null;
        $files->signature_image_url = $files->signature ? url($files->signature) : null;
        $files->hslc_registation_image_url = $files->hslc_registation ? url($files->hslc_registation) : null;
        $files->hslc_marksheet_image_url = $files->hslc_marksheet ? url($files->hslc_marksheet) : null;
        $files->hslc_certificate_image_url = $files->hslc_certificate ? url($files->hslc_certificate) : null;
        $files->hslc_admit_image_url = $files->hslc_admit ? url($files->hslc_admit) : null;
        $files->hsslc_registation_image_url = $files->hsslc_registation ? url($files->hsslc_registation) : null;
        $files->hsslc_marksheet_image_url = $files->hsslc_marksheet ? url($files->hsslc_marksheet) : null;
        $files->hsslc_certificate_image_url = $files->hsslc_certificate ? url($files->hsslc_certificate) : null;
        $files->hsslc_admit_image_url = $files->hsslc_admit ? url($files->hsslc_admit) : null;



        $response = [
            'success' => true,
            'studentsFiles' => $files
        ];

        return response()->json($response, 200);


    }
}