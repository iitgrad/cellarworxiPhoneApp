<?php
require_once('JSON.php');
require_once('../server/startdb.php');
require_once('lotinforecords.php');
require_once('staff.php');

$query='select * from bolitems';
$result=mysql_query($query);
for ($i=0;$i<mysql_num_rows($result);$i++)
{
	$row=mysql_fetch_assoc($result);
	$vals=split('-',$row['LOT']);
	$clientcode=$vals[1];
	$query2='update bolitems set CLIENTCODE="'.$clientcode.'" where ID="'.$row['ID'].'" limit 1';
	echo $query2 . '<br>';
	mysql_query($query2);
}

?>
