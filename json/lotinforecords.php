<?php
define('APPKEY','HVHWF4NpRHGvMHrsyEkWuA'); 
define('PUSHSECRET', 'RePn8gAMR52zp5HUChw7Qg'); 
define('PUSHURL', 'https://go.urbanairship.com/api/push/'); 
define('QUERYBADTOKENSURL', 'https://go.urbanairship.com/api/device_tokens/feedback/?since=2010-06-01+00:00:00'); 

//$APP_MASTER_SECRET = 'RePn8gAMR52zp5HUChw7Qg';
//$APP_KEY = 'HVHWF4NpRHGvMHrsyEkWuA';
$APP_MASTER_SECRET = 'dUCudu5nQAO2svq2Awmqng';
$APP_KEY = 'TMXhdpjcSlmKCZO3zkyrpg';

// Create Airship object

function getBadgeDataForDevToken($devToken)
{
	$query='select distinct * from badges where devToken="'.$devToken.'"';
	// echo $query;
	// exit;
	$result=mysql_query($query);
	if (mysql_num_rows($result)==0)
	{
		$query2='insert into badges set devToken="'.$devToken.'"';
		$result2=mysql_query($query2);
		$result=mysql_query($query);
	}
	$row=mysql_fetch_assoc($result);
	return $row;
}

function incrementBadgeForDevToken($devToken, $badgeName)
{
	$query='insert into badges set badges.'.$badgeName.'="1", devToken="'.$devToken.'" on duplicate key update badges.'.$badgeName.'=badges.'.$badgeName.'+1';
	$result=mysql_query($query);
}
function incrementBadge($message,$badgeName,$excludeDevToken)
{
	$APP_MASTER_SECRET = 'dUCudu5nQAO2svq2Awmqng';
	$APP_KEY = 'TMXhdpjcSlmKCZO3zkyrpg';
	$airship = new Airship($APP_KEY, $APP_MASTER_SECRET);
	
	$query='select distinct devToken from pushDevices where '.$badgeName.'="YES" and devToken!="'.$excludeDevToken.'"';
	$result=mysql_query($query);
	for ($i=0; $i<mysql_num_rows($result); $i++)
	{
		$row=mysql_fetch_assoc($result);
		incrementBadgeForDevToken($row['devToken'],$badgeName);
		$devtokens[]=$row['devToken'];
	}
	$message['aps']['badge']="+1";
	$message['notice']['sl']="update";
//	$excludeTokens[]=$excludeDevToken;
	// echo '<pre>';
	// print_r($message);
	// print_r($devtokens);
	// exit;
	$airship->push($message,$devtokens,$excludeTokens);			
}

function clearBadge($devToken, $badgeName)
{
	$APP_MASTER_SECRET = 'dUCudu5nQAO2svq2Awmqng';
	$APP_KEY = 'TMXhdpjcSlmKCZO3zkyrpg';
	$airship = new Airship($APP_KEY, $APP_MASTER_SECRET);
	
	$query='insert into badges set '.$badgeName.'="0", devToken="'.$devToken.'" on duplicate key update badges.'.$badgeName.'="0"';	
	$result=mysql_query($query);
	$query='select * from badges where devToken="'.$devToken.'"';
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	$badgeCount=0;
	for ($i=1;$i<count($row);$i++)
	{
		$badgeCount+=$row[$i];
	}	
	$devTokens[]=$devToken;
	$message['aps']['badge']=$badgeCount;
	$message['notice']['sl']="clearBadge";
	// echo '<pre>';
	// print_r($message);
	// print_r($devTokens);
	// exit;
	$airship->push($message,$devTokens);			
}


