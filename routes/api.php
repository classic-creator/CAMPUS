<?php

use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\StudentDetails;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepertmentController;
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
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/sent-reset-password-email',[PasswordResetController::class,'forgetPassword']);
Route::put('/reset-password/{token}',[PasswordResetController::class,'resetPassword']);
Route::get('/colleges',[UniversityController::class,'getAllCollege']);
Route::get('/courses',[CoursesController::class,'getAllCourses']);
Route::get('/college/{id}',[UniversityController::class,'getCollegeDetails']);
Route::get('/college/course/{id}',[CoursesController::class,'getCourseDetails']);




//login protected routes
Route::middleware(['auth:sanctum'])->group(function(){
    
    Route::get('/preference/courses',[CoursesController::class,'getPreferedCourses']);
    Route::post('/logout',[UserController::class,'logout']);
    Route::get('/getProfile',[UserController::class,'getProfile']);
    Route::put('/changepassword',[UserController::class,'changePassword']);
    Route::post('/collegeregister', [UniversityController::class, 'registerCollege']);
    Route::post('/apply/course/{id}', [AdmissionController::class, 'applyAdmission']);
    Route::post('/add/preference', [PreferenceController::class, 'addPreference']);
    Route::get('/get/preference', [PreferenceController::class, 'getPreferences']);
    Route::post('/update/preference', [PreferenceController::class, 'updatePreference']);
    Route::post('/register/personalDetails', [StudentDetails::class, 'RegisterStudentPersonalDetails']);
    Route::get('/personalDetails', [StudentDetails::class, 'getStudentPersonalDetails']);
    Route::post('/register/educationalDetails', [StudentDetails::class, 'registerStudentEducationalDetails']);
    Route::get('/get/educationalDetails', [StudentDetails::class, 'GetStudentEducation']);
    Route::post('/register/address', [StudentDetails::class, 'registerStudentAddress']);
    Route::get('/get/address', [StudentDetails::class, 'getStudentAddress']);
    Route::get('/get/applications', [AdmissionController::class, 'getMyapplications']);
    Route::get('/admission/payment/{id}', [AdmissionController::class, 'AdmissionPayment']);
});

// admin protected routes
Route::middleware(['auth:sanctum','user-access:admin'])->group(function(){
    
    Route::get('/admin/colleges',[UniversityController::class,'getAllCollegeAdmin']);
    Route::get('/admin/colleges/{id}',[UniversityController::class,'getAllCollegeDetailsAdmin']);
    Route::get('/admin/users',[UserController::class,'getAllUsersAdmin']);
    Route::put('/admin/users/{id}',[UserController::class,'updateUsersAdmin']);
    
});




// manager protected routes
Route::middleware(['auth:sanctum','user-access:manager'])->group(function(){
    
    Route::put('/college/staff/update', [UniversityController::class, 'updateCollegeDetails']);
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
});







Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
