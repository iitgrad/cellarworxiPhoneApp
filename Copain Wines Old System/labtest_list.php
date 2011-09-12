<?php

session_start();

?>

<html>



<head>

  <title></title>

<link rel="stylesheet" type="text/css" href="../site.css">

     <script type="text/javascript" src="popup/overlibmws.js"></script>

   <script type="text/javascript" src="popup/overlibmws_bubble.js"></script>

     <script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>



</head>



<body>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div> 



<?php

//echo date('l dS \of F Y h:i:s A'); exit;

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("lotinforecords.php");



if ($_POST['OVERDUEnumrecs']>0)

{

  foreach ($_POST as $key=>$value)

  {

  	if ($key!="OVERDUEnumrecs")

  	{

  		$compquery='update wo SET STATUS="COMPLETED" WHERE ID="'.$value.'"';

  		mysql_query($compquery);

  	}

  }

}

if ($_POST['TODAYnumrecs']>0)

{

  foreach ($_POST as $key=>$value)

  {

  	if ($key!="TODAYnumrecs")

  	{

  		$compquery='update wo SET STATUS="COMPLETED" WHERE ID="'.$value.'"';

  		mysql_query($compquery);

  	}

  }

}

if ($_POST['FUTUREnumrecs']>0)

{

  foreach ($_POST as $key=>$value)

  {

  	if ($key!="FUTUREnumrecs")

  	{

  		$compquery='update wo SET STATUS="COMPLETED" WHERE ID="'.$value.'"';

  		mysql_query($compquery);

  	}

  }

}



setdefaults();



if ($_SESSION['clientcode']=="")

{

	$ci=clientinfo($_SERVER['REMOTE_USER']);

	$_SESSION['clientcode']=$ci['code'];

}



function sameday($date1,$date2)

{

	if (date("m-d-Y",$date1)==date("m-d-Y",$date2))

	return 1;

	else

	return 0;

}



if (isstaff()!="YES")

$limit=" and CLIENTCODE=\"".strtoupper($_SESSION['clientcode']).'"';



$limit1=' and DATE(wo.DUEDATE)<DATE(NOW())';

$limit2=' and DATE(wo.DUEDATE)=DATE(NOW())';

$limit3=' and DATE(wo.DUEDATE)=DATE(ADDDATE(NOW(),1))';

$limit4=' and DATE(wo.DUEDATE)>DATE(ADDDATE(NOW(),1))';



function defaultquery()

{

	return 'SELECT `wo`.`LOT`, wo.TYPE,`wo`.`ID`, wo.DUEDATE, `labtest`.`LAB`, wo.OTHERDESC, wo.WORKPERFORMEDBY, `labresults`.`LABTEST`, labtest.LAB FROM `wo`

           LEFT OUTER JOIN `labtest` ON (`wo`.`ID` = `labtest`.`WOID`)

           LEFT OUTER JOIN `labresults` ON (`labtest`.`ID` = `labresults`.`LABTESTID`) where (wo.STATUS!="TEMPLATE" and wo.TYPE!="PUMP OVER" and wo.TYPE!="SCP" and wo.TYPE!="PRESSOFF" and wo.TYPE!="DRYICE" and wo.TYPE!="PUNCH DOWN") AND wo.DELETED!=1 and wo.STATUS!="COMPLETED" and wo.DELETED != "1"';

}

switch ($_GET['type'])

