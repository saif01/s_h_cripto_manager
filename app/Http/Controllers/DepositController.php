<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Deposit;
use App\Http\Controllers\LineNotification;
use App\Http\Controllers\CommonFunction;
use App\Http\Controllers\TelegramNotify;

class DepositController extends Controller
{
    //index
    public function index(){

        $paginate       = Request('paginate', 10);
        $search         = Request('search', '');
        $sort_direction = Request('sort_direction', 'desc');
        $sort_field     = Request('sort_field', 'id');

        $allData = Deposit::whereNull('delete_temp')
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

        $data = new Deposit();

        $data->money       = $request->money;
        $data->details     = $request->details;
        
        // $data->created_by =  Auth::user()->id;
        $success          = $data->save();

        $remainingBalance  = CommonFunction::RemainingBalance();
        $detailsForLine        = str_replace('&', 'and', $request->details);

        //$msg = 'SYS. Deposit %0AAmmount : '. number_format($request->money).'/=%0ADetails : '.$detailsForLine. '%0A%0A'  . 'Balance : '.number_format($remainingBalance) .'/=';
         // Line 
        //  LineNotification::SEND($msg);

        $msg_tel = "➕ Deposit ➕ \nAmmount : ". number_format($request->money)."/=\nDetails : ".$detailsForLine. "\n\n"  . "Balance : ".number_format($remainingBalance) ."/=";
        // Telegram
        TelegramNotify::T_NOTIFY($msg_tel);
       


        return response()->json(['msg'=>'Stored Successfully &#128513;', 'icon'=>'success'], 200);
      
    }


    // update
    public function update(Request $request, $id){

        // //Validate
        // $this->validate($request,[
        //     'name'     => 'required|string|max:1000|unique:zones,name,'.$id,
        // ]);

        $data = Deposit::find($id);

      
        $data->money       = $request->money;
        $data->details     = $request->details;
        //$data->created_by = Auth::user()->id;
        $success          = $data->save();

     
        return response()->json(['msg'=>'Updated Successfully &#128515;', 'icon'=>'success'], 200);
       
    }

    // destroy_temp
    public function destroy_temp($id)
    {
        $data       =  Deposit::find($id);
        $data->delete_temp  = 1;
        // $data->delete_by    =  Auth::user()->id;
        $data->save();

        return response()->json('success', 200);
      
    }

    // destroy
    public function destroy($id)
    {
        $data       =  Deposit::find($id);
        $success    =  $data->delete();
        return response()->json('success', 200);
      
    }

}
