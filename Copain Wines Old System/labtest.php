<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

    <script language="JavaScript" type="text/javascript">

    function navigate(choice)

    {

    	var url=choice.options[choice.selectedIndex].value;

    	if (url)

    	{

    		location.href=url;

    	}

    }

    function navigatechecked(choice)

    {

    	var url=choice.value;

    	if (choice.checked)

    	url=url+"YES";

    	else

    	url=url+"NO";

    	if (url)

    	{

    		location.href=url;

    	}

    }

  </script>

</head>



<body>

<body onLoad="document.addtest.value1.focus()">

<?php

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");

include ("totalgallons.php");

if ($_GET['action']=="setclient")

$_SESSION['clientcode']=$_GET['clientcode'];

if ($_GET['action']=="clearclient")

$_SESSION['clientcode']="";

if ($_GET['lot'])

$_SESSION['lot']=$_GET['lot'];

if ($_GET['woid'])

$_SESSION['woid']=$_GET['woid'];

if ($_GET['action']=="mod")

{

	$query='UPDATE labresults SET '.

	'labresults.VALUE1="'.$_POST['value1'].'",'.

	'labresults.UNITS1="'.$_POST['units1'].'",'.

	'labresults.LABTESTID="'.$_POST['labtestid'].'",'.

	'labresults.COMMENT="'.$_POST['comment'].'" WHERE (labresults.ID = "'.$_GET['labresultid'].'")';

	//    echo $query;

	$result=mysql_query($query);

	

	if ($_POST['btid']>0)

	{

		$query='UPDATE buffertitration SET '.

		   'INITIALPH="'.$_POST['initialph'].'", '.

		   'PH1="'.$_POST['ph1'].'", '.

		   'PH2="'.$_POST['ph2'].'", '.

		   'PH3="'.$_POST['ph3'].'", '.

		   'PH4="'.$_POST['ph4'].'", '.

		   'PH5="'.$_POST['ph5'].'", '.

		   'PH6="'.$_POST['ph6'].'", '.

		   'PH7="'.$_POST['ph7'].'", '.

		   'NAOH1="'.$_POST['naoh1'].'", '.

		   'NAOH2="'.$_POST['naoh2'].'", '.

		   'NAOH3="'.$_POST['naoh3'].'", '.

		   'NAOH4="'.$_POST['naoh4'].'", '.

		   'NAOH5="'.$_POST['naoh5'].'", '.

		   'NAOH6="'.$_POST['naoh6'].'", '.

		   'NAOH7="'.$_POST['naoh7'].'" '.

		   ' WHERE ID="'.$_POST['btid'].'"';

//		   echo $query;

		mysql_query($query);

	}

	

	if ($_POST['labtest']=="BRIX")

	{

		$query='SELECT `wo`.`LOT`, wo.DUEDATE FROM `labresults`  INNER JOIN `labtest` ON (`labresults`.`LABTESTID` = `labtest`.`ID`)

                INNER JOIN `wo` ON (`labtest`.`WOID` = `wo`.`ID`) WHERE labresults.ID="'.$_GET['labresultid'].'"';

		//      echo $query;

		$result=mysql_query($query);

		$row=mysql_fetch_array($result);

		$lot=$row['LOT'];

		$date=$row['DUEDATE'];

		

		$query='SELECT `wo`.`LOT`, `assets`.`NAME` FROM  `wo`

              INNER JOIN `reservation` ON (`wo`.`ID` = `reservation`.`WOID`)

              INNER JOIN `assets` ON (`reservation`.`ASSETID` = `assets`.`ID`)

              WHERE  (`wo`.`TYPE` = "SCP"  AND wo.LOT="'.$lot.'")';

		$result=mysql_query($query);

		for ($i=0;$i<mysql_num_rows($result);$i++)

		{

			$row=mysql_fetch_array($result);

			$vesseldata=explode("-",$row['NAME']);

			$vesseltype=$vesseldata[0];

			$vessel=$vesseldata[1];

			$checkquery='select * from brixtemp where lot="'.$lot.'" and vessel="'.$vessel.'" and vesseltype="'.$vesseltype.'" and DATE="'.$date.'" and BRIX="'.$_POST['value1'].'"';

			$checkresult=mysql_query($checkquery);

			if (mysql_num_rows($checkresult)>0)

			$addquery='UPDATE brixtemp SET lot="'.$lot.'", vessel="'.$vessel.'", vesseltype="'.$vesseltype.'", BRIX="'.$_POST['value1'].'" WHERE DATE="'.$date.'"';

			else

			$addquery='INSERT into brixtemp SET lot="'.$lot.'", vessel="'.$vessel.'", vesseltype="'.$vesseltype.'", DATE="'.$date.'", BRIX="'.$_POST['value1'].'"';

			echo $addquery.'<br>';

			//mysql_query($addquery);

		}

		

	}

}

