<?php

require_once('JSON.php');
require_once('startdb.php');
require_once('lotinforecords.php');
require_once('staff.php');

function sameday($date1,$date2)
{
	if (date("m-d-Y",$date1)==date("m-d-Y",$date2))
	return 1;
	else
	return 0;
}

if (isstaff()!="YES")
$limit=" and CLIENTCODE=\"".strtoupper($_GET['clientcode']).'"';

$limit1=' and DATE(wo.DUEDATE)<DATE(NOW())';
$limit2=' and DATE(wo.DUEDATE)=DATE(NOW())';
$limit3=' and DATE(wo.DUEDATE)=DATE(ADDDATE(NOW(),1))';
$limit4=' and DATE(wo.DUEDATE)>DATE(ADDDATE(NOW(),1))';

function defaultquery()
{
	return 'SELECT `wo`.`LOT`, wo.TYPE,`wo`.`ID`, wo.DUEDATE, `labtest`.`LAB`, wo.OTHERDESC, wo.WORKPERFORMEDBY, `labresults`.`LABTEST`, labtest.LAB FROM `wo`
           LEFT OUTER JOIN `labtest` ON (`wo`.`ID` = `labtest`.`WOID`)
           LEFT OUTER JOIN `labresults` ON (`labtest`.`ID` = `labresults`.`LABTESTID`) where (wo.STATUS!="TEMPLATE" and wo.TYPE!="BRIXTEMP" and wo.TYPE!="STIR" and wo.TYPE!="PUMP OVER" and wo.TYPE!="SCP" and wo.TYPE!="DRYICE" and wo.TYPE!="PUNCH DOWN") AND wo.STATUS!="COMPLETED"';
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
$result1=mysql_query($query1);
$result2=mysql_query($query2);
$result3=mysql_query($query3);
$result4=mysql_query($query4);

function showrecords($result,$value)
{
	$numrecords=mysql_num_rows($result);
	if ($numrecords>0)
	{
		$theargs="";
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
		   $row=mysql_fetch_array($result);
		   if ($i==0)
		      $theargs=$theargs.'?';
		   else
		      $theargs=$theargs."&";
		   $theargs=$theargs.$i.'='.$row['ID'];
		}
		mysql_data_seek($result,0);
	}
	for ($i=0; $i <mysql_num_rows($result); $i++)
	{
		$row=mysql_fetch_array($result);
		switch ($row['TYPE'])
		{
			case "PRESSOFF" :
			{
				$assets=getlist($row['ID'],2);
				for ($j=0;$j<count($assets);$j++)
				{
					$text= '<br>'.$assets[$j]['name'].'-'.$assets[$j]['timeslot'];
				}
				break;
			}
			case 'PUMP OVER' :
			{
				$text=     $row['TIMESLOT'].' '.$row['VESSELTYPE'].'-'.$row['VESSELID'].' FOR '.$row['DURATION'].' MINUTES';
				break;
			}
			case 'PUNCH DOWN' :
			{
				$text=    $row['TIMESLOT'].' '.$row['VESSELTYPE'].'-'.$row['VESSELID'].' '.$row['STRENGTH'];
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
				break;
			}
			default :
		}
		$item[]=$text;
	}
	return $item;
}


$record['overdue']=showrecords($result1,"OVERDUE");

$record['overdue']=showrecords($result2,"TODAY");

$record['overdue']=showrecords($result3,"TOMORROW");

$record['overdue']=showrecords($result4,"FUTURE");

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
