<?php

  session_start();

?>



<html>



<head>

<link rel="stylesheet" type="text/css" href="../site.css">

</head>



<body>

<?php

  include ("startdb.php");

  include ("queryupdatefunctions.php");

  $result = session_is_registered('clientcode');

  session_register('clientcode');

  session_register('clientname');



  $theclientinfo=clientinfo($REMOTE_USER);

  $clientcode = $theclientinfo['clientcode'];

  $clientname = $theclientinfo['clientname'];



echo '<p align="center"><b>'.$clientname.' (2003)</font></b></p>

<p align="center">

<a href="prod2.php?ccode='.$clientcode.'">FRUIT ARRIVAL WEIGHTS AND WEIGH TAGS</a></p>

<p align="center">

<a href="fermprot.php?ccode='.$clientcode.'">MAINTAIN FERMENTATION PROTOCOLS</a> /

<a href="activefermsbyclient.php?ccode='.$clientcode.'">&nbsp;VIEW FERMENTATION CURVES BY LOT</a><br>

<br>

<a href="./'.$clientcode.'/initialbrix.htm">INITIAL LAB RESULTS BY LOT</a>

<br> <br>

<align="center">2003 PRODUCTION BILL OF LADINGS<br>

(in) (<a href="./'.$clientcode.'/transfers_out.htm">out</a>)<br>

<br>';



?>



</body>



</html>