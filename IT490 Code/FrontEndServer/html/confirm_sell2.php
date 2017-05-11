<!DOCTYPE html>

<html>
<body>


<?php

session_start();


echo "Welcome, user " . $_SESSION['username'] . "!";

?>

<table>
    <tr>
        <th><a href="/welcome.php">Home</th>
        <th><a href="/portfolio.php"> Portfolio</th>
        <th><a href="/showfile.php"> Stock Search</th>
        <th><a href="/confirm_buy.php"> Buy</th>
        <th><a href="/sell.php"> Sell</th>
	<th><a href="/buybid.php">Buy Bids</th>
	<th><a href="/sellbid.php">Sell Bids</th>
    </tr>
        <th><a href="/logout.php">Logout</th>
</table>

</body>
</html>


<?php
//require_once "/home/ashish/Desktop/rabbitmq_test/send.php";
require_once "/home/ashish/Desktop/rabbitmq_test/rpc_client.php";
//require_once "/home/ashish/Desktop/rabbitmq_test/receive3.php";

$symbol = $_POST["sellstk"];
$qty = $_POST["sellstk1"];
$username = $_SESSION['username'];

$data = array("RequestType" => "Sell", "Username" => $username, "Symbol" => strtolower($symbol), "Quantity"=>$qty);

$fibonacci_rpc = new FibonacciRpcClient();
//$response = $fibonacci_rpc->test();

$x = $fibonacci_rpc->Send($data);

echo "response: ";
echo var_dump($x);



?>
