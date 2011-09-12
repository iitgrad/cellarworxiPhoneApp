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



   $morning=$_SESSION['morning'];

   $noon=$_SESSION['noon'];

   $evening=$_SESSION['evening'];



  if ($_GET['action']=="reserve")

  {

     reserveasset($_GET['assetid'],$_SESSION['dateofwork'],$_GET['timeslot'],clientid($_SESSION['clientcode']),$_SESSION['lot'],"REQUESTED");

  }

     if ($_GET['action']=="delete")

   {

      deleteassetreservation($_GET['resid']);

   }



  $result = session_is_registered('clientcode');

  session_register('clientcode');

  session_register('clientname');



  $theclientinfo=clientinfo($REMOTE_USER);

//  $clientcode = $theclientinfo['clientcode'];

  $clientname = $theclientinfo['clientname'];



  $date=$_GET['todate'];



  function showfacility($timeslot)

  {

  echo '<table border="1" align="center" width="75%">';

  echo '<tr><td align="center">'.$_SESSION['dateofwork'].' ('.$timeslot.')</td></tr>';

  echo '</table>';

  echo '<table align="center" border="1"  width="75%" >

    <tr>

      <td width="10%" rowspan="4" align="center">'; echo showstatus(7,$_SESSION['dateofwork'],$timeslot,"WORKAREA",$_SESSION['clientcode'],"","",""); echo '</td>

      <td width="20%" colspan="2" align="center">'; echo showstatus(10,$_SESSION['dateofwork'],$timeslot,"WORKAREA",$_SESSION['clientcode'],"","",""); echo '</td>

      <td width="30%" colspan="3" align="center">'; echo showstatus(12,$_SESSION['dateofwork'],$timeslot,"WORKAREA",$_SESSION['clientcode'],"","",""); echo '</td>

      <td width="20%" rowspan="5" align="center">'; echo showstatus(35,$_SESSION['dateofwork'],$timeslot,"WORKAREA",$_SESSION['clientcode'],"","",""); echo '</td>

      

    </tr>

    <tr>

      <td width="10%" rowspan="3" align="center">'; echo showstatus(9,$_SESSION['dateofwork'],$timeslot,"WORKAREA",$_SESSION['clientcode'],"","",""); echo '</td>

      <td width="10%" rowspan="4" align="center">'; echo showstatus(11,$_SESSION['dateofwork'],$timeslot,"WORKAREA",$_SESSION['clientcode'],"","",""); echo '</td>

      <td width="20%" align="center">'; echo showstatus(15,$_SESSION['dateofwork'],$timeslot,"WORKAREA",$_SESSION['clientcode'],"","",""); echo '</td>

      <td width="10%" rowspan="2" align="center">'; echo showstatus(14,$_SESSION['dateofwork'],$timeslot,"WORKAREA",$_SESSION['clientcode'],"","",""); echo '</td>

      <td width="10%" rowspan="3" align="center">'; echo showstatus(17,$_SESSION['dateofwork'],$timeslot,"WORKAREA",$_SESSION['clientcode'],"","",""); echo '</td>

    </tr>

    <tr>

      <td width="20%" align="center">'; echo showstatus(13,$_SESSION['dateofwork'],$timeslot,"WORKAREA",$_SESSION['clientcode'],"","",""); echo '</td>

    </tr>

    <tr>

      <td width="30%" colspan="2" rowspan="2" align="center">'; echo showstatus(16,$_SESSION['dateofwork'],$timeslot,"WORKAREA",$_SESSION['clientcode'],"","",""); echo '</td>

    </tr>

    <tr>

      <td width="20%" colspan="2" align="center">'; echo showstatus(8,$_SESSION['dateofwork'],$timeslot,"WORKAREA",$_SESSION['clientcode'],"","",""); echo'</td>

      <td width="10%" align="center">'; echo showstatus(19,$_SESSION['dateofwork'],$timeslot,"WORKAREA",$_SESSION['clientcode'],"","",""); echo '</td>

    </tr>

  </table>

  </center>

</div>  ';

 }



 if ($morning=="YES") showfacility("MORNING"); echo "<br>";

 if ($noon=="YES") showfacility("NOON");  echo "<br>";

 if ($evening=="YES") showfacility("EVENING"); echo "<br>";

 echo '<center><a href='.$_SESSION['returnpage'].'>RETURN TO WORK ORDER PAGE</a></center>';

?>

</body>



</html>

