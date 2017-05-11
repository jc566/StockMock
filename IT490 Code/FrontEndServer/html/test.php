<html>


<script type="text/javascript" src="/var/www/html/canvasjs.min.js"></script> 
<script type="text/javascript">
window.onload = function () {
    var chart = new CanvasJS.Chart("chartContainer",
    {
      title:{
        text: "Stock Trending"
    },
    axisX:{
        title: "timeline",
        gridThickness: 2
    },
    axisY: {
        title: "Price($)"
    },
    data: [
    {        
        type: "area",
        dataPoints: [//array
        { x: new Date(2017, 02, 21), y: 26},
        { x: new Date(2017, 02, 23), y: 38},
        { x: new Date(2017, 02, 25), y: 43},
        { x: new Date(2017, 02, 27), y: 29},
        { x: new Date(2017, 02, 29), y: 41},
        { x: new Date(2017, 02, 31), y: 54},
        { x: new Date(2017, 03, 02), y: 66},
        { x: new Date(2017, 03, 04), y: 60},
        { x: new Date(2017, 03, 06), y: 53},
        { x: new Date(2017, 03, 08), y: 60}

        ]
    }
    ]
});

    chart.render();
}
</script>


</html>