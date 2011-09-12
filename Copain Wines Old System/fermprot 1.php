<?php

session_start();

?>

<html>



<head>

<title>Fermentation Protocol</title>

<link rel="stylesheet" type="text/css" href="../site.css">

    <script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>

      <script type="text/javascript" src="popup/overlibmws.js"></script>

   <script type="text/javascript" src="popup/overlibmws_bubble.js"></script>



<?php



include ("startdb.php");

include ("yesno.php");

include ("setcheck.php");

include ("defaultvalue.php");

include ("manageadditions.php");

include ("queryupdatefunctions.php");



$query='SELECT * from lots inner join clients on (lots.CLIENTCODE=clients.CLIENTID) where ((lots.YEAR="'.$_SESSION['vintage'].'") AND

     (clients.CODE="'.$_SESSION['clientcode'].'"))';

$result=mysql_query($query);

?>

</head>



<body>



<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div> 

<?php





$query2="SELECT

  `wt`.`DATETIME`,

  `wt`.`LOT`,

  `wt`.`CLIENTCODE`,

  `wt`.`VARIETY`,

  `wt`.`VINEYARD`,

  `clients`.`CODE`

FROM

  `wt`

  INNER JOIN `clients` ON (`wt`.`CLIENTCODE` = `clients`.`clientid`)

WHERE

  (`clients`.`CODE` = '".$_SESSION['clientcode']."')";





$queryactiveferms='SELECT * FROM fermprot

    WHERE (fermprot.CLIENTCODE="'.$_SESSION['clientcode'].'" AND

          fermprot.STATUS="ACTIVE")

    ORDER BY

         fermprot.LOT';



$queryferms='SELECT * FROM fermprot

    WHERE (fermprot.CLIENTCODE="'.$_SESSION['clientcode'].'")

    ORDER BY

         fermprot.LOT';



$queryclosedferms='SELECT * FROM fermprot

    WHERE (fermprot.CLIENTCODE="'.$_SESSION['clientcode'].'" AND

          fermprot.STATUS="CLOSED")

    ORDER BY

         fermprot.LOT';



if ($_GET['status']=="ACTIVE" | $_GET['status']=="CLOSED")

{

    $updateferm='UPDATE fermprot SET STATUS="'.$_GET['status'].'"

    WHERE (fermprot.id="'.$_GET['recid'].'")';

    mysql_query($updateferm);

}



if ($_GET['modification']=="DEL")

{

    $updateferm='DELETE from fermprot

    WHERE fermprot.id="'.$_GET['recid'].'"';

    mysql_query($updateferm);

}



if ($_GET['modification']=="deladdition")

{

    // first, delete the addition record itself

    $query='SELECT fpaddmap.ADDITIONID FROM

        fpaddmap WHERE fpaddmap.ID='.$_GET['additionid'];

    //    echo $query.'<br>';

    $result=mysql_query($query);

    $row=mysql_fetch_array($result);

    

    $deladdition='DELETE from additions

       WHERE additions.ID="'.$row['ADDITIONID'].'"';

    $result=mysql_query($deladdition);

    

    // then, delete the ferm map record

    $deletefermmap='DELETE from fpaddmap

    WHERE fpaddmap.id="'.$_GET['additionid'].'"';

    mysql_query($deletefermmap);

    //  echo $deletefermmap;

    

    $queryfermstemp='SELECT * FROM fermprot WHERE (fermprot.id="'.$_GET['recid'].'")';

    $row =mysql_fetch_array(mysql_query($queryfermstemp));

    

}



if ($_GET['modification']=="VIEW")

{

    $queryfermstemp='SELECT * FROM fermprot WHERE (fermprot.id="'.$_GET['recid'].'")';

    $row =mysql_fetch_array(mysql_query($queryfermstemp));

}



if ($_GET['modification']=="addaddition")

