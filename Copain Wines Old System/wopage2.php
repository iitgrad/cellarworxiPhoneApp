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

  <script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>



<script language="JavaScript1.2">

<!-- Browser Check -->

iens6=document.all||document.getElementById

ns4=document.layers



<!--DEFINE CONTENT-->

<!--PLACE ALL YOU BOX TITLES HERE - Just add another element to the array -->

var titleArray = new Array

titleArray[1]="<b>ASSET TYPES</b>"

titleArray[2]="<b>ASSET</b>"

titleArray[3]="<b>LOT LIST</b>";



<!--PLACE ALL YOU BOX CONTENT HERE - Make sure you use a += after the first line -->

var linkArray = new Array

<?php



include ("startdb.php");

include ("yesno.php");

include ("setcheck.php");

include ("defaultvalue.php");

include ("manageadditions.php");



if ($_GET['action']=="setassettypeid")

{

	$_SESSION['assettypeid']=$_GET['assettypeid'];

	$_SESSION['assetid']="";

}



$query='SELECT * from assettypes ORDER BY assettypes.NAME';

$result=mysql_query($query);

print('linkArray[1]="" ;');

for ($i=0; $i<mysql_num_rows($result);$i++)

{

	$row=mysql_fetch_array($result);

	$tdesc=$row['NAME'];

	$link=$PHP_SELF.'?action=setassettypeid&assettypeid='.$row['ID'];

	$fullline='linkArray[1]+="<a href=\''.$link.'\'>'.$tdesc.'</a><br> " ;';

	print($fullline);

}



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

$query='SELECT * from lots inner join clients on (lots.CLIENTCODE=clients.CLIENTID) where ((lots.YEAR="'.$_SESSION['vintage'].'") AND

     (clients.CODE="'.$_SESSION['clientcode'].'"))';

$result=mysql_query($query);

print('linkArray[3]="";');

for ($i=0; $i<mysql_num_rows($result);$i++)

{

	$row=mysql_fetch_array($result);

	$tdesc=$row['LOTNUMBER'].' '.strtoupper($row['DESCRIPTION']);

	$link='wopage.php?lot='.$row['LOTNUMBER'];

	$fullline='linkArray[3]+="<a href=\''.$link.'\'>'.$tdesc.'</a><br> " ;';

	print($fullline);

}?>



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

</head>



<body>



<body onClick="stopIt()">



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

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");

include ("totalgallons.php");

include ("lotinforecords.php");



function resetvalues()

{

	//	$_SESSION['lot']="";

	$_SESSION['dateofwork']="";

	$_SESSION['enddate']="";

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

	$_SESSION['completioncomments']="";

	$_SESSION['endingtankgallons']="";

	$_SESSION['endingbarrelcount']="";

	$_SESSION['endingtoppinggallons']="";

	$_SESSION['eveningcheck']="";

	$_SESSION['workperformedby']="";

	$temp = clientinfo($_SERVER['REMOTE_USER']);

	//     $_SESSION['clientcode']=$temp['code'];

}





if (isset($_GET['lot'])) $_SESSION['lot']=$_GET['lot'];

if (isset($_GET['woid'])) $_SESSION['woid']=$_GET['woid'];

if (isset($_GET['action'])) $_SESSION['currentaction']=$_GET['action'];





$creationdate=date('y-m-d');



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



function checked($value)

{

	if ($value == "ON")

	return 'checked';

	else

	return "";

}



function doview()

