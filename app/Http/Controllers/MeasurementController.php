<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
use Throwable;


class MeasurementController extends Controller
{

    public function index(Request $request)
    {
        $client = new Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');

        $requestData = $request->all();

        // set given query parameters, to be able to forward them
        $query = $request->query();
        $passOnQuery = "";
        if( count($query) ){
            $passOnQuery .= '?';
            foreach($query as $key=>$value){
                $passOnQuery .= $key.'='.urlencode($value).'&';
            }
        }

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
            'json' => $requestData
        ];

        $response = null;
        $callUrl = $baseUrl.'device_records'.$passOnQuery;

        try{
            $response = $client->request('GET', $callUrl, $options );   // call API by serial+article-nr.
        }catch(Throwable $e){
            // fail
            $message = 'Unknown Error';
            if ($e->getCode() == 401) {
                Session::forget('bearer_token');
            } else {
                switch($e->getCode()){
                    case 404: $message = 'Not found';break;
                    case 422: $message = 'Wrong parameter';break;
                    case 409: $message = 'Create/store failed';break;
                }
            }
            return response(['code' => $e->getCode(), 'error' =>  'Backend call failed.'.$e->getMessage()], $e->getCode());
        }

        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody()->getContents());
        return response()->json($body, $statusCode);
    }

    public function getMeasurement(Request $request, $id)
    {
        $client = new Client();
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

        $response = null;
        $callUrl = $baseUrl.'device_records/'.$id;

        try{
            $response = $client->request('GET', $callUrl, $options );   // call API by serial+article-nr.
        }catch(Throwable $e){
            // fail
            $message = 'Unknown Error';
            if ($e->getCode() == 401) {
                Session::forget('bearer_token');
            } else {
                switch($e->getCode()){
                    case 404: $message = 'Not found';break;
                    case 422: $message = 'Wrong parameter';break;
                    case 409: $message = 'Create/store failed';break;
                }
            }
            return response(['code' => $e->getCode(), 'error' =>  'Backend call failed.'.$e->getMessage()], $e->getCode());
        }

        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody()->getContents());
        return response()->json($body, $statusCode);
    }
}
