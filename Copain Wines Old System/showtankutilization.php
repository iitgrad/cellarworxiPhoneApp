<?php

session_start();

?>



<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

  

<title>Fermentation Protocol</title>

<link rel="stylesheet" type="text/css" href="../site.css">

	<script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>

	    <script language="JavaScript" type="text/javascript">

 function reload(custid,doc1) {

 	link=doc1+'?'+custid+'='+document.theform[custid].value;

   parent.maincontent.location.href=link;

}

</script>

<?php

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");

include ("totalgallons.php");

include ("./jpgraph/jpgraph.php");

include ("./jpgraph/jpgraph_line.php");



$maxdrop=30;

$whites='(wt.VARIETY="CHARDONNAY" OR wt.VARIETY="RIESLING" OR wt.VARIETY="VIOGNIER" OR wt.VARIETY="ROUSSANNE" or wt.VARIETY="VIOGNIER" OR wt.VARIETY="SAVIGNON BLANC")';



if (isset($_GET['timeintank']))

{

	$timeintank=$_GET['timeintank'];

}

else 

$timeintank=14;



if (isset($_GET['custid']))

{

	$query='select sum(assets.CAPACITY) AS TOTALTANKGALLONS from assets WHERE assets.TYPEID=6 and assets.OWNER="'.$_GET['custid'].'" and assets.HIDDEN="NO" and NOT (assets.DESCRIPTION="CLOSED TOP")';

//	echo $query;

}

else

$query='select sum(assets.CAPACITY) AS TOTALTANKGALLONS from assets WHERE assets.TYPEID=6 and assets.HIDDEN="NO" and NOT (assets.DESCRIPTION="CLOSED TOP")';

$result=mysql_query($query);

$row=mysql_fetch_array($result);

$totalfacilitygallons=$row['TOTALTANKGALLONS'];



if (isset($_GET['custid']))

{

   $query='select SUM(`bindetail`.`BINCOUNT`) AS `TOTALBINS`, SUM(`bindetail`.`WEIGHT`) AS `TOTALWEIGHT`, SUM(`bindetail`.`TARE`) AS `TOTALTARE`,

  `wt`.`TAGID`, `wt`.`VARIETY`, `wt`.`VINEYARD`, `wt`.`APPELLATION`, wt.DATETIME,  `wt`.`CLIENTCODE`

   FROM  `bindetail`

     INNER JOIN `wt` ON (`bindetail`.`WEIGHTAG` = `wt`.`ID`)

     INNER JOIN clients on (wt.CLIENTCODE = clients.clientid)

   WHERE

     NOT '.$whites.' AND

     wt.CLIENTCODE="'.clientid($_GET['custid']).'"

   GROUP BY  `wt`.`TAGID`,  `wt`.`VARIETY`,  `wt`.`VINEYARD`,  `wt`.`APPELLATION`,  `wt`.`CLIENTCODE`

   ORDER BY

    wt.DATETIME';

}

elseif (isset($_GET['xcustid']))

{

   $query='select SUM(`bindetail`.`BINCOUNT`) AS `TOTALBINS`,  SUM(`bindetail`.`WEIGHT`) AS `TOTALWEIGHT`,  SUM(`bindetail`.`TARE`) AS `TOTALTARE`,

  `wt`.`TAGID`, `wt`.`VARIETY`, `wt`.`VINEYARD`, `wt`.`APPELLATION`,  wt.DATETIME, `wt`.`CLIENTCODE`

   FROM

     `bindetail`

   INNER JOIN `wt` ON (`bindetail`.`WEIGHTAG` = `wt`.`ID`)

   INNER JOIN clients on (wt.CLIENTCODE = clients.clientid)

   WHERE

     NOT '.$whites.' AND

     NOT(wt.CLIENTCODE="'.clientid($_GET['xcustid']).'")

   GROUP BY

     `wt`.`TAGID`,`wt`.`VARIETY`,`wt`.`VINEYARD`,`wt`.`APPELLATION`,`wt`.`CLIENTCODE`

   ORDER BY

     wt.DATETIME';

}

else

