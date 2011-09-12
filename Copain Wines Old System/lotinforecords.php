<?php
function debug($val)
{
	echo '<pre>';
	print_r($val);
	echo '</pre>';
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
	$query='SELECT  `lots`.`LOTNUMBER`, `assets`.`NAME` FROM
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
	// echo '<pre>';
	// echo $gallons.'<br>';
	// echo 'Target: ';
	// print_r($receivinglot);
	// echo 'Source:';
	// print_r($fromlot);
	
	if ($fromlot=="") return $receivinglot;
	
	$totalfromgallons=0;
	if (count($fromlot)>0)
	{
		foreach ($fromlot['year'] as $key => $value)
		{
			$totalfromgallons+=$value;
		}

		foreach ($fromlot as $key => $value)
		{
			foreach ($value as $key2 => $value2)
			{
				if ($totalfromgallons!=0)
				{
					$receivinglot[$key][$key2]+=$gallons * ($value2/$totalfromgallons);
				}
				else
				{
					$receivinglot[$key][$key2]+=$gallons;				
				}
			}
		}
	}
	// echo '<pre>';
	// echo 'Result<br>';
	// print_r($receivinglot);
	// echo '</pre>';
	return $receivinglot;
}

function addstructure($oldstructure, $newstructure, $gallons=0)
{
	echo '<pre>Old - ';
	print_r($oldstructure).'<br>';
	echo '<pre>Add - ';
	print_r($newstructure); echo $gallons.'--'.count($newstructure).'<br>';
	if (count($newstructure)>0)
	{
		foreach ($newstructure as $key => $value)
		{
			foreach ($value as $key2 => $value2)
			{
				$tot+=$value2;
			}
			foreach ($value as $key2 => $value2)
			{
				if ($gallons ==0)
				{
					$oldstructure[$key][$key2]+=$value2;
				}
				else
				{
					if ($tot>0)
					$oldstructure[$key][$key2]+=$gallons*($value2/$tot);
					else
					$oldstructure[$key][$key2]+=$gallons;
				}
			}
		}
	}
	echo 'New - ';
	print_r($oldstructure);
	return $oldstructure;
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

function lotinforecords ($lot, $type="", $number="", $lotsexamined="")
{
	if (alreadyexamined($lot,$lotsexamined)==1)
	{
		echo 'Circular reference encountered on lot:'.$lot;
		return;
	}
	$lotsexamined[]=$lot;
	
	// $querywt='SELECT wt.ID, wt.LOT, wt.VARIETY, wt.VINEYARD, wt.APPELLATION, wt.TAGID,
	//        UNIX_TIMESTAMP(`wt`.`DATETIME`) AS THEDATE,
	//        SUM( bindetail.BINCOUNT ) AS SUM_OF_BINCOUNT,
	//        SUM( bindetail.WEIGHT ) AS SUM_OF_WEIGHT,
	//        SUM( bindetail.TARE ) AS SUM_OF_TARE
	//     FROM bindetail
	//        RIGHT OUTER JOIN wt ON (bindetail.WEIGHTAG = wt.ID)
	//        INNER JOIN lots ON (wt.LOT = lots.LOTNUMBER)
	//     WHERE(lots.LOTNUMBER = "'.$lot.'")
	//     GROUP BY wt.VARIETY, wt.VINEYARD, wt.APPELLATION, wt.TAGID, wt.DATETIME ORDER BY THEDATE';

	$querywt='SELECT wt.ID, wt.LOT, wt.VARIETY, locations.NAME as VINEYARD, locations.APPELLATION, wt.TAGID,
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
	//  echo $querywt;
	
	$querywo='SELECT DISTINCT `wo`.`TYPE`,`wo`.`ASSIGNEDTO`, `wo`.`OTHERDESC`, `wo`.`ID`, `wo`.`VESSELID`, `wo`.`VESSELTYPE`, `wo`.`DURATION`,
  `wo`.`RELATEDADDITIONSID`,  `wo`.`STRENGTH`,  `wo`.`COMPLETEDDESCRIPTION`,  `wo`.`ENDINGTANKGALLONS`,  `wo`.`ENDINGBARRELCOUNT`,
  `wo`.`ENDINGTOPPINGGALLONS`, UNIX_TIMESTAMP(`wo`.`ENDDATE`) AS THEDATE FROM
  `lots`   INNER JOIN `wo` ON (`lots`.`LOTNUMBER` = `wo`.`LOT`)
   WHERE lots.LOTNUMBER = "'.$lot.'" and wo.DELETED!=1 ORDER BY THEDATE';
	
	//echo $querywo.'<br>';
	
	$querybol='SELECT bol.ID, bol.NAME, bol.BONDED, bol.BOND, `bol`.`DIRECTION`, `bolitems`.`LOT`,
         UNIX_TIMESTAMP(`bol`.`DATE`) AS THEDATE, bol.DATE, bolitems.TYPE, `bolitems`.`GALLONS`
   FROM `bolitems` INNER JOIN `bol` ON (`bolitems`.`BOLID` = `bol`.`ID`) WHERE (bolitems.LOT = "'.$lot.'") ORDER BY THEDATE';
	
	$queryblends='SELECT `blenditems`.`SOURCELOT`, `blenditems`.`GALLONS`, `blenditems`.`DIRECTION`, `wo`.`LOT`, wo.OTHERDESC,
  `blend`.`WOID`, UNIX_TIMESTAMP(`wo`.`DUEDATE`) AS THEDATE FROM `blenditems`
  INNER JOIN `blend` ON (`blenditems`.`BLENDID` = `blend`.`ID`)
  INNER JOIN `wo` ON (`blend`.`WOID` = `wo`.`ID`)
  WHERE
  (`blenditems`.`SOURCELOT` = "'.$lot.'")';
	
	$resultwt=mysql_query($querywt);
	$resultwo=mysql_query($querywo);
	$resultbol=mysql_query($querybol);
	$resultblends=mysql_query($queryblends);
	
	
	for ($i=0; $i<mysql_num_rows($resultwt); $i++)
	{
		$row=mysql_fetch_array($resultwt);
		$record[]=array(date => $row['THEDATE'], type=>"WT", data=>$row,
		starting_tankgallons=>0, starting_bbls=>0, starting_toppinggallons=>0,
		ending_tankgallons=>0, ending_bbls=>0, ending_toppinggallons=>0);
	}
	for ($i=0; $i<mysql_num_rows($resultwo); $i++)
	{
		$row=mysql_fetch_array($resultwo);
		$record[]=array(date => $row['THEDATE'], type=>"WO", data=>$row,
		starting_tankgallons=>0, starting_bbls=>0, starting_toppinggallons=>0,
		ending_tankgallons=>0, ending_bbls=>0, ending_toppinggallons=>0);
	}
	for ($i=0; $i<mysql_num_rows($resultbol); $i++)
	{
		$row=mysql_fetch_array($resultbol);
		$record[]=array(date => $row['THEDATE'], type=>"BOL", data=>$row,
		starting_tankgallons=>0, starting_bbls=>0, starting_toppinggallons=>0,
		ending_tankgallons=>0, ending_bbls=>0, ending_toppinggallons=>0);
	}
	
	for ($i=0; $i<mysql_num_rows($resultblends); $i++)
	{
		$row=mysql_fetch_array($resultblends);
		$record[]=array(date => $row['THEDATE'], type=>"BLEND", data=>$row,
		starting_tankgallons=>0, starting_bbls=>0, starting_toppinggallons=>0,
		ending_tankgallons=>0, ending_bbls=>0, ending_toppinggallons=>0);
	}
	if (count($record)>1)
	$sorted=0;
	else
	$sorted=1;
	
	$reps=0;
	
	

	
	while ($sorted==0)
	{
		$sortcount++;
		for ($i=0;$i<count($record);$i++)
		{
			if ($i == count($record)-1)
			{
				$sorted=1;
				break;
			}
			if (date("m/d/Y",$record[$i+1]['date'])==date("m/d/Y",$record[$i]['date']))
			{
//				echo date("m/d/Y",$record[$i+1]['date']).'-'.$record[$i+1]['type'].'-'.$record[$i+1]['data']['TYPE'].'<br>';
                if (isbottling($record[$i])==1 & isbottling($record[$i+1])==0)
                {
				   $temp=$record[$i];
				$record[$i]=$record[$i+1];
				$record[$i+1]=$temp;
				break;
                }
				if (isanscp($record[$i+1])==1 & isanscp($record[$i])==0)
				{
				   $temp=$record[$i];
				$record[$i]=$record[$i+1];
				$record[$i+1]=$temp;
				break;
				}
				else 
				{
				   if ($record[$i+1]['type']=="WT" & isanscp($record[$i])==0 & $record[$i]['type']!="WT")
				   {
				     $temp=$record[$i];
				$record[$i]=$record[$i+1];
				$record[$i+1]=$temp;
				break;
				   }
				}
			}
			elseif ($record[$i+1]['date']<$record[$i]['date'])
			{
				$temp=$record[$i];
				$record[$i]=$record[$i+1];
				$record[$i+1]=$temp;
				break;
			}
		}
//		if ($sortcount>50) exit;
	}
	if (count($record)>0)
	{
		$record[0]['starting_tankgallons']=0;
		$record[0]['starting_bbls']=0;
		$record[0]['starting_toppinggallons']=0;
	}
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
				//	echo '<pre>'; print_r($record[$i]); exit;
				}
				else
				{
					$blendrecords=lotinforecords($record[$i]['data']['LOT'],"WO",$record[$i]['data']['WOID'],$lotsexamined);
					//klm change this from -1 to -2
					$blendstructure=$blendrecords[count($blendrecords)-2]['structure'];
					
					$record[$i]['end_state']=set_state($record,$i,$blendrecords[count($blendrecords)-1]['end_state']);
					$record[$i]['alcohol']=$blendrecords[count($blendrecords)-1]['alcohol'];
					$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons']+$record[$i]['data']['GALLONS'];
					// echo 'calling addstructure...<br>';
					// echo '<pre>';
					// print_r($blendrecords);
					// echo '</pre>';
					
					$record[$i]['structure']=addstructure2($record[$i-1]['structure'],$blendstructure,$record[$i]['data']['GALLONS']);
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
				//              echo $query.'<br>';
				
				$result=mysql_query($query);
				$row=mysql_fetch_array($result);
				
				$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons']+155*($row['SUM_OF_WEIGHT']-$row['SUM_OF_TARE'])/2000;
				$record[$i]['ending_bbls']=$record[$i]['starting_bbls'];
				$record[$i]['ending_toppinggallons']=$record[$i]['starting_toppinggallons'];
				
				$gallons=($row['SUM_OF_WEIGHT']-$row['SUM_OF_TARE'])/2000*155;
				$record[$i]['structure']=addtostructure ($record[$i-1]['structure'], $record[$i]['date'], $record[$i]['data']['VARIETY'], $record[$i]['data']['APPELLATION'], $record[$i]['data']['VINEYARD'], $gallons);
				
				// echo showstructure($record[$i]['structure']);
				break;
			}
			case "WO" :
			switch ($record[$i]['data']['TYPE'])
			{
				case "BLENDING":
				{
					$woid=$record[$i]['data']['ID'];
					$queryblendsforwo='SELECT `blenditems`.`SOURCELOT`, `blenditems`.`GALLONS`, `blenditems`.`DIRECTION`, `wo`.`LOT`,
                      `blend`.`WOID`, UNIX_TIMESTAMP(`wo`.`DUEDATE`) AS THEDATE FROM `blenditems`
                       INNER JOIN `blend` ON (`blenditems`.`BLENDID` = `blend`.`ID`)
                       INNER JOIN `wo` ON (`blend`.`WOID` = `wo`.`ID`) WHERE  (`wo`.`ID` = "'.$woid.'")';
					//echo $queryblendsforwo;echo '<br>';
					$resultblendsforwo=mysql_query($queryblendsforwo);
					$totgal=0;
					$woblends='';
					$blendgallons=0;
					for ($k=0; $k<mysql_num_rows($resultblendsforwo); $k++)
					{
						$blendrow=mysql_fetch_array($resultblendsforwo);
						if ($blendrow['DIRECTION']=="IN FROM")
						{
							$sourcelot=lotinforecords($blendrow['SOURCELOT'],"WO",$blendrow['WOID'],$lotsexamined);
							$totgal=$totgal+$blendrow['GALLONS'];
							$woblends=addstructure2($woblends,$sourcelot[count($sourcelot)-2]['structure'],$blendrow['GALLONS']);
							$blendgallons+=$blendrow['GALLONS'];
							$record[$i]['end_state']=set_state($record,$i,$sourcelot[count($sourcelot)-2]['end_state']);
							$record[$i]['alcohol']=$sourcelot[count($sourcelot)-2]['alcohol'];
						}
						else
						{
							$totgal=$totgal-$blendrow['GALLONS'];
							$record[$i]['structure']=addgallons($record[$i-1]['structure'],-$blendrow['GALLONS']);
						}
					}
					if ($blendrow['DIRECTION']=="IN FROM")
					{
						// echo 'calling add structure here...<br>';
						$record[$i]['structure']=addstructure2($record[$i-1]['structure'],$woblends,$blendgallons);
					}
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
					$bottlingrow=mysql_fetch_array($bottlingresult);
					//                  if ($bottlingrow['FINALCASECOUNT']>0)
					//                  {
					$record[$i]['ending_tankgallons']=$bottlingrow['FINALCASECOUNT']*$bottlingrow['GALLONSPERCASE'];
					//                  }
					//                  else
					//                  {
					if (($record[$i]['data']['ENDINGTANKGALLONS']!=0) |
					($record[$i]['data']['ENDINGBARRELCOUNT']!=0) |
					($record[$i]['data']['ENDINGTOPPINGGALLONS']!=0))
					{
						$record[$i]['ending_tankgallons']=$record[$i]['data']['ENDINGTANKGALLONS'];
						$record[$i]['ending_bbls']=$record[$i]['data']['ENDINGBARRELCOUNT'];
						$record[$i]['ending_toppinggallons']=$record[$i]['data']['ENDINGTOPPINGGALLONS'];
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
					$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons'];
					$difference=$record[$i]['ending_tankgallons']-$record[$i]['starting_tankgallons'];
					$record[$i]['structure']=addgallons($record[$i-1]['structure'],$difference);
					break;
				}
				case "ADDITION" :
				{
					$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons'];
					$record[$i]['ending_bbls']=$record[$i]['starting_bbls'];
					$record[$i]['ending_toppinggallons']=$record[$i]['starting_toppinggallons'];
					$difference=($record[$i]['ending_tankgallons']+($record[$i]['ending_bbls']*60)+$record[$i]['ending_toppinggallons'])-
						($record[$i]['starting_tankgallons']+($record[$i]['starting_bbls']*60)+$record[$i]['starting_toppinggallons']);
					$record[$i]['structure']=addgallons($record[$i-1]['structure'],$difference);
					break;
				}
				case "PUMP OVER" :
				{
					$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons'];
					$record[$i]['ending_bbls']=$record[$i]['starting_bbls'];
					$record[$i]['ending_toppinggallons']=$record[$i]['starting_toppinggallons'];
					$difference=($record[$i]['ending_tankgallons']+($record[$i]['ending_bbls']*60)+$record[$i]['ending_toppinggallons'])-
						($record[$i]['starting_tankgallons']+($record[$i]['starting_bbls']*60)+$record[$i]['starting_toppinggallons']);
					$record[$i]['structure']=addgallons($record[$i-1]['structure'],$difference);
					break;
				}
				case "PUNCH DOWN" :
				{
					$record[$i]['ending_tankgallons']=$record[$i]['starting_tankgallons'];
					$record[$i]['ending_bbls']=$record[$i]['starting_bbls'];
					$record[$i]['ending_toppinggallons']=$record[$i]['starting_toppinggallons'];
					$difference=($record[$i]['ending_tankgallons']+($record[$i]['ending_bbls']*60)+$record[$i]['ending_toppinggallons'])-
						($record[$i]['starting_tankgallons']+($record[$i]['starting_bbls']*60)+$record[$i]['starting_toppinggallons']);
					$record[$i]['structure']=addgallons($record[$i-1]['structure'],$difference);
					break;
				}
				default:
				{
			//		echo 'got here...'.$record[$i]['data']['TYPE'].'<br>';
					if (($record[$i]['data']['ENDINGTANKGALLONS']!=0) |
					($record[$i]['data']['ENDINGBARRELCOUNT']!=0) |
					($record[$i]['data']['ENDINGTOPPINGGALLONS']!=0))
					{
						$record[$i]['ending_tankgallons']=$record[$i]['data']['ENDINGTANKGALLONS'];
						$record[$i]['ending_bbls']=$record[$i]['data']['ENDINGBARRELCOUNT'];
						$record[$i]['ending_toppinggallons']=$record[$i]['data']['ENDINGTOPPINGGALLONS'];
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
					// if ($difference<>0)
					// {
					// 	echo '<pre>'.'Difference is: '.$difference.'<br>';
					// 	echo '----------'.'<br>';
					// 	print_r($record[$i-1]);
					// 	echo '----------'.'<br>';
					// 	print_r($record[$i]);
					// 	echo '----------'.'<br>';
					// }
				}
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
				$query='SELECT `bolitems`.`GALLONS`,`bolitems`.`ALC`, bolitems.TYPE
                         FROM `bolitems`
                            INNER JOIN `bol` ON (`bolitems`.`BOLID` = `bol`.`ID`)
                         WHERE  (`bol`.`ID` = "'.$record[$i]['data']['ID'].'") AND   (`bolitems`.`LOT` = "'.$record[$i]['data']['LOT'].'")';
				//              echo $query;
				$result=mysql_query($query);
				$record[$i]['taxstatus']=$record[$i]['data']['BONDED'];
				for ($k=0;$k<mysql_num_rows($result);$k++)
				{
					$therow=mysql_fetch_array($result);
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
			$record[$i]['ending_bbls']=$record[$i]['starting_bbls'];
			$record[$i]['ending_toppinggallons']=$record[$i]['starting_toppinggallons'];
			break;
		}
		
		//check to see if we are running a full lot info and returning all records or only going up to a certain
		//wo, bol or wt.   If they have requested up to a certain point, then stop here.  The last record in this
		//array is what they will use to determine info on the lot at this current state.
		if (($type != "") & ($number != ""))
		{
			//    echo $record[$i]['data']['ID'].'<br>';
			if (($type == $record[$i]['type']) & ($number == $record[$i]['data']['ID']))
			{
				$record=array_slice($record,0,$i+1);
				showstructure($record[count($record)-1]['structure']);
				break;
			}
		}
		$record[$i]['difference']=($record[$i]['ending_tankgallons']+$record[$i]['ending_bbls']*60+$record[$i]['ending_toppinggallons'])-
		($record[$i]['starting_tankgallons']+$record[$i]['starting_bbls']*60+$record[$i]['starting_toppinggallons']);
		if($record[$i]['start_state']=='')
		$record[$i]['start_state']=$record[$i]['end_state'];
	}
	// echo '--------------';
	// debug ($record);
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
