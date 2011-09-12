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



   if ($_GET['caldate']!=0)

      $_SESSION['dateofwork']=date("Y-m-d",$_GET['caldate']);



   function showassets($timeslot)

   {

     echo '<table border="1" align="center" width="50%">';

     echo '<tr><td align="center">'.$_SESSION['dateofwork'].' ('.$timeslot.')</td></tr>';

     $theassets=getassetlist($_GET['assettype']);

     for ($i=0;$i<count($theassets);$i++)

     {

       echo '<tr><td align="center">';

       echo showstatus($theassets[$i]['id'],$_SESSION['dateofwork'],

	                   $timeslot,$_GET['assettype'],clientid($_SESSION['clientcode']),

                       $_GET['morning'],$_GET['noon'],$_GET['evening']);

       echo '</td></tr>';

     }

     echo '</table>';

   }



   if ($_GET['returnpage']!="")

       $_SESSION['returnpage']=$_GET['returnpage'];



   if ($_GET['action']=="reserve")

   {

      reserveasset($_GET['assetid'],$_SESSION['dateofwork'],$_GET['timeslot'],

	               clientid($_SESSION['clientcode']),$_SESSION['lot'],"REQUESTED");

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



  if ($_GET['morning']=="YES") showassets("MORNING"); echo "<br>";

  if ($_GET['noon']=="YES") showassets("NOON");  echo "<br>";

  if ($_GET['evening']=="YES") showassets("EVENING"); echo "<br>";





  echo '<center><a href='.$_SESSION['returnpage'].'>RETURN</a></center>';

?></body>



</html>