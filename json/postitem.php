<?php
require_once('JSON.php');
require_once('../server/startdb.php');
require_once('lotinforecords.php');
require_once('staff.php');

//sleep(1);

	$json = new Services_JSON();
//	$record=$_REQUEST;

// function getTaskData($taskid)
// {
// 	$query='select * from tasks where tasks.id="'.$taskid.'"';
// 	$result=mysql_query($query);
// 	$row=mysql_fetch_assoc($result);
// 	return $row;
// }


if ($_REQUEST['action']=='sendpush')
{
	$devtokens[]="b92792d16bc3cb231ecf6a9b623e985e45fda964de52338c7ed414a073fc21c6";
	$message="Push Working From Copain Custom Crush";
	$badgeCount=100;
	$sound="default";
	sendPush($devtokens,$message,$badgeCount,$sound);
	echo "push sent!";
	exit;
}

function hasreferences($db)
{
	if ($db=="wo")
		$query='SELECT count(LOT)as thecount from '.$db.' WHERE DELETED!=1 and LOT="'.$_REQUEST['lotid'].'"';
	else
		$query='SELECT count(LOT)as thecount from '.$db.' WHERE LOT="'.$_REQUEST['lotid'].'"';
	
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	if ($row['thecount']>0)
		return true;
	return false;
}

