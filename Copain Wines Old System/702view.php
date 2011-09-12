<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

  <style type="text/css">@import url(../jscalendar/calendar-win2k-1.css);</style>

  <script type="text/javascript" src="../jscalendar/calendar.js"></script>

  <script type="text/javascript" src="../jscalendar/lang/calendar-en.js"></script>

  <script type="text/javascript" src="../jscalendar/calendar-setup.js"></script>

  <script language="JavaScript" src="../tigra_tables/tigra_tables.js"></srcript>

include ("startdb.php");

  include ("yesno.php");

  include ("setcheck.php");

  include ("defaultvalue.php");

  include ("manageadditions.php");

  <script language="JavaScript1.2">

  function showval()

  {

  	alert(document.dates.startdate.value);

  	parent.maincontent.location.href='702view.php?startdate='+document.dates.startdate.value+'&enddate='+document.dates.enddate.value;

  }

</script>

  



<?php



//$query='SELECT * from lots inner join clients on (lots.CLIENTCODE=clients.CLIENTID) where ((lots.YEAR="'.$_SESSION['vintage'].'") AND

//     (clients.CODE="'.$_SESSION['clientcode'].'"))';

//$result=mysql_query($query);





?>

<body onClick="stopIt()">



<body>



<?php

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");

include ("totalgallons.php");

include ("lotinforecords.php");





 // $_SESSION['702clientcode']=$_SESSION['clientcode'];

  

//  if (isset($_GET['lot'])) $_SESSION['702lot']=$_GET['lot'];

//  if (isset($_POST['startdate'])) $_SESSION['702startdate']=$_POST['startdate'];

//  if (isset($_POST['enddate'])) $_SESSION['702enddate']=$_POST['enddate'];

//  if (isset($_GET['ccode'])) $_SESSION['702clientcode']=$_GET['ccode'];

 // if ($_SESSION['702clientcode']=="") $_SESSION['702clientcode']=$_SESSION['clientcode'];

 

 $_SESSION['702startdate']=mktime(0,0,0,$_GET['month'],1,$_GET['year']);

 $_SESSION['702enddate']=mktime(0,0,0,$_GET['month']+1,1,$_GET['year']);

 echo "<C>PERIOD: ".date("m-d-Y",$_SESSION['702startdate']).'  -  ';

 echo date("m-d-Y",$_SESSION['702enddate']).'</C>';

 //exit;

 function alclevel($labtestid)

 {

 	$q='SELECT labtest.WOID, labresults.VALUE1 FROM  `labresults`  INNER JOIN `labtest` ON (`labresults`.`LABTESTID` = `labtest`.`ID`) WHERE

                          ((labresults.LABTEST="ALCOHOL") AND (labtest.WOID="'.$labtestid.'"))';

					$r=mysql_query($q);

					if (mysql_num_rows($r)>0)

					{

						$therow=mysql_fetch_array($r);

						if ($therow['VALUE1']>=14)

						return "ABOVE";

						else

						return "BELOW";

					}

					return "NONE";

 }

function updatedata($data,$thelot,$startdate,$enddate)