{

    $brix="NULL";

    $labtest="NULL";

    $daycount="NULL";

    

    if ($_POST['DAYCOUNT']!="")

    $daycount=$_POST['DAYCOUNT'];

    if ($_POST['BRIX']!="")

    $brix=$_POST['BRIX'];

    if ($_POST['LABTEST']!="")

    $labtest='"'.$_POST['LABTEST'].'"';

    

    $addaddition = 'INSERT INTO additions SET

      BRIX='.$brix.',

      LABTEST='.$labtest.',

      SUPERFOODAMT="'.$_POST['SF'].'",

      DAPAMOUNT="'.$_POST['DAP'].'",

      HTAAMOUNT="'.$_POST['HTA'].'",

      BLEEDAMOUNT="'.$_POST['BLEEDAMOUNT'].'",

      DAYCOUNT='.$daycount.',

      WATERAMOUNT="'.$_POST['H20'].'",

      INNOCULATIONBRAND="'.$_POST['INOCBRAND'].'",

      INNOCULATIONAMOUNT="'.$_POST['INOCAMT'].'"';

    

    //      echo $addaddition;

    mysql_query($addaddition);

    $additionid=mysql_insert_id();

    

    $maptofermprot = 'INSERT INTO fpaddmap SET

      DATE="'.$_POST['DATE'].'",

      FERMPROTID="'.$_POST['FERMPROTRECID'].'",

      ADDITIONID="'.$additionid.'"';

    //  echo $maptofermprot;

    mysql_query($maptofermprot);

    

    $queryfermstemp='SELECT * FROM fermprot WHERE (fermprot.id="'.$_GET['recid'].'")';

    $row =mysql_fetch_array(mysql_query($queryfermstemp));

    

}



if ($_GET['modification']=="ADD")

{

    $queryupdate = 'INSERT INTO fermprot SET

     STATUS="ACTIVE",

      PO="'.setcheck($_POST['POYESNO']).'",

      PD="'.setcheck($_POST['PDYESNO']).'",

      PD2="'.setcheck($_POST['PDYESNO2']).'",

      POSTARTBRIX='.defaultvalue($_POST['POSTARTBRIX']).',

      POENDBRIX='.defaultvalue($_POST['POENDBRIX']).',

      POFREQ='.defaultvalue($_POST['POFREQ']).',

      PODURATION='.defaultvalue($_POST['PODURATION']).',

      PDSTARTBRIX='.defaultvalue($_POST['PDSTARTBRIX']).',

      PDENDBRIX='.defaultvalue($_POST['PDENDBRIX']).',

      PDFREQ='.defaultvalue($_POST['PDFREQ']).',

      PDSTRENGTH="'.$_POST['PDSTRENGTH'].'",

      TIMESLOT1="'.$_POST['TIMESLOT1'].'",

      TIMESLOT2="'.$_POST['TIMESLOT2'].'",

      PDSTARTBRIX2='.defaultvalue($_POST['PDSTARTBRIX2']).',

     PDENDBRIX2='.defaultvalue($_POST['PDENDBRIX2']).',

     PDFREQ2='.defaultvalue($_POST['PDFREQ2']).',

     PDSTRENGTH2="'.$_POST['PDSTRENGTH2'].'",

     VESSELTYPE="'.$_POST['VESSELTYPE'].'",

     LOT="'.$_POST['LOT'].'",

     COMMENT="'.strtoupper($_POST['COMMENT']).'",

     CLIENTCODE="'.$_SESSION['clientcode'].'",

     VESSELID="'.$_POST['VESSELID'].'"';

    //   echo $queryupdate;

    mysql_query($queryupdate);

    $therecid=mysql_insert_id();

    $queryfermstemp='SELECT * FROM fermprot WHERE (fermprot.id="'.$therecid.'")';

    //    echo $queryfermstemp;

    $row =mysql_fetch_array(mysql_query($queryfermstemp));

}



if ($_GET['modification']=="DUPLICATE")

