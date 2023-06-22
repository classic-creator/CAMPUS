<?php

namespace App\Http\Controllers;

use App\Models\collegeImage;
use App\Models\Universitys;
use App\Models\websiteImg;
use Storage;
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

    //galarry image delete 

    public function DeleteGalarryImage(Request $request,$id){


        $user = $request->user();
        $college = Universitys::where('create-by', $user['id'])->first();
        
        $collegeImage = collegeImage::where('id', $id)->where('college_id', $college['id'])->first();
        
        if ($collegeImage) {
            // Delete image from storage
            Storage::delete($collegeImage->image_path);
        
            // Delete image from the database
            $collegeImage->delete();
        
            $response = [
                'success' => true,
                'message' => 'Image deleted successfully'
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'message' => 'Image not found'
            ];
            return response()->json($response, 404);
        }
        
    }

    //landing page super Admin carousel image upload 

    public function addCarousel(Request $request){

        $validator = Validator::make($request->all(), [

            'image_path' => 'required|image',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        if ($request->has('image_path')) {
            
            $image_path = $request->file('image_path')->store('website_img');
            $image_link = $request->input('image_link') ? $request->input('image_link') : $request->image_path->getClientOriginalName();
            

                websiteImg::create([
                'link' =>  $image_link,
                'image_path' => $image_path,
            
                'type' => 'carousel',
            ]);

           
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

public function getCarousel(Request $request){


    $photos = websiteImg::where('type', '=', 'carousel')->get();
        
    foreach ($photos as $photo) {
        $photo->image_url = $photo->image_path ? url($photo->image_path) : null;
    }

    $response=[
        'success'=>true,
        'photos'=>$photos
    ];
    return response()->json($response,200);

}

//delete carousel image

public function DeleteCarouselImage(Request $request,$id){



    
    $collegeImage = websiteImg::where('id', $id)->first();
    
    if ($collegeImage) {
        // Delete image from storage
        Storage::delete($collegeImage['image_path']);
    
        // Delete image from the database
        $collegeImage->delete();
    
        $response = [
            'success' => true,
            'message' => 'Image deleted successfully'
        ];
        return response()->json($response, 200);
    } else {
        $response = [
            'success' => false,
            'message' => 'Image not found'
        ];
        return response()->json($response, 404);
    }
    
}

// super admin educational scheme upload 
public function addScheme(Request $request){

    $validator = Validator::make($request->all(), [

        'image_path' => 'required|image',
        'image_link'=>'required',
        'name'=>'required'
    ]);

    if ($validator->fails()) {
        $response = [
            'success' => false,
            'message' => $validator->errors()
        ];
        return response()->json($response, 400);
    }

    if ($request->has('image_path')) {
        
        $image_path = $request->file('image_path')->store('website_img');
        $image_link = $request->input('image_link');
        

            websiteImg::create([
            'link' =>  $image_link,
            'image_path' => $image_path,
            'type' => 'scheme',
            'name' => $request->input('name'),
        ]);

       
        $response = [

            'success' => true,
            'message' => "upload Scheme successfully",

        ];
        return response()->json($response, 201);
    }

    $response = [

        'success' => true,
        'message' => "file not upload",

    ];
    return response()->json($response, 400);


}

// super admin educational scheme get 
public function GetScheme(Request $request){

   
    $photos = websiteImg::where('type', '=', 'scheme')->get();
        
    foreach ($photos as $photo) {
        $photo->image_url = $photo->image_path ? url($photo->image_path) : null;
    }

    $response=[
        'success'=>true,
        'scheme'=>$photos
    ];
    return response()->json($response,200);



}

}