{



	$record=lotinforecords($thelot);

//	debug ($record);

	$lr=count($record)-1;

	$foundstart=0;

	for ($i=0;$i<count($record);$i++)

	{

		if (($record[$i]['date']>=$startdate)&($foundstart==0))

		{

			$foundstart=1;

			$data[$record[$i]['start_state']]['startgallons']+=$record[$i]['starting_tankgallons']+($record[$i]['starting_bbls']*60)+$record[$i]['starting_toppinggallons'];

		}

		if ($record[$i]['date']>=$enddate)

		{

			break;

		}

		if ($foundstart==1)

		{

			switch ($record[$i]['type'])

			{

				case 'WO' :

				{

					if ($record[$i]['data']['TYPE']!="SCP")

					{

						$data[$record[$i]['start_state']]['mods'][$record[$i]['data']['TYPE']]['val']+=$record[$i]['difference'];

					}

//					if ($record[$i]['data']['TYPE']=="LAB TEST")

//					{

//						$al=alclevel($record[$i]['data']['ID']);

//						echo $al.'<br>';

//						debug($record[$i]);

//						exit;

//					}

					if ($record[$i]['data']['TYPE']=="BOTTLING")

					{

	//					$data[$record[$i]['start_state']]['mods'][$record[$i]['data']['TYPE']]['val']+=$record[$i]['difference'];

					}

					$data[$record[$i]['start_state']]['mods'][$record[$i]['data']['TYPE']]['participant'][]=array(type=>"WO",id=>$record[$i]['data']['ID'],val=>$record[$i]['difference']);

					break;

				}

				case 'BOL' :

				{

					//print_r($record[$i]);

					if ($record[$i]['data']['DIRECTION']=="IN")

					{

						$data[$record[$i]['start_state']]['mods']['BOLIN']['val']+=$record[$i]['difference'];

						$data[$record[$i]['start_state']]['mods']['BOLIN']['participant'][]=array(type=>"BOL",id=>$record[$i]['data']['ID'],val=>$record[$i]['difference']);

					}

					else

					{

						if ($record[$i]['data']['BONDED']=="BONDTOBOND")

						{

						$data[$record[$i]['start_state']]['mods']['BOLOUT_BONDTOBOND']['val']+=$record[$i]['difference'];

						$data[$record[$i]['start_state']]['mods']['BOLOUT_BONDTOBOND']['participant'][]=array(type=>"BOL",id=>$record[$i]['data']['ID'],val=>$record[$i]['difference']);

						}

						else 

						{

						$data[$record[$i]['start_state']]['mods']['BOLOUT_TAXPAID']['val']+=$record[$i]['difference'];

						$data[$record[$i]['start_state']]['mods']['BOLOUT_TAXPAID']['participant'][]=array(type=>"BOL",id=>$record[$i]['data']['ID'],val=>$record[$i]['difference']);

						}

					}

					break;

				}

				case 'WT' :

				{

					//print_r($record[$i]);

					$data[$record[$i]['start_state']]['mods']['WT']['val']+=$record[$i]['difference'];

					$data[$record[$i]['start_state']]['mods']['WT']['participant'][]=array(type=>"WT",id=>($record[$i]['data']['TAGID']+5000),val=>$record[$i]['difference']);

					break;

				}

				case 'BLEND' :

				{

					//debug($record[$i]);

					$data[$record[$i]['start_state']]['mods']['BLENDING']['val']+=$record[$i]['difference'];

					$data[$record[$i]['start_state']]['mods']['BLENDING']['participant'][]=array(type=>"BLEND",id=>$record[$i]['data']['WOID'],val=>$record[$i]['difference']);

					//debug($data);

					break;

				}

			}

			//record state change

			$data[$record[$i]['end_state']][$record[$i]['end_state']]+=$record[$i]['ending_tankgallons']+($record[$i]['ending_bbls']*60)+$record[$i]['ending_toppinggallons'];

			$data[$record[$i]['start_state']][$record[$i]['end_state']]-=$record[$i]['ending_tankgallons']+($record[$i]['ending_bbls']*60)+$record[$i]['ending_toppinggallons'];

//			if (($record[$i]['end_state']=="WINE_ABOVE" & $record[$i]['start_state']=="WINE_BELOW") |

//			 ($record[$i]['end_state']=="WINE_BELOW" & $record[$i]['start_state']=="WINE_ABOVE")) 

//			{

//			  debug($record[$i]);

//			  exit;

//			}

//			if ($data['WINE_ABOVE']['WINE_ABOVE']>0)

//			  debug($record);

		}

	}

	//debug($record);

	if ($foundstart==0)

	{

		$data[$record[$lr]['end_state']]['startgallons']+=$record[$lr]['ending_tankgallons']+($record[$lr]['ending_bbls']*60)+$record[$lr]['ending_toppinggallons'];

	}

	foreach ($data as $key=>$value)

	{

		$sum=0;

		if (count($value['mods'])>0)

		{

			foreach ($value['mods'] as $key2=>$value2)

			{

				$sum+=$value2['val'];

			}

		}

		$sum+=$value['JUICE']+$value['WINE_BELOW']+$value['WINE_ABOVE']+$value['BOTTLED_ABOVE_INBOND']+$value['BOTTLED_BELOW_INBOND']+$value['BOTTLED_ABOVE_TAXPAID']+$value['BOTTLED_BELOW_TAXPAID'];

		$data[$key]['endgallons']=$data[$key]['startgallons']+$sum;

	}

	return $data;

}



