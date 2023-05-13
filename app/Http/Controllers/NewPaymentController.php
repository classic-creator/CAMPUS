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
  //

  public function NewPayment(Request $request, $id)
  {

    $validator = Validator::make($request->all(), [
      'fees_type' => 'required',
      'amount' => 'required',
      'last_date' => 'required',


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

    NewPayment::Create([

      'fees_type' => $request->input('fees_type'),
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

     $totalStudents=Admission::where('course_id',$id)->where('admission_status','confirmed')->count();
     

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

  //get payment requests for students

  public function getStudentPaymentRequest(Request $request, $id)
  {

    //  $user=$request->user();
    $Payments = NewPayment::select(
      'new_payments.id',
      'new_payments.fees_type',
      'new_payments.active_status',
      'new_payments.last_date',
      'new_payments.amount',
      'new_payments.course_id'
    )
      ->join('admissions', 'admissions.course_id', '=', 'new_payments.course_id')

      ->where('admissions.id', $id)

      ->where('admissions.admission_status', 'confirmed')
      ->whereNotExists(function ($query) {
        $query->select(DB::raw(1))
          ->from('payments')
          ->whereRaw('payments.fees_id = new_payments.id')
          ->whereRaw('payments.student_id = admissions.student_id');
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

    // create a new payment record in the database


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
      // 'paymentId' => $paymentId,
      'orderId' => $order->id,
      'orderAmount' => $order->amount,
      // 'order'=>$order
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

    $course=Admission::select('course_id')->where('id',$id)->first();

    $Payments = Payment::select(
      'payments.payment_id',
      'payments.fees_id',
      'payments.payment_status',
      'payments.created_at',
      'new_payments.course_id'

    ) 
     ->join('new_payments','new_payments.id','=','payments.fees_id')
   
      ->where('payments.student_id', $user['id'])
      ->where('new_payments.course_id',$course['course_id'])
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

  public function coursePaymentDetails(Request $request,$id){
    
    $user=$request->user();
   
    $course = Courses::join('new_payments', 'new_payments.course_id', '=', 'courses.id')
                  ->where('new_payments.id', $id)
                  ->where('courses.college_id', function($query) use ($user) {
                      $query->select('id')
                            ->from('Universitys')
                            ->where('create-by', $user['id'])
                            ->limit(1);
                  })
                  ->first();

     if(!$course){

     
      $response=[
        'success'=>false,
        'messege'=>'Course not exist/fees not exist'
       ];
    
       return response()->json($response,200);
     }
     $paymentData=Payment::select('payments.*','new_payments.amount','users.name')
     ->join('users','users.id','payments.student_id')
     ->join('new_payments','new_payments.id','payments.fees_id')
     ->where('payments.fees_id',$id)->get();

     $response=[
      'success'=>true,
      'payment_Data'=>$paymentData
     ];

     return response()->json($response,200);
  
 
  }



}