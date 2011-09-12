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

    $_SESSION['vintage']=2006;

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

/* echo '<table width=800 align=center><tr valign=top><td align=left>';

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

echo '<a href=hardcopy/lothistory.php?lot='.$_GET['lot'].'>PRINT LOT RECORD BOOKLET</a>';

echo '</td></tr>';



echo '</table>';

*/



$li=lotinfo($_SESSION['lot']);

$w1=60;

$w2=30;

$initiallab=initiallabanalysis($_SESSION['lot']);

echo '<table border=1 align=center>';

echo '<tr>';

echo '<td align=left width=200>Wine: '.$li['YEAR'].' '.$li['DESCRIPTION'].'</td>';

echo '<td align=left width='.$w1.'>pH</td><td align=right width='.$w2.'>'.number_format($initiallab['PH']['value'],2).'</td><td align=left width='.$w1.'>Brix</td><td align=right width='.$w2.'>'.number_format($initiallab['BRIX']['value'],1).'</td>';

echo '</tr>';

echo '<tr>';

echo '<td align=left width=200>Lot Code: '.$li['LOTNUMBER'].'</td>';

echo '<td align=left>TA</td><td align=right>'.number_format($initiallab['TA']['value'],2).'</td><td align=left>GLU/FRU</td><td align=right>'.number_format($initiallab['GLUFRU']['value'],2).'</td>';

echo '</tr>';

echo '<tr>';

echo '<td align=left width=200></td>';

echo '<td align=left>Tartaric</td><td align=right>'.number_format($initiallab['TARTARIC']['value'],2).'</td><td align=left>Ammonia</td><td align=right>'.number_format($initiallab['AMMONIA']['value'],0).'</td>';

echo '</tr>';

echo '<tr>';

echo '<td align=left width=200></td>';

echo '<td align=left>L-malic</td><td align=right>'.number_format($initiallab['MALIC_ACID']['value'],2).'</td><td align=left>Amino-nitrogen</td><td align=right>'.number_format($initiallab['AMINO_NITROGEN']['value'],0).'</td>';

echo '</tr>';

echo '<tr>';

echo '<td align=left width=200></td>';

echo '<td align=left>Buffer Cap</td><td align=right>'.number_format($initiallab['BUFFER_CAPACITY']['value'],2).'</td><td align=left>Potassium</td><td align=right>'.number_format($initiallab['POTASSIUM']['value'],0).'</td>';

echo '</tr>';

echo '</table>';

$typeabbr['WT']='WT';

$typeabbr['SCP']='SCP';

$typeabbr['BOL']='BOL';

$typeabbr['OTHER']='O';

$typeabbr['LAB TEST']='A';

$typeabbr['BLENDING']='B';

$typeabbr['PRESSOFF']='BD';

$typeabbr['BOTTLING']='BT';

$typeabbr['CHEMICAL ADDITION']='CA';

$typeabbr['BLENDING']='CL';

$typeabbr['DISPOSAL']='D';

$typeabbr['DEALCOHOLICATION']='DA';

$typeabbr['FILTRATION']='FL';

$typeabbr['FINING']='FN';

$typeabbr['MALOLACTIC BACTERIA INOCULATION']='MI';

$typeabbr['RACKING OUT AND BACK']='ROB';

$typeabbr['RACKING']='RT';

$typeabbr['RACKING TANK TO TANK']='RTT';

$typeabbr['SOLD AS BULK WINE']='SAB';

$typeabbr['TOPPING']='T';

$typeabbr['TOPPING FOR OTHER LOT']='TW';

$typeabbr['VA REDUCTION']='VAR';



$record=lotinforecords($_SESSION['lot']);



echo '<table id="table1" border="1" width=800 align="center">';

echo '<tr valign=bottom>

<td align="center">DATE<hr></td>

<td align="center">OP<br>CODE<hr></td>

<td align="center" width=40>VOL<br>BEF<hr></td>

<td align="center" width=40>VOL<br>CHG<hr></td>

<td align="center" width=40>VOL<br>AFT<hr></td>

<td align="center">VOL<br>KEG<br>CARB<hr></td>

<td align="center">PH<hr></td>

<td align="center">TA<hr></td>

<td align="center">FSO2<hr></td>

<td align="center">ALC<hr></td>

<td align="center">G/FRU<hr></td>

<td align="center">ENZ<br>MALIC<hr></td>

<td align="center">CHROMO<br>MALIC<hr></td>

<td align="center">VA<hr></td>

<td align="center">OTHER<hr></td>

<td align="center">NOTES/DESCRIPTION<hr></td>

</tr>';



for ($i=0;$i<count($record);$i++)

{

	$opcode='';

    $row=$record[$i];

    $desc=$row['data']['OTHERDESC'];

	switch ($row['type']) {

		case "BOL" :

		{

			$opcode="BOL";

			break;

		}

		case "WT" :

		{

		   $opcode="WT";

		   $tons=($row['data']['SUM_OF_WEIGHT']-$row['data']['SUM_OF_TARE'])/2000;

		   $desc='TOTAL TONS: '.number_format($tons,2);

		   break;

		}

		case "WO" :

		{

		   $opcode=$row['data']['TYPE'];

		   if ($opcode=="LAB TEST")

		   {

		   	  $labresults=thelabresults($row['data']['ID']);

//			    echo '<pre>';

//				print_r($labresults);

//				echo '</pre>';

		   }

		   break;

		}	

	}

	echo '<tr>';

	echo '<td align="center">'.date("m/d/y",$row['date']).'</td>';

	echo '<td align="center">'.$typeabbr[$opcode].'</td>';

	echo '<td align="right">'.number_format($row['starting_tankgallons'],0).'</td>';

	echo '<td align="right">'.number_format($row['ending_tankgallons']-$row['starting_tankgallons'],0).'</td>';

	echo '<td align="right">'.number_format($row['ending_tankgallons'],0).'</td>';

	echo '<td align="center"></td>';

	if ($opcode=="LAB TEST")

	{

	echo '<td align="center">'.number_format($labresults['PH']['value'],2).'</td>';

	echo '<td align="center">'.number_format($labresults['TA']['value'],2).'</td>';

	echo '<td align="center">'.number_format($labresults['FSO2']['value'],2).'</td>';

	echo '<td align="center">'.number_format($labresults['ALCOHOL']['value'],2).'</td>';

	echo '<td align="center">'.number_format($labresults['GLUFRU']['value'],2).'</td>';

	echo '<td align="center">'.number_format($labresults['MALIC_ACID']['value'],2).'</td>';

	echo '<td align="center">'.number_format($labresults['']['value'],2).'</td>';

	echo '<td align="center">'.number_format($labresults['VA']['value'],2).'</td>';

	echo '<td align="center">'.number_format($labresults['']['value'],2).'</td>';

	}

	else

	{

		echo '<td colspan=9></td>';

	}

	echo '<td align="center">'.$desc.'</td>';

	echo '</tr>';

}



//echo '<pre>';

//print_r($record);

//echo '</pre>';

//exit;

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

//echo showstructure($record[count($record)-1]['structure']);

/*

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

*/

?>



</body>



</html>