{

    $query='select * from fermprot where ID="'.$_GET['fermprotid'].'"';

    $result=mysql_query($query);

    $row=mysql_query($result);

    

    $queryupdate = 'INSERT into fermprot SET



      PO="'.$row['PO'].'",

      PO2="'.$row['PO2'].'",

      PD="'.$row['PD'].'",

      PD2="'.$row['PD2'].'",

      POSTARTBRIX2="'.$row['POSTARTBRIX2'].'",

      POENDBRIX2="'.$row['POENDBRIX2'].'",

      POFREQ2="'.$row['POFREQ2'].'",

      PODURATION2="'.$row['PODURATION'].'",

      POSTARTBRIX="'.$row['POSTARTBRIX'].'",

      POENDBRIX="'.$row['POENDBRIX'].'",

      POFREQ="'.$row['POFREQ'].'",

      PODURATION="'.$row['PODURATION'].'",

      PDSTARTBRIX="'.$row['PDSTARTBRIX'].'",

      PDENDBRIX="'.$row['PDENDBRIX'].'",

      POFREQ="'.$row['POFREQ'].'",

      TIMESLOT3="'.$_POST['TIMESLOT3'].'",

      PODURATION="'.$row['PODURATION'].'",

      PDSTARTBRIX="'.$row['PDSTARTBRIX'].'",

      PDENDBRIX="'.$row['PDENDBRIX'].'",

      PDFREQ="'.$row['PDFREQ'].'",

      TIMESLOT1="'.$row['TIMESLOT1'].'",

      TIMESLOT2="'.$row['TIMESLOT2'].'",

      POTIMESLOT2="'.$row['POTIMESLOT2'].'",

      PDSTRENGTH="'.$row['PDSTRENGTH'].'",

      PDSTARTBRIX2="'.$row['PDSTARTBRIX2'].'",

      PDENDBRIX2="'.$row['PDENDBRIX2'].'",

      PDFREQ2="'.$row['PDFREQ2'].'",

      PDSTRENGTH2="'.$row['PDSTRENGTH2'].'",

      LOT="'.$row['LOT'].'",

      COMMENT="'.$row['COMMENT'].'",

      VESSELTYPE="'.$row['VESSELTYPE'].'",

      VESSELID="'.$row['VESSELID'].'")"';

    

    echo $queryupdate;

    //   mysql_query($queryupdate);

    $_GET['recid']=mysql_insert_id();

    $queryfermstemp='SELECT * FROM fermprot WHERE (fermprot.id="'.$_GET['recid'].'")';

    $row=mysql_fetch_array(mysql_query($queryfermstemp));

}



if ($_GET['modification']=="UPDATE")

{

    $theasset=explode("-",$_POST['asset']);

    $queryupdate = 'UPDATE fermprot SET



      PO="'.setcheck($_POST['POYESNO']).'",

      PO2="'.setcheck($_POST['POYESNO2']).'",

      PD="'.setcheck($_POST['PDYESNO']).'",

      PD2="'.setcheck($_POST['PDYESNO2']).'",

      POSTARTBRIX2='.defaultvalue($_POST['POSTARTBRIX2']).',

      POENDBRIX2='.defaultvalue($_POST['POENDBRIX2']).',

      POFREQ2='.defaultvalue($_POST['POFREQ2']).',

      PODURATION2='.defaultvalue($_POST['PODURATION2']).',

      POSTARTBRIX="'.$_POST['POSTARTBRIX'].'",

      POENDBRIX="'.$_POST['POENDBRIX'].'",

      POFREQ="'.$_POST['POFREQ'].'",

      PODURATION="'.$_POST['PODURATION'].'",

      PDSTARTBRIX="'.$_POST['PDSTARTBRIX'].'",

      PDENDBRIX="'.$_POST['PDENDBRIX'].'",

      PDFREQ="'.$_POST['PDFREQ'].'",

      TIMESLOT1="'.$_POST['TIMESLOT1'].'",

      TIMESLOT2="'.$_POST['TIMESLOT2'].'",

      POTIMESLOT2="'.$_POST['POTIMESLOT2'].'",

      TIMESLOT3="'.$_POST['TIMESLOT3'].'",

      PDSTRENGTH="'.$_POST['PDSTRENGTH'].'",

      PDSTARTBRIX2="'.$_POST['PDSTARTBRIX2'].'",

     PDENDBRIX2="'.$_POST['PDENDBRIX2'].'",

     PDFREQ2="'.$_POST['PDFREQ2'].'",

     PDSTRENGTH2="'.$_POST['PDSTRENGTH2'].'",

     LOT="'.$_POST['LOT'].'",

     COMMENT="'.strtoupper($_POST['COMMENT']).'",

     VESSELTYPE="'.$theasset[0].'",

     VESSELID="'.$theasset[1].'"

     WHERE (fermprot.id="'.$_GET['recid'].'")';

    //   echo $queryupdate;

    mysql_query($queryupdate);

    $queryfermstemp='SELECT * FROM fermprot WHERE (fermprot.id="'.$_GET['recid'].'")';

    $row =mysql_fetch_array(mysql_query($queryfermstemp));

}

echo '<table align=center border="1">';

echo '<tr><td>' ;