function sendPush($devtokens,$message,$badgeCount,$sound)
{
	$devices=$devtokens;
	$contents = array(); 
	if (count($message)>0)
		$contents['alert'] = $message; 
	if (count($sound)>0)
		$contents['sound'] = $sound; 
	$push = array("alias" => $message); 
	if ($devices) 
	   $push["device_tokens"] = $devices; 
	$json = json_encode($push); 
	$session = curl_init(PUSHURL); 
	curl_setopt($session, CURLOPT_USERPWD, APPKEY . ':' . PUSHSECRET); 
	curl_setopt($session, CURLOPT_POST, True); 
	curl_setopt($session, CURLOPT_POSTFIELDS, $json); 
	curl_setopt($session, CURLOPT_HEADER, False); 
	curl_setopt($session, CURLOPT_RETURNTRANSFER, True); 
	curl_setopt($session, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
	curl_exec($session); 
	// Check if any error occured 
	$response = curl_getinfo($session); 
	curl_close($session);		
}
function registerDevToken($devtokens)
{
	$devices=$devtokens;
	$contents = array(); 
	$push = array("alias" => "klm4"); 
	if ($devices) 
	   $push["device_tokens"] = $devices; 
	$json = json_encode($push); 
	echo '<pre>';
	echo "Sending...";
	print_r($push);
	$session = curl_init(PUSHURL); 
	curl_setopt($session, CURLOPT_USERPWD, APPKEY . ':' . PUSHSECRET); 
	curl_setopt($session, CURLOPT_POST, True); 
	curl_setopt($session, CURLOPT_POSTFIELDS, $json); 
	curl_setopt($session, CURLOPT_HEADER, False); 
	curl_setopt($session, CURLOPT_RETURNTRANSFER, True); 
	curl_setopt($session, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
	curl_exec($session); 
	// Check if any error occured 
	$response = curl_getinfo($session); 
	echo "...Received";
	print_r($response);
	curl_close($session);		
}
function queryBadTokens()
{
	$devices=$devtokens;
	$contents = array(); 
	$json = json_encode($push); 
	echo '<pre>';
	echo "Sending...";
	print_r($push);
	$session = curl_init(QUERYBADTOKENSURL); 
	curl_setopt($session, CURLOPT_USERPWD, APPKEY . ':' . PUSHSECRET); 
	curl_setopt($session, CURLOPT_POST, False); 
//	curl_setopt($session, CURLOPT_POSTFIELDS, $json); 
	curl_setopt($session, CURLOPT_HEADER, False); 
	curl_setopt($session, CURLOPT_RETURNTRANSFER, True); 
	curl_setopt($session, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
	curl_exec($session); 
	// Check if any error occured 
	$response = curl_getinfo($session); 
	echo "...Received";
	print_r($response);
	curl_close($session);		
}
function debug($val)
{
	echo '<pre>';
	print_r($val);
	echo '</pre>';
}

function getTaskData($taskid)
{
	$query='select * from tasks where tasks.id="'.$taskid.'"';
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	return $row;
}

function getNextLotNumber($year,$clientid)
{
	$query='select * from lots where YEAR="'.$year.'" and CLIENTCODE="'.$clientid.'" order by LOTNUMBER DESC';
	$result=mysql_query($query);
	$max=0;
	for ($i=0;$i<mysql_num_rows($result);$i++)
	{
		$row=mysql_fetch_array($result);
		$firstrow=explode("-",$row['LOTNUMBER']);
//		echo $firstrow[2].'<br>';
		if ((int)$firstrow[2]>$max)
			$max=(int)$firstrow[2];
	}
	$max=$max+1;
	// echo $max;
	// exit;
	return $max;
}

function getclientcode($clientid)
{
    $query='select CODE from clients where clientid="'.$clientid.'"';
    $result=mysql_query($query);
    $row=mysql_fetch_array($result);
    return $row['CODE'];
}

function getClientIDAndNameFromClientcode($clientcode)
{
	$query='select * from clients where (CLIENTCODE="'.$clientcode.'")';
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	$result['clientcode']=$clientcode;
	$result['clientid']=$row['clientid'];
	$result['clientname']=$row['CLIENTNAME'];
	return $result;
}

function bblhistory($bblnumber, $date, $clientcode)
{
	$query='SELECT DISTINCT barrelhistory.BARRELNUMBER, wo.ID, wo.LOT, wo.TYPE, barrels.CAPACITY, barrelhistory.STATUS, barrelhistory.DIRECTION, UNIX_TIMESTAMP(wo.ENDDATE) AS THEENDDATE FROM  `barrelhistory`
          INNER JOIN barrels on (barrelhistory.BARRELNUMBER=barrels.NUMBER)
          INNER JOIN `wo` ON (`barrelhistory`.`WOID` = `wo`.`ID`) WHERE wo.CLIENTCODE="'.$clientcode.'" AND barrelhistory.BARRELNUMBER="'.$bblnumber.'" ORDER BY wo.ENDDATE';
	$query2='SELECT DISTINCT barrelhistory.DIRECTION AS BARRELDIRECTION, wo.ID, wo.TYPE, blenditems.SOURCELOT, blenditems.DIRECTION AS BLENDDIRECTION,
            barrelhistory.BARRELNUMBER, UNIX_TIMESTAMP(wo.ENDDATE) AS THEENDDATE, barrelhistory.STATUS FROM  `barrelhistory`
           INNER JOIN `blend` ON (`barrelhistory`.`WOID` = `blend`.`WOID`)
           INNER JOIN wo ON (wo.ID=blend.WOID)
           INNER JOIN barrels on (barrelhistory.BARRELNUMBER=barrels.NUMBER)
           INNER JOIN `blenditems` ON (`blend`.`ID` = `blenditems`.`BLENDID`)
        WHERE  wo.CLIENTCODE="'.$clientcode.'" AND (barrelhistory.BARRELNUMBER="'.$bblnumber.'")';
	//echo $query;
	
	$result=mysql_query($query);
	$result2=mysql_query($query2);
	
	for ($i=0;$i<mysql_num_rows($result);$i++)
	{
		$row=mysql_fetch_array($result);
		$temp['type']="STANDARD";
		$temp['row']=$row;
		$temp['date']=$row['THEENDDATE'];
		$data[]=$temp;
	}
	
	for ($i=0;$i<mysql_num_rows($result2);$i++)
	{
		$row=mysql_fetch_array($result2);
		$temp['type']="BLEND";
		$temp['row']=$row;
		$temp['date']=$row['THEENDDATE'];
		$data[]=$temp;
	}
	if (count($data)>1)
	$sorted=0;
	else
	$sorted=1;
	
	$reps=0;
	while ($sorted==0)
	{
		for ($i=0;$i<count($data);$i++)
		{
			if ($i == count($data)-1)
			{
				$sorted=1;
				break;
			}
			if ($data[$i+1]['date']<$data[$i]['date'])
			{
				$temp=$data[$i];
				$data[$i]=$data[$i+1];
				$data[$i+1]=$temp;
				break;
			}
		}
	}
	
	
	for ($i=0;$i<count($data);$i++)
	{
		if ($data[$i]['type']=="STANDARD")
		{
			$row=$data[$i]['row'];
			if ($date>=$row['THEENDDATE'])
			{
				if ($row['DIRECTION']=="IN")
				{
					$bbls['lot']=$row['LOT'];
				}
				else
				{
					$bbls['lot']="";
				}
			}
			$bbls['direction']=$row['DIRECTION'];
			
		}
		else
		{
			$row=$data[$i]['row'];
			if ($date>=$row['THEENDDATE'])
			{
				if ($row['BLENDDIRECTION']=="OUT TO")
				{
					if ($row['BARRELDIRECTION']=="OUT")
					{
						$bbls['lot']=$row['SOURCELOT'];
					}
					else
					{
						$bbls['lot']="";
					}
				}
				else
				{
					if ($row['BARRELDIRECTION']=="IN")
					{
						$bbls['lot']=$row['SOURCELOT'];
					}
					else
					{
						$bbls['lot']="";
					}
				}
			}
			if ($row['BARRELDIRECTION']=="IN")
			$bbls['direction']="OUT";
			else
			$bbls['direction']="IN";
			
		}
		$bbls['status']=$row['STATUS'];
		$bbls['date']=$row['THEENDDATE'];
		$bbls['woid']=$row['ID'];
		$bbls['type']=$row['TYPE'];
		$history[]=$bbls;
	}
	return $history;
	
}
function bblsinlot($lot, $date)
{
	$query='SELECT barrelhistory.BARRELNUMBER, barrels.NUMBER, barrels.CAPACITY, barrelhistory.STATUS, barrelhistory.DIRECTION, UNIX_TIMESTAMP(wo.ENDDATE) AS THEENDDATE FROM  `barrelhistory`
          INNER JOIN barrels on (barrelhistory.BARRELNUMBER=barrels.ID)
          INNER JOIN `wo` ON (`barrelhistory`.`WOID` = `wo`.`ID`) WHERE wo.LOT="'.$lot.'" ORDER BY wo.ENDDATE';
	$query2='SELECT barrelhistory.DIRECTION AS BARRELDIRECTION, blenditems.DIRECTION AS BLENDDIRECTION,
            barrelhistory.BARRELNUMBER, UNIX_TIMESTAMP(wo.ENDDATE) AS THEENDDATE, barrelhistory.STATUS FROM  `barrelhistory`
           INNER JOIN `blend` ON (`barrelhistory`.`WOID` = `blend`.`WOID`)
           INNER JOIN wo ON (wo.ID=blend.WOID)
           INNER JOIN `blenditems` ON (`blend`.`ID` = `blenditems`.`BLENDID`)
        WHERE  (blenditems.SOURCELOT = "'.$lot.'")';
	//  echo $query.'<br>';
	//  echo $query2;
	$result=mysql_query($query);
	$result2=mysql_query($query2);
	
	for ($i=0;$i<mysql_num_rows($result2);$i++)
	{
		$row=mysql_fetch_array($result2);
		$temp['type']="BLEND";
		$temp['row']=$row;
		$temp['date']=$row['THEENDDATE'];
		$data[]=$temp;
	}
	for ($i=0;$i<mysql_num_rows($result);$i++)
	{
		$row=mysql_fetch_array($result);
		$temp['type']="STANDARD";
		$temp['row']=$row;
		$temp['date']=$row['THEENDDATE'];
		$data[]=$temp;
	}
	
	if (count($data)>1)
	$sorted=0;
	else
	$sorted=1;
	
	$reps=0;
	while ($sorted==0)
	{
		for ($i=0;$i<count($data);$i++)
		{
			if ($i == count($data)-1)
			{
				$sorted=1;
				break;
			}
			if ($data[$i+1]['date']<$data[$i]['date'])
			{
				$temp=$data[$i];
				$data[$i]=$data[$i+1];
				$data[$i+1]=$temp;
				break;
			}
		}
	}
	
	//  echo '<pre>';
	//  print_r($data);
	//  echo '</pre>';
	//  exit;
	for ($i=0;$i<count($data);$i++)
	{
		if ($data[$i]['type']=="STANDARD")
		{
			$row=$data[$i]['row'];
			if (strtotime($date)>=$row['THEENDDATE'])
			{
				if ($row['DIRECTION']=="IN")
				{
					$bbls['bbls'][$row['BARRELNUMBER']]['status']=$row['STATUS'];
					$bbls['bbls'][$row['BARRELNUMBER']]['name']=$row['NUMBER'];
				}
				else
				{
					unset($bbls['bbls'][$row['BARRELNUMBER']]);
				}
				$bbls['volume']+=$row['CAPACITY'];
			}
		}
		else
		{
			$row=$data[$i]['row'];
			if (strtotime($date)>=$row['THEENDDATE'])
			{
				if ($row['BLENDDIRECTION']=="OUT TO")
				{
					if ($row['BARRELDIRECTION']=="OUT")
					{
						$bbls['bbls'][$row['BARRELNUMBER']]['status']=$row['STATUS'];
						$bbls['bbls'][$row['BARRELNUMBER']]['name']=$row['NUMBER'];
					}
					else
					{
						unset($bbls['bbls'][$row['BARRELNUMBER']]);
					}
				}
				else
				{
					if ($row['BARRELDIRECTION']=="IN")
					{
						$bbls['bbls'][$row['BARRELNUMBER']]['status']=$row['STATUS'];
						$bbls['bbls'][$row['BARRELNUMBER']]['name']=$row['NUMBER'];
					}
					else
					{
						unset($bbls['bbls'][$row['BARRELNUMBER']]);
					}
				}
				$bbls['volume']+=$row['CAPACITY'];
			}
		}
	}
	//  echo '<pre>';
	//  print_r($bbls);
	//  exit;
	if (count($bbls['bbls'])>0)
	ksort($bbls['bbls']);
	return $bbls;
}

function checkrule($lot,$cc, $rule)
{
	$query='select * from flags where CLIENTID="'.clientid($cc).'" and RULE="'.$rule.'"';
	//echo $query;
	$result=mysql_query($query);
	if (mysql_num_rows($result)>0)
	{
		$row=mysql_fetch_array($result);
		switch ($row['RULE'])
		{
			case 'TOPPING FREQUENCY' :
			{
				$days=$row['VALUE'];
				$today=time();
				$query='select wo.ENDDATE from wo where LOT="'.$lot.'" and (wo.TYPE="TOPPING" or wo.TYPE="RACKING") order by wo.ENDDATE DESC limit 1';
				$result=mysql_query($query);
				if (mysql_num_rows($result)>0)
				{
					$row=mysql_fetch_array($result);
					$lasttime=strtotime($row['ENDDATE']);
					$daydifference=($today-$lasttime)/86400;
					//echo $query;
					//echo $today.'  '.$lasttime;
					if ($daydifference>$days)
					{
						return 1;
					}
					else
					{
						return 0;
					}
				}
				else
				{
					return 1;
				}
				break;
			}
			case 'SULPHUR TEST FREQUENCY' :
			{
				$days=$row['VALUE'];
				$today=time();
				$query='select wo.ENDDATE from labresults
                         inner join labtest on (labtest.ID=labresults.LABTESTID) 
                         inner join wo on (labtest.WOID=wo.ID) 
                         where (wo.LOT="'.$lot.'" and wo.TYPE="TOPPING" and labresults.LABTEST="SULPHUR") 
                         order by wo.ENDDATE DESC limit 1';
				//echo $query;
				$result=mysql_query($query);
				if (mysql_num_rows($result)>0)
				{
					$row=mysql_fetch_array($result);
					$lasttime=strtotime($row['ENDDATE']);
					$daydifference=($today-$lasttime)/86400;
					//echo $query;
					//echo $today.'  '.$lasttime;
					if ($daydifference>$days)
					{
						return 1;
					}
					else
					{
						return 0;
					}
				}
				else
				{
					return 1;
				}
				break;
			}
			case 'GLUFRU LIMIT' :
			{
				$limit=$row['VALUE'];
				$query='select labresults.VALUE1 from labresults
                         inner join labtest on (labtest.ID=labresults.LABTESTID) 
                         inner join wo on (labtest.WOID=wo.ID) 
                         where (wo.LOT="'.$lot.'" and labresults.LABTEST="GLUFRU") 
                         order by wo.ENDDATE DESC limit 1';
				//echo $query;
				$result=mysql_query($query);
				if (mysql_num_rows($result)>0)
				{
					$row=mysql_fetch_array($result);
					$curvalue=$row['VALUE1'];
					if ($curvalue<=$limit)
					{
						return 1;
					}
					else
					{
						return 0;
					}
				}
				else
				{
					return 0;
				}
				break;
			}
			case 'MALIC ACID LIMIT' :
			{
				$limit=$row['VALUE'];
				$query='select labresults.VALUE1 from labresults
                         inner join labtest on (labtest.ID=labresults.LABTESTID) 
                         inner join wo on (labtest.WOID=wo.ID) 
                         where (wo.LOT="'.$lot.'" and labresults.LABTEST="MALIC ACID") 
                         order by wo.ENDDATE DESC limit 1';
				//echo $query;
				$result=mysql_query($query);
				if (mysql_num_rows($result)>0)
				{
					$row=mysql_fetch_array($result);
					$curvalue=$row['VALUE1'];
					if ($curvalue<=$limit)
					{
						return 1;
					}
					else
					{
						return 0;
					}
				}
				else
				{
					return 0;
				}
				break;
			}
			case 'NEVER TOPPED' :
			{
				$days=$row['VALUE'];
				$today=time();
				$query='select wo.ENDDATE from wo where LOT="'.$lot.'" and wo.TYPE="TOPPING" order by wo.ENDDATE DESC limit 1';
				$result=mysql_query($query);
				if (mysql_num_rows($result)==0)
				{
					return 1;
				}
				else
				{
					return 0;
				}
				break;
			}
		}
	}
	else
	{
		return 0;
	}
}

function lotinvessels($lot)
{
  if (strlen($lot)<5)
    return $vessel;
  else
  {
	$query='SELECT  distinct `lots`.`LOTNUMBER`, `assets`.`NAME` FROM
  `lots`  INNER JOIN `wo` ON (`lots`.`LOTNUMBER` = `wo`.`LOT`)
  INNER JOIN `scp` ON (`wo`.`ID` = `scp`.`WOID`)
  LEFT OUTER JOIN `reservation` ON (`scp`.`WOID` = `reservation`.`WOID`)
  INNER JOIN `assets` ON (`reservation`.`ASSETID` = `assets`.`ID`)
WHERE
  (`lots`.`LOTNUMBER` = "'.$lot.'")
ORDER BY
  `assets`.`NAME`';
	$result=mysql_query($query);
	for ($i=0;$i<mysql_num_rows($result);$i++)
	{
		$row=mysql_fetch_array($result);
		$vessel[]=$row['NAME'];
	}
	return $vessel;
	}
}


function initiallabanalysis($lot)
{
	$query='select labresults.LABTEST, labresults.VALUE1, labresults.UNITS1, wo.ENDDATE, labresults.COMMENT from labresults inner join labtest on (labresults.LABTESTID=labtest.ID)
        inner join wo on (labtest.WOID=wo.ID) WHERE wo.LOT="'.$lot.'"'.' ORDER BY wo.ENDDATE DESC';
	$result=mysql_query($query);
	for ($i=0;$i<mysql_num_rows($result);$i++)
	{
		$row=mysql_fetch_array($result);
		$labresult[$row['LABTEST']]['value']=$row['VALUE1'];
		$labresult[$row['LABTEST']]['units']=$row['UNITS1'];
		$labresult[$row['LABTEST']]['comment']=$row['COMMENT'];
		$labresult[$row['LABTEST']]['date']=$row['ENDDATE'];
	}
	return $labresult;	
}

function thelabresults($woid)
{
	$query='select labresults.LABTEST, labresults.VALUE1, labresults.UNITS1, wo.ENDDATE, labresults.COMMENT from labresults inner join labtest on (labresults.LABTESTID=labtest.ID)
        inner join wo on (labtest.WOID=wo.ID) WHERE wo.ID="'.$woid.'"';
	$result=mysql_query($query);
	for ($i=0;$i<mysql_num_rows($result);$i++)
	{
		$row=mysql_fetch_array($result);
		$labresult[$row['LABTEST']]['value']=$row['VALUE1'];
		$labresult[$row['LABTEST']]['units']=$row['UNITS1'];
		$labresult[$row['LABTEST']]['comment']=$row['COMMENT'];
		$labresult[$row['LABTEST']]['date']=$row['ENDDATE'];
	}
	return $labresult;
	
}

function currentlabanalysis($lot)
{
	$query='select labresults.LABTEST, labresults.VALUE1, labresults.UNITS1, wo.ENDDATE, labresults.COMMENT from labresults inner join labtest on (labresults.LABTESTID=labtest.ID)
        inner join wo on (labtest.WOID=wo.ID) WHERE wo.LOT="'.$lot.'"'.' ORDER BY wo.ENDDATE';
	$result=mysql_query($query);
	for ($i=0;$i<mysql_num_rows($result);$i++)
	{
		$row=mysql_fetch_array($result);
		$labresult[$row['LABTEST']]['value']=$row['VALUE1'];
		$labresult[$row['LABTEST']]['units']=$row['UNITS1'];
		$labresult[$row['LABTEST']]['comment']=$row['COMMENT'];
		$labresult[$row['LABTEST']]['date']=$row['ENDDATE'];
	}
	return $labresult;
}
function addgallons($structure, $gallons)
{
	$totgallons=0;
	
	if (count($structure)>0)
	{
		foreach ($structure['year'] as $key=>$value)
		{
			$totgallons+=$value;
		}
		foreach ($structure as $key => $value)
		{
			foreach ($value as $key2 => $value2)
			{
				if ($totgallons>0)
				{
					$structure[$key][$key2]+=($structure[$key][$key2]/$totgallons)*$gallons;
				}
			}
		}
	}
	
	return $structure;
}
function addstructure2($receivinglot, $fromlot, $gallons=0)
{
//	echo '<pre>';
//	echo $gallons.'<br>';
//	echo 'Target: ';
//	print_r($receivinglot);
//	echo 'Source:';
//	print_r($fromlot);
	
	// $totalfromgallons=0;
	// if (count($fromlot)>0)
	// {
	// 	foreach ($fromlot['year'] as $key => $value)
	// 	{
	// 		$totalfromgallons+=$value;
	// 	}
	// 
	// 	foreach ($fromlot as $key => $value)
	// 	{
	// 		foreach ($value as $key2 => $value2)
	// 		{
	// 			if ($totalfromgallons!=0)
	// 			{
	// 				$receivinglot[$key][$key2]+=$gallons * ($value2/$totalfromgallons);
	// 			}
	// 			else
	// 			{
	// 				$receivinglot[$key][$key2]+=$gallons;				
	// 			}
	// 		}
	// 	}
	// }
//	echo 'Result - ';
//	print_r($receivinglot);
	return $receivinglot;
}



function addtostructure ($structure, $year, $variety, $appellation, $vineyard, $gallons)
{
	$structure['year'][date("Y",$year)]+=$gallons;
	$structure['variety'][$variety]+=$gallons;
	$structure['appellation'][$appellation]+=$gallons;
	$structure['vineyard'][$vineyard]+=$gallons;
//	echo adding.' '.date("Y",$year).' '.$variety.' '.$appellation.' '.$vineyard.' '.$gallons.'<br>';
	return $structure;
}

function printelement($structure, $column)
{
	$rs='';
	if ($structure!="")
	{
		$totgallons=0;
		foreach ($structure[$column] as $key => $value)
		{
			$totgallons+=$value;
		}
		foreach ($structure[$column] as $key => $value)
		{
			if ($totgallons>0)
			{
				$rs=$rs.$key . ' '. number_format($value,0).' - '. number_format($value/$totgallons*100,0).'%<br>';
			}
			else
			{
				$rs=$rs.$key .' 0%<br>';
			}
		}
	}
	return $rs;
}

function showstructure ($structure,$titles=1)
{
//	echo '<pre>';
//	print_r($structure);
	
	$rs='';
	$totgallons=0;
	if (count($structure)>0)
	{
		foreach ($structure['year'] as $key=>$value)
		{
			$totgallons+=$value;
		}
		$rs=$rs. '<table border=1 width = 100% align=center>';
		if ($titles==1)
		{
			$rs=$rs.  '<tr><td colspan=4 align=center>LOT STRUCTURE ('.number_format($totgallons,2).' - GALLONS) </td></tr>';
			$rs=$rs.  '<tr>';
		}
		$rs=$rs.  '<td align=center>YEAR</td>';
		$rs=$rs.  '<td align=center>VARIETY</td>';
		$rs=$rs.  '<td align=center>APPELLATION</td>';
		$rs=$rs.  '<td align=center>VINEYARD</td>';
		$rs=$rs.  '</tr>';
		$rs=$rs.  '<tr>';
	//	echo '<pre>'; print_r($structure);
		$rs=$rs.  '<td align=center>'.printelement($structure, 'year').'</td>';
		$rs=$rs.  '<td align=center>'.printelement($structure, 'variety').'</td>';
		$rs=$rs.  '<td align=center>'.printelement($structure, 'appellation').'</td>';
		$rs=$rs.  '<td align=center>'.printelement($structure, 'vineyard').'</td>';
		$rs=$rs.  '</tr>';
		$rs=$rs.  '</table>';
	}
	return $rs;
}
	function isanscp($x)
	{
		if ($x['type']=="WO")
		{
			if ($x['data']['TYPE']=="SCP")
			  return 1;
			  else
			  return 0;
		}
		else
		return 0;
		
	}
	function isbottling($x)
	{
		if ($x['type']=="WO")
		{
			if ($x['data']['TYPE']=="BOTTLING")
			  return 1;
			  else
			  return 0;
		}
		else
		return 0;
		
	}
function set_state($record, $index, $tostate, $debug=0)
{
	if ($index>0)
	{
		switch ($record[$index]['type'])
		{
			case "WO" :
			{
				if ($record[$index]['data']['THEDATE']>time())
				{
					return $record[$index-1]['end_state'];
				}
				else
				{
					return $tostate;
				}
				break;
			}
			default:
			return $tostate;
		}
		
	}
	return $tostate;
}
	function getWO($id)
	{
		$ampm['']="";
		$ampm['MORNING']="AM";
		$ampm['NOON']="NOON";
		$ampm['EVENING']="PM";
		
		$myt2=new Timer();
		$myt2->startTimer();		
		// $query='select wo.ID, lots.DESCRIPTION as LOTDESCRIPTION, wo.TASKID, wo.COST, ENDINGTANKGALLONS, ENDINGBARRELCOUNT, ENDINGTOPPINGGALLONS, DUEDATE,TYPE,STATUS,LOT,WORKPERFORMEDBY, wo.CLIENTCODE, OTHERDESC,STARTSLOT, clients.*,clients.clientid as CLIENTID from wo LEFT OUTER JOIN lots ON (wo.LOT = lots.LOTNUMBER) left outer join clients on (wo.CLIENTCODE=clients.CODE) where wo.ID="'.$id.'"';
		$query='select wo.*, clients.*,clients.clientid as CLIENTID, wo.TASKID as id, lots.DESCRIPTION as LOTDESCRIPTION from wo LEFT OUTER JOIN lots ON (wo.LOT = lots.LOTNUMBER) left outer join clients on (wo.CLIENTCODE=clients.CODE) where wo.ID="'.$id.'"';
			
		$result=mysql_query($query);
		
		$record['type']="WO";
		$record['data']=mysql_fetch_assoc($result);
		if ($record['data']['TYPE']=="BLENDING")
		{
			$query='SELECT blenditems.SOURCELOT, 
				blenditems.GALLONS, 
				blenditems.DIRECTION, 
				blenditems.COMMENT, 
				lots.DESCRIPTION as SOURCELOTDESCRIPTION,
				wo.ID
			FROM wo INNER JOIN blend ON wo.ID = blend.WOID
			 INNER JOIN blenditems ON blend.ID = blenditems.BLENDID
				LEFT OUTER JOIN lots ON (blenditems.SOURCELOT=lots.LOTNUMBER)
			where wo.ID="'.$record['data']['ID'].'"';
			// echo $query;
			// exit;
		// $query='SELECT blenditems.SOURCELOT, 
		// 	blenditems.GALLONS, 
		// 	blenditems.DIRECTION, 
		// 	blenditems.COMMENT, 
		// 	wo.ID
		// FROM wo INNER JOIN blend ON wo.ID = blend.WOID
		// 	 INNER JOIN blenditems ON blend.ID = blenditems.BLENDID
		// where wo.ID="'.$record['data']['ID'].'"';
			$result=mysql_query($query);
			for ($j=0;$j<mysql_num_rows($result);$j++)
			{
				$row=mysql_fetch_assoc($result);
				$record['data']['blend'][]=$row;
			}
		}
		if ($record['data']['TASKID']>0)
		{
			$record['task']=getTaskData($record['data']['TASKID']);
		}
		if ($record['data']['TYPE']=="LAB TEST")
		{
			$query='select ID,LAB,LABTESTNUMBER from labtest where WOID="'.$id.'"';
			$result=mysql_query($query);
			if (mysql_num_rows($result)>0)
			{
				$row=mysql_fetch_assoc($result);
				$lab=$row['LAB'];
				$labr=$row['LABTESTNUMBER'];
				$query='select labresults.*,validlabtests.UNITS from labresults left outer join validlabtests on (validlabtests.LABTEST=labresults.LABTEST)  where LABTESTID="'.$row['ID'].'"';
				$result=mysql_query($query);
				unset($testresults);
				for ($j=0; $j<mysql_num_rows($result); $j++)
				{
					$testresults[]=mysql_fetch_assoc($result);
				}
				$record['data']['labtest']['lab']=$lab;
				$record['data']['labtest']['LABTESTNUMBER']=$labr;
				$record['data']['labtest']['LABTESTID']=$row['ID'];
				$record['data']['labtest']['results']=$testresults;					
			}
		}
		if ($record['data']['TYPE']=="PRESSOFF")
		{
			$vessels=lotinvessels($record['data']['LOT']);
			for ($i=0; $i<count($vessels); $i++)
			{
				$record['data']['assets'][]=$vessels[$i];
			}
		}
		
		if ($record['data']['TYPE']=="SCP")
		{
			$query='select * from scp where WOID="'.$id.'"';
			$result=mysql_query($query);
			if (mysql_num_rows($result)==0)
			{
				$query='insert into scp set WHOLECLUSTER=0, HANDSORTING="YES", CRUSHING="NOCRUSHING", ESTTONS="0", WOID="'.$id.'"';
				mysql_query($query);
				$query='select * from scp where WOID="'.$id.'"';
				$result=mysql_query($query);					
			}
			for ($j=0;$j<mysql_num_rows($result);$j++)
			{
				$row=mysql_fetch_assoc($result);
				$record['data']['scp']=$row;
				$record['data']['scp']['DELIVERYDATE']=$record['data']['DUEDATE'];
				
				$query2='select * from locations where ID="'.$row['VINEYARDID'].'"';
				$result2=mysql_query($query2);
				if (mysql_num_rows($result2)>0)
				{
					$row2=mysql_fetch_assoc($result2);
				}
				$record['data']['scp']['vineyard']=$row2;
			}
		}
		$query='select reservation.ID, reservation.tonsInVessel, reservation.binCount, ASSETID,assets.NAME AS NAME,DESCRIPTION,assets.CAPACITY, OWNER, assettypes.NAME as TYPENAME FROM reservation LEFT OUTER JOIN assets ON reservation.ASSETID=assets.ID
		 		 left outer join assettypes on assets.TYPEID=assettypes.ID where WOID="'.$id.'"';
		$result=mysql_query($query);
		for ($j=0;$j<mysql_num_rows($result);$j++)
		{
			$row=mysql_fetch_assoc($result);
			$record['data']['assets'][]=$row;
		}
		$record['data']['STARTSLOT']=$ampm[$record['data']['STARTSLOT']];
		return $record;
	}

function alreadyexamined($lot, $thelots)
{
	for ($i=0;$i<count($thelots);$i++)
	{
		if ($lot==$thelots[$i])
		{
			return 1;
		}
	}
	return 0;
}
function cmp($a,$b)
{
	$adate=strtotime(date("Y-m-d",strtotime($a['data']['DUEDATE'])));
	$bdate=strtotime(date("Y-m-d",strtotime($b['data']['DUEDATE'])));
	if ($adate==$bdate)
	{
		if (($a['data']['TYPE']=="SCP") & ($b['data']['TYPE']=="WT")) return -1;
		if (($a['data']['TYPE']=="WT") & ($b['data']['TYPE']=="SCP")) return 1;
		// echo '<pre>';
		// echo 'S------------';
		// print_r($a);
		// print_r($b);
		// echo '-----------E';
		// if ($b['data']['TYPE']=="SCP") return 1;
		// if ($a['data']['TYPE']=="SCP") return -1;
		// if ($b['type']=="WT") return 1;
		// if ($a['type']=="WT") return -1;
		// if ($a['data']['CREATIONDATE']==$b['data']['CREATIONDATE'])
		// {
		// 	return 0;
		// }
		return ($a['data']['CREATIONDATE'] < $b['data']['CREATIONDATE']) ? -1 : 1;
	}
	return ($adate < $bdate) ? -1 : 1;
}

function addstructure($current, $add, $blendedGallons=0)
{
	$totalGallons=0;

	if (count($add)>0)
	{
		foreach ($add as $category=>$items)
		{
			foreach ($items as $theItem=>$gallons)
			{
				$totalGallons=$totalGallons+$gallons;
			}
			break;
		}		
	}
	// if ($totalGallons==0)
	// {
	// 	echo '<pre>';
	// 	print_r($add);
	// 	exit;
	// }
	$new=$current;
	if (count($add)>0 & $totalGallons>0)
	{
		foreach ($add as $category=>$items)
		{
			foreach ($items as $theItem=>$gallons)
			{
				$new[$category][$theItem]=$new[$category][$theItem]+$gallons*($blendedGallons/$totalGallons);
			}
		}		
	}
	return $new;
}

function lotinforecords ($lot, $type="", $number="", $lotsexamined="", $oneShort=0)
{
	if (alreadyexamined($lot,$lotsexamined)==1)
	{
//		echo 'Circular reference encountered on lot:'.$lot;
		return;
	}
//	echo 'examining lot: '.$lot.'<br>';
	$lotsexamined[]=$lot;

	$query='SELECT wt.ID, wt.LOT, wt.VARIETY, locations.NAME as VINEYARD, locations.APPELLATION, wt.TAGID,
	       UNIX_TIMESTAMP(`wt`.`DATETIME`) AS THEDATE,
	       SUM( bindetail.BINCOUNT ) AS SUM_OF_BINCOUNT,
	       SUM( bindetail.WEIGHT ) AS SUM_OF_WEIGHT,
	       SUM( bindetail.TARE ) AS SUM_OF_TARE
	    FROM wt
	       left OUTER JOIN bindetail ON (bindetail.WEIGHTAG = wt.ID)
	       INNER JOIN lots ON (wt.LOT = lots.LOTNUMBER)
	   	   left outer join locations on (wt.VINEYARDID = locations.ID)
	    WHERE(lots.LOTNUMBER = "'.$lot.'")
	    GROUP BY wt.VARIETY, locations.NAME, locations.APPELLATION, wt.TAGID, wt.DATETIME ORDER BY THEDATE';
	$querywt='SELECT wt.ID, wt.LOT, locations.NAME as VINEYARD,wt.VARIETY, wt.CREATIONDATE, locations.APPELLATION, wt.TAGID, wt.COST, clients.clientid as CLIENTID, clients.CODE as CLIENTCODE, clients.CLIENTNAME,
	       `wt`.`DATETIME` AS DUEDATE,
	UNIX_TIMESTAMP(`wt`.`DATETIME`) AS THEDATE,
	       SUM( bindetail.BINCOUNT ) AS SUM_OF_BINCOUNT,
	       SUM( bindetail.WEIGHT ) AS SUM_OF_WEIGHT,
	       SUM( bindetail.TARE ) AS SUM_OF_TARE
	    FROM bindetail
	       RIGHT OUTER JOIN wt ON (bindetail.WEIGHTAG = wt.ID)
	       INNER JOIN lots ON (wt.LOT = lots.LOTNUMBER)
		left outer join locations on (wt.VINEYARDID = locations.ID)
	   left outer join clients on (wt.CLIENTCODE=clients.clientid)
	    WHERE(lots.LOTNUMBER = "'.$lot.'")
	    GROUP BY wt.VARIETY, locations.NAME, locations.APPELLATION, wt.TAGID, wt.DATETIME ORDER BY DUEDATE';
	$querywo='SELECT DISTINCT `wo`.`TYPE`,`wo`.`ASSIGNEDTO`,wo.SO2ADD, wo.TOPPINGLOT, wo.REQUESTOR, wo.COST, `wo`.`LOT`, clients.CODE AS CLIENTCODE, `clients`.`CLIENTNAME`, wo.CREATIONDATE, wo.ENDDATE, 
	clients.clientid as CLIENTID, `wo`.`STATUS`, `wo`.`WORKPERFORMEDBY`, `wo`.`OTHERDESC`, `wo`.`ID`, `wo`.`VESSELID`, `wo`.`VESSELTYPE`, `wo`.`DURATION`, wo.INVENTORYADJUSTED, 
  `wo`.`RELATEDADDITIONSID`,  `wo`.`STRENGTH`,  `wo`.`COMPLETEDDESCRIPTION`,  `wo`.`ENDINGTANKGALLONS`, IFNULL(ENDINGTANKGALLONS,0) AS TANKGALLONS,  `wo`.`ENDINGBARRELCOUNT`, IFNULL(ENDINGBARRELCOUNT,0) AS BBLCOUNT, wo.CREATIONDATE, 
  `wo`.`ENDINGTOPPINGGALLONS`, IFNULL(ENDINGTOPPINGGALLONS,0) AS TOPPINGGALLONS, wo.DUEDATE as DUEDATE, `wo`.`ENDDATE` AS ENDDATE, tasks.* FROM
  `lots`   INNER JOIN `wo` ON (`lots`.`LOTNUMBER` = `wo`.`LOT`) left outer join clients on (wo.CLIENTCODE=clients.CODE) left outer join tasks on (wo.TASKID=tasks.id) 
   WHERE (lots.LOTNUMBER = "'.$lot.'") and ((wo.DELETED != "1") or (wo.DELETED IS NULL)) and (wo.TYPE!="PUNCH DOWN")and (wo.TYPE!="PUMP OVER") ORDER BY DUEDATE';
				
	$querybol='SELECT bol.ID, bol.ID as BOLID, bol.NAME, bol.BONDED,bol.CREATIONDATE, bolitems.ALC, bolitems.ID AS BOLITEMS_ID, bol.CARRIER, bol.BOND,bol.COST, `bol`.`DIRECTION`, `bolitems`.`LOT`,clients.clientid as CLIENTID,clients.CODE AS CLIENTCODE, `clients`.`CLIENTNAME`,
		locations.ID as LOCATION_ID, locations.NAME, locations.ADDRESS1, locations.ADDRESS2, locations.CITY, locations.STATE, locations.ZIP, locations.LAT, locations.LONG, locations.LOCATIONTYPE as LOCATION_TYPE,
	         `bol`.`DATE` AS DUEDATE,bol.DATE as DATE, bolitems.TYPE, `bolitems`.`GALLONS`, bolitems.ID as bolItemID
	   FROM `bolitems` INNER JOIN `bol` ON (`bolitems`.`BOLID` = `bol`.`ID`) 
	left outer join locations on (bol.FACILITYID=locations.ID)
	left outer join clients on (bolitems.CLIENTCODE=clients.CODE) WHERE (bolitems.LOT = "'.$lot.'") ORDER BY DUEDATE';

$queryblends='SELECT `blenditems`.`SOURCELOT`, `blenditems`.`GALLONS`, `blenditems`.`DIRECTION`,
 `wo`.`LOT`,wo.COST, wo.TYPE, wo.OTHERDESC,wo.WORKPERFORMEDBY, lots.DESCRIPTION AS SOURCELOTDESCRIPTION,
clients.clientid as CLIENTID, clients.CODE AS CLIENTCODE, `clients`.`CLIENTNAME`,
  `blend`.`WOID`,wo.ID, wo.STATUS, `wo`.`DUEDATE`,wo.DUEDATE AS DUEDATE, wo.CREATIONDATE FROM `blenditems`
  INNER JOIN `blend` ON (`blenditems`.`BLENDID` = `blend`.`ID`)
  INNER JOIN `wo` ON (`blend`.`WOID` = `wo`.`ID`) 
  left outer join clients on (wo.CLIENTCODE=clients.CODE)
  left outer join lots on (blenditems.SOURCELOT=lots.LOTNUMBER)
  WHERE
  (`blenditems`.`SOURCELOT` = "'.$lot.'") and (wo.DELETED != 1)';

	// $queryblends='SELECT `blenditems`.`SOURCELOT`, `blenditems`.`GALLONS`, `blenditems`.`DIRECTION`,
	//  `wo`.`LOT`,wo.COST, wo.TYPE, wo.OTHERDESC,wo.WORKPERFORMEDBY, 
	// clients.clientid as CLIENTID, clients.CODE AS CLIENTCODE, `clients`.`CLIENTNAME`,
	//   `blend`.`WOID`,wo.ID, wo.STATUS, `wo`.`DUEDATE`,wo.DUEDATE AS DUEDATE, wo.CREATIONDATE FROM `blenditems`
	//   INNER JOIN `blend` ON (`blenditems`.`BLENDID` = `blend`.`ID`)
	//   INNER JOIN `wo` ON (`blend`.`WOID` = `wo`.`ID`) 
	//   left outer join clients on (wo.CLIENTCODE=clients.CODE)
	//   WHERE
	//   (`blenditems`.`SOURCELOT` = "'.$lot.'")';
	
	// echo $queryblends;
	// echo '<br>';
	
	$resultwt=mysql_query($querywt);
	$resultwo=mysql_query($querywo);
	$resultbol=mysql_query($querybol);
	$resultblends=mysql_query($queryblends);
	
	
	for ($i=0; $i<mysql_num_rows($resultwt); $i++)
	{
		$row=mysql_fetch_assoc($resultwt);
		$row['TYPE']="WT";
		$record[]=array(date => $row['DUEDATE'], type=>"WT", data=>$row,
		starting_tankgallons=>0, starting_bbls=>0, starting_toppinggallons=>0,
		ending_tankgallons=>0, ending_bbls=>0, ending_toppinggallons=>0);
	}
	
	for ($i=0; $i<mysql_num_rows($resultwo); $i++)
	{
		$row=mysql_fetch_assoc($resultwo);
		$record[]=array(date => $row['DUEDATE'], type=>"WO", data=>$row,
		starting_tankgallons=>0, starting_bbls=>0, starting_toppinggallons=>0,
		ending_tankgallons=>0, ending_bbls=>0, ending_toppinggallons=>0);
		// if ((int)$row['id']>0)
		// {
		// 	$index=count($record)-1;
		// 	$record[$index]['date']=$row['startdate'];
		// 	$record[$index]['data']['DUEDATE']=$row['startdate'];
		// 	$record[$index]['data']['TYPE']=$row['type'];
		// }
	}
	for ($i=0; $i<mysql_num_rows($resultbol); $i++)
	{
		$row=mysql_fetch_assoc($resultbol);
		$record[]=array(date => $row['DUEDATE'], type=>"BOL", data=>$row,
		starting_tankgallons=>0, starting_bbls=>0, starting_toppinggallons=>0,
		ending_tankgallons=>0, ending_bbls=>0, ending_toppinggallons=>0);
	}
	
	for ($i=0; $i<mysql_num_rows($resultblends); $i++)
	{
		unset($blends);
		$row=mysql_fetch_assoc($resultblends);
		$row['TYPE']="BLEND";
		$ablend=$row;
		$ablend['SOURCELOT']=$row['LOT'];
		$blends[]=$ablend;
		$row['blend']=$blends;
		$record[]=array(date => $row['DUEDATE'], type=>"BLEND", data=>$row,
		starting_tankgallons=>0, starting_bbls=>0, starting_toppinggallons=>0,
		ending_tankgallons=>0, ending_bbls=>0, ending_toppinggallons=>0);
	}
	
	if (count($record)>1)
		usort($record,cmp);

	// echo '<pre>';
	// print_r($record);
	// exit;

	$reps=0;	
	
	if (count($record)>0)
	{
		$record[0]['starting_tankgallons']=0;
		$record[0]['starting_bbls']=0;
		$record[0]['starting_toppinggallons']=0;
		$record[0]['starting_cost']=0;
		$record[0]['ending_cost']=0;
	}
	
	// echo '<pre>';
	// print_r($record);
	// exit;
	for ($i=0; $i<count($record); $i++)
	{
		if ($i>0)
		{
			//echo 'record '.($i-1).' '.showstructure($record[$i-1]['structure']);
			$record[$i]['alcohol']=$record[$i-1]['alcohol'];
			$record[$i]['starting_tankgallons']=$record[$i-1]['ending_tankgallons'];
			$record[$i]['starting_bbls']=$record[$i-1]['ending_bbls'];
			$record[$i]['starting_toppinggallons']=$record[$i-1]['ending_toppinggallons'];
			$record[$i]['start_state']=$record[$i-1]['end_state'];
			$record[$i]['starting_cost']=$record[$i-1]['ending_cost'];
			$record[$i]['ending_cost']=$record[$i-1]['ending_cost']+$record[$i]['data']['COST'];
			$record[$i]['cost_data']=$record[$i-1]['cost_data'];
			if ($record[$i]['type']=="WO")
				$theType=$record[$i]['data']['TYPE'];
			else
				$theType=$record[$i]['type'];
			$record[$i]['cost_data'][$theType]+=$record[$i]['data']['COST'];
		}
		
		$record[$i]['end_state']=$record[$i]['start_state'];
		switch ($record[$i]['type'])
		{
			case "BLEND": //WO FROM OTHER LOT IS RECORDING A BLENDING INTO OR OUT OF THIS LOT
			{
				if ($record[$i]['data']['DIRECTION']=="IN FROM")
				{
				//	echo '<pre>'; print_r($record[$i]); exit;
					$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons']-$record[$i]['data']['GALLONS'];
					$record[$i]['structure']=addgallons($record[$i-1]['structure'],-$record[$i]['data']['GALLONS']);
					if ($record[$i]['starting_tankgallons']>0)
						$percentageDecrease=$record[$i]['data']['GALLONS']/$record[$i]['starting_tankgallons'];
					else
						$percentageDecrease=0;
					$record[$i]['ending_cost']=$record[$i-1]['ending_cost']*(1-$percentageDecrease);
					foreach ($record[$i]['cost_data'] as $key=>$value)
					{
						$record[$i]['cost_data'][$key]=$record[$i-1]['cost_data'][$key]*(1-$percentageDecrease);
					}							
				//	echo '<pre>'; print_r($record[$i]); exit;
				}
				else
				{
					$blendrecords=lotinforecords($record[$i]['data']['LOT'],"WO",$record[$i]['data']['WOID'],$lotsexamined);
					//klm change this from -1 to -2
					$blendstructure=$blendrecords[count($blendrecords)-2]['structure'];
					$source_endingGallons=$blendrecords[count($blendrecords)-1]['starting_tankgallons'];
					$source_incomingGallons=$record[$i]['data']['blend'][0]['GALLONS'];
					$source_endingCost=$blendrecords[count($blendrecords)-2]['ending_cost'];
				//	echo "<pre>"; print_r($blendrecords); exit;
					if ($source_endingGallons>0)
						$percentageGallons=$source_incomingGallons/$source_endingGallons;
					$record[$i]['ending_cost']=$source_endingCost*$percentageGallons+$record[$i]['starting_cost'];
					
					$record[$i]['end_state']=set_state($record,$i,$blendrecords[count($blendrecords)-1]['end_state']);
					$record[$i]['alcohol']=$blendrecords[count($blendrecords)-1]['alcohol'];
					$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons']+$record[$i]['data']['GALLONS'];
					
					$record[$i]['structure']=addstructure($record[$i-1]['structure'],$blendstructure,$record[$i]['data']['GALLONS']);
				}
				//$record[$i]['ending_bbls']=$record[$i]['data']['ENDINGBARRELCOUNT'];
				$record[$i]['ending_bbls']=$record[$i]['starting_bbls'];
				$record[$i]['ending_toppinggallons']=$record[$i]['starting_toppinggallons'];
				break;
			}
			case "WT" :
			{
				$record[$i]['end_state']=set_state($record,$i,"JUICE");
				$woid=$record[$i]['data']['ID'];
				$query='SELECT SUM( bindetail.BINCOUNT ) AS SUM_OF_BINCOUNT,
                     SUM( bindetail.WEIGHT ) AS SUM_OF_WEIGHT,
                     SUM( bindetail.TARE ) AS SUM_OF_TARE  FROM bindetail
                     INNER JOIN wt ON (bindetail.WEIGHTAG = wt.ID)
                     INNER JOIN lots ON (wt.LOT = lots.LOTNUMBER) WHERE 
                     ( lots.LOTNUMBER = "'.$lot.'" AND wt.ID = "'.$woid.'")';
			 					
				$result=mysql_query($query);
				$row=mysql_fetch_array($result);
				
				$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons']+155*($row['SUM_OF_WEIGHT']-$row['SUM_OF_TARE'])/2000;
				$record[$i]['ending_bbls']=$record[$i]['starting_bbls'];
				$record[$i]['ending_toppinggallons']=$record[$i]['starting_toppinggallons'];
				
				$gallons=($row['SUM_OF_WEIGHT']-$row['SUM_OF_TARE'])/2000*155;
				$record[$i]['structure']=addtostructure ($record[$i-1]['structure'], strtotime($record[$i]['date']), $record[$i]['data']['VARIETY'], $record[$i]['data']['APPELLATION'], $record[$i]['data']['VINEYARD'], $gallons);
				
				//exit;
				// echo showstructure($record[$i]['structure']);
				break;
			}
			case "WO" :
			
			switch ($record[$i]['data']['TYPE'])
			{
				case "BLENDING":
				{
					$woid=$record[$i]['data']['ID'];
					// $queryblendsforwo='SELECT `blenditems`.`SOURCELOT`, `blenditems`.`GALLONS`, `blenditems`.`DIRECTION`, `wo`.`LOT`, lots.DESCRIPTION as SOURCELOTDESCRIPTION,
					//                       `blend`.`WOID`, UNIX_TIMESTAMP(`wo`.`DUEDATE`) AS THEDATE FROM `blenditems`
					//    LEFT OUTER JOIN lots on (blenditems.SOURCELOT=lots.LOTNUMBER)
					//                        INNER JOIN `blend` ON (`blenditems`.`BLENDID` = `blend`.`ID`)
					//                        INNER JOIN `wo` ON (`blend`.`WOID` = `wo`.`ID`) WHERE  (`wo`.`ID` = "'.$woid.'")';
					$queryblendsforwo='SELECT `blenditems`.`SOURCELOT`, `blenditems`.`GALLONS`, `blenditems`.`DIRECTION`, `wo`.`LOT`, 
                      `blend`.`WOID`, UNIX_TIMESTAMP(`wo`.`DUEDATE`) AS THEDATE FROM `blenditems`
                       INNER JOIN `blend` ON (`blenditems`.`BLENDID` = `blend`.`ID`)
                       INNER JOIN `wo` ON (`blend`.`WOID` = `wo`.`ID`) WHERE  (`wo`.`ID` = "'.$woid.'")';
//					 echo $queryblendsforwo;echo '<br>';exit;
					$resultblendsforwo=mysql_query($queryblendsforwo);
					$totgal=0;
					$woblends='';
					$blendgallons=0;
					$incrementalCost=0;
					for ($k=0; $k<mysql_num_rows($resultblendsforwo); $k++)
					{
						$blendrow=mysql_fetch_array($resultblendsforwo);
//						echo '<pre>'; print_r($blendrow); echo '<br>';
						if ($blendrow['DIRECTION']=="IN FROM")
						{
							$blendrecords=lotinforecords($blendrow['SOURCELOT'],"BLEND",$blendrow['WOID'],$lotsexamined);
							
							// echo '-----';
							//  echo '<pre>';
							//  print_r($blendrecords);
							 // exit;
							$source_endingGallons=$blendrecords[count($blendrecords)-1]['starting_tankgallons'];
							$source_incomingGallons=$blendrow['GALLONS'];
							$totgal=$totgal+$blendrow['GALLONS'];
							$source_endingCost=$blendrecords[count($blendrecords)-1]['ending_cost'];
							// echo '<pre>';
							// 					print_r($source_endingCost);
							// 					exit;
							if ($source_endingGallons!=0)
								$percentageGallons=$source_incomingGallons/$source_endingGallons;
							else
								$percentageGallons=0;
							$incrementalCost+=$source_endingCost*$percentageGallons;
							foreach ($blendrecords[count($blendrecords)-1]['cost_data'] as $key=>$value)
							{
								$incrementalCostData[$key]+=$blendrecords[count($blendrecords)-1]['cost_data'][$key]*$percentageGallons;
							}							
							
							$blendstructure=$blendrecords[count($blendrecords)-1]['structure'];
							// echo '<pre>';
							// print_r($blendstructure);
							// exit;
							if ($k==0)
							    $record[$i]['structure']=addstructure($record[$i-1]['structure'],$blendstructure,$blendrow['GALLONS']);
							else
							    $record[$i]['structure']=addstructure($record[$i]['structure'],$blendstructure,$blendrow['GALLONS']);							
							// echo '<pre>';
							// print_r($record[$i]['structure']);
							// exit;
						}
						
						else
						{
							$totgal=$totgal-$blendrow['GALLONS'];
							
							$record[$i]['structure']=addgallons($record[$i-2]['structure'],-$blendrow['GALLONS']);
							if ($record[$i]['starting_tankgallons']>0)
								$percentageDecrease=-$blendrow['GALLONS']/$record[$i]['starting_tankgallons'];
							$incrementalCost=$incrementalCost+$record[$i-2]['ending_cost']*$percentageDecrease;
						}
					}
//					$record[$i]['structure']=addstructure($record[$i-1]['structure'],$allblendstructure,$blendrow['GALLONS']);
					// echo '<pre>';
					// print_r($incrementalCostData);
					// exit;
					foreach ($incrementalCostData as $key=>$value)
					{
						$record[$i]['cost_data'][$key]+=$value;
					}							
					
					$record[$i]['ending_cost']=$record[$i-1]['ending_cost']+$incrementalCost;
					// if ($blendrow['DIRECTION']=="IN FROM")
					// {
					// 	$record[$i]['structure']=addstructure($record[$i-1]['structure'],$woblends,$blendgallons);
					// }
					$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons']+$totgal;
					//$record[$i]['ending_bbls']=$record[$i]['data']['ENDINGBARRELCOUNT'];
					$record[$i]['ending_bbls']=$record[$i]['starting_bbls'];
					$record[$i]['ending_toppinggallons']=$record[$i]['starting_toppinggallons'];
					
					break;
				}
				case "LAB TEST" :
				{
					$q='SELECT labtest.WOID, labresults.VALUE1 FROM  `labresults`  INNER JOIN `labtest` ON (`labresults`.`LABTESTID` = `labtest`.`ID`) WHERE
                          ((labresults.LABTEST="ALCOHOL") AND (labtest.WOID="'.$record[$i]['data']['ID'].'"))';
					$r=mysql_query($q);
					if (mysql_num_rows($r)>0)
					{
						$therow=mysql_fetch_array($r);
						$record[$i]['alcohol']=$therow['VALUE1'];
						if ($record[$i]['alcohol']>=14)
						$record[$i]['end_state']=set_state($record,$i,"WINE_ABOVE");
						else
						$record[$i]['end_state']=set_state($record,$i,"WINE_BELOW");
					}
					
					$queryCost='select distinct labresults.LABTEST,labtestcosts.cost from labtest left outer join labresults on (labresults.LABTESTID=labtest.ID) inner join labtestcosts on (labresults.LABTEST=labtestcosts.labtest) where labtest.WOID="'.$record[$i]['data']['ID'].'"';
					$costResult=mysql_query($queryCost);
					$labcosts=0;
					for ($labtest=0;$labtest<mysql_num_rows($costResult); $labtest++)
					{
						$row=mysql_fetch_assoc($costResult);
						$labcosts=$labcosts+$row['cost'];
					}
					$record[$i]['ending_cost']=$record[$i]['starting_cost']+$labcosts+$record[$i]['data']['COST'];
					$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons'];
					$record[$i]['ending_bbls']=$record[$i]['starting_bbls'];
					$record[$i]['ending_toppinggallons']=$record[$i]['starting_toppinggallons'];
					$difference=$record[$i]['ending_tankgallons']-$record[$i]['starting_tankgallons'];
					$record[$i]['structure']=addgallons($record[$i-1]['structure'],$difference);

					break;
				}	
				case "BOTTLING" :
				{
					$query='SELECT * from bottling WHERE WOID="'.$record[$i]['data']['ID'].'"';
					$bottlingresult=mysql_query($query);
					$bottlingrow=mysql_fetch_assoc($bottlingresult);
					$record[$i]['data']['casesBottled']=(string)$bottlingrow['FINALCASECOUNT'];
					$record[$i]['data']['gallonsPerCase']=(string)$bottlingrow['GALLONSPERCASE'];
					//                  if ($bottlingrow['FINALCASECOUNT']>0)
					//                  {
					$record[$i]['ending_tankgallons']=$bottlingrow['FINALCASECOUNT']*$bottlingrow['GALLONSPERCASE'];
					//                  }
					//                  else
					//                  {
					if (($record[$i]['data']['ENDINGTANKGALLONS']!=0) |
					($record[$i]['data']['BBLCOUNT']!=0) |
					($record[$i]['data']['TOPPINGGALLONS']!=0))
					{
						$record[$i]['ending_tankgallons']=$record[$i]['data']['ENDINGTANKGALLONS'];
						$record[$i]['ending_bbls']=$record[$i]['data']['BBLCOUNT'];
						$record[$i]['ending_toppinggallons']=$record[$i]['data']['TOPPINGGALLONS'];
					}
					else
					{
						$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons'];
						$record[$i]['ending_bbls']=$record[$i]['starting_bbls'];
						$record[$i]['ending_toppinggallons']=$record[$i]['starting_toppinggallons'];
					}
					//                  }
					$difference=($record[$i]['ending_tankgallons']+($record[$i]['ending_bbls']*60)+$record[$i]['ending_toppinggallons'])-
					($record[$i]['starting_tankgallons']+($record[$i]['starting_bbls']*60)+$record[$i]['starting_toppinggallons']);
					$record[$i]['structure']=addgallons($record[$i-1]['structure'],$difference);
					if ($record[$i]['alcohol']>=14)
					{
						$record[$i]['end_state']=set_state($record,$i,"BOTTLED_ABOVE_INBOND",1);
					}
					else
					{
						$record[$i]['end_state']=set_state($record,$i,"BOTTLED_BELOW_INBOND",1);
					}
					break;
				}
				case "DRYICE" :
				{
					$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons'];
					$difference=$record[$i]['ending_tankgallons']-$record[$i]['starting_tankgallons'];
					$record[$i]['structure']=addgallons($record[$i-1]['structure'],$difference);
					break;
				}
				case "SCP" :
				{
//					echo 'scp...';
					$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons'];
					$difference=$record[$i]['ending_tankgallons']-$record[$i]['starting_tankgallons'];
					$record[$i]['structure']=addgallons($record[$i-1]['structure'],$difference);
					break;
				}
				case "PUMP OVER" :
				{
					$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons'];
					$difference=$record[$i]['ending_tankgallons']-$record[$i]['starting_tankgallons'];
					$record[$i]['structure']=addgallons($record[$i-1]['structure'],$difference);
					break;
				}
				case "PUNCH DOWN" :
				{
					$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons'];
					$difference=$record[$i]['ending_tankgallons']-$record[$i]['starting_tankgallons'];
					$record[$i]['structure']=addgallons($record[$i-1]['structure'],$difference);
					break;
				}
				default:
				{
			//		echo '<pre>'; print_r($record[$i]);
			//		echo 'got here...'.$record[$i]['data']['TYPE'].'<br>';
					if (($record[$i]['data']['ENDINGTANKGALLONS']!=0) |
					($record[$i]['data']['BBLCOUNT']!=0) |
					($record[$i]['data']['TOPPINGGALLONS']!=0))
					{
						
						$record[$i]['ending_tankgallons']=$record[$i]['data']['ENDINGTANKGALLONS'];
						$record[$i]['ending_bbls']=$record[$i]['data']['BBLCOUNT'];
						$record[$i]['ending_toppinggallons']=$record[$i]['data']['TOPPINGGALLONS'];
					}
					else
					{
						$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons'];
						$record[$i]['ending_bbls']=$record[$i]['starting_bbls'];
						$record[$i]['ending_toppinggallons']=$record[$i]['starting_toppinggallons'];
					}
				
				/*		$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons'];
						$record[$i]['ending_bbls']=$record[$i]['starting_bbls'];
						$record[$i]['ending_toppinggallons']=$record[$i]['starting_toppinggallons'];
					*/
					$difference=($record[$i]['ending_tankgallons']+($record[$i]['ending_bbls']*60)+$record[$i]['ending_toppinggallons'])-
					($record[$i]['starting_tankgallons']+($record[$i]['starting_bbls']*60)+$record[$i]['starting_toppinggallons']);
					$record[$i]['structure']=addgallons($record[$i-1]['structure'],$difference);
		/*			if ($difference<>0)
					{
						echo '<pre>'.'Difference is: '.$difference.'<br>';
						echo '----------'.'<br>';
						print_r($record[$i-1]);
						echo '----------'.'<br>';
						print_r($record[$i]);
						echo '----------'.'<br>';
					}
		*/		}
			}
			break;
			
			case "BOL" :
			if ($record[$i]['data']['DIRECTION']=="IN")
			{
				$query='SELECT `bolitembreakout`.`VARIETAL`, `bolitembreakout`.`APPELLATION`, `bolitembreakout`.`VINEYARD`,
                         `bolitembreakout`.`PERCENTAGE`,`bolitems`.`GALLONS`,`bolitems`.`ALC`, bolitems.TYPE, `bolitembreakout`.`VINTAGE`
                         FROM `bolitembreakout`
                            INNER JOIN `bolitems` ON (`bolitembreakout`.`BOLITEMSID` = `bolitems`.`ID`)
                            INNER JOIN `bol` ON (`bolitems`.`BOLID` = `bol`.`ID`)
                         WHERE  (`bol`.`ID` = "'.$record[$i]['data']['ID'].'") AND   (`bolitems`.`LOT` = "'.$record[$i]['data']['LOT'].'")';
				//echo $query;
				$result=mysql_query($query);
				for ($k=0;$k<mysql_num_rows($result);$k++)
				{
					$therow=mysql_fetch_array($result);
				//	echo '<pre>'; print_r($therow); exit;
					if ($therow['TYPE']=="WINE")
					{
						if ($therow['ALC']=="<14%")
						$record[$i]['end_state']=set_state($record,$i,"WINE_BELOW");
						else
						$record[$i]['end_state']=set_state($record,$i,"WINE_ABOVE");
					}
					elseif ($therow['TYPE']=="BOTTLED")
					{
						if ($record[$i]['data']['BONDED']=="BONDTOBOND")
						$suffix='_INBOND';
						if ($therow['ALC']=="<14%")
						$record[$i]['end_state']=set_state($record,$i,"BOTTLED_BELOW".$suffix);
						else
						$record[$i]['end_state']=set_state($record,$i,"BOTTLED_ABOVE".$suffix);
					}
					else
					{
						$record[$i]['end_state']=set_state($record,$i,"JUICE");
					}
					$record[$i]['taxstatus']=$record[$i]['data']['BONDED'];
					if ($therow['ALC']=="<14%")
					$record[$i]['alcohol']=1;
					else
					$record[$i]['alcohol']=25;
					if ($therow['TYPE']=='GRAPES') {
						$record[$i]['ending_tankgallons']+=$record[$i]['starting_tankgallons']+($therow['GALLONS']*155);
					}
					else {
						$record[$i]['ending_tankgallons']+=$record[$i]['starting_tankgallons']+($therow['GALLONS']*($therow['PERCENTAGE']/100));
					}
					if ($therow['TYPE']=='GRAPES')
					$amount=($therow['GALLONS']*155)*($therow['PERCENTAGE']/100);
					else
					$amount=$therow['GALLONS']*($therow['PERCENTAGE']/100);
					if ($k==0)
					{
						$record[$i]['structure']=addtostructure($record[$i-1]['structure'],strtotime("Jan 1, ".$therow['VINTAGE']),
						$therow['VARIETAL'],$therow['APPELLATION'],$therow['VINEYARD'], $amount);
					}
					else
					{
						$record[$i]['structure']=addtostructure($record[$i]['structure'],strtotime("Jan 1, ".$therow['VINTAGE']),
						$therow['VARIETAL'],$therow['APPELLATION'],$therow['VINEYARD'], $amount);
					}
				}
			}
			else
			{
				$query='SELECT bolitems.*  FROM `bolitems` INNER JOIN `bol` ON (`bolitems`.`BOLID` = `bol`.`ID`)
                         WHERE  `bolitems`.`ID` = "'.$record[$i]['data']['bolItemID'].'"';
				// 	            echo $query;
				// exit;
				$result=mysql_query($query);
				// echo '<pre>';
				// print_r($record);
				// exit;
				$record[$i]['taxstatus']=$record[$i]['data']['BONDED'];
				for ($k=0;$k<mysql_num_rows($result);$k++)
				{
					$therow=mysql_fetch_assoc($result);
					// echo '<pre>';
					// print_r($therow);
					// exit;
					$record[$i]['data']['bolItems'][]=$therow;
					if ($record[$i]['data']['LOT']==$therow['LOT'])
					{
						if ($therow['TYPE']=='GRAPES')
						{
							$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons']-($record[$i]['data']['GALLONS']*155);
							$record[$i]['structure']=addgallons($record[$i-1]['structure'],-($record[$i]['data']['GALLONS']*155));
						}
						else
						{
							$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons']-$record[$i]['data']['GALLONS'];
							$record[$i]['structure']=addgallons($record[$i-1]['structure'],-$record[$i]['data']['GALLONS']);
						}						
					}
				}
			}
			$record[$i]['ending_bbls']=$record[$i]['starting_bbls'];
			$record[$i]['ending_toppinggallons']=$record[$i]['starting_toppinggallons'];
			break;
		}
		
		
		//check to see if we are running a full lot info and returning all records or only going up to a certain
		//wo, bol or wt.   If they have requested up to a certain point, then stop here.  The last record in this
		//array is what they will use to determine info on the lot at this current state.
		if (($type != "") & ($number != ""))
		{
			// echo $record[$i]['type'].'<br>';
			// echo '<pre>';
			// print_r($record[$i]);
			// echo '</pre>';
			   // echo $record[$i]['data']['ID'].'<br>';
			if (($type == $record[$i]['type'] | $record[$i]['type'] == "BLEND") & ($number == $record[$i]['data']['ID']))
//			if ($number == $record[$i]['data']['ID'])
			{
				// echo $oneShort;
				// exit;
				if ($oneShort==0)
					$record=array_slice($record,0,$i+1);
				else
					$record=array_slice($record,0,$i);				
//				showstructure($record[count($record)-1]['structure']);
				break;
			}
		}

		$record[$i]['difference']=($record[$i]['ending_tankgallons']+$record[$i]['ending_bbls']*60+$record[$i]['ending_toppinggallons'])-
		($record[$i]['starting_tankgallons']+$record[$i]['starting_bbls']*60+$record[$i]['starting_toppinggallons']);
		if($record[$i]['start_state']=='')
		$record[$i]['start_state']=$record[$i]['end_state'];
		
		// echo '<pre>';
		// echo '['.$i.']<br>';
//		print_r($record[$i]['structure']);
		
	}	

	// echo '<pre>';
	// print_r($record);
	// exit;
	
	return $record;
}
function lastrecordbeforedate($record,$date)
{
	$lr=-1;
	for ($i=0;$i<count($record);$i++)
	{
		$lr=$i;
		//        echo $date.' '.$record[$i]['date'];
		if ($record[$i]['date']>$date)
		{
			return $record[$i-1];
		}
	}
	return $record[$lr];
}

?>
