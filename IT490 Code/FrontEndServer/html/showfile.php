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


<h1> Stock Name </h1>

<p id="search"></p>


<p>
<form action="instrumentSearchResults.php">
<p>Search By Instrument</p>
<select name="instrument">
<option value="Stock">Stocks</option>
<option value="ETF">ETFs</option>
<option value="Indices">Indicies</option>

<input type="submit">
</select>
</form>
</p>

</html>

<script>

function loadSearchBar() {
  var xhttp;
  if (window.XMLHttpRequest) {
    // code for modern browsers
    xhttp = new XMLHttpRequest();
    } else {
    // code for IE6, IE5
    xhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("search").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "autofillSearch.html", true);
  xhttp.send();
}

loadSearchBar();
</script>
