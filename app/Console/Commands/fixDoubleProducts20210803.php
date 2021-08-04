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

        $articleNr = '80000081C1';
        $maxComponentCount = 3;
        $timeLatency = 10; //sec

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
        foreach($products as $product){
            $match = DB::connection('pgsql_pc')->select(
                "select products.st_serial_nr,
                (
                    SELECT COUNT(*)
                    FROM products as prod2
                    WHERE products.id = prod2.parent
                    ) as components ,
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
//            if($i++>3) break;
        }
//        print_r($products);
        return 0;
    }
}
