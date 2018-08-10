<?php

namespace App\Http\Controllers\Csv;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class CsvController extends Controller
{
    public function getCSV(){
    	return view('ebay.getcsv');
    }
    public function postCSV(Request $request){
    	$input = $request->all();

    		$file = $request->file;

            $rules= [
             'file' => 'required|mimes:csv'
            ];

            $validator=Validator::make($input,$rules);

            if ($validator->passes()){

                 $filename = 'products'.'.'.$file->getClientOriginalExtension();

                $pathPublic = public_path().'/files/';

                if(\File::exists($pathPublic.$filename)){

                    unlink($pathPublic.$filename);
                      
                }

                if(!\File::exists($pathPublic)) {

                    \File::makeDirectory($pathPublic, $mode = 0777, true, true);

                }

                $file->move($pathPublic, $filename);
            }else{
                return redirect()->back()->withErrors($validator)->withInput();
            }
           
    
    	
  //   	$array = ['title','description'];
  //   	$contents =['Parts Unlimited R09-774X Ring Set 64.50mm','Description'];

		// $file = fopen("products.csv","w");
		// fputcsv($file,$array);
		// fclose($file);
  //   	dd($input);
    }
}