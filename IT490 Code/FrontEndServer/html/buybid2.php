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
    </tr>
        <th><a href="/logout.php">Logout</th>
</table>

</body>
</html>


<?php
//require_once "/home/ashish/Desktop/rabbitmq_test/send.php";
require_once "/home/ashish/Desktop/rabbitmq_test/rpc_client.php";
//require_once "/home/ashish/Desktop/rabbitmq_test/receive3.php";

$symbol = $_POST["Buystk"];
$price = $_POST["price"];
$qty = $_POST["Buystk1"];
$username = $_SESSION['username'];

$data = array("RequestType" => "BuyBid", "Username" => $username, "Symbol" => strtolower($symbol), "Quantity"=>$qty, "AskPrice"=>$price);

$fibonacci_rpc = new FibonacciRpcClient();
//$response = $fibonacci_rpc->test();

$x = $fibonacci_rpc->Send($data);

echo "response: ";
echo var_dump($x);



?>