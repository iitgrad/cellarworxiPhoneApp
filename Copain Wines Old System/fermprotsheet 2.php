<?php

session_start();

?>

<html>



<head>

<title>Fermentation Protocol</title>

<link rel="stylesheet" type="text/css" href="../site.css">

    <script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>

      <script type="text/javascript" src="popup/overlibmws.js"></script>

   <script type="text/javascript" src="popup/overlibmws_bubble.js"></script>



<?php



include ("startdb.php");

include ("yesno.php");

include ("setcheck.php");

include ("defaultvalue.php");

include ("manageadditions.php");

include ("queryupdatefunctions.php");

include ("lotinforecords.php");



?>

</head>



<body>



<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div> 

<?php



function thestatus($value)

{

	if (is_null($value))

	  return "CLOSED";

	else

	  return "ACTIVE";

}



if (!is_null($_POST['totalcount']))

{



  $qty=$_POST['totalcount'];

// 	debug($_POST);

  for ($i=0;$i<$qty;$i++)

  {

  	$query='select * from fermprot where fermprot.LOT="'.$_POST['LOT_'.$i].'" and fermprot.VESSELTYPE="'.$_POST['VESSELTYPE_'.$i].'" and fermprot.VESSELID="'.$_POST['VESSELID_'.$i].'"';

  	$result=mysql_query($query);

  	if ($_POST["POAM_".$i]>0) $pdam=""; else $pdam=$_POST["PDAM_".$i];

  	if ($_POST["PONOON_".$i]>0) $pdnoon=""; else $pdnoon=$_POST["PDNOON_".$i];

  	if ($_POST["POPM_".$i]>0) $pdpm=""; else $pdpm=$_POST["PDPM_".$i];

  	if (mysql_num_rows($result)>0)

  	{

  		$query2='update fermprot set fermprot.PDAM="'.$pdam.'",

		fermprot.POAM="'.$_POST["POAM_".$i].'",

		fermprot.PDNOON="'.$pdnoon.'",

		fermprot.PONOON="'.$_POST["PONOON_".$i].'",

		fermprot.PDPM="'.$pdpm.'",

		fermprot.POPM="'.$_POST["POPM_".$i].'", 

		fermprot.STATUS="'.thestatus($_POST["active_".$i]).'"  

  		where fermprot.LOT="'.$_POST['LOT_'.$i].'" and fermprot.VESSELTYPE="'.$_POST['VESSELTYPE_'.$i].'" and fermprot.VESSELID="'.$_POST['VESSELID_'.$i].'"';

  		$result=mysql_query($query2);

  	}

  	else

  	{

  		$query2='insert into fermprot set fermprot.PDAM="'.$pdam.'",

		fermprot.POAM="'.$_POST["POAM_".$i].'",

		fermprot.PDNOON="'.$pdnoon.'",

		fermprot.PONOON="'.$_POST["PONOON_".$i].'",

		fermprot.PDPM="'.$pdpm.'",

		fermprot.POPM="'.$_POST["POPM_".$i].'",  

		fermprot.STATUS="'.thestatus($_POST["active_".$i]).'",  

  		fermprot.LOT="'.$_POST['LOT_'.$i].'",

  		fermprot.VESSELTYPE="'.$_POST['VESSELTYPE_'.$i].'", 

  		fermprot.VESSELID="'.$_POST['VESSELID_'.$i].'"';

  		$result=mysql_query($query2);

  	}

  }

}

  

function showchecked($value)

{

	if ($value=="ACTIVE")

		return "checked";

	else

	    return "";

}



$query='SELECT DISTINCT lots.LOTNUMBER, lots.DESCRIPTION, assets.NAME FROM  lots

  INNER JOIN wo ON (lots.LOTNUMBER = wo.LOT)

  INNER JOIN reservation ON (wo.ID = reservation.WOID)

  INNER JOIN assets ON (reservation.ASSETID = assets.ID)

WHERE

  (lots.CLIENTCODE="'.clientid($_SESSION['clientcode']).'") AND

  (lots.YEAR="'.$_SESSION['vintage'].'") AND

  (assets.TYPEID="6" OR assets.TYPEID="8")';





$result=mysql_query($query);

echo '<form method=post action='.$_SESSION['PHP_SELF'].'>';

echo '<br><br>';

echo '<table border=1 align=center width=100% id=table1>';

echo '<tr><td align=center>ACTIVE</td><td align=center>LOT</td><td align=center>VESSEL</td><td align=center>BRIX/TEMP</td><td colspan=2 align=center>AM</td><td colspan=2 align=center>NOON</td><td colspan=2 	align=center>PM</td></tr>';

echo '<tr><td colspan=4 align=center></td><td align=center>PD</td><td align=center>PO</td><td align=center>PD</td><td align=center>PO</td><td align=center>PD</td><td align=center>PO</td></tr>';

$total=0;



for ($i=0;$i<mysql_num_rows($result);$i++)

