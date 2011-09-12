<?php

session_start();

?>

<html>



<head>

  <title></title>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

  <style type="text/css">@import url(../jscalendar/calendar-win2k-1.css);</style>

  <script type="text/javascript" src="../jscalendar/calendar.js"></script>

  <script type="text/javascript" src="../jscalendar/lang/calendar-en.js"></script>

  <script type="text/javascript" src="../jscalendar/calendar-setup.js"></script>

  <script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>

   <script type="text/javascript" src="popup/overlibmws.js"></script>

   <script type="text/javascript" src="popup/overlibmws_draggable.js"></script>





<script language="JavaScript1.2">

<!-- Browser Check -->

iens6=document.all||document.getElementById

ns4=document.layers



    function navigate(choice)

    {

        var url=choice.options[choice.selectedIndex].value;

        if (url)

        {

            location.href=url;

        }

    }

    function navigatechecked(choice)

    {

        var url=choice.value;

        if (choice.checked)

        url=url+"YES";

        else

        url=url+"NO";

        if (url)

        {

            location.href=url;

        }

    }

  </script>

</head>

<!--PLACE THIS INSIDE THE BODY, BUT NOT WITHIN ANY OTHER TAG IN THE BODY-->

<layer name="nsviewer" bgcolor="#cccccc" width=0 height=0 style="border-width:thin;z-index:3"></layer>

<script language="JavaScript1.2">

if (iens6){

    document.write("<div id='viewer' style='background-color:#cccccc;marginleft:0;visibility:hidden;position:absolute;width:0;height:0;z-index:3;overflow:hidden'></div>")

}

if (ns4){

    hideobj = eval("document.nsviewer")

    hideobj.visibility="hidden"

}

</script>

<script language="JavaScript1.2">

<!-- Browser Check -->

iens6=document.all||document.getElementById

ns4=document.layers



<!--DEFINE CONTENT-->

<!--PLACE ALL YOU BOX TITLES HERE - Just add another element to the array -->



var titleArray = new Array

titleArray[1]="<b>TANK OR TBIN</b>"

titleArray[2]="<b>ASSET</b>";



<!--PLACE ALL YOU BOX CONTENT HERE - Make sure you use a += after the first line -->

var linkArray = new Array

<?php

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");

include ("totalgallons.php");

if ($_GET['action']=="setassettypeid")

{

    $_SESSION['assettypeid']=$_GET['assettypeid'];

    $_SESSION['assetid']="";

}



$query='SELECT * from assettypes WHERE assettypes.ID=6 or assettypes.ID=6 or assettypes.ID=20 ORDER BY assettypes.NAME ';

$result=mysql_query($query);

print('linkArray[1]="" ;');

for ($i=0; $i<mysql_num_rows($result);$i++)

{

    $row=mysql_fetch_array($result);

    $tdesc=$row['NAME'];

    $link=$_SERVER['PHP_SELF'].'?action=setassettypeid&assettypeid='.$row['ID'];

    $fullline='linkArray[1]+="<a href=\''.$link.'\'>'.$tdesc.'</a><br> " ;

    ';

    print($fullline);

}



if ($_SESSION['assettypeid']!=6)

{

    $query='SELECT * from assets WHERE TYPEID="'.$_SESSION['assettypeid'].'" ORDER BY assets.NAME';

    $result=mysql_query($query);

    print('linkArray[2]=""; ');

    for ($i=0; $i<mysql_num_rows($result);$i++)

    {

        $row=mysql_fetch_array($result);

        $tdesc=$row['NAME'];

        $link=$_SERVER['PHP_SELF'].'?action=setassetid&assetid='.$row['ID'];

        $fullline='linkArray[2]+="<a href=\''.$link.'\'>'.$tdesc.'</a><br> "; ';

        print($fullline);

    }

}

?>

<!--END DEFINE CONTENT-->



<!--GLOBAL VARIABLES-->

var thename

var theobj

var thetext

