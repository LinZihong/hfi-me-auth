<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Token;
use App\Node;
use Illuminate\Support\Facades\Cookie;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user());
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->token === null) {
            $user->token()->save(new Token());
        }
        $address = Node::name($request['from'])->firstOrFail()->address;
        $token = $user->token()->first()->value;

        $cookie = cookie('token', $token, 3600, '/', '.' . env('ROOT_DOMAIN'), false, false);
        $response = response('ok');
        $response->headers->setCookie($cookie);
        return $response;
    }

    public function logout(Request $request)
    {
        $logged_in = Auth::check();

        if ($logged_in) {
            $user = Auth::user();
            $user->token()->delete();
        }
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return response('logged out')->withCookie(Cookie::forget('token','/','.'.env('ROOT_DOMAIN')));
    }
}
