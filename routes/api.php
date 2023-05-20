<?php

use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\CollegeImageController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\NewPaymentController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\StudentDetails;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepertmentController;
use App\Http\Controllers\CourseImgController;
use App\Models\StudentsFilesDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Public Routes
Route::get('/carousel/get', [CollegeImageController::class, 'getCarousel']);

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/sent-reset-password-email',[PasswordResetController::class,'forgetPassword']);
Route::put('/reset-password/{token}',[PasswordResetController::class,'resetPassword']);
Route::get('/colleges',[UniversityController::class,'getAllCollege']);
Route::get('/courses',[CoursesController::class,'getAllCourses']);
Route::get('/college/{id}',[UniversityController::class,'getCollegeDetails']);
Route::get('/college/links/{id}',[UniversityController::class,'GetNotics']);
Route::get('/college/course/{id}',[CoursesController::class,'getCourseDetails']);

Route::get('/get/razorpay/key', [NewPaymentController::class, 'getRezorpayKey']);

Route::post('/payment/varification', [NewPaymentController::class, 'PaymentVerification']);
Route::get('/count', [UserController::class, 'getAllStudentandApplication']);
//login protected routes
Route::middleware(['auth:sanctum'])->group(function(){
   
    
    Route::get('/preference/courses',[CoursesController::class,'getPreferedCourses']);
    Route::post('/logout',[UserController::class,'logout']);
    Route::get('/getProfile',[UserController::class,'getProfile']);
    Route::put('/changepassword',[UserController::class,'changePassword']);
    // Route::post('/collegeregister', [UniversityController::class, 'registerCollege']);
    Route::post('/collegeregister', [UniversityController::class, 'registerCollegeRequest']);
    

    Route::post('/Profile/update', [UserController::class, 'updateProfile']);
    Route::post('/apply/course/{id}', [AdmissionController::class, 'applyAdmission']);
    Route::post('/add/preference', [PreferenceController::class, 'addPreference']);
    Route::get('/get/preference', [PreferenceController::class, 'getPreferences']);
    Route::post('/update/preference', [PreferenceController::class, 'updatePreference']);
    Route::post('/register/personalDetails', [StudentDetails::class, 'RegisterStudentPersonalDetails']);
    Route::get('/personalDetails', [StudentDetails::class, 'getStudentPersonalDetails']);
    Route::post('/register/educationalDetails', [StudentDetails::class, 'registerStudentEducationalDetails']);
    Route::get('/get/educationalDetails', [StudentDetails::class, 'GetStudentEducation']);
    Route::post('/register/address', [StudentDetails::class, 'registerStudentAddress']);
    Route::post('/upload/files', [StudentDetails::class, 'ApplyFileUploadController']);
    Route::get('/get/address', [StudentDetails::class, 'getStudentAddress']);
    Route::get('/get/applications', [AdmissionController::class, 'getMyapplications']);
    Route::get('/get/applyfiles', [StudentDetails::class, 'getApplyFilesController']);
    Route::get('/admission/payment/{id}', [AdmissionController::class, 'AdmissionPayment']);
    Route::post('/add/seatStructure/{id}', [CoursesController::class, 'uploadSeatStructure']);
    Route::post('/add/payment/{id}', [NewPaymentController::class, 'NewPayment']);
    Route::get('/get/payment/{id}', [NewPaymentController::class, 'getCoursePaymentRequest']);
    Route::get('/student/payment/{id}', [NewPaymentController::class, 'getStudentPaymentRequest']);
   
    Route::post('/process/payment/{id}',[NewPaymentController::class,'processPayments']);
    Route::get('/payment/history/{id}',[NewPaymentController::class,'getStudentPaymenthistory']);
    
  
});

