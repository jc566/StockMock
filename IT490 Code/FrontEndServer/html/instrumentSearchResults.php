<html>
<?php

$mysql_server = '192.168.1.107';
$mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");

$sql = "SELECT CompanyName,instrument,stockSymbol FROM searchInstrument";
$result = $mysqli->query($sql);
echo $_GET["instrument"];
echo "<th><form action='ViewChart.php'>";
if($result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        if($_GET["instrument"] == $row["instrument"])
        {
            
            echo "<br>";
            echo $row['CompanyName'] . "<br>";
            //echo $row['stockSymbol'] . "<br>";
             echo "<th><form action='ViewChart30.php'>
            <input type='submit' name='Stock' value=".$row['stockSymbol']." />
            </form></th>";
        }
    }
}

?>
</html>