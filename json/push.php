<?php 
require_once('JSON.php');
require_once('startdb.php');
require_once('lotinforecords.php');
require_once('staff.php');

$query='SELECT devtokens.DEVTOKEN FROM users INNER JOIN devtokens ON users.deviceid = devtokens.UDID';
$result=mysql_query($query);
for ($i=0;$i<mysql_num_rows($result);$i++)
{
	$row=mysql_fetch_assoc($result);
	$thedevtoken=ereg_replace("[^A-Za-z0-9]","",$row['DEVTOKEN']);
	if (sizeof(explode('-',$row['DEVTOKEN']))==1)  //eliminate iphone simulator devtoken which has '-' in it
	{
		// $query2='insert into newwos set DEVTOKEN="'.$row['DEVTOKEN'].'", WOID="'.$record['ID'].'"';
		// $result2=mysql_query($query2);
		// $query3='select count(DEVTOKEN) as THECOUNT from newwos where DEVTOKEN="'.$row['DEVTOKEN'].'"';
		// $result3=mysql_query($query3);
		// $row3=mysql_fetch_assoc($result3);
		$devtokens[]=$thedevtoken;
	}
}
// echo '<pre>';
// print_r($devtokens);
// exit;
if (sizeof($devtokens)>0)
{
	sendPush($devtokens,"Work Order added","44","default");				
}

?>