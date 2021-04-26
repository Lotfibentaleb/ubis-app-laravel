<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
use Throwable;

class ProductionFlowController extends Controller
{

    private function processCall(Request $request, $endpoint, $id=null, $type='GET')
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
        $callUrl = $baseUrl.$endpoint;
        if( $id ){
            $callUrl .= '/'.$id.$passOnQuery;
        }else{
            $callUrl .= $passOnQuery;
        }

        try{
            $response = $client->request($type, $callUrl, $options );   // call API by serial+article-nr.
        }catch(Throwable $e){
            // fail
            $message = 'Unknown Error';
            switch($e->getCode()){
                case 404: $message = 'Not found';break;
                case 422: $message = 'Wrong parameter';break;
                case 409: $message = 'Create/store failed';break;
            }
            return response(['code' => $e->getCode(), 'error' =>  'Backend call failed.'.$e->getMessage()], $e->getCode());
        }

        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody()->getContents());
        return response()->json($body, $statusCode);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->processCall($request, 'production_flow');
    }

    /**
     * Display a history of the resource.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function indexHistory(Request $request)
    {
        if( !auth()->user()->tokenCan('product-list') ){    // TODO update rights
            return response()->json(['message' =>  'Unauthorized'], 403);
        }
        return $this->processCall($request, 'production_flow/history');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->processCall($request, 'production_flow', null, 'POST');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        return $this->processCall($request, 'production_flow', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->processCall($request, 'production_flow', $id, 'PUT');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response(['message' =>  'Not implemented'], 501);
    }
}
