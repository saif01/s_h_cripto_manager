<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cost;
use App\Models\Deposit;
use App\Http\Controllers\LineNotification;
use App\Http\Controllers\CommonFunction;
use App\Http\Controllers\TelegramNotify;

class CostController extends Controller
{
    //index
    public function index(){

        $paginate       = Request('paginate', 10);
        $search         = Request('search', '');
        $sort_direction = Request('sort_direction', 'desc');
        $sort_field     = Request('sort_field', 'id');

        $allData = Cost::whereNull('delete_temp')
            ->orderBy($sort_field, $sort_direction)
            ->search( trim(preg_replace('/\s+/' ,' ', $search)) )
            ->paginate($paginate);

        // $totalAmmount = Cost::whereNull('delete_temp')->sum('money');
        // $totalDeposit = Deposit::whereNull('delete_temp')->sum('money');
        // $lastDeposit  = Deposit::whereNull('delete_temp')->orderBy('id', 'desc')->first();
        // $difference   = $totalDeposit - $totalAmmount;

        $remainingBalance  = CommonFunction::RemainingBalance();
        $lastDeposit       = CommonFunction::LastDeposit();

        $custom = collect([
            'remaining_balance'         => $remainingBalance ?? 0,
            'last_deposit'              => $lastDeposit,
        ]);
        $allDataFinal = $custom->merge($allData);

        // dd($totalAmmount, $totalDeposit, $difference);

        return response()->json($allDataFinal, 200);

    } 


    // store
    public function store(Request $request){

        // dd($request->all());

        // //Validate
        // $this->validate($request,[
        //     'money'     => 'required|numeric',
        // ]);

        $data = new Cost();

        $data->money       = $request->money;
        $data->to          = $request->to ?? 'Bank Transfer';
        $data->details     = $request->details;
        
        // $data->created_by =  Auth::user()->id;
        $success          = $data->save();

        $remainingBalance  = CommonFunction::RemainingBalance();

        // Update remaining balance
        $data->remaining   = $remainingBalance;
        $success           = $data->save();

        $detailsForLine        = str_replace('&', 'and', $request->details);


        if($request->sms_sent){
            $msg_tel= "➖ Cost ➖ \nAmmount : ". number_format($request->money)."/=\nDetails : ".$detailsForLine. "\n\n"  . "Balance : ".number_format($remainingBalance) ."/=";
            // Telegram
            TelegramNotify::T_NOTIFY($msg_tel);
        }
        
        
        return response()->json(['msg'=>'Stored Successfully &#128513;', 'icon'=>'success'], 200);
      
    }


    // update
    public function update(Request $request, $id){

        // //Validate
        // $this->validate($request,[
        //     'name'     => 'required|string|max:1000|unique:zones,name,'.$id,
        // ]);

        $data = Cost::find($id);

      
        $data->money       = $request->money;
        $data->to          = $request->to;
        $data->details     = $request->details;
        //$data->created_by = Auth::user()->id;
        $success          = $data->save();

     
        return response()->json(['msg'=>'Updated Successfully &#128515;', 'icon'=>'success'], 200);
       
    }

    // destroy_temp
    public function destroy_temp($id)
    {
        $data       =  Cost::find($id);
        $data->delete_temp  = 1;
        // $data->delete_by    =  Auth::user()->id;
        $data->save();

        return response()->json('success', 200);
      
    }

    // destroy
    public function destroy($id)
    {
        $data       =  Cost::find($id);
        $success    =  $data->delete();
        return response()->json('success', 200);
      
    }

    // resend_line
    public function resend_line($id){
        // dd($id);

        $data = Cost::find($id);

        $remainingBalance  = $data->remaining;
        $detailsForLine    = str_replace('&', 'and', $data->details);

        // dd($data);

        // $msg = "SYS. R. Cost %0AAmmount : ". number_format($data->money)."/=%0ADetails : ".$detailsForLine. "%0A%0A";
        // $msg .= $remainingBalance ? "Balance : ".number_format($remainingBalance) ."/=" : null;

        $msg_tel = "🔄 R. Cost 🔄 \nAmmount : ". number_format($data->money)."/= \nDetails : ".$detailsForLine. " \n\n";
        $msg_tel .= $remainingBalance ? "Balance : ".number_format($remainingBalance) ."/=" : null;

        // Telegram
        TelegramNotify::T_NOTIFY($msg_tel);

        // Line 
        // LineNotification::SEND($msg);

        return response()->json(['msg'=>'Resend Successfully &#128515;', 'icon'=>'success'], 200);

    }

}