{

   $query='select SUM(`bindetail`.`BINCOUNT`) AS `TOTALBINS`,  SUM(`bindetail`.`WEIGHT`) AS `TOTALWEIGHT`,  SUM(`bindetail`.`TARE`) AS `TOTALTARE`,

  `wt`.`TAGID`, `wt`.`VARIETY`, `wt`.`VINEYARD`, `wt`.`APPELLATION`,  wt.DATETIME, `wt`.`CLIENTCODE`

   FROM

     `bindetail`

   INNER JOIN `wt` ON (`bindetail`.`WEIGHTAG` = `wt`.`ID`)

   INNER JOIN clients on (wt.CLIENTCODE = clients.clientid)

   WHERE

        not '.$whites.' and wt.DATETIME>"2006-01-01" 

   GROUP BY

     `wt`.`TAGID`,`wt`.`VARIETY`,`wt`.`VINEYARD`,`wt`.`APPELLATION`,`wt`.`CLIENTCODE`

   ORDER BY

     wt.DATETIME';

   $querywhites='select SUM(`bindetail`.`BINCOUNT`) AS `TOTALBINS`,  SUM(`bindetail`.`WEIGHT`) AS `TOTALWEIGHT`,  SUM(`bindetail`.`TARE`) AS `TOTALTARE`,

  `wt`.`TAGID`, `wt`.`VARIETY`, `wt`.`VINEYARD`, `wt`.`APPELLATION`,  wt.DATETIME, `wt`.`CLIENTCODE`

   FROM

     `bindetail`

   INNER JOIN `wt` ON (`bindetail`.`WEIGHTAG` = `wt`.`ID`)

   INNER JOIN clients on (wt.CLIENTCODE = clients.clientid)

   WHERE

       '.$whites.' and wt.DATETIME>"2006-01-01" 

   GROUP BY

     `wt`.`TAGID`,`wt`.`VARIETY`,`wt`.`VINEYARD`,`wt`.`APPELLATION`,`wt`.`CLIENTCODE`

   ORDER BY

     wt.DATETIME';

}



if (isset($_GET['custid']))

{

$scpquery = 'SELECT *, wo.ID AS WOID from wo left outer join scp on (wo.ID=scp.WOID)

       WHERE (scp.VARIETAL <> "RIESLING" AND scp.VARIETAL <> "ROUSSANNE" AND scp.VARIETAL <> "VIOGNER" AND scp.VARIETAL <> "SAUVIGNON BLANC" AND scp.VARIETAL <> "CHARDONNAY" AND wo.CLIENTCODE="'.$_GET['custid'].'" AND wo.TYPE="SCP" AND wo.ENDDATE>"'.date("Y-m-d",(time()-86400)).'") ORDER BY ENDDATE';

//echo $query;

}

else

$scpquery = 'SELECT *, wo.ID AS WOID from wo left outer join scp on (wo.ID=scp.WOID)

       WHERE (scp.VARIETAL <> "RIESLING" AND scp.VARIETAL <> "ROUSSANNE" AND scp.VARIETAL <> "VIOGNER" AND scp.VARIETAL <> "SAUVIGNON BLANC" AND scp.VARIETAL <> "CHARDONNAY" AND wo.TYPE="SCP" AND wo.ENDDATE>"'.date("Y-m-d",(time()-86400)).'") ORDER BY ENDDATE';

//echo $scpquery; 



$scpresult=mysql_query($scpquery);



$result=mysql_query($query);

$resultwhites=mysql_query($querywhites);



$firstdate=0;

for ($i=0;$i<mysql_num_rows($result);$i++)

{

	$row=mysql_fetch_array($result);

	$thedate=strtotime($row['DATETIME']);

	$year=strftime("%Y",$thedate);

	if ($year==$_SESSION['vintage'])

	{

		$month=strftime("%m",$thedate);

		$day=strftime("%d",$thedate);

		$theday=mktime(0,0,0,$month,$day,$year);

		if ($firstdate==0)

		{

			$thefirstdate=$theday;

			$firstdate=1;

		}

		$tonsperday[($theday-$thefirstdate)/86400]+=($row['TOTALWEIGHT']-$row['TOTALTARE'])/2000;

		$maxdaycount=($theday-$thefirstdate)/86400;

		$totalredtons+=($row['TOTALWEIGHT']-$row['TOTALTARE'])/2000;

	}

}



for ($i=0; $i<=$maxdaycount; $i++)

{

	if($tonsperday[$i]==0)$tonsperday[$i]=0;

}



for ($i=0; $i<mysql_num_rows($resultwhites); $i++)