var winHeight

var winWidth

var boxPosition

var headerColor

var tableColor

var timerID

var seconds=0

var x=0

var y=0

var offsetx = 2

var offsety = 2

<!--END GLOBAL VARIABLES-->



if(ns4) {

    document.captureEvents(Event.MOUSEMOVE)

}

document.onmousemove=getXY



<!--GLOBAL FUNCTIONS-->

function buildText(value,tcolor,bcolor) {

    // CHANGE EACH ARRAY ELEMENT BELOW TO YOUR OWN CONTENT. MAKE SURE TO USE SINGLE QUOTES INSIDE DOUBLE QUOTES.

    text="<table width='"+(winWidth-4)+"' height='"+(winHeight-4)+"' border='1' bgcolor='"+tableColor+"' cellspacing='1' cellpadding=1>"

    text+="<tr><td width='"+(winWidth-4)+"' height='20' align='center' valign='top' bgcolor='"+headerColor+"'>"

    text+="<font face='Arial,Helvetica' color='"+tcolor+"' SIZE='-1'>"+titleArray[value]+"</font>"

    text+="</td></tr>"

    text+="<tr><td width='"+winWidth+"' align='left' valign='top'>"

    text+="<font face='Arial,Helvetica' color='"+bcolor+"' SIZE='-2'>"+linkArray[value]+"</font>"

    text+="</td></tr></table>"

    return text

}



function setObj(textelement,inwidth,inheight,boxpos,titlecolor,boxcolor,tfontcolor,bfontcolor) {

    clearTimeout(timerID)

    boxPosition=boxpos

    tableColor=boxcolor

    headerColor=titlecolor

    winWidth=inwidth

    winHeight=inheight

    thetext=buildText(textelement,tfontcolor,bfontcolor)

    if (boxPosition == "bottomR") { // Right

    x=x+offsetx

    y=y+offsety

    }

    if (boxPosition == "bottomC") { // Right

    x=x-(winWidth/2)

    y=y+offsety

    }

    if (boxPosition == "bottomL") { // Left

    x=x-(offsetx+2)-winWidth

    y=y-offsety

    }

    if (boxPosition == "topR") { // Top

    x=x+offsetx

    y=y+offsety-winHeight

    }

    if (boxPosition == "topL") { // Top

    x=x-(offsetx+2)-winWidth

    y=y+offsety-winHeight

    }

    if(iens6){

        thename = "viewer"

        theobj=document.getElementById? document.getElementById(thename):document.all.thename

        theobj.style.width=winWidth

        theobj.style.height=winHeight

        theobj.style.left=x

        if(iens6&&document.all) {

            theobj.style.top=document.body.scrollTop+y

            theobj.innerHTML = ""

            theobj.insertAdjacentHTML("BeforeEnd","<table cellspacing=0 width="+winWidth+" height="+winHeight+" border=0><tr><td width=100% valign=top><font type='times' size='2' style='color:black;font-weight:normal'>"+thetext+"</font></td></tr></table>")

        }

        if(iens6&&!document.all) {

            theobj.style.top=window.pageYOffset+y

            theobj.innerHTML = ""

            theobj.innerHTML="<table cellspacing=0 width="+winWidth+" height="+winHeight+" border=0><tr><td width=100% valign=top><font type='times' size='2' style='color:black;font-weight:normal'>"+thetext+"</font></td></tr></table>"

        }

    }

    if(ns4){

        thename = "nsviewer"

        theobj = eval("document."+thename)

        theobj.left=x

        theobj.top=y

        theobj.width=winWidth

        theobj.clip.width=winWidth

        theobj.height=winHeight

        theobj.clip.height=winHeight

        theobj.document.write("<table cellspacing=0 width="+winWidth+" height="+winHeight+" border=0><tr><td width=100% valign=top><font type='times' size='2' style='color:black;font-weight:normal'>"+thetext+"</font></td></tr></table>")

        theobj.document.close()

    }

    viewIt()

}



