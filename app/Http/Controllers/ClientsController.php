<?php

namespace App\Http\Controllers;

use App\Client;
use App\User;
use App\Http\Requests\ClientStoreRequest;
use Illuminate\Http\Request;
use GuzzleHttp;
use Illuminate\Support\Facades\Session;
use Log;

class ClientsController extends Controller
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
        $response = $client->request('GET', $baseUrl.$requestString, $options);   // call API
        $users = json_decode($response->getBody()->getContents());

        return response()->json(['data' => $users]);
    }

    /**
     * Get single resource
     *
     * @param Client $client
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
        $response = $client->request('GET', $baseUrl.$requestString, $options);   // call API
        $user = json_decode($response->getBody()->getContents());

        return response()->json([
            'data' => $user
        ]);
    }

    /**
     * Update single resource
     *
     * @param ClientStoreRequest $request
     * @param Client $client
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {

        $updateData = $request->all();
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
            'json' => $updateData
        ];
        $response = $client->request('put', $baseUrl.$requestString, $options);   // call API
        $resData = json_decode($response->getBody()->getContents());

        return response()->json([
        'status' => true,
            'data' => $resData
        ]);

    }

    /**
     * Store new resource
     *
     * @param ClientStoreRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
//        $client = new Client;
//        $client->fill($request->all());
//        $client->save();
//
//        return response()->json([
//            'status' => true,
//            'created' => true,
//            'data' => [
//                'id' => $client->id
//            ]
//        ]);

        $paramDate = $request->input('selectedDate', '');

    }

    /**
     * Destroy single resource
     *
     * @param Client $client
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy( Client $client ) {
        $client->delete();

        return response()->json([
            'status' => true
        ]);
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
