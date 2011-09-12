<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

</head>



<body>



<?php



   include ("startdb.php");

   include ("queryupdatefunctions.php");



  session_start();

  $result = session_is_registered('clientcode');

  session_register('clientcode');

  session_register('clientname');



  $theclientinfo=clientinfo($REMOTE_USER);

  $clientcode = $theclientinfo['clientcode'];

  $clientname = $theclientinfo['clientname'];



  $assetid=$_GET['assetid'];

  $date=$_GET['todate'];



  $clientcode="cop";



  $query='SELECT

  `wt`.`LOT`,

  `wt`.`VINEYARD`,

  `wt`.`VARIETY`

FROM

  `wt`

  INNER JOIN `clients` ON (`wt`.`CLIENTCODE` = `clients`.`clientid`)

WHERE

  (`clients`.`CODE` = UCASE("'.$clientcode.'"))';

//   echo $query;

  $result=mysql_query($query);

  $num_rows=mysql_num_rows($result);

  echo '<table align="center">';

  for ($i=0;$i<$num_rows;$i++)

  {

    $row=mysql_fetch_array($result);

    echo '<tr>';

    echo '<td align="center">';

    echo '<a href=showassetschedule.php?action=reserve&todate='.$date.'&assetid='.$assetid.'&lot='.$row['LOT'].'>'.$row['LOT'].

        '  '.$row['VINEYARD'].' '.$row['VARIETY'].'</a>';

    echo '</td>';

    echo '</tr>';

  }

  echo '</table>';

?>



</body>



</html>