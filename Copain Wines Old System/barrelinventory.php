<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

</head>



<body onload="document.entry.barrelnumber.focus()">



<?php

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");

include ("totalgallons.php");

include ("lotinforecords.php");



if ($_POST['addbutton']=="add")

{

	$query='INSERT INTO barrels SET

       barrels.NUMBER="'.strtoupper($_POST['barrelnumber']).'",

       barrels.DESCRIPTION="'.strtoupper($_POST['description']).'",

       barrels.CAPACITY="'.$_POST['capacity'].'",

       barrels.YEARNEW="'.$_POST['yearnew'].'",

       barrels.INITIALUSEDCOUNT="'.$_POST['initialusedcount'].'",

       barrels.INITIALVARIETAL="'.$_POST['initialvarietal'].'",

       barrels.VINEYARD="'.$_POST['vineyard'].'",

       barrels.CLIENTCODE="'.strtoupper($_SESSION['clientcode']).'",

       barrels.FOREST="'.strtoupper($_POST['forest']).'"';

//		echo $query;

	mysql_query($query);

}



if ($_POST['actionbutton']=="del")

{

	$query='DELETE FROM barrels WHERE (barrels.id='.$_POST['rowid'].')';

	// echo $query;

	mysql_query($query);

}



if ($_POST['actionbutton']=="mod")

{

	$query='UPDATE barrels SET

       barrels.NUMBER='.$_POST['barrelnumber'].',

       barrels.DESCRIPTION="'.strtoupper($_POST['description']).'",

       barrels.CAPACITY="'.$_POST['capacity'].'",

       barrels.YEARNEW="'.$_POST['yearnew'].'",

       barrels.INITIALUSEDCOUNT="'.$_POST['initialusedcount'].'",

       barrels.INITIALVARIETAL="'.$_POST['initialvarietal'].'",

       barrels.CLIENTCODE="'.$_SESSION['clientcode'].'",

       barrels.FOREST="'.strtoupper($_POST['forest']).'" WHERE (barrels.ID='.$_POST['rowid'].')';

	//	echo $query;

	mysql_query($query);

}



$_SESSION['returnpage']=$PHP_SELF;

if ($_GET['woid']!="")

$_SESSION['woid']=$_GET['woid'];





echo '<table width="500" border="0" align="center">';

echo '<tr>';

echo '<td colspan="4" align="center">';

echo '<big><b>BARREL INVENTORY SHEET</b></big><br><br>';

echo '</td>';

//echo '<tr><td align=center colspan=4><a href=hardcopy/presssheet.php?woid='.$_SESSION['woid'].'>PRINT</a></td></tr>';

echo '</tr>';

echo '</table>';

//echo '<p>&nbsp;</p>';





$query='SELECT * FROM barrels WHERE CLIENTCODE="'.$_SESSION['clientcode'].'" and 

           DESCRIPTION LIKE "%'.$_POST['filterdescription'].'%" and

           CAPACITY LIKE "%'.$_POST['filtercapacity'].'%" and

           (YEARNEW LIKE "%'.$_POST['filteryearnew'].'%" OR ISNULL(YEARNEW)) and

           (INITIALUSEDCOUNT LIKE "%'.$_POST['filterinitialused'].'%" OR ISNULL(INITIALUSEDCOUNT))

           ORDER BY NUMBER';

//echo $query;

$result=mysql_query($query);

$numrows=mysql_num_rows($result);



echo '<table align="center" border="1" width="700">';

echo '<tr><td></td><td align="center">BBL #</td><td align="center">DESCRIPTION</td><td align="center">CAPACITY</td>

         <td align=center>YEAR NEW</td><td align=center>INITIAL<br>USED</td><td align=center>INITIAL<br>VARIETAL</td><td align=center>VINEYARD</td><td align="center">FOREST</td><td align=center>CLIENT</td><td align=center>STATUS</td><td align=center>LOT</td></tr>';

echo '<tr>';

echo '<form method="POST" action='.$PHP_SELF.'?action=filter>';

echo '<td align="center">';

echo '</td>';

echo '<td align="center">';

echo '</td>';

echo '<td align="center">';

echo '<input type=text size=8 name=filterdescription value='.$_POST['filterdescription'].'>';

echo '</td>';

echo '<td align="center">';

echo '<input type=text size=5 name=filtercapacity value='.$_POST['filtercapacity'].'>';

echo '</td>';

echo '<td align="center">';

echo '<input type=text size=5 name=filteryearnew value='.$_POST['filteryearnew'].'>';

echo '</td>';