if ($row['id']!="")

//if ($_GET['modification']=="VIEW" | $_GET['modification']=="UPDATE")

echo '<form method="POST" action="fermprot.php?ccode='.$_SESSION['clientcode'].'&recid='.$row['id'].'&modification=UPDATE">';

else

echo '<form method="POST" action="fermprot.php?ccode='.$_SESSION['clientcode'].'&modification=ADD">';



echo '<table border="1" align="center">';

?>

<td align="center">

LOT</td>

<?php

//echo '<tr><td align="center">LOT</td>';

//echo '<td align=center>VESSEL</td><td align="center">VESSEL<br>TYPE</td><td align="center">VESSEL<br>NUM</td></tr>';

echo '<td align=center>VESSEL</td>';

echo '<td align=center><a href=hardcopy/fermprot.php?fermprotid='.$row['id'].'>PRINT</a></td>';

echo '</tr>';



echo '<td align=left>'.DrawComboForLots($row['LOT'],$_SESSION['vintage'],"LOT").' <a href=lotmgt.php>(LOTS)</a></td>';

$thevessel=$row['VESSELTYPE'].'-'.$row['VESSELID'];

echo '<td align=left>'.DrawComboForTanks($thevessel,$row['LOT'],"asset").'</td>';

echo '<td align=center><a href=viewfermcurves.php?lot='.$row['LOT'].'&allowadd=TRUE&vessel='.$row['VESSELID'].'&vesseltype='.$row['VESSELTYPE'].'>FERMENTATION<br>DATA</a></td>';

if ($row['LOT']!="" & $row['VESSELTYPE']!="" & $row['VESSELID']!="")

{

    //echo '<td align=center><a href=fermprot.php?modification="DUPLICATE"&fermid="'.$row['ID'].'">DUPLICATE</a>';

}

echo '</tr>';

echo '</table>';

if ($row['LOT']!="" & $row['VESSELTYPE']!="" & $row['VESSELID']!="")

