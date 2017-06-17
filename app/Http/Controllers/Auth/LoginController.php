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

    /**
     * Set the root-domain authentication token for authenticated user.
     *
     * @param Request $request
     * @param $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->token === null) {
            $user->token()->save(new Token());
        }
        $token = $user->token()->first()->value;

        $node = Node::name($request['from'])->first();
        if ($node === null) {
            $address = env('APP_URL');
        } else {
            $address = $node->address;
        }

        $cookie = cookie('token', $token, 3600, '/', '.' . env('ROOT_DOMAIN'), false, false);

        $redirect = redirect($address)->cookie($cookie);

        return $redirect;
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


        return response('logged out')->withCookie(Cookie::forget('token', '/', '.' . env('ROOT_DOMAIN')));
    }
}