{

	$_SESSION['currentaction']='view';

	$query='SELECT * FROM wo WHERE wo.ID='.$_SESSION['woid'];

	$result=mysql_query($query);

	$row=mysql_fetch_array($result);

	$creationdate=$row['CREATIONDATE'];

	$_SESSION['dateofwork']=$row['DUEDATE'];

	$_SESSION['enddate']=$row['ENDDATE'];

	$_SESSION['status']=$row['STATUS'];

	$_SESSION['morning']=$row['MORNING'];

	$_SESSION['startslot']=$row['STARTSLOT'];

	$_SESSION['endslot']=$row['ENDSLOT'];

	$_SESSION['noon']=$row['NOON'];

	$_SESSION['workperformedby']=$row['WORKPERFORMEDBY'];

	$_SESSION['evening']=$row['EVENING'];

	$tank=$row['VESSELID'];

	$_SESSION['workareaid']=$row['WORKAREAID'];

	$_SESSION['completioncomments']=$row['COMPLETEDDESCRIPTION'];

	$_SESSION['endingtankgallons']=$row['ENDINGTANKGALLONS'];

	$_SESSION['endingbarrelcount']=$row['ENDINGBARRELCOUNT'];

	$_SESSION['endingtoppinggallons']=$row['ENDINGTOPPINGGALLONS'];

	$_SESSION['desc']=$row['OTHERDESC'];

	$_SESSION['lot']=$row['LOT'];

	$_SESSION['type']=$row['TYPE'];

	$_SESSION['clientcode']=$row['CLIENTCODE'];

	$_SESSION['requestor']=$row['REQUESTOR'];

	

	$_SESSION['currentaction']="";

}



if (isset($_FILES['upload_test']))

{

	echo 'isset...';

	if ($_FILES['upload_test']['error'] != UPLOAD_ERR_OK)

	{

		echo ("Upload unsuccessful!<br>\n");

	}

	

	else

	{

		unlink($_FILES['upload_test']['tmp_name']);

		

		print("Local File: ". $_FILES['upload_test']['tmp_name']."<br>");

		print("Name: ". $_FILES['upload_test']['name']."<br>");

		print("Size: ". $_FILES['upload_test']['size']."<br>");

		print("Type: ". $_FILES['upload_test']['type']."<br>");

		

	}

}



if ($_SESSION['currentaction']=="view")

{

	doview();

}



if ($_GET['action']=="new")

{

	resetvalues();

	$_SESSION['lot']=$_GET['lot'];

	$_SESSION['requestor']=strtoupper($_SERVER['REMOTE_USER']);

}



if ($_GET['morning']) $_SESSION['morning']=$_GET['morning'];

if ($_GET['noon'])$_SESSION['noon']=$_GET['noon'];

if ($_GET['evening']) $_SESSION['evening']=$_GET['evening'];



if ($_GET['action']=="setassettypeid")

{

	doview();

}



if ($_GET['action']=="setassetid")

{

	$_SESSION['assetid']=$_GET['assetid'];

	doview();

}



if ($_GET['action']=="del")

{

	$query='DELETE FROM wo WHERE wo.ID='.$_GET['woid'];

	delete_assets_tied_to_woid($_GET['woid']);

	$result=mysql_query($query);

	$_SESSION['currentaction']='del';

	resetvalues();

	

}



if ($_GET['action']=="delres")

{

	$query='DELETE FROM reservation WHERE ID='.$_GET['resid'];

	mysql_query($query);

}



if($_GET['action']=="mod")

