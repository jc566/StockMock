<!DOCTYPE html>
<table>
    <tr>
        <th><a href="/welcome.php">Home</a></th>
        <th><a href="/portfolio.php"> Portfolio</a></th>
        <th><a href="/showfile.php"> Stock Search</a></th>
        <th><a href="/confirm_buy.php"> Buy</a></th>
        <th><a href="/sell.php"> Sell</a></th>
	<th><a href="/buybid.php">Buy Bids</th>
	<th><a href="/sellbid.php">Sell Bids</th>
    </tr>
        <th><a href="/logout.php">Logout</a></th>
</table>
</html>



<?php
session_start();
$username =  $_SESSION['username'];
require_once "/home/ashish/Desktop/rabbitmq_test/rpc_client.php";
echo "Welcome, user ". $_SESSION['username'] . "! ";

$data = array("RequestType" => "AccountBalance", "Username" => $username);

$fibonacci_rpc = new FibonacciRpcClient();
//$response = $fibonacci_rpc->test();

$x = $fibonacci_rpc->Send($data);

//echo "response: \n";
echo "Current Account Balance: $$x";

// /$response = $fibonacci_rpc->test();
$fibonacci_rpc = new FibonacciRpcClient();
$data = array("RequestType"=>'DetailedPortfolio', "Username"=>$username);
$detailPort = $fibonacci_rpc->Send($data);


$countOfRows = count($detailPort->Stocks);


echo "<table style='width:50%'><br><br>";
echo "<tr>";
    echo "<th>Stock Symbol</th>";
    echo "<th>Quantity Pruchased</th>";
    echo "<th>Price Pruchased   </th>";
    echo "<th>Current Stock Price</th>";
    echo "<th>Price Change</th>";
    echo "<th>Percent Change</th>";
    echo "<th>Net Gain</th>";
    echo "<th>View Chart of last 7 days</th>";
    echo "<th>View Chart of last 30 days</th>";
echo "<tr>";




for ($i = 0; $i < $countOfRows;$i++)
{
    
    echo "<tr>";
        echo "<th>" . $detailPort->Stocks[$i] . "</th>"; //Get Stock Symbol
        echo "<th>" . $detailPort->Quantity[$i] . "</th>";//Get Quanitity Purchased
        echo "<th>" . $detailPort->PurchasePrice[$i] . "</th>";//Get Purchase Price
        echo "<th>" . $detailPort->CurrentPrice[$i] . "</th>";//Get current Price
        echo "<th>" . round($detailPort->PriceChange[$i], 2) . "</th>";//Get Price Change
        echo "<th>" . round($detailPort->PercentChange[$i],2) . "%</th>";//Get Percent Change
        echo "<th>" . round($detailPort->NetGain[$i],2) . "</th>";//Get Net Gain
        echo "<th><form action='ViewChart.php'>
            <input type='submit' name='Stock' value=".$detailPort->Stocks[$i]." />
            </form></th>"; // VIEW CHART LINK
        echo "<th><form action='ViewChart30.php'>
            <input type='submit' name='Stock' value=".$detailPort->Stocks[$i]." />
            </form></th>"; // VIEW CHART LINK
    echo "</tr>";
}
echo "</table><br><br>";
?>
</html>




