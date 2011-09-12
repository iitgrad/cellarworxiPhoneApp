<?php
require_once('JSON.php');
require_once('../server/startdb.php');
require_once('lotinforecords.php');
require_once('staff.php');

	$json = new Services_JSON();

	$myt= new Timer();
	$myt->startTimer();
	
	function show($test)
	{
		echo '<pre>';
		print_r($test);
	}
		
	function lotHasDependencies($lotnumber)
	{
		$query='select ID from wt where LOT="'.$lotnumber.'"';
		$result=mysql_query($query);
		if (mysql_num_rows($result)>0)
			return 1;
		else
		{
			$query='select ID from wo where LOT="'.$lotnumber.'"';
			$result=mysql_query($query);
			if (mysql_num_rows($result)>0)
				return 1;
			else
			{
				$query='select ID from blenditems where SOURCELOT="'.$lotnumber.'"';
				$result=mysql_query($query);
				if (mysql_num_rows($result)>0)
					return 1;
			}
		}
	}

	function endingGallons($lotinfo)
	{
		$lastElementIndex=count($lotinfo);
		$lastElement=$lotinfo[$lastElementIndex-1];
		$gallons=$lastElement['ending_tankgallons'];
		$gallons+=$lastElement['ending_toppinggallons'];
		$gallons+=$lastElement['ending_bbls']*60;
		return $gallons;
	}
	function getLotInfo($onLot,$woid,$clientid)
	{
		if ($woid=="NEW")
			$woid="";
		if ($_GET['detail']=="YES")
			$record['workorders']=lotinforecords($onLot,"WO",$woid);
				
		$query='select * from lots where LOTNUMBER="'.$onLot.'"';
		$result=mysql_query($query);
		$record['lotinfo']=mysql_fetch_assoc($result);	
		$query2='select * from lotfavorites where CLIENTID="'.$clientid.'" AND LOTNUMBER="'.$onLot.'"';
		$result2=mysql_query($query2);
		if (mysql_num_rows($result2)>0)
			$record['lotinfo']['FAVORITE']="YES";
		else
			$record['lotinfo']['FAVORITE']="NO";
		
		for ($i=0;$i<count($record['workorders']);$i++)
		{
			$wo=$record['workorders'][$i]['data'];

			if ($record['workorders'][$i]['data']['TYPE']=="BLENDING")
			{
				$query='SELECT blenditems.SOURCELOT, 
					blenditems.GALLONS, 
					blenditems.DIRECTION, 
					blenditems.COMMENT,
					blenditems.ID,
					blend.ID as BLENDID,
					lots.DESCRIPTION as SOURCELOTDESCRIPTION,
					wo.ID as WOID
				FROM wo INNER JOIN blend ON wo.ID = blend.WOID
				 INNER JOIN blenditems ON blend.ID = blenditems.BLENDID
					LEFT OUTER JOIN lots on (blenditems.SOURCELOT=lots.LOTNUMBER)
				where wo.ID="'.$wo['ID'].'"';
				$result=mysql_query($query);
				for ($j=0;$j<mysql_num_rows($result);$j++)
				{
					$row=mysql_fetch_assoc($result);
					$record['workorders'][$i]['data']['blend'][]=$row;
				}
			}
			if ($wo['TYPE']=="SCP")
			{
				$query='select * from scp where WOID="'.$wo['ID'].'"';
				$result=mysql_query($query);
				if (mysql_num_rows($result)==0)
				{
					$query='insert into scp set WHOLECLUSTER=0, HANDSORTING="YES", CRUSHING="NOCRUSHING", ESTTONS="0", WOID="'.$wo['ID'].'"';
					mysql_query($query);
					$query='select * from scp where WOID="'.$wo['ID'].'"';
					$result=mysql_query($query);					
				}
				for ($j=0;$j<mysql_num_rows($result);$j++)
				{
					$row=mysql_fetch_assoc($result);
					$record['workorders'][$i]['data']['scp']=$row;
					$record['workorders'][$i]['data']['scp']['DELIVERYDATE']=$wo['DUEDATE'];
					
					$query2='select * from locations where ID="'.$row['VINEYARDID'].'"';
					$result2=mysql_query($query2);
					unset($row2);
					if (mysql_num_rows($result2)>0)
					{
						$row2=mysql_fetch_assoc($result2);
					}
					$record['workorders'][$i]['data']['scp']['vineyard']=$row2;
				}
			}
			if ($wo['TYPE']=="WT")
			{
				$query='select wt.*,clients.CLIENTNAME from wt left outer join clients on (wt.CLIENTCODE=clients.clientid) where TAGID="'.$wo['TAGID'].'"';
				$result=mysql_query($query);
				for ($j=0;$j<mysql_num_rows($result);$j++)
				{
					$row=mysql_fetch_assoc($result);
					$record['workorders'][$i]['data']['wt']=$row;
					$record['workorders'][$i]['data']['wt']['DELETEABLE']="NO";
					$record['workorders'][$i]['data']['wt']['NUMBER']=$row['TAGID']+5000;
					$record['workorders'][$i]['data']['wt']['VARIETAL']=$row['VARIETY'];
				
					$query2='select * from locations where ID="'.$row['VINEYARDID'].'"';
					$result2=mysql_query($query2);
					unset($row2);
					if (mysql_num_rows($result2)>0)
					{
						$row2=mysql_fetch_assoc($result2);
					}
					$record['workorders'][$i]['data']['wt']['vineyard']=$row2;
					
					$query2='select * from bindetail where WEIGHTAG="'.$row['ID'].'"';
					$result2=mysql_query($query2);
					unset($bindetail);
					for ($k=0;$k<mysql_num_rows($result2);$k++)
					{
						$bindetail[]=mysql_fetch_assoc($result2);
					}
					$record['workorders'][$i]['data']['wt']['bindetail']=$bindetail;
				}
			}
			if ($wo['TYPE']=="LAB TEST")
			{
				$query='select ID,LAB,LABTESTNUMBER from labtest where WOID="'.$wo['ID'].'"';
//				echo $query; exit;
				$result=mysql_query($query);
				if (mysql_num_rows($result)>0)
				{
					$row=mysql_fetch_assoc($result);
					$lab=$row['LAB'];
					$labref=$row['LABTESTNUMBER'];
					$query='select labresults.*,validlabtests.UNITS from labresults left outer join validlabtests on (validlabtests.LABTEST=labresults.LABTEST) where LABTESTID="'.$row['ID'].'"';
					$result=mysql_query($query);
					unset($testresults);
					for ($j=0; $j<mysql_num_rows($result); $j++)
					{
						$testresults[]=mysql_fetch_assoc($result);
					}
					$record['workorders'][$i]['data']['labtest']['lab']=$lab;
					$record['workorders'][$i]['data']['labtest']['LABTESTNUMBER']=$labref;
					$record['workorders'][$i]['data']['labtest']['LABTESTID']=$row['ID'];				
					$record['workorders'][$i]['data']['labtest']['results']=$testresults;	
				}				 
			}
			$query='select reservation.ID,ASSETID,assets.NAME AS NAME,DESCRIPTION,assets.CAPACITY, OWNER, assettypes.NAME as TYPENAME FROM reservation LEFT OUTER JOIN assets ON reservation.ASSETID=assets.ID
			 		 left outer join assettypes on assets.TYPEID=assettypes.ID where WOID="'.$wo['ID'].'"';
			$result=mysql_query($query);
			for ($j=0;$j<mysql_num_rows($result);$j++)
			{
				$row=mysql_fetch_assoc($result);
				$record['workorders'][$i]['data']['assets'][]=$row;
			}
			
		}
		return $record;
	}
	if ($_GET['action']=="clientlist")
	{
		$query='select clientid AS ID, CLIENTNAME, CODE, AP from clients WHERE ACTIVE="YES" ORDER BY CODE';
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			$record[]=mysql_fetch_assoc($result);
		}
	}
