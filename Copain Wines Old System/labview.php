<?php

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



function numformat($num,$dec)

{

	if ($num==0)

	return "";

	else

	return number_format($num,$dec);

}



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



if ($_GET['summary']=="YES")

{

	echo '<table id="lots_table" border=0 align=center>';

	echo '<tr>';

	echo '<td colspan=5 align=center><b><big>CLIENT VOLUME SUMMARY</big></b></td>';

	echo '</tr>';

	echo '<tr>';

	echo '<td width=300 align=center><b>CLIENT</b><hr></td>';

	echo '<td width=70 align=center><b>GALLONS</b><hr></td>';

	echo '<td width=70 align=center><b>CASES</b><hr></td>';

	echo '</tr>';

	$grandtg=0;

	$resultclient=mysql_query($queryclients);

	for ($i=0;$i<mysql_num_rows($resultclient);$i++)

	{

		$rowclient=mysql_fetch_array($resultclient);

		$resultvintage=mysql_query($queryvintage);

		$cumtg=0;

		for ($j=0;$j<mysql_fetch_array($resultvintage);$j++)

		{

			$rowvintage=mysql_fetch_array($resultvintage);

			$query='SELECT * from lots inner join clients on (lots.CLIENTCODE=clients.CLIENTID) where ((lots.YEAR="'.$_SESSION['vintage'].'") AND

             (clients.CODE="'.$rowclient['CODE'].'")) ORDER BY lots.LOTNUMBER';

			$result=mysql_query($query);

			for ($k=0;$k<mysql_num_rows($result);$k++)

			{

				$row=mysql_fetch_array($result);

				$record=lotinforecords($row['LOTNUMBER']);

				$lr=count($record)-1;

				$tg=$record[$lr]['ending_toppinggallons']+$record[$lr]['ending_bbls']*60+$record[$lr]['ending_tankgallons'];

				$cumtg+=$tg;

				$grandtg+=$tg;

			}

		}

		echo '<tr>';

		echo '<td><a href='.$PHP_SELF.'?clientcode='.$rowclient['CODE'].'>'.$rowclient['CLIENTNAME'].'</a></td>';

		echo '<td align=right>'.number_format($cumtg,2).'</td>';

		echo '<td align=right>'.number_format(($cumtg*.42),0).'</td>';

		echo '</tr>';

	}

	echo '<tr><td>TOTAL:</td>';

	echo '<td align=right><hr>'.number_format($grandtg,2).'</td>';

	echo '<td align=right><hr>'.number_format(($grandtg*.42),0).'</td></tr>';

	echo '</table>';

}

else