{

	case "labtest" :

	{

		$query=defaultquery();

		break;

	}

	case "popd" :

	{

		$query='SELECT `wo`.`LOT`, wo.TIMESLOT, wo.VESSELTYPE, wo.VESSELID, wo.OTHERDESC, wo.WORKPERFORMEDBY, wo.DURATION, wo.STRENGTH, wo.TYPE,`wo`.`ID`, wo.DUEDATE, `labtest`.`LAB`, `labresults`.`LABTEST` FROM `wo`

            LEFT OUTER JOIN `labtest` ON (`wo`.`ID` = `labtest`.`WOID`)

            LEFT OUTER JOIN `labresults` ON (`labtest`.`ID` = `labresults`.`LABTESTID`) where (wo.TYPE="PUMP OVER" or wo.TYPE="PUNCH DOWN" or wo.TYPE="DRYICE") AND wo.STATUS!="COMPLETED"';

		break;

	}

	case "pressoff" :

	{

		$query='SELECT `wo`.`LOT`, wo.TIMESLOT, wo.VESSELTYPE, wo.VESSELID, wo.OTHERDESC, wo.WORKPERFORMEDBY, wo.DURATION, wo.STRENGTH, wo.TYPE,`wo`.`ID`, wo.DUEDATE FROM `wo`

             where (wo.TYPE="PRESSOFF") AND wo.STATUS!="COMPLETED" ';

		//    echo $query;

		break;

	}

	case "other" :

	{

		$query='SELECT `wo`.`LOT`, wo.TIMESLOT, wo.VESSELTYPE, wo.VESSELID, wo.OTHERDESC, wo.WORKPERFORMEDBY, wo.DURATION, wo.STRENGTH, wo.TYPE,`wo`.`ID`, wo.DUEDATE, `labtest`.`LAB`, `labresults`.`LABTEST` FROM `wo`

            LEFT OUTER JOIN `labtest` ON (`wo`.`ID` = `labtest`.`WOID`)

            LEFT OUTER JOIN `labresults` ON (`labtest`.`ID` = `labresults`.`LABTESTID`) where (wo.STATUS!="TEMPLATE" and wo.TYPE!="PUMP OVER" and wo.TYPE!="SCP" and wo.TYPE!="DRYICE" and wo.TYPE!="PUNCH DOWN" and wo.TYPE!="ADDITION" and wo.TYPE!="LAB TEST") AND wo.STATUS!="COMPLETED"';

		break;

	}

	default :

	{

		$query=defaultquery();

	}

}

$query1=$query.$limit.$limit1." order by wo.DUEDATE, wo.WORKPERFORMEDBY, wo.TYPE";

$query2=$query.$limit.$limit2." order by wo.DUEDATE, wo.WORKPERFORMEDBY, wo.TYPE";

$query3=$query.$limit.$limit3." order by wo.DUEDATE, wo.WORKPERFORMEDBY, wo.TYPE";

$query4=$query.$limit.$limit4." order by wo.DUEDATE, wo.WORKPERFORMEDBY, wo.TYPE";



//echo $query3;



//echo $query3;

$result1=mysql_query($query1);

$result2=mysql_query($query2);

$result3=mysql_query($query3);

$result4=mysql_query($query4);



function showrecords($result,$value)

