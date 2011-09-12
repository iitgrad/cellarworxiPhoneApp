<?php

    session_start();

?>

<html>

<head>

  <title>Client Production Results</title>

<link rel="stylesheet" type="text/css" href="../site.css">

	<script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>

</head>

<body>

<?php

 include ("startdb.php");

$query="SELECT `clients`.`CLIENTNAME`, `clients`.`CODE`

    FROM `clients` WHERE (`clients`.`CODE` = '".$_SESSION['clientcode']."')";

//echo $query;



   $result = mysql_query($query);

   $row = mysql_fetch_array($result);

   $fullwineryname = $row['CLIENTNAME'];



// echo '<p align="center"><u><b><font face="Franklin Gothic Book">2003 '.$fullwineryname.' PRODUCTION</font></b></u></p>';



?>

<table id="demo_table" border="0" align="center">

  <tr>

    <td width="7%" align="center"><b>WEIGH<br>

    TAG</b></td>

    <td align=center width="7%"><b>LOT</b></td>

    <td width="11%" align="center"><b>LOT#</b></td>

    <td width="16%"><b>VINEYARD</b></td>

    <td width="16%"><b>APPELLATION</b></td>

    <td width="16%"><b>VARIETAL</b></td>

    <td width="5%"><b>CLONE</b></td>

    <td width="9%" align="center"><b>REGION<br>

    CODE</b></td>

    <td width="7%" align="center"><b>BIN<br>

    COUNT</b></td>

    <td width="6%" align="center"><b>TONS</b></td>

  </tr>

<tr><td colspan=10><hr></td></tr>

<?php

  $theclientcode = $_SESSION['clientcode'];



  $query = "SELECT

  `clients`.`CLIENTNAME`,

  `wt`.`TAGID`,

  `wt`.`VARIETY`,

  wt.CLONE,

  `wt`.`VINEYARD`,

  `wt`.`APPELLATION`,

  `wt`.`LOT`,

  unix_timestamp(`wt`.`DATETIME`) AS THEDATE,

  `wt`.`REGIONCODE`,

  SUM(`bindetail`.`WEIGHT`) AS `SUMWEIGHT`,

  SUM(`bindetail`.`TARE`) AS `SUMTARE`,

  SUM(`bindetail`.`BINCOUNT`) AS `SUMBINCOUNT`,

  `clients`.`clientid`,

  `wt`.`ID`

FROM

  `bindetail`

  RIGHT OUTER JOIN `wt` ON (`bindetail`.`WEIGHTAG` = `wt`.`ID`)

  INNER JOIN `clients` ON (`wt`.`CLIENTCODE` = `clients`.`clientid`)

WHERE

  (`clients`.`CODE` = '".$theclientcode."'".' AND

    unix_timestamp(wt.DATETIME)>=unix_timestamp("'.$_SESSION['vintage'].'-01-01") AND

    unix_timestamp(wt.DATETIME)<=unix_timestamp("'.$_SESSION['vintage'].'-12-31")'.")

GROUP BY

  `clients`.`CLIENTNAME`,

  `wt`.`TAGID`,

  `wt`.`VARIETY`,

  `wt`.`VINEYARD`,

  `wt`.`APPELLATION`,

  `wt`.`LOT`,

  `wt`.`DATETIME`,

  `wt`.`REGIONCODE`,

  `clients`.`clientid`,

  `wt`.`ID`

ORDER BY

  `wt`.`DATETIME`,

  `wt`.`LOT`,

  `wt`.`TAGID`";

 // echo $query;

  $result = mysql_query($query);



  $num_results = mysql_num_rows($result);

  $totalweight = 0;



  for ($i=0; $i <$num_results; $i++)

  {

    $row = mysql_fetch_array($result);

    $wtid = $row['TAGID']+5000;

    $thelink = 'wtpage.php?wtid='.$wtid;

    echo ' <tr><td width="7%" align="center"><a href='.$thelink.'>'.$wtid.'</a></td>';

    echo ' <td align=center>'.date("m/d/Y",$row['THEDATE']).'</td>';

    echo ' <td align="center"><a href=showlotinfo.php?lot='.$row['LOT'].'>'.$row['LOT'].'</a></td>';

    echo ' <td >'.$row['VINEYARD'].'</td>';

    echo ' <td >'.$row['APPELLATION'].'</td>';

    echo ' <td >'.$row['VARIETY'].'</td>';

    echo ' <td >'.$row['CLONE'].'</td>';

    echo ' <td align="center">'.$row['REGIONCODE'].'</td>';

    $theweight = ($row['SUMWEIGHT'] - $row['SUMTARE'])/2000;

    $totalweight = $totalweight + $theweight;

    echo ' <td align="center">'.$row['SUMBINCOUNT'].'</td>';



    echo ' <td align="center">'.number_format($theweight,2).'</td></tr>';

   }

    echo ' <tr><td colspan=8></td>';

    echo ' <td align="right">TOTAL:</td>';

    echo ' <td align="center">'.number_format($totalweight,2).'</td></tr>';



    mysql_close($db);

?>

</table>

			<script language="JavaScript">

			<!--

				tigra_tables('demo_table', 2, 1, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

			// -->

			</script>

</body>

</html>

