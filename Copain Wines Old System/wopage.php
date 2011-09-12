<?php

session_start();

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");

include ("totalgallons.php");

include ("lotinforecords.php");

//echo '<pre>';
//print_r($_POST);
//exit;

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

  function updatewoid(woid) {

    opener.document.addprogram.newwoid.value=woid;

  }

</script>

<script language="JavaScript1.2">

<!-- Browser Check -->

iens6=document.all||document.getElementByIdf

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

require_once('../json/JSON.php');

define('APPKEY','HVHWF4NpRHGvMHrsyEkWuA'); 
define('PUSHSECRET', 'RePn8gAMR52zp5HUChw7Qg'); 

define('APPKEYDEV','TMXhdpjcSlmKCZO3zkyrpg'); 
define('PUSHSECRETDEV', 'dUCudu5nQAO2svq2Awmqng'); 
define('PUSHURL', 'https://go.urbanairship.com/api/push/'); 

	$json = new Services_JSON();
//	$record=$_REQUEST;
function sendPush($devtokens,$message,$badgeCount,$sound)
{
	// The device aliases you want to send to 
	$aliases =  array('steven'); 
	//device tokens 
	//$devices = array("8EF9E24E34DE5659BDDAC35CFA09DB8E8A04E0DF6C0F94AC953BB58EA4D4666D");
	$devices=$devtokens;
	$contents = array(); 
	$contents['badge'] = (int)$badgeCount; 
	$contents['alert'] = $message; 
	$contents['sound'] = $sound; 
	$push = array("aps" => $contents); 
	// if ($aliases) 
	//    $push["aliases"] = $aliases; 
	if ($devices) 
	   $push["device_tokens"] = $devices; 
	// echo '<pre>';
	// print_r($push);
	// exit;
	$json = json_encode($push); 
	$session = curl_init(PUSHURL); 
	curl_setopt($session, CURLOPT_USERPWD, APPKEY . ':' . PUSHSECRET); 
	curl_setopt($session, CURLOPT_POST, True); 
	curl_setopt($session, CURLOPT_POSTFIELDS, $json); 
	curl_setopt($session, CURLOPT_HEADER, False); 
	curl_setopt($session, CURLOPT_RETURNTRANSFER, True); 
	curl_setopt($session, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
	curl_exec($session); 
	// Check if any error occured 
	$response = curl_getinfo($session); 
	curl_close($session);
	// unset($session);
	// $session = curl_init(PUSHURL); 
	// curl_setopt($session, CURLOPT_USERPWD, APPKEYDEV . ':' . PUSHSECRETDEV); 
	// curl_setopt($session, CURLOPT_POST, True); 
	// curl_setopt($session, CURLOPT_POSTFIELDS, $json); 
	// curl_setopt($session, CURLOPT_HEADER, False); 
	// curl_setopt($session, CURLOPT_RETURNTRANSFER, True); 
	// curl_setopt($session, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
	// curl_exec($session); 
	// // Check if any error occured 
	// $response = curl_getinfo($session); 
	// curl_close($session);		
}

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



$query='SELECT * from lots inner join clients on (lots.CLIENTCODE=clients.CLIENTID) where ((lots.YEAR="'.$_SESSION['vintage'].'") AND

     (clients.CODE="'.$_SESSION['clientcode'].'"))';
echo $query;

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

</head>

<?php

if ($_SESSION['woid']!="")

{

    $wo=getwo($_SESSION['woid']);

}





echo '<body onClick="stopIt()">';



echo '<body onLoad="javascript:updatewoid(\''.$wo['id'].'\')">';



?>

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



function resetvalues()

{

    //  $_SESSION['lot']="";

    $_SESSION['dateofwork']="";

    $_SESSION['enddate']="";

    $_SESSION['morning']="NO";

    $_SESSION['noon']="NO";

    $_SESSION['evening']="NO";

    $_SESSION['status']="ASSIGNED";

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

    $_SESSION['workperformedby']="CCC";

    $temp = clientinfo($_SERVER['PHP_AUTH_USER']);

    //     $_SESSION['clientcode']=$temp['code'];

}





if (isset($_GET['lot'])) $_SESSION['lot']=$_GET['lot'];

if (isset($_GET['woid'])) $_SESSION['woid']=$_GET['woid'];

if (isset($_GET['action'])) $_SESSION['currentaction']=$_GET['action'];





//$creationdate=date('M d, Y h:i A');



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

