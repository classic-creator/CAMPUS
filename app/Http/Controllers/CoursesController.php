<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Courses;
use App\Models\Depertment;
use App\Models\Preference;
use App\Models\SeatStructure;
use App\Models\Universitys;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

class CoursesController extends Controller
{
    //register  course  
    public function courseRegister(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [

            'courseName' => 'required',
            'application_fees' => 'required|numeric',
            'admission_fees' => 'required|numeric',
            'duration' => 'required',
            'eligibility' => 'required',
            'seat_capacity' => 'required',


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
        $depertment = Depertment::where('id', $id)->first();

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
            'vacent_seat' => $request->seat_capacity,
            'college_id' => $college['id'],
            'depertment_id' => $depertment['id']
        ]);


        $totalSeats = $request->input('seat_capacity');
        $obcSeats = ceil($totalSeats * 0.1);
        $scSeats = ceil($totalSeats * 0.05);
        $stSeats = ceil($totalSeats * 0.05);
        $ewsSeats = ceil($totalSeats * 0.1);
        $otherSeats = ceil($totalSeats * 0.02);
        $openSeats = $totalSeats - ($obcSeats + $scSeats + $stSeats + $ewsSeats + $otherSeats);
        SeatStructure::create([
            "total_seat" => $totalSeats,
            "open" => $openSeats,
            "OBC" => $obcSeats,
            "SC" => $scSeats,
            "ST" => $stSeats,
            "EWS" => $ewsSeats,
            "other" => $otherSeats,
            "course_id" => $course['id']
        ]);

