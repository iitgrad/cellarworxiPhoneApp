<?php

session_start();

?>

<html>



<head>

<title>Fermentation Protocol</title>

<link rel="stylesheet" type="text/css" href="../site.css">

    <script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>



<?php



include ("startdb.php");

include ("yesno.php");

include ("setcheck.php");

include ("defaultvalue.php");

include ("manageadditions.php");

include ("queryupdatefunctions.php");





?>

</head>



<body>





<?php



function pressedoff($lot, $dateofinterest)

{

	$query='SELECT wo.DUEDATE from wo WHERE wo.TYPE="PRESSOFF" and wo.LOT="'.$lot.'" and wo.DUEDATE<="'.$dateofinterest.'"';

	$result=mysql_query($query);

	if (mysql_num_rows($result)>0)

	return 1;

	else

	return 0;

}



function hasfermwo($lot, $dateofinterest)

{

	$query='SELECT wo.ID from wo WHERE (wo.TYPE="PUNCH DOWN" OR wo.TYPE="PUMP OVER") and wo.LOT="'.$lot.'" and wo.DUEDATE="'.$dateofinterest.'"';

//	echo $query;

	$result=mysql_query($query);

	if (mysql_num_rows($result)>0)

	return 1;

	else

	return 0;

}



$query='SELECT   `wo`.`LOT`,  `wo`.`TYPE`, wo.DUEDATE, `assettypes`.`NAME`,  `assets`.`NAME` AS `NAME1`

FROM

  `wo`

  INNER JOIN `lots` ON (`wo`.`LOT` = `lots`.`LOTNUMBER`)

  INNER JOIN `reservation` ON (`wo`.`ID` = `reservation`.`WOID`)

  INNER JOIN `assets` ON (`reservation`.`ASSETID` = `assets`.`ID`)

  INNER JOIN `assettypes` ON (`assets`.`TYPEID` = `assettypes`.`ID`)

WHERE

  (`wo`.`TYPE` = "SCP") AND 

  (`lots`.`YEAR` = "'.$_SESSION['vintage'].'")

ORDER BY

  `wo`.`DUEDATE`';



$result=mysql_query($query);



for ($i=0;$i<mysql_num_rows($result);$i++)

{

	$row=mysql_fetch_array($result);

	if (pressedoff($row['LOTNUMBER'],$row['DUEDATE'])==0)

	{

		$vessel[$row['NAME1']]['haswo']=0;

		$vessel[$row['NAME1']]['lot']=$row['LOT'];

		$vessel[$row['NAME1']]['date']=$row['DUEDATE'];

	}

}

foreach ($vessel as $key=>$value)

{

    if (hasfermwo($value['lot'],date("Y-m-d",time()))==1)

    {

    	$value['haswo']=1;

    }   	

}



echo '<pre>';

print_r($vessel);

echo '</pre>';

?>

<script language="JavaScript">

<!--

tigra_tables('table1', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

// -->

            </script>



</body>



</html>