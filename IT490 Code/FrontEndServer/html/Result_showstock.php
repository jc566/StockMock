<!DOCTYPE html>

<html>
<body>


<?php

session_start();


echo "Welcome, user " . $_SESSION['username'] . "!";

?>

<table>
    <tr>
        <th><a href="/welcome.php"> Home</th>
        <th><a href="/portfolio.php"> Portfolio</th>
        <th><a href="/showfile.php"> Stock Search</th>
        <th><a href="/confirm_buy.php"> Buy</th>
        <th><a href="/sell.php"> Sell</th>
	<th><a href="/buybid.php">Buy Bids</th>
	<th><a href="/sellbid.php">Sell Bids</th>
    </tr>
</table>

</body>
</html>



<?php

require_once "/home/ashish/Desktop/rabbitmq_test/rpc_client.php";

//echo $GOTS = $_POST["Displaystk"];

session_start();



$fibonacci_rpc = new FibonacciRpcClient();
//$response = $fibonacci_rpc->test();


$symbol = $_POST['Displaystk'];
$data = array("RequestType"=>'BasicInfo', "Symbol"=>strtolower($symbol));
$x = $fibonacci_rpc->Send($data);

//echo "response: ";
//echo var_dump($x);
echo "<br>\n";
$Name = $x->Name;
echo "Name: ". $Name."<br>\n";
$SE = $x->SE;
echo "SE: ". $SE."<br>\n";
$CEO = $x->CEO;
echo "CEO: ". $CEO."<br>\n";
$URL = $x->URL;
echo "URL: ". $URL."<br>\n";
$Desc = $x->Desc;
echo "Desc: ". $Desc."<br>\n";
$Open = $x->Open[0];
echo "Open: ". $Open."<br>\n";
$Close = $x->Close[0];
echo "Close: ". $Close."<br>\n";
$High = $x->High[0];
echo "High: ". $High."<br>\n";
$Low = $x->Low[0];
echo "Low: ". $Low."<br>\n";

//use functionDB here to display 

?>