function viewIt() {

    if(iens6) {

        theobj.style.visibility="visible"

    }

    if(ns4) {

        theobj.visibility = "visible"

    }

}



function stopIt() {

    if(theobj) {

        if(iens6) {

            theobj.innerHTML = ""

            theobj.style.visibility="hidden"

        }

        if(ns4) {

            theobj.document.write("")

            theobj.document.close()

            theobj.visibility="hidden"

        }

    }

}



function timer(sec) {

    seconds=parseInt(sec)

    if(seconds>0) {

        seconds--

        timerID=setTimeout("timer(seconds)",1000)

    }else{

        stopIt()

    }

}



function getXY(e) {

    if (ns4) {

        x=0

        y=0

        x=e.pageX;

        y=e.pageY;

    }

    if (iens6&&document.all) {

        x=0

        y=0

        x=event.x;

        y=event.y;

    }

    if (iens6&&!document.all) {

        x=0

        y=0

        x=e.pageX;

        y=e.pageY;

    }

}

<!--END GLOBAL FUNCTIONS-->

</script>



    <script language="JavaScript" type="text/javascript">

    function navigate(choice)

    {

        var url=choice.options[choice.selectedIndex].value;

        if (url)

        {

            location.href=url;

        }

    }

    function navigatechecked(choice)

    {

        var url=choice.value;

        if (choice.checked)

        url=url+"YES";

        else

        url=url+"NO";

        if (url)

        {

            location.href=url;

        }

    }

    function navigate(choice)

    {

        var url=choice.value;

        location.href=url;

    }

    function navigatetextbox(choice)

    {

        var url="wopage.php?chosendate="+choice.value;

        location.href=url;

    }

  </script>

<!--END BODY CODE-->

<body onClick="stopIt()">

 <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div> 

<body>

<!--PLACE THIS INSIDE THE BODY, BUT NOT WITHIN ANY OTHER TAG IN THE BODY-->

<layer name="nsviewer" bgcolor="#cccccc" width=0 height=0 style="border-width:thin;z-index:3"></layer>

<script language="JavaScript1.2">

if (iens6){

    document.write("<div id='viewer' style='background-color:#cccccc;marginleft:0;visibility:hidden;position:absolute;width:0;height:0;z-index:3;overflow:hidden'></div>")

}

if (ns4){

    hideobj = eval("document.nsviewer")

    hideobj.visibility="hidden"

}

</script>

<!--END BODY CODE-->



<?php

function datetimestr($date,$slot,$startperiod)

{

    $dt=date("Y-m-d",strtotime($date));

    switch ($slot)

    {

        case "MORNING":

        {

            if ($startperiod=="START")

            {

                $dt=$dt.' 06:00:00';

            }

            else

            {

                $dt=$dt.' 12:00:00';

            }

            break;

        }

        case "NOON":

        {

            if ($startperiod=="START")

            {

                $dt=$dt.' 12:00:00';

            }

            else

            {

                $dt=$dt.' 16:00:00';

            }

            break;

        }

        case "EVENING":

        {

            if ($startperiod=="START")

            {

                $dt=$dt.' 16:00:00';

            }

            else

            {

                $dt=$dt.' 21:00:00';

            }

            break;

        }

    }

    return $dt;

}



function showassets($assettype, $morning, $noon, $evening, $lot)

{

    $assets=listallocassets($assettype,$_SESSION['dateofwork'],clientid($_SESSION['clientcode']),$_SESSION['woid']);

    $params='?assettype='.$assettype.'&morning='.$morning.'&noon='.$noon.'&evening='.$evening;

    echo '<table border="1" align="center" width="100%">';

    if (($_SESSION['dateofwork']!="")& (($_SESSION['morning']=="YES") | ($_SESSION['noon']=="YES") | ($_SESSION['evening']== "YES")))

    {

        if (count($assets)==0)

        echo '<tr><td width="50%" align="center">NONE ASSIGNED';

        else

        {

            echo '<tr><td width="50%" align="center">'.$assettype.'</td><td>';

            for ($i=0;$i<count($assets);$i++)

            {

                echo $assets[$i]['timeslot'].' - <a href=viewfermcurves.php?allowadd=FALSE&lot='.$lot.'&vesseltype='.$assettype.'&vessel='.$assets[$i]['name'].'>'.$assets[$i]['name'].'</a><br>';

            }

        }

    }

    else

    echo '<tr><td align="center">SPECIFY DATE OF WO';

    echo '</td></tr></table>';



}