{

	echo '<form method=POST action='.$PHP_SELF.'?type='.$_GET['type'].'>';

	$numrecords=mysql_num_rows($result);

	if ($numrecords>0)

	{

		echo '<tr>';

		echo '<td rowspan='.$numrecords.' align=center width=10 style="border-style: solid; border-color: Sienna; border-width: 1">'.$value.'</td>';

	}

	echo '<input type=hidden name='.$value.'numrecs value='.mysql_num_rows($result).'>';

	for ($i=0; $i <mysql_num_rows($result); $i++)

	{

		$row=mysql_fetch_array($result);

		

		echo '<td align=center width=20>';

		echo '<input type=checkbox value='.$row['ID'].' name='.($value.$i).'>';

		echo '</td>';

		echo '<td align=center width=10 style="border-style: solid; border-color: Sienna; border-width: 1">'.($i+1).'</td>';

		echo    '<td align="center">';

		echo        $row['WORKPERFORMEDBY'];

		echo    '</td>';

		echo    '<td align="center">';

		echo        '<a href=showlotinfo.php?lot='.$row['LOT'].'>'.$row['LOT'].'</a>';

		echo    '</td>';

		echo    '<td align="center">';

		echo        '<a href=wopage.php?action=view&woid='.$row['ID'].'>'.$row['ID'].'</a>';

		echo    '</td>';

		echo    '<td align="center">';

		echo        date("m-d-Y",strtotime($row['DUEDATE']));

		echo    '</td>';

		echo    '<td width=200 align="center">';

		$textsize='11px';

		switch ($row['TYPE'])

		{

			case "PRESSOFF" :

			{

				echo '<a onmouseover="return overlib(\''.filter($row['OTHERDESC']).

				     '\',TEXTSIZE,\''.$textsize.'\',\'quotation\');"  onmouseout="nd();">'.$row['TYPE'].'</a>';

				$assets=getlist($row['ID'],2);

				for ($j=0;$j<count($assets);$j++)

				{

					echo '<br>'.$assets[$j]['name'].'-'.$assets[$j]['timeslot'];

				}

				break;

			}

			case 'PUMP OVER' :

			{

				$text=     $row['TIMESLOT'].' '.$row['VESSELTYPE'].'-'.$row['VESSELID'].' FOR '.$row['DURATION'].' MINUTES';

				echo '<a onmouseover="return overlib(\''.filter($text).

				     '\',TEXTSIZE,\''.$textsize.'\',\'quotation\');"  onmouseout="nd();">'.$row['TYPE'].'</a>';

				break;

			}

			case 'PUNCH DOWN' :

			{

				$text=    $row['TIMESLOT'].' '.$row['VESSELTYPE'].'-'.$row['VESSELID'].' '.$row['STRENGTH'];

				echo '<a onmouseover="return overlib(\''.filter($text).

				     '\',TEXTSIZE,\''.$textsize.'\',\'quotation\');"  onmouseout="nd();">'.$row['TYPE'].'</a>';

				break;

			}

			case 'LAB TEST' :

			{

				$labtests=getlabtests($row['ID']);

				$text='';

				for ($j=0;$j<count($labtests);$j++)

				{

					$text.=($j+1).': '.filter($labtests[$j]).'<br>';

				}

				if ($row['OTHERDESC']!="")

				    $text.='COMMENTS: '.filter($row['OTHERDESC']);

				echo '<a onmouseover="return overlib(\''.$text.

				     '\',TEXTSIZE,\''.$textsize.'\',\'quotation\');"  onmouseout="nd();">'.$row['TYPE'].'</a>';

				break;

			}

			default :

				echo '<a onmouseover="return overlib(\''.filter($row['OTHERDESC']).

				     '\',TEXTSIZE,\''.$textsize.'\',\'quotation\');"  onmouseout="nd();">'.$row['TYPE'].'</a>';

//			echo        $row['TYPE'];

			

		}

		echo    '</td>';

		

		echo '</tr>';

	}

	if ($numrecords>0)

	{

		echo '<tr><td COLSPAN=8 align=center>';

		echo '<input type=submit value="UPDATE">';

		echo '</td></tr>';

	}

	echo '</form>';

	

}





echo ' <font face="Franklin Gothic Book" size="2">';

echo '<table border=1 width=100%>';

echo '<tr><td colspan=2 align=center><b>OUTSTANDING WORK ORDERS</b></td></tr>';

echo '<tr><td valign=top width=15%>';

echo '<table width=100%>';

echo '<tr><td valign=top align=center>';

echo '<a href=labtest_list.php>LAB TESTS AND ADDITIONS</a><br><br>';

echo '<a href=labtest_list.php?type=popd>PUMP OVERS AND PUNCH DOWNS</a><br><br>';

echo '<a href=labtest_list.php?type=pressoff>PRESSINGS</a>';

echo '</td></tr>';

echo '</table>';

echo '</td>';

echo '<td width=80%>';

echo '<table id=list border=1 width="100%" align="center">';

echo '<tr>';

echo    '<td colspan></td>';

echo    '<td colspan align=center><big><b>C</b></big></td>';

echo    '<td align="center">';

echo        "<b>"." "."</b>";

echo    '</td>';

echo    '<td align="center">';

echo        "<b>".'ASSIGNED'."</b>";

echo    '</td>';

echo    '<td align="center">';

echo        "<b>".'LOT'."</b>";

echo    '</td>';

echo    '<td align="center">';

echo        "<b>".'WO'."</b>";

echo    '</td>';

echo    '<td align="center">';

echo        "<b>".'DATE'."</b>";

echo    '</td>';

echo    '<td align="center">';

echo        "<b>".'TYPE'."</b>";

echo    '</td>';

echo '</tr>';

showrecords($result1,"OVERDUE");

showrecords($result2,"TODAY");

showrecords($result3,"TOMORROW");

showrecords($result4,"FUTURE");

echo '</table></td></tr></table>';

?>

<script language="JavaScript">

<!--

tigra_tables('list', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

// -->

            </script>



</body>



</html>