//    echo '<pre>';
//    print_r($row);

    $_SESSION['creationdate']=$row['CREATIONDATE'];

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

	if (($row['LOT']!="---") & ($row['LOT']!=""))
	{
		$parse=split("-",$row['LOT']);
		$_SESSION['vintage']=$parse[0]+2000;		
	}
    $_SESSION['lot']=$row['LOT'];

    $_SESSION['type']=$row['TYPE'];

    $_SESSION['clientcode']=$row['CLIENTCODE'];

    $_SESSION['requestor']=$row['REQUESTOR'];

    $_SESSION['lastmodifieddatetime']=$row['LASTMODIFIEDDATETIME'];

    

    $_SESSION['currentaction']="";

}



if ($_SESSION['currentaction']=="view")

{

    $_SESSION['assettypeid']='';

    $_SESSION['assetid']='';

    

    doview();

}



if ($_GET['action']=="new")

{

    resetvalues();

    $_SESSION['lot']=$_GET['lot'];

    $_SESSION['requestor']=strtoupper($_SERVER['PHP_AUTH_USER']);

    $_SESSION['dateofwork']=date("Y-m-d",time()).' 00:00:00';

    $_SESSION['enddate']=date("Y-m-d",time()).' 00:00:00';

//    echo 'set date of work to '.$_SESSION['dateofwork'];

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

    $query='DELETE FROM wo WHERE wo.ID='.$_GET['woid'].' limit 1';

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

    $the_type=$_POST['activity'];

    $_SESSION['type']=$the_type;

    

    $_SESSION['desc']=$_POST['comments'];

    $_SESSION['workperformedby']=$_POST['workperformedby'];

    $_SESSION['completioncomments']=$_POST['completioncomments'];

    $_SESSION['endingtankgallons']=$_POST['endingtankgallons'];

    $_SESSION['endingbarrelcount']=$_POST['endingbarrelcount'];

    $_SESSION['endingtoppinggallons']=$_POST['endingtoppinggallons'];

    $_SESSION['status']=$_POST['status'];

    

    if (strtotime($_POST['enddate'])<strtotime($_POST['dateofwork']))

    $theenddate=$_POST['dateofwork'];

    else

    $theenddate=$_POST['enddate'];

    

    $thevessel=explode('-',$_POST['vessel']);

    if (isstaff()=="YES")

    {

    $query='UPDATE wo SET

        wo.TYPE="'.$_SESSION['type'].'",

        wo.VESSELTYPE="'.$thevessel[0].'",

        wo.VESSELID="'.$thevessel[1].'",

        wo.DUEDATE="'.datetimestr($_POST['dateofwork'],$_POST['startslot'],'START').'",

        wo.ENDDATE="'.datetimestr($theenddate,$_POST['endslot'],'END').'",

        wo.ENDSLOT="'.$_POST['endslot'].'",

        wo.STRENGTH="'.$_POST['strength'].'",

        wo.DURATION="'.$_POST['duration'].'",

        wo.TIMESLOT="'.$_POST['timeslot'].'",

        wo.RELATEDADDITIONSID="'.$_POST['mapid'].'",

        wo.DELETED=0,

        wo.STATUS="'.$_POST['status'].'",

        wo.LOT="'.$_POST['lot'].'",

        wo.MORNING="'.$_POST['morning'].'",

        wo.NOON="'.$_POST['noon'].'",

        wo.EVENING="'.$_POST['evening'].'",

        wo.WORKPERFORMEDBY="'.$_SESSION['workperformedby'].'",

        wo.WORKAREAID="'.$_SESSION['workareaid'].'",

        wo.OTHERDESC="'.strtoupper($_SESSION['desc']).'",

        wo.COMPLETEDDESCRIPTION="'.strtoupper($_SESSION['completioncomments']).'",

        wo.ENDINGTANKGALLONS="'.$_SESSION['endingtankgallons'].'",

        wo.ENDINGBARRELCOUNT="'.$_SESSION['endingbarrelcount'].'",

        wo.ENDINGTOPPINGGALLONS="'.$_SESSION['endingtoppinggallons'].'",

        wo.AUTOGENERATED="NO",

        wo.LASTMODIFIEDDATETIME="'.date("Y-m-d h:i a",time()).'",

        wo.CLIENTCODE="'.$_POST['clientcode'].'"

        WHERE (wo.ID="'.$_SESSION['woid'].'")';
//	echo $query;

    $result=mysql_query($query);

    }

    else

    {

//    	if ($_POST['status']!="COMPLETED")

 //   	{

    $query='UPDATE wo SET

        wo.TYPE="'.$_SESSION['type'].'",

        wo.VESSELTYPE="'.$thevessel[0].'",

        wo.VESSELID="'.$thevessel[1].'",

        wo.DUEDATE="'.datetimestr($_POST['dateofwork'],$_POST['startslot'],'START').'",

        wo.ENDDATE="'.datetimestr($theenddate,$_POST['endslot'],'END').'",

        wo.ENDSLOT="'.$_POST['endslot'].'",

        wo.STRENGTH="'.$_POST['strength'].'",

        wo.DURATION="'.$_POST['duration'].'",

        wo.TIMESLOT="'.$_POST['timeslot'].'",

        wo.RELATEDADDITIONSID="'.$_POST['mapid'].'",

        wo.DELETED=0,

        wo.LOT="'.$_POST['lot'].'",

        wo.MORNING="'.$_POST['morning'].'",

        wo.NOON="'.$_POST['noon'].'",

        wo.EVENING="'.$_POST['evening'].'",

        wo.WORKPERFORMEDBY="'.$_SESSION['workperformedby'].'",

        wo.WORKAREAID="'.$_SESSION['workareaid'].'",

        wo.OTHERDESC="'.strtoupper($_SESSION['desc']).'",

        wo.ENDINGTANKGALLONS="'.$_SESSION['endingtankgallons'].'",

        wo.ENDINGBARRELCOUNT="'.$_SESSION['endingbarrelcount'].'",

        wo.ENDINGTOPPINGGALLONS="'.$_SESSION['endingtoppinggallons'].'",

        wo.AUTOGENERATED="NO",

        wo.LASTMODIFIEDDATETIME="'.date("Y-m-d h:i a",time()).'",

        wo.CLIENTCODE="'.$_POST['clientcode'].'"

        WHERE (wo.ID="'.$_SESSION['woid'].'")';

    $result=mysql_query($query);

 //   	}

    }

     //   echo $query;

    

    if ($_POST['activity']=="ADDITION")

    {

        $addquery='update fpaddmap set DATE="'.date("Y-m-d",strtotime($_POST['dateofwork'])).'" WHERE ID="'.$_POST['mapid'].'"';

        mysql_query($addquery);

        

        $addquery='update additions set

          SUPERFOODAMT="'.$_POST['sf'].'",

          DAPAMOUNT="'.$_POST['dap'].'",

          HTAAMOUNT="'.$_POST['hta'].'",

          GOAMOUNT="'.$_POST['go'].'",

          WATERAMOUNT="'.$_POST['water'].'",

          INNOCULATIONBRAND="'.$_POST['innocbrand'].'",

          INNOCULATIONAMOUNT="'.$_POST['innocamt'].'" WHERE ID="'.$_POST['addid'].'"';

          //echo $addquery;

        mysql_query($addquery);

    }

    doview();



    $wo=getwo($_SESSION['woid']);



    

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

    if ($_GET['assetid']!="")

    {

        $query='INSERT into reservation SET ASSETID="'.$_GET['assetid'].'", WOID="'.$_SESSION['woid'].'"';

        // echo $query;

        mysql_query($query);

    }

    $_SESSION['assetid']="";

    $_SESSION['assettypeid']="";

    

    doview();

}



