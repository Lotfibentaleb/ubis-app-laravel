<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use GuzzleHttp;


class addMissingMeasurements20210623 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:addMissingMeasurements20210623';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load CSV files and check with DB containment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function readCSV($csvFile, $array)
    {
        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
        }
        fclose($file_handle);
        return $line_of_text;
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $directory = 'temp';
        $files = Storage::files($directory);
        $filenames = array();
        foreach($files as $file){
            $fo = Str::of($file);
            if( $fo->contains('.csv') ){
                $value = $fo->basename('.csv')->explode('_');
                $entry = array();
                $entry['file'] = $file;
                $entry['serial'] = $value[1];
                $entry['datetime'] = new Carbon($value[2]);
                $filenames[] = $entry;
            }
        }

        $translation = array();
        $translation['Red Lv purity (Saturation)'] = 'saturation_red';
        $translation['Red Lv wavelength'] = 'wavelength_red';
        $translation['Black  Lv Mean'] = 'luminance_black';
        $translation['White  Lv Mean'] = 'luminance_white';
        $translation['Blue Lv purity (Saturation)'] = 'saturation_blue';
        $translation['Blue Lv wavelength'] = 'wavelength_blue';
        $translation['Green Lv purity (Saturation)'] = 'saturation_green';
        $translation['Green Lv wavelength'] = 'wavelength_green';
        $translation['BlackMura Uniformity Black'] = 'homogeneity_black';
        $translation['BlackMura Uniformity White'] = 'homogeneity_white';
        $translation['BlackMura Max Rel. Grad White'] = 'black_mura_gradient';

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

        $count=0;
        foreach($filenames as $file){
            if($count++ > 3) break;
            $csvFileName = $file['file'];
            $csvFile = storage_path( 'app\\'.$csvFileName);
            $data = $this->readCSV($csvFile,array('delimiter' => ';'));

            // translate
            $valueSet = array();
            $valueSet['gamma'] = 0.0;
            $valueSet['ambient_temp'] = 0;
            $valueSet['heating_temp'] = 0;
            $valueSet['heating_time'] = 0;
            $valueSet['contrast_white_black'] = 0;
            $valueSet['chromatisity_white_x'] = 0.318;
            $valueSet['chromatisity_white_y'] = 0.318;

            foreach($data as $entry){
                if( empty($entry) ) continue;
                if(is_string($entry[0]) && array_key_exists($entry[0], $translation)){
                    $valueSet[$translation[$entry[0]]] = $entry[4];
                }
            }

            $product = null;
            $requestString = 'products/'.urlencode($file['serial']).'?lookup_subcomponents=1&article_nr=80000114B2';
            echo 'Serial: '.$requestString."\n";
//            print_r($valueSet);

            $response = null;
            $response = $client->request('GET', $baseUrl.$requestString, $options);
            $statusCode = $response->getStatusCode();
            if( $statusCode != 200){
                // 1. check reduced serial nr.
                echo 'Could not find product, check reduced serial nr. '.ltrim($file['serial'], '0')."\n";
                $requestString = 'products/'.urlencode(ltrim($file['serial']).'?lookup_subcomponents=1&article_nr=80000114B2');
                $response = $client->request('GET', $baseUrl.$requestString, $options);
                if( $statusCode != 200){
                    // 2. create product
                    $postData = array('st_article_nr' => '80000114B2', 'st_serial_nr' => $file['serial'], 'production_order_nr' => 'production_marker_1');
                    $requestString = 'products';
                    echo 'Could not find reduced serial nr. -> create product'.$baseUrl.$requestString."\n";
                    $response = $client->request('POST', $baseUrl.$requestString, array_merge($options, ['json' => $postData]));
                    $statusCode = $response->getStatusCode();
                    if( $statusCode != 200 && $statusCode != 201){
                        echo 'Could not create product ('.$statusCode.')'.$file['serial']." !!!!  \n";
                        $responseContent = json_decode((string)$response->getBody(), true);
                        print_r($responseContent);
                        continue;
                    }
                }
            }
            $responseContent = json_decode((string)$response->getBody(), true);
            $productId = $responseContent['data']['id'];
            echo 'Product ID is: '.$productId."\n";

            // now we have a product id, lets add the measurement
            $requestString = 'products/'.$productId.'/section/daisy.800000114B2';
            $measurement['data'] = $valueSet;
            $response = $client->request('POST', $baseUrl.$requestString, array_merge($options, ['json' => $measurement]));
            $statusCode = $response->getStatusCode();
            if( $statusCode != 200 && $statusCode != 201){
                echo 'Could not store measurements ('.$statusCode.')'.$file['serial']." !!!!  \n";
                print_r(json_encode($measurement));
                $responseContent = json_decode((string)$response->getBody(), true);
                print_r($responseContent);
                continue;
            }else{
                echo 'Measurement stored. Continue with next file.'."\n";
            }
            echo "\n";
        }
        return 0;
    }
}
