<?php 

$x = '01:goog';
    if($x[0] == '0' and $x[1] == '1'){
        echo "success";
}

$x = trim($x,"01:");
echo $x;



?>