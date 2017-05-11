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
        <th><a href="/confirm_buy.php"> Buy Stock</th>
        <th><a href="/sell.php"> Sell Stock</th>
	<th><a href="/buybid.php">Buy Bids</th>
	<th><a href="/sellbid.php">Sell Bids</th>
    </tr>
        <th><a href="/logout.php">Logout</th>
</table>

<br><br>
<h1>See user portfolio</h1> 
<p>View your current portfolio: </p>
<form action="portfolio.php" method="POST">
<input type="submit"  value="Display Current Portfolio">
</form>

--------------------------------
<h1>Stock Search</h1> 
<p>This option gives you basic information on a partifcular stock</p>
<form action="showfile.php" method="POST">
<input type="submit"  value="Display Basic Info">
</form>

--------------------------------
<h1>Buy Stock</h1> 
<form action="confirm_buy.php" method="POST">
<input type="submit"  value="Buy">
</form>

--------------------------------
<h1>Sell Stock</h1> 
<form action="sell.php" method="POST">
<input type="submit"  value="Sell">
</form>

--------------------------------
</body>
</html>