{

	$row=mysql_fetch_array($resultwhites);

	$thedate=strtotime($row['DATETIME']);

		$month=strftime("%m",$thedate);

		$day=strftime("%d",$thedate);

		$theday=mktime(0,0,0,$month,$day,$year);

		$whitetonsperday[($theday-$thefirstdate)/86400]+=($row['TOTALWEIGHT']-$row['TOTALTARE'])/2000;

		$maxdaycount=($theday-$thefirstdate)/86400;

		$totalwhitetons+=($row['TOTALWEIGHT']-$row['TOTALTARE'])/2000;

}





for ($i=0; $i<mysql_num_rows($scpresult); $i++)

{

	$row=mysql_fetch_array($scpresult);

	$thedate=strtotime($row['ENDDATE']);

			$month=strftime("%m",$thedate);

		$day=strftime("%d",$thedate);

		$theday=mktime(0,0,0,$month,$day,$year);

		$scptonsperday[($theday-$thefirstdate)/86400]+=$row['ESTTONS'];

		$maxdaycount=($theday-$thefirstdate)/86400;

}



$i=0;

$prevday=1;

while (($i<=$maxdaycount-1) | ($prevday>0))

{

	$curve[$i]+=max(0,$scptonsperday[$i]+$tonsperday[$i]+$curve[$i-1]-min(($maxdrop-$whitetonsperday[$i]),$curve[$i-$timeintank]));

	if ($curve[$i]>$peak['amount'])

	{

		 $peak['amount']=$curve[$i];

		 $peak['ondate']=$i*86400+$thefirstdate;

	}

	$prevday=$curve[$i];

	$i+=1;

	if ($i>500) break;

}



//echo '<pre>';

//print_r($scptonsperday);

//echo '</pre>';



foreach ($tonnage2 as $key=>$value)

{

//	$tonnage2[$key]+=$tonnage[$key];

//	if ($tonnage[$key]==0) $tonnage[$key]=0;

}



foreach ($tonnage as $key=>$value)

{

	if ($tonnage2[$key]==0) $tonnage2[$key]='';

}



for ($i=0;$i<count($curve);$i++)

{

	$cap[$i]=$totalfacilitygallons/300;

}



$_SESSION['thetonnage']=$curve;

//$_SESSION['thetonnage2']=$tonnage2;

$_SESSION['thecap']=$cap;





echo '<pre>';

//print_r ($tonsperday);

//print_r ($_SESSION['thetonnage']);

//print_r ($tonnage);

//print_r ($tonnage2);

echo '</pre>';





echo '<table border=1 align=center width=100%>';

echo '<tr><td align=center width=20%>';

echo 'FIRST RED FRUIT: '.date("m/d/Y",$thefirstdate).'<br>';

echo 'DAYS IN TANK: '.$timeintank.'<br>';

echo 'MAXIMUM RED PRESS TONS PER DAY: '.$maxdrop.'<br>';

echo 'AVG TONS PER TANK: 300<br>';

echo 'TOTAL TANK GALLONS : '.$totalfacilitygallons.'<br>';

echo '% OF CAPACITY: '.number_format(($peak['amount']*300)/$totalfacilitygallons*100,0).'%<br>';;

echo 'PEAK TONS IN TANK: '.$peak['amount'].'<br>ON: '.date("m/d/Y",$peak['ondate']).'<br><br>';

echo 'TOTAL RED TONS: '.$totalredtons.'<br>';

echo 'TOTAL WHITE TONS: '.$totalwhitetons.'<br>';

echo 'TOTAL TONS: '.($totalredtons+$totalwhitetons).'<br>';

/*

echo '<form name=theform>EXCLUDE CLIENT: '.DrawComboFromData("clients","CODE",$_GET['xcustid'],"xcustid");

echo '<a href="javascript:reload(\'xcustid\',\''.$PHP_SELF.'\')">refresh</a><br><br>';

echo 'LIMIT TO CLIENT: '.DrawComboFromData("clients","CODE",$_GET['custid'],"custid");

echo '<a href="javascript:reload(\'custid\',\''.$PHP_SELF.'\')">refresh</a><br><br>';

echo '</td><td align=center width=80%>';

*/

echo '<img src=showtankutilizationgraph.php>';

echo '</td></tr>';

echo '</table>';



?>



</body>



</html>

