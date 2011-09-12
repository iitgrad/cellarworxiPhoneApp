<?php

  session_start();

  $result = session_is_registered('clientcode');

  session_register('clientcode');

  session_register('clientname');

?>

<html>

<head>

<link rel="stylesheet" type="text/css" href="../site.css">

</head>

<body>

<?php

  include ("startdb.php");

  include ("queryupdatefunctions.php");

  $theclientinfo=clientinfo($REMOTE_USER);

  $clientcode = $theclientinfo['clientcode'];

  $clientname = $theclientinfo['clientname'];

//  echo $REMOTE_USER;



  echo '

<p align="center"><b>WELCOME '.$clientname.'</b><br></p>

<div align="center">

  <center>

  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="86%" id="AutoNumber1">

    <tr>

      <td width="33%">

      <p align="center"><b>DOCUMENTS &amp; FORMS</b></td>

      <td width="33%">

      <p align="center"></td>

      <td width="34%">

      <p align="center"><b>NOTICES TO CLIENTS</b></td>

    </tr>

    <tr>

      <td width="33%">&nbsp;</td>

      <td width="33%">&nbsp;</td>

      <td width="34%">&nbsp;</td>

    </tr>

    <tr>

      <td width="33%">

      <p align="center"><font face="Franklin Gothic Book">

      <a href="Processing%20Procedures2%20rev%202.pdf">Protocols &amp; Procedures

      Document (V1.3)</a><br>

      <br>

      <font size="2"><a href="03SCP.pdf">STEMMING/CRUSHING PROTOCOL</a><br>

      <br>

      <a href="03FPP.pdf">FERMENTATION PROTOCOL</a><br>

      <br>

      <a href="03LRF.pdf">LABORATORY REQUEST</a></font></font><p align="center">

      <font face="Franklin Gothic Book"><font size="2">

      <a href="Press%20Sheet.pdf">PRESS SHEET</a><br>

      </font>

      <br>

      </font>

      <font face="Franklin Gothic Book" size="2"><a href="03pdr.pdf">

      PICKUP/DELIVERY REQUEST FORM</a><br><br>

      </font>

      <font face="Franklin Gothic Book" size="2"><a href="staff/tbinsign.pdf">

      TBIN SIGN</a></font></td>

      <td width="33%">

      <p align="center">



      <b><a href=custhome.php>'.$clientname.' DATA'.'</a></b>



      <td width="34%">

      <p align="center"><font face="Franklin Gothic Book">

      <a href="notice_001.htm">001 - PICKUPS &amp; DELIVERIES</a><br>

      <a href="notice_002.htm">002 - UPDATED SANITATION PROCEDURES</a><br>

      <a href="notice_003.htm">003 - SORTING LINE PROCESSING</a><br>

      <a href="notice_004.htm">004 - DRY ICE USAGE</a><br>

      <a href="notice_005.htm">005 - PRESS SCHEDULES</a></font></td>

    </tr>

    <tr>

      <td width="33%">

      <p align="center">&nbsp;</td>

      <td width="33%">

      <p align="center"><br>

      <br>';



     if (isstaff()=="YES")

     echo '<a href=staff.php>STAFF HOME PAGE</a>';



      echo '</td>

      <td width="34%">&nbsp;</td>

    </tr>

    </table>

  </center>

</div>

<p align="center"></p> ';

?>

</body>



</html>