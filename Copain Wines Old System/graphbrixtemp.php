<?php

include ("./jpgraph/jpgraph.php");

include ("./jpgraph/jpgraph_line.php");



   include ("startdb.php");



$query = 'SELECT DISTINCT

  `brixtemp`.`lot`,

  `brixtemp`.`vessel`,

  `brixtemp`.`vesseltype`,

  `brixtemp`.`BRIX`,

  `brixtemp`.`temp`,

  `brixtemp`.`DATE`

FROM

  `fermprot`

  INNER JOIN `brixtemp` ON (`fermprot`.`LOT` = `brixtemp`.`lot`)

WHERE

  (`brixtemp`.`lot` = "'.$_GET['lot'].'" AND

   `brixtemp`.`vessel` = "'.$_GET['vessel'].'" AND

   `brixtemp`.`vesseltype` = "'.$_GET['vesseltype'].'")

ORDER BY

  `brixtemp`.`DATE`';



//  echo $query;





   $result = mysql_query($query);

   $num_results = mysql_num_rows($result);

//  for ($i=0; $i <$num_results; $i++)

  for ($i=0; $i <15; $i++)

  {

   $row=mysql_fetch_array($result);

   $brix[]=$row['BRIX'];

   $temp[]=$row['temp'];

//   echo $row['BRIX'].'<br>';

//   echo $row['temp'].'<br>';

  }



   $ydata = $brix;

   $y2data = $temp;



// Create the graph. These two calls are always required

$graph = new Graph(600,400,"auto");



//$graph->img->SetImgFormat("jpeq");



$graph->SetScale("textlin",-5,30);

$graph->SetY2Scale("lin",40,100);

$graph->SetShadow();

$graph->SetMarginColor('white');



// Create the linear plot

$lineplot=new LinePlot($ydata);

$lineplot2=new LinePlot($y2data);



// Add the plot to the graph

$graph->Add($lineplot);

$graph->AddY2($lineplot2);

$lineplot2->SetColor("red");

$lineplot2->SetWeight(1);

$graph->y2axis->SetColor("blue");



$graphtitle = $_GET['lot'] ."  ". $_GET['vesseltype'] . "  " . $_GET['vessel'];

$graph->tabtitle->Set($graphtitle);



$graph->xaxis->title->Set("Days Count");

$graph->yaxis->title->Set("Brix");

$graph->y2axis->title->Set("Temperature");



$graph->title->SetFont(FF_FONT1,FS_BOLD);

$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->y2axis->title->SetFont(FF_FONT1,FS_BOLD);



$lineplot->SetColor("navy");

$lineplot->SetWeight(1);



$lineplot2->SetColor("red");

$lineplot2->SetWeight(1);



$graph->yaxis->SetColor("blue");



// Display the graph

  $display=' <table width="100%">

      <tr>

        <td align="center">

           a test

        </td>

        <td align="center">

           a test

        </td>

   </table>

   <br>     ';

//   echo $display;

$graph->Stroke();

?>