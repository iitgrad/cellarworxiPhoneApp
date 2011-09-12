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
  	$_SESSION['2008'];
  }
  
  $query='SELECT * from lots inner join clients on (lots.CLIENTCODE=clients.CLIENTID) where ((lots.YEAR="'.$_SESSION['vintage'].'") AND
     (clients.CODE="'.$_SESSION['clientcode'].'")) order by lots.ID';
  $result=mysql_query($query);
  print('linkArray[1]="" ');
  ?>
  
  <?php
  for ($i=0; $i<mysql_num_rows($result);$i++)
  {
  	$row=mysql_fetch_array($result);
 // 	$desc="---";
// 	$desc=$row['LOTNUMBER'];
  	$desc=trim($row['LOTNUMBER']).' '.strtoupper($row['DESCRIPTION']);
  	$link=$PHP_SELF.'?bolitem_lot='.trim($row['LOTNUMBER']);
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
<?php
include ("startdb.php");
include ("queryupdatefunctions.php");
include ("assetfunctions.php");
include ("totalgallons.php");
include ("lotinforecords.php");

//echo $query;
if ($_GET['action']=="setclient")
$_SESSION['clientcode']=$_GET['clientcode'];
if ($_GET['lot'])
$_SESSION['lot']=$_GET['lot'];

if ($_GET['bolitem_lot']!="")
$_SESSION['bolitem_lot']=$_GET['bolitem_lot'];

if ($_GET['action']=="additem")
{
	if ($_POST['alc']<14)
	$alc='<14%';
	else
	$alc='>=14%';
	$query='INSERT INTO bolitems SET bolitems.LOT="'.$_POST['bolitem_lot'].'", '.
	'bolitems.GALLONS="'.$_POST['gallonstransfered'].'", '.
	'bolitems.TYPE="'.$_POST['thetype'].'", '.
	'bolitems.ALC="'.$alc.'", '.
	'bolitems.CLIENTCODE="'.$_SESSION['clientcode'].'", '.
	'bolitems.BOLID="'.$_POST['bolid'].'"';
	//	echo $query;
	$result=mysql_query($query);
}
if ($_GET['action']=="mod")
{
	$query='UPDATE bol SET '.
	'bol.CLIENTCODE="'.strtoupper($_POST['clientcode']).'", '.
	'bol.DIRECTION="'.$_POST['direction'].'", '.
	'bol.BONDED="'.$_POST['bonded'].'", '.
	'bol.DATE="'.date("Y-m-d",strtotime($_POST['date'])).'", '.
	'bol.BOND="'.strtoupper($_POST['bond']).'", '.
	'bol.FACILITYID="'.strtoupper($_POST['facility']).'", '.
	'bol.NAME="'.strtoupper($_POST['name']).'", '.
	'bol.ADDRESS1="'.strtoupper($_POST['address1']).'", '.
	'bol.ADDRESS2="'.strtoupper($_POST['address2']).'", '.
	'bol.CITY="'.strtoupper($_POST['city']).'", '.
	'bol.STATE="'.strtoupper($_POST['state']).'", '.
	'bol.ZIP="'.strtoupper($_POST['zip']).'", '.
	'bol.PHONE="'.$_POST['phone'].'", '.
	'bol.CARRIER="'.strtoupper($_POST['carrier']).'" WHERE bol.ID="'.$_POST['bolid'].'"';
//		  echo $query;
//	exit;
	$result=mysql_query($query);
}
if ($_GET['action']=="additemdetail")
{
	$query='INSERT INTO bolitembreakout SET '.
	'bolitembreakout.VINTAGE="'.$_POST['vintage'].'",'.
	'bolitembreakout.VARIETAL="'.strtoupper($_POST['variety']).'",'.
	'bolitembreakout.APPELLATION="'.strtoupper($_POST['appellation']).'",'.
	'bolitembreakout.VINEYARD="'.strtoupper($_POST['vineyard']).'",'.
	'bolitembreakout.PERCENTAGE="'.$_POST['percentage'].'",'.
	'bolitembreakout.BOLITEMSID="'.$_POST['bolitemsid'].'"';
//	echo $query;
	$result=mysql_query($query);
}

if ($_GET['action']=="new")
{
	$query='INSERT INTO bol SET bol.CLIENTCODE="'.$_SESSION['clientcode'].'"';
	$result=mysql_query($query);
	$query='SELECT * FROM bol WHERE bol.ID="'.mysql_insert_id().'"';
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	$_SESSION['bolid']=$row['ID'];
}
if ($_GET['action']=="delitembreakout")
{
	$query='DELETE FROM bolitembreakout WHERE bolitembreakout.ID="'.$_GET['id'].'"';
	$result= mysql_query($query);
}
if ($_GET['action']=="del")
{
	$query='DELETE FROM bolitems WHERE bolitems.ID="'.$_GET['bolitemid'].'"';
	$result= mysql_query($query);
}
if ($_GET['bolid']!='')
$_SESSION['bolid']=$_GET['bolid'];

$query='SELECT bol.ID, BONDED, DATE, DIRECTION, FACILITYID, CLIENTCODE, locations.NAME,locations.ADDRESS1,locations.ADDRESS2,locations.CITY,locations.STATE,
locations.ZIP,locations.BONDNUMBER AS BOND FROM bol LEFT OUTER JOIN locations ON bol.FACILITYID = locations.ID WHERE bol.ID="'.$_SESSION['bolid'].'"';
$result=mysql_query($query);

$row=mysql_fetch_array($result);
$bolid=$row['ID'];
$direction=$row['DIRECTION'];
echo '<table width=100% border="1">';
echo '<tr><td colspan=2 align=center><b><big>STRAIGHT BILL OF LADING';
echo '</b></big><br><a href=hardcopy/bol.php?bolid='.$row['ID'].'>PRINT</td></td></tr>';
echo '<tr><td align="center"><form method=POST action="bolpage.php?action=mod&bolid='.$row['ID'].'">';
echo 'BOL #:'.$row['ID'].'<br>';
if (isstaff()=="YES")
echo 'CLIENT: '.DrawComboFromData("clients","CODE",$row['CLIENTCODE'],"clientcode").'<br>';
else
echo 'CLIENT: '.$row['CLIENTCODE'].'<br>';

echo 'DIRECTION: '.DrawComboFromEnum("bol","DIRECTION",$row['DIRECTION'],"direction").'<br>';
echo 'TYPE: '.DrawComboFromEnum("bol","BONDED",$row['BONDED'],"bonded").'<br>';
echo 'FACILITY: '.DrawComboFromDataWithValue("locations","NAME",$row['FACILITYID'],"facility","ID","LOCATIONTYPE","FACILITY").'<br>';
//echo 'FACILITY: '.DrawComboFromDataWithValue("locations","NAME",$row['FACILITYID'],"facility","ID").'<br>';
echo '<a href=showcalendar.php?returnpage='.$PHP_SELF.'>DATE:</a> ';
if ($_GET['chosendate']=="")
echo '<input size=10 type=textbox name="date" value="'.date("m/d/Y",strtotime($row['DATE'])).'">';
else
echo '<input size=10 type=textbox name="date" value='.$_GET['chosendate'].'>';
echo '<button id="trigger">...</button>';
echo '<br><br>';
echo '<input type=hidden value="'.$row[ID].'" name="bolid">';
echo '<input type=submit value="UPDATE">';
echo '</td>';
echo '<td width=70%>';
// echo '<table width=100%>';
// echo '<tr><td width=30%>BOND #</td><td width=70%><input type=textbox name="bond" value="'.$row['BOND'].'"></td></tr>';
// echo '<tr><td width=30%>NAME</td><td width=70%><input type=textbox name="name" value="'.$row['NAME'].'"></td></tr>';
// echo '<tr><td width=30%>ADDRESS</td><td width=70%><input type=textbox name="address1" value="'.$row['ADDRESS1'].'"></td></tr>';
// echo '<tr><td width=30%>ADDRESS</td><td width=70%><input type=textbox name="address2" value="'.$row['ADDRESS2'].'"></td></tr>';
// echo '<tr><td width=30%>CITY</td><td width=70%><input type=textbox name="city" value="'.$row['CITY'].'"></td></tr>';
// echo '<tr><td width=30%>STATE</td><td width=70%><input type=textbox name="state" value="'.$row['STATE'].'"></td></tr>';
// echo '<tr><td width=30%>ZIP</td><td width=70%><input type=textbox name="zip" value="'.$row['ZIP'].'"></td></tr>';
// echo '</table></tr>';
// echo '</table></form>';
echo '<table width=100%>';
echo '<tr><td width=30%>BOND #</td><td width=70%>'.$row['BOND'].'</td></tr>';
echo '<tr><td width=30%>NAME</td><td width=70%>'.$row['NAME'].'</td></tr>';
echo '<tr><td width=30%>ADDRESS</td><td width=70%>'.$row['ADDRESS1'].'</td></tr>';
echo '<tr><td width=30%>ADDRESS</td><td width=70%>'.$row['ADDRESS2'].'</td></tr>';
echo '<tr><td width=30%>CITY</td><td width=70%>'.$row['CITY'].'</td></tr>';
echo '<tr><td width=30%>STATE</td><td width=70%>'.$row['STATE'].'</td></tr>';
echo '<tr><td width=30%>ZIP</td><td width=70%>'.$row['ZIP'].'</td></tr>';
echo '</table></tr>';
echo '</table></form>';
?>
<script type="text/javascript">
Calendar.setup(
{
	inputField  : "date",  // ID of the input field
	ifFormat    : "%m/%d/%Y",    // the date format
	button      : "trigger"      // ID of the button
}
);
 </script>
 <?php
if ($row['DIRECTION']=="")
	exit;
 if ($_SESSION['clientcode']!="")
 {
 	echo '<table width=100% border="1">';
 	$boldate=$row['DATE'];
 	$query='SELECT * FROM bolitems WHERE bolitems.BOLID="'.$bolid.'"';
 	$result=mysql_query($query);
 	for ($i=0;$i<mysql_num_rows($result);$i++)
 	{
 		$row=mysql_fetch_array($result);
 		$bolitemsid=$row['ID'];
 		if ($direction=="OUT")
 		{
 			echo '<tr><td><a href=bolpage.php?action=del&bolid='.$bolid.'&bolitemid='.$row['ID'].'>del</a></td>';
 			echo '<td><a href=showlotinfo.php?lot='.$row['LOT'].'>'.$row['LOT'].'</a></td>';
 			$record=lotinforecords($row['LOT'],"BOL",$boldate);
 			if ($row['TYPE']=='GRAPES')
 			{
 				echo '<td align=center>'.number_format($row['GALLONS']).' TONS</td>';
 			}
 			else
 			{
 				echo '<td align=center>'.number_format($row['GALLONS']).' GALLONS</td>';
 			}
 			if ($row['TYPE']=='WINE' | $row['TYPE']=='BOTTLED')
 			echo '<td align=center>'.$row['ALC'].'</td>';
 			else
 			echo '<td></td>';
 			echo '<td align=center>'.$row['TYPE'].'</td>';
 			echo '<td align=center>';
 			echo '<table width="100%"><td>';
 			echo showstructure($record[count($record)-2]['structure'],0);
 			//echo showstructure($record,0);
 			echo '</td></table>';
 			echo '</td>';
 			echo '</tr></form>';
 		}
 		else
 		{
 			echo '<tr><td><a href=bolpage.php?action=del&bolid='.$bolid.'&bolitemid='.$row['ID'].'>del</a></td>';
 			echo '<td><a href=showlotinfo.php?lot='.$row['LOT'].'>'.$row['LOT'].'</a></td>';
 			if ($row['TYPE']=='GRAPES')
 			{
 				echo '<td align=center>'.number_format($row['GALLONS']).' TONS</td>';
 			}
 			else
 			{
 				echo '<td align=center>'.number_format($row['GALLONS']).' GALLONS</td>';
 			}
 			if ($row['TYPE']=='WINE')
 			echo '<td align=center>'.$row['ALC'].'</td>';
 			else
 			echo '<td></td>';
 			echo '<td align=center>'.$row['TYPE'].'</td>';
 			echo '<td><table border=1>';
 			$query2='SELECT * FROM bolitembreakout WHERE bolitembreakout.BOLITEMSID="'.$bolitemsid.'"';
 			$result2=mysql_query($query2);
 			$totalpercentage=0;
 			if (mysql_num_rows($result2)>0)
 			{
 				echo '<tr><td width=5%></td>';
 				echo '<td width=5% align=center>%</td>';
 				echo '<td width=20% align=center>VINTAGE</td>';
 				echo '<td width=20% align=center>VARIETY</td>';
 				echo '<td width=30% align=center>APPELLATION</td>';
 				echo '<td width=30% align=center>VINEYARD</td>';
 				echo '</tr>';
 			}
 			for ($i2=0;$i2<mysql_num_rows($result2);$i2++)
 			{
 				$row2=mysql_fetch_array($result2);
 				echo '<tr><td width=5%><a href='.$PHP_SELF.'?action=delitembreakout&id='.$row2['ID'].'>del</a></td>';
 				echo '<td width=5% align=center>'.$row2['PERCENTAGE'].'</td>';
 				echo '<td width=20% align=center>'.$row2['VINTAGE'].'</td>';
 				echo '<td width=20% align=center>'.$row2['VARIETAL'].'</td>';
 				echo '<td width=30% align=center>'.$row2['APPELLATION'].'</td>';
 				echo '<td width=30% align=center>'.$row2['VINEYARD'].'</td>';
 				echo '</tr>';
 				$totalpercentage=$totalpercentage+$row2['PERCENTAGE'];
 			}
 			if ($totalpercentage<100) {
 				echo '<tr><td></td><td></td><td>VINTAGE</td><td>VARIETY</td><td>APPELLATION</td><td>VINEYARD</td></tr>';
 				echo '<form method=POST action='.$PHP_SELF.'?action=additemdetail&bolid='.$bolid.'&bolitemsid='.$bolitemsid.'>';
 				echo '<tr><td></td><td align=center><input type=textbox align=center value='.(100-$totalpercentage).' name="percentage" size=4></td>';
 				echo '<td align=center><input type=textbox align=center name="vintage" size=6></td>';
 				echo '<td align=center><input type=textbox align=center name="variety" size=6></td>';
 				echo '<td align=center><input type=textbox align=center name="appellation" size=30></td>';
 				echo '<td align=center><input type=textbox name="vineyard" size=30></td>';
 				echo '<input type=hidden name="bolitemsid" value="'.$bolitemsid.'">';
 				echo '<input type=hidden name="bolid" value="'.$bolid.'">';
 				echo '<td><input type=submit value="ADD"></td></tr>';
 				echo '</form>';
 				echo '<td></td><td align=center>'.$totalpercentage.'</td>';
 			}
 			echo '</table></tr>';
 		}
 	}
 	echo '<form method=POST action=bolpage.php?action=additem><tr><td></td>';
 	if ($_SESSION['bolitem_lot']=="")
 	{
 		echo '<td align=center>';
 		echo '<a href="javascript:void(0);" onmouseover="setObj(1,350,2000,\'bottomR\',\'#ffffff\',\'#ffffff\',\'Darkred\',\'black\')" onmouseout="timer(20)">';
 		echo 'CHOOSE LOT</a></td>';
 	}
 	else
 	{
 		$li=lotinfo($_SESSION['bolitem_lot']);
 		echo '<td align=center>';
 		echo '<a href="javascript:void(0);" onmouseover="setObj(1,350,2000,\'bottomR\',\'#ffffff\',\'#ffffff\',\'Darkred\',\'black\')" onmouseout="timer(20)">';
 		echo 'LOT: '.$_SESSION['bolitem_lot'].'</a>  ';
 		echo ''.strtoupper($li['DESCRIPTION']);
 		echo '</td>';
 	}
 	$info=lotinforecords($_SESSION['bolitem_lot'],"BOL",$row['DATE']);
 	$info=lastrecordbeforedate($info,strtotime($boldate));
 	//$lr=count($info)-1;
 	$rg=$info['ending_toppinggallons']+$info['ending_bbls']*60+ $info['ending_tankgallons'];
 	//$rg=$info[$lr]['ending_toppinggallons']+$info[$lr]['ending_bbls']*60+ $info[$lr]['ending_tankgallons'];
 	echo '<td align=center><input type=textbox name="gallonstransfered" value="'.$rg.'" size=7></td>';
 	if ($direction=="IN")
 	{
 		echo '<td align=center><input type=text name=alc size=5></td>';
		echo '<td align=center>'.DrawComboFromEnum('bolitems','TYPE','','thetype').'</td>';
 	}
 	else
 	{
 		echo '<td align=center>'.$info['alcohol'].'</td>';
 		echo '<input type=hidden name="alc" value="'.$info['alcohol'].'">';
 		switch ($info['end_state'])
 		{
 			case 'WINE_ABOVE' :
 			echo '<td align=center>WINE</td><input type=hidden name=thetype value=WINE>'; break;
 			case 'WINE_BELOW' :
 			echo '<td align=center>WINE</td><input type=hidden name=thetype value=WINE>'; break;
 			case 'BOTTLED_BELOW' :
 			echo '<td align=center>BOTTLED</td><input type=hidden name=thetype value=BOTTLED>'; break;
 			case 'BOTTLED_ABOVE' :
 			echo '<td align=center>BOTTLED</td><input type=hidden name=thetype value=BOTTLED>'; break;
 			case 'JUICE' :
 			echo '<td align=center>JUICE</td><input type=hidden name=thetype value=JUICE>'; break;
 		}
 	}
 	echo '<input type=hidden name="bolid" value="'.$bolid.'">';
 	echo '<input type=hidden name="bolitem_lot" value="'.$_SESSION['bolitem_lot'].'">';
 	echo '<td><input type=submit value="ADD"></td>';
 	echo '</form></tr>';
 	echo '</table>';
 }
?>
</body>

</html>
