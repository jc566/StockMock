
<html>

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

</html>

<?php

require_once "/home/ashish/Desktop/rabbitmq_test/rpc_client.php";

$username = $_POST["username"];
$password = $_POST["password"];

$data = array("RequestType" => "SignUp", "Username" => $username, "Password" => $password);

$fibonacci_rpc = new FibonacciRpcClient();
//$response = $fibonacci_rpc->test();

$x = $fibonacci_rpc->Send($data);

echo "response: ";
if ($x->SignUpStatus == 1){
    echo "Sign Up success";
    redirect(1, $username);
}
else
{
    echo "Sign Up Fail";
}
echo var_dump($x);
function redirect($vv, $username){
    if ($vv == 1)
        {
        //echo "log in worked";
        //echo "call";
        session_start();
        $_SESSION["username"] = $username;
        echo header('Location: welcome.php');
        //echo "It worked";
        //console.log("It worked");
        } else {
        echo "<script> alert('Wrong Credentials, Please Sign up or use correct information');";
        echo "window.location.href='index.html';";
        echo "</script>;";
        }

}

?>