echo '<td align="center">';

echo '<input type=text size=5 name=filterinitialusedcount value='.$_POST['filterinitialusedcount'].'>';

echo '</td>';

echo '<td align="center">';

echo '<input type=text size=15 name=filterinitialvarietal value='.$_POST['filterinitialvarietal'].'>';

echo '</td>';

echo '<td align="center">';

echo '<input type=text size=15 name=filterinitialvarietal value='.$_POST['filtervineyard'].'>';

echo '</td>';

echo '<td align="center">';

echo '<input type=text size=5 name=filterforest value='.$_POST['filterforest'].'>';

echo '</td>';

echo '<td align="center">';

echo '</td>';

echo '<td align=center>';

echo '</td>';

echo '<td align=center>';

echo '</td>';

echo '<td>';

echo '<input type="submit" value="FILTER" name="actionbutton">';

echo '</td>';

echo '</form>';

echo '</tr>';



for ($i=0;$i<$numrows;$i++)

{

	$row=mysql_fetch_array($result);

	echo '<tr>';

	echo '<form method="POST" action='.$PHP_SELF.'>';

	echo '<td align="center">';

	echo '<input type="hidden" value='.$row['ID'].' name="rowid">';

	echo '<input type="submit" value="del" name="actionbutton">';

	echo '</td>';

	echo '<td align="center">';

	echo '<a href=barrelhistoryview.php?bblnumber='.$row['NUMBER'].'>'.$row['NUMBER'].'</a>';

	echo '<input type=hidden name=barrelnumber value='.$row['NUMBER'].'>';

	echo '</td>';

	echo '<td align="center">';

	echo '<input type=text size=8 name=description value="'.$row['DESCRIPTION'].'">';

	echo '</td>';

	echo '<td align="center">';

	echo '<input type=text size=5 name=capacity value="'.$row['CAPACITY'].'">';

	echo '</td>';

	echo '<td align="center">';

	echo '<input type=text size=5 name=yearnew value="'.$row['YEARNEW'].'">';

	echo '</td>';

	echo '<td align="center">';

	echo '<input type=text size=5 name=initialusedcount value="'.$row['INITIALUSEDCOUNT'].'">';

	echo '</td>';

	echo '<td align="center">';

//	echo '<input type=text size=15 name=initialvarietal value="'.$row['INITIALVARIETAL'].'">';

	echo DrawComboFromData("varietals","NAME",$row['INITIALVARIETAL'],"varietal");

	echo '</td>';

	echo '<td align="center">';

	echo '<input type=text size=5 name=vineyard value="'.$row['VINEYARD'].'">';

	echo '</td>';

	echo '<td align="center">';

	echo '<input type=text size=5 name=forest value="'.$row['FOREST'].'">';

	echo '</td>';

	echo '<td align="center">';

	echo $row['CLIENTCODE'];

	echo '</td>';

	echo '<td align=center>';

//	$bbl=bblhistory($row['NUMBER'],time(),$_SESSION['clientcode']);

	echo $bbl[count($bbl)-1]['status'];

	echo '</td>';

	echo '<td align=center>';

	echo '<a href=showlotinfo.php?lot='.$bbl[count($bbl)-1]['lot'].'>'.$bbl[count($bbl)-1]['lot'].'</a>';

	echo '</td>';

	echo '<td>';

	echo '<input type="submit" value="mod" name="actionbutton">';

	echo '</td>';

	echo '</form>';

	echo '</tr>';

}

echo '<tr><td></td><td align=center>';

echo '<form method="POST" action='.$PHP_SELF.' name="entry">';

echo '<input type="text" size=5 name="barrelnumber">';

echo '</td>';

echo '<td align="center">';

echo '<input type=text size=8 name=description value="">';

echo '</td>';

echo '<td align="center">';

echo '<input type=text size=5 name=capacity value="60">';

echo '</td>';

echo '<td align="center">';

echo '<input type=text size=5 name=yearnew value="">';

echo '</td>';

echo '<td align="center">';

echo '<input type=text size=5 name=initialusedcount value="">';

echo '</td>';

echo '<td align="center">';

echo DrawComboFromData("varietals","NAME","","varietal");

echo '</td>';

echo '<td align="center">';

echo '<input type=text size=5 name=vineyard value="">';

echo '</td>';

echo '<td align="center">';

echo '<input type=text size=5 name=forest value="">';

echo '</td>';

echo '<td align="center">';

echo '<input type="submit" value="add" name="addbutton">';

echo '</td>';

echo '</form>';

echo '</tr>';

echo '</table>';

?> 

