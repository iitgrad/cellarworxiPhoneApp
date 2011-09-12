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

      function showval()

      {

      	theurl="702view.php?lot="+document.dates.thelot.value+"&startdate="+document.dates.startdate.value+"&enddate="+document.dates.enddate.value;

      	location.href=theurl;

      }

  </script>

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

  	$_SESSION['vintage']=2003;

  }

  

  $query='SELECT * from lots inner join clients on (lots.CLIENTCODE=clients.CLIENTID) where ((lots.YEAR="'.$_SESSION['vintage'].'") AND

     (clients.CODE="'.$_SESSION['clientcode'].'")) ORDER BY lots.LOTNUMBER';

  $result=mysql_query($query);

  print('linkArray[1]="<a href='.$PHP_SELF.'?startdate='.$_GET['startdate'].'&enddate='.$_GET['enddate'].'>ALL LOTS</a><br>" ');

  ?>

  

  <?php

  for ($i=0; $i<mysql_num_rows($result);$i++)

  {

  	$row=mysql_fetch_array($result);

  	$desc=$row['LOTNUMBER'].' '.strtoupper($row['DESCRIPTION']);

  	$link=$PHP_SELF.'?lot='.$row['LOTNUMBER'].'&startdate='.$_GET['startdate'].'&enddate='.$_GET['enddate'];

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



if (isset($_GET['clientid']))

{

$data=$_SESSION['data702byclient'];

$data=$data[$_GET['clientid']];

}

else

$data=$_SESSION['data702'];

$detail=$data[$_GET['section']]['mods'][ereg_replace('%',' ',$_GET['mod'])];

echo '<pre><center><b><big>702 SOURCE DETAIL</big></b></center><pre>';

echo '<table align=center width=500>';

for ($i=0;$i<count($detail['participant']);$i++)

{

	$item=$detail['participant'][$i];

	switch ($item['type'])

	{

		case "BLEND":

		{

			echo '<tr><td align=center>BLENDED VIA WORK ORDER <a href=wopage.php?action=view&woid='.$item['id'].'>'.$item['id'].'</a>  '.$item['val'].'</td></tr>';

			break;

		}

		case "WO":

		{

			echo '<tr><td align=center>WORK ORDER <a href=wopage.php?action=view&woid='.$item['id'].'>'.$item['id'].'</a>  '.$item['val'].'</td></tr>';

			break;

		}

		case "WT":

		{

			echo '<tr><td align=center>WEIGH TAG <a href=wtpage.php?wtid='.($item['id']).'>'.($item['id']).'</a>  '.$item['val'].'</td></tr>';

			break;

		}

		case "BOL":

		{

			echo '<tr><td align=center>BILL OF LADING <a href=bolpage.php?bolid='.$item['id'].'>'.$item['id'].'</a>  '.$item['val'].'</td></tr>';

			break;

		}

	}

}

echo '</table>';

?>



</body>



</html>

