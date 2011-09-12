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



  $result = session_is_registered('clientcode');

  session_register('clientid');

  session_register('clientcode');

  session_register('clientname');





  function asset_allocated($assetid, $date, $timeslot)

  {

    $query = 'SELECT *

       FROM

        `reservation`

       WHERE

        (`reservation`.`DATEALLOCATED` = "'.date("Y-m-d",$date).'") AND

        (`reservation`.`ASSETID` = "'.$assetid.'") AND

        (`reservation`.`STATUS` = "GRANTED")';

 //   echo $query;

    $result=mysql_query($query);

    return (mysql_num_rows($result)>0);

  }



  function movetopreviousday($caldate,$todate, $id, $assetid)

  {

    $val = '<a href=showassetschedule.php?action=move'.

    '&caldate='.$caldate.

    '&todate='.($todate-86400).

    '&id='.$id.

    '&assetid='.$assetid.

    '&fromdate='.$caldate.

    '> <-- </a>';



    return $val;

  }

   function movetonextday($caldate, $todate, $id, $assetid)

  {

    $val = '<a href=showassetschedule.php?action=move'.

    '&caldate='.$caldate.

    '&todate='.($todate+86400).

    '&id='.$id.

    '&assetid='.$assetid.

    '&fromdate='.$caldate.

    '> --> </a>';



    return $val;

  }



function getlot($id)

  {

    $query='SELECT `assets`.`NAME`,

      `wt`.`CLIENTCODE`,

      `wt`.`LOT`,

      `reservation`.`ID`

    FROM

      `reservation`

      INNER JOIN `assets` ON (`reservation`.`ASSETID` = `assets`.`ID`)

      INNER JOIN `assettypes` ON (`assets`.`TYPEID` = `assettypes`.`ID`)

      INNER JOIN `wt` ON (`reservation`.`FORLOT` = `wt`.`LOT`)

    WHERE

      (`reservation`.`ID` ="'.$id.'")';



    $result = mysql_query($query);

    $row_nums = mysql_num_rows($result);



    if ($row_nums>0)

    {

      $row=mysql_fetch_array($result);

      return $row['LOT'];

    }

    return "";

}



  function displaygrantstatus($row)

  {

     if ($row['STATUS']=="GRANTED")

       $val='['.$row['LOT'].']';

     else

       $val=$row['CODE'].' '.$row['LOT'].' (<a href=wopage.php?action=view&woid='.$row['WOID'].'>'.$row['WOID'].'</a>)';

     return $val;

  }

  function listreservedassets($timeslot,$epochdate,$assetid)

  {

    $query='SELECT  `wo`.`DUEDATE`,  `wo`.`ENDDATE`,  `assets`.`NAME`,  `wo`.`CLIENTCODE`, wo.LOT, wo.ID as WOID,

  `wo`.`MORNING`,  `wo`.`NOON`,  `wo`.`EVENING`,  `assets`.`ID`, wo.CLIENTCODE AS CODE,

  `reservation`.`DATEALLOCATED`

FROM

  `assets`  INNER JOIN `reservation` ON (`assets`.`ID` = `reservation`.`ASSETID`)

  INNER JOIN `wo` ON (`reservation`.`WOID` = `wo`.`ID`)

WHERE

  ( reservation.ASSETID="'.$assetid.'") AND

  ((

    (`wo`.`DUEDATE` <= "'.date('Y-m-d',$epochdate).'") AND (`wo`.`ENDDATE` >= "'.date('Y-m-d',$epochdate).'")

  ) OR

  (

    (`wo`.`DUEDATE` = "'.date('Y-m-d',$epochdate).'") AND (`wo`.`ENDDATE` IS NULL)

  ))';



//     echo $query.'<br>';



    $result = mysql_query($query);

    $row_nums = mysql_num_rows($result);



    $todaydate=date("Y-m-d",time());

    $todayepoch=strtotime($today);



    for ($i=0; $i<$row_nums; $i++)

    {

       $row=mysql_fetch_array($result);

       echo displaygrantstatus($row);

    }

    if ($row_nums==0) //then this asset is not reserved during the period of the wo.

      if($epochdate >= $todayepoch)

         echo '<a href=custwopage.php?action=newwithdate&dateofwork='.date('Y-m-d',$epochdate).'&timeslot='.$timeslot.'>.</a>';

 }





  $theclientinfo=clientinfo($REMOTE_USER);

  $clientcode = $theclientinfo['clientcode'];

  $clientname = $theclientinfo['clientname'];

  $clientid = $theclientinfo['clientid'];

//   echo $clientname . ' ' . $clientid;



  if ($_GET['caldate']=="")

     $epochtime=time();

  else

    $epochtime=$_GET['caldate'];



  $today=getdate($epochtime);

  $fromdate=getdate($_GET['fromdate']);

  $todate=getdate($_GET['todate']);



  $assetid=$_GET['assetid'];

  $lot=$_GET['lot'];



 // echo date("Y-m-d",$epochtime).'--'.$today['wday'];

  if ($today['wday']==0)

    $monday=$epochtime-(7*86400);

  else

    $monday=$epochtime-($today['wday']-1)*86400;



 // echo $today.'--'.date("Y-m-d",$monday);

  echo '<table height=50% width="100%" border="1">';

  echo '<tr>';

      echo '<td></td><td align="left">';

      echo '<a href=showassetschedule.php?caldate='.($epochtime-(7*86400)).'&assetid='.$_GET['assetid'].'>PREVIOUS WEEK</a>';

      echo '</td>';

      echo '<td colspan="2"></td>';

      echo '<td align="center">'.getassetname($_GET['assetid']).'</td>';

      echo '<td colspan="2"></td>';

      echo '<td align="right">';

      echo '<a href=showassetschedule.php?caldate='.($epochtime+(7*86400)).'&assetid='.$_GET['assetid'].'>NEXT WEEK</a>';

      echo '</td>';

  echo '</tr>';

  echo '<tr>';

  echo '<td>TIMESLOT</td>';

  for ($i=$monday;$i<($monday+(7*86400));$i=$i+86400)

  {

    if ($i==$_GET['caldate'])

      echo '<td align="center"><font color="red">'.date('Y-m-d',$i).'<br>'.date('l',$i).'</font><br></td>';

    else

      echo '<td align="center">'.date('Y-m-d',$i).'<br>'.date('l',$i).'</td>';

  }

  echo '</tr>';



  echo '<tr>';

  echo '<td>MORNING</td>';

  for ($i=$monday;$i<($monday+(7*86400));$i=$i+86400)

  {

      echo '<td align="center">';

      listreservedassets("MORNING",$i,$_GET['assetid']);

      echo '</td>';

  }

  echo '</tr>';



    echo '<tr>';

  echo '<td>NOON</td>';

  for ($i=$monday;$i<($monday+(7*86400));$i=$i+86400)

  {

      echo '<td align="center">';

      listreservedassets("NOON",$i,$_GET['assetid']);

      echo '</td>';

  }

  echo '</tr>';

    echo '<tr>';

  echo '<td>EVENING</td>';

  for ($i=$monday;$i<($monday+(7*86400));$i=$i+86400)

  {

      echo '<td align="center">';

      listreservedassets("EVENING",$i,$_GET['assetid']);

      echo '</td>';

  }

  echo '</tr>';

//  displayrow("MORNING",$monday,$_GET['assetid']);

//  displayrow("NOON",$monday,$_GET['assetid']);

//  displayrow("EVENING",$monday,$_GET['assetid']);

?>



</body>



</html>