function showsection($data,$section,$theclientid=0)

{

//		debug($data);

//		exit;

	echo '<tr><td align=center colspan=3><b><big>'.$section.'</b></big></tr>';

	echo '<tr><td></td><td width=200 align=right>START GALLONS:</td><td width=100 align=right>'.number_format($data[$section]['startgallons'],3).'</td></tr>';

	if (count($data[$section]['mods'])>0)

	{

		foreach ($data[$section]['mods'] as $key => $value)

		{

			if ($theclientid>0)

			echo '<tr><td></td><td width=200 align=right>'.$key.'</td><td width=100 align=right><a href=702viewdetail.php?clientid='.$theclientid.'&section='.$section.'&mod='.ereg_replace(' ','%',$key).'>'.number_format($value['val'],3).'</a></td></tr>';			

			else

			echo '<tr><td></td><td width=200 align=right>'.$key.'</td><td width=100 align=right><a href=702viewdetail.php?section='.$section.'&mod='.ereg_replace(' ','%',$key).'>'.number_format($value['val'],3).'</a></td></tr>';			

		}

	}

	switch ($section)

	{

		case "JUICE":

		{

			echo '<tr><td></td><td width=200 align=right>DECLARED WINE <14% (OUT)</td><td width=100 align=right>'.number_format($data[$section]['WINE_BELOW'],3).'</td></tr>';

			echo '<tr><td></td><td width=200 align=right>DECLARED WINE >=14% (OUT)</td><td width=100 align=right>'.number_format($data[$section]['WINE_ABOVE'],3).'</td></tr>';

			break;

		}

		case "WINE_ABOVE":

		{

			echo '<tr><td></td><td width=200 align=right>RETEST WINE BELOW >=14% (OUT)</td><td width=100 align=right>'.number_format($data[$section]['WINE_BELOW'],3).'</td></tr>';

			echo '<tr><td></td><td width=200 align=right>DECLARED WINE >=14% (IN)</td><td width=100 align=right>'.number_format($data[$section]['WINE_ABOVE'],3).'</td></tr>';

			echo '<tr><td></td><td width=200 align=right>BOTTLED IN BOND >=14% (OUT)</td><td width=100 align=right>'.number_format($data[$section]['BOTTLED_ABOVE_INBOND'],3).'</td></tr>';

			break;

		}

		case "WINE_BELOW":

		{

			echo '<tr><td></td><td width=200 align=right>RETEST WINE ABOVE >=14% (OUT)</td><td width=100 align=right>'.number_format($data[$section]['WINE_ABOVE'],3).'</td></tr>';

			echo '<tr><td></td><td width=200 align=right>DECLARED WINE <14% (IN)</td><td width=100 align=right>'.number_format($data[$section]['WINE_BELOW'],3).'</td></tr>';

			echo '<tr><td></td><td width=200 align=right>BOTTLED IN BOND <14% (OUT)</td><td width=100 align=right>'.number_format($data[$section]['BOTTLED_BELOW_INBOND'],3).'</td></tr>';

			break;

		}

		case "BOTTLED_ABOVE_INBOND":

		{

			echo '<tr><td></td><td width=200 align=right>BOTTLED (IN)</td><td width=100 align=right>'.number_format($data[$section]['BOTTLED_ABOVE_INBOND'],3).'</td></tr>';

			break;

		}

		case "BOTTLED_BELOW_INBOND":

		{

			echo '<tr><td></td><td width=200 align=right>BOTTLED (IN)</td><td width=100 align=right>'.number_format($data[$section]['BOTTLED_BELOW_INBOND'],3).'</td></tr>';

			break;

		}

	}

	echo '<tr><td></td><td align=right>ENDING GALLONS:</td><td align=right>'.number_format($data[$section]['endgallons'],3).'</td></tr>';

}

	



