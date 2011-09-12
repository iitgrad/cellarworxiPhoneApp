<?php

session_start();

?>

<html>



<head>

  <title></title>

<link rel="stylesheet" type="text/css" href="../site.css">

     <script type="text/javascript" src="popup/overlibmws.js"></script>

   <script type="text/javascript" src="popup/overlibmws_bubble.js"></script>

</head>



<body>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div> 



<?php



include ("startdb.php");

include ("queryupdatefunctions.php");

include ("lotinforecords.php");



setdefaults();



function sameday($date1,$date2)

{

	if (date("m-d-Y",$date1)==date("m-d-Y",$date2))

	return 1;

	else

	return 0;

}


$query='SELECT `wo`.`LOT`, `reservation`.`ASSETID`, `wo`.`ENDDATE`, assets.NAME FROM `wo` INNER JOIN `reservation` ON (`wo`.`ID` = `reservation`.`WOID`) INNER JOIN assets ON (reservation.ASSETID=assets.ID) WHERE (YEAR(`wo`.`ENDDATE`) = "2007") and ((assets.TYPEID=6) or (assets.TYPEID=8) and wo.TYPE="SCP") order by assets.NAME';

/*$query='SELECT

  `wo`.`LOT`,

  `reservation`.`ASSETID`,

  `wo`.`ENDDATE`,

  assets.NAME

FROM

  `scp`

  INNER JOIN `wo` ON (`wo`.`ID` = `scp`.`WOID`)

  INNER JOIN `reservation` ON (`wo`.`ID` = `reservation`.`WOID`)

  INNER JOIN assets ON (reservation.ASSETID=assets.ID)

WHERE

  (YEAR(`wo`.`ENDDATE`) = "'.date("Y",time()).'") and ((assets.TYPEID=6) or (assets.TYPEID=8)) order by assets.NAME';
*/
//echo $query;

//exit;

$result=mysql_query($query);



echo ' <font face="Franklin Gothic Book" size="2">';

echo '<table border=1 width="70%" align="center">';

echo '<tr>';

echo    '<td align="center">';

echo        "<b>"." "."</b>";

echo    '</td>';

echo    '<td align="center">';

echo        "<b>".'LOT'."</b>";

echo    '</td>';

echo    '<td align="center">';

echo        "<b>".'VESSEL'."</b>";

echo    '</td>';

echo    '<td align="center">';

echo        "<b>".'HAS FERM<br>PROTOCOL'."</b>";

echo    '</td>';

echo    '<td align="center">';

echo        "<b>".'DATE'."</b>";

echo    '</td>';

echo    '<td align="center">';

echo        "<b>".'BRIX'."</b>";

echo    '</td>';

echo    '<td align="center">';

echo        "<b>".'TEMP'."</b>";

echo    '</td>';

echo '</tr>';

for ($i=0; $i <mysql_num_rows($result); $i++)

{

	$row=mysql_fetch_array($result);

	

	$vessel=explode('-',$row['NAME']);

	$queryferms='select * from fermprot where STATUS="ACTIVE" and LOT="'.$row['LOT'].'" and VESSELTYPE="'.$vessel[0].'" and VESSELID="'.$vessel[1].'"';

	//echo $queryferms.'<br>';

	$queryresult=mysql_query($queryferms);



	if (mysql_num_rows($queryresult)>0)

	  $hasfermprot=1;

	else

	  $hasfermprot=0;

	$thelot=lotinforecords($row['LOT']);

	$vol=$thelot[count($thelot)-1]['ending_tankgallons'];

	if ($vol>0)

	{

	$vname=explode('-',$row['NAME']);

	$query2 = 'SELECT DISTINCT  `brixtemp`.`id`, `brixtemp`.`lot`, `brixtemp`.`vessel`, brixtemp.DATE, `brixtemp`.`vesseltype`, `brixtemp`.`BRIX`, `brixtemp`.`temp`, `fermprot`.`CLIENTCODE`,

  DATE_FORMAT(`brixtemp`.`DATE`,'. '"'. '%m-%d-%Y' . '"'.') AS THEDATE FROM `fermprot`

             INNER JOIN `brixtemp` ON (`fermprot`.`LOT` = `brixtemp`.`lot`)

  

WHERE

  (`brixtemp`.`lot` = "'.$row['LOT'].'" AND

   `brixtemp`.`vessel` = "'.$vname[1].'" AND

   `brixtemp`.`vesseltype` = "'.$vname[0].'")

ORDER BY

  `brixtemp`.`DATE` DESC LIMIT 1';

	$result2=mysql_query($query2);

	

	$row2=mysql_fetch_array($result2);

	

	echo '<tr>';

	echo    '<td align="center">';

	//	echo        '<a href='.$thelink.'>HISTORY</a>';

	echo    '</td>';

	echo    '<td align="center">';

	echo        '<a href=showlotinfo.php?lot='.$row['LOT'].'>'.$row['LOT'].'</a>';

	echo    '</td>';

	echo    '<td align="center">';

	$vname=explode("-",$row['NAME']);

	echo        '<a href=viewfermcurves.php?allowadd=TRUE&lot='.$row['LOT'].'&vesseltype='.$vname[0].'&vessel='.$vname[1].'>'.$row['NAME'].'</a>';

	echo    '</td>';

	echo '<td align=center>';

	if ($hasfermprot==1)

	  echo "X";

	echo '</td>';

	echo    '<td align="center">';

	echo        $row2['THEDATE'];

	echo    '</td>';

	echo    '<td align="center">';

	echo        $row2['BRIX'];

	echo    '</td>';

	echo    '<td align="center">';

	echo        $row2['temp'];

	echo    '</td>';

	if (sameday(strtotime($row2['DATE']),time())==1)

	echo '<td></td>';

	else

	echo '<td>*</td>';

	echo '</tr>';

	

	}

}





echo '</table> ';

?>



</body>



</html>