if ($_GET['action']=="setassetid")

{

    $_SESSION['assetid']=$_GET['assetid'];

//  doview();

}



if ($_SESSION['vintage']=='')

{

    $_SESSION['vintage']='2009';

}

if ($_GET['action']=="setclient")

$_SESSION['clientcode']=$_GET['clientcode'];

if ($_GET['action']=="clearclient")

$_SESSION['clientcode']="";

if ($_GET['lot'])

$_SESSION['lot']=$_GET['lot'];

if ($_GET['action']=="newscp")

{

        $query='INSERT INTO wo SET

        wo.TYPE="SCP",

        wo.DUEDATE="'.datetimestr($_GET['dateofwork'],"MORNING",'START').'",

        wo.ENDDATE="'.datetimestr($_GET['dateofwork'],"EVENING",'END').'",

        wo.AUTOGENERATED="NO",

        wo.CREATIONDATE="'.date("Y-m-d").'",

        wo.CLIENTCODE="'.$_SESSION['clientcode'].'"';

 //  echo $query;

  $result=mysql_query($query);

    

    $_SESSION['woid']=mysql_insert_id();



}



if ($_GET['woid'])

$_SESSION['woid']=$_GET['woid'];

if ($_GET['action']=="delres")

{

    $query='DELETE FROM reservation WHERE ID='.$_GET['resid'];

    mysql_query($query);

}


if ($_GET['action']=="mod")

{
	// echo '<pre>';
	// print_r($_POST);
	// exit;

    $query='UPDATE wo SET ' .

      'wo.DUEDATE="'.date("Y-m-d",strtotime($_POST['dateofwork'])).'",'.

      'wo.ENDDATE="'.date("Y-m-d",(strtotime($_POST['dateofwork']))).'",'.

      'wo.LOT="'.$_POST['LOT'].'" WHERE wo.ID="'.$_SESSION['woid'].'"';

 //   echo $query;

    $result=mysql_query($query);

    

    $query='UPDATE scp SET '.

    'scp.HANDSORTING="'.$_POST['handsorting'].'",'.

    'scp.SPECIALINSTRUCTIONS="'.strtoupper($_POST['specialinstructions']).'",'.

    'scp.ESTDAYSINTANK="'.$_POST['estdaysintank'].'",'.

    'scp.WHOLECLUSTER="'.$_POST['wholecluster'].'",'.

    'scp.VARIETAL="'.strtoupper($_POST['varietal']).'",'.

    'scp.VINEYARD="'.strtoupper($_POST['vineyard']).'",'.

    'scp.DELIVERYDATE="'.date("Y-m-d",strtotime($_POST['deliverydate'])).'",'.

    'scp.APPELLATION="'.strtoupper($_POST['appellation']).'",'.

    'scp.CLONE="'.strtoupper($_POST['clone']).'",'.

    'scp.VINEYARDID="'.strtoupper($_POST['vineyardid']).'",'.

    'scp.ZONE="'.strtoupper($_POST['zone']).'",'.

    'scp.TANKPOSITION="'.$_POST['tankposition'].'",'.

    'scp.ACTUALTONS="'.$_POST['actualtons'].'",'.

    'scp.CRUSHING="'.$_POST['crushing'].'",'.

    'scp.ESTTONS="'.$_POST['esttons'].'" WHERE scp.WOID="'.$_SESSION['woid'].'"';

 //       echo $query;

    $result=mysql_query($query);

}

