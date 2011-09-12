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



$ci=clientinfo($_SERVER['REMOTE_USER']);

echo '<table width=100%>';

echo '<tr><td width=140 align=center><img src=logo.gif></td><td align=center>';

echo '<table align=center width=100%>';

echo '<td align=center>';

echo '<tr><td align=center><b><big>COPAIN WINE CELLARS</big></b></td></tr>';

//echo '<tr><td align=center>USER: '.strtoupper($_SERVER['REMOTE_USER']).' OF '.$ci['clientname'].'</td><td align=center><td>';

echo '</tr>';

echo '</td>';

echo '</table>';

echo '</td></tr></table>';

?>



</body>



</html>

