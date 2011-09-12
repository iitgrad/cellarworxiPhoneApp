<?php

include ("./jpgraph/jpgraph.php");

include ("./jpgraph/jpgraph_line.php");

session_start();

?>

<html>

<head>

  <title>Client Vintage Summary</title>

<link rel="stylesheet" type="text/css" href="../site.css">

	<script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>

</head>

<body>

<?php

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("lotinforecords.php");



if (isset($_GET['clientcode']))

{

	$lclientcode=$_GET['clientcode'];

	$queryclients='SELECT * from clients WHERE clients.CODE="'.$lclientcode.'" ORDER BY CODE';

}

else

{

	$queryclients="SELECT * from clients ORDER BY clientname";

}

if (isset($_GET['vintage']))

{

	$lvintage=$_GET['vintage'];

	$queryvintage='SELECT DISTINCT lots.YEAR from lots WHERE lots.YEAR="'.$lvintage.'" ORDER BY lots.YEAR, lots.LOTNUMBER';

}

else

{

	$lvintage=$_SESSION['vintage'];

	$queryvintage='SELECT DISTINCT lots.YEAR from lots ORDER BY lots.YEAR, lots.LOTNUMBER';

}

$query='SELECT 

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

//echo $query;

$result=mysql_query($query);



echo '<table id=lots_table align=center width=50%>';

echo '<tr>';

echo '<td align=center>DATE</td>';

echo '<td align=center>WO</td>';

echo '<td align=center>LABTEST</td>';

echo '<td align=center>LAB RESULT</td>';

echo '</tr>';

for ($i=0;$i<mysql_num_rows($result);$i++)

{

	$row=mysql_fetch_array($result);

	echo '<tr>';

	echo '<td align=center>'.date("m/d/Y",$row['THEDATE']).'</tr>';

	echo '<td align=center><a href=wopage.php?action=view&woid='.$row['WOID'].'>'.$row['WOID'].'</a></tr>';

	echo '<td align=center>'.$_GET['labtest'].'</tr>';

	echo '<td align=center>'.$row['VALUE1'].'</tr>';



	echo '</tr>';

      echo '<tr></tr>';

}

echo '</table>';

echo '<table align=center>';



echo '<tr><td align=center><img src=graphlabresult.php?lot='.$_GET['lot'].'&labtest='.$_GET['labtest'].'></tr>';

echo '</table>';

?>



			<script language="JavaScript">

			<!--

			tigra_tables('lots_table', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

			// -->

			</script>

</body>

</html>