set_time_limit(120);

if (isset($_GET['clientid']))

{

	$query="select * from clients WHERE clients.AP='NO' AND clients.ACTIVE='YES' AND clients.clientid='".$_GET['clientid']."'";

}

else

{

	$query="select * from clients WHERE (clients.AP='NO' AND clients.ACTIVE='YES')";

}

//echo $query;

$kresult=mysql_query($query);



for ($k=0;$k<mysql_num_rows($kresult);$k++)

{

	$row=mysql_fetch_array($kresult);

	$theclient=$row['clientid'];

	

if ($_SESSION['702lot']!="")

{

	$query='SELECT * from lots WHERE lots.LOTNUMBER="'.$_SESSION['702lot'].'"';

}

else

{

//	if (isset($_GET['clientid']))

//	{

		$query='SELECT * from lots WHERE lots.CLIENTCODE="'.$theclient.'"';

//	}

//	else

//	{

//		$query='SELECT * from lots WHERE lots.CLIENTCODE="'.clientid($_SESSION['clientcode']).'"';

//		$query='SELECT *  lots WHERE lots.CLIENTCODE="'.$theclient.'"';

//		$query='SELECT * from lots INNER JOIN clients ON (lots.CLIENTCODE=clients.clientid) WHERE clients.AP="NO"';

//	}

}



//echo $query;

$result=mysql_query($query);





for ($j=0;$j<mysql_num_rows($result);$j++)

{

	$row=mysql_fetch_array($result);

//	echo $row['LOTNUMBER'].'<BR>';

	$data=updatedata($data,$row['LOTNUMBER'],$_SESSION['702startdate'],$_SESSION['702enddate']);

	$totdata=updatedata($totdata,$row['LOTNUMBER'],$_SESSION['702startdate'],$_SESSION['702enddate']);

}

$databyclient[$theclient]=$data;

//debug($data);

//exit;

unset($data);



//debug($data);

echo '<table border=1 width=400 align=center><tr><td align=center>';

echo clientname(clientcode($theclient)).' ('.$theclient.') '.$_SESSION['702lot'].'</td></tr></table>';





//debug ($data);

echo '<table border=1 width=700 align=center>';

echo '<tr>';

echo '<td align=center width=50%>';

echo '<table width=100% align=center>';

showsection($databyclient[$theclient],"JUICE",$theclient);

echo '</table>';

echo '</td>';

echo '</tr>';

echo '<tr>';

echo '<td valign=top align=center width=50%>';

echo '<table width=100% align=center>';

showsection($databyclient[$theclient],"WINE_BELOW",$theclient);

echo '</table>';

echo '</td>';

echo '<td valign=top align=center width=50%>';

echo '<table width=100% align=center>';

showsection($databyclient[$theclient],"WINE_ABOVE",$theclient);

echo '</table>';

echo '</td>';

echo '</tr>';



echo '<tr>';

echo '<td valign=top align=center width=50%>';

echo '<table width=100% align=center>';

showsection($databyclient[$theclient],"BOTTLED_BELOW_INBOND",$theclient);

echo '</table>';

echo '</td>';

echo '<td valign=top align=center width=50%>';

echo '<table width=100% align=center>';

showsection($databyclient[$theclient],"BOTTLED_ABOVE_INBOND",$theclient);

echo '</table>';

echo '</td>';

echo '</tr>';



echo '<tr>';

echo '<td valign=top align=center width=50%>';

echo '<table width=100% align=center>';

echo '<tr><td align=right>STATE TAX DUE:</td><td align=right>'.number_format(-$databyclient[$theclient]['BOTTLED_BELOW_INBOND']['mods']['BOLOUT_TAXPAID']['val']*.2,2).'</td></tr>';

echo '<tr><td align=right>FEDERAL TAX:</td><td align=right>'.number_format(-$databyclient[$theclient]['BOTTLED_BELOW_INBOND']['mods']['BOLOUT_TAXPAID']['val']*1.17,2).'</td></tr>';

echo '<tr><td align=right>SMALL PRODUCER TAX CREDIT:</td><td align=right>'.number_format($databyclient[$theclient]['BOTTLED_BELOW_INBOND']['mods']['BOLOUT_TAXPAID']['val']*0.9,2).'</td></tr>';

echo '<tr><td align=right>TOTAL FEDERAL TAX:</td><td align=right>'.number_format(-$databyclient[$theclient]['BOTTLED_BELOW_INBOND']['mods']['BOLOUT_TAXPAID']['val']*0.17,2).'</td></tr>';

echo '<tr><td align=right>TOTAL TAX:</td><td align=right>'.number_format(-$databyclient[$theclient]['BOTTLED_BELOW_INBOND']['mods']['BOLOUT_TAXPAID']['val']*0.37,2).'</td></tr>';

echo '</table>';

echo '</td>';

echo '<td valign=top align=center width=50%>';

echo '<table width=100% align=center>';

echo '<tr><td align=right>STATE TAX DUE:</td><td align=right>'.number_format(-$databyclient[$theclient]['BOTTLED_ABOVE_INBOND']['mods']['BOLOUT_TAXPAID']['val']*.2,2).'</td></tr>';

echo '<tr><td align=right>FEDERAL TAX:</td><td align=right>'.number_format(-$databyclient[$theclient]['BOTTLED_ABOVE_INBOND']['mods']['BOLOUT_TAXPAID']['val']*1.57,2).'</td></tr>';

echo '<tr><td align=right>SMALL PRODUCER TAX CREDIT:</td><td align=right>'.number_format($databyclient[$theclient]['BOTTLED_ABOVE_INBOND']['mods']['BOLOUT_TAXPAID']['val']*0.9,2).'</td></tr>';

echo '<tr><td align=right>TOTAL FEDERAL TAX:</td><td align=right>'.number_format(-$databyclient[$theclient]['BOTTLED_ABOVE_INBOND']['mods']['BOLOUT_TAXPAID']['val']*0.67,2).'</td></tr>';

echo '<tr><td align=right>TOTAL TAX:</td><td align=right>'.number_format(-$databyclient[$theclient]['BOTTLED_ABOVE_INBOND']['mods']['BOLOUT_TAXPAID']['val']*0.87,2).'</td></tr>';

echo '<tr><td align=right>GRAND TOTAL TAX:</td><td align=right>'.

   number_format(-(($databyclient[$theclient]['BOTTLED_BELOW_INBOND']['mods']['BOLOUT_TAXPAID']['val'])*.37+

                   ($databyclient[$theclient]['BOTTLED_ABOVE_INBOND']['mods']['BOLOUT_TAXPAID']['val'])*.87),2).'</td></tr>';

echo '</table>';

echo '</td>';

echo '</tr>';



echo '</table>';

//debug ($data);

$_SESSION['data702']=$data;

flush();

}

