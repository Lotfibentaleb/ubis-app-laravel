<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp;

class addManualData80000109C1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:addManualData80000109C1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $data = array(
            ['BL_Date' => '210628', 'BL_Ser'=>'000083','PCB_Date'=>'250624','PCB_Ser'=>'001138','serial'=>'028534'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000082','PCB_Date'=>'250630','PCB_Ser'=>'001140','serial'=>'028535'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000090','PCB_Date'=>'250627','PCB_Ser'=>'001150','serial'=>'028536'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000091','PCB_Date'=>'250626','PCB_Ser'=>'001143','serial'=>'028537'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000086','PCB_Date'=>'250628','PCB_Ser'=>'001139','serial'=>'028538'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000094','PCB_Date'=>'250623','PCB_Ser'=>'000968','serial'=>'028539'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000036','PCB_Date'=>'250625','PCB_Ser'=>'001142','serial'=>'028540'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000085','PCB_Date'=>'250622','PCB_Ser'=>'000940','serial'=>'028541'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000135','PCB_Date'=>'250629','PCB_Ser'=>'000809','serial'=>'028542'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000084','PCB_Date'=>'250621','PCB_Ser'=>'000947','serial'=>'028543'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000096','PCB_Date'=>'250649','PCB_Ser'=>'000764','serial'=>'028544'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000104','PCB_Date'=>'250642','PCB_Ser'=>'000892','serial'=>'028545'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000093','PCB_Date'=>'250643','PCB_Ser'=>'000969','serial'=>'028546'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000142','PCB_Date'=>'250651','PCB_Ser'=>'000814','serial'=>'028547'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000088','PCB_Date'=>'250648','PCB_Ser'=>'000950','serial'=>'028548'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000151','PCB_Date'=>'250647','PCB_Ser'=>'000760','serial'=>'028549'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000105','PCB_Date'=>'250641','PCB_Ser'=>'000957','serial'=>'028550'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000100','PCB_Date'=>'250644','PCB_Ser'=>'000763','serial'=>'028551'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000089','PCB_Date'=>'250646','PCB_Ser'=>'000378','serial'=>'028552'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000095','PCB_Date'=>'250650','PCB_Ser'=>'000998','serial'=>'028553'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000149','PCB_Date'=>'250635','PCB_Ser'=>'000920','serial'=>'028554'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000154','PCB_Date'=>'250639','PCB_Ser'=>'000913','serial'=>'028555'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000137','PCB_Date'=>'250632','PCB_Ser'=>'000971','serial'=>'028556'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000153','PCB_Date'=>'250636','PCB_Ser'=>'000795','serial'=>'028557'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000152','PCB_Date'=>'250637','PCB_Ser'=>'000926','serial'=>'028558'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000136','PCB_Date'=>'250638','PCB_Ser'=>'000797','serial'=>'028559'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000150','PCB_Date'=>'250631','PCB_Ser'=>'000810','serial'=>'028560'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000099','PCB_Date'=>'250634','PCB_Ser'=>'000801','serial'=>'028561'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000092','PCB_Date'=>'250640','PCB_Ser'=>'000787','serial'=>'028562'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000112','PCB_Date'=>'250633','PCB_Ser'=>'000769','serial'=>'028563'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000147','PCB_Date'=>'250653','PCB_Ser'=>'000915','serial'=>'028564'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000121','PCB_Date'=>'250653','PCB_Ser'=>'000789','serial'=>'028565'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000145','PCB_Date'=>'250653','PCB_Ser'=>'000799','serial'=>'028566'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000141','PCB_Date'=>'250653','PCB_Ser'=>'000816','serial'=>'028567'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000148','PCB_Date'=>'250653','PCB_Ser'=>'000817','serial'=>'028568'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000081','PCB_Date'=>'250653','PCB_Ser'=>'000879','serial'=>'028569'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000120','PCB_Date'=>'250653','PCB_Ser'=>'000777','serial'=>'028570'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000075','PCB_Date'=>'250653','PCB_Ser'=>'000785','serial'=>'028571'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000131','PCB_Date'=>'250653','PCB_Ser'=>'000895','serial'=>'028572'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000073','PCB_Date'=>'250653','PCB_Ser'=>'000796','serial'=>'028573'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000072','PCB_Date'=>'250653','PCB_Ser'=>'000782','serial'=>'028574'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000125','PCB_Date'=>'250653','PCB_Ser'=>'000951','serial'=>'028575'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000079','PCB_Date'=>'250653','PCB_Ser'=>'000778','serial'=>'028576'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000108','PCB_Date'=>'250653','PCB_Ser'=>'000793','serial'=>'028577'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000117','PCB_Date'=>'250653','PCB_Ser'=>'000818','serial'=>'028578'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000102','PCB_Date'=>'250653','PCB_Ser'=>'000806','serial'=>'028580'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000126','PCB_Date'=>'250653','PCB_Ser'=>'000808','serial'=>'028581'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000116','PCB_Date'=>'250653','PCB_Ser'=>'000798','serial'=>'028582'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000113','PCB_Date'=>'250653','PCB_Ser'=>'000815','serial'=>'028583'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000114','PCB_Date'=>'250653','PCB_Ser'=>'000821','serial'=>'028584'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000106','PCB_Date'=>'250653','PCB_Ser'=>'001033','serial'=>'028585'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000110','PCB_Date'=>'250653','PCB_Ser'=>'001144','serial'=>'028586'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000111','PCB_Date'=>'250653','PCB_Ser'=>'001149','serial'=>'028587'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000109','PCB_Date'=>'250653','PCB_Ser'=>'000823','serial'=>'028588'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000118','PCB_Date'=>'250653','PCB_Ser'=>'000982','serial'=>'028589'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000066','PCB_Date'=>'250653','PCB_Ser'=>'000791','serial'=>'028590'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000119','PCB_Date'=>'250653','PCB_Ser'=>'000955','serial'=>'028591'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000069','PCB_Date'=>'250653','PCB_Ser'=>'000802','serial'=>'028592'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000107','PCB_Date'=>'250653','PCB_Ser'=>'001151','serial'=>'028593'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000103','PCB_Date'=>'250653','PCB_Ser'=>'000967','serial'=>'028594'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000138','PCB_Date'=>'250653','PCB_Ser'=>'000970','serial'=>'028595'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000140','PCB_Date'=>'250653','PCB_Ser'=>'000966','serial'=>'028596'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000139','PCB_Date'=>'250653','PCB_Ser'=>'000965','serial'=>'028597'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000132','PCB_Date'=>'250653','PCB_Ser'=>'000759','serial'=>'028598'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000133','PCB_Date'=>'250653','PCB_Ser'=>'000889','serial'=>'028599'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000122','PCB_Date'=>'250653','PCB_Ser'=>'000960','serial'=>'028600'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000130','PCB_Date'=>'250653','PCB_Ser'=>'000952','serial'=>'028601'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000123','PCB_Date'=>'250653','PCB_Ser'=>'000946','serial'=>'028602'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000128','PCB_Date'=>'250653','PCB_Ser'=>'000963','serial'=>'028603'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000129','PCB_Date'=>'250653','PCB_Ser'=>'000516','serial'=>'028604'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000062','PCB_Date'=>'250653','PCB_Ser'=>'000790','serial'=>'028605'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000077','PCB_Date'=>'250653','PCB_Ser'=>'000803','serial'=>'028606'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000060','PCB_Date'=>'250653','PCB_Ser'=>'000794','serial'=>'028607'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000067','PCB_Date'=>'250653','PCB_Ser'=>'000788','serial'=>'028608'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000076','PCB_Date'=>'250653','PCB_Ser'=>'000804','serial'=>'028609'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000061','PCB_Date'=>'250653','PCB_Ser'=>'000800','serial'=>'028610'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000070','PCB_Date'=>'250653','PCB_Ser'=>'000807','serial'=>'028611'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000059','PCB_Date'=>'250653','PCB_Ser'=>'000792','serial'=>'028612'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000071','PCB_Date'=>'250653','PCB_Ser'=>'000768','serial'=>'028613'],
            ['BL_Date' => '210628', 'BL_Ser'=>'000068','PCB_Date'=>'250653','PCB_Ser'=>'000813','serial'=>'028614'],
        );

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

        foreach($data as $entry){
            $postData = array('st_article_nr' => '80000109C1',
            'st_serial_nr' => $entry['serial'],
            'production_order_nr' => 'prod_marker_2');
            $requestString = 'products';
            $response = $client->request('POST', $baseUrl.$requestString, array_merge($options, ['json' => $postData]));
            $statusCode = $response->getStatusCode();
            if( $statusCode != 200 && $statusCode != 201){
                echo 'Could not create product ('.$statusCode.')'.$entry['serial']." !!!!  \n";
                $responseContent = json_decode((string)$response->getBody(), true);
                print_r($responseContent);
                continue;
            }
            // we have a product
            $responseContent = json_decode((string)$response->getBody(), true);
            $productId = $responseContent['data']['id'];

            $requestString = 'products/'.$productId.'/components';
            $response = $client->request('POST', $baseUrl.$requestString, array_merge($options, ['json' => [
                'st_article_nr' => '10000972C1',    // Touch-Display-Module_1-8IN_round
                'serial_nr' => $entry['BL_Ser']
                ]]));
            $statusCode = $response->getStatusCode();
            if( $statusCode != 200 && $statusCode != 201){
                echo 'Could not add component 10000972C1 to product ('.$statusCode.')'.$entry['serial']." !!!!  \n";
                $responseContent = json_decode((string)$response->getBody(), true);
                print_r($responseContent);
                continue;
            }

            $requestString = 'products/'.$productId.'/components';
            $response = $client->request('POST', $baseUrl.$requestString, array_merge($options, ['json' => [
                'st_article_nr' => '1600000001 ',    // Touch-Display-Module_1-8IN_round
                'serial_nr' => $entry['PCB_Ser']
                ]]));
            $statusCode = $response->getStatusCode();
            if( $statusCode != 200 && $statusCode != 201){
                echo 'Could not add component 1600000001  to product ('.$statusCode.')'.$entry['serial']." !!!!  \n";
                $responseContent = json_decode((string)$response->getBody(), true);
                print_r($responseContent);
                continue;
            }
            echo 'Product '.$entry['serial']." created successfully with 2 components \n";
        }
        return 0;
    }
}
