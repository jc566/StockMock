<?php

$Button = $_POST["Button"];

if ($Button == 'Display')
{
header('Location: showfile.php');
}

else if ($Button == 'Buy')
{
header('Location: confirm_buy.php');
}

else if ($Button == 'Sell')
{
header('Location: sold.php');
}





?>