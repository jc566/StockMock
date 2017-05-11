<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;

//$x = array("Username" => "ashish", "Symbol"=>"goog", "Quantity" => "50");

/***********
Local Tests*
***********/

//displayDetailedPortfolio($x);
//buyStock($x);
//showBasicInfo($x);
//showLast7($x);
//showLast30($x);

//portfolioDB()
$mysql_server = '192.168.1.104';
  
$mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
// Check connection
if ($mysqli->connect_error)
{
    die("Connection failed: " . $mysqli->connect_error);
}
else
{
    echo "connected";
}





function getUniqueNames()
{
    //make connection to SQL server
    global $mysql_server;

    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");

    //make empty array to hold the UNIQUE Usernames
    $stackUsers = array();

    //find the list of UNIQUE user names
    $UsernameQry = "Select * from portfolio";
    $UsernameResult = $mysqli->query($UsernameQry);

    if($UsernameResult->num_rows > 0){
        while ($row = $UsernameResult->fetch_assoc()){

            if(!in_array($row['username'], $stackUsers)){
                array_push($stackUsers, $row['username']);
            }

        }
    }
  
    $SQLresultObj = array('UsersUnique' => $stackUsers);


    return($SQLresultObj);
}

function checkBids()
{
    global $mysql_server;
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
 
 
 
 
    //Retrieve all Values from SellBidOffer
    $indexQry = "SELECT * FROM SellBestBidOffer";
    $indexResult = $mysqli->query($indexQry);
 
    //Create Info Stacks to store Information
    $stackUsername = array();
    $stackSymbol = array();
    $stackQty = array();
    $stackAsk = array();
    $stackIndexNo = array();
    $stackBidType = array();
 
    //Copy the info into arrays in exact same order
    if($indexResult->num_rows > 0)
    { //if there is something in the DB then...
        while ($row = $indexResult->fetch_assoc())
        {
            array_push($stackUsername, $row['username']);
            array_push($stackSymbol, $row['stockSymbol']);
            array_push($stackQty, $row['qty']);
            array_push($stackIndexNo, $row['indexNo']);
            array_push($stackAsk, $row['askPrice']);
        }
    }
  
  
    //Get the size of Arrays
    $arraySize = count($stackUsername);
    
    //Get the List of Unique tickers in SellBids DB
    $SellBidsTickers = getSellBidStockTickers();
 
    //Array that contains Current Price for Stocks
    $currentPriceStack = array();
 
    //get information about DISTINCT tickers only
    foreach($SellBidsTickers["Stocks"] as $symbol)
    {
    
        $tempVar = getInfo($symbol);
        $tempObj = json_decode($tempVar);
        array_push($currentPriceStack, $tempObj->Close[0]); //get the current prices for Tickers
    }
  
    //Get the List of Unique Usernames in Portfolio
  
    $UniqueUsers = getUniqueNames();
  
    //array to hold the total quantity of shares invested to Y TICKER belonging to X USER
    $stackUserQuantity = array();
  
  
    //find the amount of stocks USER X has of TICKER Y
    foreach($UniqueUsers["UsersUnique"] as $key => $user)
    {
        foreach($SellBidsTickers["Stocks"] as $compareKey => $compareSymbol)
        {
            //var_dump($user);
            $tempVal = getStockQuantity($user, $compareSymbol);
            //Keep Track of total amount of Shares held for this stock.
            array_push($stackUserQuantity, $tempVal);
          
        }
    }
  
    //var_dump($stackUserQuantity);
 
 
 
    //Loop and compare each Ticker asking price with Current Prices. Ignore Username at this Level.
    foreach($stackSymbol as $key => $symbol)
    {
        //for each Ticker excluding duplicates in your sell bids DB
        foreach($SellBidsTickers["Stocks"] as $compareKey => $compareSymbol)
        {
            //for each User
            foreach($UniqueUsers["UsersUnique"] as $userKey => $user)
            {
                //var_dump($stackUserQuantity[$userKey]);
              
                //if the Users total Quantity of TICKER Y is greater than or equal to the Quantity asked to be sold
                if($stackUserQuantity[$userKey] >= $stackQty[$key])
                {
                    //if ticker in records matches a ticker in currentPrice data list
                    if ($symbol == $compareSymbol)
                    {
                        //if asking price is above or equal to current price, sell for profit
                        if($stackAsk[$key] <= $currentPriceStack[$compareKey])
                        {
                        //Find the user linked to this current items.
                        //var_dump($stackUsername[$key]);
                        //Create Data Object and Run Sell function
                        $tempArray = array("Username" => $stackUsername[$key],
                                        "Symbol" => $stackSymbol[$key],
                                        "Quantity" => $stackQty[$key]);
                        sellStock($tempArray);
                        //delete the IndexNo of this record in SellBestBidOffer Table
                        deleteFromSellBids($stackIndexNo[$key]);
                        //break out of one layer of the loop
                        break;
                        }
                    }
                }
            }
        }
    }
 
}