echo '<table border=1 width=400 align=center><tr><td align=center>';

echo 'SUMMARY OF ALL CLIENTS</td></tr></table>';





//debug ($data);

echo '<table border=1 width=700 align=center>';

echo '<tr>';

echo '<td align=center width=50%>';

echo '<table width=100% align=center>';

showsection($totdata,"JUICE");

echo '</table>';

echo '</td>';

echo '</tr>';

echo '<tr>';

echo '<td valign=top align=center width=50%>';

echo '<table width=100% align=center>';

showsection($totdata,"WINE_BELOW");

echo '</table>';

echo '</td>';

echo '<td valign=top align=center width=50%>';

echo '<table width=100% align=center>';

showsection($totdata,"WINE_ABOVE");

echo '</table>';

echo '</td>';

echo '</tr>';



echo '<tr>';

echo '<td valign=top align=center width=50%>';

echo '<table width=100% align=center>';

showsection($totdata,"BOTTLED_BELOW_INBOND");

echo '</table>';

echo '</td>';

echo '<td valign=top align=center width=50%>';

echo '<table width=100% align=center>';

showsection($totdata,"BOTTLED_ABOVE_INBOND");

echo '</table>';

echo '</td>';

echo '</tr>';



echo '<tr>';

echo '<td valign=top align=center width=50%>';

