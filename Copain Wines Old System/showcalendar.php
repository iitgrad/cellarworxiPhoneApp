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



   if ($_GET['returnpage']!="") $returnpage=$_GET['returnpage'];



  $showassetidfilter=$_SESSION['showassetidfilter'];

  $_SESSION['calendardefaultdisplay'];





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

       $val=$row['CODE'].' '.$row['LOT'].' (<a href=custwopage.php?action=view&woid='.$row['WOID'].'>'.$row['WOID'].'</a>)';

     return $val;

  }

  function listreservedassets($timeslot,$epochdate,$assetid)

  {

    $query='SELECT

  `assets`.`NAME`,

  `assettypes`.`NAME` AS `NAMETYPE`,

  `reservation`.`ID`,

  `reservation`.`TIMESLOT`,

  `reservation`.`STATUS`,

  `reservation`.`WOID`,

  `clients`.`CODE`

FROM

  `reservation`

  INNER JOIN `assets` ON (`reservation`.`ASSETID` = `assets`.`ID`)

  INNER JOIN `assettypes` ON (`assets`.`TYPEID` = `assettypes`.`ID`)

  INNER JOIN `clients` ON (`reservation`.`CUSTID` = `clients`.`clientid`)

       WHERE (reservation.TIMESLOT = "'.$timeslot.'") AND (`reservation`.`DATEALLOCATED` = "'.date('Y-m-d',$epochdate).'") AND (`reservation`.`ASSETID` ="'.$assetid.'")';

//     echo $query.'<br>';

    $result = mysql_query($query);

    $row_nums = mysql_num_rows($result);

    for ($i=0; $i<$row_nums; $i++)

    {

       $row=mysql_fetch_array($result);

       echo displaygrantstatus($row);

    }

    if ($row_nums==0) echo 'CREATE WO';

  }



  if ($_GET['caldate']=="")

     $epochtime=time();

  else

    $epochtime=$_GET['caldate'];



  $today=getdate($epochtime);

  $fromdate=getdate($_GET['fromdate']);

  $todate=getdate($_GET['todate']);



  $assetid=$_GET['assetid'];



 // echo date("Y-m-d",$epochtime).'--'.$today['wday'];

  if ($today['wday']==0)

    $monday=$epochtime-(7*86400);

  else

    $monday=$epochtime-($today['wday']-1)*86400;



 // echo $today.'--'.date("Y-m-d",$monday);

  echo '<table width="100%" border="1">';

  echo '<tr>';

      echo '<td></td><td align="left">';

      echo '<a href=showcalendar.php?returnpage='.$_GET['returnpage'].'&returnvar='.$_GET['returnvar'].'&caldate='.($epochtime-(7*86400)).'&assetid='.$_GET['assetid'].'>PREVIOUS WEEK</a>';

      echo '</td>';

      echo '<td colspan="2"></td>';

      echo '<td align="center">'.getassetname($_GET['assetid']).'</td>';

      echo '<td colspan="2"></td>';

      echo '<td align="right">';

      echo '<a href=showcalendar.php?returnpage='.$_GET['returnpage'].'&returnvar='.$_GET['returnvar'].'&caldate='.($epochtime+(7*86400)).'&assetid='.$_GET['assetid'].'>NEXT WEEK</a>';

      echo '</td>';

  echo '</tr>';

  echo '<tr>';

  echo '<td>TIMESLOT</td>';

  $count=0;

  for ($i=$monday;$i<($monday+(7*86400));$i=$i+86400)

  {

    if ($i==$_GET['caldate'])

      echo '<td align="center"><font color="red">'.date('Y-m-d',$i).'<br>'.date('l',$i).'</font><br></td>';

    else

      echo '<td align="center">'.date('l',$i).'</td>';

  }

  echo '</tr>';



  echo '<tr>';

  echo '<td></td>';

  for ($i=$monday;$i<($monday+(28*86400));$i=$i+86400)

  {

      echo '<td align="center">';

      echo '<a href='.$returnpage.'?'.$_GET['returnvar'].'='.date("Y-m-d",$i).'>'.date("m-d",$i).'</a>';

      echo '</td>';

      if ($count==6){ echo '</tr><tr><td></td>'; $count=0;} else $count++;

  }

  echo '</tr></table>';

  echo '<center><a href=assetpicklist.php?returnpage=showcalendar.php>SHOW ASSETS</a>';



//  echo 'hello'.$showassetidfilter;

  for ($i=0;$i<count($showassetidfilter);$i++)

  {

    echo $showassetidfilter[$i]['NAME'].'<br>';

  }

//  displayrow("MORNING",$monday,$_GET['assetid']);

//  displayrow("NOON",$monday,$_GET['assetid']);

//  displayrow("EVENING",$monday,$_GET['assetid']);

?>



</body>



</html>

