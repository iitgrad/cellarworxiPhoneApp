<?php

function isStaff($device)
{
	$query='select * from users where deviceid="'.$device.'" and deviceid!=""';
	$result=mysql_query($query);
	if (mysql_num_rows($result)>0)
	{
		$row=mysql_fetch_array($result);
		$authorization['clientid']=$row['clientid'];
		$authorization['staff']=$row['staff'];
		$authorization['group']=$row['group'];
	}
	return $authorization;
}