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



if ($_POST['addbutton']=="add")

{

	$query='INSERT INTO barrelhistory SET

       barrelhistory.BARRELNUMBER="'.$_POST['barrelnumber'].'",

       barrelhistory.DIRECTION="'.$_POST['direction'].'",

       barrelhistory.STATUS="'.$_POST['status'].'",

       barrelhistory.PRESSFRACTION="'.$_POST['pressfraction'].'",

       barrelhistory.WOID='.$_POST['thewoid'];

//	echo $query;

	mysql_query($query);

}



if ($_POST['deletebutton']=="del")

{

	$query='DELETE FROM barrelhistory WHERE (barrelhistory.id='.$_POST['rowid'].')';

	// echo $query;

	mysql_query($query);

}



$_SESSION['returnpage']=$PHP_SELF;

if ($_GET['woid']!="")

$_SESSION['woid']=$_GET['woid'];

$wo=getwo($_SESSION['woid']);



$query='SELECT `assets`.`NAME` FROM `reservation` INNER JOIN `assets` ON (`reservation`.`ASSETID` = `assets`.`ID`)

  WHERE (`assets`.`TYPEID` = "2") AND (`reservation`.`WOID` = '.$_SESSION['woid'].') ORDER BY `assets`.`NAME`';



$result=mysql_query($query);

$row=mysql_fetch_array($result);



echo '<table width="500" border="0" align="center">';

echo '<tr>';

echo '<td colspan="4" align="center">';

echo 'PRESS SHEET';

echo '</td>';

echo '<tr><td align=center colspan=4><a href=hardcopy/presssheet.php?woid='.$_SESSION['woid'].'>PRINT</a></td></tr>';

echo '</tr>';

echo '<tr>';

echo '<td align="center">';

echo 'DATE: '.$wo['duedate'];

echo '</td>';

echo '<td align="center">';

echo 'LOT: <a href=showlotinfo.php?lot='.$wo['lot'].'>'.$wo['lot'].'</a>';

echo '</td>';

echo '<td align="center">';

echo 'PRESS: '.$row['NAME'];

echo '</td>';

echo '<td align="center">';

echo 'WORK ORDER: <a href=wopage.php?action=view&woid='.$_SESSION['woid'].'>'.$_SESSION['woid'].'</a>';

echo '</td>';

echo '</tr><tr></tr>';

echo '</table>';

//echo '<p>&nbsp;</p>';





$query='SELECT barrelhistory.STATUS, barrelhistory.DIRECTION, `barrelhistory`.`PRESSFRACTION`, `barrelhistory`.`BARRELNUMBER`, barrelhistory.ID,

              `barrels`.`DESCRIPTION`, `barrels`.`CLIENTCODE`, `barrels`.`FOREST`, `barrels`.`CAPACITY`

              FROM `barrelhistory` LEFT OUTER JOIN `barrels` ON (`barrelhistory`.`BARRELNUMBER` = `barrels`.`NUMBER`)

              WHERE `barrelhistory`.`WOID` = '.$_SESSION['woid'].' AND barrels.CLIENTCODE="'.$_SESSION['clientcode'].'"';

  //   echo $query;





$result=mysql_query($query);

$numrows=mysql_num_rows($result);

echo '<table align="center" border="1" width="500">';

echo '<tr><td></td><td align="center">BBL #</td><td align="center">DIRECTION</td><td align="center">STATUS</td><td align="center">DESCRIPTION</td><td align="center">FOREST</td>

          <td align="center">PRESS<br>FRACTION</td><td align="center">GALLONS</td><td></td></tr>';

for ($i=0;$i<$numrows;$i++)

{

	$row=mysql_fetch_array($result);

	echo '<tr>';

	echo '<form method="POST" action=presssheet.php>';

	echo '<td align="center">';

	echo '<input type="hidden" value='.$row['ID'].' name="rowid">';

	echo '<input type="submit" value="del" name="deletebutton">';

	echo '</td>';

	echo '</form>';

	echo '<td align="center">';

	echo '<a href=barrelhistoryview.php?bblnumber='.$row['BARRELNUMBER'].'>'.$row['BARRELNUMBER'].'</a>';

	echo '</td>';

	echo '<td align="center">';

	echo $row['DIRECTION'];

	echo '</td>';

	echo '<td align="center">';

	echo $row['STATUS'];

	echo '</td>';

	echo '<td align="center">';

	echo $row['DESCRIPTION'];

	echo '</td>';

	echo '<td align="center">';

	echo $row['FOREST'];

	echo '</td>';

	echo '<td align="center">';

	if ($row['PRESSFRACTION']==0)

	echo 'FR';

	else

	echo $row['PRESSFRACTION'];

	echo '</td>';

	echo '<td align="center">';

	echo $row['CAPACITY'];

	echo '</td>';

	echo '</tr>';

}

echo '<tr>';

echo '<td align="center">';

echo '<form method="POST" action=presssheet.php name="entry">';

echo '<input type="hidden" value='.$_SESSION['woid'].' name="thewoid">';

echo '</td>';

echo '<td align="center">';

echo '<input type="text" columns="5" name="barrelnumber">';

echo '</td>';

echo '<td align="center">';

echo DrawComboFromEnum("barrelhistory","DIRECTION", $row['DIRECTION'],"direction");

echo '</td>';

echo '<td align="center">';

echo DrawComboFromEnum("barrelhistory","STATUS", $row['STATUS'],"status");

echo '</td>';

echo '<td align="center">';

echo '</td>';

echo '<td align="center">';

echo '</td>';

echo '<td align="center">';

echo '<input type="text" columns="5" name="pressfraction">';

echo '</td>';

echo '<td align="center">';

echo '</td>';

echo '<td align="center">';

echo '<input type="submit" value="add" name="addbutton">';

echo '</td>';

echo '</form>';



echo '</table>';

?> 