{

	$type=$_POST['activity'];

	$_SESSION['type']=$type;

	

	$_SESSION['desc']=$_POST['comments'];

	$_SESSION['workperformedby']=$_POST['workperformedby'];

	$_SESSION['completioncomments']=$_POST['completioncomments'];

	$_SESSION['endingtankgallons']=$_POST['endingtankgallons'];

	$_SESSION['endingbarrelcount']=$_POST['endingbarrelcount'];

	$_SESSION['endingtoppinggallons']=$_POST['endingtoppinggallons'];

	$_SESSION['status']=$_POST['status'];

	

	$query='UPDATE wo SET

        wo.TYPE="'.$_SESSION['type'].'",

        wo.VESSELTYPE="TANK",

        wo.DUEDATE="'.datetimestr($_POST['dateofwork'],$_POST['startslot'],'START').'",

        wo.ENDDATE="'.datetimestr($_POST['enddate'],$_POST['endslot'],'END').'",

	    wo.STARTSLOT="'.$_POST['startslot'].'",

	    wo.ENDSLOT="'.$_POST['endslot'].'",

        wo.STATUS="'.$_SESSION['status'].'",

        wo.LOT="'.$_SESSION['lot'].'",

        wo.MORNING="'.$_POST['morning'].'",

        wo.NOON="'.$_POST['noon'].'",

        wo.EVENING="'.$_POST['evening'].'",

        wo.WORKPERFORMEDBY="'.$_SESSION['workperformedby'].'",

        wo.WORKAREAID="'.$_SESSION['workareaid'].'",

        wo.OTHERDESC="'.$_SESSION['desc'].'",

        wo.COMPLETEDDESCRIPTION="'.$_SESSION['completioncomments'].'",

        wo.ENDINGTANKGALLONS="'.$_SESSION['endingtankgallons'].'",

        wo.ENDINGBARRELCOUNT="'.$_SESSION['endingbarrelcount'].'",

        wo.ENDINGTOPPINGGALLONS="'.$_SESSION['endingtoppinggallons'].'",

        wo.AUTOGENERATED="NO",

        wo.CREATIONDATE="'.date("Y-m-d").'",

        wo.CLIENTCODE="'.$_SESSION['clientcode'].'"

        WHERE (wo.ID="'.$_SESSION['woid'].'")';

	//   echo $query;

	$result=mysql_query($query);

	

	doview();

	

	//     assign_wo_to_reservations($_SESSION['dateofwork'],$_SESSION['clientcode'],$_SESSION['woid']);

	$_SESSION['currentaction']='mod';

	//     resetvalues();

}



if ($_GET['action']=="setclient")

$_SESSION['clientcode']=$_GET['clientcode'];

if ($_GET['action']=="clearclient")

$_SESSION['clientcode']="";



if ($_GET['action']=='addasset')

{

	if ($_SESSION['assetid']!="")

	{

		$query='INSERT into reservation SET ASSETID="'.$_SESSION['assetid'].'", WOID="'.$_SESSION['woid'].'"';

		mysql_query($query);

	}

	$_SESSION['assetid']="";

	$_SESSION['assettypeid']="";

	

}



if($_GET['action']=="add")

{

	$type=$_POST['activity'];

	$_SESSION['type']=$type;

	$_SESSION['desc']=$_POST['comments'];

	$_SESSION['workperformedby']=$_POST['workperformedby'];

	$_SESSION['endingtankgallons']=$_POST['endingtankgallons'];

	$_SESSION['endingbarrelcount']=$_POST['endingbarrelcount'];

	$_SESSION['endingtoppinggallons']=$_POST['endingtoppinggallons'];

	$_SESSION['status']=$_POST['status'];

	

	$query='INSERT INTO wo SET

        wo.TYPE="'.$type.'",

        wo.MORNING="'.$_POST['morning'].'",

        wo.NOON="'.$_POST['noon'].'",

        wo.EVENING="'.$_POST['evening'].'",

        wo.VESSELTYPE="TANK",

        wo.VESSELID="'.$_POST['tank'].'",

        wo.DUEDATE="'.datetimestr($_POST['dateofwork'],$_POST['startslot'],'START').'",

        wo.ENDDATE="'.datetimestr($_POST['enddate'],$_POST['endslot'],'END').'",

	    wo.STARTSLOT="'.$_POST['startslot'].'",

	    wo.ENDSLOT="'.$_POST['endslot'].'",

        wo.LOT="'.$_SESSION['lot'].'",

        wo.REQUESTOR="'.$_SESSION['requestor'].'",

        wo.ENDINGTANKGALLONS="'.$_SESSION['endingtankgallons'].'",

        wo.ENDINGBARRELCOUNT="'.$_SESSION['endingbarrelcount'].'",

        wo.ENDINGTOPPINGGALLONS="'.$_SESSION['endingtoppinggallons'].'",

        wo.COMPLETEDDESCRIPTION="'.$_SESSION['completioncomments'].'",

        wo.OTHERDESC="'.$_SESSION['desc'].'",

        wo.WORKPERFORMEDBY="'.$_SESSION['workperformedby'].'",

        wo.STATUS="'.$_SESSION['status'].'",

        wo.AUTOGENERATED="NO",

        wo.CREATIONDATE="'.date("Y-m-d").'",

        wo.CLIENTCODE="'.$_SESSION['clientcode'].'"';

	$result=mysql_query($query);

	

	$_SESSION['woid']=mysql_insert_id();

	//     assign_wo_to_reservations($_SESSION['dateofwork'],$_SESSION['clientcode'],$_SESSION['woid']);

	$_SESSION['currentaction']='add';

	$_SESSION['currentaction']='view';

	doview();

	//resetvalues();

}



