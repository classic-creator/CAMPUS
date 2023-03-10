<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use App\Models\Universitys;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

class CoursesController extends Controller
{
    //register  course
    public function courseRegister(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'courseName' => 'required',
            'fees' => 'required',
            'duration' => 'required',
            'eligibility' => 'required',

        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()

            ];
            return response()->json($response, 404);
        }

        $user = $request->user();

        $college = Universitys::where('create-by', $user['id'])->first();

        if (!$college) {
            $response = [

                'success' => false,
                'message' => "Add your college first",

            ];
            return response()->json($response, 404);
        }

        $course = Courses::create([
            'courseName' => $request->courseName,
            'fees' => $request->fees,
            'duration' => $request->duration,
            'eligibility' => $request->eligibility,
            'college_id' => $college['id'],
        ]);


        $response = [

            'success' => true,
            'message' => "Course registration success",
            $college['collegeName'],
            $course
        ];
        return response()->json($response, 201);
    }

    // get course details public
    public function getCourseDetails(Request $request, $id)
    {

        $course = Courses::where('id', $id)->first();
        if (!$course) {
            $response = [
                'success' => false,
                'message' => 'course not found'
            ];
            return response()->json($response, 200);
        }

        $response = [
            'success' => true,
            'courses' => $course
        ];
        return response()->json($response, 200);

    }

    // get course details for authorize user (manager/collegestuff)

    public function getCourseDetailsForStuff(Request $request)
    {

        $user = $request->user();
        $college = Universitys::where('create-by', $user['id'])->first();
        if (!$college) {
            $response = [
                'success' => false,
                'message' => 'college not found'
            ];
            return response()->json($response, 404);
        }
        $courses = DB::table('courses')->where('college_id', $college['id'])->get();

        if (!$courses) {
            $response = [
                'success' => false,
                'message' => 'course not found'
            ];
            return response()->json($response, 404);
        }
        $response = [
            'success' => true,
            $courses
        ];
        return response()->json($response, 200);

    }

    // update course details

    public function updateCourseDetails(Request $request, $id)
    {

        $user = $request->user();
        $college = Universitys::where('create-by', $user['id'])->first();
        if (!$college) {
            $response = [
                'success' => false,
                'message' => 'college not found'
            ];
            return response()->json($response, 404);
        }
        $course = Courses::where('id', $id)->where('college_id', $college['id'])->first();
        // $course = DB::table('courses')->find($courseid);

        if (!$course) {
            $response = [
                'success' => false,
                'message' => "course not found"
            ];
            return response()->json($response, 404);
        }
        ;

        $validator = Validator::make($request->all(), [

            'courseName' => 'required',
            'fees' => 'required',
            'duration' => 'required',
            'eligibility' => 'required',
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
            "fees" => $request->input('fees'),
            "duration" => $request->input('duration'),
            "eligibility" => $request->input('eligibility'),
        ]);


        $course->save();

        $response = [
            'success' => true,
            $course,

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
            return response()->json($response, 404);
        }
        $course->delete();
        $response = [
            'success' => true,
            'message' => 'remove course successfully'

        ];
        return response()->json($response, 200);

    }
}