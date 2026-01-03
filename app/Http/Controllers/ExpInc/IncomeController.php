<?php

namespace App\Http\Controllers\ExpInc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Income;
use Auth;

class IncomeController extends Controller
{
    //index
    public function index(){

        $paginate       = Request('paginate', 10);
        $search         = Request('search', '');
        $sort_direction = Request('sort_direction', 'desc');
        $sort_field     = Request('sort_field', 'id');

        $allData = Income::whereNull('delete_temp')
            ->where('created_by', Auth::user()->id)
            ->orderBy($sort_field, $sort_direction)
            ->search( trim(preg_replace('/\s+/' ,' ', $search)) )
            ->paginate($paginate);

        return response()->json($allData, 200);

    } 


    // store
    public function store(Request $request){

        // dd($request->all());

        // //Validate
        // $this->validate($request,[
        //     'money'     => 'required|numeric',
        // ]);

        $data = new Income();

        $data->money       = $request->money;
        $data->details     = $request->details;
        
        $data->created_by =  Auth::user()->id;
        $success          = $data->save();

       

        return response()->json(['msg'=>'Stored Successfully &#128513;', 'icon'=>'success'], 200);
      
    }


    // update
    public function update(Request $request, $id){

        // //Validate
        // $this->validate($request,[
        //     'name'     => 'required|string|max:1000|unique:zones,name,'.$id,
        // ]);

        $data = Income::find($id);

      
        $data->money       = $request->money;
        $data->details     = $request->details;
        $data->created_by  = Auth::user()->id;
        $success           = $data->save();

     
        return response()->json(['msg'=>'Updated Successfully &#128515;', 'icon'=>'success'], 200);
       
    }

    // destroy_temp
    public function destroy_temp($id)
    {
        $data       =  Income::find($id);
        $data->delete_temp  = 1;
        // $data->delete_by    =  Auth::user()->id;
        $data->save();

        return response()->json('success', 200);
      
    }

    // destroy
    public function destroy($id)
    {
        $data       =  Income::find($id);
        $success    =  $data->delete();
        return response()->json('success', 200);
      
    }

}
