<?php

namespace cni\Http\Controllers\Auth;

use cni\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Auth;
use Request;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/horario';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(){
        $credentials = Request::only('email','password');

        if(Auth::attempt($credentials)){
            $user = Auth::user();
            return redirect()->route('horario');
        }

        return "Credenciais invalidas.";
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