if ($_GET['action']=="del")

{

    $query='DELETE FROM labresults WHERE labresults.ID="'.$_GET['labresultid'].'"';

    $result=mysql_query($query);

}

if ($_GET['action']=='addasset')

{

    if ($_GET['assetid']!="")

    {

        $query='INSERT into reservation SET ASSETID="'.$_GET['assetid'].'", WOID="'.$_SESSION['woid'].'"';

        mysql_query($query);

    }

    $_SESSION['assetid']="";

    $_SESSION['assettypeid']="";

    

}

$wo=getwo($_SESSION['woid']);



//$query='SELECT *, locations.APPELLATION AS APL FROM scp left outer join wo on (scp.WOID=wo.ID) left outer join locations on (locations.ID=scp.VINEYARDID) WHERE scp.WOID="'.$_SESSION['woid'].'"';
$query='SELECT *,scp.ID as SCPID, locations.APPELLATION AS APL FROM scp left outer join locations on (locations.ID=scp.VINEYARDID) WHERE scp.WOID="'.$_SESSION['woid'].'"';
//echo $query;
//$query='select * from scp WHERE scp.WOID="'.$_SESSION['woid'].'"';
$result=mysql_query($query);

if (mysql_num_rows($result)==0)

{

    $insertquery='INSERT INTO scp SET scp.WOID="'.$_SESSION['woid'].'"';

    $result=mysql_query($insertquery);

    $result=mysql_query($query);

}

$row=mysql_fetch_array($result);
//$_SESSION['clientcode']=$row['CLIENTCODE'];

$scpid=$row['SCPID'];



//$lotquery='select * from lots WHERE (lots.CLIENTCODE="'.clientid($_SESSION['clientcode']).'" AND lots.YEAR="'.$_SESSION['vintage'].'") order by LOTNUMBER';

$lotquery='select * from lots WHERE (lots.CLIENTCODE="'.clientid($wo['clientcode']).'" AND lots.YEAR="'.$_SESSION['vintage'].'") order by LOTNUMBER';

$lotresults=mysql_query($lotquery);



$thelist='<table>';

for ($i=0;$i<mysql_num_rows($lotresults);$i++)

{

   $lotrow=mysql_fetch_array($lotresults);

//   $thelist.='<tr><td align=left>'.$lotrow['LOTNUMBER'].' '.preg_replace("/'/","\\'",preg_replace("/[\n\t\r]+/","",$lotrow['DESCRIPTION'])).'</td></tr>';

   $thelist.='<tr><td align=left>'.preg_replace("/'/","",preg_replace("/[\n\t\r]+/","",$lotrow['LOTNUMBER'])).' '.preg_replace("/'/","",preg_replace("/[\n\t\r]+/","",$lotrow['DESCRIPTION'])).'</td></tr>';

}

$thelist.='</table>';



//echo $thelist;

//echo '--'.$_SERVER['PHP_SELF'];


echo '<table align=center width=50% border="1">';

echo '<tr><td colspan=4 align=center><b>STEMMING & CRUSHING PROTOCOL WORKSHEET<br>(SCP)</b><br><br></td></tr>';

echo '<tr><td align=center><a href=hardcopy/scp.php?woid='.$_SESSION['woid'].'>PRINT</a></td><td colspan=2></td><td align=center><a href=scpcal.php>SCP CALENDAR</a></td></tr>';

echo '<tr>';

echo '<td align="center">';

echo '<form method="POST" action=scppage.php?woid='.$_SESSION['woid'].'&action=mod>';

echo 'DATE: <input type="text" name="dateofwork" size="12" value="'.date("m/d/Y",strtotime($wo['duedate'])).'">';

//echo '<button id="trigger">...</button>';

//echo 'EXPECTED DELIVERY DATE: '.date("m/d/Y",strtotime($wo['duedate']));

echo '</td>';



