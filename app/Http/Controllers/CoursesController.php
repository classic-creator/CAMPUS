<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Courses;
use App\Models\Depertment;
use App\Models\Preference;
use App\Models\Universitys;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

class CoursesController extends Controller
{
    //register  course
    public function courseRegister(Request $request ,$id)
    {

        $validator = Validator::make($request->all(), [

            'courseName' => 'required',
            'application_fees' => 'required|numeric',
            'admission_fees' => 'required|numeric',
            'duration' => 'required',
            'eligibility' => 'required',
            'seat_capacity'=>'required',


        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first()

            ];
            return response()->json($response, 200);
        }

        $user = $request->user();

        $college = Universitys::where('create-by', $user['id'])->first();
        $depertment=Depertment::where('id',$id)->first();

        if (!$college && !$depertment) {
            $response = [

                'success' => false,
                'message' => "Add your college/depertment first",

            ];
            return response()->json($response, 200);
        }

        $course = Courses::create([
            'courseName' => $request->courseName,
            'admission_fees' => $request->admission_fees,   
            'application_fees' => $request->application_fees,
            'duration' => $request->duration,
            'eligibility' => $request->eligibility,
            'seat_capacity' => $request->seat_capacity,
            'college_id' => $college['id'],
            'depertment_id'=>$depertment['id']
        ]);


        $response = [

            'success' => true,
            'message' => "Course registration success",
              'course'=> $course
        ];
        return response()->json($response, 201);
    }

    //get all courses public
    public function getAllCourses(Request $request)
    {

        $course = DB::table('courses')
            ->select('courses.id','courses.courseName', 'universitys.collegeName','depertments.depertment_name', 'courses.duration', 'courses.eligibility', 'courses.admission_fees','courses.application_fees','courses.seat_capacity','universitys.address')
            ->join('universitys', 'universitys.id', '=', 'courses.college_id') ->join('depertments', 'depertments.id', '=', 'courses.depertment_id');


     //search
            if($keyword = $request->input('keyword')) {
            $course->whereRaw("courseName LIKE '%" . $keyword . "%'")
            ->orWhereRaw("collegeName LIKE '%" . $keyword . "%'")->orWhereRaw("address LIKE '%" . $keyword . "%'")
            ;
        }

        //filter 
        
        if($fe =$request->input('fe')){
            $course->orderBy("fees",$fe);
        }
        
        $courses = $course->get();

        if (!$courses) {

            $response = [
                'success' => true,
                'message' => 'no course found',
            ];
            return response()->json($response, 200);
        }
        $response = [
            'success' => true,
            'courses' => $courses
        ];
        return response()->json($response, 200);

    }