function showworkareas($dummy,$startdate,$enddate)

{

	$sd=strtotime($startdate);

	$ed=strtotime($enddate);

	

	$workareas=listallocassets("WORKAREA",$_SESSION['dateofwork'],clientid($_SESSION['clientcode']),$_SESSION['woid']);

	echo '<table border="1" align="center" width="100%">';

	if ((($ed-$sd)>0) & ($_SESSION['woid']!=""))

	{

		if (count($workareas)==0)

		echo '<tr><td width="50%" align="center"><a href=listworkareas.php>CHOOSE WORK AREA</a>';

		else

		{

			echo '<tr><td width="50%" align="center">WORK AREA</td><td>';

			for ($i=0;$i<count($workareas);$i++)

			{

				echo '<a href=listworkareas.php>'.$workareas[$i]['timeslot'].' - '.$workareas[$i]['name'].'</a><br>';

			}

		}

	}

	else

	echo '<tr><td align="center">---';

	echo '</td></tr></table>';

}



function showassets($assettype, $startdate, $enddate)

{

	$sd=strtotime($startdate);

	$ed=strtotime($enddate);

	

	$assets=listallocassets($assettype,$_SESSION['dateofwork'],clientid($_SESSION['clientcode']),$_SESSION['woid']);

	$params='?assettype='.$assettype.'&morning='.$morning.'&noon='.$noon.'&evening='.$evening;

	echo '<table border="1" align="center" width="100%">';

	if ((($ed-$sd)>0) & ($_SESSION['woid']!=""))

	{

		if (count($assets)==0)

		echo '<tr><td width="50%" align="center"><a href=listassets.php'.$params.'>CHOOSE '.$assettype.'(S)</a>';

		else

		{

			echo '<tr><td width="50%" align="center">'.$assettype.'</td><td>';

			for ($i=0;$i<count($assets);$i++)

			{

				echo '<a href=listassets.php'.$params.'>'.$assets[$i]['timeslot'].' - '.$assets[$i]['name'].'</a><br>';

			}

		}

	}

	else

	echo '<tr><td align="center">---';

	echo '</td></tr></table>';

	

}





if ($_SESSION['requestor']=="")

$_SESSION['requestor']=$_SERVER['REMOTE_USER'];



$_SESSION['returnpage']=$PHP_SELF;



if ($_SESSION['woid']!="")

{

	echo '<form  method="POST" action="'.$PHP_SELF.'?action=mod">';

}

else

{

	echo '<form  method="POST" action="'.$PHP_SELF.'?action=add">';

}

//--STARTS LEFT TABLE

echo '<table width=100%><tr><td align=center width=30%>';

echo '<table border=1 align="center" width="100%" border="0">';

echo '<tr>';

echo '<td valign=top align="center">';

echo '<b><big><a href='.$PHP_SELF.'?action=view>WO ID: '.$_SESSION['woid'].'</a></b></big><br><br>';

echo '<a href=hardcopy/wo.php?woid='.$_SESSION['woid'].'>PRINT</a><br><br>';

if (isstaff()=="YES")

{

	pic ($_SESSION['clientcode'],FALSE,listallclientcodes(),$PHP_SELF.'?=setclient',"clientcode",$PHP_SELF.'?action=clearclient',"CLIENT: ");

}

