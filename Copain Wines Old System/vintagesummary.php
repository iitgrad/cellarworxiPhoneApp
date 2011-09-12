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

}

else

{

	$lvintage=$_SESSION['vintage'];

}

	$queryvintage='SELECT DISTINCT lots.YEAR from lots WHERE lots.YEAR="'.$lvintage.'" ORDER BY lots.YEAR, lots.LOTNUMBER';



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

     (clients.CODE="'.$lclientcode.'")) ORDER BY lots.DESCRIPTION';

	$result=mysql_query($query);

	

	echo '<table id="lots_table" border="0" align="center">';

	echo '<tr>';

	echo '<td colspan=4 align=center><b><big>VINTAGE SUMMARY FOR '.$_SESSION['vintage'].'</big></b></td>';

	echo '</tr>';

	echo '<tr>';

	echo '<td width=10 align=center><b>F</b><hr></td>';

	echo '<td width=80 align=center><b>LOT</b><hr></td>';

	echo '<td width=300 align=center><b>DESCRIPTION</b><hr></td>';

	echo '<td width=70 align=center><b>GALLONS</b><hr></td>';

	echo '<td width=70 align=center><b>CASES</b><hr></td>';

	echo '<td width=30 align=right><b>ALC</b><hr></td>';

	echo '<td width=70 align=center><b>STATE</b><hr></td>';

	echo '<td width=30 align=center><b>FLAGS</b><hr></td>';

	echo '</tr>';

	

	for ($i=0; $i <mysql_num_rows($result); $i++)

	{

		$row = mysql_fetch_array($result);

		$record=lotinforecords($row['LOTNUMBER'],'WO','2003-12-31');

		echo '<tr>';

		//if (checkrule($row['LOTNUMBER'],$lclientcode,"GLUFRU LIMIT")==1)

				if ((checkrule($row['LOTNUMBER'],$lclientcode,"GLUFRU LIMIT")==1) &

				 (checkrule($row['LOTNUMBER'],$lclientcode,"MALIC ACID LIMIT")==1))

		{

			$finished=1;

			echo '<td align=center>[F]</td>';

		}

		else

		{

			echo '<td></td>';

			$finished=0;

		}

		echo '<td><a href=showlotinfo.php?lot='.$row['LOTNUMBER'].'>'.$row['LOTNUMBER'].'</a></td>';

		echo '<td>'.strtoupper($row['DESCRIPTION']).'</td>';

		$lr=count($record)-1;

		$tg=$record[$lr]['ending_toppinggallons']+$record[$lr]['ending_bbls']*60+$record[$lr]['ending_tankgallons'];

		$cumtg+=$tg;

		echo '<td align=right>'.number_format($tg,2).'</td>';

		echo '<td align=right>'.number_format((.42*$tg),0).'</td>';

		echo '<td align=right>'.number_format($record[$lr]['alcohol'],1).'</td>';

		if ($tg<.001)

		echo '<td ></td>';

		else

		echo '<td align=center>'.$record[$lr]['end_state'].'</td>';

		echo '<td align=center>';

		if ($tg>.001)

		{

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

		}

		echo '</td>';

		echo '</tr>';

	}

	echo ' <tr><td colspan=2></td>';

	echo ' <td align="right">TOTAL:</td>';

	echo ' <td align="right">'.number_format($cumtg,2).'</td>';

	echo ' <td align="right">'.number_format(($cumtg*.42),0).'</td>';

	

	echo '</tr>';

	echo '</table></table>';

	

}

?>



			<script language="JavaScript">

			<!--

			tigra_tables('lots_table', 2, 1, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

			// -->

			</script>

</body>

</html>