    //get courses with preference

//     public function getPreferedCourses(Request $request){

//         $user=$request->user();

//         $preference=Preference::where("student_id",$user['id'])->first();

//         if($preference){
//             // DB::table('courses')
//             $course = Courses::
//             select('courses.id','courses.courseName', 'universitys.collegeName', 'courses.duration', 'courses.eligibility', 'courses.admission_fees','courses.application_fees','courses.seat_capacity','universitys.address')
//             ->join('universitys', 'universitys.id', '=', 'courses.college_id')
            
//              ->where('courses.courseName',$preference['course1'])->orwhere ('courses.courseName',$preference['course2'])->orWhere('courses.courseName',$preference['course3'])
//              // ->orWhere('universitys.address',$preference['address_preference_1'])
//              ;

//              if($course){
//              if($keyword = $request->input('keyword')) {
//                  $course->where('courseName', 'like', "%{$keyword}%")
//                  ->orWhere('collegeName', 'like', "%{$keyword}%")
//                  ->orWhere('address', 'like',"%{$keyword}%")
//                  ;
//              }



//              if($fe =$request->input('fe')){

//                 $course->orderBy("fees",$fe);
//             }
    
    
//                 $preferCourses=$course->get();
        
//                 $response = [
//                     'success' => true,
//                     'preferCourses' => $preferCourses,
//                 ];
//                 return response()->json($response, 200);}


//   $response = [
//             'success' => false,
//             'message' => "Please update Preference",
//         ];
//         return response()->json($response, 200);

//         }

//         $response = [
//             'success' => false,
//             'message' => "Please update Preference",
//         ];
//         return response()->json($response, 200);

//     }

public function getPreferedCourses(Request $request)
{
    $user = $request->user();

    $preference = Preference::where("student_id", $user['id'])->first();

    if (!$preference) {
        $response = [
            'success' => false,
            'message' => "Please update Preference",
        ];
        return response()->json($response, 200);
    }
    // $course = DB::table('courses')->join('universitys', 'courses.college_id', '=', 'universitys.id')
    // ->select('courses.*','universitys.collegeName')
    // ->where(function ($query) use ($preference) {
    //     $query->where(DB::raw('LOWER(courseName)'), 'LIKE', strtolower($preference['course1']))
    //           ->orWhere(DB::raw('LOWER(courseName)'), 'LIKE', strtolower($preference['course2']))
    //           ->orWhere(DB::raw('LOWER(courseName)'), 'LIKE', strtolower($preference['course3']));
    // })
    // ->orderBy('id') // Replace "id" with the name of a unique column
    // ->get();
  
  
    // $course = DB::table('courses')
    // ->join('universitys', 'courses.college_id', '=', 'universitys.id')
    // ->select('courses.*', 'universitys.collegeName')
    // ->where(function ($query) use ($preference) {
    //     $query->where(DB::raw('LOWER(courseName)'), 'LIKE', strtolower($preference['course1']))
    //         ->orWhere(DB::raw('LOWER(courseName)'), 'LIKE', strtolower($preference['course2']))
    //         ->orWhere(DB::raw('LOWER(courseName)'), 'LIKE', strtolower($preference['course3']));
       
    // })
    // ->when(!empty($preference['college1']), function ($query) use ($preference) {
    //     return $query->where('universitys.collegeName', '=', $preference['college1']);
    // })  
    // ->orderBy('courses.id') // Replace "id" with the name of a unique column in the courses table
    // ->get();
   
//success fully run for 2 college
    // $course = DB::table('courses')
    // ->join('universitys', 'courses.college_id', '=', 'universitys.id')
    // ->select('courses.*', 'universitys.collegeName')
    // ->where(function ($query) use ($preference) {
    //     $query->where(DB::raw('LOWER(courseName)'), 'LIKE', strtolower($preference['course1']))
    //         ->orWhere(DB::raw('LOWER(courseName)'), 'LIKE', strtolower($preference['course2']))
    //         ->orWhere(DB::raw('LOWER(courseName)'), 'LIKE', strtolower($preference['course3']));
       
    // })
    // ->when(!empty($preference['college1']), function ($query) use ($preference) {
    //     return $query->where('universitys.collegeName', '=', $preference['college1']);
    // })  
    // ->when(!empty($preference['college2']), function ($query) use ($preference) {
    //     return $query->orWhere(function ($query) use ($preference) {
    //         $query->where('universitys.collegeName', '=', $preference['college2'])
    //               ->whereIn(DB::raw('LOWER(courseName)'), array_map('strtolower', [
    //                   $preference['course1'],
    //                   $preference['course2'],
    //                   $preference['course3'],
    //               ]));
    //     });
    // })
    // ->orderBy('courses.id')
    // ->get();


//for 3 college
$course = DB::table('courses')
    ->join('universitys', 'courses.college_id', '=', 'universitys.id')
    ->select('courses.*', 'universitys.collegeName')
    ->where(function ($query) use ($preference) {
        $query->where(DB::raw('LOWER(courseName)'), 'LIKE', strtolower($preference['course1']))
            ->orWhere(DB::raw('LOWER(courseName)'), 'LIKE', strtolower($preference['course2']))
            ->orWhere(DB::raw('LOWER(courseName)'), 'LIKE', strtolower($preference['course3']));
    })
    ->when(!empty($preference['college1']), function ($query) use ($preference) {
        return $query->where('universitys.collegeName', '=', $preference['college1']);
    })  
    ->when(!empty($preference['college2']), function ($query) use ($preference) {
        return $query->orWhere(function ($query) use ($preference) {
            $query->where('universitys.collegeName', '=', $preference['college2'])
                  ->whereIn(DB::raw('LOWER(courseName)'), array_map('strtolower', [
                      $preference['course1'],
                      $preference['course2'],
                      $preference['course3'],
                  ]));
        });
    })
    ->when(!empty($preference['college3']), function ($query) use ($preference) {
        return $query->orWhere(function ($query) use ($preference) {
            $query->where('universitys.collegeName', '=', $preference['college3'])
                  ->whereIn(DB::raw('LOWER(courseName)'), array_map('strtolower', [
                      $preference['course1'],
                      $preference['course2'],
                      $preference['course3'],
                  ]));
        });
    })
    ->orderBy('courses.id')
    ->get();


            if(!$course){
            $response = [
                'success' => false,
                'message' => "Failed to fetch preferred courses.",
            ];
            return response()->json($response, 500);}
            
            $response = [
                'success' => true,
                'preferCourses' => $course,
            ];
            return response()->json($response, 200);
        
}


