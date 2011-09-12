<?php
require_once('JSON.php');
require_once('../server/startdb.php');
require_once('lotinforecords.php');
require_once('staff.php');

$query='select * from wo where (CLIENTCODE="" and DELETED="NO")';
//echo $query;
$result=mysql_query($query);
for ($i=0; $i<mysql_num_rows($result); $i++)
{
	$row=mysql_fetch_assoc($result);
	$lotTokens=explode('-',$row['LOT']);
	
	if (strlen($lotTokens[1])==3)
	{
		echo '<pre>';
		// print_r($row);
		// print_r($lotTokens);
		$query2='update wo set CLIENTCODE="'.$lotTokens[1].'" where ID="'.$row['ID'].'" limit 1';
		$result2=mysql_query($query2);
		echo $query2;
	}
}
?>