echo '<td align=center>'.DrawComboForLots($wo['lot'],$_SESSION['vintage'],"LOT",$wo['clientcode']);

echo '<a  href="lotmgt.php" onmouseover="return overlib(\''.$thelist.'\',

                CAPTION,\'<c>LOTS</c>\',

                WIDTH, 300, STICKY, DRAGGABLE);"

                onmouseout="nd();">(LOTS)</a>';

echo '</td>';

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



//echo 'LOT: <a href=showlotinfo.php?lot='.$wo['lot'].'>'.$wo['lot'].'</a>';

echo '</td>';

echo '<td align="center">';

echo 'SCP #:'.$row['ID'].'<br>';

echo '</td>';

echo '<td>';

echo 'WO: '.'<a href=wopage.php?action=view&woid='.$_SESSION['woid'].'>'.$_SESSION['woid'].'</a>';

echo '</td>';

echo '</table>';

echo '<table align=center width=50%>';

echo '<tr><td width=50% align=center>';

echo '<table width=100% align=center>';

//echo '<tr><td align=right>EXPECTED DELIVERY DATE: </td>';

//echo '<td align=left><input type="text" name="deliverydate" size="18" value="'.date("m/d/Y",strtotime($row['DELIVERYDATE'])).'">';

//echo '<button id="trigger">...</button>';

//echo '</td></tr>';

echo '<tr><td align=right>ESTIMATED TONS:</td><td align=left><input type=textbox value="'.$row['ESTTONS'].'" size=3 name="esttons"></td></tr>';

echo '<tr><td align=right>ESTIMATED TIME IN TANK:</td><td align=left><input type=textbox value="'.$row['ESTDAYSINTANK'].'" size=3 name="estdaysintank"> DAYS</td></tr>';

//echo '<tr><td align=right>VINEYARD:</td><td align=left><input type=textbox size=30 name=vineyard value="'.$row['VINEYARD'].'"></td></tr>';

echo '<tr><td align=right>VINEYARD:</td><td align=left>'.DrawComboForVineyard("locations","NAME",$row['VINEYARDID'],"vineyardid","LOCATIONTYPE","VINEYARD","CLIENTID",$_SESSION['clientid']).'<br><a href=vineyards.php?action=show>Modify Vineyard List</a></td></tr>';
echo '<tr><td align=right>APPELLATION:</td><td align=left>'.$row['APL'].'</td></tr>';
echo '<tr><td align=right>CROP REPORT ZONE:</td><td align=left>'.$row['REGION'].'</td></tr>';

//echo '<tr><td align=right>VARIETY:</td><td align=left><input type=textbox size=30 name=varietal value="'.$row['VARIETAL'].'"></td></tr>';

echo '<tr><td align=right>VARIETY:</td><td align=left>'.DrawComboFromData("varietals","NAME",$row['VARIETAL'],"varietal").'</tr>';

//echo '<tr><td align=right>APPELLATION:</td><td align=left>'.DrawComboFromData("appellations","NAME",$row['APPELLATION'],"appellation").'</tr>';

echo '<tr><td align=right>CLONE:</td><td align=left><input type=textbox name=clone value="'.$row['CLONE'].'"></td></tr>';

//echo '<tr><td align=right>CROP REPORT ZONE:</td><td align=left><input size=5 type=textbox name=zone value='.$row['ZONE'].'></td></tr>';

//echo '<tr><td align=right>CROP REPORT ZONE:</td><td align=left>'.DrawComboFromEnum("scp","zone",$row['ZONE'],"zone").'</tr>';

echo '<tr><td><br></tr>';

echo '</table></td>';

echo '<td align=center>';

if (isstaff()=="YES")

echo '<a href=wtpage.php?action=newwt&scpid='.$scpid.'>CREATE WEIGHT TAG</a><br><br>';

echo '<a href=scpcal.php?action=del&woid='.$_SESSION['woid'].'>DELETE</a><br><br>';

echo '</table>';