{

    echo '<br>' ;

    

    echo '<table border="1" align="center">';

    echo '<tr><td align="center">PUMP<BR>OVER</td><td align="center">START<BR>BRIX</td><td align="center">END<BR>BRIX</td><td align="center">FREQ</td><td align="center">DURATION</td><td align="center">TIMESLOT</td></tr>';

    echo '<tr>';

    if (yesno($row['PO']))

    echo '<td align="center"><input type="checkbox" value="YES" name="POYESNO" CHECKED></td>';

    else

    echo '<td align="center"><input type="checkbox" value="YES" name="POYESNO"></td>';

    

    echo '<td align="center"><input type="text" name="POSTARTBRIX" size="5" value='.$row['POSTARTBRIX'].'></td>';

    echo '<td align="center"><input type="text" name="POENDBRIX" size="5" value='.$row['POENDBRIX'].'></td>';

    echo '<td align="center"><input type="text" name="POFREQ" size="5" value='.$row['POFREQ'].'></td>';

    echo '<td align="center"><input type="text" name="PODURATION" size="5" value='.$row['PODURATION'].'></td>';

    

    echo '<td align="center">'.drawComboFromEnum("fermprot","TIMESLOT1",$row['TIMESLOT1'],"TIMESLOT1").'</td>';

    echo '</tr>';

    echo '<tr>';

    if (yesno($row['PO2']))

    echo '<td align="center"><input type="checkbox" value="YES" name="POYESNO2" CHECKED></td>';

    else

    echo '<td align="center"><input type="checkbox" value="YES" name="POYESNO2"></td>';

    

    echo '<td align="center"><input type="text" name="POSTARTBRIX2" size="5" value='.$row['POSTARTBRIX2'].'></td>';

    echo '<td align="center"><input type="text" name="POENDBRIX2" size="5" value='.$row['POENDBRIX2'].'></td>';

    echo '<td align="center"><input type="text" name="POFREQ2" size="5" value='.$row['POFREQ2'].'></td>';

    echo '<td align="center"><input type="text" name="PODURATION2" size="5" value='.$row['PODURATION2'].'></td>';

    

    echo '<td align="center">'.drawComboFromEnum("fermprot","POTIMESLOT2",$row['POTIMESLOT2'],"POTIMESLOT2").'</td>';

    echo '</tr>';

    echo '</table>';

    

    echo '<br><table border="1" align="center">';

    echo '<tr><td align="center">PUNCH<BR>DOWN</td><td align="center">START<BR>BRIX</td><td align="center">END<BR>BRIX</td><td align="center">FREQ</td><td align="center">STRENGTH</td><td align="center">TIMESLOT</td></tr>';

    echo '<tr>';

    if (yesno($row['PD']))

    echo '<td align="center"><input type="checkbox" value="YES" name="PDYESNO" CHECKED></td>';

    else

    echo '<td align="center"><input type="checkbox" value="YES" name="PDYESNO"></td>';

    echo '<td align="center"><input type="text" name="PDSTARTBRIX" size="5" value='.$row['PDSTARTBRIX'].'></td>';

    echo '<td align="center"><input type="text" name="PDENDBRIX" size="5" value='.$row['PDENDBRIX'].'></td>';

    echo '<td align="center"><input type="text" name="PDFREQ" size="5" value='.$row['PDFREQ'].'></td>';

    //echo '<td align="center"><input type="text" name="PDSTRENGTH" size="7" value='.$row['PDSTRENGTH'].'></td>';

    switch ($row['PDSTRENGTH'])

    {

        case "LIGHT":

        {

            echo '<td align="center"><select size="1" name=PDSTRENGTH><option selected>LIGHT</option><option>MEDIUM</option><option>HEAVY</option></select>';

            break;

        }

        case "MEDIUM":

        {

            echo '<td align="center"><select size="1" name=PDSTRENGTH><option>LIGHT</option><option selected>MEDIUM</option><option>HEAVY</option></select>';

            break;

        }

        case "HEAVY":

        {

            echo '<td align="center"><select size="1" name=PDSTRENGTH><option>LIGHT</option><option>MEDIUM</option><option selected>HEAVY</option></select>';

            break;

        }

        default:

        echo '<td align="center"><select size="1" name=PDSTRENGTH><option selected>LIGHT</option><option>MEDIUM</option><option>HEAVY</option></select>';

        

    }

    //echo '<td align="center"><input type="text" name="TIMESLOT2" size="8" value='.$row['TIMESLOT2'].'></td>';

    echo '<td align="center">'.drawComboFromEnum("fermprot","TIMESLOT2",$row['TIMESLOT2'],"TIMESLOT2").'</td>';

    echo '</tr>';

    //echo '</table>';

    

    //echo '<br><table border="1" align="center">';

    //echo '<tr><td align="center">PUNCH<BR>DOWN (2)</td><td align="center">START<BR>BRIX</td><td align="center">END<BR>BRIX</td><td align="center">FREQ</td><td align="center">STRENGTH</td></tr>';

    echo '<tr>';

    if (yesno($row['PD2']))

    echo '<td align="center"><input type="checkbox" value="YES" name="PDYESNO2" CHECKED></td>';

    else

    echo '<td align="center"><input type="checkbox" value="YES" name="PDYESNO2"></td>';

    echo '<td align="center"><input type="text" name="PDSTARTBRIX2" size="5" value='.$row['PDSTARTBRIX2'].'></td>';

    echo '<td align="center"><input type="text" name="PDENDBRIX2" size="5" value='.$row['PDENDBRIX2'].'></td>';

    echo '<td align="center"><input type="text" name="PDFREQ2" size="5" value='.$row['PDFREQ2'].'></td>';

    //echo '<td align="center"><input type="text" name="PDSTRENGTH2" size="7" value='.$row['PDSTRENGTH2'].'></td>';

    switch ($row['PDSTRENGTH2'])

    {

        case "LIGHT":

        {

            echo '<td align="center"><select size="1" name=PDSTRENGTH2><option selected>LIGHT</option><option>MEDIUM</option><option>HEAVY</option></select>';

            break;

        }

        case "MEDIUM":

        {

            echo '<td align="center"><select size="1" name=PDSTRENGTH2><option>LIGHT</option><option selected>MEDIUM</option><option>HEAVY</option></select>';

            break;

        }

        case "HEAVY":

        {

            echo '<td align="center"><select size="1" name=PDSTRENGTH2><option>LIGHT</option><option>MEDIUM</option><option selected>HEAVY</option></select>';

            break;

        }

        default:

        echo '<td align="center"><select size="1" name=PDSTRENGTH2><option selected>LIGHT</option><option>MEDIUM</option><option>HEAVY</option></select>';

        

    }

    echo '<td align="center">'.drawComboFromEnum("fermprot","TIMESLOT3",$row['TIMESLOT3'],"TIMESLOT3").'</td>';

    echo '</tr></table>';

    echo '<table align=center><tr>';

    echo '<td align=left><br>SPECIAL INSTRUCTIONS:</td></tr><tr>';

    echo '<td align=center>';

    echo '<textarea cols=100 rows=6 name=COMMENT>'.$row['COMMENT'].'</textarea>';

    echo '</td>';

    echo '</tr>';

    echo '</table>';

}

