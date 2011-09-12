<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

  <style type="text/css">@import url(../jscalendar/calendar-win2k-1.css);</style>

  <script type="text/javascript" src="../jscalendar/calendar.js"></script>

  <script type="text/javascript" src="../jscalendar/lang/calendar-en.js"></script>

  <script type="text/javascript" src="../jscalendar/calendar-setup.js"></script>

      <script language="JavaScript" type="text/javascript">

      function confirmdelete(url)

      {

        if (confirm('Are you SURE you want to delete this weight!!??'))

        {

            location.href=url;

        }

      }

  </script>

</head>



<body onLoad="document.addbinform.weight.focus()">



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

         'MISC="'.strtoupper($_POST['misc']).'" '.

         ' WHERE bindetail.ID="'.$_POST['binid'].'"';

         mysql_query($query);

}

if ($_GET['action']=='delbin')

{

    $query='DELETE from bindetail WHERE bindetail.ID="'.$_GET['binid'].'"';

         mysql_query($query);

}

function regioncode($value)

{

  switch ($value)

  {

     case "1 - MENDOCINO" : return 1;

     case "2 - LAKE" : return 2;

     case "3 - SONOMA" : return 3;

     case "4 - NAPA" : return 4;

     case "7 - MONTEREY" : return 7;

  }

}



$region[1]="MENDOCINO COUNTY";

$region[2]="LAKE COUNTY";

$region[3]="SONOMA COUNTY";

$region[4]="NAPA COUNTY";

$region[7]="MONTEREY COUNTY";



if ($_GET['action']=='newwt')

{

    if ($_GET['scpid']>0)

    {

        $query='SELECT * from scp left outer join wo on (wo.ID=scp.WOID) where scp.ID="'.$_GET['scpid'].'"';

//		echo $query;
		
        $result=mysql_query($query);

        $scprow=mysql_fetch_assoc($result);

//    	$_SESSION['clientcode']=$scprow['CLIENTCODE'];

//        echo $scprow['CLIENTCODE'].'<br>';
//		echo clientid($scprow['CLIENTCODE']);
//		exit;
        

    }

    $query='SELECT DISTINCT MAX(`wt`.`TAGID`) AS `NEWTAGID` from wt';

    $result=mysql_query($query);

    $row=mysql_fetch_array($result);

//    echo $_SESSION['clientcode'];

//    echo '<pre>';

//    print_r($scprow);

//    exit;

    $newtagid=$row['NEWTAGID']+1;

    $query='INSERT INTO wt '.

       'SET wt.DATETIME="'.date("Y-m-d H:i",time()).'", '.

       'wt.CLIENTCODE="'.clientid($scprow['CLIENTCODE']).'", '.

       'wt.LOT="'.$scprow['LOT'].'", '.

       'wt.VARIETY="'.$scprow['VARIETAL'].'", '.

       'wt.APPELLATION="'.$scprow['APPELLATION'].'", '.

       'wt.VINEYARD="'.$scprow['VINEYARD'].'", '.

       'wt.CLONE="'.$scprow['CLONE'].'", '.

     'wt.VINEYARDID="'.$scprow['VINEYARDID'].'", '.

       'wt.REGIONCODE="'.regioncode($scprow['ZONE']).'", '.

       'wt.TAGID="'.$newtagid.'"';

    $result=mysql_query($query);

	$_SESSION['CLIENTCODE']=$scprow['CLIENTCODE'];

//	echo $_SESSION['CLIENTCODE'];
	
    $_SESSION['wtid']=5000+$newtagid;
	// echo $query;
	// exit;

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

	echo '<pre>';
    $query='UPDATE wt SET wt.DATETIME="'.date("Y-m-d H:i",strtotime($_POST['thedatetime'])).'",

                wt.VARIETY="'.strtoupper($_POST['variety']).'",

                wt.LOT="'.strtoupper($_POST['thelot']).'",

                wt.VINEYARDID="'.strtoupper($_POST['vineyardid']).'",

                wt.CLONE="'.strtoupper($_POST['clone']).'",

                wt.TRUCKLICENSE="'.strtoupper($_POST['trucklicense']).'",

                wt.TRAILERLICENSE="'.strtoupper($_POST['trailerlicense']).'",

                wt.CLIENTCODE="'.strtoupper(clientid($_POST['winery'])).'"

                WHERE wt.TAGID="'.($_SESSION['wtid']-5000).'"'; 

    $_SESSION['wtid']=$_POST['tagid'];

    mysql_query($query);

}

