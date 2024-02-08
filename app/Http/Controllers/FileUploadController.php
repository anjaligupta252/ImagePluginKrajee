<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImageModel;
use File;

class FileUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $files = $request->file('files');
             $path = public_path('/media/');
            if(!File::isDirectory($path)){
                (File::makeDirectory($path, 0777, true, true));
            }

            foreach($files as $file){
                $new_file_name = $file->getClientOriginalName();
                $file->move(public_path('media/'), $new_file_name);
                ImageModel::create(['name' => $new_file_name]);
                 
            } 

           $getData = ImageModel::get();
           foreach ($getData as $key => $value) {
                $urlData[] = ["url" => url('media/'.$value->name),"key" => $value->id];
           }

           return response()->json($urlData); 
        } catch (Exception $e) {
            

        }
        

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        ImageModel::where('id',$id)->delete();

        $urlData = [];
        $getData = ImageModel::get();
           foreach ($getData as $key => $value) {
                $urlData[] = ["url" => url('media/'.$value->name),"key" => $value->id];
           }

           return response()->json($urlData); 
       
    }
}