{

	$query='SELECT * from lots inner join clients on (lots.CLIENTCODE=clients.CLIENTID) where ((lots.YEAR="'.$lvintage.'") AND

     (clients.CODE="'.$lclientcode.'")) ORDER BY lots.LOTNUMBER';

	$result=mysql_query($query);

	

	echo '<table id="lots_table" border="1" align="center">';

	echo '<tr>';

	echo '<td colspan=18 align=center><b><big>VINTAGE LAB VIEW FOR '.$_SESSION['vintage'].'</big></b></td>';

	echo '</tr>';

	echo '<tr>';

	$numwidth=35;

	echo '<td width=10></td>';

	echo '<td width=60 align=center><b>LOT</b><hr></td>';

	echo '<td width=150 align=center><b>DESCRIPTION</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>pH</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>TA</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>VA</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>F SO2</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>T SO2</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>TART</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>MALIC</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>POT</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>AMM</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>AM_N</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>YAN</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>BRIX</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>GLU</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>FRU</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>G+F</b><hr></td>';

	echo '<td width='.$numwidth.' align=center><b>ALC</b><hr></td>';

	echo '</tr>';

	

	for ($i=0; $i <mysql_num_rows($result); $i++)

	{

		$row = mysql_fetch_array($result);

		$record=lotinforecords($row['LOTNUMBER'],'WO','2003-12-31');

		$labanalysis=currentlabanalysis($row['LOTNUMBER']);

		echo '<tr>';

		if (checkrule($row['LOTNUMBER'],$lclientcode,"GLUFRU LIMIT")==1)

		{

			echo '<td align=center>[F]</td>';

			$finished=1;

		}

		else

		{

			echo '<td></td>';

			$finished=0;

		}

		echo '<td><a href=showlotinfo.php?lot='.$row['LOTNUMBER'].'>'.$row['LOTNUMBER'].'</a></td>';

		echo '<td>'.strtoupper($row['DESCRIPTION']).'</td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=PH>'.numformat($labanalysis['PH']['value'],2).'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=TA>'.numformat($labanalysis['TA']['value'],2).'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=VA>'.$labanalysis['VA']['value'].'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=FSO2>'.$labanalysis['FSO2']['value'].'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=TSO2>'.$labanalysis['TS02']['value'].'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=TARTARIC>'.$labanalysis['TARTARIC']['value'].'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=MALIC_ACID>'.numformat($labanalysis['MALIC_ACID']['value'],2).'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=POTASSIUM>'.numformat($labanalysis['POTASSIUM']['value'],0).'</a></td>';

        $thesum=$labanalysis['AMMONIA']['value']+$labanalysis['AMINO_NITROGEN']['value'];

   //     echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=NOPA>'.$labanalysis['AMMONIA']['value']+$labanalysis['AMINO_NITROGEN']['value'].'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=AMMONIA>'.numformat($labanalysis['AMMONIA']['value'],0).'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=AMINO_NITROGEN>'.numformat($labanalysis['AMINO_NITROGEN']['value'],0).'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=YAN>'.numformat($thesum,0).'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=BRIX>'.numformat($labanalysis['BRIX']['value'],1).'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=GLU>'.$labanalysis['GLU']['value'].'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=FRU>'.$labanalysis['FRU']['value'].'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=GLUFRU>'.numformat($labanalysis['GLUFRU']['value'],1).'</a></td>';

        echo '<td align=center><a href=labresultshistory.php?lot='.$row['LOTNUMBER'].'&labtest=ALCOHOL>'.$labanalysis['ALCOHOL']['value'].'</a></td>';

        echo '<td align=center>';

		if (checkrule($row['LOTNUMBER'],$lclientcode,"NEVER TOPPED")==1)

		{

			echo 'N';

		}

		else

		{

			if (checkrule($row['LOTNUMBER'],$lclientcode,"TOPPING FREQUENCY")==1)

			{

				echo 'T';

			}

		}

		if (checkrule($row['LOTNUMBER'],$lclientcode,"SULPHUR TEST FREQUENCY")==1)

		{

			if ($finished==1)

			  echo 'S';

		}

		echo '</td>';

		echo '</tr>';

	}

	echo '</table></table>';

	

	/*	echo '<table align=center width=75%>';

	echo '<tr><td align=center>VARIETAL</td><td align=center>TONS</td><td align=center>%</td></tr>';

	$querytemp='SELECT `wt`.`CLIENTCODE`, SUM(`bindetail`.`WEIGHT`) AS `TOTALWEIGHT`, SUM(`bindetail`.`TARE`) AS `TOTTARE`,

	`wt`.`VARIETY` FROM `wt` INNER JOIN `bindetail` ON (`wt`.`ID` = `bindetail`.`WEIGHTAG`)

	WHERE (`wt`.`CLIENTCODE` = "'.clientid($_SESSION['clientcode']).'") GROUP BY `wt`.`CLIENTCODE`, `wt`.`VARIETY`';

	$resulttemp=mysql_query($querytemp);

	$overall=0;

	for ($i=0;$i<mysql_num_rows($resulttemp);$i++)

	{

	$rowtemp=mysql_fetch_array($resulttemp);

	$varietal[]=$rowtemp['VARIETY'];

	$thetons[]=($rowtemp['TOTALWEIGHT']-$rowtemp['TOTTARE'])/2000;

	$overall+=$thetons[$i];

	}

	if ($overall>0)

	{

	for ($i=0;$i<count($varietal);$i++)

	{

	$perc=$thetons[$i]/$overall;

	echo '<tr><td align=center>'.$varietal[$i].'</td><td align=center>'.$thetons[$i].'</td><td align=center>'.number_format($perc*100,0).'%</td></tr>';

	}

	}

	echo '</td>';

	*/

}

?>



			<script language="JavaScript">

			<!--

			tigra_tables('lots_table', 2, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

			// -->

			</script>

</body>

</html>

