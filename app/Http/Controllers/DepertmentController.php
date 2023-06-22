<?php

namespace App\Http\Controllers;
use App\Models\Depertment;
use App\Models\Universitys;
use Validator;
use Illuminate\Http\Request;

class DepertmentController extends Controller
{
    //
    public function createDepertment(Request $request){

        $user=$request->user();

        $college=Universitys::where('create-by',$user['id'])->first();

        if(!$college){
          $response=[
                'success'=>false,
                'message'=>'No college found'
            ];
            return response()->json($response,200);
        }
      
            $validator= Validator::make($request->all(),[
                'depertment_name'=>'required',
                'depertment_email'=>'required|email|unique:depertments',
                'instructor'=>'required',
                'description'=>'required',
                // 'type'=>'numeric'
           ]);
           if ($validator->fails()) {
               $response=[
                   'success'=>false,
                   'message'=>$validator->errors()->first()
               ];
               return response()->json($response,400);
           } 
        
            $depertment = Depertment::create([
                'college_id'=>$college['id'],
                'depertment_name'=>$request->depertment_name,
                'depertment_email'=>$request->depertment_email,
                'instructor'=>$request->instructor,
                'description'=>$request->description,
                         
            ]);
             
            $response=[
                'success'=>true,
                'message'=>'Register Depertment Successfully',
                'depertment'=>$depertment,
                
            ];
            return response()->json($response,201);
           
        
    }

    //get depertment

    public function getDepertments(Request $request){

        $user=$request->user();

        $college=Universitys::where('create-by',$user['id'])->first();
        if (!$college) {

            $response = [
                'success' => false,
                'message' => 'No college found'
            ];

            return response()->json($response, 200);
        }

        $depertments = Depertment::where('college_id', $college['id'])->get();

        if (!$depertments) {

            $response = [
                'success' => false,
                'message' => 'please enter details'
            ];

            return response()->json($response, 404);
        }


        $response = [
            'success' => true,
            'depertments' => $depertments
        ];

        return response()->json($response, 200);

    }

    //get depertment

    public function getDepertmentsDetails(Request $request,$id){

        $user=$request->user();

        $college=Universitys::where('create-by',$user['id'])->first();
        if (!$college) {

            $response = [
                'success' => false,
                'message' => 'No college found'
            ];

            return response()->json($response, 200);
        }

        $depertment = Depertment::where('id', $id)->first();

        if (!$depertment) {

            $response = [
                'success' => false,
                'message' => 'depertment not found'
            ];

            return response()->json($response, 404);
        }


        $response = [
            'success' => true,
            'depertment' => $depertment
        ];

        return response()->json($response, 200);

    }

    //update depertment

    public function UpdateDepertment(Request $request,$id){

        $user=$request->user();

        $college=Universitys::where('create-by',$user['id'])->first();

        if(!$college){
          $response=[
                'success'=>false,
                'message'=>'No college found'
            ];
            return response()->json($response,200);
        }
      $depertment=Depertment::where('id',$id)->first();

            $validator= Validator::make($request->all(),[
                'depertment_name'=>'required',
                // 'depertment_email'=>'required|email|unique:depertments',
                'instructor'=>'required',
                'description'=>'required',
                // 'type'=>'numeric'
           ]);
           if ($validator->fails()) {
               $response=[
                   'success'=>false,
                   'message'=>$validator->errors()->first()
               ];
               return response()->json($response,400);
           } 
        
            $depertment->update([

                'depertment_name'=>$request->depertment_name,
                // 'depertment_email'=>$request->depertment_email,
                'instructor'=>$request->instructor,
                'description'=>$request->description,
                         
            ]);
            $depertment->save();
             
            $response=[
                'success'=>true,
                'message'=>'Update Successfully',
              
                
            ];
            return response()->json($response,200);
           
        
    }
}