function get_wt_data($query)
{
	$result=mysql_query($query);
	for ($i=0;$i<mysql_num_rows($result);$i++)
	{
		$row=mysql_fetch_assoc($result);
		$wt=$row;
		$vyd['ID']=$row['LOCID'];
		$vyd['NAME']=$row['NAME'];
		$vyd['LOCATIONTYPE']=$row['LOCATIONTYPE'];
		$vyd['LAT']=$row['LAT'];
		$vyd['LONG']=$row['LONG'];
		$vyd['ORGANIC']=$row['ORGANIC'];
		$vyd['CLIENTID']=$row['CLIENTID'];
		$vyd['BIODYNAMIC']=$row['BIODYNAMIC'];
		$vyd['GATECODE']=$row['GATECODE'];
		$vyd['APPELLATION']=$row['LOCATION_APPELLATION'];
		$vyd['REGION']=$row['LOCATION_REGION'];

		$query2='select * from bindetail where WEIGHTAG="'.$row['ID'].'"';
		$result2=mysql_query($query2);
		unset($bindetail);
		for ($k=0;$k<mysql_num_rows($result2);$k++)
		{
			$bindetail[]=mysql_fetch_assoc($result2);
		}
		$record[$i]['type']="WT";
		$record[$i]['data']['TYPE']="WT";
		$record[$i]['data']['DUEDATE']=$row['DATETIME'];
		$record[$i]['data']['CLIENTNAME']=$row['CLIENTNAME'];
		$record[$i]['data']['CLIENTID']=$row['CLIENTID'];
		$record[$i]['data']['CLIENTCODE']=$row['CLIENTCODE'];
		$record[$i]['data']['wt']=$row;
		$record[$i]['data']['wt']['DELETEABLE']="NO";
		$record[$i]['data']['wt']['NUMBER']=$row['TAGID']+5000;
		$record[$i]['data']['wt']['VARIETAL']=$row['VARIETY'];
		$record[$i]['data']['wt']['bindetail']=$bindetail;
		$record[$i]['data']['wt']['vineyard']=$vyd;
	}
	return $record;
}	

