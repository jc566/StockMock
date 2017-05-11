<?php

require 'infoFunctions.php';

checkBids();

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

?>