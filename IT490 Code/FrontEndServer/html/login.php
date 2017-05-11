<?php 
require_once "/home/ashish/Desktop/rabbitmq_test/rpc_client.php";
$Username = $_POST["username"];
$Password = $_POST["password"];
//$Username = "ashish";//$_POST["ucid"];
//$Password = "password";//$_POST["pass"];   
    
$fibonacci_rpc = new FibonacciRpcClient();
$data = array("RequestType"=>'SignIn', "Username"=>$Username, "Password"=>$Password);
$x = $fibonacci_rpc->Send($data);
var_dump($x);    
redirect($x->LogInStatus, $Username);

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