if ($row['ESTTONS']>0)

{

echo '<table border=1 align="center" width=50%>';

echo '<tr><td colspan=2 align=center>TANK ASSIGNMENTS<br></tr>';

echo '<tr><td colspan=2 align=center>';

if ($_SESSION['woid']!="")

{

    echo '<table border=1 id=res align=center width=100%>';

    

    $assetquery='SELECT `wo`.`ID`,`wo`.`DUEDATE`, `assets`.`NAME`, `wo`.`CLIENTCODE`, assets.OWNER, reservation.ID AS RESID, assettypes.ID AS ASSETTYPEID, assets.ID AS ASSETSID

FROM  `assettypes`

  INNER JOIN `assets` ON (`assettypes`.`ID` = `assets`.`TYPEID`)

  INNER JOIN `reservation` ON (`assets`.`ID` = `reservation`.`ASSETID`)

  INNER JOIN `wo` ON (`reservation`.`WOID` = `wo`.`ID`)

WHERE

  (wo.ID ="'.$_SESSION['woid'].'")';

   // echo $assetquery;

    $assetresult=mysql_query($assetquery);

    for ($i=0;$i<mysql_num_rows($assetresult);$i++)

    {

        $assetrow=mysql_fetch_array($assetresult);

        echo '<tr>';

        echo '<td align=center width=20%>';

        echo '<a href='.$_SERVER['PHP_SELF'].'?action=delres&resid='.$assetrow['RESID'].'>DEL</a>';

        echo '</td>';

        echo '<td align=center width=80%>';

        //  echo '<a href=assetsched.php?assettype='.$row['ASSETTYPEID'].'&assetid='.$row['ASSETSID'].'>'.$row['NAME'].' '.$row['CAPACITY'].'</a>';

        echo $assetrow['NAME'].' ['.$assetrow['OWNER'].']';

        echo '</td><td width=20%></td>';

        echo '</tr>';

    }

    

    $queryassettype='SELECT * FROM assettypes WHERE ID="'.$_SESSION['assettypeid'].'"';

    $resultassettype=mysql_query($queryassettype);

    $rowassettype=mysql_fetch_array($resultassettype);

    

    $queryasset='SELECT * from assets WHERE ID="'.$_SESSION['assetid'].'" ORDER BY assets.NAME';

    $resultasset=mysql_query($queryasset);

    $rowasset=mysql_fetch_array($resultasset);

    

    echo '<tr>';

    echo '<td width=20%></td><td width=80% align=center>';

    echo '<table align=center width=100%><tr>';

    echo '<td align=center><a href=tankscheduling.php?returnpage='.$_SERVER['PHP_SELF'].'&thedate='.strtotime($wo['duedate']).'&assettypeid=6>SELECT TANK</a></td>';

    echo '<td align=center><a href=tankscheduling.php?returnpage='.$_SERVER['PHP_SELF'].'&thedate='.strtotime($wo['duedate']).'&assettypeid=8>SELECT TBIN</a></td>';

	echo '<td align=center><a href=tankscheduling.php?returnpage='.$_SERVER['PHP_SELF'].'&thedate='.strtotime($wo['duedate']).'&assettypeid=14>SELECT PORTA TANK</a></td></tr>';

    echo '</table>';

}

/*    echo '<a href="javascript:void(0);" onmouseover="setObj(1,100,70,\'bottomC\',\'#ffffff\',\'#ffffff\',\'Darkred\',\'black\')" onmouseout="timer(20)">';

    if ($_SESSION['assettypeid']=="")

    echo 'SELECT ASSET TYPE</a>';

    else

    {

        echo $rowassettype['NAME'].'</a> --> ';

        //if ($_SESSION['assettypeid']!=6)

        //echo '<a href="javascript:void(0);" onmouseover="setObj(2,100,100,\'bottomC\',\'#ffffff\',\'#ffffff\',\'Darkred\',\'black\')" onmouseout="timer(20)">';

        //else

        echo '<a href=tankscheduling.php?returnpage='.$_SERVER['PHP_SELF'].'&thedate='.strtotime($_SESSION['dateofwork']).'&assettypeid='.$_SESSION['assettypeid'].'>';

        if ($_SESSION['assetid']=="")

        echo 'SELECT ASSET</a>';

        else

        {

            echo $rowasset['NAME'].'</a>';

        }

        

    }

    echo '</td>';

*/

//    echo '<td width=20% align=center><a href='.$_SERVER['PHP_SELF'].'?action=addasset>ADD ('.$_SESSION['assetid'].')</a></td>';

    echo '</tr>';

    echo '</table>';

}

