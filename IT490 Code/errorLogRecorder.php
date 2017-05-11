<?php

function writeError($string)
{
    $myFile = "errorLog.txt";
    $fh = fopen($myFile, 'a') or die("can't open file");
    $stringData = "$string\n";
    fwrite($fh, $stringData);
    fclose($fh);
}

?>