function DrawComboForVineyard($table,$field,$value,$name,$index,$limitname="",$limitvalue="",$limitname2="",$limitvalue2="")
{
	$query='select NAME,ID from locations where LOCATIONTYPE="VINEYARD" AND CLIENTID="'.clientid($_SESSION['clientcode']).'" order by NAME';
     $result=mysql_query($query) or die (mysql_error());
    
    for ($i=0;$i<mysql_num_rows($result);$i++)
    {
        $row=mysql_fetch_assoc($result);
        if ($row['ID']==$value)
	        $items.='<option selected value="'.$row['ID'].'">'.ucfirst($row[$field]).'</option>\n';
        else
	        $items.="<option value=\"".$row['ID']."\">".ucfirst($row[$field])."</option>\n";
    }
    $text='<select name="'.$name.'">\n';
    $text.=$items;
    $text.="</select>\n\n";
    return $text;
}



$query='SELECT wt.ID, unix_timestamp(wt.DATETIME) AS THEDATETIME, wt.CLONE, locations.ID as VINEYARDID, locations.NAME, wt.LOT, wt.TAGID, wt.CLONE, locations.ORGANIC, wt.VARIETY, locations.APPELLATION AS APL, wt.TRUCKLICENSE, wt.TRAILERLICENSE, locations.REGION, wt.CLIENTCODE, wt.LOT FROM wt 
  left outer join locations on (locations.ID=wt.VINEYARDID) WHERE wt.TAGID="'.($_SESSION['wtid']-5000).'"';

$result=mysql_query($query);

$row=mysql_fetch_array($result);
$_SESSION['clientcode']=clientcode($row['CLIENTCODE']);

$binsum='SELECT SUM(bindetail.BINCOUNT) AS SUMBINCOUNT, SUM(bindetail.WEIGHT) AS SUMWEIGHT, SUM(bindetail.TARE) AS SUMTARE FROM

           bindetail WHERE bindetail.WEIGHTAG="'.$row['ID'].'"';

$binresult=mysql_query($binsum);

$binrow=mysql_fetch_array($binresult);



if (isstaff()=="YES")

