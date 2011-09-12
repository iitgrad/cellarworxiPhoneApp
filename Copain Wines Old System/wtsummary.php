<?php

session_start();

?>

<html>



<head>

  <title></title>

<link rel="stylesheet" type="text/css" href="../site.css">

</head>



<body>

<?php



include ("startdb.php");



$startofyear=$_SESSION['vintage'].'-01-01';

if ($_GET['CLIENTCODE']=='')

{

$query = "SELECT

  `clients`.`CLIENTNAME`,

  `wt`.`TAGID`,

  `wt`.`VARIETY`,

  wt.CLONE,

  `wt`.`VINEYARD`,

  `wt`.`APPELLATION`,

  `wt`.`LOT`,

  locations.NAME AS VYD,

  locations.APPELLATION as APPL,

  locations.REGION as RC,

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

 left outer join locations on (locations.ID=wt.VINEYARDID)
 
 INNER JOIN `clients` ON (`wt`.`CLIENTCODE` = `clients`.`clientid`)

WHERE

  (".' unix_timestamp(wt.DATETIME)>=unix_timestamp("'.$_SESSION['vintage'].'-01-01") AND

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

}

else

{

$query = "SELECT

  `clients`.`CLIENTNAME`,

  `wt`.`TAGID`,

  `wt`.`VARIETY`,

  wt.CLONE,

  `wt`.`VINEYARD`,

  `wt`.`APPELLATION`,

  `wt`.`LOT`,

  locations.NAME AS VYD,

  locations.APPELLATION as APPL,

  locations.REGION as RC,

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

  left outer join locations on (locations.ID=wt.VINEYARDID)

  INNER JOIN `clients` ON (`wt`.`CLIENTCODE` = `clients`.`clientid`)

WHERE

  (".' unix_timestamp(wt.DATETIME)>=unix_timestamp("'.$_SESSION['vintage'].'-01-01") AND

    wt.CLIENTCODE="'.$_GET['CLIENTCODE'].'" AND

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

}



// echo $query;

$result = mysql_query($query);



$num_results = mysql_num_rows($result);

$totalweight = 0;

?>

<table id="demo_table" border="0" align="center">

  <tr>

    <td width="7%" align="center"><b>WEIGH<br>

    TAG</b></td>

    <td align=center width="7%"><b>DATE</b></td>

    <td width="11%" align="center"><b>LOT#</b></td>

    <td width="16%"><b>VINEYARD</b></td>

    <td width="16%"><b>APPELLATION</b></td>

    <td width="16%"><b>VARIETAL</b></td>

    <td width="9%" align="center"><b>REGION<br>

    CODE</b></td>

    <td width="7%" align="center"><b>BIN<br>

    COUNT</b></td>

    <td width="6%" align="center"><b>TONS</b></td>

    <td width="6%" align="center"><b>TONS TO<br>DATE</b></td>

  </tr>

<tr><td colspan=11><hr></td></tr>

<?php

for ($i=0; $i <$num_results; $i++)

{

	$row = mysql_fetch_array($result);

	$wtid = $row['TAGID']+5000;

	$thelink = 'wtpage.php?wtid='.$wtid;

	$daynum=date("d",$row['THEDATE']);

	if ($daynum!=$prevdaynum)

	{

		echo '<tr><td align=right colspan=8>DAY TOTAL: </td><td align=right><b>'.number_format($daytotal,2).'</td><td align=right><b>'.number_format($totalweight,2).'</b></td></tr>';

		echo '<tr><td colspan=10><hr></td></tr>';

		$daytotal=0;

	}

	echo ' <tr><td width="6%" align="center"><a href='.$thelink.'>'.$wtid.'</a></td>';

	echo ' <td align=center>'.date("m/d/Y",$row['THEDATE']).'</td>';

	echo ' <td align="center"><a href=showlotinfo.php?lot='.$row['LOT'].'>'.$row['LOT'].'</a></td>';

	echo ' <td >'.$row['VYD'].'</td>';

	echo ' <td >'.$row['APPL'].'</td>';

	echo ' <td >'.$row['VARIETY'].'</td>';

	echo ' <td align="center">'.$row['RC'].'</td>';

	$theweight = ($row['SUMWEIGHT'] - $row['SUMTARE'])/2000;

	$totalweight = $totalweight + $theweight;

	$daytotal+=$theweight;

	echo ' <td align="center">'.$row['SUMBINCOUNT'].'</td>';

	

	if ($_GET['CLIENTCODE']!=1 & $_GET['CLIENTCODE']!=26)

	{

	echo ' <td align="right">'.number_format($theweight,2).'</td></tr>';

	}

	else

	{

	echo ' <td align="right">'.number_format($theweight,2).'('.number_format($theweight*185.87,0).')'.'</td></tr>';

	}

	$prevdaynum=$daynum;

}

echo '<tr><td align=right colspan=8>DAY TOTAL: </td><td align=right><b>'.number_format($daytotal,2).'</td><td align=right><b>'.number_format($totalweight,2).'</b></td></tr>';

echo '<tr><td colspan=10><hr></td></tr>';

$daytotal=0;



echo '</table> ';

?>



</body>



</html>