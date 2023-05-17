<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Universitys;
use Illuminate\Support\Facades\DB;
use App\Models\Courses;
use App\Models\NewPayment;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;
use Razorpay\Api\Api;

class NewPaymentController extends Controller
{
  //  create payment request

  public function NewPayment(Request $request, $id)
  {

    $validator = Validator::make($request->all(), [
      // 'fees_type' => 'required',
      'amount' => 'required',
      'last_date' => 'required',
      'option' => 'required'


    ]);
    if ($validator->fails()) {
      $response = [
        'success' => false,
        'message' => $validator->errors()->first()
      ];
      return response()->json($response, 400);
    }

    $course = Courses::findOrFail($id);
    $date = Carbon::now()->format('YmdHis');

    $feesType = $request->input('option') === 'other' ? $request->fees_type : $request->option;

    NewPayment::Create([

      // 'fees_type' => $request->input('fees_type'),
      'fees_type' => $feesType,
      'fees_id' => 'FEES-' . Str::uuid() . '-' . $date,
      'amount' => $request->input('amount'),
      'last_date' => $request->input('last_date'),
      'course_id' => $course['id'],

    ]);

    $response = [
      'success' => true,
      'messege' => 'New  Payment request add Succesfully'
    ];
    return response()->json($response, 201);

  }


  //get payment requests for courses

  public function getCoursePaymentRequest(Request $request, $id)
  {

    $user=$request->user();

    $course=Courses::
    join('universitys','universitys.id','=','courses.college_id')
    ->where('courses.id',$id)->where('universitys.create-by',$user['id'])->first();

    if(!$course){
      $response=[
        'success'=>false,
        'messege'=>'no Course found'
      ];
      return response()->json($response,200);
    }



    $totalStudents = Admission::where('course_id', $id)->where('admission_status', 'confirmed')->count();


    //  $Payments = NewPayment::where('course_id', $id)->get();
    $Payments = NewPayment::select('*', DB::raw('(SELECT COUNT(*) FROM payments WHERE fees_id = new_payments.id) as feePaymentStudent'))
      ->where('course_id', $id)
      ->get();

    if (!$Payments) {

      $response = [
        'success' => true,
        'payments' => $Payments
      ];
      return response()->json($response, 200);
    }

    foreach ($Payments as $payment) {
      $payment->totalStudents = $totalStudents;
      // $payment->feePaymentStudent =  Payment::where('fees_id',$payment->id)->count();

    }

    $response = [
      'success' => true,
      'payments' => $Payments
    ];
    return response()->json($response, 200);

  }


  //get application fees for student


  public function studentApplicationFees(Request $request, $id)
  {
    $user = $request->user();
    $admission = Admission::where('id', $id)->where('admissions.student_id', $user['id'])->first();

    if (!$admission) {

      $response = [
        'success' => false,
        'messege' => 'no record found'
      ];
      return response()->json($response, 200);
    }

  }

  //get payment requests for students