else

{

	echo 'CLIENT: '.strtoupper($_SESSION['clientcode']);

}

echo '<br>REQUESTOR: '.strtoupper($_SESSION['requestor']).'<br><br>';

echo '<a href=showlotinfo.php?lot='.$_SESSION['lot'].'>LOT HISTORY</a><br><br>';

echo 'LOT: ';

echo '<a href="javascript:void(0);" onmouseover="setObj(3,350,500,\'bottomR\',\'#ffffff\',\'#ffffff\',\'Darkred\',\'black\')" onmouseout="timer(20)">';

if ($_SESSION['lot']=="")

echo 'CHOOSE LOT#</a>';

else

{

	echo $_SESSION['lot'].'</a>';

}

if (isstaff()=="YES")

{

	echo '<br>STATUS: '.DrawComboFromEnum("wo","STATUS", $_SESSION['status'],"status");

}

else

{

	echo '<br>STATUS: '.$_SESSION['status'];

}





echo '<br><br>CREATION DATE: '.$creationdate.'<br><br>';

echo '</tr><tr><td align=center>';

echo '<table border=1 width=100% align=center>';

echo '<tr><td width=70% align=center>START:';

echo '<input type="text" name="dateofwork" size="18" value="'.date("m/d/Y h:i A",strtotime($dateofwork)).'">';

echo '<button id="trigger">...</button>';

echo '</td>';

echo '<td width=30% align=center>';

echo DrawComboFromEnum('wo','STARTSLOT',$_SESSION['startslot'],'startslot');

echo '</td>';

echo '</tr><td align=center>END:';

echo '<input type="text" name="enddate" size="18" value="'.date("m/d/Y h:i A",strtotime($enddate)).'">';

echo '<button id="trigger2">...</button>';

echo '</td><td align=center>';

echo DrawComboFromEnum('wo','ENDSLOT',$_SESSION['endslot'],'endslot');

echo '</td>';

echo '</tr>';



echo '</table>';

echo '<tr>';

if ($_SESSION['woid']!="")

{

	echo '<td align=center><a href='.$PHP_SELF.'?action=del&woid='.$_SESSION['woid'].'>DELETE WORK ORDER</a>';

}

else

{

	echo '<td></td>';

}

echo '<tr>';

echo '</table>';



echo '</td><td width=70% align=center>';



//--ENDS LEFT TABLE



echo '<table border=1 id=res align=center width=100%>';

echo '<tr><td colspan=3 align=center><b><big>RESERVED ASSETS</b></big></td></tr>';



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

	echo '<a href=assetsched.php?assettype='.$row['ASSETTYPEID'].'&assetid='.$row['ASSETSID'].'>'.$row['NAME'].'</a>';

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

echo '<td width=20%></td><td width=80% align=center><a href="javascript:void(0);" onmouseover="setObj(1,130,300,\'bottomC\',\'#ffffff\',\'#ffffff\',\'Darkred\',\'black\')" onmouseout="timer(20)">';

if ($_SESSION['assettypeid']=="")

echo 'SELECT ASSET TYPE</a> --> ';

else

{

	echo $rowassettype['NAME'].'</a> --> ';

}



echo '<a href="javascript:void(0);" onmouseover="setObj(2,170,300,\'bottomC\',\'#ffffff\',\'#ffffff\',\'Darkred\',\'black\')" onmouseout="timer(20)">';

if ($_SESSION['assetid']=="")

echo 'SELECT ASSET</a>';

else

{

	echo $rowasset['NAME'].'</a>';

}

echo '</td>';

echo '<td width=20% align=center><a href='.$PHP_SELF.'?action=addasset>ADD</a></td>';

echo '</tr>';



echo '</table>';

?>

<script language="JavaScript">

<!--