echo '<table width=100% align=center>';

echo '<tr><td align=right>STATE TAX DUE:</td><td align=right>'.number_format(-$totdata['BOTTLED_BELOW_INBOND']['mods']['BOLOUT_TAXPAID']['val']*.2,2).'</td></tr>';

echo '<tr><td align=right>FEDERAL TAX:</td><td align=right>'.number_format(-$totdata['BOTTLED_BELOW_INBOND']['mods']['BOLOUT_TAXPAID']['val']*1.17,2).'</td></tr>';

echo '<tr><td align=right>SMALL PRODUCER TAX CREDIT:</td><td align=right>'.number_format($totdata['BOTTLED_BELOW_INBOND']['mods']['BOLOUT_TAXPAID']['val']*0.9,2).'</td></tr>';

echo '<tr><td align=right>TOTAL FEDERAL TAX:</td><td align=right>'.number_format(-$totdata['BOTTLED_BELOW_INBOND']['mods']['BOLOUT_TAXPAID']['val']*0.17,2).'</td></tr>';

echo '<tr><td align=right>TOTAL TAX:</td><td align=right>'.number_format(-$totdata['BOTTLED_BELOW_INBOND']['mods']['BOLOUT_TAXPAID']['val']*0.37,2).'</td></tr>';

echo '</table>';

echo '</td>';

echo '<td valign=top align=center width=50%>';

echo '<table width=100% align=center>';

echo '<tr><td align=right>STATE TAX DUE:</td><td align=right>'.number_format(-$totdata['BOTTLED_ABOVE_INBOND']['mods']['BOLOUT_TAXPAID']['val']*.2,2).'</td></tr>';

echo '<tr><td align=right>FEDERAL TAX:</td><td align=right>'.number_format(-$totdata['BOTTLED_ABOVE_INBOND']['mods']['BOLOUT_TAXPAID']['val']*1.57,2).'</td></tr>';

echo '<tr><td align=right>SMALL PRODUCER TAX CREDIT:</td><td align=right>'.number_format($totdata['BOTTLED_ABOVE_INBOND']['mods']['BOLOUT_TAXPAID']['val']*0.9,2).'</td></tr>';

echo '<tr><td align=right>TOTAL FEDERAL TAX:</td><td align=right>'.number_format(-$totdata['BOTTLED_ABOVE_INBOND']['mods']['BOLOUT_TAXPAID']['val']*0.67,2).'</td></tr>';

echo '<tr><td align=right>TOTAL TAX:</td><td align=right>'.number_format(-$totdata['BOTTLED_ABOVE_INBOND']['mods']['BOLOUT_TAXPAID']['val']*0.87,2).'</td></tr>';

echo '<tr><td align=right>GRAND TOTAL TAX:</td><td align=right>'.

   number_format(-(($totdata['BOTTLED_BELOW_INBOND']['mods']['BOLOUT_TAXPAID']['val'])*.37+

                   ($totdata['BOTTLED_ABOVE_INBOND']['mods']['BOLOUT_TAXPAID']['val'])*.87),2).'</td></tr>';

echo '</table>';

echo '</td>';

echo '</tr>';



echo '</table>';

$_SESSION['data702']=$totdata;

$_SESSION['data702byclient']=$databyclient;



?>

</body>



</html>