if($_GET['action']=="add")

{

    $the_type=$_POST['activity'];

    $_SESSION['type']=$the_type;

    $_SESSION['desc']=$_POST['comments'];

    $_SESSION['workperformedby']=$_POST['workperformedby'];

    $_SESSION['endingtankgallons']=$_POST['endingtankgallons'];

    $_SESSION['endingbarrelcount']=$_POST['endingbarrelcount'];

    $_SESSION['endingtoppinggallons']=$_POST['endingtoppinggallons'];

    $_SESSION['status']=$_POST['status'];

        

    $thevessel=explode('-',$_POST['vessel']);

    

    $query='INSERT INTO wo SET

        wo.TYPE="'.$the_type.'",

        wo.MORNING="'.$_POST['morning'].'",

        wo.NOON="'.$_POST['noon'].'",

        wo.EVENING="'.$_POST['evening'].'",

         wo.VESSELTYPE="'.$thevessel[0].'",

        wo.VESSELID="'.$thevessel[1].'",

       wo.DUEDATE="'.datetimestr($_POST['dateofwork'],$_POST['startslot'],'START').'",

        wo.ENDDATE="'.datetimestr($_POST['enddate'],$_POST['endslot'],'END').'",

        wo.STARTSLOT="'.$_POST['startslot'].'",

        wo.ENDSLOT="'.$_POST['endslot'].'",

        wo.LOT="'.$_POST['lot'].'",

        wo.ASSIGNEDTO="'.$settoassigned.'",

        wo.REQUESTOR="'.$_SESSION['requestor'].'",

        wo.DELETED=0,

        wo.ENDINGTANKGALLONS="'.$_SESSION['endingtankgallons'].'",

        wo.ENDINGBARRELCOUNT="'.$_SESSION['endingbarrelcount'].'",

        wo.ENDINGTOPPINGGALLONS="'.$_SESSION['endingtoppinggallons'].'",

        wo.COMPLETEDDESCRIPTION="'.$_SESSION['completioncomments'].'",

        wo.FERMPROTID="'.$_POST['fermprotid'].'",

        wo.OTHERDESC="'.$_SESSION['desc'].'",

        wo.WORKPERFORMEDBY="'.$_SESSION['workperformedby'].'",

        wo.STATUS="'.$_SESSION['status'].'",

        wo.AUTOGENERATED="NO",

        wo.CREATIONDATE="'.date("Y-m-d").'",

        wo.CLIENTCODE="'.$_SESSION['clientcode'].'"';

    //      echo $query;

    $result=mysql_query($query);
    $_SESSION['woid']=mysql_insert_id();
	
	$devtokens[]="b92792d16bc3cb231ecf6a9b623e985e45fda964de52338c7ed414a073fc21c6";
	$devtokens[]="206d0b7d2b7968caccd38c86cd69040d2488acdccbe76224357a514cb286cf9c";
	$message=$the_type." Work Order (".$_SESSION['woid'].") added by ".$_SESSION['clientcode'];	
	$badgeCount=0;
	$sound="default";
	
	sendPush($devtokens,$message,$badgeCount,$sound);

    

 
    //     assign_wo_to_reservations($_SESSION['dateofwork'],$_SESSION['clientcode'],$_SESSION['woid']);

    $_SESSION['currentaction']='add';

    $_SESSION['currentaction']='view';

    doview();

    //resetvalues();

}

