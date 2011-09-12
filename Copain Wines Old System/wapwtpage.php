<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

</head>



<body onLoad="document.addbinform.bincount.focus()">



<?php



include ("startdb.php");

include ("queryupdatefunctions.php");

include ("lotinforecords.php");



if ($_GET['wtid']!='')

{

    $_SESSION['wtid']=$_GET['wtid'];

}



if ($_GET['action']=='addbin')

{

    $query='INSERT into bindetail SET BINCOUNT="'.$_POST['bincount'].'", '.

         'WEIGHT="'.$_POST['weight'].'", '.

         'TARE="'.$_POST['tare'].'", '.

         'MISC="'.$_POST['misc'].'", '.

         'WEIGHTAG='.$_POST['recid'];

         mysql_query($query);

}

if ($_GET['action']=='modbin')

{

    $query='UPDATE bindetail SET BINCOUNT="'.$_POST['bincount'].'", '.

         'WEIGHT="'.$_POST['weight'].'", '.

         'TARE="'.$_POST['tare'].'", '.

         'MISC="'.$_POST['misc'].'" '.

         ' WHERE bindetail.ID="'.$_POST['binid'].'"';

         mysql_query($query);

}

if ($_GET['action']=='delbin')

{

    $query='DELETE from bindetail WHERE bindetail.ID="'.$_GET['binid'].'"';

         mysql_query($query);

}

if ($_GET['action']=='newwt')

{

    if ($_GET['scpid']>0)

    {

        $query='SELECT * from scp where ID="'.$_GET['scpid'].'"';

        $result=mysql_query($query);

        $scprow=mysql_fetch_array($result);



        

    }

    $query='SELECT DISTINCT MAX(`wt`.`TAGID`) AS `NEWTAGID` from wt';

    $result=mysql_query($query);

    $row=mysql_fetch_array($result);

    $newtagid=$row['NEWTAGID']+1;

    $query='INSERT INTO wt '.

       'SET wt.DATETIME="'.date("Y-m-d H:i",time()).'", '.

       'wt.CLIENTCODE="'.clientid($_SESSION['clientcode']).'", '.

       'wt.LOT="'.$scprow['PROPOSEDLOT'].'", '.

       'wt.VARIETY="'.$scprow['VARIETAL'].'", '.

       'wt.APPELLATION="'.$scprow['APPELLATION'].'", '.

       'wt.VINEYARD="'.$scprow['VINEYARD'].'", '.

       'wt.CLONE="'.$scprow['CLONE'].'", '.

       'wt.REGIONCODE="'.$scprow['ZONE'].'", '.

       'wt.TAGID="'.$newtagid.'"';

    $result=mysql_query($query);

    $_SESSION['wtid']=5000+$newtagid;

}



if ($_GET['action']=='updatewt')

{

    $query='SELECT * from lots where lots.LOTNUMBER="'.strtoupper($_POST['thelot']).'"';

    $result=mysql_query($query);

    if (mysql_num_rows($result)==0)

    {

        $query='INSERT into lots SET lots.LOTNUMBER="'.strtoupper($_POST['thelot']).'",'.

          'lots.YEAR="'.date("Y",strtotime($_POST['thedatetime'])).'",'.

          'lots.DESCRIPTION="'.strtoupper($_POST['appellation']).' '.strtoupper($_POST['vineyard']).' '.strtoupper($_POST['variety']).'",'.

          'lots.CLIENTCODE="'.clientid($_POST['winery']).'"';

        mysql_query($query);

    }

    $query='UPDATE wt SET wt.APPELLATION="'.strtoupper($_POST['appellation']).'",

                wt.DATETIME="'.date("Y-m-d H:i",strtotime($_POST['thedatetime'])).'",

                wt.TAGID="'.($_POST['tagid']-5000).'",

                wt.VARIETY="'.strtoupper($_POST['variety']).'",

                wt.CLONE="'.strtoupper($_POST['clone']).'",

                wt.REGIONCODE="'.$_POST['zone'].'",

                wt.LOT="'.strtoupper($_POST['thelot']).'",

                wt.VINEYARD="'.strtoupper($_POST['vineyard']).'",

                wt.TRUCKLICENSE="'.strtoupper($_POST['trucklicense']).'",

                wt.TRAILERLICENSE="'.strtoupper($_POST['trailerlicense']).'",

                wt.CLIENTCODE="'.strtoupper(clientid($_POST['winery'])).'"

                WHERE wt.TAGID="'.($_SESSION['wtid']-5000).'"'; 

    $_SESSION['wtid']=$_POST['tagid'];

    mysql_query($query);

}



$query='SELECT wt.ID, unix_timestamp(wt.DATETIME) AS THEDATETIME, wt.VINEYARD, wt.LOT, wt.TAGID, wt.CLONE, wt.VARIETY, wt.APPELLATION, wt.TRUCKLICENSE, wt.TRAILERLICENSE, wt.REGIONCODE, wt.CLIENTCODE, wt.LOT FROM wt WHERE wt.TAGID="'.($_SESSION['wtid']-5000).'"';

$result=mysql_query($query);

