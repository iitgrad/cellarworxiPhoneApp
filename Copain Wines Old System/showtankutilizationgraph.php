<?php

session_start();

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");

include ("totalgallons.php");

include ("./jpgraph/jpgraph.php");

include ("./jpgraph/jpgraph_line.php");



$graph = new Graph(700,500,"auto");



$tonnage=$_SESSION['thetonnage'];

$tonnage2=$_SESSION['thetonnage2'];

$cap=$_SESSION['thecap'];



foreach ($tonnage as $key=>$value)

{

	if ($value>$peak) $peak=$value;

}



$graph->SetScale("textlin",0,($peak*1.1));

$graph->SetShadow();

$graph->SetMarginColor('white');



// Create the linear plot

$lineplot=new LinePlot($tonnage);

//$lineplot2=new LinePlot($tonnage2);

$lineplot3=new LinePlot($cap);



// Add the plot to the graph

$graph->Add($lineplot);

//$graph->Add($lineplot2);

$graph->Add($lineplot3);



$graphtitle = "TANK UTILIZATION";

$graph->tabtitle->Set($graphtitle);



$graph->xaxis->title->Set("Days Count");

$graph->yaxis->title->Set("Tons");



$graph->title->SetFont(FF_FONT1,FS_BOLD);

$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);



$lineplot->SetColor("navy");

$lineplot->SetWeight(1);



$graph->yaxis->SetColor("blue");



$graph->Stroke();

?>



</body>



</html>