/*****************************************
Connects to API and retrieves information*
*****************************************/
function getInfo($string){
    $sym = $string;
    try {
    //Create a request but don't send it immediately
    $client = new Client();

    //$Client is server we are using to connect server API
    $client = new GuzzleHttp\Client(['base_url' => '192.168.1.109']);

    //This 'page' is the one we use to gather Stock Information
    $response = $client->get('192.168.1.109:9090/stocks?sym='.$sym);
  
    $getInfo = $response->getBody();
    
    return $getInfo;
    } catch (Exception $e) {
        echo "Error in getInfo Function in infoFunctions.php", $e->getMessage();
    }
}

/******************************************
Sign In Function
Respons with 1 for success and 0 for failure
*****************************************/
function SignIn($data){
try{
    $username_input = $data["Username"];
    $password_input = $data["Password"];
  
    //purpose is to display portfolio
    global $mysql_server; //reference the global mysql_server
  
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
  

    $qry = "Select * from LogIn where username='$username_input' and password= '$password_input'";
    $result = $mysqli->query($qry);
  
    $x = 0;
    if($result->num_rows > 0)
    {
        while($row = $result->fetch_assoc()){
            $x++;
        }
    }
  
    var_dump($x);
    $returnObj = array('LogInStatus' => $x);
    var_dump($returnObj);
    return (json_encode($returnObj));
  
    $mysqli.close();
    }catch (Exception $e){
        echo "Error in SignIn Function infoFunctions.php::::: \n" . $e->getMessage();
    }
}

function SignUp($data){
try{
    $username_input = $data["Username"];
    $password_input = $data["Password"];
  
    //purpose is to display portfolio
    global $mysql_server; //reference global mysql_server
  
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
  

    $qry = "Insert into LogIn values ('$username_input','$password_input')";
    $result = $mysqli->query($qry);
  
    $qry = "Insert into bank values ('$username_input', 50000)";
    $result = $mysqli->query($qry);
  
    $x = 0;
    if ($result)
    {
        $x = 1;
    } else
    {
        $x = 1000;
    }
  
    var_dump($x);
    $returnObj = array('SignUpStatus' => $x);
    var_dump($returnObj);
    return (json_encode($returnObj));
  
    $mysqli.close();
    }catch (Exception $e){
        echo "Error in SignUp Function infoFunctions.php::::: \n" . $e->getMessage();
    }
}
/**************************************************
Retrieves the current users Account Balance       *
Uses getCurrentAccountBalance and returns json obj*
**************************************************/
function viewAccountBalance($data)
{
    $username = $data["Username"];
   
    $currentBalance = getCurrentAccountBalance($username);
   
    var_dump($currentBalance);
   
    return (json_encode($currentBalance));
}

