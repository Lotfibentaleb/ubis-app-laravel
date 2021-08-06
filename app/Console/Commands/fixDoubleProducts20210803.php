<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use GuzzleHttp;
use Illuminate\Support\Facades\DB;


class fixDoubleProducts20210803 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fixDoubleProducts20210803';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix accidently twice created products due to a delay in registration page';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        //$products = DB::connection('pgsql_pc')->table('products')->where('st_article_nr','80000081C1')->get();


        $articleNr = '80000114B2';
        $maxComponentCount = 2;
        $timeLatency = 7; //sec

        $client = new GuzzleHttp\Client();
/*
ERP_SERVICE_BASE_URL=http://127.0.0.1:8000/api/
PC_SERVICE_BASE_URL=http://127.0.0.1:8087/api/
PIS_SERVICE_BASE_URL=http://127.0.0.1:8082/api/
PIS_SERVICE_BASE_URL2=http://127.0.0.1:8082/api/
*/

        $baseUrlERP = env('ERP_SERVICE_BASE_URL');
        $options = [
            'http_errors'=> false,
            'headers' =>[
                'Authorization' => 'Bearer ' .env('PIS_BEARER_TOKEN'),
                'Accept'        => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ];


        // find all single entry SCONs
        $products = DB::connection('pgsql_pc')->select(
            "select products.st_serial_nr,
            (
            SELECT COUNT(*)
            FROM products as prod2
            WHERE products.id = prod2.parent
            ) as components,
        products.st_article_nr, products.id, products.lifecycle, products.created_at as product_creation
        from products
        where products.st_article_nr ='".$articleNr."'
        and (
            SELECT COUNT(*)
            FROM products as prod3
            WHERE products.id = prod3.parent
            ) = 1
        order by products.st_serial_nr desc;");

        $i=0;
        $matchCount = array();
        foreach($products as $product){
            $match = DB::connection('pgsql_pc')->select(
                "select products.st_serial_nr,
                (
                    SELECT COUNT(*)
                    FROM products as prod2
                    WHERE products.id = prod2.parent
                ) as components ,
                (
                    SELECT COUNT(*)
                    FROM device_records dr
                    WHERE products.id = dr.products_id
                ) as device_records ,
                products.st_article_nr, products.id, products.lifecycle, products.created_at as product_creation
                from products
                where products.st_article_nr ='".$articleNr."'
                and (products.created_at - interval '".$timeLatency." seconds', products.created_at + interval '".$timeLatency." seconds') overlaps (TIMESTAMP '".$product->product_creation."', TIMESTAMP '".$product->product_creation."')
                and (
                    SELECT COUNT(*)
                    FROM products as prod3
                    WHERE products.id = prod3.parent
                    ) != ".$maxComponentCount.";
                "
            );
            print_r($match);
            if( !array_key_exists(count($match), $matchCount) ){
                $matchCount[count($match)] = 1;
            }else{
                $matchCount[count($match)]++;
            }
            // TODO: merge 2 entry values
            if( count($match) == 2 ){
                $master = null;
                $slave = null;
                // select slave
                if( $match[0]->components == 1 && $match[0]->device_records == 0 && $match[1]->components == 1 && $match[1]->device_records == 0){
                    // merge using first generated serial-nr. as master
                    if( intval($match[0]->st_serial_nr) >  intval($match[1]->st_serial_nr) ){
                        $master = $match[1];
                        $slave = $match[0];
                    }else{
                        $master = $match[0];
                        $slave = $match[1];
                    }
                    echo "Selected master by first serial nr. as both products have no measurements\n";
                    //echo "Skip entry for serial ".$match[1]->st_serial_nr." and ".$match[0]->st_serial_nr." as there could no slave be selected\n";
                    //continue;   // Go on with loop
                }elseif( $match[0]->components == 1 && $match[0]->device_records == 0){
                    $master = $match[1];
                    $slave = $match[0];
                }elseif($match[1]->components == 1 && $match[1]->device_records == 0){
                    $master = $match[0];
                    $slave = $match[1];
                };

                if( $master != null ){
                    // check if there is a delivery note for slave
                    $requestString = 'stock/articlenr/'.$articleNr;
                    $payload['serials'] = array($slave->st_serial_nr); // , '029286', '029284'
                    $response = $client->request('GET', $baseUrlERP.$requestString, array_merge($options, ['json' => $payload]));
                    $statusCode = $response->getStatusCode();
                    if( $statusCode != 200){
                        echo "Could not request shipping data for \n";
                    }
                    $responseContent = json_decode((string)$response->getBody(), true);
                    if( !empty($responseContent) ){
                        // found shippment
                        echo "There is a shippment registered for slave ".$slave->st_serial_nr.". So we skip this entry\n";
                        continue;   // Go on with loop
                    }else{
                        echo "Shipping requested, but non found for ".$slave->st_serial_nr.". Continue processing\n";
                    }
                    // we have a master, fetch slave component and move over
                    $updateComponent = DB::connection('pgsql_pc')->select("update products set parent = '".$master->id."' where parent = '".$slave->id."'");
                    echo "Merge executed for ".$master->st_serial_nr."(*) and ".$slave->st_serial_nr."\n";
                }else{
                    echo "Skip entry for serial ".$match[1]->st_serial_nr." and ".$match[0]->st_serial_nr." as there seems something wrong\n";
                }
            }
//            if($i++>3) break;
        }

        print_r($matchCount);

        // TODO: Cleanup 0 entry values
		// Note: must check before cleanup:
		// - was delivered to customer?
		// - 0 components
		// - take care on components which had been created manually
/*        $deleteZeroComponentProducts = DB::connection('pgsql_pc')->select("
            delete from products
            where products.st_article_nr ='".$articleNr."'
            and (
                SELECT COUNT(*)
                FROM products as prod3
                WHERE products.id = prod3.parent
            ) = 0
            and (
                SELECT COUNT(*)
                FROM device_records as records
                WHERE products.id = records.products_id
                ) = 0;
            ");
*/
        $zeroComponentProductsToDelete = DB::connection('pgsql_pc')->select("
        select products.st_serial_nr from products
        where products.st_article_nr ='".$articleNr."'
        and (
            SELECT COUNT(*)
            FROM products as prod3
            WHERE products.id = prod3.parent
        ) = 0
        and (
            SELECT COUNT(*)
            FROM device_records as records
            WHERE products.id = records.products_id
            ) = 0
        and (products.st_serial_nr::INTEGER < 27384 or products.st_serial_nr::INTEGER > 27618)
        and (products.st_serial_nr::INTEGER < 26352 or products.st_serial_nr::INTEGER > 26501)
        and (products.st_serial_nr::INTEGER < 26627 or products.st_serial_nr::INTEGER > 26776);
        ");

        $serialsToDelete = array();
        array_map( function( $val ) use(&$serialsToDelete) {
            $serialsToDelete[] = $val->st_serial_nr;
        }, $zeroComponentProductsToDelete );

        $requestString = 'stock/articlenr/'.$articleNr;
        $payload['serials'] = $serialsToDelete; // , '029286', '029284'
        $response = $client->request('GET', $baseUrlERP.$requestString, array_merge($options, ['json' => $payload]));
        $statusCode = $response->getStatusCode();
        if( $statusCode != 200){
            echo "Could not request shipping data for serials\n";
        }else{
            $responseContent = json_decode((string)$response->getBody(), true);
            $serialsDelivered = array();
            array_map( function( $val ) use(&$serialsDelivered) {
                $serialsDelivered[] = $val['serialNumber'];
            }, $responseContent );

            $serialsDelivered = '\''.implode ('\',\'', $serialsDelivered).'\'';   // implode could not use PDO::quote, but we should be in an save environment

            $zeroComponentProductsToDelete = DB::connection('pgsql_pc')->select("
            delete from products
            where products.st_article_nr ='".$articleNr."'
            and (
                SELECT COUNT(*)
                FROM products as prod3
                WHERE products.id = prod3.parent
            ) = 0
            and (
                SELECT COUNT(*)
                FROM device_records as records
                WHERE products.id = records.products_id
                ) = 0
            and (products.st_serial_nr::INTEGER < 27384 or products.st_serial_nr::INTEGER > 27618)
            and (products.st_serial_nr::INTEGER < 26352 or products.st_serial_nr::INTEGER > 26501)
            and (products.st_serial_nr::INTEGER < 26627 or products.st_serial_nr::INTEGER > 26776)
            and products.st_serial_nr not in (".$serialsDelivered.")
            ");

            echo "Einträge gelöscht :".count($zeroComponentProductsToDelete)."\n";
        }
        return 0;
    }
}
