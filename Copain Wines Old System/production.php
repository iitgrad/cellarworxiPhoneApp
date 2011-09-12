<html>



<head>

<meta http-equiv="Content-Language" content="en-us">

<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

<meta name="GENERATOR" content="Microsoft FrontPage 5.0">

<meta name="ProgId" content="FrontPage.Editor.Document">

<link rel="stylesheet" type="text/css" href="../site.css">

</head>



<body>



<p align="center"><font face="Franklin Gothic Book">



<?php



  include ("startdb.php");

  include ("displayqueryresult.php");

//  include ("queryupdatefunctions.php");



  function test($val)

  {

    echo "hello...";

  }

//  $clients = getclientarray();

//  array_walk($clients,$test);



  $query='SELECT DISTINCT

  `clients`.`CLIENTNAME`,

  SUM(`bindetail`.`WEIGHT`) AS `totweight`,

  SUM(`bindetail`.`TARE`) AS `tottare`,

  LCASE(`clients`.`CODE`)AS `ccode`

FROM

  `bindetail`

  INNER JOIN `wt` ON (`bindetail`.`WEIGHTAG` = `wt`.`ID`)

  INNER JOIN `clients` ON (`wt`.`CLIENTCODE` = `clients`.`clientid`)

GROUP BY

  `clients`.`CLIENTNAME`,

  `clients`.`CODE`';



  $result= mysql_query($query);

  $num_results = mysql_num_rows($result);

  echo '<table border="1" cellpadding="3">';

  $ccc_total=0;

  for ($i=0; $i <$num_results; $i++)

  {

     $row = mysql_fetch_array($result);

     $tottons =  ($row['totweight']-$row['tottare'])/2000;

     $ccc_total = $ccc_total+$tottons;

     echo '<tr>';

     echo '<td>'.$row['CLIENTNAME'].'</td>';

     echo '<td><a href=prod2.php?ccode='.$row['ccode'].'>PRODUCTION</a></td>';

     echo '<td align="right">'.number_format($tottons,2).'</td>';

     echo '</tr>';

  }

  echo '<tr><td colspan="5" align="right"><b>'.number_format($ccc_total,2).'</b></td></tr>';

  echo '</table>';



?>



</font></p>



</body>



</html>

