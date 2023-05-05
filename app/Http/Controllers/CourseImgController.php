<?php

namespace App\Http\Controllers;

use App\Models\CourseImg;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseImgController extends Controller
{
    //add image 
    public function AddCourseCoverImg(Request $request)
    {


        $validator = Validator::make($request->all(), [

            'image_name' => 'required',

            'image_path' => 'required',


        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()

            ];
            return response()->json($response, 400);
        }
        ;

        if ($request->has('image_path')) {
            $image_name = $request->input('image_name');
            $course_id = $request->input('course_id');
            $image_path = $request->file('image_path')
                // $name=time().'.'.$image_path->getClientOriginalExtension();

                // $image_path->move('images/',$name);



                ->store('course_images');

            CourseImg::create([
                'image_name' => $image_name,
                'image_path' => $image_path,
                'course_id' => $course_id,
                'type' => 'cover',
            ]);

            // DB::table('course_imgs')->insert([
            //     'image_name'=>$image_name,
            //     'image_path'=>$image_path,
            //     'course_id'=>$id,
            //     'type'=>'cover',
            //   ]); 
            $response = [

                'success' => true,
                'message' => "upload image successfully",

            ];
            return response()->json($response, 201);
        }

        $response = [

            'success' => true,
            'message' => "file not upload",

        ];
        return response()->json($response, 400);
    }


    public function courseOtherImageUpload(Request $request)
{
    $validator = Validator::make($request->all(), [
        'course_id' => 'required',
        'image_*' => 'required|image|max:2048',
    ]);

    if ($validator->fails()) {
        $response = [
            'success' => false,
            'message' => $validator->errors()
        ];
        return response()->json($response, 400);
    }

    $course_id = $request->input('course_id');
    $images = [];

    foreach ($request->allFiles() as $key => $file) {
        if (Str::startsWith($key, 'image_')) {
            $image_path = $file->store('course_images');
            $images[] = [
                'path' => $image_path,
                'name' => $file->getClientOriginalName(),
            ];
        }
    }

    if (!empty($images)) {
        foreach ($images as $image) {
            CourseImg::create([
                'image_path' => $image['path'],
                'image_name' => $image['name'],
                'course_id' => $course_id,
                'type' => 'other',
            ]);
        }

        $response = [
            'success' => true,
            'message' => "Uploaded image(s) successfully",
        ];
        return response()->json($response, 201);
    } else {
        $response = [
            'success' => false,
            'message' => "No image(s) were uploaded",
        ];
        return response()->json($response, 400);
    }
}


//logo image upload

public function AddCourseLogoImg(Request $request)
{


    $validator = Validator::make($request->all(), [

        // 'image_name' => 'required',
        'image_path' => 'required',


    ]);
    if ($validator->fails()) {
        $response = [
            'success' => false,
            'message' => $validator->errors()

        ];
        return response()->json($response, 400);
    }
    ;

    if ($request->has('image_path')) {
        $image_name = $request->input('image_name');
        $course_id = $request->input('course_id');
        $image_path = $request->file('image_path')
            // $name=time().'.'.$image_path->getClientOriginalExtension();

            // $image_path->move('images/',$name);



            ->store('course_images');

        CourseImg::create([
            'image_name' => $image_name,
            'image_path' => $image_path,
            'course_id' => $course_id,
            'type' => 'logo',
        ]);

        // DB::table('course_imgs')->insert([
        //     'image_name'=>$image_name,
        //     'image_path'=>$image_path,
        //     'course_id'=>$id,
        //     'type'=>'cover',
        //   ]); 
        $response = [

            'success' => true,
            'message' => "upload image successfully",

        ];
        return response()->json($response, 201);
    }

    $response = [

        'success' => true,
        'message' => "file not upload",

    ];
    return response()->json($response, 400);
}
}