        $response = [

            'success' => true,
            'message' => "Course registration success",
            'course' => $course
        ];
        return response()->json($response, 201);
    }

    //get all courses public
    public function getAllCourses(Request $request)
    {

        $course = DB::table('courses')
            ->select('courses.id', 'courses.courseName', 'universitys.collegeName', 'depertments.depertment_name', 'courses.duration', 'courses.eligibility', 'courses.admission_fees', 'courses.application_fees', 'courses.seat_capacity', 'courses.active','universitys.address', 'universitys.city')
            
            ->join('universitys', 'universitys.id', '=', 'courses.college_id')
            ->join('depertments', 'depertments.id', '=', 'courses.depertment_id')
            ->where('courses.active',1);


        //search
        if ($keyword = $request->input('keyword')) {
            $course->whereRaw("courseName LIKE '%" . $keyword . "%'")
                ->orWhereRaw("collegeName LIKE '%" . $keyword . "%'")->orWhereRaw("address LIKE '%" . $keyword . "%'")
            ;
        }

        //filter 

        if ($fe = $request->input('fe')) {
            $course->orderBy("fees", $fe);
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
            'total_courses'=> $courses->count(),
            'courses' => $courses,
        ];
        return response()->json($response, 200);

    }



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

        $college1 = $preference['college1'] === 'null' ? null : $preference['college1'];
        $course1 = $preference['course1'] === 'null' ? null : $preference['course1'];
        $depertment1 = $preference['depertment1'] === 'null' ? null : $preference['depertment1'];


        $query = DB::table('courses')
            ->join('universitys', 'courses.college_id', '=', 'universitys.id')
            ->join('depertments', 'depertments.id', '=', 'courses.depertment_id')
            ->select('courses.*', 'universitys.collegeName', 'universitys.address', 'depertments.depertment_name')
            ->where('courses.active',1);


        if (isset($college1, $depertment1, $course1) && !empty($college1) && !empty($depertment1) && !empty($course1)) {
            $query->where('universitys.collegeName', 'LIKE', "%$college1%")
                ->where('depertments.depertment_name', 'LIKE', "%$depertment1%")
                ->where('courses.courseName', 'LIKE', "%$course1%");
        } else if (isset($college1, $depertment1) && !empty($college1) && !empty($depertment1) && empty($course1)) {
            $query->where('universitys.collegeName', 'LIKE', "%$college1%")
                ->where('depertments.depertment_name', 'LIKE', "%$depertment1%");
        } else if (isset($college1, $course1) && !empty($college1) && !empty($course1) && empty($depertment1)) {
            $query->where('universitys.collegeName', 'LIKE', "%$college1%")
                ->where('courses.courseName', 'LIKE', "%$course1%");
        } else if (isset($depertment1, $course1) && !empty($depertment1) && !empty($course1) && empty($college1)) {
            $query->where('depertments.depertment_name', 'LIKE', "%$depertment1%")
                ->where('courses.courseName', 'LIKE', "%$course1%");
        } else if (isset($college1) && !empty($college1) && empty($depertment1) && empty($course1)) {
            $query->where('universitys.collegeName', 'LIKE', "%$college1%");
        } else if (isset($depertment1) && !empty($depertment1) && empty($course1) && empty($college1)) {
            $query->where('depertments.depertment_name', 'LIKE', "%$depertment1%");
        } else if (isset($course1) && !empty($course1) && empty($depertment1) && empty($college1)) {
            $query->where('courses.courseName', 'LIKE', "%$course1%");
        }

        $course = $query->get();


        if (!$course) {
            $response = [
                'success' => false,
                'message' => "Failed to fetch preferred courses.",
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'preferCourses' => $course,
        ];
        return response()->json($response, 200);

    }


    // get course details public


    public function getCourseDetails(Request $request, $id)
    {


        $course = DB::table('courses')
            ->select(
                'courses.*',
                'universitys.address',
                'universitys.collegeName',
                'depertments.depertment_name',
                'seat_structures.OBC',
                'seat_structures.SC',
                'seat_structures.ST',
                'seat_structures.open',
                'seat_structures.total_seat',
                'seat_structures.EWS',
                'seat_structures.other',
                'college_images.image_path as cover_image_path',
                DB::raw("(SELECT image_path FROM college_images WHERE college_id = courses.college_id AND type = 'logo') as logo_image_path")
            )
            ->join('universitys', 'universitys.id', '=', 'courses.college_id')
            ->join('depertments', 'depertments.id', '=', 'courses.depertment_id')
            ->leftJoin('seat_structures', 'seat_structures.course_id', '=', 'courses.id')
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
            'courses' => $courses,

        ];
        return response()->json($response, 200);

    }

    //get courses for depertments

    public function getDepertmentCourses(Request $request, $id)
    {

        $depertment = Depertment::where('id', $id)->first();
        if (!$depertment) {
            $response = [
                'success' => false,
                'message' => 'depertment not found'
            ];
            return response()->json($response, 200);
        }
        $courses = DB::table('courses')->select('courses.*', 'depertments.depertment_name')->join('depertments', 'courses.depertment_id', '=', 'depertments.id')->where('courses.depertment_id', $depertment['id'])->get();

        if (!$courses) {
            $response = [
                'success' => false,
                'message' => 'course not found'
            ];
            return response()->json($response, 200);
        }
        $response = [
            'success' => true,
            'courses' => $courses
        ];
        return response()->json($response, 200);

    }


    // update course details

    public function updateCourseDetails(Request $request, $id)
    {

        $user = $request->user();
        $universitys=Universitys::where('create-by',$user->id)->first();

        if (!$universitys) {
            $response = [
                'success' => false,
                'message' => "institute not found"
            ];
            return response()->json($response, 200);
        }
        ;

        $course = Courses::
            where('courses.id', $id)
            ->where('college_id',$universitys['id'])
            ->first();

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
            'seat_capacity' => 'required',
            'application_fees' => 'required|numeric',
            'admission_fees' => 'required|numeric',
         
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first()
            ];
            return response()->json($response, 400);
        }
        ;
        if ($request->input('seat_capacity') > $course['seat_capacity']) {
            $vacent_seat = $course['vacent_seat'] + ($request->input('seat_capacity') - $course['seat_capacity']);
            $course->update(['vacent_seat' => $vacent_seat]);
            $course->save();
        }
        if ($request->input('seat_capacity') < $course['seat_capacity']) {
            $vacent_seat = $course['vacent_seat'] - ($course['seat_capacity'] - $request->input('seat_capacity'));
            $course->update(['vacent_seat' => $vacent_seat]);
            $course->save();
        }


        $course->update([
            "courseName" => $request->input('courseName'),

            "duration" => $request->input('duration'),
            "eligibility" => $request->input('eligibility'),
            "seat_capacity" => $request->input('seat_capacity'),
            "application_fees" => $request->input('application_fees'),
            "admission_fees" => $request->input('admission_fees'),
      
        ]);


        $course->save();

        $seat_structure = SeatStructure::where('course_id', $course['id'])->first();


        $totalSeats = $request->input('seat_capacity');
        $obcSeats = ceil($totalSeats * 0.1);
        $scSeats = ceil($totalSeats * 0.05);
        $stSeats = ceil($totalSeats * 0.05);
        $ewsSeats = ceil($totalSeats * 0.1);
        $otherSeats = ceil($totalSeats * 0.02);
        $openSeats = $totalSeats - ($obcSeats + $scSeats + $stSeats + $ewsSeats + $otherSeats);

        $seat_structure->update([
            // SeatStructure::create([  
            "total_seat" => $totalSeats,
            "open" => $openSeats,
            "OBC" => $obcSeats,
            "SC" => $scSeats,
            "ST" => $stSeats,
            "EWS" => $ewsSeats,
            "other" => $otherSeats,
            "course_id" => $course['id']
        ]);

        $seat_structure->save();


        $response = [
            'success' => true,
            'course' => $course,

        ];
        return response()->json($response, 200);

    }

    //upload seat Structure


    public function uploadSeatStructure(Request $request, $id)
    {



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
            'total_seat' => 'required'
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        ;

        $totalSeats = $request->input('total_seat');
        $obcSeats = ceil($totalSeats * 0.1);
        $scSeats = ceil($totalSeats * 0.05);
        $stSeats = ceil($totalSeats * 0.05);
        $ewsSeats = ceil($totalSeats * 0.1);
        $otherSeats = ceil($totalSeats * 0.02);
        $openSeats = $totalSeats - ($obcSeats + $scSeats + $stSeats + $ewsSeats + $otherSeats);
        SeatStructure::create([
            "total_seat" => $totalSeats,
            "open" => $openSeats,
            "OBC" => $obcSeats,
            "SC" => $scSeats,
            "ST" => $stSeats,
            "EWS" => $ewsSeats,
            "other" => $otherSeats,
            "course_id" => $course['id']
        ]);




        $response = [
            'success' => true,
            'messege' => 'successfully add seat Structure'

        ];
        return response()->json($response, 200);

    }


    //delete course
    public function DeActive(Request $request, $id)
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

        if($course->active==1){
       
        $course->update([
            'active'=>0
        ]); }
        else{
            $course->update([
                'active'=>1
            ]);
        }

        $course->save();

        $response = [
            'success' => true,
            'message' => 'Deactive course successfully'

        ];
        return response()->json($response, 200);

    }
}