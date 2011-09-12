<?php
require_once('JSON.php');
require_once('startdb.php');
require_once('lotinforecords.php');
require_once('staff.php');
define('APPKEY','TMXhdpjcSlmKCZO3zkyrpg'); 
define('PUSHSECRET', 'dUCudu5nQAO2svq2Awmqng'); 
define('PUSHURL', 'https://go.urbanairship.com/api/push/'); 

	$json = new Services_JSON();
//	$record=$_REQUEST;
function sendPush($devtokens,$message,$badgeCount,$sound)
{
	// The device aliases you want to send to 
	$aliases =  array('steven'); 
	//device tokens 
	//$devices = array("8EF9E24E34DE5659BDDAC35CFA09DB8E8A04E0DF6C0F94AC953BB58EA4D4666D");
	$devices=$devtokens;
	$contents = array(); 
	$contents['badge'] = (int)$badgeCount; 
	$contents['alert'] = $message; 
	$contents['sound'] = $sound; 
	$push = array("aps" => $contents); 
	// if ($aliases) 
	//    $push["aliases"] = $aliases; 
	if ($devices) 
	   $push["device_tokens"] = $devices; 
	// echo '<pre>';
	// print_r($push);
	// exit;
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

	if ($_REQUEST['action']=='update_devtoken')
	{
		$query='select DEVTOKEN from devtokens where DEVTOKEN="'.$_REQUEST['DEVTOKEN'].'"';
		$result=mysql_query($query);
		if (mysql_num_rows($result)==0)
		{
			$query='insert into devtokens set DEVTOKEN="'.$_REQUEST['DEVTOKEN'].'", UDID="'.$_REQUEST['UDID'].'"';
			$result=mysql_query($query);
		}
	}	
	
	if ($_REQUEST['action']=='update_labtest')
	{
		if ($_REQUEST['ID']=="NEW")
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
	if ($_REQUEST['action']=='clear_newwos')
	{
		$query='delete from newwos where devtoken="'.$_REQUEST['DEVTOKEN'].'"';
		$result=mysql_query($query);
		$record['query']=$query;
	}
	
	if ($_REQUEST['action']=='delete_labtest')
	{
		 $query='delete from labresults where ID="'.$_REQUEST['ID'].'" LIMIT 1';
		 $result=mysql_query($query);		
	}
	if ($_REQUEST['action']=='update_lab')
	{
		$query='select * from labtest where WOID="'.$_REQUEST['WOID'].'"';
		$result=mysql_query($query);
		if (mysql_num_rows($result)==0)
		{
			$query='insert into labtest set LAB="'.$_REQUEST['LAB'].'", 
				   WOID="'.$_REQUEST['WOID'].'"';
			$result=mysql_query($query);
		}
		else
		{
			$query='update labtest set LAB="'.$_REQUEST['LAB'].'" 
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
		$code=$_REQUEST['clientid'];
		$newlotnumber=$_REQUEST['lotnumber'];
		$query='select LOTNUMBER from lots where LOTNUMBER="'.$_REQUEST['lotnumber'].'"';
		$result=mysql_query($query);
		if (mysql_num_rows($result)>0)
		{
			$query='UPDATE lots SET lots.DESCRIPTION="'.strtoupper($_REQUEST['description']).'" WHERE LOTNUMBER="'.$_REQUEST['lotnumber'].'" limit 1';
			mysql_query($query);
		}
		else
		{
			$query='INSERT INTO lots SET lots.LOTNUMBER="'.$newlotnumber.'",'.
				'lots.DESCRIPTION="'.strtoupper($_REQUEST['description']).'",'.
				'lots.YEAR="'.$_REQUEST['vintage'].'",'.
				'lots.CLIENTCODE="'.$_REQUEST['clientid'].'"';
			mysql_query($query);
		}

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
		$record=$_REQUEST;
		$record['query2']=$query;
		$result=mysql_query($query);
		if (!$result)
		{
			$record['query_result2']="ERROR";
			$record['the_error2']=mysql_error();			
		}
		else
			$record['query_resul2']="SUCCESSFUL";

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
				VINEYARD="'.$_REQUEST['VINEYARD'].'"';

//			$query='insert into scp set WHOLECLUSTER=0, HANDSORTING="YES", CRUSHING="NOCRUSHING", ESTTONS="0", WOID="'.$record['ID'].'"';
			$result=mysql_query($query);
			$record['ID']=mysql_insert_id();
			$record['query']=$query;
		}
		else
		{
			$query='update wo set DUEDATE="'.$_REQUEST['DELIVERYDATE'].'",ENDDATE="'.$_REQUEST['DELIVERYDATE'].'" WHERE ID="'.$_REQUEST['WOID'].'" LIMIT 1';
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
				VINEYARD="'.$_REQUEST['VINEYARD'].'" 
			      WHERE ID="'.$_REQUEST['ID'].'" LIMIT 1';
			$result=mysql_query($query);
			$record['query']=$query;
			
		}
	}
	
	if ($_REQUEST['action']=='delete_wo')
	{
		$query='update wo SET DELETED="1" where ID="'.$_REQUEST['ID'].'" limit 1';
		$result=mysql_query($query);
		$record['query']=$query;
	}
	if ($_REQUEST['action']=='update_wo')
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
			 TYPE="'.$_REQUEST['TYPE'].'",
			 ENDINGTANKGALLONS="'.$_REQUEST['ENDINGTANKGALLONS'].'",
			 ENDINGTOPPINGGALLONS="'.$_REQUEST['ENDINGTOPPINGGALLONS'].'",
			 ENDINGBARRELCOUNT="'.$_REQUEST['ENDINGBARRELCOUNT'].'",
			 LOT="'.$_REQUEST['LOT'].'",
			 CLIENTCODE="'.$clientcode.'",
			 STATUS="'.$_REQUEST['STATUS'].'",
			 OTHERDESC="'.$_REQUEST['OTHERDESC'].'",
			 WORKPERFORMEDBY="'.$_REQUEST['WORKPERFORMEDBY'].'"';
			$result=mysql_query($query);
			$record['ID']=mysql_insert_id();
			
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
				sendPush($devtokens,"Work Order ".$record['ID']." added",$row3['THECOUNT'],"default");				
			}
		}
		else
		{
			$record['ID']=$_REQUEST['ID'];
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
				VINEYARD="'.$_REQUEST['VINEYARD'].'"';
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
				VINEYARD="'.$_REQUEST['VINEYARD'].'" 
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
	}
	if ($_REQUEST['action']=='add_bindetail')
	{
		$query='insert into bindetail set BINCOUNT="'.$_REQUEST['BINCOUNT'].'",
		      WEIGHT="'.$_REQUEST['WEIGHT'].'",
			  TARE="'.$_REQUEST['TARE'].'",
			  WEIGHTAG="'.$_REQUEST['WEIGHTAG'].'"';
		$result=mysql_query($query);
		$record['ID']=mysql_insert_id();
	}
	if ($_REQUEST['action']=='update_bindetail')
	{
		$query='UPDATE bindetail set BINCOUNT="'.$_REQUEST['BINCOUNT'].'",
		      WEIGHT="'.$_REQUEST['WEIGHT'].'",
			  TARE="'.$_REQUEST['TARE'].'" WHERE ID="'.$_REQUEST['ID'].'"';
		$result=mysql_query($query);
	}	
	if (!$result)
	{
		$record['query_result']="ERROR";
		$record['the_error']=mysql_error();			
	}
	else
		$record['query_result']="SUCCESSFUL";
	
	$output = $json->encode($record);
	print $output;
	
?>
