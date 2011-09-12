<?php

session_start();

?>
<html>
<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="iphone.css">

</head>
<body>
	<div class=Column>
	<table width=100% align=center>
		<tr><td align=center>
			<a href=picclient.php>CLIENT: <?php echo $_SESSION['clientcode']?></a>
		</td></tr>
		<tr><td align=center>
			<a href=lotlist.php>LOT LIST</a>
		</td></tr>
		<tr><td align=center>
			<a href=scptoday.php>TODAYS SCP'S</a>
		</td></tr>
	</table>
	</div>
</body>