if ($_GET['action']=="add")

{

	$query='INSERT INTO labresults SET labresults.LABTEST="'.$_POST['labtest'].'",'.

	'labresults.VALUE1="'.$_POST['value1'].'",'.

	'labresults.UNITS1="'.$_POST['units1'].'",'.

	'labresults.LABTESTID="'.$_POST['labtestid'].'",'.

	'labresults.COMMENT="'.$_POST['comment'].'"';

	//    echo $query;

	$result=mysql_query($query);

}

if ($_GET['action']=="del")

{

	$query='DELETE FROM labresults WHERE labresults.ID="'.$_GET['labresultid'].'"';

	$result=mysql_query($query);

}



$wo=getwo($_SESSION['woid']);

$query='SELECT * FROM labtest WHERE labtest.WOID="'.$_SESSION['woid'].'"';

$result=mysql_query($query);

if (mysql_num_rows($result)==0)

{

	$insertquery='INSERT INTO labtest SET labtest.WOID="'.$_SESSION['woid'].'"';

	$result=mysql_query($insertquery);

	$result=mysql_query($query);

}

$row=mysql_fetch_array($result);

$labtestid=$row['ID'];

echo '<table align=center width=50% border="1">';

echo '<tr><td align=center colspan=5><a href=hardcopy/labresults.php?woid='.$_GET['woid'].'>PRINT</a></td></tr>';

echo '<tr>';

echo '<td align="center">';

echo 'DATE: '.$wo['duedate'];

echo '</td>';

echo '<td>';

echo 'LOT: <a href=showlotinfo.php?lot='.$wo['lot'].'>'.$wo['lot'].'</a>';

echo '</td>';

echo '<td align="center">';

echo 'LAB TEST #:'.$row['ID'].'<br>';

echo '</td>';

echo '<td>';

echo 'WO: '.'<a href=wopage.php?action=view&woid='.$_SESSION['woid'].'>'.$_SESSION['woid'].'</a>';

echo '</td>';

echo '<td>';

echo 'LAB: '.DrawComboFromEnum ("labtest","lab",$_SESSION['lab'],"lab");

echo '</td>';

echo '</table>';

echo '<table align=center width=50% border="1">';

echo '<tr><td></td><td>LAB TEST</td><td align=right>RESULT</td><td align=right>UNITS</td><td align=center>COMMENTS</td></tr>';

$query='SELECT * FROM `labtest`

     INNER JOIN `labresults` ON (`labtest`.`ID` = `labresults`.`LABTESTID`) WHERE labtest.ID="'.$row['ID'].'"';

$result=mysql_query($query);

$num_rows=mysql_num_rows($result);

for ($i=0;$i<$num_rows;$i++)

