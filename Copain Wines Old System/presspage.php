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

titleArray[1]="<b>PRESS/TANKS</b>"

titleArray[2]="<b>ASSET</b>";



<!--PLACE ALL YOU BOX CONTENT HERE - Make sure you use a += after the first line -->

var linkArray = new Array

<?php

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("lotinforecords.php");

include ("assetfunctions.php");

include ("totalgallons.php");

if ($_GET['action']=="setassettypeid")

{

    $_SESSION['assettypeid']=$_GET['assettypeid'];

    $_SESSION['assetid']="";

}



$query='SELECT * from assettypes WHERE assettypes.ID=2 OR assettypes.ID=6 ORDER BY assettypes.NAME ';

$result=mysql_query($query);

print('linkArray[1]="" ;');

for ($i=0; $i<mysql_num_rows($result);$i++)

{

    $row=mysql_fetch_array($result);

    $tdesc=$row['NAME'];

    $link=$PHP_SELF.'?action=setassettypeid&assettypeid='.$row['ID'];

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

        $link=$PHP_SELF.'?action=setassetid&assetid='.$row['ID'];

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



function printvessels($vessels)

{

	for ($i=0;$i<count($vessels);$i++)

	{

		if ($i==0) $thevessels.=$vessels[$i];

		else $thevessels.='<br>'.$vessels[$i];

	}

	return $thevessels;

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

    $_SESSION['vintage']='2007';

}

if ($_GET['action']=="setclient")

$_SESSION['clientcode']=$_GET['clientcode'];

if ($_GET['action']=="clearclient")

$_SESSION['clientcode']="";

if ($_GET['lot'])

$_SESSION['lot']=$_GET['lot'];

if ($_GET['action']=="newpressoff")

{

        $query='INSERT INTO wo SET

        wo.TYPE="PRESSOFF",

        wo.DUEDATE="'.datetimestr($_GET['dateofwork'],"MORNING",'START').'",

        wo.ENDDATE="'.datetimestr($_GET['dateofwork'],"EVENING",'END').'",

        wo.STATUS="ASSIGNED",

        wo.WORKPERFORMEDBY="CCC",

        wo.AUTOGENERATED="NO",

        wo.STARTSLOT="'.$_GET['startslot'].'",

        wo.CREATIONDATE="'.date("Y-m-d").'",

        wo.CLIENTCODE="'.$_SESSION['clientcode'].'"';

 //  echo $query;

  $result=mysql_query($query);

    

    $_SESSION['woid']=mysql_insert_id();

    if ($_GET['assetid']!="")

    {

        $query='INSERT into reservation SET ASSETID="'.$_GET['assetid'].'", WOID="'.$_SESSION['woid'].'"';

        mysql_query($query);

    }

    $_SESSION['assetid']="";

    $_SESSION['assettypeid']="";



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

    $query='UPDATE wo SET ' .

      'wo.DUEDATE="'.date("Y-m-d",strtotime($_POST['dateofwork'])).'",'.

      'wo.ENDDATE="'.date("Y-m-d",strtotime($_POST['dateofwork'])).'",'.

      'wo.STARTSLOT="'.$_POST['startslot'].'",'.

      'wo.LOT="'.$_POST['LOT'].'" WHERE wo.ID="'.$_SESSION['woid'].'"';

//    echo $query;

    $result=mysql_query($query);

    

    $query='UPDATE pressprogram SET '.

    'pressprogram.PROGRAM="'.strtoupper($_POST['program']).'", '.

    'pressprogram.PRESSCUT="'.strtoupper($_POST['presscut']).'", '.

    'pressprogram.DESCRIPTION="'.strtoupper($_POST['description']).'", '.

    'pressprogram.PRESSDURATION="'.strtoupper($_POST['pressduration']).'", '.

    'pressprogram.FILLLEVEL="'.strtoupper($_POST['filllevel']).'", '.

    'pressprogram.PRESSTYPE="'.$_POST['presstype'].'" WHERE pressprogram.WOID="'.$_SESSION['woid'].'"';

   //   echo $query;

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

$query='SELECT * FROM pressprogram left outer join wo on (wo.ID=pressprogram.WOID) WHERE pressprogram.WOID="'.$_SESSION['woid'].'"';

$result=mysql_query($query);

if (mysql_num_rows($result)==0)

{

    $insertquery='INSERT INTO pressprogram SET pressprogram.WOID="'.$_SESSION['woid'].'"';

    $result=mysql_query($insertquery);

    $result=mysql_query($query);

}

$row=mysql_fetch_array($result);

$pressprogramid=$row['ID'];

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

echo '<table align=center width=50% border="1">';

echo '<tr><td colspan=3 align=center><b>PRESS SHEET</b>      <a href=presscal.php?action=del&woid='.$_SESSION['woid'].'>DELETE</a><br><br></td></tr>';

echo '<tr><td align=center><a href=hardcopy/pressprogram.php?woid='.$_SESSION['woid'].'>PRINT</a></td><td></td><td align=center><a href=presscal.php>PRESS CALENDAR</a></td></tr>';

echo '<tr>';

echo '<td align="center">';

echo '<form method="POST" action=presspage.php?woid='.$_SESSION['woid'].'&action=mod>';

echo 'DATE: <input type="text" name="dateofwork" size="12" value="'.date("m/d/Y",strtotime($wo['duedate'])).'">';

//echo '<button id="trigger">...</button>';

//echo 'EXPECTED DELIVERY DATE: '.date("m/d/Y",strtotime($wo['duedate']));

echo '</td>';

echo '<td align=center>'.DrawComboForLots($wo['lot'],$_SESSION['vintage'],"LOT",$wo['clientcode']);

echo '<a  href="lotmgt.php" onmouseover="return overlib(\''.$thelist.'\',

                CAPTION,\'<c>LOTS</c>\',

                WIDTH, 300, STICKY, DRAGGABLE);"

                onmouseout="nd();">(LOTS)</a>';





//echo 'LOT: <a href=showlotinfo.php?lot='.$wo['lot'].'>'.$wo['lot'].'</a>';

echo '</td>';

//echo '<td align="center">';

//echo ' PRESS PROGRAM #:'.$row['ID'].'<br>';

//echo '</td>';

echo '<td>';

echo 'WO: '.'<a href=wopage.php?action=view&woid='.$_SESSION['woid'].'>'.$_SESSION['woid'].'</a>';

echo '</td>';

echo '</table>';

$thewo=getwo($_SESSION['woid']);

$vessels=filter(lotinvessels($thewo['lot']));

echo '<table border=1 width=50% align=center>';

echo '<tr><td colspan=2 align=right>PRESSING OFF TANK(S):</td><td align=center colspan=2>'.printvessels($vessels).'</td></tr>';

echo '<tr><td align=right>TIME SLOT:</td><td align=left>'.DrawComboFromEnum("wo","STARTSLOT",$row['STARTSLOT'],"startslot").'</td>';

echo '<td align=right>PRESS CUT:</td><td align=left><input type=textbox value="'.$row['PRESSCUT'].'" size=15 name="presscut"></td></tr>';

echo '<tr><td align=right>PRESS PROGRAM:</td><td align=left><input type=textbox value="'.$row['PROGRAM'].'" size=15 name="program"></td>';

echo '<td align=right>APPROX DURATION:</td><td align=left><input type=textbox value="'.$row['PRESSDURATION'].'" size=5 name="pressduration"> (HRS.)</td></tr>';

echo '<tr><td align=right>PRESS TYPE:</td><td align=left>'.DrawComboFromEnum("pressprogram","PRESSTYPE",$row['PRESSTYPE'],"presstype").'</td>';

if ($row['PRESSTYPE']=="PRESS_TO_BBL")

{

echo '<td align=right>FILL LEVEL:</td><td align=left><input type=textbox value="'.$row['FILLLEVEL'].'" size=5 name="filllevel"> (GLNS)</td></tr>';

//echo '<td><a href=presssheet.php?woid='.$_SESSION['woid'].'>BBL SHEET</a></td>';

}

else

echo '<td align=right>SETTLING TIME:</td><td align=left><input type=textbox value="'.$row['SETTLINGTIME'].'" size=5 name="settlingtime"> (HRS)</td></tr>';

echo '</tr>';

echo '<tr><td align=right>COMMENTS: </td><td colspan=4><textarea rows="5" cols="60" name="description">'.$row['DESCRIPTION'].'</textarea></td></tr>';

echo '</table>';

echo '<table border=1 align="center" width=50%>';

echo '<tr><td colspan=2 align=center>PRESS ASSIGNMENTS<br></tr>';

echo '<tr><td colspan=2 align=center>';

if ($_SESSION['woid']!="")

{

    echo '<table border=1 id=res align=center width=100%>';

    

    $query='SELECT `wo`.`ID`,`wo`.`DUEDATE`, `assets`.`NAME`, `wo`.`CLIENTCODE`, reservation.ID AS RESID, assettypes.ID AS ASSETTYPEID, assets.ID AS ASSETSID

FROM  `assettypes`

  INNER JOIN `assets` ON (`assettypes`.`ID` = `assets`.`TYPEID`)

  INNER JOIN `reservation` ON (`assets`.`ID` = `reservation`.`ASSETID`)

  INNER JOIN `wo` ON (`reservation`.`WOID` = `wo`.`ID`)

WHERE

  (wo.ID ="'.$_SESSION['woid'].'")';

    //echo $query;

    $result=mysql_query($query);

    for ($i=0;$i<mysql_num_rows($result);$i++)

    {

        $row=mysql_fetch_array($result);

        echo '<tr>';

        echo '<td align=center width=20%>';

        echo '<a href='.$PHP_SELF.'?action=delres&resid='.$row['RESID'].'>DEL</a>';

        echo '</td>';

        echo '<td align=center width=80%>';

        //  echo '<a href=assetsched.php?assettype='.$row['ASSETTYPEID'].'&assetid='.$row['ASSETSID'].'>'.$row['NAME'].'</a>';

        echo $row['NAME'];

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

    echo '<a href="javascript:void(0);" onmouseover="setObj(1,100,70,\'bottomC\',\'#ffffff\',\'#ffffff\',\'Darkred\',\'black\')" onmouseout="timer(20)">';

    if ($_SESSION['assettypeid']=="")

    echo 'SELECT ASSET TYPE</a>';

    else

    {

        echo $rowassettype['NAME'].'</a> --> ';

        //if ($_SESSION['assettypeid']!=6)

        //echo '<a href="javascript:void(0);" onmouseover="setObj(2,100,100,\'bottomC\',\'#ffffff\',\'#ffffff\',\'Darkred\',\'black\')" onmouseout="timer(20)">';

        //else

        echo '<a href=tankscheduling.php?returnpage='.$PHP_SELF.'&thedate='.strtotime($_SESSION['dateofwork']).'&assettypeid='.$_SESSION['assettypeid'].'>';

        if ($_SESSION['assetid']=="")

        echo 'SELECT ASSET</a>';

        else

        {

            echo $rowasset['NAME'].'</a>';

        }

        

    }

    echo '</td>';

    echo '<td width=20% align=center><a href='.$PHP_SELF.'?action=addasset&assetid='.$_SESSION['assetid'].'>ADD</a></td>';

    echo '</tr>';

    echo '</table>';

}

    echo '</table>';

    echo '<br><br>';

    echo '<table border=1 align="center" width=50%>';

echo '<tr><td align=center colspan=2><input type=submit value="UPDATE"></td></tr>';

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

);



</body>





</html>