  public function getStudentPaymentRequest(Request $request, $id)
  {

    $user = $request->user();
    $admission = Admission::where('id', $id)->where('admissions.student_id', $user['id'])->first();

    if (!$admission) {

      $response = [
        'success' => false,
        'messege' => 'no record found'
      ];
      return response()->json($response, 200);
    }

    if ($admission->admission_status === 'Selected') {

      $Payments = NewPayment::select(
        'new_payments.id',
        'new_payments.fees_type',
        'new_payments.active_status',
        'new_payments.last_date',
        'new_payments.amount',
        'new_payments.course_id'
      )
        ->join('courses', 'courses.id', '=', 'new_payments.course_id')
        ->join('admissions', 'admissions.course_id', '=', 'courses.id')
        ->where('admissions.id', $id)
        ->where('admissions.admission_status', 'Selected')
        ->where('new_payments.fees_type', 'admission_fees')
        ->where('new_payments.active_status', 'active')
        ->whereNotExists(function ($query) use ($id) {
          $query->select(DB::raw(1))
            ->from('payments')
            ->join('admissions', 'admissions.student_id', '=', 'payments.student_id')
            ->where('admissions.id', '=', $id)
            ->whereRaw('payments.fees_id = new_payments.id');
        })
        ->get();
      $response = [
        'success' => true,
        'new_payments' => $Payments
      ];
      return response()->json($response, 200);

    }
    if ($admission->admission_status === 'application_fee_panding') {

      $Payments = NewPayment::select(
        'new_payments.id',
        'new_payments.fees_type',
        'new_payments.active_status',
        'new_payments.last_date',
        'new_payments.amount',
        'new_payments.course_id'
      )
        ->join('courses', 'courses.id', '=', 'new_payments.course_id')
        ->join('admissions', 'admissions.course_id', '=', 'courses.id')
        ->where('admissions.id', $id)
        ->where('admissions.admission_status', 'application_fee_panding')
        ->where('new_payments.fees_type', 'application_fees')
        ->where('new_payments.active_status', 'active')
        ->whereNotExists(function ($query) use ($id) {
          $query->select(DB::raw(1))
            ->from('payments')
            ->join('admissions', 'admissions.student_id', '=', 'payments.student_id')
            ->where('admissions.id', '=', $id)
            ->whereRaw('payments.fees_id = new_payments.id');
        })
        ->get();
      $response = [
        'success' => true,
        'new_payments' => $Payments
      ];
      return response()->json($response, 200);

    }

    $Payments = NewPayment::select(
      'new_payments.id',
      'new_payments.fees_type',
      'new_payments.active_status',
      'new_payments.last_date',
      'new_payments.amount',
      'new_payments.course_id'
    )
      ->join('courses', 'courses.id', '=', 'new_payments.course_id')
      ->join('admissions', 'admissions.course_id', '=', 'courses.id')
      ->where('admissions.id', $id)
      ->where('admissions.admission_status', 'confirmed')
      ->where('new_payments.active_status', 'active')
      ->whereNotExists(function ($query) use ($id) {
        $query->select(DB::raw(1))
          ->from('payments')
          ->join('admissions', 'admissions.student_id', '=', 'payments.student_id')
          ->where('admissions.id', '=', $id)
          ->whereRaw('payments.fees_id = new_payments.id');
      })
      ->get();


    if (!$Payments) {

      $response = [
        'success' => true,
        'payments' => $Payments
      ];
      return response()->json($response, 200);
    }

    $response = [
      'success' => true,
      'new_payments' => $Payments
    ];
    return response()->json($response, 200);

  }

  //process payments

  public function processPayments(Request $request)
  {



    $user = $request->user();
    $id = $request->id;
    $feesDetails = NewPayment::where('id', $id)->first();

    $key = config('services.razorpay.key');
    $secret = config('services.razorpay.secret');
    $amount = $feesDetails['amount'];
    $student_id = $user['id'];




    // create a new order in Razorpay
    $options = [
      'amount' => $amount * 100,
      'currency' => 'INR',
      // 'receipt' => $paymentId,
    ];

    $api = new Api($key, $secret);
    $order = $api->order->create($options);

    // return the payment ID and order details to your view
    return response()->json([
      'success' => true,

      'orderId' => $order->id,
      'orderAmount' => $order->amount,

    ], 201);
  }





  // payment verification

