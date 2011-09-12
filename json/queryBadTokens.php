<?php 
require_once('JSON.php');
require_once('../server/startdb.php');
require_once('lotinforecords.php');
require_once('staff.php');

// $query='SELECT DISTINCT devtokens.DEVTOKEN FROM users INNER JOIN devtokens ON users.deviceid = devtokens.UDID where not (users.deviceid like "%-%") and devtokens.DEVTOKEN like "%beb5%"';
// $result=mysql_query($query);
// for ($i=0;$i<mysql_num_rows($result);$i++)
// {
// 	$row=mysql_fetch_assoc($result);
// 	$thedevtoken=ereg_replace("[^A-Za-z0-9]","",$row['DEVTOKEN']);
// 	if (sizeof(explode('-',$row['DEVTOKEN']))==1)  //eliminate iphone simulator devtoken which has '-' in it
// 	{
// 		$devtokens[]=$thedevtoken;
// 	}
// }
// if (sizeof($devtokens)>0)
// {
	queryBadTokens();				
//}

?>