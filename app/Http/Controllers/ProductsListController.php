<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp;
use Validator;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExcelCollection;
use App\Exports\FullExport;
use Illuminate\Support\Facades\Session;
use Throwable;


class ProductsListController extends Controller
{
    /**
     * Index resource
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {

        $query = $request->query();
        $passOnQuery = "";
        // set given query parameters, to be able to forward them
        if( count($query) ){
            $passOnQuery .= '?';
            foreach($query as $key=>$value){
                $passOnQuery .= $key.'='.urlencode($value).'&';
            }
        }

        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'products'. $passOnQuery;

        $bearer_token = '';
        if (Session::has('bearer_token')) {
            $bearer_token = Session::get('bearer_token');
        } else {
            return response(['code' => 401, 'error' =>  'Unauthorized'], 401);
        }

        $options = [
            'headers' =>[
            'Authorization' => 'Bearer ' .$bearer_token,
            'Accept'        => 'application/json',
            'Content-Type' => 'application/json'
            ]
        ];

        try{
            $response = $client->request('GET', $baseUrl.$requestString, $options);   // call API
        }catch (Throwable $e){
            if ($e->getCode() == '401') {
                Session::forget('bearer_token');
            }
            return response(['code' => $e->getCode(), 'error' =>  'No product found.'], $e->getCode());
        }

    	$statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody()->getContents());

        return response()->json($body, $statusCode);
    }

    public function excel(Request $request) {

        $query = $request->query();
        $passOnQuery = "";
        // set given query parameters, to be able to forward them
        if( count($query) ){
            $passOnQuery .= '?';
            foreach($query as $key=>$value){
                $passOnQuery .= $key.'='.urlencode($value).'&';
            }
        }

        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'excelProducts'.$passOnQuery;

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
    	$statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody()->getContents());

        $excel_data = [];
        $excel_header = [
            'id' => 'ID',
            'st_article_nr' => 'Artikel-Nr.',
            'st_serial_nr' => 'Serial-Nr.',
            'lifecycle' => 'Status',
            'components_count' => 'Komponenten',
            'production_data_count' => 'Produktionsdaten',
            'production_order_nr' => 'Produktionsauftrag',
            'created_at' => 'Erstellt',
            'new_date' => 'Neues Datum',
            'updated_at' => 'Aktualisiert'
        ];
        array_push($excel_data, $excel_header);

        //convert stdClass object to array to fit excel format
        foreach ($body->data as $item_array) {
            array_push($excel_data, $item_array);
        }

        return Excel::download(new ExcelCollection($excel_data), 'data.xlsx');
    }

    public function enhancedExcel(Request $request) {

        $query = $request->query();
        $passOnQuery = "";
        // set given query parameters, to be able to forward them
        if( count($query) ){
            $passOnQuery .= '?';
            foreach($query as $key=>$value){
                $passOnQuery .= $key.'='.urlencode($value).'&';
            }
        }

        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'excelProducts'.$passOnQuery;

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
        $body = json_decode($response->getBody()->getContents());

        $excel_data = [];
        $excel_header = [
            'id' => 'ID',
            'st_article_nr' => 'st_article_nr',
            'st_serial_nr' => 'st_serial_nr',
            'lifecycle' => 'lifecycle',
            'components_count' => 'components_count',
            'production_data_count' => 'production_data_count',
            'production_order_nr' => 'production_order_nr',
            'created_at' => 'created_at',
//            'updated_at' => 'updated_at',
            'tested_at' => 'tested_at',
            'daisy.state' => 'daisy.state',
            'data.gamma' => 'data.gamma',
            'data.ambient_temp' => 'data.ambient_temp',
            'data.heating_temp' => 'data.heating_temp',
            'data.saturation_red' => 'data.saturation_red',
            'data.wavelength_red' => 'data.wavelength_red',
            'data.luminance_black' => 'data.luminance_black',
            'data.luminance_white' => 'data.luminance_white',
            'data.saturation_blue' => 'data.saturation_blue',
            'data.wavelength_blue' => 'data.wavelength_blue',
            'data.saturation_green' => 'data.saturation_green',
            'data.wavelength_green' => 'data.wavelength_green',
            'data.homogeneity_black' => 'data.homogeneity_black',
            'data.homogeneity_white' => 'data.homogeneity_white',
            'data.black_mura_gradient' => 'data.black_mura_gradient',
            'data.chromatisity_white_x' => 'data.chromatisity_white_x',
            'data.chromatisity_white_y' => 'data.chromatisity_white_y',
            'data.contrast_white_black' => 'data.contrast_white_black',
        ];
        array_push($excel_data, $excel_header);

        //convert stdClass object to array to fit excel format
        foreach ($body->data as $item_array) {
            array_push($excel_data, $item_array);
        }
        return Excel::download(new ExcelCollection($excel_data), 'enhanced_data.xlsx');
    }

    public function fullExcel(Request $request) {

        $query = $request->query();
        $passOnQuery = "";
        // set given query parameters, to be able to forward them
        if( count($query) ){
            $passOnQuery .= '?';
            foreach($query as $key=>$value){
                $passOnQuery .= $key.'='.urlencode($value).'&';
            }
        }

        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'excelProducts'.$passOnQuery;

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
        $body = json_decode($response->getBody()->getContents());

        $excel_data = $body->data;
        return (new FullExport(json_decode(json_encode($excel_data))))->download('fullExcel.xlsx');

    }

    public function updateProduct(Request $request, $id) {
        $requestData = $request->all();
        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'products/'.$id;

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
            'json' => [
                'st_article_nr' => $requestData["st_article_nr"],
                'st_serial_nr' => $requestData["st_serial_nr"],
                'production_order_nr' => $requestData["production_order_nr"]
            ]
        ];
        $response = $client->request('PUT', $baseUrl.$requestString, $options);   // call API
        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody()->getContents());
        return response()->json($body, $statusCode);
    }

    public function destroy(Request $request, $id) {
        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $requestString = 'products/'. $id;
        $options = [
            'headers' =>[
            'Authorization' => 'Bearer ' .env('PIS_BEARER_TOKEN'),
            'Accept'        => 'application/json',
            'Content-Type' => 'application/json'
            ]
        ];

        $response = $client->request('DELETE', $baseUrl.$requestString, $options);   // call API
    	$statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody()->getContents());

        return response()->json($body, $statusCode);
    }

    public function bulkRegister(Request $request) {

        $validator = Validator::make($request->all(), [
            'productionOrderNr' => 'string|between:1,65',
            'articleNr' => 'string|between:1,65',
            'file' => 'file'
        ]);

        if($validator->fails()){
            return response()->json(['message' =>  'Wrong parameter. '.implode(' ',$validator->errors()->all())], 422);
        }

        $csvFile = $request->file('file');

        if(!$csvFile || $csvFile->getClientOriginalExtension() != 'csv') {
            return response()->json(['message'=>'Invalid File'], 422);
        }

        $requestData = $request->all();

        //CSV file processing
        $file_handle = fopen($csvFile, 'r');
        $lineText = [];
        while (!feof($file_handle)) {
            $lineText[] = fgetcsv($file_handle, 0, ',');
        }
        fclose($file_handle);

        //remove last index item because the last index item is false
        unset($lineText[count($lineText) - 1]);

        $productionOrderNr = $requestData['productionOrderNr'];
        $articleNr = $requestData['articleNr'];

        $resData = [];
        foreach($lineText as $serialNr) {
            array_push($resData, $this->createProduct($articleNr, $productionOrderNr, '0'.(string)$serialNr[0]));
        }

        return response()->json($resData);
    }

    private function createProduct($articleNr, $productionOrderNr, $serialNr) {
        $client = new GuzzleHttp\Client();
        $baseUrl = env('PIS_SERVICE_BASE_URL2');
        $options = [
            'http_errors'=> false,
            'headers' =>[
                'Authorization' => 'Bearer ' .env('PIS_BEARER_TOKEN'),
                'Accept'        => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ];

        $postData = array('st_article_nr' => $articleNr, 'st_serial_nr' => $serialNr);

        $product = null;
        // no product ID given -> create product
        $requestString = 'products';
        $response = $client->request('POST', $baseUrl.$requestString, array_merge($options, ['json' => $postData]));
        $statusCode = $response->getStatusCode();
        if( $statusCode != 201){
            $statusMessage = 'Could not create product.';
            if( $response &&  !empty($response->getBody()) && !empty((string)$response->getBody())){
                $responseContent = json_decode((string)$response->getBody(), true);
                $statusMessage = (array_key_exists('error', $responseContent))?$responseContent['error']:$statusMessage;
                $statusMessage = (array_key_exists('message', $responseContent))?$responseContent['message']:$statusMessage;
            }
            //echo 'Product with serial '.$serialNr.' could not be created ('.$statusMessage.')'."\r\n";
            return array('status'=>false, 'serialNr'=>$serialNr);
        }

        $product = json_decode((string)$response->getBody());
        $product = $product->data;
        //echo 'Product with serial '.$serialNr.' and ID '.$product->id.' created successfully.'."\r\n";
        $resData = array('status'=>true, 'serialNr'=>$serialNr, 'productId'=>$product->id);
        return $resData;

    }
}
