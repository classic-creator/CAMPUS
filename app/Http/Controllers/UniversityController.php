<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\collegeImage;
use App\Models\Courses;
use App\Models\Links;
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

        if ($user['type'] == 'admin') /*|| Universitys::where("create-by", $user['id'])->exists() )*/{ //one user can add only one college and admin account not add college
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
            'college' => $college

        ];
        return response()->json($response, 201);
    }

    //get all college for public
    public function getAllCollege(Request $request)
    {
        // $colleges = Universitys::join('college_images','college_images.college_id','=','universitys.id')->select('universitys.*','college_images.image_path');
        $colleges = Universitys::leftJoin('college_images', function ($join) {
            $join->on('college_images.college_id', '=', 'universitys.id')
                ->where('college_images.type', '=', 'cover');
        })
            ->select('universitys.*', 'college_images.image_path');

        if ($keyword = $request->input('keyword')) {
            $colleges->WhereRaw("collegeName LIKE '%" . $keyword . "%'")->orWhereRaw("address LIKE '%" . $keyword . "%'");
        }


        $result = $colleges->get();

        if (!$result) {

            $response = [
                'success' => false,
                'message' => 'No college found'
            ];
            return response()->json($response, 200);
        }

        foreach ($result as $college) {
            // $college->image_url = $college->image_path ? url('public/' . $college->image_path) : null;
            $college->image_url = $college->image_path ? url($college->image_path) : null;

        }

        $collegCounts = $result->count();

        $response = [
            'success' => true,
            'collegeCount' => $collegCounts,
            'colleges' => $result,
        ];
        return response()->json($response, 200);


    }


    //get college with preferences

    public function getPreferedCollege(Request $request)
    {

        $user = $request->user();

        $colleges = Universitys::query();

        $preference = Preference::where('student_id', $user['id'])->first();

        if ($preference) {
            $colleges->where('collegeName', $preference['college_preference_1'])->orWhere('collegeName', $preference['college_preference_2'])->orWhere('collegeName', $preference['college_preference_3'])->orWhere('address', $preference['address_preference_1']);




            if ($keyword = $request->input('keyword')) {
                $colleges->WhereRaw("collegeName LIKE '%" . $keyword . "%'")->orWhereRaw("address LIKE '%" . $keyword . "%'")
                ;
            }

            $preferedcollege = $colleges->get();
            $response = [

                'success' => false,
                'preferedCollege' => $preferedcollege
            ];
            return response()->json($response, 200);
        }


        $response = [
            'success' => false,
            'message' => 'No college found'
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
    //  public function getCollegeDetails(Request $request, $id)
    // {
    //     $college = Universitys::join('college_images', 'college_images.college_id', '=', 'universitys.id')
    //         ->select(
    //             'universitys.id as college_id','universitys.collegeName','universitys.address','universitys.description','universitys.rating',
    //             'college_images.image_path as cover_image_path',
    //             DB::raw("(SELECT image_path FROM college_images WHERE college_id = $id AND type = 'logo') as logo_image_path")
    //         )
    //         ->where('universitys.id', $id)
    //         ->where('college_images.type', 'cover')
    //         ->first();

    //     if (!$college) {
    //         $response = [
    //             'success' => false,
    //             'message' => "college not found"
    //         ];
    //         return response()->json($response, 200);
    //     }
    //     $course = DB::table('courses')
    //     ->select('courses.*','depertments.depertment_name','seat_structures.OBC',
    //     'seat_structures.SC',
    //     'seat_structures.ST',
    //     'seat_structures.open',
    //     'seat_structures.total_seat',
    //     'seat_structures.EWS',
    //     'seat_structures.other',)
    //     ->join('depertments' ,'depertments.id','=','courses.depertment_id')
    //     ->leftJoin('seat_structures', 'seat_structures.course_id', '=', 'courses.id')
    //     ->where('courses.college_id', $college->college_id)
    //     ->get();

    //     $photos = collegeImage::where('college_id', $id)->where('type', '=', 'other')->get();

    //     foreach ($photos as $photo) {
    //         // $college->image_url = $college->image_path ? url('public/' . $college->image_path) : null;
    //         $photo->image_url = $photo->image_path ? url($photo->image_path) : null;

    //     }

       
    //     $college->cover_image_url = $college->cover_image_path ? url($college->cover_image_path) : null;
    //     $college->logo_image_url = $college->logo_image_path ? url($college->logo_image_path) : null;


    //     $response = [
    //         'success' => true,
    //         'college' => $college,
    //         'courses' => $course,
    //         'photos' => $photos
    //     ];
    //     return response()->json($response, 200);
    // } 

    public function getCollegeDetails(Request $request, $id)
{
    $college = Universitys::select(
        'universitys.id as college_id', 'universitys.collegeName', 'universitys.address', 'universitys.description', 'universitys.rating'
    )
        ->where('universitys.id', $id)
        ->first();

    if (!$college) {
        $response = [
            'success' => false,
            'message' => "College not found"
        ];
        return response()->json($response, 200);
    }

    $course = DB::table('courses')
        ->select(
            'courses.*', 'depertments.depertment_name', 'seat_structures.OBC',
            'seat_structures.SC', 'seat_structures.ST', 'seat_structures.open',
            'seat_structures.total_seat', 'seat_structures.EWS', 'seat_structures.other'
        )
        ->join('depertments', 'depertments.id', '=', 'courses.depertment_id')
        ->leftJoin('seat_structures', 'seat_structures.course_id', '=', 'courses.id')
        ->where('courses.college_id', $college->college_id)
        ->get();

    $photos = collegeImage::where('college_id', $id)->where('type', '=', 'other')->get();

    foreach ($photos as $photo) {
        $photo->image_url = $photo->image_path ? url($photo->image_path) : null;
    }

    $college->cover_image_url = null; // Set default cover image URL
    $college->logo_image_url = null; // Set default logo image URL

    if ($college_images = Universitys::join('college_images', 'college_images.college_id', '=', 'universitys.id')
        ->select(
            'college_images.image_path as cover_image_path',
            DB::raw("(SELECT image_path FROM college_images WHERE college_id = $id AND type = 'logo') as logo_image_path")
        )
        ->where('universitys.id', $id)
        ->where('college_images.type', 'cover')
        ->first()
    ) {
        $college->cover_image_url = $college_images->cover_image_path ? url($college_images->cover_image_path) : null;
        $college->logo_image_url = $college_images->logo_image_path ? url($college_images->logo_image_path) : null;
    }

    $response = [
        'success' => true,
        'college' => $college,
        'courses' => $course,
        'photos' => $photos
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
        $courses = DB::table('courses')->where('college_id', $college['id'])->get();
        if (!$courses) {
            $response = [
                'success' => false,
                'message' => "course not found"
            ];
            return response()->json($response, 200);
        }
      
        $application = Admission::select('admissions.id', 'courses.courseName', 'universitys.collegeName','student_personal_data.first_name','student_personal_data.middle_name','student_personal_data.last_name','student_personal_data.qualification','student_personal_data.mark_obtain_lastExam', 'depertments.depertment_name')
        ->join('universitys', 'admissions.college_id', '=', 'universitys.id')
        ->join('courses', 'admissions.course_id', '=', 'courses.id')
        ->join('depertments', 'courses.depertment_id', '=', 'depertments.id')
        ->join('student_personal_data','student_personal_data.id','=','admissions.personalDetails_id')

    
       
        ->where('admissions.college_id', $college['id'])
        ->where('admission_status', 'confirmed')
        // ->distinct()
        ->get();


    
       
        $photos = collegeImage::where('college_id', $college['id'])->where('type', '=', 'other')->get();
        
        foreach ($photos as $photo) {
            $photo->image_url = $photo->image_path ? url($photo->image_path) : null;
        }
        
  

      $cover = collegeImage::where('college_id', $college['id'])->where('type', '=', 'cover')->first();
      if($cover){

          $cover->image_url = $cover->image_path ? url($cover->image_path) : null;
      }

      $logo = collegeImage::where('college_id', $college['id'])->where('type', '=', 'logo')->first();
      if($logo){

          $logo->image_url = $logo->image_path ? url($logo->image_path) : null;
      }


        $response = [
            'success' => true,
            'myCollege' => $college,
            'myCourses' => $courses,
            'clgConfirmApplication'=>$application,
            'photos'=>$photos,
            'cover_image'=>$cover ,
            'logo_image'=>  $logo
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
            return response()->json($response, 200);
        }
        $course = DB::table('courses')->where('college_id', $college['id'])->get();
        if (!$course) {
            $response = [
                'success' => false,
                'message' => "course not found"
            ];
            return response()->json($response, 200);
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
            return response()->json($response, 200);
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

//add importantas link
public function AddNotice(Request $request)
{


     $user=$request->user();
     $college=Universitys::where('create-by',$user['id'])->first();

    if (!$college){

        $response = [
            'success' => false,
            'message' => 'college not found'

        ];
        return response()->json($response, 200);
     };


    $validator = Validator::make($request->all(), [

            'title' => 'required',
            'link' => 'required',
        

        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()

            ];
            return response()->json($response, 404);
        };

    Links::Create([
    'title'=>$request->input('title'),
    'link'=>$request->input('link'),
    'college_id'=>$college['id']
   
    ]);

    $response = [
        'success' => true,
        'message' =>'add notic successfully'

    ];
    return response()->json($response, 201);

}

//get links for college

public function GetCollegeNots(Request $request)
{

     $user=$request->user();
     $college=Universitys::where('create-by',$user['id'])->first();

    if (!$college){

        $response = [
            'success' => false,
            'message' => 'college not found'

        ];
        return response()->json($response, 200);
     };


   $links=Links::where('college_id',$college['id'])->get();

  

    $response = [
        'success' => true,
        'notic' =>$links

    ];
    return response()->json($response, 200);

}

//get links for Public

public function GetNotics(Request $request,$id)
{

   $links=Links::where('college_id',$id)->get();

  

    $response = [
        'success' => true,
        'notic' =>$links

    ];
    return response()->json($response, 200);

}
public function deleteNotice(Request $request,$id)
{


     $user=$request->user();
     $college=Universitys::where('create-by',$user['id'])->first();


     if (!$college){

        $response = [
            'success' => false,
            'message' => 'college not found'

        ];
        return response()->json($response, 200);
     };

     Links::where('college_id', $college['id'])->where('id',$id)->delete();

    $response = [
        'success' => true,
        'message' => 'Delete notic successfully'

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