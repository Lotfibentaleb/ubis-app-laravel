<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use GuzzleHttp;
use Illuminate\Support\Facades\Session;

class CurrentUserController extends Controller
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
     * Get current user's profile
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show( Request $request ) {
//        $user = $request->user();
//
//        $user->load('file');
//        $user->append('avatar');
//
//        return response()->json([
//            'data' => $request->user()
//        ]);

        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'auth/me';
        $bearer_token = '';
        if (Session::has('bearer_token')) {
            $bearer_token = Session::get('bearer_token');
        } else {
            return redirect('login');
        }

        $options = [
            'headers' =>[
                'Authorization' =>'Bearer ' .$bearer_token,
                'Accept'        => 'application/json',
                'Content-Type' => 'application/json'
            ],
        ];

        $response = $client->request('get', $baseUrl.$requestString, $options);   // call API

        $body = json_decode($response->getBody()->getContents());
        return response()->json([
            'data' => $body
        ]);
    }

    /**
     * Update current user's profile
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update( Request $request ) {

        $paramData = $request->all();
        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'users/updateProfile';
        $bearer_token = '';
        if (Session::has('bearer_token')) {
            $bearer_token = Session::get('bearer_token');
        } else {
            return redirect('login');
        }
        $options = [
            'headers' =>[
                'Authorization' =>'Bearer ' .$bearer_token,
                'Accept'        => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'json' => $paramData,
        ];

        try {
            $response = $client->request('post', $baseUrl.$requestString, $options);   // call API
            $body = json_decode($response->getBody()->getContents());
            return response()->json([
                'data' => $body
            ]);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            return response()->json([
                'status' => false
            ], 404);
        }
    }

    /**
     * Update current user's password
     *
     * @param PasswordUpdateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword( Request $request ) {

        $paramData = $request->all();
        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'users/updatePassword';
        $bearer_token = '';
        if (Session::has('bearer_token')) {
            $bearer_token = Session::get('bearer_token');
        } else {
            return redirect('login');
        }
        $options = [
            'headers' =>[
                'Authorization' =>'Bearer ' .$bearer_token,
                'Accept'        => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'json' => $paramData,
        ];

        try {
            $response = $client->request('post', $baseUrl.$requestString, $options);   // call API
            $body = json_decode($response->getBody()->getContents());
            return response()->json([
                'status' => true,
                'data' => $body,
            ]);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $responseErrorBody = $e->getResponse()->getBody(true);
            return response()->json([
                'status' => false,
                'data' => $responseErrorBody,
            ], 500);
        }

    }
}
