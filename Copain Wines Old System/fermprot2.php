<?php

session_start();

include ("startdb.php");

include ("yesno.php");

include ("setcheck.php");

include ("defaultvalue.php");

include ("manageadditions.php");

include ("queryupdatefunctions.php");



echo '<html>';

echo '<head>';

echo '<script language="JavaScript">';

$query='select LOT from fermprot where id="'.$_GET['recid'].'"';

$result=mysql_query($query);

$row=mysql_fetch_array($result);

echo 'function newWindow() {;

    mywindow=open(\'wopage.php\',\'wopage\',\'resizable=yes,width=600,height=600\');

    mywindow.location.href = \'wopage.php?action=new&lot='.$row['LOT'].'&fermprotid='.$_GET['recid'].'\';

    if (mywindow.opener == null) mywindow.opener = self;}';



echo '</script>';

?>



<title>Fermentation Protocol</title>

<link rel="stylesheet" type="text/css" href="../site.css">

    <script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>

      <script type="text/javascript" src="popup/overlibmws.js"></script>

   <script type="text/javascript" src="popup/overlibmws_bubble.js"></script>



<?php





$query='SELECT * from lots inner join clients on (lots.CLIENTCODE=clients.CLIENTID) where ((lots.YEAR="'.$_SESSION['vintage'].'") AND

     (clients.CODE="'.$_SESSION['clientcode'].'"))';

$result=mysql_query($query);

?>

</head>



<body>



<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div> 

<?php





function displaywodata($woid)

{

    $wo=getwo($woid);

    



            echo '<table width=100%>';

            echo '<tr>';

    switch ($wo['type'])

    {

        case "PUMP OVER" :

        {

            echo '<td width=30% align=center>';

            echo $wo['type'];

            echo '</td>';

            echo '<td align=center>';

            echo $wo['timeslot'].' FOR '.$wo['duration'];

            echo '</td>';           

            break;

        }

        case "PUNCH DOWN" :

        {

            echo '<td width=30% align=center>';

            echo $wo['type'];

            echo '</td>';

            echo '<td align=center>';

            echo $wo['timeslot'].' - '. $wo['strength'];

            echo '</td>';           

            break;

        }

        case "LAB TEST" :

        {

            echo '<td width=30% align=center>';

            echo $wo['type'];

            echo '</td>';

            echo '<td align=center>';

            $labquery='select labresults.LABTEST from labtest inner join labresults on (labtest.ID=labresults.LABTESTID) where labtest.WOID="'.$wo['id'].'"';

            $labresults=mysql_query($labquery);

            for ($i=0;$i<mysql_num_rows($labresults);$i++)

            {

               $row=mysql_fetch_array($labresults);

               echo $row['LABTEST'];

               if ($i!=mysql_num_rows($labresults)-1)

                 echo '<br>';

            }

            echo '</td>';

            break;

        }

        default :

        {

            echo '<td width=30% align=center>';

            echo $wo['type'];

            echo '</td>';

            echo '<td align=center>';

            echo $wo['otherdesc'];

            echo '</td>';

            break;

        }

    }

                echo '</tr>';

            echo '</table>';



}



function filter($thestring)

{

    return preg_replace("/'/","",preg_replace("/[\n\t\r]+/","",$thestring));

}



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

    $updateferm='DELETE from program WHERE ID="'.$_GET['programid'].'"';

    mysql_query($updateferm);

    $queryfermstemp='SELECT * FROM fermprot WHERE (fermprot.id="'.$_GET['recid'].'")';

    $row =mysql_fetch_array(mysql_query($queryfermstemp));

}



if ($_GET['modification']=="modline")

{

    $query='update program SET DAY="'.$_POST['day'].'",

      HIGHBRIX="'.$_POST['highbrix'].'",

      REPEATING="'.$_POST['repeating'].'",

      DAYCOUNT="'.$_POST['daycount'].'",

      FREQUENCY="'.$_POST['frequency'].'",

      LOWBRIX="'.$_POST['lowbrix'].'"  WHERE ID="'.$_POST['programid'].'"';

 echo $query;

    mysql_query($query);

    $queryfermstemp='SELECT * FROM fermprot WHERE (fermprot.id="'.$_GET['recid'].'")';

    $row =mysql_fetch_array(mysql_query($queryfermstemp));

}



if ($_GET['modification']=="VIEW")

{

    $queryfermstemp='SELECT * FROM fermprot WHERE (fermprot.id="'.$_GET['recid'].'")';

    $row =mysql_fetch_array(mysql_query($queryfermstemp));

}





if ($_GET['modification']=="ADD")