if ($_GET['action']=="labviewsummary")
{
	$query='SELECT labresults.LABTEST, 
		labresults.VALUE1, 
		lots.LOTNUMBER, 
		wo.DUEDATE, 
		lots.CLIENTCODE, 
		lots.DESCRIPTION,
		lots.YEAR
	FROM labtest INNER JOIN wo ON labtest.WOID = wo.ID
		 INNER JOIN labresults ON labresults.LABTESTID = labtest.ID
		 INNER JOIN lots ON wo.LOT = lots.LOTNUMBER
	WHERE lots.YEAR = "'.$_GET['vintage'].'" AND lots.ACTIVELOT = "YES" and wo.DELETED=0 and lots.CLIENTCODE="'.$_GET['clientid'].'" ORDER BY lots.DESCRIPTION, wo.DUEDATE ASC';
//	echo $query;
//	exit;
	$result=mysql_query($query);
	for ($i=0;$i<mysql_num_rows($result);$i++)
	{
		$row=mysql_fetch_assoc($result);
		$labresults[$row['LOTNUMBER']][$row['LABTEST']]=$row['VALUE1'];
		$labresults[$row['LOTNUMBER']]['DESCRIPTION']=$row['DESCRIPTION'];
	}
	$record=$labresults;
}
if ($_GET['action']=="showLotsForBlending")
{
	$record['inputs']=$_REQUEST;
	if ($_REQUEST['woid']!="NEW")
	{
		$query='select LOTNUMBER, DESCRIPTION, ACTIVELOT, lots.ID from lots where lots.CLIENTCODE="'.$_GET['clientcode'].'" and YEAR='.$_REQUEST['vintage'].' order by LOTNUMBER';		
	}
	else
	{
		if ($_REQUEST['allActive']==1)
			$query='select LOTNUMBER, DESCRIPTION, ACTIVELOT, lots.ID from lots where lots.CLIENTCODE="'.$_GET['clientcode'].'" and ACTIVELOT="YES" order by LOTNUMBER';
		else
			$query='select LOTNUMBER, DESCRIPTION, ACTIVELOT, lots.ID from lots where lots.CLIENTCODE="'.$_GET['clientcode'].'" and YEAR="'.$_GET['vintage'].'" order by LOTNUMBER';		
	}
	$result=mysql_query($query);
	$record['wotype']=$_GET['wotype'];
	for ($i=0;$i<mysql_num_rows($result);$i++)
	{
		$row=mysql_fetch_assoc($result);
		$record['lots'][]=getLotInfo($row['LOTNUMBER'],"",$_GET['clientcode']);
	}
	if ($_REQUEST['woid']!="NEW")
	{
		$record['blendwo']=GetWO($_REQUEST['woid']);		
	}
}
if ($_GET['action']=="showlots")
{
	$record['inputs']=$_REQUEST;
	if ($_REQUEST['allActive']==1)
		$query='select LOTNUMBER, DESCRIPTION, ACTIVELOT, lots.ID from lots where lots.CLIENTCODE="'.$_GET['clientcode'].'" and ACTIVELOT="YES" order by LOTNUMBER';
	else
		$query='select LOTNUMBER, DESCRIPTION, ACTIVELOT, lots.ID from lots where lots.CLIENTCODE="'.$_GET['clientcode'].'" and YEAR="'.$_GET['vintage'].'" order by LOTNUMBER';
	// echo $query;
	// exit;
	$result=mysql_query($query);
	$record['wotype']=$_GET['wotype'];
	for ($i=0;$i<mysql_num_rows($result);$i++)
	{
		$row=mysql_fetch_assoc($result);
		$record['lots'][]=getLotInfo($row['LOTNUMBER'],"",$_GET['clientcode']);
	}
}

