<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use GuzzleHttp;
use Validator;
use Exception;
use Log;


class ProductTemplateController extends Controller
{
    /**
     * Create a new ProductTemplateController instance.
     *
     * @return void
     */
    public function __construct () {

    }

    /**
     * Index
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index (Request $request) {
        $query = $request->query();
        $passOnQuery = "";
        if( count($query) ){
            $passOnQuery .= '?';
            foreach($query as $key=>$value){
                $passOnQuery .= $key.'='.urlencode($value).'&';
            }
        }

        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'product_templates'. $passOnQuery;

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
            ]
        ];

        try {
            $response = $client->request('GET', $baseUrl.$requestString, $options);   // call API
            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody()->getContents());
            return response()->json($body, $statusCode);
        } catch (Exception $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            return response()->json(['message' => $e->getMessage()], $statusCode);
        }

    }
}
