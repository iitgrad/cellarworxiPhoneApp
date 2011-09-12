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

  function resetvalues()

  {

     $_SESSION['lot']="";

     $_SESSION['dateofwork']="";

     $_SESSION['morning']="NO";

     $_SESSION['noon']="NO";

     $_SESSION['evening']="NO";

     $_SESSION['status']="";

     $morningcheck="";

     $nooncheck="";

     $eveningcheck="";

     $_SESSION['setcurrentaction']="";

     $_SESSION['woid']="";

     $_SESSION['type']="";

     $_SESSION['desc']="";

     $_SESSION['morningcheck']="";

     $_SESSION['nooncheck']="";

     $_SESSION['eveningcheck']="";

     $_SESSION['workperformedby']="";

  }



//  function updatecheckboxes($thetype)

 // {

  //    switch ($thetype)

  //    {

  //      case "BARRELING" : $_SESSION['barreling']="CHECKED"; break;

  //      case "BLENDING" : $_SESSION['blending']="CHECKED"; break;

  //      case "BOTTLING" : $_SESSION['bottling']="CHECKED"; break;

  //      case "FILTRATION" : $_SESSION['filtration']="CHECKED"; break;

  //      case "RACKING" : $_SESSION['racking']="CHECKED"; break;

  //      case "SETTLING" : $_SESSION['settling']="CHECKED"; break;

  //      case "STORAGE" : $_SESSION['storage']="CHECKED"; break;

  //      case "TOPPING" : $_SESSION['topping']="CHECKED"; break;

  //      case "OTHER" : $_SESSION['other']="CHECKED"; break;

  //    }

 // }



  $theclientinfo=clientinfo($REMOTE_USER);

  $clientcode = $theclientinfo['clientcode'];

  $clientname = $theclientinfo['clientname'];

  $_SESSION['custid']=$theclientinfo['clientid'];



  if (isset($_GET['lot'])) $_SESSION['lot']=$_GET['lot'];

  if (isset($_GET['assetid'])) $_SESSION['assetid']=$_GET['assetid'];

  if (isset($_GET['areaid'])) $_SESSION['workareaid']=$_GET['areaid'];

  if (isset($_GET['woid'])) $_SESSION['woid']=$_GET['woid'];

  if (isset($_POST['dateofwork'])) $_SESSION['dateofwork']=$_POST['dateofwork'];

  if (isset($_GET['action'])) $_SESSION['currentaction']=$_GET['action'];

