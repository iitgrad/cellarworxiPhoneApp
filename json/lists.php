<?php
	require_once('JSON.php');
	require_once('../server/startdb.php');
	require_once('lotinforecords.php');
	require_once('staff.php');
	
	$json = new Services_JSON();
	
	$favorite=array();
	
	switch ($_GET['action'])
	{
		case "LOT" :
		{
			$query='select LOTNUMBER, DESCRIPTION from lots where CLIENTCODE="'.$_GET['clientcode'].'" and YEAR="'.$_GET['vintage'].'"';
			$result=mysql_query($query);
			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$list[]=mysql_fetch_assoc($result);
			}
			break;
		}
		case "CLIENTCODE" :
		{
			$query='select CODE from clients where ACTIVE="YES" order by CODE';
			$result=mysql_query($query);

			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$row=mysql_fetch_assoc($result);
				$list[]=$row['CODE'];
			}		
			break;
		}	
		case "CLIENTNAME" :
		{
			$query='select CLIENTNAME from clients where ACTIVE="YES" order by CLIENTNAME';
			$result=mysql_query($query);

			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$row=mysql_fetch_assoc($result);
				$list[]=$row['CLIENTNAME'];
			}		
			break;
		}	
		case "VINEYARD":
		{
			$query='select NAME, COUNT(NAME) AS VINEYARDCOUNT from locations where CLIENTID="'.$_GET['clientid'].'" GROUP BY NAME ORDER BY VINEYARDCOUNT DESC';
			$result=mysql_query($query);
			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$row=mysql_fetch_assoc($result);
				$favorite[]=$row['NAME'];
				if ($i==5)
					break;
			}
			$query='select NAME from locations where CLIENTCODE="'.$_GET['clientcode'].'" order by NAME';
			$result=mysql_query($query);

			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$row=mysql_fetch_assoc($result);
				$list[]=$row['NAME'];
			}		
			$record['favorites']=$favorite;
			$record['list']=$list;
		  break;
		}
		case "VARIETAL":
		{
		}
		case "VARIETY":
		{
			$query='select VARIETY, COUNT(VARIETY) AS VARIETYCOUNT from wt where CLIENTCODE="'.$_GET['clientid'].'" GROUP BY VARIETY ORDER BY VARIETYCOUNT DESC';
			$result=mysql_query($query);
			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$row=mysql_fetch_assoc($result);
				$favorite[]=$row['VARIETY'];
				if ($i==4)
					break;
			}
			$query='select NAME from varietals order by NAME';
			$result=mysql_query($query);

			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$row=mysql_fetch_assoc($result);
				$list[]=$row['NAME'];
			}		
			$record['favorites']=$favorite;
			$record['list']=$list;
		  break;
		}
		case "APPELLATION" :
		{
			$query='select APPELLATION, COUNT(APPELLATION) AS APPELLATIONCOUNT from wt where CLIENTCODE="'.$_GET['clientcode'].'" GROUP BY APPELLATION ORDER BY APPELLATIONCOUNT DESC';
			$result=mysql_query($query);
			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$row=mysql_fetch_assoc($result);
				$favorite[]=$row['APPELLATION'];
				if ($i==4)
					break;
			}
			$query='select NAME from appellations order by NAME';
			$result=mysql_query($query);

			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$row=mysql_fetch_assoc($result);
				$list[]=strtoupper($row['NAME']);
			}		
		
		  break;
		}
		case "REGIONCODE" :
		{
			$query='select NAME from zones order by NUMBER';
			$result=mysql_query($query);

			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$row=mysql_fetch_assoc($result);
				$list[]=$row['NAME'];
			}		

		  break;
		}
		case "WORKPERFORMEDBY" :
		{
			$sql = "SHOW COLUMNS FROM wo LIKE 'WORKPERFORMEDBY'";
			$result = mysql_query($sql);
			$query_data = mysql_fetch_array($result);

			if (eregi("('.*')", $query_data["Type"], $match)) {
			$enum_str = ereg_replace("'", "", $match[1]);
			$enum_options = explode(',', $enum_str);
			}

			sort($enum_options);
			$list=$enum_options;
		  break;
		}
		case "STATUS" :
		{
			$sql = "SHOW COLUMNS FROM wo LIKE 'STATUS'";
			$result = mysql_query($sql);
			$query_data = mysql_fetch_array($result);

			if (eregi("('.*')", $query_data["Type"], $match)) {
			$enum_str = ereg_replace("'", "", $match[1]);
			$enum_options = explode(',', $enum_str);
			}

			sort($enum_options);
			$list=$enum_options;
		  break;
		}
		case "TYPE" :
		{
			$sql = "SHOW COLUMNS FROM wo LIKE 'TYPE'";
			$result = mysql_query($sql);
			$query_data = mysql_fetch_array($result);

			if (eregi("('.*')", $query_data["Type"], $match)) {
			$enum_str = ereg_replace("'", "", $match[1]);
			$enum_options = explode(',', $enum_str);
			}

			sort($enum_options);
			$list=$enum_options;
			$query='select TYPE, COUNT(TYPE) AS TYPECOUNT from wo where CLIENTCODE="'.$_GET['clientcode'].'" GROUP BY TYPE ORDER BY TYPECOUNT DESC';
			$result=mysql_query($query);
			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$row=mysql_fetch_assoc($result);
				$favorite[]=$row['TYPE'];
				if ($i==2)
					break;
			}
			
		  break;
		}
		case "LABTESTS" :
		{
			$query='select * from validlabtests order by ID';
			$result=mysql_query($query);
			for ($i=0;$i<mysql_num_rows($result);$i++)
			{
				$row=mysql_fetch_assoc($result);
//				unset($temp);
				$temp[$row['LABTEST']]=$row;
//				$list[]=$temp;
			}
			$list=$temp;
			break;
		}
		case "LAB" :
		{
			$sql = "SHOW COLUMNS FROM labtest LIKE 'LAB'";
			$result = mysql_query($sql);
			$query_data = mysql_fetch_array($result);

			if (eregi("('.*')", $query_data["Type"], $match)) {
			$enum_str = ereg_replace("'", "", $match[1]);
			$enum_options = explode(',', $enum_str);
			}

			sort($enum_options);
			$list=$enum_options;
		  break;
		}
		case "CRUSHING" :
		{
			$sql = "SHOW COLUMNS FROM scp LIKE 'CRUSHING'";
			$result = mysql_query($sql);
			$query_data = mysql_fetch_array($result);

			if (eregi("('.*')", $query_data["Type"], $match)) {
			$enum_str = ereg_replace("'", "", $match[1]);
			$enum_options = explode(',', $enum_str);
			}

			sort($enum_options);
			$list=$enum_options;
		  break;
		}
	}
	
	$record['favorites']=$favorite;
	$record['list']=$list;
	
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

	
?>