{

	$row=mysql_fetch_array($result);

	$desc=$row['DESCRIPTION'];

	$vessel=explode("-",$row['NAME']);

    	$brixtempquery='select * from brixtemp where brixtemp.lot="'.$row['LOTNUMBER'].'" AND brixtemp.vesseltype="'.$vessel[0].'" and brixtemp.vessel="'.$vessel[1].'" order by brixtemp.DATE DESC';

    	$brixtempresult=mysql_query($brixtempquery);

    	$brixtemprow=mysql_query($brixtempresult);

    	if (mysql_num_rows($brixtempresult)>0)

    	{

    		$brixrow=mysql_fetch_array($brixtempresult);

    		$brixtemp=$brixrow['BRIX'].' / '.$brixrow['temp'];

    	}

    	else

    	{

    		$brixtemp='NONE';

    	}

    $total++;

	

    $query2='select * from fermprot where fermprot.LOT="'.$row['LOTNUMBER'].'" AND fermprot.VESSELTYPE="'.$vessel[0].'" and fermprot.VESSELID="'.$vessel[1].'"';	

	$result2=mysql_query($query2);



    if (mysql_num_rows($result2)>0)

    {

    	$row2=mysql_fetch_array($result2);

		if ($row2['STATUS']=="ACTIVE")

		{

			echo '<tr>';

			echo '<td align=center><input type=checkbox name=active_'.$i.' '.showchecked($row2['STATUS']).' value="ACTIVE"></td>';

			echo '<td align=center onmouseover="return overlib(\''.$desc.'\',BUBBLE,BUBBLETYPE,\'quotation\');" 

                onmouseout="nd();"><a href=showlotinfo.php?lot='.$row2['LOT'].'>'.$row2['LOT'].'</a></td><td align=center>'.$row2['VESSELTYPE'].'-'.$row2['VESSELID'].'';

			echo '<input type=hidden name=LOT_'.$i.' value='.$row2['LOT'].'><input type=hidden name=VESSELTYPE_'.$i.' value='.$row2['VESSELTYPE'].'><input type=hidden name=VESSELID_'.$i.' value='.$row2['VESSELID'].'></td>';

			echo '<td align=center><a href=viewfermcurves.php?allowadd=TRUE&lot='.$row2['LOT'].'&vesseltype='.$row2['VESSELTYPE'].'&vessel='.$row2['VESSELID'].'>'.$brixtemp.'</a></td>';

			echo '<td align=center>'.DrawComboFromEnum("fermprot","PDAM",$row2['PDAM'],("PDAM_".$i)).'</td><td align=center><input type=text size=5 name=POAM_'.$i.' value='.$row2['POAM'].'></td>';

			echo '<td align=center>'.DrawComboFromEnum("fermprot","PDNOON",$row2['PDNOON'],("PDNOON_".$i)).'</td><td align=center><input tye=text size=5 name=PONOON_'.$i.' value='.$row2['PONOON'].'></td>';

			echo '<td align=center>'.DrawComboFromEnum("fermprot","PDPM",$row2['PDPM'],("PDPM_".$i)).'</td><td align=center><input type=text size=5 name=POPM_'.$i.' value='.$row2['POPM'].'><input type=hidden name=fermprotid_'.$i.' value='.$row2['id'].'></td>';

			echo '</tr>';

		}

		else

		{

			echo '<tr>';

			echo '<td align=center><input type=checkbox name=active_'.$i.' '.showchecked($row2['STATUS']).' value="ACTIVE"></td><td align=center onmouseover="return overlib(\''.$desc.'\',BUBBLE,BUBBLETYPE,\'quotation\');" 

   	             onmouseout="nd();"><a href=showlotinfo.php?lot='.$row['LOTNUMBER'].'>'.$row['LOTNUMBER'].'</a></td><td align=center>'.$vessel[0].'-'.$vessel[1].'';

			echo '<input type=hidden name=LOT_'.$i.' value='.$row['LOTNUMBER'].'><input type=hidden name=VESSELTYPE_'.$i.' value='.$vessel[0].'><input type=hidden name=VESSELID_'.$i.' value='.$vessel[1].'></td>';

			echo '<td align=center><a href=viewfermcurves.php?allowadd=TRUE&lot='.$row['LOTNUMBER'].'&vesseltype='.$vessel[0].'&vessel='.$vessel[1].'>NONE</a></td>';

			echo '</tr>';

		}

	}

    else

    {

		echo '<tr>';

		echo '<td align=center><input type=checkbox name=active_'.$i.' '.showchecked($row['STATUS']).' value="ACTIVE"></td><td align=center onmouseover="return overlib(\''.$desc.'\',BUBBLE,BUBBLETYPE,\'quotation\');" 

                onmouseout="nd();"><a href=showlotinfo.php?lot='.$row['LOTNUMBER'].'>'.$row['LOTNUMBER'].'</a></td><td align=center>'.$vessel[0].'-'.$vessel[1].'';

		echo '<input type=hidden name=LOT_'.$i.' value='.$row['LOTNUMBER'].'><input type=hidden name=VESSELTYPE_'.$i.' value='.$vessel[0].'><input type=hidden name=VESSELID_'.$i.' value='.$vessel[1].'></td>';

		echo '<td align=center><a href=viewfermcurves.php?allowadd=TRUE&lot='.$row['LOTNUMBER'].'&vesseltype='.$vessel[0].'&vessel='.$vessel[1].'>NONE</a></td>';

		echo '<td align=center>'.DrawComboFromEnum("fermprot","PDAM","",("PDAM_".$i)).'</td><td align=center><input type=text size=5 name=POAM_'.$i.' ></td>';

		echo '<td align=center>'.DrawComboFromEnum("fermprot","PDNOON","",("PDNOON_".$i)).'</td><td align=center><input type=text size=5 name=PONOON_'.$i.' ></td>';

		echo '<td align=center>'.DrawComboFromEnum("fermprot","PDPM","",("PDPM_".$i)).'</td><td align=center><input type=text size=5 name=POPM_'.$i.' ><input type=hidden name=fermprotid_'.$i.' value='.$row['id'].'></td>';

		echo '</tr>';

    }

}





echo '</table>';



echo '<tr><td align=center><input type=submit value=SUBMIT><input type=hidden name=totalcount value='.$total.'></td></tr>';

echo '</table>';

echo '</form>';

?>

<script language="JavaScript">

<!--

tigra_tables('table1', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

// -->

            </script>

<?php





?>



</form>



</body>



</html>