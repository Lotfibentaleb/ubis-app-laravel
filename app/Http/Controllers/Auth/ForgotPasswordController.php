<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

use Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Log;
use Throwable;
use Illuminate\Support\Str;
use Carbon\Carbon;


class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails; // do not use default trait as we have to communicate with PIService

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
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = $request->validate(['email' => 'required|email']);
        $email = $request->input('email', null);
        $token = $request->input('token', hash('sha256', Str::random(40)));

        $requestString = 'users/'.$email.'?email=true';
        $response = $this->processCall($requestString);

        if($response['code'] != 200){
            return $this->sendResetLinkFailedResponse($request, Password::INVALID_USER);
        }
        // user exists
        $requestString = 'users/requestNewPasswordMail';
        $link = route('password.reset', ['token' => $token]);
        $requestData = array('email' => $email, 'link' => $link);

        // clear table and create new entry for pwd reset
        DB::table('password_resets')->where('email', '=', $email)->delete();
        $response = $this->processCall($requestString, $requestData);
        if($response['code'] != 200){
            return $this->sendResetLinkFailedResponse($request, Password::INVALID_TOKEN);
        }
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
        return $this->sendResetLinkResponse($request, Password::RESET_LINK_SENT);
    }
}
