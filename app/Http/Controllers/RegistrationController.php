<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp;
use Validator;


class RegistrationController extends Controller
{
        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Index resource
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {

        return view('registration');
    }

    public function articles() {

        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'articles?size=10&search=A5';
        $options = [
//            'debug' => fopen('php://stderr', 'w'),
            'headers' =>[
            'Authorization' => 'Bearer ' .env('PIS_BEARER_TOKEN'),
            'Accept'        => 'application/json',
            'Content-Type' => 'application/json'
            ]
        ];


        //$request = $client->createRequest('GET', $baseUrl.$requestString, ['debug' => fopen('php://stderr', 'w')],  $options);   // call API
        //print_r(array($baseUrl.$requestString));

        //$response = $client->request('GET', $baseUrl.$requestString, ['debug' => fopen('php://stderr', 'w')],  $options);   // call API
        $response = $client->request('GET', $baseUrl.$requestString, $options);   // call API
    	$statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody()->getContents());

//        echo '<pre>';
//        print_r(array($baseUrl.$requestString, $statusCode, $body));
//        die(__FILE__);

        return response()->json(array('data' => $body->data), $statusCode);
    }

}
