<html>
<?php
require_once "/home/ashish/Desktop/rabbitmq_test/rpc_client.php";

$Symbol = $_GET["Stock"];


echo "Viewing Stock History Of: " . $Symbol;


$fibonacci_rpc4 = new FibonacciRpcClient();

    $data = array("RequestType"=>'ShowLast7', "Symbol"=>$Symbol);
    $lx = $fibonacci_rpc4->Send($data);
    //var_dump($lx);
    $countOfRows = count($lx);
    //echo "Count of rows: " . $countOfRows;
    
    $data = array();
    $countOfY = count($lx);
    for ($g = 0; $g < $countOfRows; $g++) {
        //$lx[$g];
        
        array_push($data, array("y"=>$lx[$g], "label"=> $countOfY));  
        $countOfY--;
        
        
    }
   // echo "data: : :";
   // echo var_dump($data);
   
?>

<body>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="http://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <div id="chartContainer"></div>

    <script type="text/javascript">

        $(function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                theme: "theme1",
                animationEnabled: true,
                title: {
                    text: "Stock Chart"
                },
                axisY:{
                    includeZero: false,
                    interval: 1
                },
                axisX:{
                    includeZero: false,
                    interval: 1
                },
                data: [
                {
                    type: "line",                
                    dataPoints: <?php echo json_encode($data, JSON_NUMERIC_CHECK); ?>
                }
                ]
            });
            chart.render();
        });
    </script>
</body>
 
</html>