{

    echo '<table border=1 width=700 align=center>';

    echo '<td align=center width=60%>';

    echo '<table width=100% align=center>';

    echo '<form method=post action='.$PHP_SELF.'?action=updatewt>';

    echo '<tr><td align=right><a href=wtpage.php?wtid='.($row['TAGID']+5000).'>WEIGH TAG:</a></td><td align=left><b><input type=hidden name=tagid value='.($row['TAGID']+5000).'>'.($row['TAGID']+5000).'</b></td></tr>';

    echo '<tr><td align=right>DATE/TIME:</td><td align=left><input type=textbox name=thedatetime value="'.date("m/d/Y h:i A",$row['THEDATETIME']).'"><button id=trigger>...</button></td></tr>';

    echo '<tr><td align=right>WINERY:</td><td align=left>'.DrawComboFromData('clients','CODE',clientcode($row['CLIENTCODE']),'winery').'</td></tr>';

    echo '<tr><td align=right><a href=showlotinfo.php?lot='.$row['LOT'].'>LOT:</a></td><td align=left>'.DrawComboForLots($row['LOT'],$_SESSION['vintage'],"thelot",clientcode($row['CIENTCODE'])).' <a href=lotmgt.php>(LOTS)</a></td></tr>';

	echo '<tr><td align=right>VINEYARD:</td><td align=left>'.DrawComboForVineyard("locations","NAME",$row['VINEYARDID'],"vineyardid","LOCATIONTYPE","VINEYARD","CLIENTID",$_SESSION['clientid']).'</td></tr>';
	echo '<tr><td align=right>ORGANIC:</td><td align=left>'.strtoupper($row['ORGANIC']).'</td></tr>';
	echo '<tr><td align=right>APPELLATION:</td><td align=left>'.strtoupper($row['APL']).'</td></tr>';
	echo '<tr><td align=right>CROP REPORT ZONE:</td><td align=left>'.$row['REGION'].'</td></tr>';

    echo '<tr><td align=right>VARIETY:</td><td align=left>'.DrawComboFromData("varietals","NAME",$row['VARIETY'],"variety").'</tr>';

    echo '<tr><td align=right>CLONE:</td><td align=left><input type=textbox name=clone value="'.$row['CLONE'].'"></td></tr>';

    echo '<tr><td align=right>TRUCK LICENSE:</td><td align=left><input type=textbox name=trucklicense value="'.$row['TRUCKLICENSE'].'"></td></tr>';

    echo '<tr><td align=right>TRAILER LICENSE:</td><td align=left><input type=textbox name=trailerlicense value="'.$row['TRAILERLICENSE'].'"></td></tr>';

    echo '<tr><td colspan=2 align=center><input type=submit name=b1 value=UPDATE></td></form>';

    echo '</table>';

    echo '</td>';

    echo '<td valign=center align=center>';

    echo '<table width=100%>';

    echo '<tr><td width=33% align=right>GROSS:</td><td style="border-style:solid; border-width:1" align=right width=33$><b>'.$binrow['SUMWEIGHT'].'</b></td></tr>';

    echo '<tr><td width=33% align=right>TARE:</td><td align=right width=33$><b>'.$binrow['SUMTARE'].'</b</td><td align=center>TONS</td></tr>';

    echo '<tr><td width=33% align=right>NET:</td><td align=right width=33$><b>'.($binrow['SUMWEIGHT']-$binrow['SUMTARE']).'</b</td><td align=center><b><big>'.number_format(($binrow['SUMWEIGHT']-$binrow['SUMTARE'])/2000,2).'</big></b</td></tr>';

    echo '<tr></tr><tr></tr><tr></tr><tr></tr>';

    echo '<tr><td colspan=3 width=33% align=center>BIN COUNT:<b><big>'.$binrow['SUMBINCOUNT'].'</big></b></td></tr>';

    echo '<tr></tr><tr></tr>';

    echo '<tr><td colspan=3 align=center><a href=hardcopy/wt.php?wt='.($row['TAGID']+5000).'>PDF</a><br><br>';

 //   if (isstaff()=="YES") echo '<a href=filemgt.php?linktype=WT&linkid='.$_SESSION['wtid'].'>ATTACHED FILES</a><br><br>';

    

    $docquery='select * from files where (TYPEID="WT" and THEID="'.(5000+$row['TAGID']).'")';

 //   echo $docquery;

    $docresult=mysql_query($docquery);

    if (mysql_num_rows($docresult)>0)

    {

        $docrow=mysql_fetch_array($docresult);

        echo '<a href='.$docrow['LOCATION'].'>DIGITALLY SIGNED WEIGHT TAG</a></td></tr>';

    }

    

    echo '</table>';

    echo '</td>';

    echo '</table>';

    echo '<br>';

    ?>

<script type="text/javascript">

Calendar.setup(

{

    inputField  : "thedatetime",  // ID of the input field

    ifFormat    : "%m/%d/%Y",    // the date format

    button      : "trigger"      // ID of the button

}

);



 </script>

<?php

}

else

