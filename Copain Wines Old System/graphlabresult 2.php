<?php

include ("./jpgraph/jpgraph.php");

include ("./jpgraph/jpgraph_line.php");

include ("./jpgraph/jpgraph_bar.php");

   include ("startdb.php");



$query = 'SELECT 

  `labresults`.`LABTESTID`,

  `labresults`.`VALUE1`,

  unix_timestamp(`wo`.`DUEDATE`) as THEDATE,

  `labtest`.`WOID`

FROM

  `labresults`

  INNER JOIN `labtest` ON (`labresults`.`LABTESTID` = `labtest`.`ID`)

  INNER JOIN `wo` ON (`labtest`.`WOID` = `wo`.`ID`)

WHERE

  (`labresults`.`LABTEST` = "'.$_GET['labtest'].'") AND 

  (`wo`.`LOT` = "'.$_GET['lot'].'")

ORDER BY

  `wo`.`DUEDATE`';



 // echo $query;

//exit;



   $result = mysql_query($query);

   $num_results = mysql_num_rows($result);

//  for ($i=0; $i <$num_results; $i++)

  for ($i=0; $i <mysql_num_rows($result); $i++)

  {

   $row=mysql_fetch_array($result);

   $value[]=$row['VALUE1'];

  }



   $ydata = $value;



// Create the graph. These two calls are always required

$graph = new Graph(600,300,"auto");



//$graph->img->SetImgFormat("jpeq");



$graph->SetScale("textlin");

$graph->SetShadow();

$graph->SetMarginColor('white');



// Create the linear plot

$lineplot=new BarPlot($ydata);

$lineplot->SetFillColor('blue');

$lineplot->SetShadow();

// Add the plot to the graph

$graph->Add($lineplot);



$graphtitle = $_GET['labtest'] ;

$graph->tabtitle->Set($graphtitle);



$graph->xaxis->title->Set("TEST ITERATION");

$graph->yaxis->title->Set("RESULT");



$graph->title->SetFont(FF_FONT1,FS_BOLD);

$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);



$lineplot->SetColor("navy");

$lineplot->SetWeight(1);





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