/*  $querytanks='SELECT `wo`.`ID`,`wo`.`DUEDATE`, `assets`.`NAME`, `wo`.`CLIENTCODE`, reservation.ID AS RESID, assettypes.ID AS ASSETTYPEID, assets.ID AS ASSETSID

        FROM  `assettypes`  INNER JOIN `assets` ON (`assettypes`.`ID` = `assets`.`TYPEID`)

                            INNER JOIN `reservation` ON (`assets`.`ID` = `reservation`.`ASSETID`)

                            INNER JOIN `wo` ON (`reservation`.`WOID` = `wo`.`ID`)

            WHERE  (wo.ID ="'.$_SESSION['woid'].'")';

    //echo $query;

    $resulttanks=mysql_query($querytanks);

    for ($i=0;$i<mysql_num_rows($resulttanks);$i++)

    {

        $rowtanks=mysql_fetch_array($resulttanks);

        echo '<tr>';

        echo '<td colspan=2 align=center>';

        echo $rowtanks['NAME'];

        echo '</tr>';

    }

*/  

    echo '</table>';

    echo '<br><br>';

echo '<table border=1 align="center" width=50%>';

$whites=array ('SAUVIGNON BLANC' => 1, 'CHARDONNAY' => 1, 'VIOGNIER'=>1,'RIESLING'=>1,'ROUSSANNE'=>1);

if (isset($whites[$row['VARIETAL']]))

		echo '<tr><td align=center>HAND SORTING: DIRECTTOPRESS<input type=hidden name=handsorting value=DIRECTTOPRESS></td>';

	else

		echo '<tr><td align=center>HAND SORTING: '.DrawComboFromEnum("scp","HANDSORTING", $row['HANDSORTING'],"handsorting").'</td>';

//echo '<tr><td align=center>HAND SORTING: '.DrawComboFromEnum("scp","HANDSORTING", $row['HANDSORTING'],"handsorting").'</td>';

echo '<td align="center">SPECIAL INSTRUCTIONS:<br><textarea name="specialinstructions" cols=40>'.$row['SPECIALINSTRUCTIONS'].'</textarea></td>';

echo '<tr><td align=center>% WHOLECLUSTER: <input type=textbox value="'.$row['WHOLECLUSTER'].'" size=3 name="wholecluster"></td>';

echo '<td align=center>TANK POSITION: '. DrawComboFromEnum("scp","TANKPOSITION", $row['TANKPOSITION'],"tankposition").'</td>';

echo '<tr><td align=center>CRUSHING: '. DrawComboFromEnum("scp","CRUSHING", $row['CRUSHING'],"crushing").'</td>';

echo '<td align=center>ACTUAL TONS: <input type=textbox value="'.number_format($row['ACTUALTONS'],2).'" size=3 name="actualtons"></td>';

echo '<tr><td align=center colspan=2><input type=submit value="UPDATE"></td></tr>';

echo '</table>';

echo '<table>';

echo '<tr><td>';



    echo '</table>';

echo '</form>';

?>

<script type="text/javascript">

Calendar.setup(

{

    inputField  : "dateofwork",  // ID of the input field

    ifFormat    : "%m/%d/%Y",    // the date format

    button      : "trigger"      // ID of the button

}
</script>

</body>





</html>
