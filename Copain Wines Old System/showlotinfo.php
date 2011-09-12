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



<script language="JavaScript1.2">

<!-- Browser Check -->

iens6=document.all||document.getElementById

ns4=document.layers



<!--DEFINE CONTENT-->

<!--PLACE ALL YOU BOX TITLES HERE - Just add another element to the array -->

var titleArray = new Array

titleArray[1]="<b>LOT LIST</b>"



<!--PLACE ALL YOU BOX CONTENT HERE - Make sure you use a += after the first line -->

var linkArray = new Array

<?php



include ("startdb.php");

include ("yesno.php");

include ("setcheck.php");

include ("defaultvalue.php");

include ("manageadditions.php");



if (!isset($_SESSION['vintage']))

{

    $_SESSION['vintage']=2007;

}



$query='SELECT * from lots inner join clients on (lots.CLIENTCODE=clients.CLIENTID) where ((lots.YEAR="'.$_SESSION['vintage'].'") AND

     (clients.CODE="'.$_SESSION['clientcode'].'")) ORDER BY lots.LOTNUMBER';

$result=mysql_query($query);

print('linkArray[1]="" ');

?>



<?php

for ($i=0; $i<mysql_num_rows($result);$i++)

{

    $row=mysql_fetch_array($result);

    $desc=$row['LOTNUMBER'].' '.strtoupper($row['DESCRIPTION']);

    $link='showlotinfo.php?lot='.$row['LOTNUMBER'];

    $fullline='linkArray[1]+="<a href=\''.$link.'\'>'.$desc.'</a><br> " ';

    print($fullline);

    ?>

    

    <?php

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

</head>



<body>

<body onClick="stopIt()">

<body >



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



if ($_GET['lot']!="")

{

    $_SESSION['lot']=$_GET['lot'];

}

if ($_GET['showpopd']!="")

  $_SESSION['showpopd']=$_GET['showpopd'];

  

$date=$_GET['todate'];

$totalgallons=0;

echo '<table width=800 align=center><tr valign=top><td align=left>';

echo '<a href=hardcopy/lothistorypage.php?lot='.$_SESSION['lot'].'>PRINT THIS PAGE</a></td>';



if ($_SESSION['lot']=="")

{

    echo '<td align=center>';

    echo '<a href="javascript:void(0);" onmouseover="setObj(1,350,500,\'bottomC\',\'#ffffff\',\'#ffffff\',\'Darkred\',\'black\')" onmouseout="timer(20)">';

    echo 'CHOOSE LOT</a></td>';

}

else

{

    $li=lotinfo($_SESSION['lot']);

    echo '<td align=center>';

    echo '<b><big><a href="javascript:void(0);" onmouseover="setObj(1,350,500,\'bottomC\',\'#ffffff\',\'#ffffff\',\'Darkred\',\'black\')" onmouseout="timer(20)">';

    echo 'LOT: '.$_SESSION['lot'].'</a><br>';

    echo '</big>'.strtoupper($li['DESCRIPTION']).'</big></b><br>';

    if ($_SESSION['showpopd']==1)

    echo '<a href=showlotinfo.php?showpopd=0&lot='.$_GET['lot'].'>FILTER OUT PUMP OVERS AND PUNCH DOWNS</a>';

    else

    echo '<a href=showlotinfo.php?showpopd=1&lot='.$_GET['lot'].'>INCLUDE PUMP OVERS AND PUNCH DOWNS</a>';  

}

echo '</td><td align=right>';

echo '<a href=hardcopy/lothistory.php?lot='.$_GET['lot'].'>PRINT LOT RECORD BOOKLET</a><br>';

echo '<a href=showlotinfo2.php?lot='.$_GET['lot'].'>ALTERNATE VIEW</a>';

echo '</td></tr>';



echo '</table>';

$record=lotinforecords($_SESSION['lot']);

//echo '<pre>';

//print_r($record);

//exit;

echo '<table id="table1" border="0" width=800 align="center">';

echo '<tr valign=bottom><td align="center" width="10%">DATE<hr></td><td width="10%" align="center">ID<hr></td>

  <td width="70%" align="center">DESCRIPTION<hr></td><td width=10% align="center">TANK<br>GALLONS<hr></td>

  <td>BBLS<hr></td><td align=center>TOPPING<br>GALLONS<hr></td><td align=center>TOTAL<br>GALLONS<hr></td>

  <td align=center>CASE<br>EQUIV<hr></td><td align=center>END<br>STATE<hr></td><td align=center>ALC<hr></td></tr>';

for ($i=0;$i<count($record);$i++)

{

    $row=$record[$i]['data'];

 //   print_r($row);

    if (($row['TYPE']!="PUMP OVER" & $row['TYPE']!="PUNCH DOWN") | ($_SESSION['showpopd']==1))

    {

        switch ($record[$i]['type'])

        {

            case "BLEND":

            {

                echo '<tr><td align="center">'.date("m-d-y",$record[$i]['date']).'</td>';

                echo '<td align="center"><a href=wopage.php?action=view&woid='.$row['WOID'].'>WO-'.$row['WOID'].'</a></td>';

                echo '<td><table border="0" width="100%"><tr><td align="center" width="20%">BLEND</td><td align="center">';

                if ($row['DIRECTION']=="IN FROM")

                echo '<td align=center>'.$row['GALLONS'].' GALLONS OUT TO <a href=showlotinfo.php?lot='.$row['LOT'].'>'.$row['LOT'].'</a><br>'.$row['OTHERDESC'].'</td></table></td>';

                else

                echo '<td align=center>'.$row['GALLONS'].' GALLONS IN FROM <a href=showlotinfo.php?lot='.$row['LOT'].'>'.$row['LOT'].'</a><br>'.$row['OTHERDESC'].'</td></table></td>';

                break;

            }

            case "WT" :

            {

                $tons=($row['SUM_OF_WEIGHT']-$row['SUM_OF_TARE'])/2000;

                echo '<tr><td align="center">'.date("m-d-y",$row['THEDATE']).'</td>';

                echo '<td align="center"><a href=wtpage.php?wtid='.(5000+$row['TAGID']).'>WT-'.(5000+$row['TAGID']).'</a></td>';

                echo '<td align="center">'.date("Y",$row['THEDATE']);

                echo '  '.strtoupper($row['VINEYARD']);

                echo '  '.strtoupper($row['APPELATION']);

                echo '  '.strtoupper($row['VARIETY']);

                echo '  '.$tons.' TONS</td>';

                break;

            }

            case "BOL" :

            {

                echo '<tr><td align="center">'.date("m-d-y",$row['THEDATE']).'</td>';

                echo '<td align="center"><a href=bolpage.php?bolid='.$row['ID'].'>BOL-'.$row['ID'].'</a></td>';

                echo '<td align="center">'.$row['NAME'].' '.$row['DIRECTION'].' '.$row['BONDED'].'</td>';

                break;

            }

            case "WO" :

            {

                echo '<tr><td align="center">'.date("m-d-y",$row['THEDATE']).'</td>';

                echo '<td align="center"><a href=wopage.php?action=view&woid='.$row['ID'].'>WO-'.$row['ID'].'</a></td>';

                if (($row['ENDINGTANKGALLONS']!="") | ($row['ENDINGBARRELCOUNT']!="") | ($row['ENDINGTOPPINGGALLONS']!=""))

                {

                    $volume= round($row['ENDINGTANKGALLONS'] + $row['ENDINGBARRELCOUNT']*60 +$row['ENDINGTOPPINGGALLONS'],0);

                    $totalgallons=$volume;

                }

                switch ($row['TYPE'])

                {

                    case "LAB TEST" :

                    {

                        echo '<td><table border="0" width="100%"><tr><td align="center" width="20%">'.$row['TYPE'].'</td><td align="center">';

                        $labtestquery='SELECT * FROM  `labresults` INNER JOIN `labtest` ON (`labresults`.`LABTESTID` = `labtest`.`ID`) WHERE labtest.WOID="'.$row['ID'].'" LIMIT 3';

                        $labtestresults=mysql_query($labtestquery);

                        echo '<table border=1 width=100%>';

                        if ($row['OTHERDESC']!="")

                        echo '<tr><td colspan=3 align=center>'.$row['OTHERDESC'].'</td></tr>';

                        for ($k=0;$k<mysql_num_rows($labtestresults);$k++)

                        {

                            $labrow=mysql_fetch_array($labtestresults);

                            echo '<tr><td align=right>'.$labrow['LABTEST'].'</td><td align=right>'.$labrow['VALUE1'].' '.$labrow['UNITS1'].'</td><td align=left>'.$labrow['COMMENT'].'</td></tr>';

                        }

                        echo '</table>';

                        echo '</td></tr></table></td>';

                        break;

                    }

                    case "SCP" :

                    {

                        $scpquery='SELECT * from scp WHERE WOID="'.$row['ID'].'"';

                        $scpresult=mysql_query($scpquery);

                        $scprow=mysql_fetch_array($scpresult);

                        echo '<td><table border="0" width="100%"><tr><td align="center" width="20%"><a href=scppage.php?woid='.$row['ID'].'>SCP</a> '.

                        '</td><td align="left">ESTIMATE : '.$scprow['ESTTONS'].' TONS OF '.$scprow['VARIETAL'].'</td></tr></table></td>';

                        break;

                    }

                    case "BOTTLING" :

                    {

                        $bottlingquery='SELECT * from bottling WHERE WOID="'.$row['ID'].'"';

                        $bottlingresult=mysql_query($bottlingquery);

                        $bottlingrow=mysql_fetch_array($bottlingresult);

                        echo '<td><table border="0" width="100%"><tr><td align="center" width="20%">'.$row['TYPE'].

                        '</td><td align="center">'.strtoupper($bottlingrow['FINALCASECOUNT']).' CASES x '.strtoupper($bottlingrow['GALLONSPERCASE']).'</td></tr></table></td>';

                        break;

                    }

                    case "PUMP OVER" :

                    {

                        

                        echo '<td><table border="0" width="100%"><tr><td align="center" width="22%">'.

                        $row['TYPE'].'</td><td align="center" width=40%>'.

                        'DURATION - '.$row['DURATION'].'</td>'.

                        '<td align="center"><a href=viewfermcurves.php?allowadd=TRUE&lot='.$_GET['lot'].'&vesseltype='.$row['VESSELTYPE'].'&vessel='.$row['VESSELID'].'>'.$row['VESSELTYPE'].'-'.$row['VESSELID'].'</a></td></tr></table></td>';

                        break;

                    }

                    case "PUNCH DOWN" :

                    {

                        echo '<td><table border="0" width="100%"><tr><td align="center" width="22%">'.

                        $row['TYPE'].'</td>'.

                        '<td align="center" width=40%>STRENGTH - '.$row['STRENGTH'].'</td>'.

                        '<td align="center"><a href=viewfermcurves.php?allowadd=TRUE&lot='.$_GET['lot'].'&vesseltype='.$row['VESSELTYPE'].'&vessel='.$row['VESSELID'].'>'.$row['VESSELTYPE'].'-'.$row['VESSELID'].'</a></td></tr></table></td>';

                        break;

                    }

                    case "DRYICE" :

                    {

                        echo '<td><table border="0" width="100%"><tr><td align="center" width="22%">'.

                        $row['TYPE'].'</td>'.

                        '<td align="center" width=40%></td>'.

                        '<td align="center"><a href=viewfermcurves.php?allowadd=TRUE&lot='.$_GET['lot'].'&vesseltype='.$row['VESSELTYPE'].'&vessel='.$row['VESSELID'].'>'.$row['VESSELTYPE'].'-'.$row['VESSELID'].'</a></td></tr></table></td>';

                        break;

                    }

                    case "BLENDING" :

                    {

                        $queryblendsforwo='SELECT `blenditems`.`SOURCELOT`, `blenditems`.`GALLONS`, `blenditems`.`DIRECTION`, `wo`.`LOT`,

              `blend`.`WOID`, UNIX_TIMESTAMP(`wo`.`DUEDATE`) AS THEDATE FROM `blenditems`

               INNER JOIN `blend` ON (`blenditems`.`BLENDID` = `blend`.`ID`)

               INNER JOIN`wo` ON (`blend`.`WOID` = `wo`.`ID`)

               WHERE  (`wo`.`ID` = "'.$row['ID'].'")';

                        $result2=mysql_query($queryblendsforwo);

                        echo '<td><table border="0" width="100%"><tr><td align="center" width="22%">'.

                        $row['TYPE'].'</td><td align=center>';

                        for ($k=0;$k<mysql_num_rows($result2);$k++)

                        {

                            $row2=mysql_fetch_array($result2);

                            echo $row2['GALLONS'].' GALLONS '. $row2['DIRECTION'].' <a href=showlotinfo.php?lot='.$row2['SOURCELOT'].'>'.$row2['SOURCELOT'].'</a><br>'.$row['OTHERDESC'].'<br>';

                        }

                        echo '</td></table>';

                        break;

                    }

                    default :

                    {

                        echo '<td><table border="0" width="100%"><tr><td align="center" width="20%">'.$row['TYPE'].'</td><td align="center">'.strtoupper($row['OTHERDESC']).'</td></tr></table></td>';

                    }

                }

                

                $query2='SELECT  `wo`.`LOT`,`additions`.`SUPERFOODAMT`,`additions`.`DAPAMOUNT`,`additions`.`HTAAMOUNT`,

                     `additions`.`GOAMOUNT`,`additions`.`WATERAMOUNT`,`additions`.`INNOCULATIONBRAND`,`additions`.`INNOCULATIONAMOUNT`

               FROM `wo`

                 INNER JOIN `fpaddmap` ON (`wo`.`RELATEDADDITIONSID` = `fpaddmap`.`FERMPROTID`)

                 INNER JOIN `additions` ON (`fpaddmap`.`ADDITIONID` = `additions`.`ID`)

               WHERE wo.ID="'.$row['ID'].'"';

                

                $result2=mysql_query($query2);

                $num_rows2=mysql_num_rows($result2);

                if ($additionsshown[$row['ID']]!=1)

                { $additionsshown[$row['ID']]=1;

                for ($j=0;$j<$num_rows2;$j++)

                {

                    $row2=mysql_fetch_array($result2);

                /*    echo '<tr><td></td><td></td>';

                    echo '<td align="center"><table border="1" width="100%"><tr>';

                    echo '<td align="center" width=22%>ADDITION</td>';

                    echo '<td align="center">SF<br>'.$row2['SUPERFOODAMOUNT'].'</td>';

                    echo '<td align="center">DAP<br>'.$row2['DAPAMOUNT'].'</td>';

                    echo '<td align="center">HTA<br>'.$row2['HTAAMOUNT'].'</td>';

                    echo '<td align="center">GO<br>'.$row2['GOAMOUNT'].'</td>';

                    echo '<td align="center">H20<br>'.$row2['WATERAMOUNT'].'</td>';

                    echo '<td align="center">INNBRND<br>'.$row2['INNOCULATIONBRAND'].'</td>';

                    echo '<td align="center">INNAMT<br>'.$row2['INNOCULATIONAMOUNT'].'</td>';

                    echo '</tr></table>';

                    //echo '<td align="center">'.number_format($totalgallons,2).'</td>';

                    echo '</tr>'; */

                }

                }

                

            }

            break;

            

        }

        echo '<td align="center">'.number_format($record[$i]['ending_tankgallons'],0).'</td>';

        echo '<td align="center">'.number_format($record[$i]['ending_bbls'],0).'</td>';

        echo '<td align="center">'.number_format($record[$i]['ending_toppinggallons'],0).'</td>';

        echo '<td align="center">'.number_format($record[$i]['ending_toppinggallons']+$record[$i]['ending_bbls']*60+$record[$i]['ending_tankgallons'],0).'</td>';

        echo '<td align="center">'.number_format(.42*($record[$i]['ending_toppinggallons']+$record[$i]['ending_bbls']*60+$record[$i]['ending_tankgallons']),0).'</td>';

        echo '<td align="center">'.$record[$i]['end_state'].'</td>';

        if (($record[$i]['end_state']!="JUICE")&($record[$i]['end_state']!=''))

        {

            if ($record[$i]['alcohol']==1)

            echo '<td align="center"><14%</td>';

            elseif ($record[$i]['alcohol']==25)

            echo '<td align="center">>=14%</td>';

            else

            echo '<td align="center">'.number_format($record[$i]['alcohol'],1).'%</td>';

            

        }

    }

}

echo '<tr><td></td><td name=start align=center><a href=wopage.php?action=new&lot='.$_GET['lot'].'>NEW WO</a></td></tr>';

echo '<tr><td></td><td align=center><a href=bolpage.php?action=new>NEW BOL</a></td></tr>';

echo '</table>';

?>

            <script language="JavaScript">

            <!--

            tigra_tables('table1', 1, 2, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

            // -->

            </script>

<?php

echo '<table align=center width=50%><td align=center>';

echo showstructure($record[count($record)-1]['structure']);



$labresults=currentlabanalysis($_SESSION['lot']);



echo '</td></table>';

echo '<table align=center border=1>';

if (count($labresults)>0)

{

    echo '<tr>';

    foreach ($labresults as $key=>$value)

    {

        echo '<td align=center width=120>'.$key.' - '.$value['value'].' '.$value['units'].'</td>';

    }

    echo '</tr>';

    echo '<tr>';

    foreach ($labresults as $key=>$value)

    {

        echo '<td align=center width=120>'.date("m/d/Y",strtotime($value['date'])).'</td>';

    }

    echo '</tr>';

}

echo '</table>';

$allbbls=bblsinlot($_GET['lot'],date("Y-m-d",time()));

echo '<table align=center border=1>';

echo '<tr><td colspan=5 align=center><b>BBL INVENTORY</b></td></tr>';

$bblc=0;

echo '<tr>';

if (count($allbbls['bbls'])>0)

{

    foreach ($allbbls['bbls'] as $key=>$value)

    {

       if ($bblc==0)

         echo '<tr>';

        echo '<td align=center><a href=barrelhistoryview.php?bblnumber='.$key.'>'.$value['name'].'</a></td>';

        $bblc++;

        if ($bblc==5)

       {

         echo '</tr>';

         $bblc==0;

         }

    }

}

echo '</table>';

echo '</td>';



?>



</body>



</html>
