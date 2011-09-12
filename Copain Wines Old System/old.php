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



  function nextstates($currentstate)

  {

    $state['PENDING']=array("ASSIGNED","PENDING","WAITING ON CUSTOMER","HOLD", "CANCELED");

    $state['ASSIGNED']=array("ASSIGNED","COMPLETED","CANCELED","HOLD", "CANCELED");

    $state['HOLD']=array("ASSIGNED","HOLD","CANCELED","HOLD");

    $state['WAITING ON CUSTOMER']=array("ASSIGNED","WAITING ON CUSTOMER","CANCELED","HOLD");

    $state['COMPLETED']=array("ASSIGNED","COMPLETED","HOLD","CANCELED");

    return $state[$currentstate];

  }



  function showassets($assettype, $date, $returnpage, $morning, $noon, $evening)

  {

      $assets=listallocassets($assettype,$date,$_SESSION['custid'],$_SESSION['woid']);

      $params='?assettype='.$assettype.'&returnpage='.$returnpage.'&morning='.$morning.'&noon='.$noon.'&evening='.$evening;

      if (count($assets)==0)

          echo '<td align="center"><a href=listassets.php'.$params.'>CHOOSE '.$assettype.'(S)</a></td>';

      else

        {

          echo '<td align="center">';

          for ($i=0;$i<count($assets);$i++)

          {

            echo '<a href=listassets.php'.$params.'>'.$assets[$i]['timeslot'].' - '.$assets[$i]['name'].'</a><br>';

          }

          echo '</td>';

        }



  }

   function showallwobystatus($status)

  {

     $query='SELECT * FROM `wo`WHERE

       (`wo`.`STATUS` = "'.$status.'" AND

       NOT ((wo.TYPE = "DRYICE") OR (wo.TYPE = "PUMP OVER") OR (wo.TYPE = "PUNCH DOWN"))) ORDER BY  `wo`.`DUEDATE`';



    $result=mysql_query($query);

    $num_rows=mysql_num_rows($result);

    for ($j=0;$j<$num_rows;$j++)

    {

      $wo[$j]=mysql_fetch_array($result);



    }

    return $wo;

  }



  if ($_GET['action']=="assign")

  {

     $query='UPDATE wo SET ASSIGNEDTO="'.$_POST['assigned'].'" WHERE wo.ID='.$_GET['woid'].';';

     $result=mysql_query($query);

  }



  if ($_GET['action']=="clearassigned")

  {

     $query='UPDATE wo SET ASSIGNEDTO="" WHERE wo.ID='.$_GET['woid'].';';

     $result=mysql_query($query);

  }



  if ($_GET['action']=="clearstate")

  {  

	  $showstates[$_GET['woid']]="TRUE";

  }



  if ($_GET['action']=="setnewstate")

  {  

     $showstates[$_GET['woid']]="FALSE";

     $query='UPDATE wo SET STATUS="'.$_POST['newstate'].'" WHERE wo.ID='.$_GET['woid'].';';

     $result=mysql_query($query);

  }



function displaywobystatus($status,$showstates)

{

  echo '<table align="center" width="100%">';

  echo '<tr><td colspan="7" align="center">ALL '.$status.' WORK ORDERS</td></tr>';

  $pwo=showallwobystatus($status);

   echo '<tr>';

   echo '<td align="center" width="10%">WO ID</td>';

   echo '<td align="center" width="10%">DUE DATE</td>';

   echo '<td align="center" width="10%">CLIENT<BR>CODE</td>';

   echo '<td align="center" width="10%">LOT</td>';

   echo '<td align="center" width="15%">ACTIVITY</td>';

   echo '<td align="center" width="15%">ASSIGNED</td>';

   echo '<td align="center" width="15%">ACTION</td>';

   echo '</tr>';

  for ($i=0;$i<count($pwo);$i++)

  {

    echo '<tr>'.

     '<td align="center"><a href=wopage.php?action=view&returnpage='.$_SERVER['PHP_SELF'].'&woid='.$pwo[$i]['ID'].'>'.$pwo[$i]['ID'].'</a>'.'</td>'.

     '<td align="center">'.$pwo[$i]['DUEDATE'].'</td>'.

     '<td align="center">'.$pwo[$i]['CLIENTCODE'].'</td>'.

     '<td align="center">'.$pwo[$i]['LOT'].'</td>'.

     '<td align="center">'.$pwo[$i]['TYPE'].'</td>';



     echo '<td align="center">';

       pic($pwo[$i]['ASSIGNEDTO'],FALSE,

         listassetsnames("INDIVIDUAL"),

         'staffwopanel.php?action=assign&woid='.$pwo[$i]['ID'],

         "assigned",

         'staffwopanel.php?action=clearassigned&woid='.$pwo[$i]['ID']);

     echo '</td>';



    $states=nextstates($pwo[$i]['STATUS']);

     echo '<td align="center">';

    pic($pwo[$i]['STATUS'],

        $showstates[$pwo[$i]['ID']],

        nextstates($pwo[$i]['STATUS']),

        'staffwopanel.php?action=setnewstate&woid='.$pwo[$i]['ID'],

        "newstate",

        'staffwopanel.php?action=clearstate&woid='.$pwo[$i]['ID']);

     echo '</td>';

 echo '</tr>';

  }

  echo '</table>';

 }

 echo '<a href=wopage.php?action=new>CREATE NEW WO</a>';



  displaywobystatus("PENDING",$showstates);

  echo '<br>&nbsp;<br>';

  displaywobystatus("ASSIGNED",$showstates);  echo '<br>&nbsp;<br>';

  displaywobystatus("COMPLETED",$showstates);



?>



</body>



</html>