{

    echo '<table border=1 width=500 align=center>';

    echo '<td align=center width=60%>';

    echo '<table width=100% align=center>';

    echo '<tr><td align=right>WEIGH TAG:</td><td align=left><b><big>'.($row['TAGID']+5000).'</big></b></td></tr>';

    echo '<tr><td align=right>DATE/TIME:</td><td align=left>'.date("m/d/Y h:i A",$row['THEDATETIME']).'</td></tr>';

    echo '<tr><td align=right>WINERY:</td><td align=left>'.clientcode($row['CLIENTCODE']).'</td></tr>';

    echo '<tr><td align=right>VARIETY:</td><td align=left>'.$row['VARIETY'].'</td></tr>';

    echo '<tr><td align=right>VINEYARD:</td><td align=left>'.strtoupper($row['NAME']).'</td></tr>';

    echo '<tr><td align=right>APPELLATION:</td><td align=left>'.strtoupper($row['APL']).'</td></tr>';

    echo '<tr><td align=right>ORGANIC:</td><td align=left>'.strtoupper($row['ORGANIC']).'</td></tr>';

    echo '<tr><td align=right>CLONE:</td><td align=left>'.$row['CLONE'].'</td></tr>';

    echo '<tr><td align=right>CROP REPORT ZONE:</td><td align=left>'.$row['REGION'].'</td></tr>';

    echo '<tr><td align=right>TRUCK LICENSE:</td><td align=left>'.$row['TRUCKLICENSE'].'</td></tr>';

    echo '<tr><td align=right>TRAILER LICENSE:</td><td align=left>'.$row['TRAILERLICENSE'].'</td></tr>';

    echo '</table>';

    echo '</td>';

    echo '<td valign=center align=center>';

    echo '<table width=100%>';

    echo '<tr><td width=33% align=right>GROSS:</td><td style="border-style:solid; border-width:1" align=right width=33$><b>'.$binrow['SUMWEIGHT'].'</b></td></tr>';

    echo '<tr><td width=33% align=right>TARE:</td><td align=right width=33$><b>'.$binrow['SUMTARE'].'</b</td><td align=center>TONS</td></tr>';

    echo '<tr><td width=33% align=right>NET:</td><td align=right width=33$><b>'.($binrow['SUMWEIGHT']-$binrow['SUMTARE']).'</b</td><td align=center><b><big>'.number_format(($binrow['SUMWEIGHT']-$binrow['SUMTARE'])/2000,2).'</big></b</td></tr>';

    echo '<tr></tr><tr></tr><tr></tr><tr></tr>';

    echo '<tr><td colspan=3 width=33% align=center>BIN COUNT:<b><big>'.$binrow['SUMBINCOUNT'].'</big></b></td></tr>';

    echo '<tr></tr><tr></tr>';

    echo '<tr><td colspan=3 align=center><a href=hardcopy/wt.php?wt='.($row['TAGID']+5000).'>PDF</a><br><br>';

    $docquery='select * from files where (TYPEID="WT" and THEID="'.(5000+$row['TAGID']).'")';

 //   echo $docquery;

    $docresult=mysql_query($docquery);

    if (mysql_num_rows($docresult)>0)

    {

        $docrow=mysql_fetch_array($docresult);

        echo '<a href='.$docrow['LOCATION'].'>DIGITALLY SIGNED WEIGHT TAG</a></td></tr>';

    }

    echo '</table>';

    echo '</td>';

    echo '</table>';

    echo '<br>';

}

$recid=$row['ID'];

$query='SELECT * FROM bindetail WHERE bindetail.WEIGHTAG="'.$row['ID'].'"';

$result=mysql_query($query);

echo '<center><b>BIN DETAIL</b></center>';



echo '<table width=200 align=center>';

echo '<tr valign=bottom>';

if (isstaff()=="YES")

echo '<td align=center width=10%></td>';

echo '<td align=center width=10%>WEIGHT<hr></td>';

echo '<td align=center width=10%>TARE<hr></td>';

echo '<td align=center width=10%>BIN<br>COUNT<hr></td>';

echo '<td align=center>MISCELLANEOUS<hr></td>';

echo '</tr>';

if (isstaff()=="YES")

{

    

    for ($i=0;$i<mysql_num_rows($result);$i++)

    {

        $bin=mysql_fetch_array($result);

        echo '<tr>';

        echo '<form method=post action='.$PHP_SELF.'?action=modbin>';

        echo '<td align=center><input type=submit name=b1 value=MOD></td>';

        echo '<td align=center><input type=textbox size=7 name=weight value='.$bin['WEIGHT'].'></td>';

        echo '<td align=center><input type=textbox size=7 name=tare value='.$bin['TARE'].'></td>';

        echo '<td align=center><input type=textbox size=5 name=bincount value='.$bin['BINCOUNT'].'></td>';

        echo '<td align=center><input type=textbox size=20 name=misc value="'.$bin['MISC'].'"></td>';

        echo '<td align=center><a href="javascript:confirmdelete(\''.$PHP_SELF.'?action=delbin&binid='.$bin['ID'].'\')">del</a></td>';

        echo '<input type=hidden name=binid value='.$bin['ID'].'></form>';

        echo '</tr>';

    }

    echo '<tr>';

    echo '<form name=addbinform method=post action='.$PHP_SELF.'?action=addbin>';

 //   echo '<td></td>';

    echo '<td align=center><input type=submit name=b1 value=ADD></td>';

    echo '<td align=center><input type=textbox size=7 name=weight></td>';

    echo '<td align=center><input type=textbox size=7 value=192 name=tare></td>';

    echo '<td align=center><input type=textbox size=5 value=2 name=bincount></td>';

    echo '<td align=center><input type=textbox size=20 name=misc></td>';

    echo '<input type=hidden name=recid value='.$recid.'></form>';

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