<html>
<?php

$mysql_server = '192.168.1.107';
$mysqli = new mysqli($mysql_server, "badgers", "honey", "searchInstrument");

if($mysqli->connection_error)
{
    die("Connection failed" . $mysqli->connect_error);
}

$sql = "SELECT CompanyName,instrument,stockSymbol FROM searchInstrument";
$result = $mysqli->query($sql);

if($result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        if($_GET["instrument"] == $row["instrument"])
        {
            echo "<br>";
            echo "$row['CompanyName']";
            echo "$row['stockSymbol']";
        }
    }
}

?>
</html>