{

    if ($_POST['repeating']=="YES")

    $repeat="YES";

    else

    $repeat="NO";

    

    $query='insert into program set DAY="'.$_POST['day'].'",

      LOWBRIX="'.$_POST['lowbrix'].'", 

      HIGHBRIX="'.$_POST['highbrix'].'", 

      DAYCOUNT="'.$_POST['daycount'].'",

      FREQUENCY="'.$_POST['frequency'].'", 

      WOTEMPLATEID="'.$_POST['newwoid'].'",

      REPEATING="'.$repeat.'", 

      FERMPROTID="'.$_POST['fermprotid'].'"';

    mysql_query($query);

    $queryfermstemp='SELECT * FROM fermprot WHERE (fermprot.id="'.$_GET['recid'].'")';

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

echo '<table align=center width=100% border="1">';

echo '<tr><td width=75%>' ;



echo '<table border="1" align="center">';

echo '<td align="center">LOT</td>';

echo '<td align=center>VESSEL</td>';

if ($row['LOT']!="" & $row['VESSELTYPE']!="" & $row['VESSELID']!="")

echo '<td align=center><a href=hardcopy/fermprot.php?fermprotid='.$row['id'].'>PRINT</a></td>';

echo '</tr>';



echo '<td align=left>'.DrawComboForLots($row['LOT'],$_SESSION['vintage'],"LOT").' <a href=lotmgt.php>(LOTS)</a></td>';

$thevessel=$row['VESSELTYPE'].'-'.$row['VESSELID'];

echo '<td align=left>'.DrawComboForTanks($thevessel,$row['LOT'],"asset").'</td>';

if ($row['LOT']!="" | $row['VESSELTYPE']!="" | $row['VESSELID']!="")

{

    echo '<td align=center><a href=viewfermcurves.php?lot='.$row['LOT'].'&allowadd=TRUE&vessel='.$row['VESSELID'].'&vesseltype='.$row['VESSELTYPE'].'>FERMENTATION<br>DATA</a></td>';

    //echo '<td align=center><a href=fermprot.php?modification="DUPLICATE"&fermid="'.$row['ID'].'">DUPLICATE</a>';

}

echo '</tr>';

echo '</table>';

if ($row['LOT']!="" | $row['VESSELTYPE']!="" | $row['VESSELID']!="")

{

    echo '<table border=1 width=100% align=center>';

    $query='select REPEATING, LOWBRIX, HIGHBRIX, DAYCOUNT, FREQUENCY, DAY, program.ID, WOTEMPLATEID

        from fermprot inner join program on (program.FERMPROTID = fermprot.id) where fermprot.id="'.$_GET['recid'].'" order by DAY, HIGHBRIX DESC, program.ID';

    //echo $query;

    $result=mysql_query($query);

    echo '<tr><b><td align=center width=50>REPEAT</td>';

    echo '<td align=center width=50>DAY</td>';

    echo '<td align=center width=50>DAY<br>COUNT</td>';

    echo '<td align=center width=50>HIGH<br>BRIX</td>';

    echo '<td align=center width=50>LOW<br>BRIX</td>';

    echo '<td align=center width=50>FREQUENCY</td>';

    echo '<td align=center align=center width=100>WORK ORDER<br>TEMPLATE</td>';

    echo '<td align=center>DESCRIPTION</td>';

    echo '<td width=50></td>';

    echo '</b></tr>';

    for ($i=0;$i<mysql_num_rows($result);$i++)

    {

        $therow=mysql_fetch_array($result);

        echo '<form method=POST action='.$PHP_SELF.'?ccode='.$_SESSION['clientcode'].'&recid='.$_GET['recid'].'&modification=modline>';

        echo '<input type=hidden value='.$therow['ID'].' name=programid>';

        echo '<tr>';

        echo '<td width=50 align=center>';

        if ($therow['REPEATING']=="YES")

             echo '<input checked type=checkbox name=repeating value=YES>';

        else

             echo '<input type=checkbox name=repeating value=YES>';

        echo '</td>';

        echo '<td align=center>';

        echo '<input size=5 type=text value='.$therow['DAY'].' name=day>';

        echo '</td>';

        echo '<td align=center>';

        echo '<input size=5 type=text value='.$therow['DAYCOUNT'].' name=daycount>';

        echo '</td>';

        echo '<td align=center>';

        echo '<input size=5 type=text value='.$therow['HIGHBRIX'].' name=highbrix>';

        echo '</td>';

        echo '<td align=center>';

        echo '<input size=5 type=text value='.$therow['LOWBRIX'].' name=lowbrix>';

        echo '</td>';

        echo '<td align=center>';

        echo DrawComboFromEnum("program","FREQUENCY",$therow['FREQUENCY'],"frequency");

//      echo '<input size=5 type=text value='.$therow['FREQUENCY'].' name=frequency>';

        echo '</td>';

        echo '<td align=center>';

        if ($therow['WOTEMPLATEID']!=0)

        echo '<a href=wopage.php?action=view&woid='.$therow['WOTEMPLATEID'].'>'.$therow['WOTEMPLATEID'].'</a>';

        else 

        echo '';

        echo '</td>';

        echo '<td align=center>';

        displaywodata($therow['WOTEMPLATEID']);

        echo '</td>';

        echo '<td align=center>';

        echo '<input type=submit value=MOD></td><td align=center><a href='.$PHP_SELF.'?modification=DEL&recid='.$_GET['recid'].'&programid='.$therow['ID'].'>DEL</a>';

        echo '</td>';

        echo '</tr>';

        echo '</form>';

    }

    echo '<form name=addprogram method="POST" action="'.$PHP_SELF.'?ccode='.$_SESSION['clientcode'].'&recid='.$_GET['recid'].'&modification=ADD">';

    echo '<tr>';

    echo '<td align=center>';

    //echo '<input type=text name=repeating size=5>';

    echo '<input type=checkbox name=repeating value=YES>';

    echo '</td>';

    echo '<td align=center>';

    echo '<input type=text name=day size=5>';

    echo '</td>';

    echo '<td align=center>';

    echo '<input type=text name=daycount size=5>';

    echo '</td>';

    echo '<td align=center>';

    echo '<input type=text name=highbrix size=5>';

    echo '</td>';

    echo '<td align=center>';

    echo '<input type=text name=lowbrix size=5>';

    echo '</td>';

    echo '<td align=center>';

    echo DrawComboFromEnum("program","FREQUENCY","1","frequency");

    echo '</td>';

    echo '<td align=center>';

    echo '<input type=hidden name=fermprotid value="'.$row['id'].'">';

        echo '<a href=javascript:newWindow()>NEW WO</a>';

        echo '<input type=text size=4 name=newwoid>';

    echo '</td>';

    echo '<td></td>';

    echo '<td align=center>';

    echo '<input type="submit" value="ADD" name="B1">';

    echo '</td>';

    echo '</table>';

    echo '</form>';

}

echo '</td><td align=center width=50%>';

echo '<table id=table1 border="1">';

echo '<tr><td>INACTIVE</td><td></td><td>ACTIVE</td></tr>';

$queryferms='SELECT * FROM fermprot

    INNER JOIN `lots` ON (`fermprot`.`LOT` = `lots`.`LOTNUMBER`)

    WHERE ((fermprot.CLIENTCODE="'.$_SESSION['clientcode'].'") AND

           (lots.YEAR = "'.$_SESSION['vintage'].'"))

    ORDER BY

         fermprot.LOT';

$resultferms = mysql_query($queryferms);

$num_results = mysql_num_rows($resultferms);

for ($i=0; $i <$num_results; $i++)

{

    $row = mysql_fetch_array($resultferms);

    echo '<tr>';

    if ($row['STATUS']=="CLOSED")

    {

        echo '<td>';

        echo '<a href='.$PHP_SELF.'?ccode='.$_SESSION['clientcode'].'&recid='.$row['id'].'&modification=VIEW

                onmouseover="return overlib(\''.filter(clientcode($row['CLIENTCODE']).' '.$row['DESCRIPTION']).'\',BUBBLE,BUBBLETYPE,\'quotation\');" 

                onmouseout="nd();">'.$row['LOT'].'  '.$row['VESSELTYPE'].'  '.$row['VESSELID'].'</a><br>';

        echo '</td>';

    }

    else

    echo '<td></td>';

    if ($row['STATUS']=="CLOSED")

    {

        echo '<td>';

        echo '<a href='.$PHP_SELF.'?recid='.$row['id'].'&status=ACTIVE&ccode='.$_SESSION['clientcode'].'>----></a><br>';

        echo '</td>';

    }

    else

    {

        echo '<td>';

        echo '<a href='.$PHP_SELF.'?recid='.$row['id'].'&status=CLOSED&ccode='.$_SESSION['clientcode'].'><-----</a><br>';

        echo '</td>';

    }

    if ($row['STATUS']=="ACTIVE")

    {

        echo '<td>';

        //        echo '<a href='.$PHP_SELF.'?ccode='.$_SESSION['clientcode'].'&recid='.$row['id'].'&modification=VIEW>'.$row['LOT'].'  '.$row['VESSELTYPE'].'  '.$row['VESSELID'].'</a><br>';

        echo '<a href='.$PHP_SELF.'?ccode='.$_SESSION['clientcode'].'&recid='.$row['id'].'&modification=VIEW

                onmouseover="return overlib(\''.filter(clientcode($row['CLIENTCODE']).' '.$row['DESCRIPTION']).'\',BUBBLE,BUBBLETYPE,\'quotation\');" 

                onmouseout="nd();">'.$row['LOT'].'  '.$row['VESSELTYPE'].'  '.$row['VESSELID'].'</a><br>';

        echo '</td>';

    }

    else

    echo '<td></td>';

    echo '<td>';

    echo '<a href='.$PHP_SELF.'?ccode='.$_SESSION['clientcode'].'&recid='.$row['id'].'&modification=DEL>del</a>';

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