<?php

namespace Jalmatari\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Jalmatari\Http\Controllers\Core\MyBaseController;

class LoginController extends MyBaseController
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

    use AuthenticatesUsers {
        showLoginForm as showLoginForm2;
        login as login2;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {


        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return setting('authLoginCol') ?? 'email';
    }

    public function showLoginForm()
    {
        session([ 'lastUrlBefore' => request()->headers->get('referer') ]);

        return $this->showLoginForm2();
    }

    public function login(Request $request)
    {
        $response = $this->login2($request);
        if (session()->has('lastUrlBefore'))
            $response->setTargetUrl(url('user'));
        //$response->setTargetUrl(session('lastUrlBefore'));
        $alert = "مرحباً بك \"" . auth()->user()->name . "\" تم تسجيل دخولك بنجاح.";

        return $response->with('alert', $alert);
    }
}
