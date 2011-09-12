<?php
require_once('JSON.php');
require_once('startdb.php');
require_once('lotinforecords.php');
require_once('staff.php');

$query='select * from clients';
$result=mysql_query($query);

for ($i=0;$i<mysql_num_rows($result);$i++)
{
	$row=mysql_fetch_assoc($result);

//	echo 'checking clientid: '.$row['clientid'].'<br>';
	
	$query2='select DISTINCT VINEYARD, COUNT(VINEYARD) AS VINEYARDCOUNT, clients.clientid from scp join wo on (scp.WOID=wo.ID)
	     join clients on (upper(wo.CLIENTCODE)=upper(clients.CODE)) where clients.clientid="'.$row['clientid'].'"  GROUP BY VINEYARD order by VINEYARD';

//	echo $query2;
	$result2=mysql_query($query2);
//	echo '<pre>';

	for ($j=0;$j<mysql_num_rows($result2);$j++)
	{
		$row2=mysql_fetch_assoc($result2);
		if (trim($row2['VINEYARD'])!="")
		{
			//		echo "vineyard:".trim($row2['VINEYARD']).'<br>';
			//		exit;
			//		print_r($row2);

					$vyd=trim($row2['VINEYARD']);
					$query3='select * from locations where CLIENTID="'.$row2['clientid'].'" and NAME="'.$vyd.'"';
//					echo $query3;
					$result3=mysql_query($query3);
					if (mysql_num_rows($result3)==0)
					{
						$query4='insert into locations set LOCATIONTYPE="VINEYARD", LAT="38.580714578", locations.LONG="-122.867145538", CLIENTID="'.$row2['clientid'].'", NAME="'.$vyd.'"';
//						echo $query4;
						$result4=mysql_query($query4);
						$vid=mysql_insert_id();
//						echo 'id returned:'.$id;
					}
					else
					{
						$row3=mysql_fetch_assoc($result3);
						$vid=$row3['ID'];
//						echo 'already in db:'.$id.'<br>';
					}			
		}
		
	}
}

$query='select scp.*,clients.clientid from scp join wo on (scp.WOID=wo.ID)
     join clients on (upper(wo.CLIENTCODE)=upper(clients.CODE))';
$result=mysql_query($query);
for ($i=0;$i<mysql_num_rows($result);$i++)
{
	$row=mysql_fetch_assoc($result);
	$query2='select ID from locations where CLIENTID="'.$row['clientid'].'" and NAME="'.$row['VINEYARD'].'"';
	$result2=mysql_query($query2);
	if (mysql_num_rows($result2)>0)
	{
		$row2=mysql_fetch_assoc($result2);
		$query3='update scp set VINEYARDID="'.$row2['ID'].'" where ID="'.$row['ID'].'"';
		mysql_query($query3);
		
	}
}
$query='select * from wt';
$result=mysql_query($query);
for ($i=0;$i<mysql_num_rows($result);$i++)
{
	$row=mysql_fetch_assoc($result);
	$query2='select ID from locations where CLIENTID="'.$row['CLIENTCODE'].'" and NAME="'.$row['VINEYARD'].'"';
	$result2=mysql_query($query2);
	if (mysql_num_rows($result2)>0)
	{
		$row2=mysql_fetch_assoc($result2);
		$query3='update wt set VINEYARDID="'.$row2['ID'].'" where ID="'.$row['ID'].'"';
		mysql_query($query3);
		
	}
}

?>
