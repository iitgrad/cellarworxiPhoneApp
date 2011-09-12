<?php
require_once('JSON.php');
require_once('startdb.php');
require_once('lotinforecords.php');

$json = new Services_JSON();

function getNextLotNumber($year,$clientid)
{
	$query='select * from lots where YEAR="'.$year.'" and CLIENTCODE="'.$clientid.'" order by LOTNUMBER DESC';
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	$firstrow=explode("-",$row['LOTNUMBER']);
	$max=$firstrow[2]+1;
	return $max;
}
function getclientcode($clientid)
{
    $query='select CODE from clients where clientid="'.$clientid.'"';
    $result=mysql_query($query);
    $row=mysql_fetch_array($result);
    return $row['CODE'];
}

if ($_GET['action']=="addlot")
{
	$year=substr($_GET['vintage'],-2);
	$code=getclientcode($_GET['clientid']);
	$lotnumber=sprintf("%02d",getNextLotNumber($_GET['vintage'],$_GET['clientid']));
	$newlotnumber=$year.'-'.$code.'-'.$lotnumber;

	$query='INSERT INTO lots SET lots.LOTNUMBER="'.$newlotnumber.'",'.
		'lots.DESCRIPTION="'.strtoupper($_GET['description']).'",'.
		'lots.YEAR="'.$_GET['vintage'].'",'.
		'lots.CLIENTCODE="'.$_GET['clientid'].'"';
	$result=mysql_query($query);
}

if ($_GET['action']=="modlot")

{
	$code=$_GET['clientid'];
	$newlotnumber=$_GET['lotnumber'];
	$query='select LOTNUMBER from lots where LOTNUMBER="'.$_GET['lotnumber'].'"';
	$result=mysql_query($query);
	if (mysql_num_rows($result)>0)
	{
		$query='UPDATE lots SET lots.DESCRIPTION="'.strtoupper($_GET['description']).'" WHERE LOTNUMBER="'.$_GET['lotnumber'].'" limit 1';
	}
	else
	{
		$query='INSERT INTO lots SET lots.LOTNUMBER="'.$newlotnumber.'",'.
			'lots.DESCRIPTION="'.strtoupper($_GET['description']).'",'.
			'lots.YEAR="'.$_GET['vintage'].'",'.
			'lots.CLIENTCODE="'.$_GET['clientid'].'"';
	}
	

	$result=mysql_query($query);

}



function hasreferences($db)
{
	if ($db=="wo")
		$query='SELECT count(LOT)as thecount from '.$db.' WHERE DELETED!=1 and LOT="'.$_GET['lotid'].'"';
	else
		$query='SELECT count(LOT)as thecount from '.$db.' WHERE LOT="'.$_GET['lotid'].'"';
	
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	if ($row['thecount']>0)
		return true;
	return false;
}

if ($_GET['action']=="deletelot")
{
	$result=0;
	if (!hasreferences('wt'))
	{
//		echo 'nowt';
//		if (!hasreferences('bolitems'))
//		{
//			echo 'nobol';
			if (!hasreferences('wo'))
			{
				$query='DELETE from lots WHERE LOTNUMBER="'.$_GET['lotid'].'" limit 1';
//				echo $query; exit;
				mysql_query($query);
				$result=1;
			}
//		}
	}
	$record[]=$result;
}

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