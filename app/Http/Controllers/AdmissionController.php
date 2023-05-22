<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Courses;
use App\Models\NewPayment;
use App\Models\Payment;
use App\Models\StudentEducationalDetails;
use App\Models\StudentPersonalDetails;
use App\Models\StudentsFilesDetails;
use App\Models\Universitys;
use App\Models\Address;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Storage;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class AdmissionController extends Controller
{
    //apply for admission

    public function applyAdmission(Request $request, $id)
    {

        $user = $request->user();

        $course = Courses::findOrFail($id);


        $college = Universitys::where('id', $course['college_id'])->first();
        $personalDetails = StudentPersonalDetails::where('student_id', $user['id'])->latest('created_at')->first();
        $educationalDetails = StudentEducationalDetails::where('student_id', $user['id'])->latest('created_at')->first();
        $files = StudentsFilesDetails::where('student_id', $user['id'])->latest('created_at')->first();
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
            'personalDetails_id' => $personalDetails['id'],
            'educationalDetails_id' => $educationalDetails['id'],
            'files_id' => $files['id'],
            'address_id' => $studentAddress['id'],
            // 'admission_payment_status'=>$request->admission_payment_status,
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


        // $applications = DB::table('admissions')
        $applications = Admission::
            select('admissions.id', 'admissions.admission_status', 'admissions.admission_payment_status', 'admissions.apply_payment_status', 'users.name', 'courses.courseName', 'admissions.course_id', 'universitys.collegeName', 'universitys.address', )
            ->join('universitys', 'universitys.id', '=', 'admissions.college_id')
            ->join('courses', 'courses.id', '=', 'admissions.course_id')
            ->join('users', 'users.id', '=', 'admissions.student_id')
            ->where('student_id', $user['id'])
            ->get();

        foreach ($applications as $apply) {

            if ($apply->apply_payment_status === 'panding') {
                $FeesDetails = NewPayment::where('course_id', $apply['course_id'])->where('fees_type', 'application_fees')->first();
                $apply->fees_id = $FeesDetails->id;
                $apply->amount = $FeesDetails->amount;
                $apply->fees_type = $FeesDetails->fees_type;
            }
        }

        if (!$applications) {
            $response = [
                'success' => true,
                'applications' => $applications,

            ];
            return response()->json($response, 200);
        }

        $response = [
            'success' => true,
            'applications' => $applications,

        ];
        return response()->json($response, 200);
    }



    //get all admission request for college

    public function getAdmission(Request $request)
    {

        $user = $request->user();

        $college = Universitys::where('create-by', $user['id'])->first();

        if (!$college) {
            $response = [
                'success' => false,
                'message' => 'college not found'
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

    public function getCourseApplication(Request $request, $id)
    {
        $user = $request->user();

        $courses = Courses::
            join('universitys', 'universitys.id', '=', 'courses.college_id')
            ->where('courses.id', $id)
            ->where('universitys.create-by', $user['id'])->first();
        if (!$courses) {
            $response = [
                'success' => false,

                'messege' => 'no course found'
            ];
        }

        // $applications = DB::table('admissions')
        $applications = Admission::
            select('admissions.id', 'admissions.admission_status', 'admissions.admission_payment_status', 'users.name', 'universitys.collegeName', 'courses.courseName', 'student_educational_details.class10_board', 'student_personal_data.mark_obtain_lastExam', 'student_personal_data.dob', 'student_personal_data.first_name', 'student_personal_data.middle_name', 'student_personal_data.last_name', 'addresses.circle_office')
            ->join('users', 'users.id', '=', 'admissions.student_id')
            ->join('universitys', 'universitys.id', '=', 'admissions.college_id')
            ->join('courses', 'courses.id', '=', 'admissions.course_id')
            ->join('addresses', 'addresses.id', '=', 'admissions.address_id')
            ->join('student_educational_details', 'student_educational_details.id', '=', 'admissions.educationalDetails_id')
            ->join('student_personal_data', 'student_personal_data.id', '=', 'admissions.personalDetails_id')
            ->where('admissions.course_id', $id)
            ->get();
        if (!$applications) {
            $response = [
                'success' => false,
                'applications' => 'application not found',

            ];
            return response()->json($response, 200);
        }

        $response = [
            'success' => true,
            'course_applications' => $applications,

        ];
        return response()->json($response, 200);
    }

    //get admission details
    public function getApplicationDetails(Request $request, $id)
    {


        $user = $request->user();

        $college = Universitys::where('create-by', $user['id'])->first();




        $application = Admission::select('admissions.id', 'admissions.admission_status', 'admissions.admission_payment_status', 'admissions.student_id', 'admissions.course_id', 'users.name', 'universitys.collegeName', 'courses.courseName', 'student_educational_details.class10_board', 'student_educational_details.class10_school', 'student_educational_details.class10_roll', 'student_educational_details.class10_no', 'student_educational_details.class10_totalMark', 'student_educational_details.class10_markObtain', 'student_educational_details.class12_college', 'student_educational_details.class12_strem', 'student_educational_details.class12_board', 'student_educational_details.class12_totalMark', 'student_educational_details.class12_markObtain', 'student_educational_details.class12_roll', 'student_educational_details.class12_no', 'student_personal_data.email', 'student_personal_data.first_name', 'student_personal_data.middle_name', 'student_personal_data.last_name', 'student_personal_data.mark_obtain_lastExam', 'student_personal_data.qualification', 'students_files_details.profile_photo', 'students_files_details.aadhar', 'students_files_details.signature', 'students_files_details.hslc_registation', 'students_files_details.hslc_marksheet', 'students_files_details.hslc_certificate', 'students_files_details.hslc_admit', 'students_files_details.hsslc_registation', 'students_files_details.hsslc_marksheet', 'students_files_details.hsslc_certificate', 'students_files_details.hsslc_admit')
            ->join('users', 'users.id', '=', 'admissions.student_id')
            ->join('universitys', 'universitys.id', '=', 'admissions.college_id')
            ->join('courses', 'courses.id', '=', 'admissions.course_id')
            ->join('addresses', 'addresses.id', '=', 'admissions.address_id')
            ->join('students_files_details', 'students_files_details.id', '=', 'admissions.files_id')
            ->join('student_educational_details', 'student_educational_details.id', '=', 'admissions.educationalDetails_id')
            ->join('student_personal_data', 'student_personal_data.id', '=', 'admissions.personalDetails_id')
            ->where('admissions.id', $id)
            ->where('admissions.college_id', $college['id'])
            ->first();

        if ($application) {

            $payment = Payment::select('payments.*', 'new_payments.id as fees_id', 'new_payments.amount', 'new_payments.fees_type')->join('new_payments', 'new_payments.id', '=', 'payments.fees_id')
                ->where('new_payments.course_id', $application['course_id'])
                ->where('payments.student_id', $application['student_id'])
                ->get();

            $application->passport_image_url = $application->profile_photo ? url($application->profile_photo) : null;
            $application->aadhar_image_url = $application->aadhar ? url($application->aadhar) : null;
            $application->signature_image_url = $application->signature ? url($application->signature) : null;
            $application->hslc_registation_image_url = $application->hslc_registation ? url($application->hslc_registation) : null;
            $application->hslc_marksheet_image_url = $application->hslc_marksheet ? url($application->hslc_marksheet) : null;
            $application->hslc_certificate_image_url = $application->hslc_certificate ? url($application->hslc_certificate) : null;
            $application->hslc_admit_image_url = $application->hslc_admit ? url($application->hslc_admit) : null;
            $application->hsslc_registation_image_url = $application->hsslc_registation ? url($application->hsslc_registation) : null;
            $application->hsslc_marksheet_image_url = $application->hsslc_marksheet ? url($application->hsslc_marksheet) : null;
            $application->hsslc_certificate_image_url = $application->hsslc_certificate ? url($application->hsslc_certificate) : null;
            $application->hsslc_admit_image_url = $application->hsslc_admit ? url($application->hsslc_admit) : null;

            $response = [
                'success' => true,
                'application' => $application,
                'payment_history' => $payment

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


        $user = $request->user();

        $admission = Admission::
            join('universitys', 'universitys.id', '=', 'admissions.college_id')
            ->where('admissions.id', $id)
            ->where('universitys.create-by', $user['id'])->first();
        if (!$admission) {
            $response = [
                'success' => false,

                'messege' => 'no course found'
            ];

            return response()->json($response,200);
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
        $colleges=Courses::where('id',$admission['course_id'])->first();
        if($colleges['vacent_seat']=='0')
{
    $response = [
        'success' => true,
        'message' => 'Seat Capacity full'
    ];
    return response()->json($response, 400);

}


        $admission->update(['admission_status' => $request->admission_status]);

        if ($request->admission_status == 'Selected') {

            $StudentDetails = StudentPersonalDetails::where('id', $admission['personalDetails_id'])->first();
            $email = $StudentDetails['email'];

            $course = Courses::where('id', $admission['course_id'])->first();
            $course_name = $course['courseName'];

            $college = Universitys::where('id', $admission['college_id'])->first();
            $college_name = $college['collegeName'];

            Mail::send('selected', ['course_name' => $course_name, 'college_name' => $college_name], function (Message $message) use ($email) {
                $message->subject('Your Application Accept');
                $message->to($email);


            });
        }


        $response = [
            'success' => true,
            'message' => 'Update status successfully'
        ];
        return response()->json($response, 200);
    }

    //get Selected Applications

    public function getSelectedApplication(Request $request, $id)
    {


        $application = Admission::where(['course_id' => $id, 'admission_status' => 'Selected'])
            ->select('admissions.id as id', 'admissions.admission_status', 'admissions.admission_payment_status', 'users.name', 'universitys.collegeName', 'courses.courseName', 'student_educational_details.class10_board', 'student_personal_data.first_name', 'student_personal_data.middle_name', 'student_personal_data.last_name')
            ->join('users', 'users.id', '=', 'admissions.student_id')
            ->join('universitys', 'universitys.id', '=', 'admissions.college_id')
            ->join('courses', 'courses.id', '=', 'admissions.course_id')
            ->join('addresses', 'addresses.id', '=', 'admissions.address_id')
            ->join('student_educational_details', 'student_educational_details.id', '=', 'admissions.educationalDetails_id')
            ->join('student_personal_data', 'student_personal_data.id', '=', 'admissions.personalDetails_id')
            ->get();

        if ($application) {

            $response = [
                'success' => true,
                'SelectedApplication' => $application,

            ];
            return response()->json($response, 200);
        }

        $response = [
            'success' => false,
            'message' => 'not found'

        ];
        return response()->json($response, 200);

    }
    // admission fee payment 


    public function AdmissionPayment(Request $request, $id)
    {


        $admission = Admission::findOrFail($id);




        $admission->update([
            'admission_payment_status' => 'paid',
            'admission_status' => 'confirmed'
        ]);

        $course = Courses::where('id', $admission['course_id'])->first();

        $course->seat_capacity = $course->seat_capacity - 1;

        $course->save();




        $response = [
            'success' => true,
            'message' => 'Update status successfully'
        ];
        return response()->json($response, 200);
    }
    //get Confirm Applications

    public function getConfirmStudentList(Request $request, $id)
    {


        $ConfirmStudent = Admission::where(['course_id' => $id, 'admission_payment_status' => 'paid'])
            ->select('admissions.id as id', 'admissions.admission_status', 'admissions.admission_payment_status', 'users.name', 'universitys.collegeName', 'courses.courseName', 'student_educational_details.class10_board', 'student_personal_data.first_name', 'student_personal_data.middle_name', 'student_personal_data.last_name', 'student_personal_data.mark_obtain_lastExam', 'student_personal_data.phon_no', 'student_personal_data.email')
            ->join('users', 'users.id', '=', 'admissions.student_id')
            ->join('universitys', 'universitys.id', '=', 'admissions.college_id')
            ->join('courses', 'courses.id', '=', 'admissions.course_id')
            ->join('addresses', 'addresses.id', '=', 'admissions.address_id')
            ->join('student_educational_details', 'student_educational_details.id', '=', 'admissions.educationalDetails_id')
            ->join('student_personal_data', 'student_personal_data.id', '=', 'admissions.personalDetails_id')
            ->get();

        if ($ConfirmStudent) {

            $response = [
                'success' => true,
                'ConfirmStudent' => $ConfirmStudent,

            ];
            return response()->json($response, 200);
        }




        $response = [
            'success' => false,
            'message' => 'not found'

        ];
        return response()->json($response, 200);


    }

    //view pdf 
    public function AdmissionAcknowladgement(Request $request , $id){

        // $admission=Admission::where('id',$id)->first();


        $admission = Admission::select('admissions.id', 'admissions.admission_status', 'admissions.apply_payment_status', 'admissions.admission_payment_status','admissions.student_id', 'admissions.created_at',  'universitys.address as clgAdress','admissions.course_id', 'users.name', 'universitys.collegeName', 'courses.courseName', 'student_educational_details.class10_board', 'student_educational_details.class10_school', 'student_educational_details.class10_roll',  'student_educational_details.class10_passingYear', 'student_educational_details.class12_passingYear','student_educational_details.class10_no', 'student_educational_details.class10_totalMark', 'student_educational_details.class10_markObtain', 'student_educational_details.class12_college', 'student_educational_details.class10_school','student_educational_details.class12_strem', 'student_educational_details.class12_board', 'student_educational_details.class12_totalMark', 'student_educational_details.class12_markObtain', 'student_educational_details.class12_roll', 'student_educational_details.class12_no', 'student_personal_data.email', 'student_personal_data.identification','student_personal_data.identification_no',  'student_personal_data.first_name', 'student_personal_data.middle_name', 'student_personal_data.last_name', 'student_personal_data.phon_no','student_personal_data.mark_obtain_lastExam', 'student_personal_data.qualification', 'students_files_details.profile_photo', 'students_files_details.aadhar', 'students_files_details.signature', 'students_files_details.hslc_registation', 'students_files_details.hslc_marksheet', 'students_files_details.hslc_certificate', 'students_files_details.hslc_admit', 'students_files_details.hsslc_registation', 'students_files_details.hsslc_marksheet', 'students_files_details.hsslc_certificate', 'students_files_details.hsslc_admit','addresses.state','addresses.district','addresses.sub_district','addresses.pin_no')
        ->join('users', 'users.id', '=', 'admissions.student_id')
        ->join('universitys', 'universitys.id', '=', 'admissions.college_id')
        ->join('courses', 'courses.id', '=', 'admissions.course_id')
        ->join('addresses', 'addresses.id', '=', 'admissions.address_id')
        ->join('students_files_details', 'students_files_details.id', '=', 'admissions.files_id')
        ->join('student_educational_details', 'student_educational_details.id', '=', 'admissions.educationalDetails_id')
        ->join('student_personal_data', 'student_personal_data.id', '=', 'admissions.personalDetails_id')
        ->where('admissions.id', $id)
        // ->where('admissions.college_id', $college['id'])
        ->first();

        return view('admission.details-pdf',compact('admission'));
    }
    //view pdf 
    public function AdmissionAcknowladgementDownload(Request $request , $id){

        // $admission=Admission::where('id',$id)->first();


        $admission = Admission::select('admissions.id', 'admissions.admission_status', 'admissions.apply_payment_status', 'admissions.admission_payment_status','admissions.student_id', 'admissions.created_at',  'universitys.address as clgAdress','admissions.course_id', 'users.name', 'universitys.collegeName', 'courses.courseName', 'student_educational_details.class10_board', 'student_educational_details.class10_school', 'student_educational_details.class10_roll',  'student_educational_details.class10_passingYear', 'student_educational_details.class12_passingYear','student_educational_details.class10_no', 'student_educational_details.class10_totalMark', 'student_educational_details.class10_markObtain', 'student_educational_details.class12_college', 'student_educational_details.class10_school','student_educational_details.class12_strem', 'student_educational_details.class12_board', 'student_educational_details.class12_totalMark', 'student_educational_details.class12_markObtain', 'student_educational_details.class12_roll', 'student_educational_details.class12_no', 'student_personal_data.email', 'student_personal_data.identification','student_personal_data.identification_no',  'student_personal_data.first_name', 'student_personal_data.middle_name', 'student_personal_data.last_name', 'student_personal_data.phon_no','student_personal_data.mark_obtain_lastExam', 'student_personal_data.qualification', 'students_files_details.profile_photo', 'students_files_details.aadhar', 'students_files_details.signature', 'students_files_details.hslc_registation', 'students_files_details.hslc_marksheet', 'students_files_details.hslc_certificate', 'students_files_details.hslc_admit', 'students_files_details.hsslc_registation', 'students_files_details.hsslc_marksheet', 'students_files_details.hsslc_certificate', 'students_files_details.hsslc_admit','addresses.state','addresses.district','addresses.sub_district','addresses.pin_no')
        ->join('users', 'users.id', '=', 'admissions.student_id')
        ->join('universitys', 'universitys.id', '=', 'admissions.college_id')
        ->join('courses', 'courses.id', '=', 'admissions.course_id')
        ->join('addresses', 'addresses.id', '=', 'admissions.address_id')
        ->join('students_files_details', 'students_files_details.id', '=', 'admissions.files_id')
        ->join('student_educational_details', 'student_educational_details.id', '=', 'admissions.educationalDetails_id')
        ->join('student_personal_data', 'student_personal_data.id', '=', 'admissions.personalDetails_id')
        ->where('admissions.id', $id)
        // ->where('admissions.college_id', $college['id'])
        ->first();

        $data=['admission'=>$admission];

        $pdf=Pdf::loadView('admission.details-pdf',$data);
        $todayData=Carbon::now()->format('d-m-y');

        return $pdf->download('Acknowledgement-'.$admission->$id.'-'.$todayData.'.pdf');

    }

}