// admin protected routes
Route::middleware(['auth:sanctum','user-access:admin'])->group(function(){
    
    // Route::get('/admin/colleges',[UniversityController::class,'getAllCollegeAdmin']);
    Route::get('/admin/approve/colleges',[UniversityController::class,'getAllCollegeRequest']);
    Route::post('/admin/colleges/register',[UniversityController::class,'registerApproveCollege']);
    // Route::get('/admin/colleges/{id}',[UniversityController::class,'getAllCollegeDetailsAdmin']);
    // Route::put('/admin/users/{id}',[UserController::class,'updateUsersAdmin']);

    Route::post('/carousel/upload', [CollegeImageController::class, 'addCarousel']);
    Route::post('/scheme/upload', [CollegeImageController::class, 'addScheme']);
    Route::get('/scheme/get', [CollegeImageController::class, 'GetScheme']);

    Route::delete('/carousel/delete/{id}', [CollegeImageController::class, 'DeleteCarouselImage']);
    Route::get('/admin/users',[UserController::class,'getAllUsersAdmin']);
    Route::get('/admin/colleges',[UniversityController::class,'getAllCollegeAdmin']);
    Route::get('/admin/colleges/{id}',[UniversityController::class,'getAllCollegeDetailsAdmin']);
    Route::put('/admin/users/{id}',[UserController::class,'updateUsersAdmin']);

    
});




// manager protected routes
Route::middleware(['auth:sanctum','user-access:manager'])->group(function(){
    
    Route::put('/college/update', [UniversityController::class, 'updateCollegeDetails']);
    Route::get('/college/staff/profile',[UniversityController::class,'getMyCollegeDetails']);
    Route::delete('/college/staff/delete', [UniversityController::class, 'deleteCollege']);
    Route::post('/college/course/register/{id}', [CoursesController::class, 'courseRegister']);
    Route::get('/college/all/courses', [CoursesController::class, 'getCollegeCourses']);
    Route::put('/college/staff/course/{id}', [CoursesController::class, 'updateCourseDetails']);
    Route::delete('/college/staff/course/{id}', [CoursesController::class, 'deleteCourse']);
    Route::get('/college/staff/admissions', [AdmissionController::class, 'getAdmission']);
    Route::get('/application/{id}', [AdmissionController::class, 'getApplicationDetails']);
    Route::put('/application/update/{id}', [AdmissionController::class, 'updateAdmissionStatus']);
    Route::post('/college/create/depertment', [DepertmentController::class, 'createDepertment']);
    Route::get('/get/depertments', [DepertmentController::class, 'getDepertments']);
    Route::get('/depertment/course/{id}', [CoursesController::class, 'getDepertmentCourses']);
    Route::get('/course/applications/{id}', [AdmissionController::class, 'getCourseApplication']);
    Route::get('/course/selected/application/{id}', [AdmissionController::class, 'getSelectedApplication']);
    Route::get('/course/confirm/student/{id}', [AdmissionController::class, 'getConfirmStudentList']);
    Route::post('/image/upload/cover', [CourseImgController::class, 'AddCourseCoverImg']);
    Route::post('/image/upload/other', [CourseImgController::class, 'courseOtherImageUpload']);
    Route::post('/image/upload/logo', [CourseImgController::class, 'AddCourseLogoImg']);
    Route::post('/image/college/logo', [CollegeImageController::class, 'AddCollegeLogoImg']);
    Route::post('/image/college/cover', [CollegeImageController::class, 'AddCollegeCoverImg']);
    Route::post('/image/college/other', [CollegeImageController::class, 'collegeOtherImageUpload']);
    Route::delete('/image/delete/{id}', [CollegeImageController::class, 'DeleteGalarryImage']);
    Route::get('/course/payments/details/{id}', [NewPaymentController::class, 'coursePaymentDetails']);
    Route::post('/course/payments/close', [NewPaymentController::class, 'CloseFeesStatus']);
    Route::post('/add/notic', [UniversityController::class, 'AddNotice']);
    Route::delete('/delete/notic/{id}', [UniversityController::class, 'deleteNotice']);
    Route::get('/get/notic', [UniversityController::class, 'GetCollegeNots']);
   
  

});







Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
