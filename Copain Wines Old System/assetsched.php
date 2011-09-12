<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

  



<title>Fermentation Protocol</title>

<link rel="stylesheet" type="text/css" href="../site.css">

    <script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>



<script language="JavaScript">

<!-- Browser Check -->

iens6=document.all||document.getElementById

ns4=document.layers



<!--DEFINE CONTENT-->

<!--PLACE ALL YOU BOX TITLES HERE - Just add another element to the array -->

var titleArray = new Array

titleArray[1]="<b>CLIENTS</b>"

titleArray[2]="<b>ASSET TYPES</b>"

titleArray[3]="<b>ASSET</b>"



<!--PLACE ALL YOU BOX CONTENT HERE - Make sure you use a += after the first line -->

var linkArray = new Array

<?php



include ("startdb.php");

include ("yesno.php");

include ("setcheck.php");

include ("defaultvalue.php");

include ("manageadditions.php");



if (isset($_GET['ccode']))

{

    $_SESSION['lclient']=$_GET['ccode'];

}

if (isset($_GET['assettypeid']))

{

    $_SESSION['assettypeid']=$_GET['assettypeid'];

    $_SESSION['assetid']="";

}

if (isset($_GET['assetid']))

{

    $_SESSION['assetid']=$_GET['assetid'];

}

if (!isset($_SESSION['lclient']))

{

    $_SESSION['lclient']=$_SESSION['ccode'];

}

$query='SELECT * from clients ORDER BY clients.CLIENTNAME';

$result=mysql_query($query);

print('linkArray[1]="" ;');

for ($i=0; $i<mysql_num_rows($result);$i++)

{

    $row=mysql_fetch_array($result);

    $desc=$row['CLIENTNAME'];

    $link='assetsched.php?ccode='.$row['CODE'];

    $fullline='linkArray[1]+="<a href=\''.$link.'\'>'.$desc.'</a><br> "; ';

    print($fullline);

    

}

$query='SELECT * from assettypes ORDER BY assettypes.NAME';

$result=mysql_query($query);

print('linkArray[2]="" ;');

for ($i=0; $i<mysql_num_rows($result);$i++)

{

    $row=mysql_fetch_array($result);

    $desc=$row['NAME'];

    $link='assetsched.php?assettypeid='.$row['ID'];

    $fullline='linkArray[2]+="<a href=\''.$link.'\'>'.$desc.'</a><br> " ;';

    print($fullline);

}

$query='SELECT * from assets WHERE TYPEID="'.$_SESSION['assettypeid'].'" ORDER BY assets.NAME';

$result=mysql_query($query);

print('linkArray[3]="" ;');

for ($i=0; $i<mysql_num_rows($result);$i++)