if ($_GET['action']=="getTaskData")
{
		$query='select * from tasks left outer join wo ON tasks.id=wo.TASKID where tasks.id="'.$_GET['id'].'" AND wo.DELETED="0"';
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			$row=mysql_fetch_assoc($result);
			$data['taskid']=$row['id'];
			$data['startdate']=$row['startdate'];
			$data['enddate']=$row['enddate'];
			$data['type']=$row['type'];
			$data['workperformedby']=$row['workperformedby'];
			$data['description']=$row['description'];
			$data['wos'][]=$row;
		}
		$record=$data;
}

if ($_GET['action']=="showwtsfortoday")
{
	$query='select wt.*, 
	locations.ID AS LOCID, 
	locations.NAME, 
	locations.LOCATIONTYPE, 
	locations.LAT, 
	locations.LONG, 
	locations.ORGANIC, 
	locations.CLIENTID,
	locations.BIODYNAMIC,
	locations.GATECODE, 
	locations.APPELLATION AS LOCATION_APPELLATION, 
	locations.REGION AS LOCATION_REGION, 
	clients.CLIENTNAME, clients.CODE AS CLIENTCODE, clients.clientid as CLIENTID from wt 
	left outer join clients on (wt.CLIENTCODE=clients.clientid)
	left outer join locations on (wt.VINEYARDID=locations.ID)
	 where DATE(DATETIME)=DATE(NOW()) order by TAGID';
	$result=mysql_query($query);
	$record=get_wt_data($query);
}