if ($_SESSION['woid']!="")

{

    $wo=getwo($_SESSION['woid']);

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

$_SESSION['requestor']=$_SERVER['PHP_AUTH_USER'];



$_SESSION['returnpage']=$PHP_SELF;



if ($_SESSION['woid']!="")

{

    echo '<form  name=main method="POST" action="'.$PHP_SELF.'?action=mod">';

}

else

{

    echo '<form  name=main method="POST" action="'.$PHP_SELF.'?action=add">';

}

//--STARTS LEFT TABLE

echo '<table width=100%><tr><td align=center width=25%>';

echo '<table border=1 align="center" width="100%" border="0">';

echo '<tr>';

echo '<td valign=top align="center">';

echo '<b><big><a href='.$PHP_SELF.'?action=view>WO ID: '.$_SESSION['woid'].'</a></b></big>';

echo '<br><br><a href=hardcopy/wo.php?woid='.$_SESSION['woid'].'>PRINT</a><br><br>';

echo 'CREATION DATE: '.date("m/d/Y",strtotime($_SESSION['creationdate'])).'<br><br>';

//echo 'LAST MODIFIED DATE: '.$_SESSION['lastmodifieddatetime'].'<br><br>';

echo '<a href=filemgt.php?linktype=WO&linkid='.$_SESSION['woid'].'>ATTACHED FILES</a><br>';

//if (isstaff()=="YES")

//{



if ($_GET['fermprotid']!="")

{

    echo '<input type=hidden value='.$_GET['fermprotid'].' name=fermprotid>';

    echo '<input type=hidden value='.$_GET['thevessel'].' name=vessel>';

}

else

echo '<input type=hidden value='.$wo['vesseltype'].'-'.$wo['vesselid'].'name=vessel>';



if ($_GET['fermprotid']!="" | $_SESSION['status']=="TEMPLATE")

{

    echo '<br>STATUS: TEMPLATE';

    echo '<br><a href=javascript:updatewoid(\''.$wo['id'].'\')>UPDATE</a>';

    $_SESSION['status']="TEMPLATE";

    echo '<input type=hidden name=status value=TEMPLATE>';

    

}

else

echo '<br>STATUS: '.DrawComboFromEnum("wo","STATUS", $_SESSION['status'],"status");

//}

//else

//{

//  echo '<br>STATUS: '.$_SESSION['status'];

//}

echo '<tr><td align=center>';

if ($_SESSION['status']!="TEMPLATE")

{

    echo '<table border=1 width=100% align=center>';

    echo '<tr><td width=100% align=center>START:';

//    echo $_SESSION['dateofwork'].'--';

//    echo date("m-d-y",strtotime($_SESSION['dateofwork']));

    echo '<input type="text" name="dateofwork" size="12" value="'.date("m/d/Y",strtotime($_SESSION['dateofwork'])).'">';

    echo '<button id="trigger">...</button>';

    echo '</td>';

    //echo '<td width=30% align=center>';

    //echo DrawComboFromEnum('wo','STARTSLOT',$_SESSION['startslot'],'startslot');

    //echo '</td>';

    echo '</tr><td align=center>END:';

 //   echo date("m-d-Y",strtotime($enddate));

    echo '<input type="text" name="enddate" size="12" value="'.date("m/d/Y",strtotime($_SESSION['enddate'])).'">';

    echo '<button id="trigger2">...</button>';

    //echo '</td><td align=center>';

    //echo DrawComboFromEnum('wo','ENDSLOT',$_SESSION['endslot'],'endslot');

    //echo '</td>';

    echo '</tr>';

    echo '</table><br>';

}

if (isstaff()=="YES")

{

    //pic ($_SESSION['clientcode'],FALSE,listallclientcodes(),$PHP_SELF.'?action=setclient',"clientcode",$PHP_SELF.'?action=clearclient',"CLIENT: ");

    echo "CLIENT: ".DrawComboFromData("clients","CODE",strtoupper($_SESSION['clientcode']),"clientcode").'<br>';

}

else

{

    echo 'CLIENT: '.strtoupper($_SESSION['clientcode']);

    echo '<input type=hidden value='.strtoupper($_SESSION['clientcode']).' name=clientcode>';

}

//echo '<br>REQUESTOR: '.strtoupper($_SESSION['requestor']).'<br><br>';

echo '<br>REQUESTOR: '.strtoupper($_SESSION['requestor']).'<br><br>';

if ($_SESSION['status']!="TEMPLATE")

{
 	    echo '<a href=showlotinfo.php?lot='.$_SESSION['lot'].'>LOT HISTORY</a>       ';

	    echo '<input type=hidden value='.$_SESSION['lot'].' name=lot>';

	    if ($_GET['lot']!="")

	    echo 'LOT: '.DrawComboForLots($_GET['lot'],$_SESSION['vintage'],"lot",strtoupper($_SESSION['clientcode']));

	    else

	    echo 'LOT: '.DrawComboForLots($wo['lot'],$_SESSION['vintage'],"lot",strtoupper($_SESSION['clientcode']));
		echo '<br><br>';
		echo '<a href=vintagesummary.php?clientcode='.$_SESSION['clientcode'].'&vintage='.$_SESSION['vintage'].'>VINTAGE SUMMARY</a>';
}

else

{

    echo 'LOT: ';

    if ($_GET['lot']!="")

    {

        echo $_GET['lot'];

        echo '<input type=hidden name=lot value='.$_GET['lot'].'>';

    }

    else

    {

        echo $wo['lot'];

        echo '<input type=hidden name=lot value='.$wo['lot'].'>';

    }

}

echo '</tr>';



echo '<tr>';

if ($_SESSION['woid']!="")

{

//	if (isstaff()=="YES")

    echo '<td align=center><br><br><a href='.$PHP_SELF.'?action=del&woid='.$_SESSION['woid'].'>DELETE WORK ORDER</a>';

}

else

{

    echo '<td></td>';

}

echo '<tr>';

echo '</table>';



echo '</td><td width=70% align=center>';



//--ENDS LEFT TABLE



echo '<table border="1" width=100%><tr><td align="center">WORK PERFORMED BY </td><td>';

echo DrawComboFromEnum("wo","WORKPERFORMEDBY", $_SESSION['workperformedby'],"workperformedby");

echo '</td><td>WORK TO BE PERFORMED:</td><td>';

//echo DrawComboFromEnum("wo","TYPE", $_SESSION['type'],"activity");
$wolist['']=1;
$wolist['ADDITION']=1;
$wolist['BBL DOWN']=1;
$wolist['BLEED OFF']=1;
$wolist['BLENDING']=1;
$wolist['BOL']=1;
$wolist['BOTTLING']=1;
$wolist['DRYICE']=1;
$wolist['DEMP']=1;
$wolist['FILTRATION']=1;
$wolist['HEAT TANK']=1;
$wolist['LAB TEST']=1;
$wolist['OTHER']=1;
$wolist['PRESSOFF']=1;
$wolist['PULL SAMPLE']=1;
$wolist['RACKING']=1;
$wolist['SCP']=1;
$wolist['SETTLING']=1;
$wolist['TOPPING']=1;

echo DrawComboFromArray($wolist, $_SESSION['type'],"activity");

echo '</td>';



if ($_SESSION['woid']!="")

{

    echo '<td align=center>';

    

    switch ($_SESSION['type'])

    {

        case "LAB TEST" : echo '<a href=labtest.php?woid='.$_SESSION['woid'].'>LAB SHEET</a>'; break;

        case "BOTTLING" : echo '<a href=bottlingreport.php?woid='.$_SESSION['woid'].'>BOTTLING REPORT</a>'; break;

        case "BBL DOWN" :

        {

            echo '<a href=presssheet.php?woid='.$_SESSION['woid'].'>BARREL OPERATIONS</a>';

            break;

        }

        case "PRESSOFF" :

        {

            echo '<a href=presspage.php?woid='.$_SESSION['woid'].'>PRESS SHEET</a><br>';

            echo '<a href=presssheet.php?woid='.$_SESSION['woid'].'>BARREL OPERATIONS</a>';

            break;

        }

        case "BLENDING" :

        {

            echo '<a href=blend.php?woid='.$_SESSION['woid'].'>BLENDING BREAKDOWN</a><br>';

            echo '<a href=barrelsinblend.php?woid='.$_SESSION['woid'].'>BARREL OPERATIONS</a>'; break;

        }

        case "BLEEDOFF" :

        {

            echo '<a href=blend.php?woid='.$_SESSION['woid'].'>BLENDING BREAKDOWN</a><br>';

            echo '<a href=presssheet.php?woid='.$_SESSION['woid'].'>BARREL OPERATIONS</a>';

            break;

        }

        case "OTHER" : echo '<a href=barrelhistory.php?woid='.$_SESSION['woid'].'>BARREL OPERATIONS</a>'; break;

        case "TOPPING" : echo '<a href=barrelhistory.php?woid='.$_SESSION['woid'].'>BARREL OPERATIONS</a>'; break;

        case "RACKING" : echo '<a href=barrelhistory.php?woid='.$_SESSION['woid'].'>BARREL OPERATIONS</a>'; break;

        case "SCP" : echo '<a href=scppage.php?woid='.$_SESSION['woid'].'>SCP</a>'; break;

        default : echo '---'; break;

    }

    echo'</td></tr></table>';

    switch ($wo['type'])

    {

        case "DRYICE" :

        {

            echo '<table>';

            echo '<tr>';

            echo '<td align=center>';

            echo 'VESSEL';

            echo '</td>';

            echo '<td align=center>';

            echo 'TIMESLOT';

            echo '</td>';

            echo '</tr>';

            echo '<tr>';

            echo '<td align=center>';

            if ($_SESSION['status']=="TEMPLATE")

                echo DrawComboForTanks($wo['vesseltype'].'-'.$wo['vesselid'],$wo['lot'],"vessel");

            else

            {

                echo $wo['vesseltype'].'-'.$wo['vesselid'];

                echo '<input type=hidden name=vessel value='.$wo['vesseltype'].'-'.$wo['vesselid'].'>';

            }

            echo '</td>';

            echo '<td align=center>';

            echo DrawComboFromEnum("wo","TIMESLOT",$wo['timeslot'],"timeslot");

            echo '</td>';

            echo '</tr>';

            echo '</table>';

            break;

        }

        case "PUNCH DOWN" :

        {

            echo '<table>';

            echo '<tr>';

            echo '<td align=center>';

            echo 'VESSEL';

            echo '</td>';

            echo '<td align=center>';

            echo 'TIMESLOT';

            echo '</td>';

            echo '<td align=center>';

            echo 'STRENGTH';

            echo '</td>';

            echo '</tr>';

            echo '<tr>';

            echo '<td align=center>';

            if ($_SESSION['status']!="TEMPLATE")

            echo DrawComboForTanks($wo['vesseltype'].'-'.$wo['vesselid'],$wo['lot'],"vessel");

            else

            {

                echo $wo['vesseltype'].'-'.$wo['vesselid'];

                echo '<input type=hidden name=vessel value='.$wo['vesseltype'].'-'.$wo['vesselid'].'>';

            }

            echo '</td>';

            echo '<td align=center>';

            echo DrawComboFromEnum("wo","TIMESLOT",$wo['timeslot'],"timeslot");

            echo '</td>';

            echo '<td align=center>';

            echo DrawComboFromEnum("wo","STRENGTH",$wo['strength'],"strength");

            echo '</td>';

            echo '</tr>';

            echo '</table>';

            break;

        }

        case "PUMP OVER" :

        {

            echo '<table>';

            echo '<tr>';

            echo '<td align=center>';

            echo 'VESSEL';

            echo '</td>';

            echo '<td align=center>';

            echo 'TIMESLOT';

            echo '</td>';

            echo '<td align=center>';

            echo 'DURATION';

            echo '</td>';

            echo '</tr>';

            echo '<tr><td align=center>';

            if ($_SESSION['status']!="TEMPLATE")

            echo DrawComboForTanks($wo['vesseltype'].'-'.$wo['vesselid'],$wo['lot'],"vessel");

            else

            {

                echo $wo['vesseltype'].'-'.$wo['vesselid'];

                echo '<input type=hidden name=vessel value='.$wo['vesseltype'].'-'.$wo['vesselid'].'>';

            }

            echo '</td>';

            echo '<td align=center>';

            echo DrawComboFromEnum("wo","TIMESLOT",$wo['timeslot'],"timeslot");

            echo '</td>';

            echo '<td align=center>';

            echo '<input type=text  size=5 name=duration value='.$wo['duration'].'>';

            echo '</td>';

            echo '</tr></table>';

            break;

        }

        case "ADDITION" :

        {

            $fpquery='select ID from fermprot where LOT="'.$wo['lot'].'" and VESSELTYPE="'.$wo['vesseltype'].'" AND VESSELID="'.$wo['vesselid'].'"';

            $fpresult=mysql_query($fpquery);

            if (mysql_num_rows($fpresult)==0)

            {

                echo '<table>';

                echo '<tr>';

                echo '<td align=center>';

                echo 'VESSEL';

                echo '</td></tr>';

                $fpquery='select * from fermprot where LOT="'.$wo['lot'].'"';

                $fpresult=mysql_query($fpquery);

                for ($fpi=0;$fpi<mysql_num_rows($fpresult);$fpi++)

                {

                    $fprow=mysql_fetch_array($fpresult);

                    $vessels[$fprow['VESSELTYPE'].'-'.$fprow['VESSELID']]=0;

                }

                echo '<tr><td align=center>';

                if ($_SESSION['status']!="TEMPLATE")

                echo DrawComboForTanks($wo['vesseltype'].'-'.$wo['vesselid'],$wo['lot'],"vessel");

                else

                {

                    echo $wo['vesseltype'].'-'.$wo['vesselid'];

                    echo '<input type=hidden name=vessel value='.$wo['vesseltype'].'-'.$wo['vesselid'].'>';

                }

                echo '</td></tr>';

            }

            else{

                $fprow=mysql_fetch_array($fpresult);

                $fpid=$fprow['ID'];

                echo '<table border=>';

                echo '<tr>';

                echo '<td align=center>';

                echo 'VESSEL';

                echo '</td>';

                echo '<td align=center>';

                echo 'SF';

                echo '</td>';

                echo '<td align=center>';

                echo 'DAP';

                echo '</td>';

                echo '<td align=center>';

                echo 'HTA';

                echo '</td>';

                echo '<td align=center>';

                echo 'GO';

                echo '</td>';

                echo '<td align=center>';

                echo 'H20';

                echo '</td>';

                echo '<td align=center>';

                echo 'INOC WITH';

                echo '</td>';

                echo '<td align=center>';

                echo 'INNOC AMOUNT';

                echo '</td>';

                echo '</tr>';

                

                

                $addquery='select * from fpaddmap where ID="'.$wo['relatedadditionsid'].'"';

                $addresult=mysql_query($addquery);

                if (mysql_num_rows($addresult)==0)

                {

                    $addquery='insert into additions set brix=""';

                    $addresult=mysql_query($addquery);

                    $addid=mysql_insert_id();

                    $addquery='insert into fpaddmap set FERMPROTID="'.$fpid.'", ADDITIONID="'.$addid.'"';

                    $addresult=mysql_query($addquery);

                    $mapid=mysql_insert_id();

                    

                    $addquery='select * from additions where ID="'.$addid.'"';

                    $addresult=mysql_query($addquery);

                }

                else

                {

                    $maprow=mysql_fetch_array($addresult);

                    $mapid=$maprow['ID'];

                }

                $addquery='select * from fpaddmap inner join additions on (fpaddmap.ADDITIONID=additions.ID) where fpaddmap.ID="'.$wo['relatedadditionsid'].'"';

                $addresult=mysql_query($addquery);

                $add=mysql_fetch_array($addresult);

                echo '<tr>';

                echo '<input type=hidden name=addid value='.$add['ADDITIONID'].'>';

                echo '<input type=hidden name=mapid value='.$mapid.'>';

                $fpquery='select * from fermprot where LOT="'.$wo['lot'].'"';

                $fpresult=mysql_query($fpquery);

                for ($fpi=0;$fpi<mysql_num_rows($fpresult);$fpi++)

                {

                    $fprow=mysql_fetch_array($fpresult);

                    $vessels[$fprow['VESSELTYPE'].'-'.$fprow['VESSELID']]=0;

                }

                echo '<td align=center>';

                echo DrawComboFromArray($vessels,($wo['vesseltype'].'-'.$wo['vessselid']),"vessel");

                echo '</td>';

                echo '<td align=center><input name=sf type=text size=4 value='.$add['SUPERFOODAMT'].'></td>';

                echo '<td align=center><input name=dap type=text size=4 value='.$add['DAPAMOUNT'].'></td>';

                echo '<td align=center><input name=hta type=text size=4 value='.$add['HTAAMOUNT'].'></td>';

                echo '<td align=center><input name=go type=text size=4 value='.$add['GOAMOUNT'].'></td>';

                echo '<td align=center><input name=water type=text size=4 value='.$add['WATERAMOUNT'].'></td>';

                echo '<td align=center><input name=innocbrand type=text size=4 value='.$add['INNOCULATIONBRAND'].'></td>';

                echo '<td align=center><input name=innocamt type=text size=4 value='.$add['INNOCULATIONAMOUNT'].'></td>';

                echo '</tr>';

                echo '</table>';

            }

            break;

        }

    }

    if ($wo['type']!="LAB TEST" & $wo['type']!="ADDITION" & $wo['type']!="DRYICE" & $wo['type']!="PUMP OVER" & $wo['type']!="PUNCH DOWN")

    {

        // echo '<table border=1 id=res align=center width=100%>';
        // 
        // echo '<tr><td colspan=3 align=center><b><big>RESERVED ASSETS</b></big></td></tr>';
//--------------------xxxx
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


    echo '</tr>';

    echo '</table>';

}

    echo '</table>';

    }

echo '<table border=1 width=100%>';

echo '<tr><td align=center width=15%>';

echo 'COMMENTS</td><td width="35%"><textarea rows="5" cols="60" name="comments">'.$_SESSION['desc'].'</textarea></td></tr>';

echo '<tr><td width="15%" align="center">';

echo 'COMPLETION COMMENTS</td><td width="35%"><textarea rows="5" cols="60" name="completioncomments">'.$_SESSION['completioncomments'].'</textarea>';



echo '</td></tr></table>';


//echo $_SESSION['lot'];

if ($_SESSION['lot']!="---" & $_SESSION['woid']!="")

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
    
 //   echo $_SESSION['lot'];
    if ($_SESSION['lot']!="---")
	{
    	echo showstructure($records[count($records)-1]['structure']);
	//	echo '<pre>'; print_r($records);
	}
    

    echo '</td>';

    echo '</table>';

}



if ($_SESSION['woid']!="")

{

echo '<tr><td colspan=2 align=center><input type="submit" value="UPDATE WORKORDER" name="B1">';

echo '</td></tr>';

}

else

echo '<tr><td colspan=2 align=center><input type="submit" value="ADD WORKORDER" name="B1"></td></tr>';



echo '</table>';

echo '<tr>';

echo '</td></table>';

echo '<table border="1" width="100%">';

echo '</td></tr></table>';



echo '</form>';

?>

</table>

</body>



</html>