    // get course details public


    public function getCourseDetails(Request $request, $id)
    {

        // $course = Courses::where('id', $id)->first();

        // $course = DB::table('courses')
        // ->select('courses.id','universitys.id','courses.courseName','courses.depertment_id','courses.seat_capacity', 'universitys.collegeName', 'courses.duration', 'courses.eligibility',  'courses.admission_fees','courses.application_fees','courses.seat_capacity','universitys.address')
        // ->join('universitys', 'universitys.id', '=', 'courses.college_id')->where('courses.id', $id)->first();
        $course = DB::table('courses')
        ->select('courses.*', 'universitys.address','depertments.depertment_name', 'college_images.image_path as cover_image_path', 
            DB::raw("(SELECT image_path FROM college_images WHERE college_id = courses.college_id AND type = 'logo') as logo_image_path"))
        ->join('universitys', 'universitys.id', '=', 'courses.college_id')
        ->join('depertments', 'depertments.id', '=', 'courses.depertment_id')
        ->leftJoin('college_images', function ($join) {
            $join->on('college_images.college_id', '=', 'courses.college_id')
                 ->where('college_images.type', '=', 'cover');
        })
        ->where('courses.id', $id)
        ->first();



        if (!$course) {
            $response = [
                'success' => false,
                'message' => 'course not found'
            ];
            return response()->json($response, 404);
        }
        $course->cover_image_url = $course->cover_image_path ? url($course->cover_image_path) : null;
        $course->logo_image_url = $course->logo_image_path ? url($course->logo_image_path) : null;
        
        $response = [
            'success' => true,
            'course' => $course
        ];
        return response()->json($response, 200);

    }

    // get course  for authorize user (manager/collegestuff)

    public function getCollegeCourses(Request $request)
    {

        $user = $request->user();

        $college = Universitys::where('create-by', $user['id'])->first();
      
       
        $courses = DB::table('courses')->where('college_id', $college['id'])->get();
    //   $application=Admission::get();

        if (!$courses) {
            $response = [
                'success' => false,
                'message' => 'course not found'
            ];
            return response()->json($response, 200);
        }
        $response = [
            'success' => true,
          'courses'=>  $courses,

        ];
        return response()->json($response, 200);

    }

    //get courses for depertments

    public function getDepertmentCourses(Request $request,$id)
    {

      $depertment = Depertment::where('id', $id)->first();
        if (!$depertment) {
            $response = [
                'success' => false,
                'message' => 'depertment not found'
            ];
            return response()->json($response, 200);
        }
        $courses = DB::table('courses')->where('depertment_id', $depertment['id'])->get();

        if (!$courses) {
            $response = [
                'success' => false,
                'message' => 'course not found'
            ];
            return response()->json($response, 200);
        }
        $response = [
            'success' => true,
             'courses'=> $courses
        ];
        return response()->json($response, 200);

    }


    // update course details

    public function updateCourseDetails(Request $request, $id)
    {

        $user = $request->user();
       
        $course = Courses::where('id', $id)->first();
        // $course = DB::table('courses')->find($courseid);

        if (!$course) {
            $response = [
                'success' => false,
                'message' => "course not found"
            ];
            return response()->json($response, 200);
        }
        ;

        $validator = Validator::make($request->all(), [

            'courseName' => 'required',
           
            'duration' => 'required|numeric',
            'eligibility' => 'required',
            'seat_capacity'=>'required',
            'application_fees' => 'required|numeric',
            'admission_fees' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        ;

        $course->update([
            "courseName" => $request->input('courseName'),
          
            "duration" => $request->input('duration'),
            "eligibility" => $request->input('eligibility'),
            "seat_capacity" => $request->input('seat_capacity'),
            "application_fees" => $request->input('application_fees'),
            "admission_fees" => $request->input('admission_fees'),
        ]);


        $course->save();

        $response = [
            'success' => true,
            'course'=> $course,

        ];
        return response()->json($response, 200);

    }
    //delete course
    public function deleteCourse(Request $request, $id)
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
        $course = Courses::where('id', $id)->where('college_id', $college['id'])->first();
        if (!$course) {
            $response = [
                'success' => false,
                'message' => ' course not found'

            ];
            return response()->json($response, 200);
        }
        $course->delete();
        $response = [
            'success' => true,
            'message' => 'remove course successfully'

        ];
        return response()->json($response, 200);

    }
}