if ($_GET['action']=="showwtsforvintage")
{
	$query='select wt.*, 
	locations.ID AS LOCID, 
	locations.NAME, 
	locations.LOCATIONTYPE, 
	locations.LAT, 
	locations.LONG, 
	locations.ORGANIC, 
	locations.CLIENTID,
	locations.BIODYNAMIC,
	locations.GATECODE, 
	locations.APPELLATION AS LOCATION_APPELLATION, 
	locations.REGION AS LOCATION_REGION, 
	clients.CLIENTNAME, clients.CODE AS CLIENTCODE, clients.clientid as CLIENTID from wt 
	left outer join clients on (wt.CLIENTCODE=clients.clientid)
	left outer join locations on (wt.VINEYARDID=locations.ID)
	 where YEAR(DATETIME)="'.$_GET['vintage'].'" order by TAGID';
	$result=mysql_query($query);
	$record=get_wt_data($query);
}

	if ($_GET['action']=="showFacilities")
	{
		$query='select * from locations where LOCATIONTYPE="FACILITY" order by NAME,BONDNUMBER';
		$result=mysql_query($query);
		for ($i=0; $i<mysql_num_rows($result); $i++)
		{
			$row=mysql_fetch_assoc($result);
			$record[]=$row;
		}
	}
	
	if ($_GET['action']=="showwts")
	{
		$query='select wt.*, 
		locations.ID AS LOCID, 
		locations.NAME, 
		locations.LOCATIONTYPE, 
		locations.LAT, 
		locations.LONG, 
		locations.ORGANIC, 
		locations.CLIENTID,
		locations.BIODYNAMIC,
		locations.GATECODE, 
		locations.APPELLATION AS LOCATION_APPELLATION, 
		locations.REGION AS LOCATION_REGION, 
		clients.CLIENTNAME, clients.CODE AS CLIENTCODE, clients.clientid as CLIENTID from wt 
		left outer join clients on (wt.CLIENTCODE=clients.clientid)
		left outer join locations on (wt.VINEYARDID=locations.ID)
		 where CLIENTCODE="'.$_GET['clientcode'].
		'" and YEAR(DATETIME)="'.$_GET['vintage'].'" order by TAGID';
		$result=mysql_query($query);
		$record=get_wt_data($query);

	}
	if ($_GET['action']=="get_wo_data")
	{
		$record['wo']=GetWO($_GET['woid']);
		$record['woid']=$_GET['woid'];
		if ($_GET['oneShort']==1)
			$record['lotinfo']=lotinforecords($record['wo']['data']['LOT'],"WO",$record['wo']['data']['ID'],"",1);
		else
			$record['lotinfo']=lotinforecords($record['wo']['data']['LOT'],"WO",$record['wo']['data']['ID']);
	}
	if ($_GET['action']=="outstandingPOs")
	{		
		$query='SELECT wo.ID FROM wo where TYPE="PRESSOFF" and STATUS!="COMPLETED" and wo.DELETED!=1';
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			$row=mysql_fetch_assoc($result);
			$record[]=GetWO($row['ID']);
		}
	}
	function lotData($lot)
	{
		$query='select * from lots where LOTNUMBER="'.$lot.'"';
		$result=mysql_query($query);
		$row=mysql_fetch_assoc($result);
		return $row;
	}
	function lotMadeUpOfLots($lot,$date,$lotsexamined="")
	{
		if (alreadyexamined($lot,$lotsexamined)==1)
		{
			return;
		}
		$lotsexamined[]=$lot;
		
		$query='select blenditems.SOURCELOT, blenditems.GALLONS, lots.DESCRIPTION, blenditems.DIRECTION, wo.LOT, wo.ID from blenditems left outer join blend on (blenditems.BLENDID=blend.ID) left outer join wo on (blend.WOID=wo.ID) left outer join lots on (blenditems.SOURCELOT=lots.LOTNUMBER) where (wo.LOT="'.$lot.'" or blenditems.SOURCELOT="'.$lot.'") and wo.DUEDATE<="'.$date.'" and wo.DELETED=0';
		  // echo $query;
		  // exit;
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			$row=mysql_fetch_assoc($result);
			if ($row['DIRECTION']=="OUT TO" & $row['SOURCELOT']==$lot)
			{
				$record[]=lotData($row['LOT']);
				$record[count($record)-1]['SUBJECTLOT']=$row['LOT'];
				$record[count($record)-1]['SOURCELOT']=$lot;
				$record[count($record)-1]['GALLONS']=$row['GALLONS'];
				$moreLots=lotMadeUpOfLots($row['LOT'],$date,$lotsexamined);
			}
			if ($row['DIRECTION']=="IN FROM" & $row['LOT']==$lot)
			{
				$record[]=lotData($row['SOURCELOT']);
				$record[count($record)-1]['SUBJECTLOT']=$row['SOURCELOT'];
				$record[count($record)-1]['SOURCELOT']=$lot;
				$record[count($record)-1]['GALLONS']=$row['GALLONS'];
				$moreLots=lotMadeUpOfLots($row['SOURCELOT'],$date,$lotsexamined);
			}
			if ($moreLots != null & $records != null)
			{
				array_merge($records,$moreLots);		
			}
		}
		return $record;
	}
	function lotInputToLots($lot,$date,$lotsexamined="")
	{
		if (alreadyexamined($lot,$lotsexamined)==1)
		{
			return;
		}
		$lotsexamined[]=$lot;
		$query='select blenditems.SOURCELOT, blenditems.GALLONS, lots.DESCRIPTION, blenditems.DIRECTION, wo.LOT, wo.ID from blenditems left outer join blend on (blenditems.BLENDID=blend.ID) left outer join wo on (blend.WOID=wo.ID) left outer join lots on (blenditems.SOURCELOT=lots.LOTNUMBER) where (wo.LOT="'.$lot.'" or blenditems.SOURCELOT="'.$lot.'") and wo.DUEDATE<="'.$date.'" and wo.DELETED=0';
		 // echo $query;
		 // exit;
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			$row=mysql_fetch_assoc($result);
			if ($row['DIRECTION']=="IN FROM" & $row['SOURCELOT']==$lot)
			{
				$record[]=lotData($row['LOT']);
				$record[count($record)-1]['SUBJECTLOT']=$row['LOT'];
				$record[count($record)-1]['SOURCELOT']=$lot;
				$record[count($record)-1]['GALLONS']=$row['GALLONS'];
				$moreLots=lotInputToLots($row['LOT'],$date,$lotsexamined);
			}
			if ($row['DIRECTION']=="OUT TO" & $row['LOT']==$lot)
			{
				$record[]=lotData($row['SOURCELOT']);
				$record[count($record)-1]['SUBJECTLOT']=$row['SOURCELOT'];
				$record[count($record)-1]['SOURCELOT']=$lot;
				$record[count($record)-1]['GALLONS']=$row['GALLONS'];
				$moreLots=lotInputToLots($row['SOURCELOT'],$date,$lotsexamined);
			}
			if ($moreLots != null)
			{
				if (count($records)>0)
					array_merge($records,$moreLots);		
			}
		}
		return $record;
	}
	if ($_GET['action']=="lotInputToLots")
	{
		$record['inputs']=$_REQUEST;
		$record['result']=lotInputToLots($_GET['lot'],date("Y-m-d",strtotime($_GET['asOfDate'])));
	}
	if ($_GET['action']=="lotMadeUpOfLots")
	{
		$record['inputs']=$_REQUEST;
		$record['result']=lotMadeUpOfLots($_GET['lot'],date("Y-m-d",strtotime($_GET['asOfDate'])));
	}
	if ($_GET['action']=="outstandingSCPsForToday")
	{
		$query='SELECT wo.ID,wo.CLIENTCODE FROM wo INNER JOIN scp ON wo.ID = scp.WOID where STATUS!="COMPLETED" and wo.DELETED!=1 and DUEDATE>=CURDATE() and DUEDATE<=DATE_ADD(CURDATE(),INTERVAL 2 DAY)';
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			$row=mysql_fetch_assoc($result);
			if (strlen($row['CLIENTCODE'])>0)
				$record[]=GetWO($row['ID']);
		}
	}
	if ($_GET['action']=="outstandingSCPsAfterToday")
	{
		$query='SELECT wo.ID,wo.CLIENTCODE FROM wo INNER JOIN scp ON wo.ID = scp.WOID where STATUS!="COMPLETED" and wo.DELETED!=1 and DUEDATE>DATE_ADD(CURDATE(),INTERVAL 2 DAY)';
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			$row=mysql_fetch_assoc($result);
			if (strlen($row['CLIENTCODE'])>0)
				$record[]=GetWO($row['ID']);
		}
	}
	if ($_GET['action']=="getfacilityweight")
	{
		$query='SELECT 	clients.CODE, 
			SUM(bindetail.WEIGHT) AS SUM_WEIGHT, 
			SUM(bindetail.TARE) AS SUM_TARE, 
			clients.CLIENTNAME
		FROM wt INNER JOIN bindetail ON wt.ID = bindetail.WEIGHTAG
			 LEFT OUTER JOIN clients ON wt.CLIENTCODE = clients.clientid
		WHERE YEAR(DATETIME) = YEAR(NOW())
		GROUP BY clients.CLIENTNAME, clients.CODE
		ORDER BY SUM_WEIGHT DESC';
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			$row=mysql_fetch_assoc($result);
			$record[$i]=$row;
			$record[$i]['tons']=($row['SUM_WEIGHT']-$row['SUM_TARE'])/2000;
		}		
	}
	
	if ($_GET['action']=="getvineyards")
	{
		$query='select * from locations where CLIENTID="'.$_GET['clientid'].'" and LOCATIONTYPE="VINEYARD" ORDER BY NAME';
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			$row=mysql_fetch_assoc($result);
			$record[]=$row;
		}
	}
	if ($_GET['action']=="showlotinfo")
	{
		$_GET['detail']="YES";
		if ($_GET['lot']!="---")
			$record=getLotInfo($_GET['lot'],$_GET['woid'],$_GET['clientid']);
	}
	
	if ($_GET['action']=="getassetlist")
	{
		$query='select assets.*, assettypes.NAME AS TYPENAME from assets left outer join assettypes on assettypes.ID=assets.TYPEID 
		where HIDDEN="NO" AND (assettypes.ID="6" or assettypes.ID="6") order by assets.NAME, assets.CAPACITY,OWNER';
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			$row=mysql_fetch_assoc($result);
			$record2['CAPACITY']=$row['CAPACITY'];
			$record2['NAME']=$row['NAME'];
			$record2['OWNER']=$row['OWNER'];
			$record2['DESCRIPTION']=$row['DESCRIPTION'];
			$record2['LOCATION']=$row['LOCATION'];
			$record2['ASSETID']=$row['ID'];
			$record2['TYPENAME']=$row['TYPENAME'];
			$record2['CYLINDERHEIGHT']=$row['CYLINDERHEIGHT'];
			$record2['CYLINDERDIAMETER']=$row['CYLINDERDIAMETER'];
			$record[]=$record2;
		}
	}
	if ($_GET['action']=="workorder")
	{
		$record=getWO($_GET['id']);
	}
	if ($_GET['action']=="shownewwos")
	{
		$query='delete from newwos using newwos left join wo on wo.ID=newwos.WOID where wo.DELETED="1"';
		mysql_query($query);
		$query='select WOID from newwos WHERE DEVTOKEN="'.$_GET['devtoken'].'"';
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			$row=mysql_fetch_assoc($result);
			$record[]=getWO($row['WOID']);
		}
	}
	
	if ($_GET['action']=="showoutstandingwos")
	{
		$query='select wo.ID, wo.TASKID from wo left outer join tasks on (wo.TASKID=tasks.id) WHERE DELETED!="1" and wo.TYPE!="SCP" AND wo.STATUS!="COMPLETED" AND wo.TYPE!="PUMP OVER" AND wo.TYPE!="PUNCH DOWN" AND wo.TYPE!="PRESSOFF"';
				
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			$row=mysql_fetch_assoc($result);
			if ($row['TASKID']>0)
			{
				if ($tasks[$row['TASKID']]=="")
				{
					$tasks[$row['TASKID']]=1;
					$test=getWO($row['ID']);
					if ($test['data']['CLIENTNAME']!="")  //IPHONE does not properly handle a case where the clientname and id are null so filtering this out before sending.
						$record[]=getWO($row['ID']);									
				}
			}
			else
			{
				$test=getWO($row['ID']);
				if ($test['data']['CLIENTNAME']!="")  //IPHONE does not properly handle a case where the clientname and id are null so filtering this out before sending.
					$record[]=getWO($row['ID']);													
			}
		}
	}
	if ($_GET['action']=="getstaff")
	{
		$query='select staff from users where deviceid="'.$_GET['deviceid'].'"';
		$result=mysql_query($query);
		$row=mysql_fetch_assoc($result);
		$record=$row;		
	}
	
	if ($_GET['action']=="fermprotocols")
	{
		$query='SELECT DISTINCT lots.LOTNUMBER, lots.DESCRIPTION as LOTDESC, assets.ID, assettypes.NAME as TYPENAME FROM lots 
		INNER JOIN wo ON (lots.LOTNUMBER = wo.LOT) 
		INNER JOIN reservation ON (wo.ID = reservation.WOID) 
		INNER JOIN assets ON (reservation.ASSETID = assets.ID) 
		INNER JOIN assettypes ON (assets.TYPEID = assettypes.ID) 
		WHERE (lots.CLIENTCODE="'.$_GET['clientid'].'") AND (lots.YEAR="'.$_GET['vintage'].'") 
		AND (assets.TYPEID="6" OR assets.TYPEID="7" OR assets.TYPEID="8" OR assets.TYPEID="14") ORDER BY TYPENAME DESC, assets.NAME';
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			$row=mysql_fetch_assoc($result);
			$query2='select assets.*, assettypes.NAME AS TYPENAME from assets left outer join assettypes on assettypes.ID=assets.TYPEID 
					where assets.ID="'.$row['ID'].'"';
			$result2=mysql_query($query2);
			$asset=mysql_fetch_assoc($result2);
			unset($myrecord);
			$myrecord=$row;
			$myrecord['asset']=$asset;
			$assetnumber=explode('-',$asset['NAME']);
			$query3='select STATUS,PDAM,PDNOON,PDPM,POAM,PONOON,POPM,DRYICE,STIR  from fermprot where LOT="'.$row['LOTNUMBER'].'" and VESSELTYPE="'.$asset['TYPENAME'].'" and VESSELID="'.$assetnumber[1].'"';
			$result3=mysql_query($query3);
			if (mysql_num_rows($result3)>0)
			{
				$row3=mysql_fetch_assoc($result3);
				$myrecord['fermprot']['status']=$row3['STATUS'];
				if ($row3['STATUS']=="ACTIVE")
				{
					$myrecord['fermprot']['data']=$row3;
				}
			}
			else
			{
				$myrecord['fermprot']['status']="CLOSED";
			}
			$record[]=$myrecord;
		}
	}
	
	if ($_GET['action']=="activeferms")
	{
		// $slot['MORNING']="AM";
		// $slot['NOON']="NOON";
		// $slot['EVENING']="PM";
		
		$query='select wo.*,date(wo.DUEDATE) as THEDATE, clients.CLIENTNAME from wo left outer join clients on (wo.CLIENTCODE=clients.CODE) where (TYPE="PUMP OVER" or TYPE="PUNCH DOWN") and STATUS="ASSIGNED" order by DUEDATE,LOT,VESSELTYPE,VESSELID';		
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			unset($temp);
			unset($brixtemp);
			$row=mysql_fetch_assoc($result);
			$temp['type']="WO";
			$temp['data']=$row;
//			$temp['slot']=$slot[$row['TIMESLOT']];
			// $key=$row['VESSELID'].'-'.$row['VESSELTYPE'].'-'.$row['THEDATE'];
			// $query2='select * from brixtemp where DATE="'.$row['THEDATE'].
			// 			'" and vessel="'.$row['VESSELID'].
			// 			'" and vesseltype="'.$row['VESSELTYPE'].'"';
			// if (array_key_exists($key, $checked)!=1)
			// {
			// 	$checked[$key]=1;
			// 	$result2=mysql_query($query2);
			// 	for ($j=0;$j<mysql_num_rows($result2);$j++)
			// 	{
			// 		$brixtemp=mysql_fetch_assoc($result2);
			// 	}									
			// 	$temp['brixtemp']=$brixtemp;
			// }
			$record[$row['TYPE']][]=$temp;
		}		
	}
	
	if ($_GET['action']=="brixtemp")
	{
		$vessel=explode('-',$_GET['vessel']);
		$query='select * from brixtemp where LOT="'.$_GET['lot'].
					'" and vessel="'.$vessel[1].
					'" and vesseltype="'.$vessel[0].'" order by DATE';
		$result=mysql_query($query);
		for ($i=0;$i<mysql_num_rows($result);$i++)
		{
			$row=mysql_fetch_assoc($result);
			$record[]=$row;
		}
	}
	
	if ($_GET['action']=="login")
	{
		$query='select clients.*, username, deviceid, users.clientid, staff, users.group as GROUPNUMBER from users left outer join clients on (users.clientid=clients.clientid) where username="'.$_GET['username'].'" and password="'.md5($_GET['password']).'"';
		$result=mysql_query($query);
		if (mysql_num_rows($result)>0)
		{
			$row=mysql_fetch_assoc($result);
			$record=$row;
		}
	}
	
	function showDefaults($query)
	{
		$result=mysql_query($query);
		if (mysql_num_rows($result)>0)
		{
			$row=mysql_fetch_assoc($result);
			if ($row['staff']=="YES")
			{
				$query='select distinct clientid, CLIENTNAME, CODE, lots.YEAR from clients left outer join lots on (lots.CLIENTCODE=clients.clientid) WHERE ACTIVE="YES" order by CLIENTNAME, lots.YEAR desc';				
				$result=mysql_query($query);
			}
			else
			{
				if ($row['GROUPNUMBER']>0)
				{
					$query='select distinct clients.clientid, CLIENTNAME, CODE, lots.YEAR FROM users INNER JOIN groups ON users.`group` = groups.GROUPID
						 INNER JOIN clients ON groups.CLIENTID = clients.clientid left outer join lots on (lots.CLIENTCODE=clients.clientid) WHERE ACTIVE="YES" AND users.group="'.$row['GROUPNUMBER'].'" order by CLIENTNAME, lots.YEAR desc';
					$result=mysql_query($query);				

				}
				else
				{
					$query='select distinct clients.clientid, CLIENTNAME, CODE, lots.YEAR FROM clients left outer join lots on (lots.CLIENTCODE=clients.clientid) 
					WHERE ACTIVE="YES" and clients.clientid="'.$row['clientid'].'" order by CLIENTNAME, lots.YEAR desc';
				//	echo $query; exit;
					$result=mysql_query($query);
				}
			}
			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$row=mysql_fetch_assoc($result);
				$temprecord[$row['CLIENTNAME']]['CLIENTID']=$row['clientid'];
				$temprecord[$row['CLIENTNAME']]['CLIENTNAME']=$row['CLIENTNAME'];
				$temprecord[$row['CLIENTNAME']]['CODE']=$row['CODE'];
				if ($row['YEAR']!="")
					$temprecord[$row['CLIENTNAME']]['VINTAGES'][]=$row['YEAR'];
//				$temprecord[$row['CLIENTNAME']]['MAXYEAR']=
			}
			foreach ($temprecord as $key => $value)
			{
				$maxyear=$temprecord[$key]['VINTAGES'][0];
				// if ($maxyear<date("Y",time()))
				// {
				// 	$temprecord[$key]['VINTAGES'][]=date("Y",time());
				// 	rsort($temprecord[$key]['VINTAGES']);
				// }
			}
		}
		$record=$temprecord;
		return $record;
	}
	
	if ($_GET['action']=="defaults")
	{
		if ($_GET['deviceid']!="cellarworx")
			$query='select clientid, staff, users.group as GROUPNUMBER from users where deviceid="'.$_GET['deviceid'].'"';
		else
			$query='select clientid, staff, users.group as GROUPNUMBER from users where username="'.$_GET['username'].'"';
			
		$record=showDefaults($query);
	}
	
	if ($_GET['debug']==1)
	{
		echo '<pre>';
		echo $myt->getTime();
		echo '<br>';
		print_r($record);
		exit;
	}
	$output = $json->encode($record);
	print $output;
	
?>
