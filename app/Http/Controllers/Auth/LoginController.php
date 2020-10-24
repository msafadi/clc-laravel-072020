<?php

namespace App\Http\Controllers\Auth;

use App\Admin;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout', 'weblogout');
    }

    /*public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        //$result = Auth::attempt($request->only('email', 'password'));
        $email = $request->post('email');
        $user = Admin::where('username', $email)->orWhere('email', $email)->first();
        
        if ($user && Hash::check($request->post('password'), $user->password)) {
            Auth::login($user);
            Auth::guard('admin')->login($user);
            return redirect($this->redirectTo);
        }

        return redirect()->back()->withInput()->withErrors([
            'email' => 'Invalid username and password'
        ]);
    }*/

    public function weblogout()
    {
        //$this->guard()->logout();
        return $this->logout(request());
    }
}
