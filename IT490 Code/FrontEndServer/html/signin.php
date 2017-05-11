<?php
require_once "/home/ashish/Desktop/rabbitmq_test/rpc_client.php";
//require_once "/home/ashish/Desktop/rabbitmq_test/receive3.php";

$stockSym = $_POST['stocksym'];
$fibonacci_rpc = new FibonacciRpcClient();


//$response = $fibonacci_rpc->test();
//$x = $fibonacci_rpc->SignIn($_POST['ucid'], $_POST['pass']);



$data = array('RequestType' => "BasicInfo", 'Symbol' => "$stockSym");

$stock = $fibonacci_rpc->Send(json_encode($data));

//echo $stock->StockSymbol;


//echo "response: $stockSym";
echo var_dump($stock);

//$user = $x->UserName;
//echo "user: ". $user;



?>


