<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Courses;
use App\Models\StudentEducationalDetails;
use App\Models\StudentPersonalDetails;
use App\Models\Universitys;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class AdmissionController extends Controller
{
    //apply for admission

    public function applyAdmission(Request $request, $id)
    {

        $user = $request->user();

        $course = Courses::where('id', $id)->first();
        if (!$course) {
            $response = [
                'success' => false,
                'message' => "course not found",
            ];
            return response()->json($response, 200);
        }

        $college = Universitys::where('id', $course['college_id'])->first();
        $personalDetails=StudentPersonalDetails::where('student_id', $user['id'])->latest('created_at')->first();
        $educationalDetails=StudentEducationalDetails::where('student_id', $user['id'])->latest('created_at')->first();
        $studentAddress = Address::where('user_id', $user['id'])->latest('created_at')->first();

        $admissionRecord = Admission::where('course_id', $id)->where('student_id', $user['id'])->first();

        if ($admissionRecord) {
            $response = [
                'success' => false,
                'message' => "apply already",
            ];
            return response()->json($response, 400);

        }

      Admission::create([
            'student_id' => $user['id'],
            'course_id' => $course['id'],
            'college_id' => $college['id'],
            'personalDetails_id'=>$personalDetails['id'],
            'educationalDetails_id'=>$educationalDetails['id'],
            'address_id'=>$studentAddress['id'],
            // 'payment_status'=>$request->payment_status,
            // 'admission_status'=>$request->admission_status
        ]);
        $response = [

            'success' => true,
            'msg' => "apply successfully",
            
        ];
        return response()->json($response, 201);
    }


    //get admission request for users


 public function getMyapplications(Request $request)
    {

        $user = $request->user();
    

        $applications = DB::table('admissions')->select('admissions.id','admissions.admission_status','admissions.payment_status','users.name','courses.courseName','universitys.collegeName','universitys.address',) ->join('universitys', 'universitys.id', '=', 'admissions.college_id') ->join('courses', 'courses.id', '=', 'admissions.course_id')->join('users', 'users.id', '=', 'admissions.student_id')->where('student_id', $user['id'])->get();

        if(!$applications){
            $response = [
                'success' => true,
               'applications'=> $applications,
    
            ];
            return response()->json($response, 200);
        }

        $response = [
            'success' => true,
           'applications'=> $applications,

        ];
        return response()->json($response, 200);
    }



    //get all admission request for college

    public function getAdmission(Request $request)
    {

        $user = $request->user();
        
        $college = Universitys::where('create-by', $user['id'])->first();

        if(!$college){
            $response = [
                'success' => false,
                'message'=>'college not found'
            ];
            return response()->json($response, 200);
        }

        $admissions = DB::table('admissions')->where('college_id', $college['id'])->get();

        $response = [
            'success' => true,
            $admissions,

        ];
        return response()->json($response, 200);
    }
  //get applications for course 

  public function getCourseApplication(Request $request,$id)
    {

        
        $course = Courses::where('id', $id)->first();

        if(!$course){
            $response = [
                'success' => false,
                'message'=>'college not found'
            ];
            return response()->json($response, 200);
        }

        // $applications = DB::table('admissions')->where('course_id', $id)->join('users','users.id','=','admissions.student_id')->join('universitys','universitys.id','=','admissions.college_id')->join('courses','courses.id','=','admissions.course_id')->join('addresses','addresses.id','=','admissions.address_id')->join('student_educational_details','student_educational_details.id','=','admissions.educationalDetails_id')->join('student_personal_data','student_personal_data.id','=','admissions.personalDetails_id')->get();
        $applications = DB::table('admissions')->where('course_id', $id)->select('admissions.id as id','admissions.admission_status','admissions.payment_status' ,'users.name','universitys.collegeName','courses.courseName','student_educational_details.*','student_personal_data.dob')->join('users','users.id','=','admissions.student_id')->join('universitys','universitys.id','=','admissions.college_id')->join('courses','courses.id','=','admissions.course_id')->join('addresses','addresses.id','=','admissions.address_id')->join('student_educational_details','student_educational_details.id','=','admissions.educationalDetails_id')->join('student_personal_data','student_personal_data.id','=','admissions.personalDetails_id')->get();
        $response = [
            'success' => true,
           'applications'=> $applications,

        ];
        return response()->json($response, 200);
    }

    //get admission details
    public function getAdmissionDetails(Request $request, $id)
    {

        $user = $request->user();

        $college = Universitys::where('create-by', $user['id'])->first();

        if(!$college){
            $response = [
                'success' => false,
                'message'=>'not found'

            ];
            return response()->json($response, 200);
        }

        $admission = Admission::where('id', $id)->where('collegeId',$college['id'])->first();

        if ($admission) {

            $response = [
                'success' => true,
                $admission,

            ];
            return response()->json($response, 200);
        }
    
        $response = [
            'success' => false,
            'message' => 'not found'

        ];
        return response()->json($response, 200);

    }
    //update admission status

    public function updateAdmissionStatus(Request $request, $id)
    {
        $admission = Admission::where('id', $id)->first();

        if(!$admission){
            $response = [
                'success' => false,
                'message'=>'not found'

            ];
            return response()->json($response, 200);
        }

        $validator = Validator::make($request->all(), [

            'admission_status' => 'required',

        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        ;

        $admission->admission_status = $request->admission_status;

        $admission->save();

        $response = [
            'success' => true,
           'message'=>'Update status successfully'
        ];
        return response()->json($response, 200);
    }

}