{

    $row=mysql_fetch_array($result);

    $desc=$row['NAME'];

    $link='assetsched.php?assetid='.$row['ID'];

    $fullline='linkArray[3]+="<a href=\''.$link.'\'>'.$desc.'</a><br> " ;';

    print($fullline);

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

    }

    else

    {

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

</head>



<body>

<body onClick="stopIt()">



<!--PLACE THIS INSIDE THE BODY, BUT NOT WITHIN ANY OTHER TAG IN THE BODY-->

<layer name="nsviewer" bgcolor="#cccccc" width=0 height=0 style="border-width:thin;z-index:1"></layer>

<script language="JavaScript1.2">

if (iens6){

    document.write("<div id='viewer' style='background-color:#cccccc;marginleft:0;visibility:hidden;position:absolute;width:0;height:0;z-index:1;overflow:hidden'></div>")

}

if (ns4){

    hideobj = eval("document.nsviewer")

    hideobj.visibility="hidden"

}

</script>

<!--END BODY CODE-->

<?php



include ("startdb.php");

include ("queryupdatefunctions.php");

include ("lotinforecords.php");

$_SESSION['clientcode']=getclientcode();

$queryccode='SELECT * FROM clients WHERE CODE="'.strtoupper($_SESSION['lclient']).'"';

$resultccode=mysql_query($queryccode);

$rowccode=mysql_fetch_array($resultccode);



$queryassettype='SELECT * FROM assettypes WHERE ID="'.$_SESSION['assettypeid'].'"';

$resultassettype=mysql_query($queryassettype);

$rowassettype=mysql_fetch_array($resultassettype);



$queryasset='SELECT * from assets WHERE ID="'.$_SESSION['assetid'].'" ORDER BY assets.NAME';

$resultasset=mysql_query($queryasset);

$rowasset=mysql_fetch_array($resultasset);



echo '<table align=center width=100%><tr>';



//echo '<td align=center><a href="javascript:void(0);" onmouseover="setObj(1,130,300,\'bottomC\',\'#ffffff\',\'#ffffff\',\'Darkred\',\'black\')" onmouseout="timer(20)">';

//echo 'CLIENT: '.$rowccode['CLIENTNAME'].'</a></td>';



echo '<td align=center><a href="javascript:void(0);" onmouseover="setObj(2,130,300,\'bottomC\',\'#ffffff\',\'#ffffff\',\'Darkred\',\'black\')" onmouseout="timer(20)">';

echo 'ASSETTYPE: '.$rowassettype['NAME'].'</a></td>';



echo '<td align=center><a href="javascript:void(0);" onmouseover="setObj(3,200,300,\'bottomL\',\'#ffffff\',\'#ffffff\',\'Darkred\',\'black\')" onmouseout="timer(20)">';

echo 'ASSETTYPE: '.$rowasset['NAME'].'</a></td>';

echo '</tr></table>';

?>

 <!--     Begin the DHTML Calendar      -->

    <script language="JavaScript" src="../bigcalendar/dhtmlcal.js"></script>

     <script language="JavaScript" >

     <?php

     $query='SELECT `wo`.`ID`, unix_timestamp(`wo`.`DUEDATE`) AS STARTDATE, UNIX_TIMESTAMP(wo.ENDDATE) AS THEENDDATE, `assets`.`NAME`,`wo`.`CLIENTCODE`

               FROM `assettypes` INNER JOIN `assets` ON (`assettypes`.`ID` = `assets`.`TYPEID`)

                                 INNER JOIN `reservation` ON (`assets`.`ID` = `reservation`.`ASSETID`)

                                 INNER JOIN `wo` ON (`reservation`.`WOID` = `wo`.`ID`)

                    WHERE (`assets`.`ID` ="'. $_SESSION['assetid'].'")';

     $result=mysql_query($query);

     for ($i=0;$i<mysql_num_rows($result);$i++)

     {

        $row=mysql_fetch_array($result);

        if ($row['THEENDDATE']<=$row['STARTDATE'])

        {

            $enddate=strtotime(date('m/d/Y',$row['STARTDATE']).' 11:59 PM');

        }

        else

        $enddate=$row['THEENDDATE'];

        for ($dt=$row['STARTDATE']; $dt<=$enddate; $dt=$dt+86400)

        {

            $yearnum=date("Y",$dt);

            $monthnum=date("m",$dt);

            $daynum=date("d",$dt);

            if ((isstaff()=="YES")|(strtoupper($row['CLIENTCODE'])==strtoupper($_SESSION['clientcode'])))

            {

                $list[$yearnum][$monthnum][$daynum][]='<tr><td align=center><a href=wopage.php?action=view&woid='.$row['ID'].'>['.$row['ID'].']</a> ('.strtoupper($row['CLIENTCODE']).')</td></tr>';

            }

            else

            {

                $list[$yearnum][$monthnum][$daynum][]='<tr><td align=center>'.$row['ID'].' ('.strtoupper($row['CLIENTCODE']).')</td></tr>';

            }

        }

     }

     if (count($list)>0)

     {

        foreach ($list as $y => $value)

        {

            foreach ($value as $m => $value1)

            {

                foreach ($value1 as $d=> $value2)

                {

                    $val="<table align=center>";

                    for ($i=0;$i<count($value2);$i++)

                    {

                        $val=$val.$list[$y][$m][$d][$i];

                    }

                    $val=$val.'</table>';

                    echo 'dcEvent( '.$m.', '.$d.','.$y.', null, "'.$val.'", null, null, null, 1 );';

                    

                }

            }

        }

     }

     ?>

     </script>  

    <script language="JavaScript">

    // the argument needs to be the object name

    // example: var x = new Calendar("x");

    var cal = new Calendar("cal");

    

    //cal.initialMonth  = 0;  // 0=January; 1=February...

    //cal.initialYear   = 2002;

    

    cal.slotCount = 1; // number of slots

    

    cal.monthStartDate = new Array(1,1,1, 1,1,1, 1,1,1, 1,1,1);

    cal.longDays = new Array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat","Sun" );

    cal.longMonths   = new Array( "January", "February", "March", "April",

    "May", "June", "July", "August",

    "September", "October", "November", "December" );

    

    cal.beginMonday     = true;  // begin week with Sun or Mon

    cal.displayDeadText     = false;  // display prev/ next month events

    cal.displayDeadNumber   = false;  // display prev/ next month days

    cal.displayMonthCombo   = true;   // display month selector

    cal.displayYearCombo    = true;   // display year selector

    cal.dateBreak       = true;   // cause a break after displaying the date

    cal.bottomHeading   = false;  // display weekday names at the bottom of calendar

    cal.todayText       = "-TODAY-";  // text to appear in the current date

    cal.trackSelectedDate   = false;

    

    cal.cellWidth  = 90;         // width of date cells

    cal.cellHeight = 90;         // height of date cells

    cal.borderWidth = 1;         // width of the date cell borders (in pixels)

    

    cal.clrBorder   = "#800000";    // border color of calendar

    cal.clrCellText = "#800000";    // event text color

    cal.clrDead = "#c0c0c0";    // background color- unused this month

    cal.clrFuture   = "#ffffff";    // background color- future dates

    cal.clrHdrBg    = "#c04040";    // header background color (mon, tues...)

    cal.clrHdrText  = "#ffffff";    // header text color

    cal.clrNow  = "#ffffc0";    // background color- the current date

    cal.clrPast = "#e0e0e0";    // background color- previous dates

    cal.clrWeekend  = "#f0f0ff";    // background color- weekend dates

    

    var szFont = "Tahoma, Tahoma, Tahoma, Sans Serif";

    cal.hdrFace = szFont;

    cal.hdrSize = "2";

    cal.numFace = szFont;

    cal.numSize = "3";

    cal.cellFace    = szFont;

    cal.cellSize    = "2";

    

    cal.createDateSelect();

    </script>



    <div id="MSIE" name="MSIE">

    <ilayer id="NSALIGN" name="NSALIGN">

    <layer id="NSLAYER" name="NSLAYER">



    <script language="JavaScript">

    cal.createCalendar();

    </script>



    </layer></ilayer></div>

    <!--     End of DHTML Calendar       -->

    <?php

 

?>

</body>



</html>