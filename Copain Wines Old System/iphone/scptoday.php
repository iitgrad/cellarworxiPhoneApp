<?php

  session_start();

?>

<html>



<head>

  <title></title>

<link rel="stylesheet" type="text/css" href="iphone.css">

</head>



<body>

<?php



include ("../startdb.php");


	$query = 'select * from wo left outer join scp on (wo.ID=scp.WOID) where (wo.type="SCP" AND wo.enddate=curdate())';
  	$result = mysql_query($query);
	if (!$result) {echo 'query failed with'.mysql_error();}
	echo '<div class=Column><table width=100%>';
    for ($i=0; $i<mysql_num_rows($result); $i++)
	{
		$row=mysql_fetch_array($result);
		echo '<tr><td align=center>'.$row['LOT'].'</td><td align=center>'.$row['VARIETAL'].'<td align=center>'.$row['VINEYARD'].'</tr>';
	}
    echo "</table></div>"
?>



</body>



</html>

