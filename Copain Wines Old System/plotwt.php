<?php

session_start();



include ("startdb.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");

include ("totalgallons.php");

include ("lotinforecords.php");



include ("jpgraph/jpgraph.php");

include ("jpgraph/jpgraph_bar.php");



$query='SELECT

  wt.TAGID,

  SUM(bindetail.BINCOUNT) AS TOTBINCOUNT,

  SUM(bindetail.WEIGHT) AS TOTWEIGHT,

  SUM(bindetail.TARE) AS TOTTARE,

  unix_timestamp(wt.`DATETIME`)as THEDATE

FROM

  wt

  INNER JOIN bindetail ON (wt.ID = bindetail.WEIGHTAG)

GROUP BY

  wt.TAGID,

  wt.`DATETIME`

ORDER BY

  wt.`DATETIME`';

  

  $result=mysql_query($query);

  for ($i=0;$i<mysql_num_rows($result);$i++)

  {

    $row=mysql_fetch_array($result);

    $theyear=date("Y",$row['THEDATE']);

    $val[$theyear][date("m-d",$row['THEDATE'])]+=(($row['TOTWEIGHT']-$row['TOTTARE'])/2000);

//    echo 'adding '.$theyear.'...'.(($row['TOTWEIGHT']-$row['TOTTARE'])/2000).'<br>';

  }

//  echo '<pre>';

//  print_r($val);

for ($i=0;$i<=90;$i++)

{

  $adate=mktime(0,0,0,8,15+$i,2003);

  $v[]=$val[2003][date("m-d",$adate)];

  $v2[]=$val[2004][date("m-d",$adate)];

  $v3[]=$val[2005][date("m-d",$adate)];

}



//print_r($v);

//exit;

// Create the graph. These two calls are always required

$graph = new Graph(900,700,"auto");

$graph->SetScale("textlin");



// Create the linear plot

$lineplot=new BarPlot($v);

$lineplot->SetColor("blue");



// Create the linear plot

$lineplot2=new BarPlot($v2);

$lineplot2->SetColor("red");



// Create the linear plot

$lineplot3=new BarPlot($v3);

$lineplot3->SetColor("green");



// Add the plot to the graph

$graph->Add($lineplot);

$graph->Add($lineplot2);

$graph->Add($lineplot3);



// Display the graph

$graph->Stroke();

?>

