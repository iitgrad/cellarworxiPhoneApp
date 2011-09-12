<?php
	require_once('JSON.php');
	require_once('startdb.php');
	require_once('lotinforecords.php');
	require_once('staff.php');
	
	$json = new Services_JSON();
	
	if ($_GET['action']=="totalweight")
	{
		$query='SELECT lots.YEAR,  SUM(`bindetail`.`WEIGHT`) AS `TOTWEIGHT`,  SUM(`bindetail`.`TARE`) AS `TOTTARE`,
		  `wt`.`CLIENTCODE`,
		  `clients`.`CLIENTNAME`
		FROM
		  `bindetail`
		  INNER JOIN `wt` ON (`bindetail`.`WEIGHTAG` = `wt`.`ID`)
		  INNER JOIN `lots` ON (`wt`.`LOT` = `lots`.`LOTNUMBER`)
		  INNER JOIN `clients` ON (`lots`.`CLIENTCODE` = `clients`.`clientid`)
		WHERE
		  (`lots`.`YEAR` = "'.$_GET['vintage'].'") and (wt.CLIENTCODE="'.$_GET['clientcode'].'") GROUP BY CLIENTCODE';

		$result=mysql_query($query);
		$row=mysql_fetch_array($result);

		$record[]=$row['TOTWEIGHT']-$row['TOTTARE'];
	}
	else
	{
		sleep(5);
		$record[]=99;
	}
		
	
	if ($_GET['debug']==1)
	{
		echo '<pre>';
		print_r($record);
	}
	else
	{
		$output = $json->encode($record);
		print $output;		
	}

	
?>
