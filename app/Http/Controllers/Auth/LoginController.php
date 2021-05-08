<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use GuzzleHttp;
use Validator;
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

    }

    public function login(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($requestData, [
            'email' => 'string|required',
            'password' => 'string|required',
        ]);

        if($validator->fails()){
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);
        }

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
                'email' => $requestData['email'],
                'password' => $requestData['password']
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

    public function logout()
    {
        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $bearer_token = '';
        if (Session::has('bearer_token')) {
            $bearer_token = Session::get('bearer_token');
        } else {
            return redirect('login');
        }

        $options = [
            'headers' =>[
                'Authorization' => 'Bearer ' .$bearer_token,
                'Accept'        => 'application/json',
                'Content-Type' => 'application/json'
            ],
        ];
        $requestString = 'auth/logout';
        $callUrl = $baseUrl.$requestString;
        $response = $client->request('POST', $callUrl, $options );
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