$row=mysql_fetch_array($result);

$binsum='SELECT SUM(bindetail.BINCOUNT) AS SUMBINCOUNT, SUM(bindetail.WEIGHT) AS SUMWEIGHT, SUM(bindetail.TARE) AS SUMTARE FROM

           bindetail WHERE bindetail.WEIGHTAG="'.$row['ID'].'"';

$binresult=mysql_query($binsum);

$binrow=mysql_fetch_array($binresult);



    echo '<form method=post action='.$PHP_SELF.'?action=updatewt>';

    echo 'WT: '.($row['TAGID']+5000);

    echo ' '.date("m/d/Y h:i A",$row['THEDATETIME']);

    echo '<br>CC: '.DrawComboFromData('clients','CODE',clientcode($row['CLIENTCODE']),'winery');

    echo '<br>LOT: '.DrawComboForLots($row['LOT'],$_SESSION['vintage'],"thelot");

    echo '<br>VYD: <input type=textbox size=6 name=vineyard value="'.$row['VINEYARD'].'">';

    echo '<br>VAR: <input type=textbox size=6 name=variety value="'.$row['VARIETY'].'">';

    echo '<br>APP: <input type=textbox size=6 name=appellation value="'.$row['APPELLATION'].'">';

    echo '<br>CLN: <input type=textbox size=6 name=clone value="'.$row['CLONE'].'">';

    echo '<br>ZONE: <input type=textbox size=6 name=zone value='.$row['REGIONCODE'].'>';

    echo '<br>LIC1: <input type=textbox size=6 name=trucklicense value="'.$row['TRUCKLICENSE'].'">';

    echo '<br>LIC2: <input type=textbox size=6 name=trailerlicense value="'.$row['TRAILERLICENSE'].'">';

    echo '<input type=submit name=b1 value=UPDATE>';

    echo '<br>GROSS: '.$binrow['SUMWEIGHT'];

    echo '<br>TARE: '.$binrow['SUMTARE'];

    echo '<br>NET: '.($binrow['SUMWEIGHT']-$binrow['SUMTARE']);

    echo '<br>TONS: '.number_format(($binrow['SUMWEIGHT']-$binrow['SUMTARE'])/2000,3);

    echo '<br>BINCOUNT: '.$binrow['SUMBINCOUNT'];

    echo '<br>';



$recid=$row['ID'];

$query='SELECT * FROM bindetail WHERE bindetail.WEIGHTAG="'.$row['ID'].'"';

$result=mysql_query($query);

echo '<b>BIN DETAIL</b>';

echo '<table>';

echo '<tr valign=bottom>';

if (isstaff()=="YES")

echo '<td align=center ></td>';

echo '<td align=center >BIN<br>COUNT<hr></td>';

echo '<td align=center >WEIGHT<hr></td>';

echo '<td align=center >TARE<hr></td>';

echo '<td align=center>MISC<hr></td>';

echo '</tr>';

if (isstaff()=="YES")

{

    

    for ($i=0;$i<mysql_num_rows($result);$i++)

    {

        $bin=mysql_fetch_array($result);

        echo '<tr>';

        echo '<form method=post action='.$PHP_SELF.'?action=modbin>';

        echo '<td align=center><a href='.$PHP_SELF.'?action=delbin&binid='.$bin['ID'].')">d</a></td>';

        echo '<td align=center><input type=textbox size=1 name=bincount value='.$bin['BINCOUNT'].'></td>';

        echo '<td align=center><input type=textbox size=2 name=weight value='.$bin['WEIGHT'].'></td>';

        echo '<td align=center><input type=textbox size=1 name=tare value='.$bin['TARE'].'></td>';

        echo '<td align=center><input type=textbox size=1 name=misc value="'.$bin['MISC'].'"></td>';

        echo '<input type=hidden name=binid value='.$bin['ID'].'>';

        echo '<td align=center><input type=submit name=b1 value=M></td></form>';

        echo '</tr>';

    }

    echo '<tr>';

    echo '<form name=addbinform method=post action='.$PHP_SELF.'?action=addbin>';

    echo '<td></td>';

    echo '<td align=center><input type=textbox size=1 name=bincount></td>';

    echo '<td align=center><input type=textbox size=2 name=weight></td>';

    echo '<td align=center><input type=textbox size=1 name=tare></td>';

    echo '<td align=center><input type=textbox size=1 name=misc></td>';

    echo '<input type=hidden name=recid value='.$recid.'>';

    echo '<td align=center><input type=submit name=b1 value=A></td></form>';

    echo '</tr>';

}

else

{

    for ($i=0;$i<mysql_num_rows($result);$i++)

    {

        $bin=mysql_fetch_array($result);

        echo '<tr>';

        echo '<td align=center>'.$bin['BINCOUNT'].'</td>';

        echo '<td align=center>'.$bin['WEIGHT'].'</td>';

        echo '<td align=center>'.$bin['TARE'].'</td>';

        echo '<td align=center>'.$bin['MISC'].'</td>';

        echo '</tr>';

    }

}

echo '</table>';

?>



</body>



</html>