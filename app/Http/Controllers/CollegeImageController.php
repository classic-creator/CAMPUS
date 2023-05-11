<?php

namespace App\Http\Controllers;

use App\Models\collegeImage;
use App\Models\Universitys;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CollegeImageController extends Controller
{
    //upload college cover image
    public function AddCollegeCoverImg(Request $request)
    {


        $validator = Validator::make($request->all(), [



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
        $user = $request->user();
        $college = Universitys::where('create-by', $user['id'])->first();
        $college_id = $college['id'];
        if ($request->has('image_path')) {
            $image_name = $request->input('image_name');

            $image_path = $request->file('image_path')
              


                ->store('college_images');

            collegeImage::create([
                'image_name' => $image_name,
                'image_path' => $image_path,
                'college_id' => $college_id,
                'type' => 'cover',
            ]);

            collegeImage::where('college_id', $college_id)
            ->where('type', 'cover')
            ->whereNotIn('id', [collegeImage::where('college_id', $college_id)->max('id')])
            ->delete();
           
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

//upload collegess images for galarry
    public function collegeOtherImageUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'image_*' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }


        $images = [];

        foreach ($request->allFiles() as $key => $file) {
            if (Str::startsWith($key, 'image_')) {
                $image_path = $file->store('college_images');
                $images[] = [
                    'path' => $image_path,
                    'name' => $file->getClientOriginalName(),
                ];
            }
        }
        $user = $request->user();
        $college = Universitys::where('create-by', $user['id'])->first();
        $college_id = $college['id'];
        if (!empty($images)) {
            
            foreach ($images as $image) {
                collegeImage::create([
                    'image_path' => $image['path'],
                    'image_name' => $image['name'],
                    'college_id' => $college_id,
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

    public function AddCollegeLogoImg(Request $request)
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

        $user = $request->user();
        $college = Universitys::where('create-by', $user['id'])->first();
        $college_id = $college['id'];
        if ($request->has('image_path')) {
            $image_name = $request->input('image_name');

            $image_path = $request->file('image_path')->store('college_images');
                // $name=time().'.'.$image_path->getClientOriginalExtension();

                // $image_path->move('images/',$name);



                

                collegeImage::create([
                'image_name' => $image_name,
                'image_path' => $image_path,
                'college_id' => $college_id,
                'type' => 'logo',
            ]);

            collegeImage::where('college_id', $college_id)
            ->where('type', 'logo')
            ->whereNotIn('id', [collegeImage::where('college_id', $college_id)->max('id')])
            ->delete();
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

    //


}