<?php 
 //   $hardcoded_username = "Jatan";
  //  $hardcoded_password = "password";
    
    $Username = $_POST["ucid"];
    $Password = $_POST["pass"];
    
    
$mysql_server = '192.168.0.106';
    //$mysqli = mysqli_connect($mysql_server, "badgers", "honey", "user_info"
    
    
    $conn = new mysqli($mysql_server, "badgers", "honey", "user_info");
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
	//echo "connected";
	//echo "connected";
}
        
        $sql = "select * from LogIn where username = '$Username' and password = '$Password'";
        $result = $conn->query($sql);
        //var_dump($result);
        
        
        //echo "-----------------";
        $x = 0;
        	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) 
			{
    			$x++;
  		  		
    		}
	}
	
	redirect($x);
        
        //$row = mysqli_num_rows($result);
    
    //echo "row: " . $row;
    
        
function redirect($vv){
    if ($vv == 1)
        {
        //echo "log in worked";
        //echo "call";
        echo header('Location: welcome.php');
        //echo "It worked";
        //console.log("It worked");
        } else 
        {
        echo "<script> alert('Wrong Credentials, Please Sign up or use correct information');";
        echo "window.location.href='index.html';";
        echo "</script>;";
        }
$conn->close();
        

}


?>