tigra_tables('res', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

// -->

			</script>

<?php

// --ENDS ASSET RESERVATION TABLE

echo '<table border="1" width=100%><tr><td align="center">WORK PERFORMED BY </td><td>';

echo DrawComboFromEnum("wo","WORKPERFORMEDBY", $_SESSION['workperformedby'],"workperformedby");

echo '</td><td>WORK TO BE PERFORMED:</td><td>';

echo DrawComboFromEnum("wo","TYPE", $_SESSION['type'],"activity");

echo '</td><td align=center>';





switch ($_SESSION['type'])

{

	case "LAB TEST" : echo '<a href=labtest.php?woid='.$_SESSION['woid'].'>LAB SHEET</a>'; break;

	case "PRESSOFF" : echo '<a href=presssheet.php?woid='.$_SESSION['woid'].'>PRESS SHEET</a>'; break;

	case "SCP" : echo '<a href=scppage.php?woid='.$_SESSION['woid'].'>SCP</a>'; break;

	case "BLENDING" : echo '<a href=blend.php?woid='.$_SESSION['woid'].'>BLENDING BREAKDOWN</a>'; break;

	default : echo '---'; break;

}

echo'</td></tr></table>';

echo '<table border=1 width=100%>';

echo '<tr><td align=center width=15%>';

echo 'COMMENTS</td><td width="35%"><textarea rows="2" cols="40" name="comments">'.$_SESSION['desc'].'</textarea></td>';

echo '<td width="15%" align="center">';

echo 'COMPLETION COMMENTS</td><td width="35%"><textarea rows="2" cols="40" name="completioncomments">'.$_SESSION['completioncomments'].'</textarea>';



echo '</td></tr></table>';



if ($_SESSION['lot']!="" & $_SESSION['woid']!="")

{

	

	$records=lotinforecords($_SESSION['lot'],"WO",$_SESSION['woid']);

	$tankgallons=$records[count($records)-1]['starting_tankgallons'];

	$bbls=$records[count($records)-1]['starting_bbls'];

	$toppinggallons=$records[count($records)-1]['starting_toppinggallons'];

	

	echo '<table width=100% align=center>';

	echo '<td width=35% align=center>';

	echo '<table border=1 align=center with=100%>';

	echo '<tr><td></td><td>TNK GLNS</td><td>TOT BBLS</td><td>TOP GLNS</td></tr>';

	echo '<tr><td>CURRENT GLNS</td><td>'.$tankgallons.'</td><td>'.$bbls.'</td><td>'. $toppinggallons.'</td></tr>';

	echo '<tr><td>RESULTING GLNS</td><td><input type=text value="'.number_format($_SESSION['endingtankgallons'],2).'" name="endingtankgallons" size="9"></td>';

	echo '<td><input type=text value="'.number_format($_SESSION['endingbarrelcount'],0).'" name="endingbarrelcount" size="5"></td>';

	echo '<td><input type=text value="'.number_format($_SESSION['endingtoppinggallons'],2).'" name="endingtoppinggallons" size="7"></td></tr>';

	echo '</table></td>';

	echo '<td width=65% align=center>';

	echo showstructure($records[count($records)-1]['structure']);

	echo '</td>';

	echo '</table>';

}



if ($_SESSION['woid']!="")

echo '<tr><td colspan=2 align=center><input type="submit" value="UPDATE WORKORDER" name="B1"></td></tr>';

else

echo '<tr><td colspan=2 align=center><input type="submit" value="ADD WORKORDER" name="B1"></td></tr>';



echo '</table>';

echo '<tr>';

echo '</td></table>';

echo '<table border="1" width="100%">';

echo '</td></tr></table>';



?>     

</form>

<script type="text/javascript">

Calendar.setup(

{

	inputField  : "dateofwork",  // ID of the input field

	ifFormat    : "%m/%d/%Y",    // the date format

	button      : "trigger"      // ID of the button

}

);

Calendar.setup(

{

	inputField  : "enddate",  // ID of the input field

	ifFormat    : "%m/%d/%Y",    // the date format

	button      : "trigger2"      // ID of the button

}

);

 </script>



</table>

</body>



</html>

