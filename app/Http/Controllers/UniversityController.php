<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Courses;
use App\Models\Preference;
use App\Models\Universitys;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

class UniversityController extends Controller
{
    //register College
    public function registerCollege(Request $request)
    {

        //validate
        $validator = Validator::make($request->all(), [

            'collegeName' => 'required',
            'address' => 'required',
            'email' => 'required|email|unique:universitys',
            'rating' => 'required',
            'description' => 'required',


        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()

            ];
            return response()->json($response, 404);
        }
        $user = $request->user();

        $type = $user['type'];

        if ($user['type'] == 'admin') /*|| Universitys::where("create-by", $user['id'])->exists() )*/ { //one user can add only one college and admin account not add college
            $response = [
                'seccess' => false,
                'message' => 'you are not eligible for register college '
            ];
            return response()->json($response, 401);
        }

        $college = Universitys::create([
            'collegeName' => $request->collegeName,
            'address' => $request->address,
            'email' => $request->email,
            'rating' => $request->rating,
            'description' => $request->description,
            'create-by' => $user['id'],
        ]);


        if ($type == 'user') {

            $user->update([
                "type" => '2',
            ]);
        }
        $user->save();


        $response = [

            'success' => true,
            'message' => "registration success",
            $college,

        ];
        return response()->json($response, 201);
    }

    //get all college for public
    public function getAllCollege(Request $request)

    {
        $user = $request->user();
      
        $colleges = Universitys::query();
        $preference=Preference::where('student_id',$user['id'])->first();
    
        if($preference){  
            $colleges->where('collegeName', $preference['college_preference_1'] )->orWhere( 'collegeName', $preference['college_preference_2']) ->orWhere('collegeName' ,$preference['college_preference_3'] )->orWhere('address',$preference['address_preference_1']);
        };
        
        if ($keyword = $request->input('keyword')) {
            $colleges->WhereRaw("collegeName LIKE '%" . $keyword . "%'")->orWhereRaw("address LIKE '%" .  $keyword  . "%'")
            ;
        }


        $result = $colleges->get();
        $collegCounts = $result->count();
       
        if(!$result){
 
            $response = [
                'success' => false,
               'message'=>'No college found'
            ];
            return response()->json($response, 404);
        }
        $response = [
            'success' => true,
            'collegeCount'=> $collegCounts,
            'colleges'=> $result,
        ];
        return response()->json($response, 200);
      

    }

    //get all college for --admin

    public function getAllCollegeAdmin(Request $request)
    {
        $colleges = Universitys::all();
        $collegCounts = $colleges->count();
        $response = [
            'success' => true,
            'collegeCounts' => $collegCounts,
            'colleges' => $colleges,
        ];
        return response()->json($response, 200);

    }
    //get college details for public
    public function getCollegeDetails(Request $request, $id)
    {


        $college = Universitys::where('id', $id)->first();

        if (!$college) {
            $response = [
                'success' => false,
                'message' => "college not found"
            ];
            return response()->json($response, 404);
        }
        $course = DB::table('courses')->where('college_id', $college['id'])->get();

        if(!$course){
            $response = [
                'success' => false,
                'message' => "course not found"
            ];
            return response()->json($response, 404);
        }

        $response = [
            'success' => true,
            'college' => $college,
            'courses' => $course
        ];
        return response()->json($response, 200);
    }


    //get college details for college stuff
    public function getMyCollegeDetails(Request $request)
    {
        $user = $request->user();
        $userId = $user['id'];

        // $college=DB::table('universitys')->where('create-by',$userId)->get();   //if a user can add more then one college then use this code 

        $college = Universitys::where('create-by', $userId)->first(); // if a user add only one college then use this code 
        if (!$college) {
            $response = [
                'success' => false,
                'message' => 'college not found'
            ];
            return response()->json($response, 200);
        }
        $response = [
            'success' => true,
            $college
        ];
        return response()->json($response, 200);
    }

    //get college details --admin
    public function getAllCollegeDetailsAdmin(Request $request, $id)
    {

        $college = Universitys::where('id', $id)->first();
        if (!$college) {
            $response = [
                'success' => false,
                'message' => "college not found"
            ];
            return response()->json($response, 404);
        }
        $course = DB::table('courses')->where('college_id', $college['id'])->get();
      if (!$course) {
            $response = [
                'success' => false,
                'message' => "course not found"
            ];
            return response()->json($response, 404);
        }

        $response = [
            'success' => true,
            'collegeDetails' => $college,
            'courses' => $course
        ];
        return response()->json($response, 200);

    }

    //update details
    public function updateCollegeDetails(Request $request)
    {


        $user = $request->user();

        $college = Universitys::where('create-by', $user['id'])->first();

        if (!$college) {
            $response = [
                'success' => false,
                'message' => "college not found"
            ];
            return response()->json($response, 404);
        }
        ;


        $validator = Validator::make($request->all(), [

            'collegeName' => 'required',
            'address' => 'required',
            'description' => 'required'
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        ;

        //   $college->collegeName=$request->collegeName;
        //   $college->email=$request->email;
        //   $college->address=$request->address;
        //   $college->description=$request->description;

        $college->update([
            "collegeName" => $request->input('collegeName'),
            "address" => $request->input('address'),
            "description" => $request->input('description'),
        ]);
        $college->save();

        $response = [
            'success' => true,
            $college,

        ];
        return response()->json($response, 200);
    }




    //delete college 
    public function deleteCollege(Request $request)
    {

        $user = $request->user();

        Universitys::where('create-by', $user['id'])->delete();


        $response = [
            'success' => true,
            'message' => 'delete college successfully'

        ];
        return response()->json($response, 200);
    }
}