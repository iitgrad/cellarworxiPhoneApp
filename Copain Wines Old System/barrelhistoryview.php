<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

</head>



<body>



<?php

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");

include ("totalgallons.php");

include ("lotinforecords.php");



$bblnumber=$_GET['bblnumber'];

$history=bblhistory($bblnumber,time(),$_SESSION['clientcode']);

echo '<table align=center border=1 width=75%>';

echo '<tr>';

echo '<td align=center colspan=5><big><b>BARREL HISTORY<br><br></b></big></td></tr>';

echo '<tr>';

echo '<td align=center width=38%><b>FROM</b></td><td width=4%></td><td align=center width=5%><b>BBL#</b></td><td width=3%></td><td width=40% align=center><b>TO<br></b></td>';

echo '</tr>';

for ($i=0;$i<count($history);$i++)

{

	echo '<tr>';

	if ($history[$i]['direction']=="IN")

	{

		echo '<td align=center>'.$history[$i]['type'].' OF LOT: <a href=showlotinfo.php?lot='.$_history[$i]['lot'].'>'.$history[$i]['lot'].'</a> ON '.date("m/d/y",$history[$i]['date']).'</td>';

		echo '<td align=center>---></td>';

		echo '<td align=center>'.$bblnumber.'</td>';

		echo '<td colspan=2></td>';

	}

	else

	{

		echo '<td colspan=2></td>';

		echo '<td align=center>'.$bblnumber.'</td>';

		echo '<td align=center>---></td>';

		echo '<td align=center>'.$history[$i]['type'].' '.$history[$i]['lot'].' ON '.date("m/d/y",$history[$i]['date']).'</td>';

	}

	echo '</tr>';

}



?> 

