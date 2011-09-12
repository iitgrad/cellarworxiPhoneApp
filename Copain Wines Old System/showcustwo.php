<?php

    session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

    <script language="JavaScript" type="text/javascript">

    

    function navigate(choice)

    {

      var url=choice.options[choice.selectedIndex].value;

      if (url)

      {

        location.href=url;

      }

    }

  </script>

  	<script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>



</head>



<body>



<?php

  include ("startdb.php");

  include ("queryupdatefunctions.php");

  include ("assetfunctions.php");



  function nextstates($currentstate)

  {

    $state['PENDING']=array("ASSIGNED","COMPLETED","PENDING","WAITING ON CUSTOMER","HOLD", "CANCELED");

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

   function showallwobystatus($status,$clientcode)

  {

     $query='SELECT * FROM `wo` WHERE

       (`wo`.`STATUS` = "'.$status.'" AND

       `wo`.`CLIENTCODE` = "'.$clientcode.'" AND

       NOT ((wo.TYPE = "DRYICE") OR (wo.TYPE = "PUMP OVER") OR (wo.TYPE = "PUNCH DOWN"))) ORDER BY `wo`.`DUEDATE` DESC';



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

     $query='UPDATE wo SET ASSIGNEDTO="'.$_GET['assigned'].'" WHERE wo.ID='.$_GET['woid'].';';

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

     if ($_GET['newstate']=="COMPLETED")

       $query='UPDATE wo SET COMPLETIONDATE='.date('y-m-d').', STATUS="'.$_GET['newstate'].'" WHERE wo.ID='.$_GET['woid'].';';

     else

       $query='UPDATE wo SET STATUS="'.$_GET['newstate'].'" WHERE wo.ID='.$_GET['woid'].';';

     $result=mysql_query($query);

  }



function displaywobystatus($status,$showstates,$clientcode)

{

  echo '<table id=table1 border=1 align="center" width="90%">';

  echo '<tr><td colspan="7" align="center">ALL '.$status.' WORK ORDERS</td></tr>';

  $pwo=showallwobystatus($status,$clientcode);

   echo '<tr>';

   echo '<td align="center" >WO ID</td>';

   echo '<td align="center" >DUE DATE</td>';

//   echo '<td align="center" >CLIENT<BR>CODE</td>';

   echo '<td align="center" >LOT</td>';

   echo '<td align="center" >ACTIVITY</td>';

   echo '<td align="center" >COMMENTS</td>';

   echo '<td align="center" >ASSIGNED</td>';

   echo '<td align="center" >ACTION</td>';

   echo '</tr>';

  for ($i=0;$i<count($pwo);$i++)

  {

    echo '<tr>'.

     '<td align="center"><a href=wopage.php?action=view&returnpage='.$_SERVER['PHP_SELF'].'&woid='.$pwo[$i]['ID'].'>'.$pwo[$i]['ID'].'</a>'.'</td>'.

     '<td align="center">'.date("m/d/Y",strtotime($pwo[$i]['DUEDATE'])).'</td>'.

//     '<td align="center">'.$pwo[$i]['CLIENTCODE'].'</td>'.

     '<td align="center"><a href=showlotinfo.php?lot='.$pwo[$i]['LOT'].'>'.$pwo[$i]['LOT'].'</a></td>'.

     '<td align="center">'.$pwo[$i]['TYPE'].'</td>'.

     '<td align="left" width="300">'.$pwo[$i]['OTHERDESC'].'</td>';



     echo '<td align="center">';

       pic($pwo[$i]['ASSIGNEDTO'],FALSE,

         listassetsnames("INDIVIDUAL"),

//         'showwo.php?status='.$status.'&assignedto='.$pwo[$i]['ASSIGNEDTO'].'&action=assign&woid='.$pwo[$i]['ID'],

         'showwo.php?status='.$status.'&action=assign&woid='.$pwo[$i]['ID'],

         "assigned",

//         'showwo.php?status='.$status.'&assignedto='.$pwo[$i]['ASSIGNEDTO'].'&action=clearassigned&woid='.$pwo[$i]['ID']);

         'showwo.php?status='.$status.'&action=clearassigned&woid='.$pwo[$i]['ID']);

     echo '</td>';



    $states=nextstates($pwo[$i]['STATUS']);

     echo '<td align="center">';

    pic($pwo[$i]['STATUS'],

        $showstates[$pwo[$i]['ID']],

        nextstates($pwo[$i]['STATUS']),

        'showwo.php?status='.$status.'&assignedto='.$pwo[$i]['ASSIGNEDTO'].'&action=setnewstate&woid='.$pwo[$i]['ID'],

//        'showwo.php?status='.$status.'&action=setnewstate&woid='.$pwo[$i]['ID'],

        "newstate",

        'showwo.php?status='.$status.'&assignedto='.$pwo[$i]['ASSIGNEDTO'].'&action=clearstate&woid='.$pwo[$i]['ID']);

//        'showwo.php?status='.$status.'&action=clearstate&woid='.$pwo[$i]['ID']);

     echo '</td>';

 echo '</tr>';

  }

  echo '</table>';

  ?>

<script language="JavaScript">

<!--

tigra_tables('table1', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

// -->

			</script>

<?php

 }



  $ci=clientinfo($_SERVER['REMOTE_USER']);

  displaywobystatus($_GET['status'],$showstates,$ci['clientcode']);



?>



</body>



</html>

