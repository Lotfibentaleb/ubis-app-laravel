<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

use Validator;
use Illuminate\Support\Facades\Session;
use Log;
use Throwable;
use Carbon\Carbon;



class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;


    private function processCall($requestString, $requestData=null, $type='GET')
    {
        $client = new Client();

        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $options = [
    //            'debug' => fopen('php://stderr', 'w'),
            'http_errors'=> false,
            'headers' =>[
            'Authorization' => 'Bearer ' .env('PIS_BEARER_TOKEN'),
            'Accept'        => 'application/json',
            'Content-Type' => 'application/json',
            ],
            'json' => $requestData
        ];

        $response = null;
        $callUrl = $baseUrl.$requestString;
        try{
            $response = $client->request($type, $callUrl, $options );   // call API by serial+article-nr.
        }catch(Throwable $e){
            // fail
            return ['code' => $e->getCode(), 'data' => null];
        }

        return ['code' => $response->getStatusCode(), 'data' => json_decode($response->getBody()->getContents())];
    }


    /**
     * Reset the given user's password.
     *  POST http://ubis.dev-markus.regensburg.semsotec.de/password/reset
     *   {
     *	    "token": "pAVl5hxlrE2uIyunYAJvQxH9uI8M4MywnN7FxZiG",
     *	    "email": "mh@tecpool.de",
     *  	"password": "bla",
     *   }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());
        $email = $request->input('email', null);
        $password = $request->input('password', null);
        $token = $request->input('token', null);

        // Get change pwd token if existing and valid (timeout 60min)
        $token_entry = DB::table('password_resets')
            ->where('email', '=', $email)
            ->where('token', '=', $token)
            ->where('created_at', '>', Carbon::now()->subMinutes(60) )->first();

        if(empty($token_entry)){
            return $this->sendResetFailedResponse($request, Password::INVALID_TOKEN);
        }

        // Token is valid -> remove it
        DB::table('password_resets')->where('email', '=', $email)->delete();

        $requestString = 'users/setPassword'; // was für eine scheiß Lösung...
        $requestData = array('email' => $email, 'password' => $password);

        $response = $this->processCall($requestString, $requestData, 'POST');
        if($response['code'] != 200){
            return $this->sendResetFailedResponse($request, Password::INVALID_USER);
        }

        return $this->sendResetResponse($request,Password::PASSWORD_RESET);
        //$this->guard()->login($user);
    }


}
