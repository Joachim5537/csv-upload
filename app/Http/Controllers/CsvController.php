<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use \App\Models\Product;
use \App\Models\Upload;

use App\Jobs\ProcessCsvUpload;

use Carbon\Carbon;
use App\Http\Resources\UploadResource;


class CsvController extends Controller{

    public function construct(){}

    public function index() {
        return view('upload-page');
    }

    public function upload(Request $request)
    {  

        try{
   
            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:csv,txt', //max 50mb
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->route('csv.index')
                    ->with('error', 'CSV upload failed: ' . $validator->errors()->first());
            }

            // Store the file temporarily
            $path = $request->file('file')->store('uploads');

            // Create upload record (status = pending)
            $upload = Upload::create([
                'file_name' => $request->file('file')->getClientOriginalName(),
                'path' => $path,
                'status' => Upload::STATUS_PENDING,
            ]);

            //dispatch background job
            ProcessCsvUpload::dispatch($upload)->onQueue('uploads');

            return redirect()->route('csv.index')->with('success', 'Upload Success! Please wait for the job to complete. ');
        }catch(\Throwable $e){
            \Log::error('Upload failed: ' . $e->getMessage());
            return redirect()->route('csv.index')->with('error', $e->getMessage());
        }
        
    }

    public function status(Request $request){

        //default value for query
        if($request->has('sort')){
            $sort = $request->sort;
        }else{
            $sort = 'created_at';
        }

        if($request->has('order') && $request->order == 'asc'){
            $order = $request->order;
        }else{
            $order = 'desc';
        }

        $uploads = Upload::orderby($sort, $order)->get();

        //API transformer
        return UploadResource::collection($uploads);
    }


}