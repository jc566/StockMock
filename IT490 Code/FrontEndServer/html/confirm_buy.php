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

<h1>Buy Stocks</h1> 
<form action="confirm_buy2.php" method="POST">

<input type="text"  name="Buystk"><br>
<input type="number" name="Buystk1"><br><br>
<input type="submit" value="Buy">
</form>
<script>
//<form onSubmit="return confirm('are you sure you want to buy it?');">
</script>

</body>
</html>
