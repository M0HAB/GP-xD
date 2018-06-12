<?php

namespace App\Http\Controllers\_Auth;

use Auth;
use Session;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public function __construct()
    {
        $this->middleware(['guest'], ['except' => ['logout']]);
    }

    /**
     * @param request
     * @return user dashboard 
     */
    public function login(Request $request)
    {
        if ($request->email == null || $request->password == null)
        {
            Session::flash('warning', "Email and Password are required");
            return redirect()->back();
        }
        else
        {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password]))
                return redirect()->route('user.dashboard');
            else
            {
                //if failed login as user try as admin
                if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password]))
                    return redirect()->route('pindex.index');
                else{
//                    return $request;
                    Session::flash('error', "The email or password you have entered is invalid");
                    return redirect()->back();
                }
                
            }
        }
    }

    /**
     * Log the user out of the application.
     * Guard Default
     * @return index page
     */
    public function logout()
    {
        if (Auth::check())
        {
             //to logout admin too xD
            if (Auth::guard('admin')->check()){
                Auth::guard('admin')->logout();
            }            
            Auth::logout();
            return redirect()->route('index');
        }
    }
}
