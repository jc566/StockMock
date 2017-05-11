<?php
//require_once "/home/ashish/Desktop/rabbitmq_test/send.php";
require_once "/home/ashish/Desktop/rabbitmq_test/rpc_client.php";
//require_once "/home/ashish/Desktop/rabbitmq_test/receive3.php";

$username = $_POST["username"];
$password = $_POST["password"];
$startMoney = 5000;

//$data = array("RequestType" => "Buy", "Symbol" => $symbol, "Quantity"=>$qty);

//$fibonacci_rpc = new FibonacciRpcClient();
//$response = $fibonacci_rpc->test();

//$x = $fibonacci_rpc->Send($data);

$mysql_server = '192.168.0.103';
$mysqli = mysqli_connect($mysql_server, "badgers", "honey", "user_info", "3306");
if (mysqli_connect_errno($mysqli)) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
} else {
    //echo "Connection worked";
}



//echo "creating user: " . $username . " and password: " . $password;

$qry = "insert into LogIn values ('$username','$password')";
//echo  "xxxxxxxxxxxxxxxxx";
//var_dump($qry);
$result = $mysqli->query($qry);


$qry2 = "insert into bank values ('$username','$startMoney')";
//var_dump($qry2);
$result = $mysqli->query($qry2)

//echo "response: ";
//echo var_dump($x);

?>