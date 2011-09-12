<?php
require_once('JSON.php');
require_once('../server/startdb.php');
require_once('lotinforecords.php');
require_once('staff.php');

$query='select ID,LOT,CLIENTCODE from wo';
$result=mysql_query($query);
for ($i=0;$i<mysql_num_rows($result);$i++)
{
	$row=mysql_fetch_assoc($result);
	$parts=split('-',$row['LOT']);
	if ($parts[1]!=$row['CLIENTCODE'])
		echo $row['ID'].'  '.$parts[1].'-'.$row['CLIENTCODE'].'<BR>';
}

?>
