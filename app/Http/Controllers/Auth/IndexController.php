<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Auth;
use Session;

class IndexController extends Controller
{
    //index
    public function index(){

        if( Auth::check() ){
            return redirect()->route('dashboard');
        }

        return view('auth.index');
    }

    // login_action
    public function login_action(Request $request){
       
        // dd($request->all());

        // Validations
        request()->validate([
            'email' => 'required|min:5|max:30',
            'password' => 'required|min:3|max:30',
        ]);

        $email      = $request->email;
        $password   = $request->password;

        $allData = User::where('email', $email)
        ->where('password', $password)
        ->first();

        if($allData){
            //Local Server Authentication passed...
            Auth::login($allData);

            $response = (object) [
                'status'    => 'success',
                'msg'       => 'User Found',
                'data'      => $allData,
            ];
        }else{
            $response = (object) [
                'status'    => 'error',
                'msg'       => 'Incorrect password or email',
                'data'      => null,
                'email'     => $email,
            ];

        }

        
        return $response;
    }


    // logout
    public function logout(){
        // dd('okk');
        // Logout Log Update
        if(Auth::check()){
            Session::flush();
            Auth::logout();
        }
        return redirect()->route('dashboard');
    }
}