{

	$row=mysql_fetch_array($result);

	if ($row['LABTEST']!="BUFFER_TITRATION")

	{

		echo '<tr>';

		echo '<td>'.'<a href='.$PHP_SELF.'?action=del&woid='.$_SESSION['woid'].'&labresultid='.$row['ID'].'>del</a></td>';

		echo '<form method="POST" action="'.$PHP_SELF.'?action=mod&woid='.$_SESSION['woid'].'&labresultid='.$row['ID'].'">';

		echo '<td>'.'<input type=hidden name="labtest" value="'.$row['LABTEST'].'">'.$row['LABTEST'].'</td>';

		echo '<td>'.'<input type=textbox name="value1" value="'.$row['VALUE1'].'" size=7>'.'</td>';

		echo '<td>'.'<input type=textbox name="units1" value="'.$row['UNITS1'].'" size=7>'.'</td>';

		echo '<td>'.'<textarea name="comment" cols=50>'.$row['COMMENT'].'</textarea>'.'</td>';

		echo '<input type=hidden value='.$labtestid.' name="labtestid">';

		echo '<td>'.'<input type=submit value=mod></td></form>';

		echo '</tr>';

	}

	else

	{

		echo '<tr>';

		echo '<td>'.'<a href='.$PHP_SELF.'?action=del&woid='.$_SESSION['woid'].'&labresultid='.$row['ID'].'>del</a></td>';

		echo '<form method="POST" action="'.$PHP_SELF.'?action=mod&woid='.$_SESSION['woid'].'&labresultid='.$row['ID'].'">';

		echo '<td>'.'<input type=hidden name="labtest" value="'.$row['LABTEST'].'">'.$row['LABTEST'].'</td>';

		echo '<td colspan=3><table width=100% border=1>';



		$btquery='select * from buffertitration where LABRESULTSID="'.$row['ID'].'"';

		$btresult=mysql_query($btquery);

		if (mysql_num_rows($btresult)==0)

		{

			$btquery='insert into buffertitration set LABRESULTSID="'.$row['ID'].'"';

			$btresult=mysql_query($btquery);

			$btid=mysql_insert_id();

			$btquery='select * from buffertitration where ID="'.$btid.'"';

			$btresult=mysql_query($btquery);

		}

		$btrow=mysql_fetch_array($btresult);

		echo '<tr>';

		echo '<input type=hidden name=btid value='.$btrow['ID'].'>';

		echo '<td align=right width=16%>PH:</td><td align=right width=12%><input type=text size=5 value='.number_format($btrow['INITIALPH'],2).' name=initialph></td><td colspan=5><textarea name="comment" cols=50>'.$row['COMMENT'].'</textarea></td></tr>';

		echo '<tr><td align=right>PH</td>';

		echo '<td align=right width=12%><input type=text size=5 value='.number_format($btrow['PH1'],2).' name=ph1></td>';

		echo '<td align=right width=12%><input type=text size=5 value='.number_format($btrow['PH2'],2).' name=ph2></td>';

		echo '<td align=right width=12%><input type=text size=5 value='.number_format($btrow['PH3'],2).' name=ph3></td>';

		echo '<td align=right width=12%><input type=text size=5 value='.number_format($btrow['PH4'],2).' name=ph4></td>';

		echo '<td align=right width=12%><input type=text size=5 value='.number_format($btrow['PH5'],2).' name=ph5></td>';

		echo '<td align=right width=12%><input type=text size=5 value='.number_format($btrow['PH6'],2).' name=ph6></td>';

		echo '<td align=right width=12%><input type=text size=5 value='.number_format($btrow['PH7'],2).' name=ph7></td></tr>';

		echo '<tr><td align=right>NaOH</td>';

		echo '<td align=right width=12%><input type=text size=5 value='.number_format($btrow['NAOH1'],1).' name=naoh1></td>';

		echo '<td align=right width=12%><input type=text size=5 value='.number_format($btrow['NAOH2'],1).' name=naoh2></td>';

		echo '<td align=right width=12%><input type=text size=5 value='.number_format($btrow['NAOH3'],1).' name=naoh3></td>';

		echo '<td align=right width=12%><input type=text size=5 value='.number_format($btrow['NAOH4'],1).' name=naoh4></td>';

		echo '<td align=right width=12%><input type=text size=5 value='.number_format($btrow['NAOH5'],1).' name=naoh5></td>';

		echo '<td align=right width=12%><input type=text size=5 value='.number_format($btrow['NAOH6'],1).' name=naoh6></td>';

		echo '<td align=right width=12%><input type=text size=5 value='.number_format($btrow['NAOH7'],1).' name=naoh7></td></tr>';

		echo '</table>';

		echo '<input type=hidden value='.$labtestid.' name="labtestid">';

		echo '<td>'.'<input type=submit value=mod></td></form>';

		echo '</tr>';

	}

	

}

echo '<tr>';

echo '<td></td>';

echo '<form name=addtest method="POST" action="'.$PHP_SELF.'?action=add&woid='.$_SESSION['woid'].'">';

echo '<td>'.DrawComboFromEnum("labresults","LABTEST","","labtest").'</td>';

echo '<td>'.'<input type=textbox name="value1" size=7>'.'</td>';

echo '<td>'.'<input type=textbox name="units1" size=7>'.'</td>';

echo '<td>'.'<textarea name="comment" cols=50></textarea>'.'</td>';

echo '<input type=hidden value='.$labtestid.' name="labtestid">';

echo '<td>'.'<input type=submit value=add></td></form>';

echo '</tr>';



echo '<tr><td>';

echo '</table>';

?>



</body>



</html>