//if ($_GET['modification']=="VIEW" | $_GET['modification']=="UPDATE")

if ($row['id']!="")

echo '<p align="center"><input type="submit" value="UPDATE RECORD" name="B1"><input type="reset" value="Reset" name="B2"></p>';

else

echo '<p align="center"><input type="submit" value="ADD RECORD" name="B1"><input type="reset" value="Reset" name="B2"></p>';

echo '</form>';



if (!($_GET['recid']=="")&($_GET['modification']!="DEL"))

manage_additions($_GET['recid'],$_SESSION['clientcode']);



echo '</td>';

echo '<td align="center">';

echo '<table id=table1 border="1">';

echo '<tr><td>INACTIVE</td><td></td><td>ACTIVE</td></tr>';

$queryferms='SELECT * FROM fermprot

    INNER JOIN `lots` ON (`fermprot`.`LOT` = `lots`.`LOTNUMBER`)

    WHERE ((fermprot.CLIENTCODE="'.$_SESSION['clientcode'].'") AND

           (lots.YEAR = "'.$_SESSION['vintage'].'"))

    ORDER BY

         fermprot.LOT, fermprot.VESSELTYPE, fermprot.VESSELID';

$resultferms = mysql_query($queryferms);

$num_results = mysql_num_rows($resultferms);

for ($i=0; $i <$num_results; $i++)

{

    $row = mysql_fetch_array($resultferms);

    echo '<tr>';

    if ($row['STATUS']=="CLOSED")

    {

        echo '<td>';

        echo '<a href=fermprot.php?ccode='.$_SESSION['clientcode'].'&recid='.$row['id'].'&modification=VIEW

                onmouseover="return overlib(\''.filter(clientcode($row['CLIENTCODE']).' '.$row['DESCRIPTION']).'\',BUBBLE,BUBBLETYPE,\'quotation\');" 

                onmouseout="nd();">'.$row['LOT'].'  '.$row['VESSELTYPE'].'  '.$row['VESSELID'].'</a><br>';

        echo '</td>';

    }

    else

    echo '<td></td>';

    if ($row['STATUS']=="CLOSED")

    {

        echo '<td>';

        echo '<a href=fermprot.php?recid='.$row['id'].'&status=ACTIVE&ccode='.$_SESSION['clientcode'].'>----></a><br>';

        echo '</td>';

    }

    else

    {

        echo '<td>';

        echo '<a href=fermprot.php?recid='.$row['id'].'&status=CLOSED&ccode='.$_SESSION['clientcode'].'><-----</a><br>';

        echo '</td>';

    }

    if ($row['STATUS']=="ACTIVE")

    {

        echo '<td>';

        //        echo '<a href=fermprot.php?ccode='.$_SESSION['clientcode'].'&recid='.$row['id'].'&modification=VIEW>'.$row['LOT'].'  '.$row['VESSELTYPE'].'  '.$row['VESSELID'].'</a><br>';

        echo '<a href=fermprot.php?ccode='.$_SESSION['clientcode'].'&recid='.$row['id'].'&modification=VIEW

                onmouseover="return overlib(\''.filter(clientcode($row['CLIENTCODE']).' '.$row['DESCRIPTION']).'\',BUBBLE,BUBBLETYPE,\'quotation\');" 

                onmouseout="nd();">'.$row['LOT'].'  '.$row['VESSELTYPE'].'  '.$row['VESSELID'].'</a><br>';

        echo '</td>';

    }

    else

    echo '<td></td>';

    echo '<td>';

    echo '<a href=fermprot.php?ccode='.$_SESSION['clientcode'].'&recid='.$row['id'].'&modification=DEL>del</a>';

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

echo '</td>';



echo '</table>';

echo '</table>';

?>



</form>



</body>



</html>