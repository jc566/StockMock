<?php

require 'infoFunctions.php';

checkBids();

function checkBids()
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
                if($stackAsk[$key] >= $currentPriceStack[$compareKey])
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

?>