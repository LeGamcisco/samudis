<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(){
        if(Auth::check()){
            return redirect()->route("dashboard");
        }
        return view("auth.login");
    }
    public function doLogin(Request $request){
        try{
            $request->validate([
                "email" => "required",
                "password" => "required"
            ]);
            if(Auth::attempt($request->only("email","password"))){
                return redirect()->route("dashboard");
            }
            return redirect()->back()->withErrors("Wrong Email or Password")->withInput();
        }catch(Exception $e){
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function logout(){
        Auth::logout();
        return redirect()->route("login");
    }
}