  public function PaymentVerification(Request $request)
  {



    // $id = $request->id;
    $success = true;

    $razorpay_payment_id = $request->input('razorpay_payment_id');
    $razorpay_order_id = $request->input('razorpay_order_id');
    $razorpay_signature = $request->input('razorpay_signature');
    // $fees_id = $request->input('fees_id');

    try {
      $key = config('services.razorpay.key');
      $secret = config('services.razorpay.secret');
      $api = new Api($key, $secret);


      $attributes = array(
        'razorpay_payment_id' => $razorpay_payment_id,
        'razorpay_order_id' => $razorpay_order_id,
        'razorpay_signature' => $razorpay_signature
      );

      $api->utility->verifyPaymentSignature($attributes);

    } catch (\Exception $e) {
      $success = false;
    }

    $payment = $api->payment->fetch($razorpay_payment_id);


    $status = $payment->status;


    $fees_id = $payment->notes['fees_id'];
    $user_id = $payment->notes['user_id'];

    if ($success === true) {





      Payment::create([
        'fees_id' => $fees_id,
        'student_id' => $user_id,
        'payment_id' => $razorpay_payment_id,
        'payment_status' => $status
      ]);

      $feesDetails = NewPayment::where('id', $fees_id)->first();

      if ($feesDetails->fees_type === 'application_fees') {


        $admission = Admission::where('student_id', $user_id)->where('course_id', $feesDetails['course_id'])->first();

        $admission->update(['apply_payment_status' => 'paid']);
        $admission->update(['admission_status' => 'Under Review']);

      }
      if ($feesDetails->fees_type === 'admission_fees') {


        $admission = Admission::where('student_id', $user_id)->where('course_id', $feesDetails['course_id'])->first();

        $admission->update(['admission_payment_status' => 'paid']);
        $admission->update(['admission_status' => 'confirmed']);

        $course = Courses::where('id', $admission['course_id'])->first();

        $course->vacent_seat = $course->vacent_seat - 1;

        $course->save();

      }



      // return response()->json(['success' => true, 'message' => 'Payment verified successfully']);
      return redirect()->away("http://localhost:3000/paymentsuccess?reference={$razorpay_payment_id}");
    } else {
      // Payment verification failed, handle the error
      return response()->json(['success' => false, 'message' => 'Payment verification failed']);
    }
  }
  //student payment history

  public function getStudentPaymenthistory(Request $request, $id)
  {

    $user = $request->user();

    $admission = Admission::select('course_id')->where('id', $id)->first();


    $Payments = Payment::select(
      'payments.payment_id',
      'payments.fees_id',
      'payments.payment_status',
      'payments.created_at',
      'new_payments.course_id',
      'new_payments.amount',
      'new_payments.fees_type',
    )
      ->join('new_payments', 'new_payments.id', '=', 'payments.fees_id')
      ->join('courses', 'courses.id', '=', 'new_payments.course_id')
      ->join('admissions', 'admissions.course_id', '=', 'courses.id')
      ->where('admissions.id', $id)
      ->where('admissions.student_id', $user['id'])
      ->where('payments.student_id', $user['id'])
      ->where('new_payments.course_id', $admission['course_id'])
      ->get();

    if (!$Payments) {

      $response = [
        'success' => true,
        'messege' => 'no payment request'
      ];
      return response()->json($response, 200);
    }

    $response = [
      'success' => true,
      'paymentsHistory' => $Payments
    ];
    return response()->json($response, 200);

  }


  public function getRezorpayKey(Request $request)
  {
    $key = config('services.razorpay.key');
    $secret = config('services.razorpay.secret');
    $response = [
      'success' => true,
      'key' => $key
    ];

    return response()->json($response, 200);

  }

  // course payment details

  public function coursePaymentDetails(Request $request, $id)
  {

    $user = $request->user();

    $course = Courses::join('new_payments', 'new_payments.course_id', '=', 'courses.id')
      ->where('new_payments.id', $id)
      ->where('courses.college_id', function ($query) use ($user) {
        $query->select('id')
          ->from('Universitys')
          ->where('create-by', $user['id'])
          ->limit(1);
      })
      ->first();

    if (!$course) {


      $response = [
        'success' => false,
        'messege' => 'Course not exist/fees not exist'
      ];

      return response()->json($response, 200);
    }
    $paymentData = Payment::select('payments.*', 'new_payments.amount', 'users.name','courses.courseName')
      ->join('users', 'users.id', 'payments.student_id')
      ->join('new_payments', 'new_payments.id', 'payments.fees_id')
      ->join('courses','courses.id','=','new_payments.course_id')
      ->where('payments.fees_id', $id)->get();

    $response = [
      'success' => true,
      'payment_Data' => $paymentData
    ];

    return response()->json($response, 200);


  }


  // close payment status

  public function CloseFeesStatus(Request $request)
  {

  
    $id=$request->id;

    $paymentDetails = NewPayment::where('id', $id)->first();

    $paymentDetails->update(['active_status' => 'closed']);



    $response = [

      'success' => true,
      'messege' => 'Closed Fees Request Successfully'

    ];
    return response()->json($response, 200);


  }
}