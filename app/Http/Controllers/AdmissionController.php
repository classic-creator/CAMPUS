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
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
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
        $applications = DB::table('admissions')->where('course_id', $id)->select('admissions.id as id','admissions.admission_status','admissions.payment_status' ,'users.name','universitys.collegeName','courses.courseName','student_educational_details.class10_board','student_personal_data.dob')->join('users','users.id','=','admissions.student_id')->join('universitys','universitys.id','=','admissions.college_id')->join('courses','courses.id','=','admissions.course_id')->join('addresses','addresses.id','=','admissions.address_id')->join('student_educational_details','student_educational_details.id','=','admissions.educationalDetails_id')->join('student_personal_data','student_personal_data.id','=','admissions.personalDetails_id')->get();
        $response = [
            'success' => true,
           'applications'=> $applications,

        ];
        return response()->json($response, 200);
    }

    //get admission details
    public function getApplicationDetails(Request $request, $id)
    {


        $application = Admission::select('admissions.id as id','admissions.admission_status','admissions.payment_status' ,'users.name','universitys.collegeName','courses.courseName','student_educational_details.class10_board','student_educational_details.class10_school','student_educational_details.class10_roll','student_educational_details.class10_no','student_educational_details.class10_totalMark','student_educational_details.class10_markObtain','student_educational_details.class12_college','student_educational_details.class12_strem','student_educational_details.class12_board','student_educational_details.class12_totalMark','student_educational_details.class12_markObtain','student_educational_details.class12_roll','student_educational_details.class12_no','student_personal_data.email','student_personal_data.first_name','student_personal_data.middle_name','student_personal_data.last_name','student_personal_data.mark_obtain_lastExam','student_personal_data.qualification')->join('users','users.id','=','admissions.student_id')->join('universitys','universitys.id','=','admissions.college_id')->join('courses','courses.id','=','admissions.course_id')->join('addresses','addresses.id','=','admissions.address_id')->join('student_educational_details','student_educational_details.id','=','admissions.educationalDetails_id')->join('student_personal_data','student_personal_data.id','=','admissions.personalDetails_id')->where('admissions.id',$id)->first();

        if ($application) {

            $response = [
                'success' => true,
              'application'=>  $application,

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
        $admission = Admission::where('id',$id)->first();

        if(!$admission){
            $response = [
                'success' => false,
                'message'=>'not found'

            ];
            return response()->json($response, 404);
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

        if($request->admission_status=='Selected'){

        $StudentDetails=StudentPersonalDetails::where('id',$admission['personalDetails_id'])->first();
        $email=$StudentDetails['email'];

        $course=Courses::where('id',$admission['course_id'])->first();
        $course_name=$course['courseName'];

        $college=Universitys::where('id',$admission['college_id'])->first();
        $college_name=$college['collegeName'];

        Mail::send('selected',['course_name'=>$course_name,'college_name'=>$college_name],function(Message $message)use($email){
            $message->subject('Your Application Accept');
            $message->to($email);

         
        });
    }
        $response = [
            'success' => true,
           'message'=>'Update status successfully'
        ];
        return response()->json($response, 200);
    }

    //get Selected Applications

      public function getSelectedApplication(Request $request, $id)
    {


      $application=Admission::where(['course_id'=>$id,'admission_status'=>'Selected'])->select('admissions.id as id','admissions.admission_status','admissions.payment_status' ,'users.name','universitys.collegeName','courses.courseName','student_educational_details.class10_board','student_personal_data.first_name','student_personal_data.middle_name','student_personal_data.last_name')->join('users','users.id','=','admissions.student_id')->join('universitys','universitys.id','=','admissions.college_id')->join('courses','courses.id','=','admissions.course_id')->join('addresses','addresses.id','=','admissions.address_id')->join('student_educational_details','student_educational_details.id','=','admissions.educationalDetails_id')->join('student_personal_data','student_personal_data.id','=','admissions.personalDetails_id')->get();

        if ($application) {

            $response = [
                'success' => true,
              'SelectedApplication'=>  $application,

            ];
            return response()->json($response, 200);
        }
    
        $response = [
            'success' => false,
            'message' => 'not found'

        ];
        return response()->json($response, 200);

    }

}