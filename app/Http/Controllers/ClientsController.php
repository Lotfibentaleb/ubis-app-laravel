<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use GuzzleHttp;
use Illuminate\Support\Facades\Session;
use Log;
use Exception;

class ClientsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Index resource
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {

        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'users';

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
            $users = json_decode($response->getBody()->getContents());
            return response()->json(['data' => $users]);
        } catch (Exception $e) {
            if ($e->getCode() == 401) {
                Session::forget('bearer_token');
            }
            $statusCode = $e->getResponse()->getStatusCode();
            return response()->json(['message' => $e->getMessage()], $statusCode);
        }
    }

    /**
     * Get single resource
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'users/'.$id;

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
            $user = json_decode($response->getBody()->getContents());
            return response()->json(['data' => $user]);
        } catch (Exception $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            return response()->json(['message' => $e->getMessage()], $statusCode);
        }
    }

    /**
     * Update single resource
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {

        $paramData = $request->all();
        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'users/'.$id;

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
            'json' => $paramData
        ];

        try {
            $response = $client->request('put', $baseUrl.$requestString, $options);   // call API
            $resData = json_decode($response->getBody()->getContents());
            return response()->json([
                'data' => $resData
            ]);
        } catch (Exception $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            return response()->json(['message' => $e->getMessage()], $statusCode);
        }
    }

    /**
     * Create new resource
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {

        $paramData = $request->all();
        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'users/create';

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
            'json' => $paramData
        ];

        try {
            $response = $client->request('post', $baseUrl.$requestString, $options);   // call API
            $resData = json_decode($response->getBody()->getContents());
            return response()->json(['data' => $resData]);
        } catch (Exception $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            return response()->json(['message' => $e->getMessage()], $statusCode);
        }

    }

    /**
     * Destroy single resource
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id) {

        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'users/'.$id;

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

        try {
            $response = $client->request('delete', $baseUrl.$requestString, $options);   // call API
            $resData = json_decode($response->getBody()->getContents());
            return response()->json($resData);
        } catch (Exception $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            return response()->json(['message' => $e->getMessage()], $statusCode);
        }
    }

    /**
     * Destroy resources by ids
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroyMass( Request $request ) {
        $request->validate([
            'ids' => 'required|array'
        ]);

        Client::destroy($request->ids);

        return response()->json([
            'status' => true
        ]);
    }
}
