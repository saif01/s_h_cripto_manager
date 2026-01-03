<?php

namespace App\Http\Controllers\ExpInc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Expense;
use Auth;
use App\Models\ExpenseType;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    //index
    public function index(){

        $paginate       = Request('paginate', 10);
        $search         = Request('search', '');
        $sort_direction = Request('sort_direction', 'desc');
        $sort_field     = Request('sort_field', 'id');

        $sort_by_type   = Request('sort_by_type', '');
        $start_date     = Request('start_date', '');
        $end_date       = Request('end_date', '');

        $allDataQuery = Expense::with('exptype:id,name')
            ->whereNull('delete_temp')
            ->where('created_by', Auth::user()->id)
            ->search( trim(preg_replace('/\s+/' ,' ', $search)) );
            
        if( !empty($sort_by_type) ){
            $allDataQuery->where("type_id", $sort_by_type);
        }

        if( !empty($start_date) ){
            $allDataQuery->whereDate("expense_date",">=",$start_date);
        }
        if( !empty($end_date) ){
            $allDataQuery->whereDate("expense_date","<=",$end_date);
        }
        
        // FOr other query purpose
        $allDataQuery2 = $allDataQuery->get();

        // Paginate Data
        $allData = $allDataQuery->orderBy($sort_field, $sort_direction)
            ->paginate($paginate);

       
        // Total Sum of Ammount
        $total_expense = $allDataQuery2->sum('money');

        // dd( $total_expense, $allDataQuery2->toArray(), $allData);

        // Data group by category
        $chartData = $allDataQuery2->groupBy('type_id');

        

        // Last 12 months expens
        $expenses = Expense::whereNull('delete_temp')
        ->where('created_by', Auth::user()->id)
        ->selectRaw('SUM(money) as total_money, MONTH(expense_date) as month')
        ->whereBetween('expense_date', [Carbon::now()->subMonths(12), Carbon::now()])
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->get();

        $last_12_months_expense = [];
        foreach ($expenses as $key => $expense) {
            $last_12_months_expense[$key]['month'] = Carbon::create()->month($expense->month)->format('F');
            $last_12_months_expense[$key]['total'] = $expense->total_money;
        }
        //dd($last_12_months_expense);



        //  dd($chartData->toArray());

        $pieLabels = [];
        $pieData = [];

        foreach ($chartData as $key => $value) {
            //dd( $value->sum('money'), $value[0]->exptype->name);
            array_push($pieLabels, $value[0]->exptype->name);
            array_push($pieData, $value->sum('money'));
        }

        $chart_data = ['labels'=> $pieLabels, 'data'=> $pieData ];

        // Expense Types
        $exp_types = ExpenseType::whereNull('delete_temp')->select('id','name')->orderBy('name')->get()->toArray();

        $custom = collect( [
            'exp_types'       => $exp_types,
            'total_expense'   => $total_expense,
            'chart_data'      => $chart_data,
            'last_12_months_expense' => $last_12_months_expense
        ] );
        $allDataFinal = $custom->merge($allData);

        return response()->json($allDataFinal, 200);

    } 


    // store
    public function store(Request $request){

        // dd($request->all());

        // //Validate
        // $this->validate($request,[
        //     'money'     => 'required|numeric',
        // ]);

        $data = new Expense();

        $data->money        = $request->money;
        $data->type_id      = $request->type_id;
        if($request->expense_date){
            $data->expense_date = $request->expense_date;
        }
        $data->details      = $request->details;
        
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

        $data = Expense::find($id);

      
        $data->money       = $request->money;
        $data->type_id     = $request->type_id;
        if($request->expense_date){
            $data->expense_date = $request->expense_date;
        }
       
        $data->details     = $request->details;
        $data->created_by  = Auth::user()->id;
        $success           = $data->save();

     
        return response()->json(['msg'=>'Updated Successfully &#128515;', 'icon'=>'success'], 200);
       
    }

    // destroy_temp
    public function destroy_temp($id)
    {
        $data       =  Expense::find($id);
        $data->delete_temp  = 1;
        // $data->delete_by    =  Auth::user()->id;
        $data->save();

        return response()->json('success', 200);
      
    }

    // destroy
    public function destroy($id)
    {
        $data       =  Expense::find($id);
        $success    =  $data->delete();
        return response()->json('success', 200);
      
    }

}
