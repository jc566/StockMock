<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once "/home/frontend/rabbitMQ/infoFunctions.php";
require_once "/home/frontend/rabbitMQ/errorLogRecorder.php";
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$rpc_server = '192.168.1.106';

$connection = new AMQPStreamConnection($rpc_server, 5672, 'admin', 'password');
$channel = $connection->channel();



$channel->queue_declare('rpc_queue', false, false, false, false);

echo " [x] Awaiting RPC requests\n";
$callback = function($req) {
	//$n = intval($req->body);
	$requestIn = json_decode($req->body);
	
	//echo "var dump: ";	
	var_dump($requestIn);///this point is decoded
	/////////--------------//////////
	
	//Error Log Function
	if($requestIn->RequestType == 'WriteError'){
	try{
		
		writeError($requestIn->Error);
            }catch (Exception $e){
                echo "Error in Error Log rpc_server.php::::: \n" . $e->getMessage();
                writeError("Error in Error Log rpc_server.php::::: \n" . $e->getMessage());
            }
	}

	//Basic Info Function
	if($requestIn->RequestType == 'BasicInfo'){
	try{
		
		$data = array('Symbol' => $requestIn->Symbol);
		
		
		$Result_ShowBasicInfo = showBasicInfo($data);
		//echo "var dump------- of $Result_ShowBasicInfo: " . var_dump($Result_ShowBasicInfo);
		
		$msg = new AMQPMessage(
		$Result_ShowBasicInfo,
		array('correlation_id' => $req->get('correlation_id'))
		);

		$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
		$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
            }catch (Exception $e){
        echo "Error in RequestType BasicInfo rpc_server.php::::: \n" . $e->getMessage();
        writeError("Error in RequestType BasicInfo rpc_server.php::::: \n" . $e->getMessage());
            }
	}

        //Buy Function
        elseif($requestIn->RequestType == 'Buy'){
        try{
		$data = array('Username' => $requestIn->Username, 'Symbol' => $requestIn->Symbol, 'Quantity'=>$requestIn->Quantity);
		
		
		$Result_BuyInfo = buyStock($data);
		//echo "xxxxxxxxxxxxxxxxxxxx------ Buy called -----xxxx";
		//var_dump($Result_ShowBasicInfo);
                $msg = new AMQPMessage(
		json_encode($Result_BuyInfo),
		
		array('correlation_id' => $req->get('correlation_id'))
		);
		echo var_dump($Result_BuyInfo);

		$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
		$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
            }catch (Exception $e){
        echo "Error in RequestType Buy rpc_server.php::::: \n" . $e->getMessage();
        writeError("Error in RequestType Buy rpc_server.php::::: \n" . $e->getMessage());
            }
	}

	//Sell Function
	elseif($requestIn->RequestType == 'Sell'){
	try{
		$data = array('Username' => $requestIn->Username, 'Symbol' => $requestIn->Symbol, 'Quantity'=>$requestIn->Quantity);
		
		
		$Result_SellInfo = sellStock($data);
		echo "xxxxxxxxxxxxxxxxx------Sell called -----xxxxx";
		var_dump($Result_SellInfo);
		
		$msg = new AMQPMessage(
		json_encode($Result_SellInfo),
		array('correlation_id' => $req->get('correlation_id'))
		);

		$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
		$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
            }catch (Exception $e){
        echo "Error in RequestType Sell rpc_server.php::::: \n" . $e->getMessage();
        writeError("Error in RequestType Sell rpc_server.php::::: \n" . $e->getMessage());
            }
	}
	
	//DisplayPortfolio
	elseif($requestIn->RequestType == 'DisplayPortfolio'){
	try{
		$data = array('Username' => $requestIn->Username);
		
		
		$Result_DisplayPortfolio = portfolioDB($data);
				
		$msg = new AMQPMessage(
		$Result_DisplayPortfolio,
		array('correlation_id' => $req->get('correlation_id'))
		);

		$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
		$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
            }catch (Exception $e){
        echo "Error in RequestType DisplayPortfolio rpc_server.php::::: \n" . $e->getMessage();
        writeError("Error in RequestType DisplayPortfolio rpc_server.php::::: \n" . $e->getMessage());
            }
	}
	
	//SignUp
	elseif($requestIn->RequestType == 'SignUp'){
	try{
		$data = array('Username' => $requestIn->Username, 'Password' => $requestIn->Password);
		
		
		$Result_DisplayPortfolio = SignUp($data);
				
		$msg = new AMQPMessage(
		$Result_DisplayPortfolio,
		array('correlation_id' => $req->get('correlation_id'))
		);

		$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
		$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
            }catch (Exception $e){
        echo "Error in RequestType SignUp rpc_server.php::::: \n" . $e->getMessage();
        writeError("Error in RequestType SignUp rpc_server.php::::: \n" . $e->getMessage());
            }
	}
	
	//PriceChange for portfolioDB
	elseif($requestIn->RequestType == 'DetailedPortfolio'){
        try{
            echo "Detailed POrtfolio gets called";
		$data = array('Username' => $requestIn->Username);
		
		
		$Result_DisplayPriceChange = displayDetailedPortfolio($data);
				
		$msg = new AMQPMessage(
		$Result_DisplayPriceChange,
		array('correlation_id' => $req->get('correlation_id'))
		);

		$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
		$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
            }catch (Exception $e){
        echo "Error in RequestType DetailedPortfolio rpc_server.php::::: \n" . $e->getMessage();
        writeError("Error in RequestType DetailedPortfolio rpc_server.php::::: \n" . $e->getMessage());
            }
            
	}
	
	//SignIn
	elseif($requestIn->RequestType == 'SignIn'){
	try{
		$data = array('Username' => $requestIn->Username, 'Password' => $requestIn->Password);
		
		
		$Result_DisplayPortfolio = SignIn($data);
				
		$msg = new AMQPMessage(
		$Result_DisplayPortfolio,
		array('correlation_id' => $req->get('correlation_id'))
		);

		$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
		$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
            }catch (Exception $e){
        echo "Error in RequestType SignIn rpc_server.php::::: \n" . $e->getMessage();
        writeError("Error in RequestType SignIn rpc_server.php::::: \n" . $e->getMessage());
            }
	}
	
	//Show Last 7
	if($requestIn->RequestType == 'ShowLast7'){
	try{
		$data = array('Symbol' => $requestIn->Symbol);
		
		
		var_dump($data);
		$Result_Last7 = showLast7($data);
		
		
		$msg = new AMQPMessage(
		$Result_Last7,
		array('correlation_id' => $req->get('correlation_id'))
		);

		$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
		$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
            }catch (Exception $e){
        echo "Error in RequestType ShowLast7 rpc_server.php::::: \n" . $e->getMessage();
        writeError("Error in RequestType ShowLast7 rpc_server.php::::: \n" . $e->getMessage());
            }
	}
	//Show Last 30
	if($requestIn->RequestType == 'ShowLast30'){
	try{
		$data = array('Symbol' => $requestIn->Symbol);
		
		
		var_dump($data);
		$Result_Last30 = showLast30($data);
		
		
		$msg = new AMQPMessage(
		$Result_Last30,
		array('correlation_id' => $req->get('correlation_id'))
		);

		$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
		$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
            }catch (Exception $e){
        echo "Error in RequestType ShowLast30 rpc_server.php::::: \n" . $e->getMessage();
        writeError("Error in RequestType ShowLast30 rpc_server.php::::: \n" . $e->getMessage());
            }
	}
	//Buy Bid at Best Price
	if($requestIn->RequestType == 'BuyBid'){
	try{
		$data = array('Username' => $requestIn-> Username,'Symbol' => $requestIn->Symbol, 'Quantity' => $requestIn->Quantity, 'AskPrice' => $requestIn->AskPrice);
		
		
		var_dump($data);
		$Result_BuyBid = addToBuyBestOfferBid($data);
		
		
		$msg = new AMQPMessage(
		$Result_BuyBid,
		array('correlation_id' => $req->get('correlation_id'))
		);

		$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
		$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
            }catch (Exception $e){
        echo "Error in RequestType BuyBid rpc_server.php::::: \n" . $e->getMessage();
        writeError("Error in RequestType BuyBid rpc_server.php::::: \n" . $e->getMessage());
            }
	}
	//Buy Bid at Best Price
	if($requestIn->RequestType == 'SellBid'){
	try{
		$data = array('Username' => $requestIn-> Username,'Symbol' => $requestIn->Symbol, 'Quantity' => $requestIn->Quantity, 'AskPrice' => $requestIn->AskPrice);
		
		
		var_dump($data);
		$Result_SellBid = addToSellBestOfferBid($data);
		
		$msg = new AMQPMessage(
		$Result_SellBid,
		array('correlation_id' => $req->get('correlation_id'))
		);

		$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
		$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
            }catch (Exception $e){
        echo "Error in RequestType SellBid rpc_server.php::::: \n" . $e->getMessage();
        writeError("Error in RequestType SellBid rpc_server.php::::: \n" . $e->getMessage());
            }
	}
	
	//Buy Bid at Best Price
	if($requestIn->RequestType == 'AccountBalance'){
	try{
		$data = array('Username' => $requestIn-> Username);
		
		
		var_dump($data);
		$Result_ViewBalance = viewAccountBalance($data);
		
		$msg = new AMQPMessage(
		$Result_ViewBalance,
		array('correlation_id' => $req->get('correlation_id'))
		);

		$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
		$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
            }catch (Exception $e){
        echo "Error in RequestType SellBid rpc_server.php::::: \n" . $e->getMessage();
        writeError("Error in RequestType SellBid rpc_server.php::::: \n" . $e->getMessage());
            }
	}
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

?>