//  echo $_SESSION['lot'];





  $creationdate=date('y-m-d');



  function checked($value)

  {

     if ($value == "ON")

       return 'checked';

     else

       return "";

  }



  if ($_SESSION['currentaction']=="view")

  {

      $_SESSION['currentaction']='view';

      $query='SELECT * FROM wo WHERE wo.ID='.$_SESSION['woid'];

      $result=mysql_query($query);

      $row=mysql_fetch_array($result);

      $creationdate=$row['CREATIONDATE'];

      $_SESSION['dateofwork']=$row['DUEDATE'];

      $_SESSION['status']=$row['STATUS'];

      $_SESSION['morning']=$row['MORNING'];

      $_SESSION['noon']=$row['NOON'];

      $_SESSION['workperformer']=$row['WORKPERFORMEDBY'];

      $_SESSION['evening']=$row['EVENING'];

      $tank=$row['VESSELID'];

      $_SESSION['workareaid']=$row['WORKAREAID'];

      $_SESSION['assetid']=$row['ASSETSID'];

      $_SESSION['desc']=$row['OTHERDESC'];

      $_SESSION['lot']=$row['LOT'];

      $_SESSION['type']=$row['TYPE'];

      $_SESSION['recordclientcode']=$row['CLIENTCODE'];

      $_SESSION['requestor']=$row['REQUESTOR'];

      if ($_SESSION['morning']=="YES") $_SESSION['morningcheck']="CHECKED"; else $morningcheck="";

      if ($_SESSION['noon']=="YES") $_SESSION['nooncheck']="CHECKED"; else $nooncheck="";

      if ($_SESSION['evening']=="YES") $_SESSION['eveningcheck']="CHECKED"; else $eveningcheck="";



      $_SESSION['currentaction']="";

  }



  if ($_GET['action']=="new")

  {

     resetvalues();

  }



  if ($_GET['action']=="newwithdate")

  {

     resetvalues();

     $_SESSION['dateofwork']=$_GET['dateofwork'];

     if ($_GET['timeslot']=="MORNING") $_SESSION['morning']="YES";

     if ($_GET['timeslot']=="NOON") $_SESSION['noon']="YES";

     if ($_GET['timeslot']=="EVENING") $_SESSION['evening']="YES";

    if ($_SESSION['morning']=="YES") $_SESSION['morningcheck']="CHECKED"; else $morningcheck="";

    if ($_SESSION['noon']=="YES") $_SESSION['nooncheck']="CHECKED"; else $nooncheck="";

    if ($_SESSION['evening']=="YES") $_SESSION['eveningcheck']="CHECKED"; else $eveningcheck="";

  //  updatecheckboxes($_SESSION['type']);

  }



  if ($_GET['action']=="setdateofwork")

  {

     $_SESSION['dateofwork']=$_POST['dateofwork'];

     $_SESSION['morning']=$_POST['morning'];

     $_SESSION['noon']=$_POST['noon'];

     $_SESSION['evening']=$_POST['evening'];

    if ($_SESSION['morning']=="YES") $_SESSION['morningcheck']="CHECKED"; else $morningcheck="";

    if ($_SESSION['noon']=="YES") $_SESSION['nooncheck']="CHECKED"; else $nooncheck="";

    if ($_SESSION['evening']=="YES") $_SESSION['eveningcheck']="CHECKED"; else $eveningcheck="";

  //  updatecheckboxes($_SESSION['type']);

  }





  if ($_GET['action']=="del")

  {

     $query='DELETE FROM wo WHERE wo.ID='.$_GET['woid'];

     delete_assets_tied_to_woid($_GET['woid']);

     $result=mysql_query($query);

     $_SESSION['currentaction']='del';

     resetvalues();



  }



  if($_GET['action']=="mod")

  {

     $type=$_POST['activity'];

     $_SESSION['type']=$type;



     $_SESSION['desc']=$_POST['comments'];

     $_SESSION['workperformer']=$_POST['workperformer'];



     $query='UPDATE wo SET

        wo.TYPE="'.$_SESSION['type'].'",

        wo.VESSELTYPE="TANK",

        wo.DUEDATE="'.$_SESSION['dateofwork'].'",

        wo.LOT="'.$_SESSION['lot'].'",

        wo.MORNING="'.$_SESSION['morning'].'",

        wo.NOON="'.$_SESSION['noon'].'",

        wo.EVENING="'.$_SESSION['evening'].'",

        wo.ASSETSID="'.$_SESSION['assetid'].'",

        wo.WORKPERFORMEDBY="'.$_SESSION['workperformer'].'",

        wo.WORKAREAID="'.$_SESSION['workareaid'].'",

        wo.OTHERDESC="'.$_SESSION['desc'].'",

        wo.STATUS="PENDING",

        wo.AUTOGENERATED="NO",

        wo.CREATIONDATE="'.date("Y-m-d").'",

        wo.CLIENTCODE="'.$clientcode.'"

        WHERE (wo.ID="'.$_SESSION['woid'].'")';

//      echo $query;

      $result=mysql_query($query);



      assign_wo_to_reservations($_SESSION['dateofwork'],$_SESSION['custid'],$_SESSION['woid']);

     $_SESSION['currentaction']='mod';

     resetvalues();

  }



  if($_GET['action']=="add")

  {

     $type=$_POST['activity'];

     $_SESSION['type']=$type;



     $_SESSION['desc']=$_POST['comments'];

     $_SESSION['workperformer']=$_POST['workperformer'];

     $_SESSION['recordclientcode']=$clientcode;

     $_SESSION['workperformer']=$REMOTE_USER;



     $query='INSERT INTO wo SET

        wo.TYPE="'.$type.'",

        wo.MORNING="'.$morning.'",

        wo.NOON="'.$noon.'",

        wo.EVENING="'.$evening.'",

        wo.VESSELTYPE="TANK",

        wo.VESSELID="'.$_POST['tank'].'",

        wo.DUEDATE="'.$_SESSION['dateofwork'].'",

        wo.LOT="'.$_SESSION['lot'].'",

        wo.OTHERDESC="'.$_SESSION['desc'].'",

        wo.WORKPERFORMEDBY="'.$_SESSION['workperformer'].'",

        wo.STATUS="PENDING",

        wo.AUTOGENERATED="NO",

        wo.CREATIONDATE="'.date("Y-m-d").'",

        wo.CLIENTCODE="'.$clientcode.'"';



      $result=mysql_query($query);



      assign_wo_to_reservations($_SESSION['dateofwork'],$_SESSION['custid'],mysql_insert_id());

     $_SESSION['currentaction']='add';

     resetvalues();

  }



  function showworkareas($returnpage)

  {

      $workareas=listallocassets("WORKAREA",$_SESSION['dateofwork'],$_SESSION['custid'],$_SESSION['woid']);

      if (count($workareas)==0)

          echo '<td align="center" width="25%"><a href=listworkareas.php?returnpage='.$returnpage.'>CHOOSE WORK AREA</a></td>';

      else

        {

          echo '<td align="center">';

          for ($i=0;$i<count($workareas);$i++)

          {

            echo '<a href=listworkareas.php?returnpage='.$returnpage.'>'.$workareas[$i]['timeslot'].' - '.$workareas[$i]['name'].'</a><br>';

          }

          echo '</td>';

        }

  }



  function showassets($assettype, $returnpage, $morning, $noon, $evening)

  {

      $assets=listallocassets($assettype,$_SESSION['dateofwork'],$_SESSION['custid'],$_SESSION['woid']);

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



  echo '<table align="center" border="1" width="100%">';

  echo '<tr>';

  echo '<td align="center" width="70%">';

     echo '<table align="center" width="100%" border="1">';

     echo '<tr>';

        echo '<td align="center" width="20%">CLIENT</td>';

        echo '<td align="center" width="20%">DATE SUBMITTED</td>';

        echo '<td align="center" width="20%">ASSIGNED</td>';

	    echo '<td align="center" width="20%">DATE OF WORK';

        echo '<td align="center" width="20%">LOT</td>';

     echo '</tr>';

     echo '<tr>';

        if ($recordclientcode==$clientcode)

          echo '<td align="center" width="20%">CLIENT: '.$clientname.

           '<br>REQUESTOR: '.$REMOTE_USER.

           '<br>WO ID: '.$_SESSION['woid'].

           '<br>STATUS: '.$_SESSION['status'].

           '</td>';

        else

          echo '<td align="center" width="20%">'.$recordclientname.'<br> REQUESTOR: '.$requestor.'<br>'.$_SESSION['woid'].'</td>';

        echo '<td align="center" width="20%">'.$creationdate.'</td>';

        showassets("INDIVIDUAL",$PHP_SELF,$_SESSION['morning'],$_SESSION['noon'],$_SESSION['evening']);

           echo '<form method="POST" action="custwopage.php?action=setdateofwork&woid='.$_GET['woid'].'">';

             if ($_GET['chosendate']!="") $dateofwork=$_GET['chosendate'];

             echo '<td align="center" width="20%"><input type="text" name="dateofwork" size="15" value='.$dateofwork.'>

                <a href=showcalendar.php?returnpage='.$PHP_SELF.'>CAL</a><br>';

             echo '<input type="checkbox" name="morning" value="YES"'. $_SESSION['morningcheck'] .'>M ';

             echo '<input type="checkbox" name="noon" value="YES" '. $_SESSION['nooncheck'] .'>N ';

             echo '<input type="checkbox" name="evening" value="YES" '. $_SESSION['eveningcheck'] .'>E ';

           echo '<input type="submit" value="" name="B1"></td></form>';

        if ($_SESSION['lot']=="")

          echo '<td align="center" width="20%"><a href=listlots.php>CHOOSE LOT#</a></td>';

        else

          {

            echo '<td align="center" width="20%"><a href=listlots.php>'.$_SESSION['lot'].'</a></td>';

          }

     echo '</tr><tr><td colspan="4">&nbsp;</td></tr>';

     echo '<tr>';

        echo '<td align="center">WORK AREA</td>';

        echo '<td align="center">TANKS</td>';

        echo '<td align="center">PUMPS</td>';

        echo '<td align="center">PRESS</td>';

     echo '</tr>';

     echo '<tr>';

        showworkareas($PHP_SELF);

        showassets("TANK",$PHP_SELF,$_SESSION['morning'],$_SESSION['noon'],$_SESSION['evening']);

        showassets("PUMP",$PHP_SELF,$_SESSION['morning'],$_SESSION['noon'],$_SESSION['evening']);

        showassets("PRESS",$PHP_SELF,$_SESSION['morning'],$_SESSION['noon'],$_SESSION['evening']);

        showassets("MISCELLANEOUS",$PHP_SELF,$_SESSION['morning'],$_SESSION['noon'],$_SESSION['evening']);

     echo '</tr>';

     echo '<tr><td colspan="4">&nbsp;</td></tr>';

     echo '<tr><td align="center" colspan="5">WORK TO BE PERFORMED</td></tr>';

    if ($_SESSION['woid']!=="")

     echo '<form method="POST" action="custwopage.php?action=mod">';

    else

     echo '<form method="POST" action="custwopage.php?action=add">';



        echo '<tr><td align="center" colspan="4"><table align="center"><tr>

        <td align="center"><input type="radio" name="activity" value="BARRELING" '; if($_SESSION['type']=="BARRELING") echo 'CHECKED'; echo '> Barreling</td>

        <td align="center"><input type="radio" name="activity" value="BLENDING"'; if($_SESSION['type']=="BLENDING") echo 'CHECKED'; echo '> Blending</td>

        <td align="center"><input type="radio" name="activity" value="BOTTLING"'; if($_SESSION['type']=="BOTTLING") echo 'CHECKED'; echo '> Bottling</td>

        <td align="center"><input type="radio" name="activity" value="FILTRATION"'; if($_SESSION['type']=="FILTRATION") echo 'CHECKED'; echo '> Filtration</td>

        <td align="center"><input type="radio" name="activity" value="RACKING"'; if($_SESSION['type']=="RACKING") echo 'CHECKED'; echo '> Racking</td>

        <td align="center"><input type="radio" name="activity" value="SETTLING"'; if($_SESSION['type']=="SETTLING") echo 'CHECKED'; echo '> Settling</td>

        <td align="center"><input type="radio" name="activity" value="STORAGE"'; if($_SESSION['type']=="STORAGE") echo 'CHECKED'; echo '> Storage</td>

        <td align="center"><input type="radio" name="activity" value="TOPPING"'; if($_SESSION['type']=="TOPPING") echo 'CHECKED'; echo '> Topping</td>

        <td align="center"><input type="radio" name="activity" value="COLDSTABALIZE"'; if($_SESSION['type']=="COLDSTABILIZE") echo 'CHECKED'; echo '> Cold Stabalize</td>

        <td align="center"><input type="radio" name="activity" value="OTHER"'; if($_SESSION['type']=="OTHER") echo 'CHECKED'; echo '> Other</td></tr></table>

        </td></tr>';

        echo '<tr><td colspan="5" align="center">COMMENTS<textarea rows="4" cols="70" name="comments">'.$_SESSION['desc'].'</textarea></td></tr>';

    echo '<tr><td colspan="5">&nbsp;</td></tr>';

     echo '<tr>';

     echo '<td align="center" colspan="5">WORK PERFORMED BY</td>';

     echo '<tr>';

        echo '<td colspan="4" align="center"><input type="radio" name="workperformer" value="CCC"'; if($_SESSION['workperformer']=="CCC") echo 'CHECKED'; echo '> COPAIN CUSTOM CRUSH';

        echo '<input type="radio" name="workperformer" value="CLIENT"'; if($_SESSION['workperformer']=="CLIENT") echo 'CHECKED'; echo '> '.$clientname;

     echo '</tr>';

     if ($_SESSION['woid']!="")

     {

        echo '<tr><td align="center"><input type="submit" value="UPDATE WORKORDER" name="B1"></td></tr>';

     }

     else

        echo '<tr><td align="center"><input type="submit" value="ADD WORKORDER" name="B1"></td></tr>';

    echo '<tr><td colspan="4">&nbsp;</td></tr>';

     echo '</form></table>';

  echo '</td>';



  //  RIGHT PART OF FORM

  echo '<td align="center" width="30%">';

    $querypending='SELECT `wo`.`ASSIGNEDTO`,`wo`.`COMPLETEBY`,`wo`.`COMPLETIONDATE`,`wo`.`STATUS`,`wo`.`LOT`,

      `wo`.`DUEDATE`,`wo`.`TYPE`,`wo`.`ID` FROM `wo` WHERE

      (`wo`.`CLIENTCODE` = "'.$clientcode.'") AND (`wo`.`STATUS` = "PENDING")';

     $queryactive='SELECT `wo`.`ASSIGNEDTO`,`wo`.`COMPLETEBY`,`wo`.`COMPLETIONDATE`,`wo`.`STATUS`,`wo`.`LOT`,

      `wo`.`DUEDATE`,`wo`.`TYPE`,`wo`.`ID` FROM `wo` WHERE

      (`wo`.`CLIENTCODE` = "'.$clientcode.'") AND (`wo`.`STATUS` = "APPROVED")';

     $querycompleted='SELECT `wo`.`ASSIGNEDTO`,`wo`.`COMPLETEBY`,`wo`.`COMPLETIONDATE`,`wo`.`STATUS`,`wo`.`LOT`,

      `wo`.`DUEDATE`,`wo`.`TYPE`,`wo`.`ID` FROM `wo` WHERE

      (`wo`.`CLIENTCODE` = "'.$clientcode.'") AND (`wo`.`STATUS` = "COMPLETED")';

     $resultpending=mysql_query($querypending);

     $resultactive=mysql_query($queryactive);

     $resultcompleted=mysql_query($querycompleted);

     $num_rows_pending=mysql_num_rows($resultpending);

     $num_rows_active=mysql_num_rows($resultactive);

     $num_rows_completed=mysql_num_rows($resultcompleted);





     echo '<table width=100% border="0">';

     echo '<tr><td align="center">PENDING WORK ORDERS  <a href=custwopage.php?action=new>(CREATE NEW)</a></td>';

     echo '<tr><td><table width="100%">';

        echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td>ID</td><td>LOT</td><td>ACTIVITY</td><td>DUEDATE</td></tr>';

        for ($i=0;$i<$num_rows_pending;$i++)

        {

           $row=mysql_fetch_array($resultpending);

           echo '<tr><td>&nbsp;</td><td><a href=custwopage.php?action=del&woid='.$row['ID'].

           '>del</a></td><td><a href=custwopage.php?action=view&woid='.$row['ID'].'>'.

           $row['ID'].'</a></td><td>'.$row['LOT'].'</td><td>'.

           $row['TYPE'].'</td><td>'.$row['DUEDATE'].'</td></tr>';

        }

        echo '</table>';

     echo '<tr><td>&nbsp;</td></tr>';

     echo '<tr><td align="center">ACTIVE/INPROGRESS WORK ORDERS</td>';

     echo '<tr><td><table width="100%">';

        echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td>ID</td><td>LOT</td><td>ACTIVITY</td><td>DUEDATE</td></tr>';

        for ($i=0;$i<$num_rows_active;$i++)

        {

           $row=mysql_fetch_array($resultactive);

           echo '<tr><td>&nbsp;</td><td><a href=custwopage.php?action=del&woid='.$row['ID'].

           '>del</a></td><td><a href=custwopage.php?action=view&woid='.$row['ID'].'>'.

           $row['ID'].'</a></td><td>'.$row['LOT'].'</td><td>'.$row['TYPE'].'</td><td>'.

           $row['DUEDATE'].'</td><td>'.$row['ASSIGNEDTO'].'</td></tr>';



        }

        echo '</table>';

     echo '<tr><td>&nbsp;</td></tr>';

     echo '<tr><td align="center">COMPLETED WORK ORDERS</td>';

     echo '<tr><td><table width="100%">';

        echo '<tr><td>&nbsp;</td><td>LOT</td><td>ACTIVITY</td><td>DUEDATE</td><td>COMPLETION DATE</td><td>COMPLETED BY</td></tr>';

        for ($i=0;$i<$num_rows_completed;$i++)

        {

           $row=mysql_fetch_array($resultcompleted);

           echo '<tr><td>&nbsp;</td><td>'.$row['LOT'].'</td><td>'.$row['TYPE'].'</td><td>'.$row['DUEDATE'].'</td><td>'.$row['COMPLETIONDATE'].'</td><td>'.$row['COMPLETEDBY'].'</td></tr>';

        }

        echo '</table>';

     echo '</table>';

  echo '</td></tr>';

  echo '</table>';

  echo '</form>';



?>



</body>



</html>