/*************************************************
Get the Quantity/Ticker requested from User input*
Run calculations to give Total Value             *
*************************************************/
function buyStock($data){
try{
    //retrieve data and set variables accordingly
    $sym = $data["Symbol"];
    $username = $data["Username"];
    $qty = $data["Quantity"];
     
    //grab the information needed
    $jsonObj = getInfo($sym);
    //decode the structure
    $newObj = json_decode($jsonObj);
    //Single out the Closing price aka Current Price
    $currentCost = $newObj->Close[0];
    //var_dump($newObj->Close[0]); //display the closing price
   
    //Check Account Balance to see if the buy is possible
    $accountValue = getCurrentAccountBalance($username);
   
    //$purCost is to store a value in DB, ex: $purCost = API->currentCost;
    $purCost = $currentCost;
  
    //Calculate the total value of your purchase. Cost * Shares
    $totalValue = $purCost * $qty;
   
    if($accountValue >= $totalValue)
    {
        //add this entry into the DB.
        addToPortfolioDB($username,$purCost,$qty,$sym);
        //subtract cost of purchase from Bank Account Balance
        deleteFromAccountBalance($username,$totalValue,$accountValue);
    }
    else
    {
        echo "NOT ENOUGH MONEY";
    }
    //make a new object to store data to return?
    $returnObj = array("TotalValue"=>$totalValue);
    //example of how to single out a value is Below
    //var_dump($returnObj["TotalValue"]);
 
    //returns an array
    return (json_encode($returnObj));
    //returns an json object
    //return (json_encode($returnObj));
    }catch (Exception $e){
        echo "Error in buyStock Function infoFunctions.php::::: \n" . $e->getMessage();
    }
}
function deleteFromAccountBalance($username,$subtractAmount,$currentBal)
{
try{
     //make connection to MYSQL Database
    global $mysql_server; //reference global mysql_server
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
  
   
    $newBalance = $currentBal - $subtractAmount;
  
    $qry = "UPDATE bank SET balance='$newBalance' WHERE username='$username'";
    $result = $mysqli->query($qry);
   }catch (Exception $e){
        echo "Error in deleteFromAccountBalance Function infoFunctions.php::::: \n" . $e->getMessage();
    }
  
}
/*********************************************
Grab the current Balance attached to the User*
*********************************************/
function getCurrentAccountBalance($username)
{
try{
    //placeholder for Account Balance
    $accountValue = 0;
    //make connection to MYSQL Database
    global $mysql_server; //reference global mysql_server
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
  

    $qry = "SELECT * FROM bank WHERE username='$username'";
    $result = $mysqli->query($qry);
   
    if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()){
          
            $accountValue = $row['balance'];
        }
    }
   
   return ($accountValue);
   }catch (Exception $e){
        echo "Error in getCurrentAccountBalance Function infoFunctions.php::::: \n" . $e->getMessage();
    }
   
}
/*
Buy at Best Bid/Offer
allow user to input a number to buy X stock ticker with X quantity
check current prices, if it is same or below user input price, BUY IT
other wise ignore it.

Save buy price in DB. Only way to keep it.
*/
function addToBuyBestOfferBid($data)
{
    $username = $data["Username"]; //get username
    $sym = $data["Symbol"]; //get ticker
    $qty = $data["Quantity"]; //get quantity desired
    $askPrice = $data["AskPrice"]; //get asking price
    
    //Add to DB first
    global $mysql_server; //reference global mysql_server
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
  
    //Insert information to DB
    $qry = "INSERT INTO BuyBestBidOffer (username, stockSymbol, qty, askPrice) 
    Values('$username','$sym','$qty','$askPrice'";
    $result = $mysqli->query($qry);
    
    
}
/**********************************
Adds a new Sell bid into SellBidDB*
**********************************/
function addToSellBestOfferBid($data)
{
    $username = $data["Username"]; //get username
    $sym = $data["Symbol"]; //get ticker
    $qty = $data["Quantity"]; //get quantity desired
    $askPrice = $data["AskPrice"]; //get asking price
    
    //Add to DB first
    global $mysql_server; //reference global mysql_server
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
  
    //Insert information to DB
    $qry = "INSERT INTO SellBestBidOffer (username, stockSymbol, qty, askPrice) 
    Values('$username','$sym','$qty','$askPrice'";
    $result = $mysqli->query($qry);
}





/*****************************************************************
User Chron or Anachron to run this function periodically.        *
Compares Ticker and related asking Prices against Current Prices.*
*****************************************************************/
function checkBuyBids()
{
    global $mysql_server; //reference global mysql_server
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
  
    //Retrieve all Values from BestBidOffer
    $indexQry = "SELECT * FROM BuyBestBidOffer";
    $indexResult = $mysqli->query($indexQry);
  
    //Create Info Stacks to store Information
    $stackUsername = array();
    $stackSymbol = array();
    $stackQty = array();
    $stackAsk = array();
    $stackIndexNo = array();
    $stackBidType = array();
  
    //Copy the info into arrays in exact same order
    if($indexResult->num_rows > 0)
    { //if there is something in the DB then...
        while ($row = $indexResult->fetch_assoc())
        {
            array_push($stackUsername, $row['username']);
            array_push($stackSymbol, $row['stockSymbol']);
            array_push($stackQty, $row['qty']);
            array_push($stackIndexNo, $row['indexNo']);
            array_push($stackAsk, $row['askPrice']);
        }
    }
    //Get the size of Arrays
    $arraySize = count($stackUsername);
       
    //Get the List of Unique tickers in BuyBids DB
    $BuyBidsTickers = getBuyBidStockTickers();
   
    //Array that contains Current Price for Stocks
    $currentPriceStack = array();
   
    //get information about DISTINCT tickers only
    foreach($BuyBidsTickers["Stocks"] as $symbol)
    {
       
        $tempVar = getInfo($symbol);
        $tempObj = json_decode($tempVar);
        array_push($currentPriceStack, $tempObj->Close[0]); //get the current prices for Tickers
    }
   
    //var_dump($currentPriceStack);
   
    //Loop and compare each Ticker asking price with Current Prices. Ignore Username at this Level.
    foreach($stackSymbol as $key => $symbol)
    {
        //for each Ticker excluding duplicates in your buy bids DB
        foreach($BuyBidsTickers["Stocks"] as $compareKey => $compareSymbol)
        {
            //if ticker in records matches a ticker in currentPrice data list
            if ($symbol == $compareSymbol)
            {
                if($stackAsk[$key] <= $currentPriceStack[$compareKey])
                {
                //Find the user linked to this current items.
                //var_dump($stackUsername[$key]);
                //Create Data Object and Run Buy function
                $tempArray = array("Username" => $stackUsername[$key],
                                   "Symbol" => $stackSymbol[$key],
                                   "Quantity" => $stackQty[$key]);
                buyStock($tempArray);
                //delete the IndexNo of this record in BuyBestBidOffer Table
                deleteFromBuyBids($stackIndexNo[$key]);
                }
            }
        }
    }
   
}
/*******************************************
Simply Deletes Current record from BuyBidDB*
*******************************************/
function deleteFromBuyBids($indexNo)
{
    global $mysql_server;//reference to global mysql_server
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
   
    $qry = "DELETE FROM BuyBestBidOffer WHERE indexNo='$indexNo'";
    $qryResult = $mysqli->query($qry);
}
/********************************************************
Searches BuyBidDB and creates a list of DISTINCT tickers*
********************************************************/
function getBuyBidStockTickers()
{

    //make connection to SQL server
    global $mysql_server;//reference to global mysql_server

    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");

    //make empty array to hold the Stock Tickers attached to Username
    $stackTickers = array();

    //find the items that are attached to username and requested Ticker
    $TickerQry = "Select * from BuyBestBidOffer";
    $TickerResult = $mysqli->query($TickerQry);

    if($TickerResult->num_rows > 0){
        while ($row = $TickerResult->fetch_assoc()){

            if(!array_search($row['stockSymbol'], $stackTickers)){
                array_push($stackTickers, $row['stockSymbol']);
            }

        }
    }

    $SQLresultObj = array('Stocks' => $stackTickers);

    //var_dump($SQLresultObj);
    return($SQLresultObj);
}
/*****************************************************************
User Chron or Anachron to run this function periodically.        *
Compares Ticker and related asking Prices against Current Prices.*
*****************************************************************/
function checkSellBids()
{
    global $mysql_server; //reference global mysql_server
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
  
    //Retrieve all Values from SellBidOffer
    $indexQry = "SELECT * FROM SellBestBidOffer";
    $indexResult = $mysqli->query($indexQry);
  
    //Create Info Stacks to store Information
    $stackUsername = array();
    $stackSymbol = array();
    $stackQty = array();
    $stackAsk = array();
    $stackIndexNo = array();
    $stackBidType = array();
  
    //Copy the info into arrays in exact same order
    if($indexResult->num_rows > 0)
    { //if there is something in the DB then...
        while ($row = $indexResult->fetch_assoc())
        {
            array_push($stackUsername, $row['username']);
            array_push($stackSymbol, $row['stockSymbol']);
            array_push($stackQty, $row['qty']);
            array_push($stackIndexNo, $row['indexNo']);
            array_push($stackAsk, $row['askPrice']);
        }
    }
    //Get the size of Arrays
    $arraySize = count($stackUsername);
       
    //Get the List of Unique tickers in SellBids DB
    $SellBidsTickers = getSellBidStockTickers();
   
    //Array that contains Current Price for Stocks
    $currentPriceStack = array();
   
    //get information about DISTINCT tickers only
    foreach($SellBidsTickers["Stocks"] as $symbol)
    {
       
        $tempVar = getInfo($symbol);
        $tempObj = json_decode($tempVar);
        array_push($currentPriceStack, $tempObj->Close[0]); //get the current prices for Tickers
    }
   
    //var_dump($currentPriceStack);
   
    //Loop and compare each Ticker asking price with Current Prices. Ignore Username at this Level.
    foreach($stackSymbol as $key => $symbol)
    {
        //for each Ticker excluding duplicates in your sell bids DB
        foreach($SellBidsTickers["Stocks"] as $compareKey => $compareSymbol)
        {
            //if ticker in records matches a ticker in currentPrice data list
            if ($symbol == $compareSymbol)
            {
                //if asking price is above or equal to current price, sell for profit
                if($stackAsk[$key] >= $currentPriceStack[$compareKey])
                {
                //Find the user linked to this current items.
                //var_dump($stackUsername[$key]);
                //Create Data Object and Run Sell function
                $tempArray = array("Username" => $stackUsername[$key],
                                   "Symbol" => $stackSymbol[$key],
                                   "Quantity" => $stackQty[$key]);
                sellStock($tempArray);
                //delete the IndexNo of this record in SellBestBidOffer Table
                deleteFromSellBids($stackIndexNo[$key]);
                }
            }
        }
    }
   
}
/********************************************
Simply Deletes Current record from SellBidDB*
********************************************/
function deleteFromSellBids($indexNo)
{
    global $mysql_server; //reference to global mysql_server
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
   
    $qry = "DELETE FROM SellBestBidOffer WHERE indexNo='$indexNo'";
    $qryResult = $mysqli->query($qry);
}
/*********************************************************
Searches SellBidDB and creates a list of DISTINCT tickers*
*********************************************************/
function getSellBidStockTickers()
{

    //make connection to SQL server
    global $mysql_server;//reference to global mysql_server

    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");

    //make empty array to hold the Stock Tickers attached to Username
    $stackTickers = array();

    //find the items that are attached to username and requested Ticker
    $TickerQry = "Select * from SellBestBidOffer";
    $TickerResult = $mysqli->query($TickerQry);

    if($TickerResult->num_rows > 0){
        while ($row = $TickerResult->fetch_assoc()){

            if(!in_array($row['stockSymbol'], $stackTickers)){
                array_push($stackTickers, $row['stockSymbol']);
            }

        }
    }

    $SQLresultObj = array('Stocks' => $stackTickers);

    //var_dump($SQLresultObj);
    return($SQLresultObj);

}
function portfolioDB($data){
try{
    $username_input = $data["Username"];
  
    //purpose is to display portfolio
    global $mysql_server;//reference global mysql_server
  
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
  

    $qry = "Select * from portfolio where username='$username_input'";
    $result = $mysqli->query($qry);
  
    $stackSymbol = array();
    $stackPrices = array();
    $stackQty = array();
  
    if($result->num_rows > 0){ //if there is something in the DB then...
        while ($row = $result->fetch_assoc()){
          
            array_push($stackSymbol, $row['stockSymbol']);
            array_push($stackPrices, $row['price']);
            array_push($stackQty, $row['qty']);
        }
  
  
    }//just in case there should probably be a condition for when the DB is empty.
  
    $returnObj = array('Stocks' => $stackSymbol, 'Prices' => $stackPrices, 'Quantity' => $stackQty);
    var_dump($returnObj);
    return (json_encode($returnObj));
  
    $mysqli.close();
    }catch (Exception $e){
        echo "Error in portfolioDB Function infoFunctions.php::::: \n" . $e->getMessage();
    }

  
}/********************************************************
Add a new entry into the Database with following Info:  *
Username, Stock Ticker, Quantity, Purchased Price       *
********************************************************/
function addToPortfolioDB($username_input,$purchaseCost,$qty,$symbol)
{
try{  
    global $mysql_server;//reference global mysql_server
  
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
  
  
    $qry = "Insert into portfolio (username, stockSymbol, qty, price) values('$username_input','$symbol','$qty','$purchaseCost')";
    $result = $mysqli->query($qry);
  
//let me turn it on for you
//thats why its not connecting
    echo "Added into DB";
   //$mysqli.close();
   }catch (Exception $e){
        echo "Error in addToPortfolioDB Function infoFunctions.php::::: \n" . $e->getMessage();
    }
  
}
/************************************************************
Sell Stocks in your portfolio                               *
So obtain the total value of the stock order you are selling*
Then delete the info inside the portfolio DB,               *
Then add the stored total value into Bank Account Balance   *
************************************************************/
function sellStock($data){
try{
    //var_dump($data);
    //retrieve data and set variables accordingly
    $sym = $data["Symbol"];
    $username = $data["Username"];
    $qtyRequested = $data["Quantity"];
  
    //grab the information needed
    $jsonObj = getInfo($sym);
    //decode the structure
    $newObj = json_decode($jsonObj);
    //Single out the Closing price aka Current Price
    $currentCost = $newObj->Close[0];
    //var_dump($newObj->Close[0]); //display the closing price

    //check how many of the stock you own
    $qtyYouOwn = getStockQuantity($username,$sym);
  
    //Make sure you have more or equal qty in portfolio compared to what you wanna sell
    if($qtyYouOwn >= $qtyRequested)
    {
        //Calculate the total value of your sell.
        $totalValue = $currentCost * $qtyRequested;
    }
    else //Placeholder for now, should probably not do this.
    {
        //Sell only the amount that you have in portfolio
        $totalValue = $currentCost * $qtyYouOwn;
    }
  
  
    //Call addtoAccountBalance and add $totalValue to the account balance
    $currentBalance = addtoAccountBalance($username,$totalValue,$sym);
  
    //Call deleteFromPortfolioDB and delete the previous entry
    deleteFromPortfolioDB($username, $qtyRequested, $sym);

    $returnString = "Sell Order Confirmed!";
  
    return(json_encode($returnString));
    
    }catch (Exception $e){
        echo "Error in sellStock Function infoFunctions.php::::: \n" . $e->getMessage();
    }
}
/***************************************************
Get the TOTAL Quantity of a Stock in your portfolio*
***************************************************/
function getStockQuantity($username, $symbol)
{
try{
        global $mysql_server;//reference global mysql_server
    
        $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
    
    
        $qry = "SELECT * FROM portfolio WHERE username='$username' and stockSymbol='$symbol'";
        $result = $mysqli->query($qry);
    
        $totalQty = 0;
    
    
        if($result->num_rows > 0){
            while ($row = $result->fetch_assoc()){
            
                $totalQty += $row['qty'];
            }
        }
        return $totalQty;
    }catch (Exception $e){
            echo "Error in getStockQuantity Function infoFunctions.php::::: \n" . $e->getMessage();
        }
}
/****************************************************************************************
Delete a entry from the Database.                                                       *
Find the TOTAL quantity of requested ticker in your portfolio                           *
Subtract from the number you want to sell and compare to each record                    *
Delete from each records QTY, if QTY is now 0, delete the record                        *
IF a record QTY is not set to 0 after subtracting, subtract difference and update QTY   *
****************************************************************************************/
function deleteFromPortfolioDB($username_input,$qtyRequested,$symbol)
{
try{
    global $mysql_server;//reference global mysql_server
  
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");

    $CompareQry = "SELECT * FROM portfolio WHERE username='$username_input' and stockSymbol='$symbol'";
    $CompareResult = $mysqli->query($CompareQry);
  
    if($CompareResult->num_rows > 0){
        while ($row = $CompareResult->fetch_assoc())
            {
                //store the row Index Number temporarily
                $tempIndexNo = $row['indexNo'];
            
                if($qtyRequested >= $row['qty'])
                {
                
                    //subtract the qty in this row from the total you are trying to sell
                    $qtyRequested -= $row['qty'];
                
                
                    //Delete this Record
                    $deleteQry = "DELETE FROM portfolio where indexNo='$tempIndexNo'";
                    $deleteResult = $mysqli->query($deleteQry);
                }
                elseif($qtyRequested < $row['qty'])
                {
                    //Store value of this Records qty by remaning qty asked for
                    $tempQtyAmt = $row['qty'] - $qtyRequested;
                
                    //Update this Record with its Qty MINUS qtyRequested
                    $updateQry = "UPDATE portfolio SET qty='$tempQtyAmt' WHERE indexNo='$tempIndexNo'";
                    $updateResult = $mysqli->query($updateQry);
                }
          
            }
    }
}catch (Exception $e){
        echo "Error in deleteFromPortfolioDB Function infoFunctions.php::::: \n" . $e->getMessage();
    }
}
/********************************************************************************************
Add the total quantity of requested Ticker in the account                                   *
Compare that total to the account amount asked to sell, confirm its possible                *
Store the sellingQuantity, remove the sellingQuantity from portfolio.                       *
Multiply the sellingQuantity by CurrentCost and store Value in SellTotalValue.              *
Delete the entries from Portfolio as needed, update values in entries if needed as well.    *
Update Bank table's Balance column with the pre-existing value PLUS SellTotalValue.         *
********************************************************************************************/
function addToAccountBalance($username,$soldValue,$stockTicker)
{
try{
    global $mysql_server;//reference global mysql_server
  
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");
  
    $findBalQry = "SELECT * FROM bank WHERE username='$username'";
    $findBalResult = $mysqli->query($findBalQry);
  
    $oldBalance = 0;
  
    if($findBalResult->num_rows > 0){
        while ($row = $findBalResult->fetch_assoc()){
          
        $oldBalance = $row['balance'];
        }
    }
    $newBalance = $oldBalance + $soldValue;
  
    $UpdateQry = "UPDATE bank SET balance='$newBalance' where username='$username'";
    $result = $mysqli->query($UpdateQry);
  
    //Print Values for Tests
    //var_dump($oldBalance);
    //var_dump($newBalance);
} catch (Exception $e){
        echo "Error in addToAccountBalance Function infoFunctions.php::::: \n" . $e->getMessage();
    }
  
}
 
/*****************************
Returns company info & prices*
*****************************/
function showBasicInfo($data){
try{
    $sym = $data["Symbol"];

    $symbol = getInfo($sym);

    return $symbol;
    } catch (Exception $e){
        echo "Error in showBasicInfo Function infoFunctions.php::::: \n" . $e->getMessage();
    }
}
/************************************
Returns the last 7 days Price Info  *
By Default, we grab the last 30 days*
************************************/
function showLast7($data){
try{
    $sym = $data["Symbol"];
    $symbol = getInfo($sym);
    $newObj = json_decode($symbol);

    //How many days are we returning?
    $days = 7;

    $Close = array();

    //Populate a new object with the necesary information
    for ($i = 0; $i < 7;$i++)
    {
            array_push($Close, $newObj->Close[$i]);
    }
    
    return json_encode($Close);
} catch (Exception $e){
    echo "Error in showLast7 Function infoFunctions.php::::: \n" . $e->getMessage();
    //send("Error in showLast7 Function infoFunctions.php::::: \n" . $e->getMessage());
    }
    
}
/***********************************
Returns the last 30 Days Price info  *
By Default we grab the last 30 Days*
***********************************/
function showLast30($data){
try{
    $sym = $data["Symbol"];
    $symbol = getInfo($sym);
    $newObj = json_decode($symbol);

    //How many days are we returning?
    $days = 30;

    $Close = array();

    //Populate a new object with the necesary information
    for ($i = 0; $i < $days;$i++)
    {
            array_push($Close, $newObj->Close[$i]);
    }
    
    return json_encode($Close);
} catch (Exception $e){
    echo "Error in showLast30 Function infoFunctions.php::::: \n " . $e->getMessage();
    }
    
}


function getPortfolioStockTickers($username)
{
try{
    //make connection to SQL server
    global $mysql_server;//reference global mysql_server

    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");

    //make empty array to hold the Stock Tickers attached to Username
    $stackTickers = array();

    //find the items that are attached to username and requested Ticker
    $TickerQry = "Select * from portfolio where username='$username'";
    $TickerResult = $mysqli->query($TickerQry);

    if($TickerResult->num_rows > 0){
        while ($row = $TickerResult->fetch_assoc()){

            if(!in_array($row['stockSymbol'], $stackTickers)){ //If repeat found, returns key. 
                array_push($stackTickers, $row['stockSymbol']);
                //var_dump($stackTickers);
            }
            

        }
    }

    $SQLresultObj = array('Stocks' => $stackTickers);

    //var_dump($SQLresultObj);
    return($SQLresultObj);
    }catch (Exception $e){
        echo "Error in getPortfolioStockTickers Function infoFunctions.php::::: \n" . $e->getMessage();
    }

}
function displayDetailedPortfolio($data)
{
try{
    $username = $data["Username"];
    //Get a list of tickers related to the account name
    $stockList = getPortfolioStockTickers($username);

    //Array that contains Current Price for Stocks
    $currentPriceStack = array();

    //get information about DISTINCT tickers only
    foreach($stockList["Stocks"] as $symbol)
    {
        $tempVar = getInfo($symbol);
        $tempObj = json_decode($tempVar);
        array_push($currentPriceStack, $tempObj->Close[0]); //get the current prices for Tickers
    }

    $stockCur = array("Stocks" => $stockList["Stocks"], "CurrentPrice" => $currentPriceStack);

    //var_dump($stockList["Stocks"]);
    //make connection to MYSQL Database
    global $mysql_server;//reference global mysql_server
    $mysqli = new mysqli($mysql_server, "badgers", "honey", "user_info");

    //find the items that are attached to username and requested Ticker
    $qry = "Select * from portfolio where username='$username'";
    $result = $mysqli->query($qry);

    //Arrays that contain information about your Portfolio Records
    $stackSymbol = array();
    $stackPrices = array();
    $stackQty = array();

    //when the list is NOT empty
    if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()){
            //Extract Records Information
            array_push($stackSymbol, $row['stockSymbol']);
            array_push($stackPrices, $row['price']);
            array_push($stackQty, $row['qty']);
        }
    }
    //Arrays that contain DETAILED information about your portfolio
    $stackPriceChange = array();
    $stackCurrentPrice = array();
    $stackPercentChange = array();
    $stackNetGain = array();


    //for each record in your portfolio
    foreach($stackSymbol as $key => $symbol)
    {
        //for each Ticker excluding duplicates in your portfolio
        foreach($stockCur["Stocks"] as $compareKey => $compareSymbol)
        {
            //if ticker in records matches a ticker in currentPrice data list
            if ($symbol == $compareSymbol)
            {
                //if the purchase cost is greater than the current price, you LOST money
                if($stackPrices[$key] > $currentPriceStack[$compareKey])
                {
                    //calculate the price change
                    $priceChg = $stackPrices[$key] - $currentPriceStack[$compareKey];
                    $priceChg = -1 * abs($priceChg);
                    array_push($stackCurrentPrice, $currentPriceStack[$compareKey]); //Keep track of current price
                    array_push($stackPriceChange, $priceChg); //keep track of price change
                
                    //calculate the percent change
                    $percentChg = (($stackPrices[$key] - $currentPriceStack[$compareKey]) / $stackPrices[$key]) * 100;
                    $percentChg = -1 * abs($percentChg);
                    array_push($stackPercentChange, $percentChg); //keep track of the percent change
                
                    //calculate the Net Loss
                    $diff = $currentPriceStack[$compareKey] - $stackPrices[$key];
                    $netGain = $diff * $stackQty[$key];
                    array_push($stackNetGain, $netGain); //keep track of the net gain
                }
            
                //if the purchase cost is less than the current price, you GAINED money
                elseif($stackPrices[$key] <= $currentPriceStack[$compareKey])
                {
                    //calculate the price change
                    $priceChg = $currentPriceStack[$compareKey] - $stackPrices[$key];
                    array_push($stackCurrentPrice, $currentPriceStack[$compareKey]); //Keep track of current price
                    array_push($stackPriceChange, $priceChg); //keep track of price change
                
                    //calculate the percent change
                    $percentChg = (($currentPriceStack[$compareKey] - $stackPrices[$key]) / $stackPrices[$key]) * 100;
                    array_push($stackPercentChange, $percentChg); //keep track of the percent change
                
                    //calculate the Net Gained
                    $diff = $currentPriceStack[$compareKey] - $stackPrices[$key];
                    $netGain = $diff * $stackQty[$key];
                    array_push($stackNetGain, $netGain); //keep track of the net gain
                }
            }
        
        }
    
    
    
    }
    $SQLresultObj = array('Stocks' => $stackSymbol, 'PurchasePrice' => $stackPrices, 'PriceChange' => $stackPriceChange, 'CurrentPrice' => $stackCurrentPrice,
                        'PercentChange' => $stackPercentChange, 'NetGain' => $stackNetGain, 'Quantity' => $stackQty);
    //var_dump($SQLresultObj);

    return json_encode($SQLresultObj);
    }catch (Exception $e){
        echo "Error in displayDetailedPortfolio Function infoFunctions.php::::: \n" . $e->getMessage();
    }
   
}

?>