if ($_REQUEST['action']=="update_wo_blending")
{
	$record['inputs']=$_REQUEST;
	$data=(array)$json->decode(str_replace('\\','',$_REQUEST['data']),false);
	// print_r($data);
	// exit;
	if ($_REQUEST['woid']=="NEW")
	{
		$query='insert into wo set TYPE="BLENDING",
			DUEDATE="'.date("Y-m-d",strtotime($_REQUEST['date'])).'",
			ENDDATE="'.date("Y-m-d",strtotime($_REQUEST['date'])).'",
			OTHERDESC="",
			CLIENTCODE="'.$_REQUEST['clientcode'].'",
			LOT="'.$data['receivingLot'].'"';
		$result=mysql_query($query);
		$woid=mysql_insert_id();
		
		$query='insert into blend set WOID="'.$woid.'"';
		$result=mysql_query($query);
		$blendid=mysql_insert_id();
		foreach ($data['inputLots'] as $key=>$value)
		{
			$query='insert into blenditems set BLENDID="'.$blendid.'",
				SOURCELOT="'.$key.'",
				GALLONS="'.$value.'",
				DIRECTION="IN FROM"';
			$result=mysql_query($query);
		}		
	}
	else
	{
		$woid=$_REQUEST['woid'];
		$query='update wo set TYPE="BLENDING",
			DUEDATE="'.date("Y-m-d",strtotime($_REQUEST['date'])).'",
			ENDDATE="'.date("Y-m-d",strtotime($_REQUEST['date'])).'",
			CLIENTCODE="'.$_REQUEST['clientcode'].'",
			LOT="'.$data['receivingLot'].'" where ID="'.$woid.'" limit 1';
		$result=mysql_query($query);

		$query='select * from blend where WOID="'.$_REQUEST['woid'].'"';
		$result=mysql_query($query);
		if (mysql_num_rows($result)==1)
		{
			$row=mysql_fetch_assoc($result);
			$blendid=$row['ID'];
		}
		else
		{
			$query='insert into blend set WOID="'.$woid.'"';
			$result=mysql_query($query);
			$blendid=mysql_insert_id();
		}
		$query='delete from blenditems where BLENDID="'.$blendid.'"';
		$result=mysql_query($query);
		foreach ($data['inputLots'] as $key=>$value)
		{
			$query='insert into blenditems set BLENDID="'.$blendid.'",
				SOURCELOT="'.$key.'",
				GALLONS="'.$value.'",
				DIRECTION="IN FROM"';
			$result=mysql_query($query);
		}		
		
	}
	$record['results']['woid']=$woid;
	$record['results']['inputs']=$_REQUEST;
}
if ($_REQUEST['action']=='update_pullsample_wo')
{
	$record['inputs']=$_REQUEST;
	if ($_REQUEST['woid']=="NEW")
	{
		$query='insert into wo set DUEDATE="'.$_REQUEST['startdate'].'",
				ENDDATE="'.$_REQUEST['enddate'].'",
				LOT="'.strtoupper($_REQUEST['lot']).'",
				TYPE="PULL SAMPLE",
				OTHERDESC="",
				REQUESTOR="'.$_REQUEST['username'].'",
				TASKID="'.$_REQUEST['taskid'].'",
				CLIENTCODE="'.$_REQUEST['clientcode'].'"';
		$result=mysql_query($query);
		$newwoid=mysql_insert_id();
	}
	else
	{
		$query='update wo set DUEDATE="'.$_REQUEST['startdate'].'",
				ENDDATE="'.$_REQUEST['enddate'].'",
				LOT="'.strtoupper($_REQUEST['lot']).'",
				TYPE="PULL SAMPLE",
				OTHERDESC="",
				REQUESTOR="'.$_REQUEST['username'].'",
				TASKID="'.$_REQUEST['taskid'].'",
				CLIENTCODE="'.$_REQUEST['clientcode'].'" where ID="'.$_REQUEST['woid'].'"';
		$result=mysql_query($query);
	}	
	$query='select * from wo where ID="'.$newwoid.'"';
	$result=mysql_query($query);
	$record['results']['wo']['data']=mysql_fetch_assoc($result);
}
if ($_REQUEST['action']=="deletelot")
{
	$result=0;
	if (!hasreferences('wt'))
	{
		if (!hasreferences('wo'))
		{
			$query='DELETE from lots WHERE LOTNUMBER="'.$_REQUEST['lotid'].'" limit 1';
			mysql_query($query);
			$result=1;
		}
	}
	$record[]=$result;
}

	if ($_REQUEST['action']=='update_wo_description')
	{
		$query='update wo set
			OTHERDESC="'.strtoupper($_REQUEST['new_value']).'" where ID="'.$_REQUEST['id'].'" limit 1';
		mysql_query($query);
		$result=mysql_query($query);
		$output['is_error']=false;
		$output['error_text']="";
		$output['html']=strtoupper($_REQUEST['new_value']);
		print $json->encode($output);
		exit;
	}
	
	if ($_REQUEST['action']=='update_topping_wo')
	{
//		$clientinfo=getClientIDAndNameFromClientcode($_REQUEST['clientcode']);
		$query='select startdate,enddate from tasks where id="'.$_REQUEST['taskid'].'"';
		$r=mysql_query($query);
		$row=mysql_fetch_assoc($r);
		$sqlStartDate=$row['startdate'];
		$sqlEndDate=$row['enddate'];
		$topWithLot=getLastToppingLotFromLot($_REQUEST['lot']);
		
		if ($_REQUEST['woid']=="")
		{
			$query='insert into wo set TYPE="'.$_REQUEST['type'].'",
				LOT="'.strtoupper($_REQUEST['lot']).'",
				REQUESTOR="'.$_REQUEST['username'].'",
				DUEDATE="'.$sqlStartDate.'",
				ENDDATE="'.$sqlEndDate.'",
				OTHERDESC="'.strtoupper($topWithLot['OTHERDESC']).'",
				TASKID="'.$_REQUEST['taskid'].'",
				SO2ADD="'.strtoupper($_REQUEST['so2Add']).'",
				TOPPINGLOT="'.strtoupper($topWithLot['TOPPINGLOT']).'",
				CLIENTCODE="'.$_REQUEST['clientcode'].'"';
			mysql_query($query);
			$result['query']=$query;
			$result['woid']=mysql_insert_id();
			$result['lotinfo']=lotinforecords($_REQUEST['lot']);
			// echo '<pre>';
			// print_r($result);
		}
		else
		{
			$query='update wo set TYPE="'.$_REQUEST['type'].'",
				LOT="'.strtoupper($_REQUEST['lot']).'",
				TYPE="TOPPING",
				REQUESTOR="'.$_REQUEST['username'].'"
				DUEDATE="'.$sqlStartDate.'",
				ENDDATE="'.$sqlEndDate.'",
				OTHERDESC="'.strtoupper($_REQUEST['notes']).'",
				TASKID="'.$_REQUEST['taskid'].'",
				SO2ADD="'.strtoupper($_REQUEST['so2Add']).'",
				TOPPINGLOT="'.strtoupper($_REQUEST['topWithLot']).'",
				CLIENTCODE="'.$_REQUEST['clientcode'].'" where ID="'.$_REQUEST['woid'].'"';
			mysql_query($query);
			$result['query']=$query;			
		}
		$result['lot']=$_REQUEST['lot'];
		$result['lotinfo']=lotinforecords($_REQUEST['lot']);
		$result['notes']=strtoupper($_REQUEST['notes']);
		$result['so2Add']=strtoupper($_REQUEST['so2Add']);
		$output=$json->encode($result);
		print $output;
		exit;
	}
	if ($_REQUEST['action']=='update_task')
	{
		$sqlStartDate=date('y-m-d',strtotime($_REQUEST['startDate']));
		$sqlEndDate=date('y-m-d',strtotime($_REQUEST['endDate']));
		
		if ($_REQUEST['taskid']=="" | $_REQUEST['taskid']=="null")
		{
			$query='insert into tasks set type="'.$_REQUEST['type'].'",
				startdate="'.$sqlStartDate.'",
				enddate="'.$sqlEndDate.'",
				description="'.strtoupper($_REQUEST['description']).'",
				workperformedby="'.$_REQUEST['workperformedby'].'",
				clientid="'.$_REQUEST['clientid'].'"';
			mysql_query($query);
			$result['query']=$query;
			$result['taskid']=mysql_insert_id();
			$result['description']=strtoupper($_REQUEST['description']);			
		}
		else
		{
			$query='update tasks set type="'.$_REQUEST['type'].'",
				startdate="'.$sqlStartDate.'",
				enddate="'.$sqlEndDate.'",
				description="'.strtoupper($_REQUEST['description']).'",
				workperformedby="'.$_REQUEST['workperformedby'].'",
				clientid="'.$_REQUEST['clientid'].'" where id="'.$_REQUEST['taskid'].'"';
			mysql_query($query);
			$result['query']=$query;
			$result['taskid']=$_REQUEST['taskid'];
			$result['description']=strtoupper($_REQUEST['description']);						
		}
		$output=$json->encode($result);
		print $output;
		exit;
	}
	
	if ($_REQUEST['action']=='update_devtoken')
	{
		$devToken=ereg_replace("[^A-Za-z0-9]","",$_REQUEST['DEVTOKEN']);
		
		$query='select devToken from devTokens where devToken="'.$devToken.'"';
		$result=mysql_query($query);
		if (mysql_num_rows($result)==0)
		{
			$query='insert into devTokens set devToken="'.$devToken.'", UDID="'.$_REQUEST['UDID'].'"';
			$result=mysql_query($query);
		}
	}	
	
	if ($_REQUEST['action']=='clear_newwos')
	{
		$query='delete from newwos where devtoken="'.$_REQUEST['DEVTOKEN'].'"';
		$result=mysql_query($query);
		$record['query']=$query;
	}
	
	if ($_REQUEST['action']=='add_ferm_wo')
	{
		$vessel=explode('-',$_REQUEST['VESSEL']);
		$cc=explode('-',$_REQUEST['LOT']);
		$thedate=explode(' ',$_REQUEST['DATE']);
	
		$query='select ID from wo where DATE(DUEDATE)="'.$thedate[0].'" and 
				LOT="'.$_REQUEST['LOT'].'" and
				VESSELTYPE="'.$vessel[0].'" and
				VESSELID="'.$vessel[1].'" and
				TIMESLOT="'.$_REQUEST['TIMESLOT'].'"';
		$result=mysql_query($query);
		if (mysql_num_rows($result)>0)
		{
			$row=mysql_fetch_assoc($result);
			$query='update wo set 
			DURATION="'.$_REQUEST['DURATION'].'",
			DRYICE="'.$_REQUEST['DRYICE'].'",
			TYPE="'.$_REQUEST['TYPE'].'",
			STRENGTH="'.$_REQUEST['STRENGTH'].'" WHERE ID="'.$row['ID'].'" limit 1';
		}
		else
		{
			$query='insert into wo set
				TYPE="'.$_REQUEST['TYPE'].'",
				DURATION="'.$_REQUEST['DURATION'].'",
				STRENGTH="'.$_REQUEST['STRENGTH'].'",
				VESSELID="'.$vessel[1].'",
				VESSELTYPE="'.$vessel[0].'",
				TIMESLOT="'.$_REQUEST['TIMESLOT'].'",
				DRYICE="'.$_REQUEST['DRYICE'].'",
				STATUS="'.$_REQUEST['STATUS'].'",
				LOT="'.$_REQUEST['LOT'].'",
				DUEDATE="'.$thedate[0].'",
				CLIENTCODE="'.$cc[1].'"';
		}
		$result=mysql_query($query);
		$record['query']=$query;
	}
	
	if ($_REQUEST['action']=='mark_all_lots_favorite')
	{
		$query='select LOTNUMBER from lots where CLIENTCODE="'.$_REQUEST['CLIENTCODE'].'" AND YEAR="'.$_REQUEST['VINTAGE'].'"';
		$result=mysql_query($query);
		for ($i=0; $i<mysql_num_rows($result); $i++)
		{
			$row=mysql_fetch_assoc($result);
			
			$query2='select * from lotfavorites where LOTNUMBER="'.$row['LOTNUMBER'].'" and CLIENTID="'.$_REQUEST['CLIENTCODE'].'"';
			$result2=mysql_query($query2);
			if (mysql_num_rows($result2)==0)
			{
				$query3='insert into lotfavorites set LOTNUMBER="'.$row['LOTNUMBER'].'",CLIENTID="'.$_REQUEST['CLIENTCODE'].'", FAVORITE="YES"';
				mysql_query($query3);				
			}
		}	
		$record['query1']=$query;	
		$record['query2']=$query2;	
		$record['query3']=$query3;	
	}
	if ($_REQUEST['action']=='clear_all_lots_favorite')
	{
		$query='select LOTNUMBER from lots where CLIENTCODE="'.$_REQUEST['CLIENTCODE'].'" AND YEAR="'.$_REQUEST['VINTAGE'].'"';
		$result=mysql_query($query);
		for ($i=0; $i<mysql_num_rows($result); $i++)
		{
			$row=mysql_fetch_assoc($result);
			
			$query2='select * from lotfavorites where LOTNUMBER="'.$row['LOTNUMBER'].'" and CLIENTID="'.$_REQUEST['CLIENTCODE'].'"';
			$result2=mysql_query($query2);
			if (mysql_num_rows($result2)>0)
			{
				$query3='delete from lotfavorites where LOTNUMBER="'.$row['LOTNUMBER'].'" and CLIENTID="'.$_REQUEST['CLIENTCODE'].'" and FAVORITE="YES"';
				mysql_query($query3);				
			}
		}	
		$record['query1']=$query;	
		$record['query2']=$query2;	
		$record['query3']=$query3;	
	}
	
	if ($_REQUEST['action']=='update_fermprotocol')
	{
		$vessel=explode('-',$_REQUEST['VESSELNAME']);
		$query='select * from fermprot where LOT="'.$_REQUEST['LOTNUMBER'].'" and VESSELTYPE="'.$vessel[0].'" and VESSELID="'.$vessel[1].'" limit 1';
		$result=mysql_query($query);
		if (mysql_num_rows($result)==0)
		{
			$query='insert into fermprot set STATUS="'.$_REQUEST['STATUS'].'",
			      PDAM="'.$_REQUEST['PDAM'].'",
			      PDNOON="'.$_REQUEST['PDNOON'].'",
			      PDPM="'.$_REQUEST['PDPM'].'",
			      POAM="'.$_REQUEST['POAM'].'",
			      PONOON="'.$_REQUEST['PONOON'].'",
			      POPM="'.$_REQUEST['POPM'].'",
			      DRYICE="YES",
			      STIR="'.$_REQUEST['STIR'].'",
				  LOT="'.$_REQUEST['LOTNUMBER'].'",
				  VESSELTYPE="'.$vessel[0].'",
				  VESSELID="'.$vessel[1].'"';
		}
		else
		{
			$query='update fermprot set STATUS="'.$_REQUEST['STATUS'].'",
			      PDAM="'.$_REQUEST['PDAM'].'",
			      PDNOON="'.$_REQUEST['PDNOON'].'",
			      PDPM="'.$_REQUEST['PDPM'].'",
			      POAM="'.$_REQUEST['POAM'].'",
			      PONOON="'.$_REQUEST['PONOON'].'",
			      POPM="'.$_REQUEST['POPM'].'",
			      DRYICE="'.$_REQUEST['DRYICE'].'",
			      STIR="'.$_REQUEST['STIR'].'"
				where LOT="'.$_REQUEST['LOTNUMBER'].'" and VESSELTYPE="'.$vessel[0].'" and VESSELID="'.$vessel[1].'" limit 1';
		}
		$result=mysql_query($query);
		$record['query']=$query;
	}

	if ($_REQUEST['action']=='update_brixtemp')
	{
		$vessel=explode('-',$_REQUEST['VESSEL']);
		$thedate=explode(' ',$_REQUEST['DATE']);
		$query='select * from brixtemp 
			where DATE="'.$thedate[0].'" AND LOT="'.$_REQUEST['LOT'].'" and vesseltype="'.$vessel[0].'" and vessel="'.$vessel[1].'" limit 1';
		$result=mysql_query($query);
		if (mysql_num_rows($result)>0)
		{
			$query='update brixtemp set BRIX="'.$_REQUEST['BRIX'].'",
			temp="'.$_REQUEST['TEMP'].'"
			where DATE="'.$thedate[0].'" AND lot="'.$_REQUEST['LOT'].'" and vesseltype="'.$vessel[0].'" and vessel="'.$vessel[1].'" limit 1';			
		}
		else
		{
			$query='insert into brixtemp set BRIX="'.$_REQUEST['BRIX'].'",
			temp="'.$_REQUEST['TEMP'].'",
		    DATE="'.$thedate[0].'",
			lot="'.$_REQUEST['LOT'].'",
			vesseltype="'.$vessel[0].'",
			vessel="'.$vessel[1].'"';			
			
		}
		$result=mysql_query($query);
		$record['query']=$query;
	}

	if ($_REQUEST['action']=='update_labtest_web')
	{
		$output['woid']=$_REQUEST['woid'];
		$output['value']=$_REQUEST['value'];
		if ($_REQUEST['labtestid']!="")
		{
			$query='update labtest set LABTESTNUMBER="'.$_REQUEST['value'].'" where ID="'.$_REQUEST['labtestid'].'" limit 1';
			$result=mysql_query($query);
			$output['labtestid']=$_REQUEST['labtestid'];
		}
		else
		{
			$query='insert into labtest set LABTESTNUMBER="'.$_REQUEST['value'].'"';
			$result=mysql_query($query);
			$output['labtestid']=mysql_insert_id();
		}
		$output['query']=$query;
		print $json->encode($output);
		exit;			
	}
	
	if ($_REQUEST['action']=='update_labresult_web')
	{
		$output=$_REQUEST;
		$query='select * from labtest where WOID="'.$_REQUEST['woid'].'" limit 1';
		$result=mysql_query($query);
		
		if (mysql_num_rows($result)==0)
		{
			$query='insert into labtest set WOID="'.$_REQUEST['woid'].'"';
			$result=mysql_query($query);
			$output['labtestid']=mysql_insert_id();			
		}
		else
		{
			$row=mysql_fetch_assoc($result);
			$output['labtestid']=$row['ID'];
		}
		
		if ($_REQUEST['test']=="labReportNumber")
		{
			$query='update labtest set LABTESTNUMBER="'.$_REQUEST['value'].'" where ID="'.$output['labtestid'].'" limit 1';
			$output['is_error']=false;
			$output['error_text']="";
			$output['html']=strtoupper($_REQUEST['new_value']);
			$result=mysql_query($query);
			$output['query']=$query;
			print $json->encode($output);
			exit;			
		}
		if ($_REQUEST['labresultid']!="")
		{
			if ($_REQUEST['request']=="NO")
			{
				$query='delete from labresults where ID="'.$_REQUEST['labresultid'].'" limit 1';
				$output['labresults_id']="";
			}
			else
			{
				$query='update labresults set VALUE1="'.$_REQUEST['value'].'" where ID="'.$_REQUEST['labresultid'].'"';
				$output['labresults_id']=$_REQUEST['labresultid'];
			}
			$result=mysql_query($query);
		}
		else
		{
			// $query='select ID from labtest where WOID="'.$_REQUEST['woid'].'"';
			// $result=mysql_query($query);
			// $row=mysql_fetch_array($result);
			$query='insert into labresults set VALUE1="'.$_REQUEST['value'].'", LABTESTID="'.$output['labtestid'].'", LABTEST="'.$_REQUEST['test'].'"';
			$result=mysql_query($query);
			$output['labresults_id']=mysql_insert_id();
		}
		$output['query']=$query;
		print $json->encode($output);
		exit;			
	}
	
	if ($_REQUEST['action']=='update_labtest')
	{
		
		if ($_REQUEST['ID']=="NEW" | $_REQUEST['ID']=="")
		{
			$query='select ID from labtest where WOID="'.$_REQUEST['WOID'].'"';
			$result=mysql_query($query);
			$row=mysql_fetch_assoc($result);
			$labtestid=$row['ID'];

			$query='insert into labresults set LABTEST="'.$_REQUEST['LABTEST'].'",
			      VALUE1="'.$_REQUEST['VALUE1'].'",
				  UNITS1="'.$_REQUEST['UNITS1'].'",
				  LABTESTID="'.$labtestid.'"';
			$result=mysql_query($query);
			$record['ID']=mysql_insert_id();
			$record['LABTESTID']=$labtestid;			
		}
		else
		{
			$query='update labresults set LABTEST="'.$_REQUEST['LABTEST'].'",
			      VALUE1="'.$_REQUEST['VALUE1'].'",
				  UNITS1="'.$_REQUEST['UNITS1'].'"
				  where ID="'.$_REQUEST['ID'].'" limit 1';
			$result=mysql_query($query);
			
		}
		
	}

	if ($_REQUEST['action']=='delete_labtest')
	{
		 $query='delete from labresults where ID="'.$_REQUEST['ID'].'" LIMIT 1';
		 $record['query']=$query;
		 $result=mysql_query($query);		
	}

	if ($_REQUEST['action']=='update_lab')
	{
		$query='select * from labtest where WOID="'.$_REQUEST['WOID'].'"';
		$result=mysql_query($query);
		if (mysql_num_rows($result)==0)
		{
			$query='insert into labtest set LAB="'.$_REQUEST['LAB'].'", LABTESTNUMBER="'.$_REQUEST['LABTESTNUMBER'].'",
				   WOID="'.$_REQUEST['WOID'].'"';
			$result=mysql_query($query);
		}
		else
		{
			$query='update labtest set LAB="'.$_REQUEST['LAB'].'", LABTESTNUMBER="'.$_REQUEST['LABTESTNUMBER'].'"
				  where WOID="'.$_REQUEST['WOID'].'" limit 1';
			$result=mysql_query($query);
		}
		$record['query']=$query;
		
	}
	if ($_REQUEST['action']=='add_reservation')
	{
		$query='select * from assets where HIDDEN="NO" and NAME="'.$_REQUEST['ASSETNAME'].'"';
		$result=mysql_query($query);
		$row=mysql_fetch_assoc($result);
		$record['asset']=$row;
		
		$query='insert into reservation set 
		 WOID="'.$_REQUEST['WOID'].'",
		 ASSETID="'.$row['ID'].'"';
		$result=mysql_query($query);
	}
	if ($_REQUEST['action']=='delete_reservation')
	{
		$query='select * from assets where HIDDEN="NO" and NAME="'.$_REQUEST['ASSETNAME'].'"';
		$result=mysql_query($query);
		$row=mysql_fetch_assoc($result);

		$query='delete from reservation where 
		 WOID="'.$_REQUEST['WOID'].'" and
		 ASSETID="'.$row['ID'].'" limit 1';
		$result=mysql_query($query);
	}

	if ($_REQUEST['action']=='add_labtest_wo')
	{
		$query='select startdate,enddate from tasks where id="'.$_REQUEST['taskid'].'"';
		$r=mysql_query($query);
		$row=mysql_fetch_assoc($r);
		$sqlStartDate=$row['startdate'];
		$sqlEndDate=$row['enddate'];
		
		$query='insert into wo set TYPE="'.$_REQUEST['type'].'",
			LOT="'.strtoupper($_REQUEST['lot']).'",
			REQUESTOR="'.$_REQUEST['username'].'",
			DUEDATE="'.$sqlStartDate.'",
			ENDDATE="'.$sqlEndDate.'",
			OTHERDESC="'.strtoupper($_REQUEST['notes']).'",
			TASKID="'.$_REQUEST['taskid'].'",
			CLIENTCODE="'.$_REQUEST['clientcode'].'"';
		mysql_query($query);
		$result['query']=$query;
		$result['woid']=mysql_insert_id();
		$q='insert into labtest set WOID='.$result['woid'];
		$r=mysql_query($q);
		
		$record['wo']=GetWO($result['woid']);
		$record['lotinfo']=lotinforecords($record['wo']['data']['LOT'],"WO",$record['wo']['data']['ID']);
	}
	
	if ($_REQUEST['action']=='delete_blend')
	{
		$query='delete from blenditems where ID="'.$_REQUEST['ID'].'" limit 1';
		$result=mysql_query($query);
	}
	
	if ($_REQUEST['action']=='update_blend')
	{
		$query='select * from blend where WOID="'.$_REQUEST['WOID'].'"';
		$result=mysql_query($query);
		if (mysql_num_rows($result)>0)
		{
			$row=mysql_fetch_assoc($result);
			$blendid=$row['ID'];
		}
		else
		{
			$query='insert into blend set WOID="'.$_REQUEST['WOID'].'"';
			$result=mysql_query($query);
			$blendid=mysql_insert_id();
		}
		if ($_REQUEST['BLENDID']=="NEW")
		{
			$query='insert into blenditems set 
			 SOURCELOT="'.$_REQUEST['SOURCELOT'].'",
			 GALLONS="'.$_REQUEST['GALLONS'].'",
			 DIRECTION="'.$_REQUEST['DIRECTION'].'",
			 COMMENT="'.$_REQUEST['COMMENT'].'",
			 BLENDID="'.$blendid.'"';
			$result=mysql_query($query);
			$record['BLENDID']=mysql_insert_id();
		}
		else
		{
			$query='update blenditems set 
			 SOURCELOT="'.$_REQUEST['SOURCELOT'].'",
			 GALLONS="'.$_REQUEST['GALLONS'].'",
			 DIRECTION="'.$_REQUEST['DIRECTION'].'",
			 COMMENT="'.$_REQUEST['COMMENT'].'",
			 BLENDID="'.$_REQUEST['BLENDID'].'"
			 WHERE ID="'.$_REQUEST['ID'].'"';
			$result=mysql_query($query);			
		}
	}
	if ($_REQUEST['action']=="modlot")
	{
		if ($_REQUEST['organic']=="")
			$_REQUEST['organic']="NO";
		$code=$_REQUEST['clientid'];
		$newlotnumber=$_REQUEST['lotnumber'];
		if ($_REQUEST['DBID']!="NEW")
		{
			$query='UPDATE lots SET lots.DESCRIPTION="'.strtoupper($_REQUEST['description']).'",
			lots.ORGANIC="'.strtoupper($_REQUEST['organic']).'",'.
			'lots.ACTIVELOT="'.strtoupper($_REQUEST['active']).'"
			 WHERE LOTNUMBER="'.$_REQUEST['lotnumber'].'" limit 1';
			mysql_query($query);
			$record['ID']=$_REQUEST['DBID'];
		}
		else
		{
			$query='INSERT INTO lots SET lots.LOTNUMBER="'.$newlotnumber.'",'.
				'lots.DESCRIPTION="'.strtoupper($_REQUEST['description']).'",'.
				'lots.ORGANIC="'.strtoupper($_REQUEST['organic']).'",'.
				'lots.ACTIVELOT="'.strtoupper($_REQUEST['active']).'",'.
				'lots.YEAR="'.$_REQUEST['vintage'].'",'.
				'lots.CLIENTCODE="'.$_REQUEST['clientid'].'"';
			mysql_query($query);
			$record['ID']=mysql_insert_id();
		}
		$record['query1']=$query;

		if ($_REQUEST['favorite']=="YES")
		{
			$query='select * from lotfavorites where LOTNUMBER="'.$_REQUEST['lotnumber'].'" and CLIENTID="'.$_REQUEST['clientid'].'"';
			$result=mysql_query($result);
			if (mysql_num_rows($result)==0)
			{
				$query='insert into lotfavorites set LOTNUMBER="'.$_REQUEST['lotnumber'].'",CLIENTID="'.$_REQUEST['clientid'].'", FAVORITE="YES"';
				$result=mysql_query($result);				
			}
		}
		else
		{
			$query='delete from lotfavorites where LOTNUMBER="'.$_REQUEST['lotnumber'].'" and CLIENTID="'.$_REQUEST['clientid'].'"';
		}
		$record['query2']=$query;
		$result=mysql_query($query);
		if (!$result)
		{
			$record['query_result2']="ERROR";
			$record['the_error2']=mysql_error();			
		}
		else
			$record['query_result2']="SUCCESSFUL";

	}

	if ($_REQUEST['action']=='delete_vineyard')
	{
		$query='delete from locations where ID="'.$_REQUEST['ID'].'" LIMIT 1';
		$result=mysql_query($query);			
	}

	if ($_REQUEST['action']=='update_vineyard')
	{
		if ($_REQUEST['ID']=="NEW")
		{
			$query='insert into locations set 
				 BIODYNAMIC="'.$_REQUEST['BIODYNAMIC'].'",
				 CLIENTID="'.$_REQUEST['CLIENTID'].'",
				 ORGANIC="'.$_REQUEST['ORGANIC'].'",
				 GATECODE="'.$_REQUEST['GATECODE'].'",
				 APPELLATION="'.$_REQUEST['APPELLATION'].'",
				 REGION="'.$_REQUEST['REGION'].'",
				 LOCATIONTYPE="VINEYARD",
				 LAT="'.$_REQUEST['LAT'].'",
				 locations.LONG="'.$_REQUEST['LONG'].'",
				 NAME="'.$_REQUEST['NAME'].'"';
			$result=mysql_query($query);
			$record['ID']=mysql_insert_id();			
		}
		else
		{
			$query='update locations set 
				 BIODYNAMIC="'.$_REQUEST['BIODYNAMIC'].'",
				 ORGANIC="'.$_REQUEST['ORGANIC'].'",
				 GATECODE="'.$_REQUEST['GATECODE'].'",
				 APPELLATION="'.$_REQUEST['APPELLATION'].'",
				 REGION="'.$_REQUEST['REGION'].'",
				 LAT="'.$_REQUEST['LAT'].'",
				 locations.LONG="'.$_REQUEST['LONG'].'",
				 NAME="'.$_REQUEST['NAME'].'"	
				where ID="'.$_REQUEST['ID'].'" LIMIT 1';
			$result=mysql_query($query);
			$record['ID']=$_REQUEST['ID'];			
		}
		$record['query']=$query;
	}	
	
	if ($_REQUEST['action']=='update_scp')
	{
		$query='select * from clients where CLIENTNAME="'.$_REQUEST['CLIENTNAME'].'"';
		$result=mysql_query($query);
		$row=mysql_fetch_assoc($result);
		$clientcode=$row['CODE'];
		
		if ($_REQUEST['ID']=="NEW")
		{
			$query='insert into wo SET 
			 DUEDATE="'.$_REQUEST['DUEDATE'].'",
			 ENDDATE="'.$_REQUEST['DUEDATE'].'",
			 TYPE="SCP",
			 DELETED="0",
			 LOT="'.$_REQUEST['LOT'].'",
			 CLIENTCODE="'.$clientcode.'",
			 STATUS="'.$_REQUEST['STATUS'].'",
			 OTHERDESC="'.$_REQUEST['OTHERDESC'].'",
			 WORKPERFORMEDBY="'.$_REQUEST['WORKPERFORMEDBY'].'"';
			$result=mysql_query($query);
			$record['WOID']=mysql_insert_id();
		
			$query='insert into scp set
				WOID="'.$record['WOID'].'",
				ZONE="'.$_REQUEST['ZONE'].'",
				DELIVERYDATE="'.$_REQUEST['DELIVERYDATE'].'",
				WHOLECLUSTER="'.$_REQUEST['WHOLECLUSTER'].'",
				TANKPOSITION="'.$_REQUEST['TANKPOSITION'].'",
				SPECIALINSTRUCTIONS="'.$_REQUEST['SPECIALINSTRUCTIONS'].'",
				CRUSHING="'.$_REQUEST['CRUSHING'].'",
				ESTTONS="'.$_REQUEST['ESTTONS'].'",
				HANDSORTING="'.$_REQUEST['HANDSORTING'].'",
				VARIETAL="'.$_REQUEST['VARIETAL'].'",
				APPELLATION="'.$_REQUEST['APPELLATION'].'",
				VINEYARDID="'.$_REQUEST['VINEYARDID'].'"';

//			$query='insert into scp set WHOLECLUSTER=0, HANDSORTING="YES", CRUSHING="NOCRUSHING", ESTTONS="0", WOID="'.$record['ID'].'"';
			$result=mysql_query($query);
			$record['ID']=mysql_insert_id();
			$record['query']=$query;
		}
		else
		{
			$query='update wo set LOT="'.$_REQUEST['LOT'].'", STATUS="'.$_REQUEST['STATUS'].'", DUEDATE="'.$_REQUEST['DELIVERYDATE'].'",ENDDATE="'.$_REQUEST['DELIVERYDATE'].'" WHERE ID="'.$_REQUEST['WOID'].'" LIMIT 1';
			$result=mysql_query($query);

			$query='update scp set
				WOID="'.$_REQUEST['WOID'].'",
				ZONE="'.$_REQUEST['ZONE'].'",
				DELIVERYDATE="'.$_REQUEST['DELIVERYDATE'].'",
				WHOLECLUSTER="'.$_REQUEST['WHOLECLUSTER'].'",
				TANKPOSITION="'.$_REQUEST['TANKPOSITION'].'",
				SPECIALINSTRUCTIONS="'.$_REQUEST['SPECIALINSTRUCTIONS'].'",
				CRUSHING="'.$_REQUEST['CRUSHING'].'",
				ESTTONS="'.$_REQUEST['ESTTONS'].'",
				HANDSORTING="'.$_REQUEST['HANDSORTING'].'",
				VARIETAL="'.$_REQUEST['VARIETAL'].'",
				APPELLATION="'.$_REQUEST['APPELLATION'].'",
				VINEYARDID="'.$_REQUEST['VINEYARDID'].'" 
			      WHERE ID="'.$_REQUEST['ID'].'" LIMIT 1';
			$result=mysql_query($query);
			$record['query']=$query;
			
		}
	}
	if ($_REQUEST['action']=='ping')
	{
		$output = $json->encode($_REQUEST);
		print $output;
		exit;
	}
	
	if ($_REQUEST['action']=='changeLotName')
	{
		$query='update lots set DESCRIPTION="'.strtoupper($_REQUEST['new_value']).'" where LOTNUMBER="'.$_REQUEST['id'].'" limit 1';
		$result=mysql_query($query);
		$output['is_error']=false;
		$output['error_text']="";
		$output['html']=strtoupper($_REQUEST['new_value']);
		print $json->encode($output);
		exit;
	}

	if ($_REQUEST['action']=='update_inventory')
	{
		$query='update wo set '.$_REQUEST['vessel'].'="'.$_REQUEST['new_value'].'" where ID="'.$_REQUEST['woid'].'"';
		$result=mysql_query($query);
		$output['is_error']=false;
		$output['error_text']="";
		$output['html']=strtoupper($_REQUEST['new_value']);
		$output['query']=$query;
		print $json->encode($output);
		exit;			
	}
	if ($_REQUEST['action']=='update_wo_date')
	{
		$query='update wo set DUEDATE="'.date("Y-m-d",strtotime($_REQUEST['date'])).'",ENDDATE="'.date("Y-m-d",strtotime($_REQUEST['date'])).'" where ID="'.$_REQUEST['woid'].'" limit 1';
		$result=mysql_query($query);
		$record['query']=$query;
	}
	if ($_REQUEST['action']=='deleteRow')
	{
		$query='delete from '.$_REQUEST['table'].' where ID="'.$_REQUEST['rowid'].'" limit 1';
		$result=mysql_query($query);
		$record=$query;
		print $json->encode($record);
		exit;
	}

	function getLastToppingLotFromWO($woid)
	{
		$query='select LOT from wo where wo.ID="'.$woid.'"';
		$result=mysql_query($query);
		$row=mysql_fetch_assoc($result);
		$lastToppingLot=getLastToppingLotFromLot($row['LOT']);
		return $lastToppingLot['LOT'];
	}
	function getLastToppingLotFromLot($lot)
	{
		$query='select TOPPINGLOT,OTHERDESC from wo where wo.LOT="'.$lot.'" and wo.TYPE="TOPPING" and wo.DELETED=0 order by DUEDATE desc limit 1';
		$result=mysql_query($query);
		if (mysql_num_rows($result)>0)
		{
			$row=mysql_fetch_assoc($result);
			return $row;			
		}
		else
			return "";	
	}
	
	if ($_REQUEST['action']=='update_field')
	{
		$updateSecondField=0;
		if (($_REQUEST['field']=="type") & ($_REQUEST['value']=="LAB TEST"))
		{
			$r=mysql_query('select * from labtest where WOID='.$_REQUEST['rowid']);
			if (mysql_num_rows($r)==0)
			{
				$r=mysql_query('insert into labtest set WOID='.$_REQUEST['rowid']);
			}
		}
		if (($_REQUEST['field']=="type") & ($_REQUEST['value']=="TOPPING"))
		{
			$updateSecondField=1;
			$secondFieldValue=getLastToppingLotFromWO($_REQUEST['rowid']);	
			$secondFieldName="TOPPINGLOT";	
		}
		// if ($_REQUEST['field']=="type") & ($_REQUEST['table']=="wo")
		// {
		// 	$query='select * from wo where wo.ID="'.$_REQUEST['rowid'].'"';
		// 	$result=mysql_query($query);
		// 	$row=mysql_fetch_assoc($result);
		// 	if ($row['TASKID']>0)
		// 	{
		// 		$query='update wo set TYPE="'.$_REQUEST['value'].'" where TASKID="'.$row['TASKID'].'"';
		// 		$result=mysql_query($query);
		// 		$query='update tasks set TYPE="'.$_REQUEST['value'].'" where ID="'.$row['TASKID'].'"';
		// 		$result=mysql_query($query);
		// 	}
		// }
		if ($_REQUEST['fieldtype']=="text" | $_REQUEST['fieldtype']=="textarea")
		{
			$_REQUEST['value']=strtoupper($_REQUEST['value']);
		}
		$record=$_REQUEST;
		if ($_REQUEST['rowid']=="NEW")
		{
			$query='insert into '.$_REQUEST['table'].' set '.$_REQUEST['field'].'="'.$_REQUEST['value'].'"';
			$result=mysql_query($query);
			$id=mysql_insert_id();
			$query='select * from '.$_REQUEST['table'].' where ID="'.$id.'"';
			$result=mysql_query($query);
			$row=mysql_fetch_assoc($result);
			$record['data']=$row;
			print $json->encode($record);
			exit;
		}
		if ($updateSecondField==1)
		{
			$query='update '.$_REQUEST['table'].' set '.$secondFieldName.'="'.$secondFieldValue.'" where ID="'.$_REQUEST['rowid'].'" limit 1';
			$result=mysql_query($query);
			$record['queries'][]=$query;			
		}
		
		$fieldnames=explode(",",$_REQUEST['field']);

		$data=$json->decode($_REQUEST['data']);
		$record['thedata']=$data;
		if ($_REQUEST['fieldtype']=="date")
			$value=date('y-m-d',strtotime($_REQUEST['value']));
		else
			$value=$_REQUEST['value'];
		for ($i=0;$i<count($fieldnames);$i++)
		{
			$query='update '.$_REQUEST['table'].' set '.$fieldnames[$i].'="'.$value.'" where ID="'.$_REQUEST['rowid'].'" limit 1';
			$result=mysql_query($query);
			$record['queries'][]=$query;			
		}
		$record['thedata']=getWO($_REQUEST['parentrowid']);
		$record['query']=$query;
		print $json->encode($record);
		exit;
	}

	if ($_REQUEST['action']=='update_wo_field')
	{
		$record=$_REQUEST;
		if ($_REQUEST['field']=="OTHERDESC")
			$_REQUEST['value']=strtoupper($_REQUEST['value']);
		if ($_REQUEST['fieldtype']=="date")
			$value=date('y-m-d',strtotime($_REQUEST['value']));
		else
			$value=$_REQUEST['value'];
		$query='update wo set '.$_REQUEST['field'].'="'.$value.'" where ID="'.$_REQUEST['rowid'].'" limit 1';
		$result=mysql_query($query);
		
		if (($_REQUEST['value']=="LAB TEST") & ($_REQUEST['field']=="TYPE"))
		{
			$query='select * from labtest where WOID="'.$_REQUEST['rowid'].'"';
			$result=mysql_query($query);
			if (mysql_num_rows($result)==0)
			{
				mysql_query('insert into labtest set WOID="'.$_REQUEST['rowid'].'"');
			}
		}
		$record['thedata']=getWO($_REQUEST['parentrowid']);
		$record['query']=$query;
		print $json->encode($record);
		exit;
	}

	if ($_REQUEST['action']=='change_multilot_status')
	{
		$record=$_REQUEST;
		if ($_REQUEST['complete']=="true")
		{
			$query='select TYPE,DUEDATE,ENDDATE,WORKPERFORMEDBY from wo where ID="'.$_REQUEST['woid'].'"';
			$result=mysql_query($query);
			$row=mysql_fetch_assoc($result);
			$query='insert into tasks set type="'.$row['TYPE'].'", startdate="'.$row['DUEDATE'].'", enddate="'.$row['ENDDATE'].'", workperformedby="'.$row['WORKPERFORMEDBY'].'"';
			$result=mysql_query($query);
			$taskid=mysql_insert_id();
			$query='update wo set TASKID="'.$taskid.'" where ID="'.$_REQUEST['woid'].'" limit 1';
			$result=mysql_query($query);
			$record['taskid']=$taskid;
		}
		else
		{
			$query='update wo set TASKID="0" where ID="'.$_REQUEST['woid'].'" limit 1';
			$result=mysql_query($query);
			$record['taskid']=0;
		}
		$record['query']=$query;
	}
	
	if ($_REQUEST['action']=='update_wo_status')
	{
		if ($_REQUEST['complete']=="true")
			$query='update wo set STATUS="COMPLETED" where ID="'.$_REQUEST['woid'].'"';
		else
			$query='update wo set STATUS="ASSIGNED" where ID="'.$_REQUEST['woid'].'"';
		$result=mysql_query($query);
		$record['query']=$query;
		
	}
	if ($_REQUEST['action']=='delete_wo')
	{
		$query='update wo SET DELETED="1" where ID="'.$_REQUEST['ID'].'" limit 1';
		$result=mysql_query($query);
		$record['inputs']=$_REQUEST;
		$record['query']=$query;
	}
	if ($_REQUEST['action']=='update_wo')
	{
		$query='select * from clients where CLIENTNAME="'.$_REQUEST['CLIENTNAME'].'"';
		$result=mysql_query($query);
		$row=mysql_fetch_assoc($result);
		$clientcode=$row['CODE'];
		
		IF ($_REQUEST['DUEDATE']=="")
		{
			$_REQUEST['DUEDATE']=date("Y-m-d H:i:s");
		}
		if ($_REQUEST['ID']=="NEW")
		{
			$query='insert into wo SET 
			 DUEDATE="'.date("Y-m-d H:i:s",time()).'",
			 ENDDATE="'.date("Y-m-d H:i:s",time()).'",
			 DELETED="0",
			 TYPE="'.$_REQUEST['TYPE'].'",
			 ENDINGTANKGALLONS="'.$_REQUEST['ENDINGTANKGALLONS'].'",
			 ENDINGTOPPINGGALLONS="'.$_REQUEST['ENDINGTOPPINGGALLONS'].'",
			 ENDINGBARRELCOUNT="'.$_REQUEST['ENDINGBARRELCOUNT'].'",
			 LOT="'.$_REQUEST['LOT'].'",
			 CLIENTCODE="'.$clientcode.'",
			 REQUESTOR="'.$_REQUEST['username'].'",
			 STATUS="'.$_REQUEST['STATUS'].'",
			 OTHERDESC="'.$_REQUEST['OTHERDESC'].'",
			 WORKPERFORMEDBY="'.$_REQUEST['WORKPERFORMEDBY'].'"';
			$result=mysql_query($query);
			$record['ID']=mysql_insert_id();
			$allwos=lotinforecords($_REQUEST['LOT']);
			$record['newwo']=$allwos[count($allwos)-1];
			
			$query='SELECT devtokens.DEVTOKEN FROM users INNER JOIN devtokens ON users.deviceid = devtokens.UDID';
			$result=mysql_query($query);
			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$row=mysql_fetch_assoc($result);
				$thedevtoken=ereg_replace("[^A-Za-z0-9]","",$row['DEVTOKEN']);
				if (sizeof(explode('-',$row['DEVTOKEN']))==1)  //eliminate iphone simulator devtoken which has '-' in it
				{
					$query2='insert into newwos set DEVTOKEN="'.$row['DEVTOKEN'].'", WOID="'.$record['ID'].'"';
					$result2=mysql_query($query2);
					$query3='select count(DEVTOKEN) as THECOUNT from newwos where DEVTOKEN="'.$row['DEVTOKEN'].'"';
					$result3=mysql_query($query3);
					$row3=mysql_fetch_assoc($result3);
					$devtokens[]=$thedevtoken;
				}
			}
			if (sizeof($devtokens)>0)
			{
				$message=$_REQUEST['TYPE']." Work Order (".$record['ID'].") added by ".$_REQUEST['CLIENTCODE'];
		//		sendPush($devtokens,$message,$row3['THECOUNT'],"default");				
			}
		}
		else
		{
			if ($_REQUEST['TYPE']=="PRESSOFF")
			{
				$volume=$_REQUEST['ENDINGTANKGALLONS']+$_REQUEST['ENDINGBARRELCOUNT']+$_REQUEST['ENDINGTOPPINGGALLONS'];
				if ($volume<=0 & $_REQUEST['STATUS']=="COMPLETED")
				{
					$query='update wo SET 
					 DUEDATE="'.$_REQUEST['DUEDATE'].'",
					 TYPE="'.$_REQUEST['TYPE'].'",
					 ENDDATE="'.$_REQUEST['DUEDATE'].'",
					 ENDINGTANKGALLONS="'.$_REQUEST['ENDINGTANKGALLONS'].'",
					 ENDINGTOPPINGGALLONS="'.$_REQUEST['ENDINGTOPPINGGALLONS'].'",
					 ENDINGBARRELCOUNT="'.$_REQUEST['ENDINGBARRELCOUNT'].'",
					 LOT="'.$_REQUEST['LOT'].'",
					 CLIENTCODE="'.$clientcode.'",
					 OTHERDESC="'.$_REQUEST['OTHERDESC'].'",
					 WORKPERFORMEDBY="'.$_REQUEST['WORKPERFORMEDBY'].'" where ID="'.$_REQUEST['ID'].'"';
				}
			}
			else
			{
				$query='update wo SET 
				 DUEDATE="'.$_REQUEST['DUEDATE'].'",
				 ENDDATE="'.$_REQUEST['DUEDATE'].'",
				 TYPE="'.$_REQUEST['TYPE'].'",
				 ENDINGTANKGALLONS="'.$_REQUEST['ENDINGTANKGALLONS'].'",
				 ENDINGTOPPINGGALLONS="'.$_REQUEST['ENDINGTOPPINGGALLONS'].'",
				 ENDINGBARRELCOUNT="'.$_REQUEST['ENDINGBARRELCOUNT'].'",
				 LOT="'.$_REQUEST['LOT'].'",
				 CLIENTCODE="'.$clientcode.'",
				 STATUS="'.$_REQUEST['STATUS'].'",
				 OTHERDESC="'.$_REQUEST['OTHERDESC'].'",
				 WORKPERFORMEDBY="'.$_REQUEST['WORKPERFORMEDBY'].'" where ID="'.$_REQUEST['ID'].'"';
			}
			$record['ID']=$_REQUEST['ID'];
			$result=mysql_query($query);
			
		}
		if ($_REQUEST['TYPE']=="LAB TEST")
		{
			$query2='select * from labtest where WOID="'.$record['ID'].'"';
			$result2=mysql_query($query2);
			if (mysql_num_rows($result2)==0)
			{
				$query2='insert into labtest set LAB="", 
					   WOID="'.$record['ID'].'"';
				$result2=mysql_query($query2);
			}
		}
		$record['query']=$query;
	}

	if ($_REQUEST['action']=='complete_wo')
	{
		$query='update wo set STATUS="COMPLETED" where ID="'.$_REQUEST['ID'].'" LIMIT 1';
		$result=mysql_query($query);
	}

	if ($_REQUEST['action']=="addlot")
	{
		$year=substr($_REQUEST['vintage'],-2);
		$code=getclientcode($_REQUEST['clientid']);
		$lotnumber=sprintf("%03d",getNextLotNumber($_REQUEST['vintage'],$_REQUEST['clientid']));
		$newlotnumber=$year.'-'.$code.'-'.$lotnumber;

		$query='INSERT INTO lots SET lots.LOTNUMBER="'.$newlotnumber.'",'.
			'lots.DESCRIPTION="'.strtoupper($_REQUEST['description']).'",'.
			'lots.YEAR="'.$_REQUEST['vintage'].'",'.
			'lots.CLIENTCODE="'.$_REQUEST['clientid'].'"';
		$result=mysql_query($query);
		$query='select * from lots where ID="'.mysql_insert_id().'"';
		$result=mysql_query($query);
		$record['lotinfo']=mysql_fetch_assoc($result);
		// echo '<pre>';
		// print_r($record);
		// exit;
		$output = $json->encode($record);
		print $output;
		exit;
	}
	
	if ($_REQUEST['action']=='delete_wt')
	{
		$query='delete from bindetail where WEIGHTAG="'.$_REQUEST['ID'].'"';
		$result=mysql_query($query);
		$query='delete from wt where ID="'.$_REQUEST['ID'].'" limit 1';
		$result=mysql_query($query);
	}

	if ($_REQUEST['action']=='update_wt')
	{
		$query='select * from clients where CLIENTNAME="'.$_REQUEST['CLIENTNAME'].'"';
		$result=mysql_query($query);
		$row=mysql_fetch_assoc($result);
		$clientcode=$row['CODE'];
		$clientid=$row['clientid'];
		
		if ($_REQUEST['ID']=="NEW")
		{
			$query='select max(TAGID)as MAXTAGID from wt';
			$result=mysql_query($query);
			$row=mysql_fetch_assoc($result);
			$maxtagid=$row['MAXTAGID']+1;
			
			$query='insert into wt set
				DATETIME="'.$_REQUEST['DATETIME'].'",
				REGIONCODE="'.$_REQUEST['REGIONCODE'].'",
				LOT="'.$_REQUEST['LOT'].'",
				TAGID="'.$maxtagid.'",
				CLIENTCODE="'.$clientid.'",
				VARIETY="'.$_REQUEST['VARIETY'].'",
				APPELLATION="'.$_REQUEST['APPELLATION'].'",
				VINEYARDID="'.$_REQUEST['VINEYARDID'].'"';
			$result=mysql_query($query);
			$record['ID']=mysql_insert_id();
			$record['TAGID']=$maxtagid;
			$record['query']=$query;			
		}
		else
		{		
			$query='update wt set
				DATETIME="'.$_REQUEST['DATETIME'].'",
				REGIONCODE="'.$_REQUEST['REGIONCODE'].'",
				LOT="'.$_REQUEST['LOT'].'",
				TRUCKLICENSE="'.$_REQUEST['TRUCKLICENSE'].'",
				VARIETY="'.$_REQUEST['VARIETY'].'",
				APPELLATION="'.$_REQUEST['APPELLATION'].'",
				VINEYARDID="'.$_REQUEST['VINEYARDID'].'" 
			      WHERE ID="'.$_REQUEST['ID'].'" LIMIT 1';
			$record['ID']=$_REQUEST['ID'];
			$record['TAGID']=$_REQUEST['TAGID'];
			$result=mysql_query($query);
			$record['query']=$query;
		}
	}

	if ($_REQUEST['action']=='delete_bindetail')
	{
		 $query='delete from bindetail where ID="'.$_REQUEST['ID'].'" LIMIT 1';
		 $result=mysql_query($query);		
		 $record['query']=$query;
	}
	if ($_REQUEST['action']=='add_bindetail')
	{
		$query='insert into bindetail set BINCOUNT="'.$_REQUEST['BINCOUNT'].'",
		      WEIGHT="'.$_REQUEST['WEIGHT'].'",
			  TARE="'.$_REQUEST['TARE'].'",
		      MISC="'.$_REQUEST['MISC'].'",		
			  WEIGHTAG="'.$_REQUEST['WEIGHTAG'].'"';
		$result=mysql_query($query);
		$record['ID']=(string) mysql_insert_id();
		$record['query']=$query;
	}
	if ($_REQUEST['action']=='update_bindetail')
	{
		$query='UPDATE bindetail set BINCOUNT="'.$_REQUEST['BINCOUNT'].'",
		      WEIGHT="'.$_REQUEST['WEIGHT'].'",
		      MISC="'.$_REQUEST['MISC'].'",		
			  TARE="'.$_REQUEST['TARE'].'" WHERE ID="'.$_REQUEST['ID'].'"';
		$result=mysql_query($query);
		$record['query']=$query;
	}	
	if (!$result)
	{
		$record['query_result']="ERROR";
		$record['the_query']=$query;
		$record['the_error']=mysql_error();			
	}
	else
		$record['query_result']="SUCCESSFUL";
	
	$output = $json->encode($record);
	print $output;
	
?>
