<?php

session_start();

?>

<html>



<head>

  <title></title>

<link rel="stylesheet" type="text/css" href="../site.css">

     <script type="text/javascript" src="popup/overlibmws.js"></script>

   <script type="text/javascript" src="popup/overlibmws_bubble.js"></script>

     <script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>



</head>



<body>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div> 



<?php



include ("startdb.php");

include ("queryupdatefunctions.php");

include ("lotinforecords.php");





$query='select * from messages WHERE ENABLED="TRUE"';

$result=mysql_query($query);



for ($i=0;$i<mysql_num_rows($result);$i++)

{

	$row=mysql_fetch_array($result);

	

	$query2='select * from usermessagemap where USERID="'.$REMOTE_USER.'" and MESSAGEID="'.$row['ID'].'"'; 

//	echo $query2;

	$result2=mysql_query($query2);

	if (mysql_num_rows($result2)==0)

	{

		echo '<table align=center valign=center>';

		echo '<tr><td align=center><big><b>'.$row['MESSAGE'].'</big></b></td></tr>';

		echo '</table>';

	}

}





?>



</body>



</html>