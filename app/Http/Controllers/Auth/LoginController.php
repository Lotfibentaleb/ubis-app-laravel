<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use GuzzleHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Log;


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

//    use AuthenticatesUsers;

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
//        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'auth/login';
        $options = [
            'headers' =>[
                'Authorization' => '',
                'Accept'        => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'json' =>[
                'email' => $email,
                'password' => $password
            ]
        ];

        try {
            $response = $client->request('post', $baseUrl.$requestString, $options);   // call API
            $body = json_decode($response->getBody()->getContents());
            if (isset($body->access_token)) {
                Session::put('bearer_token', $body->access_token);
                return redirect('/home');
            } else {
                throw ValidationException::withMessages([
                    $this->username() => [trans('auth.failed')],
                ]);
            }
        }
        catch (GuzzleHttp\Exception\ClientException $e) {
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        Session::forget('bearer_token');
        return Session::has('bearer_token')
            ? Session::forget('bearer_token')
            : redirect('/');
    }


    public function username()
    {
        return 'email';
    }
}
