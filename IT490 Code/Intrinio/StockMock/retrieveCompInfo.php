<?php

require 'vendor/autoload.php';
//require_once 'send2.php';
use GuzzleHttp\Client;
//use GuzzleHttp\Message\Request;
//use GuzzleHttp\Message\MessageFactoryInterface;
//use GuzzleHttp\Stream\StreamInterface;
//use GuzzleHttp\Exception\RequestException;
//use GuzzleHttp\Pool;

//$client = new GuzzleHttp\Client(['base_uri'=> 'http://localhost:9090/stocks?sym=aapl']);
function getInfo($string){

    $sym = $string;


   

    //{
  
        
        

        

        
        //Create a request but don't send it immediately
        $client = new Client();

        //$client = new GuzzleHttp\Client(['base_url' => 'http://localhost:9090']);
        $client = new GuzzleHttp\Client(['base_url' => 'http://localhost']);


        $response = $client->get('http://localhost:9090/stocks?sym='.$sym);

        $getInfo = $response->getBody();
        //echo json_encode($getInfo);

        //echo ($getInfo);
        //echo "\nHELLO WORLD\n";
        
        return $getInfo;
        //return "testing it";
        //sendMessage($getInfo);
        //}
    

/*if($symname[0] == '0' and $symname[1] == '2'){
        //echo "success2";
        $sym = trim($symname,"02:");
        //echo $sym;

        
        //Create a request but don't send it immediately
        $client = new Client();

        //$client = new GuzzleHttp\Client(['base_url' => 'http://localhost:9090']);
        $client = new GuzzleHttp\Client(['base_url' => 'http://localhost']);


        $response = $client->get('http://localhost:9090/stocks?sym='.$sym);

        $getInfo = $response->getBody();
        //Required to get specific info on the next stage
        $jsonData = json_decode($getInfo);
        //method to single out one thing
        return $jsonData->Close;
        //return "testing it";
        //sendMessage($getInfo);
        //}
    }*/

        
}
//getInfo('aapl');


?>
