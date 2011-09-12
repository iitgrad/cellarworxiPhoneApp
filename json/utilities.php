<?php
require_once('../../server/startdb.php');
require_once("../lotinforecords.php");

function getCompanyInfo()
{
	$query='select * from companyInfo limit 1';
	$result=mysql_query($query);
	$companyInfo=mysql_fetch_assoc($result);
	return $companyInfo;
}

?>