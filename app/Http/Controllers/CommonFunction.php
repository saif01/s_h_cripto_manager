<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cost;
use App\Models\Deposit;

class CommonFunction extends Controller
{
    //
    public static function TotalCost(){
        return Cost::whereNull('delete_temp')->sum('money');
    }

    public static function TotalDeposit(){
        return Deposit::whereNull('delete_temp')->sum('money');
    }

    public static function LastDeposit(){
        return Deposit::whereNull('delete_temp')->orderBy('id', 'desc')->first();
    }

    public static function RemainingBalance(){

        $totalDeposit = self::TotalDeposit();
        $TotalCost    = self::TotalCost();

        return  $totalDeposit - $TotalCost;
    }

   

   
    
}
