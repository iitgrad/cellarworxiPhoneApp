<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

    <script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>



    <script language="JavaScript" type="text/javascript">

    function confirmdelete(url)

    {

    	if (confirm('Are you SURE you want to delete this lot!!??'))

    	{

    		location.href=url;

    	}

    }

  </script>

</head>





<?php

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");

include ("totalgallons.php");



if ($_GET['action']=="setclient")

$_SESSION['clientcode']=$_GET['clientcode'];



if ($_GET['action']=="clearclient")

$_SESSION['clientcode']='';



if ($_GET['action']=="addlot")

{
	if ($_POST['organic']=="on")
		$_POST['organic']="YES";
	else
		$_POST['organic']="NO";

	if (strlen($_POST['lotnumber'])>5)

	{

		$query='SELECT * FROM lots WHERE lots.LOTNUMBER="'.strtoupper($_POST['lotnumber']).'"';

		$result=mysql_query($query);

		if (mysql_num_rows($result)==0)

		{

			$query='INSERT INTO lots SET lots.LOTNUMBER="'.strtoupper($_POST['lotnumber']).'",'.

			'lots.DESCRIPTION="'.strtoupper($_POST['description']).'",'.

			'lots.YEAR="'.$_POST['year'].'",'.

			'lots.ORGANIC="'.$_POST['organic'].'",'.

			'lots.CLIENTCODE="'.strtoupper($_POST['clientcode']).'"';

			$result=mysql_query($query);

		}

	}

}

if ($_GET['action']=="modlot")

{
	// echo '<pre>';
	// print_r($_POST);
	
	if ($_POST['organic']=="on")
		$_POST['organic']="YES";
	else
		$_POST['organic']="NO";
		
	$query='UPDATE lots SET lots.DESCRIPTION="'.strtoupper($_POST['description']).'",'.

	'lots.YEAR="'.$_POST['year'].'", lots.ORGANIC="'.$_POST['organic'].'" WHERE lots.ID="'.$_POST['rowid'].'"';

	//echo $query;

	$result=mysql_query($query);

}



if ($_GET['action']=="delete")

{

	$cannot_delete=0;

	$query='SELECT wo.ID from lots INNER JOIN `wo` ON (`lots`.`LOTNUMBER` = `wo`.`LOT`) WHERE lots.ID="'.$_GET['lotid'].'"';

	$result=mysql_query($query);

	if (mysql_num_rows($result)>0)

	{

		$row=mysql_fetch_array($result);

		$cannot_delete=1;

		$ref[]['type']="WO";

		$ref[]['id']=$row['ID'];

	}

	$query='SELECT bolitems.ID from lots INNER JOIN `bolitems` ON (`lots`.`LOTNUMBER` = `bolitems`.`LOT`) WHERE lots.ID="'.$_GET['lotid'].'"';

	$result=mysql_query($query);

	if (mysql_num_rows($result)>0)

	{

		$row=mysql_fetch_array($result);

		$cannot_delete=1;

		$ref[]['type']="BOL";

		$ref[]['id']=$row['ID'];

	}

	$query='SELECT wt.ID from lots INNER JOIN `wt` ON (`lots`.`LOTNUMBER` = `wt`.`LOT`) WHERE lots.ID="'.$_GET['lotid'].'"';

	$result=mysql_query($query);

	if (mysql_num_rows($result)>0)

	{

		$row=mysql_fetch_array($result);

		$cannot_delete=1;

		$ref[]['type']="WT";

		$ref[]['id']=$row['ID'];

	}

	

	if ($cannot_delete==1)

	{

		$query='SELECT * from lots  WHERE lots.ID="'.$_GET['lotid'].'"';

		$result=mysql_query($query);

		$row=mysql_fetch_array($result);

		echo "<center><b><big>CANNOT DELETE LOT: ".$row['LOTNUMBER'].", IT IS REFERENCED<br></big></b><br>";

		for ($i=0;$i<count($ref);$i++)

		{

			switch ($ref[$i]['type'])

			{

				case 'WT' : echo 'WT-'.$ref[$i]['ID'].'<br>'; break;

				case 'BOL' : echo 'BOL-'.$ref[$i]['ID'].'<br>'; break;

				case 'WO' : echo 'WO-'.$ref[$i]['ID'].'<br>'; break;

			}

		}

	}

	else

	{

		$query='DELETE from lots WHERE lots.ID="'.$_GET['lotid'].'"';

		mysql_query($query);

	}

}



$cc=$_SESSION['clientcode'];
//echo 'clientcode is '.$cc;

if ($cc=="")
{
	
	$ci=clientinfo($_SERVER['PHP_AUTH_USER']);
	//echo '--'.$_SERVER['PHP_AUTH_USER'];



	if ($_GET['vintage']|='')

	  $_SESSION['vintage']=$_GET['vintage'];



	if ($_GET['clientcode']|='')

	  $_SESSION['clientcode']=$_GET['clientcode'];



	if ($_SESSION['clientcode']=='')

	$_SESSION['clientcode']=$ci['code'];
}
$cc=$_SESSION['clientcode'];
//echo 'clientcode is '.$cc;
if ($cc!="")

{

	echo '<table id=table1 width=60% align=center><tr><big><b><td></td><td align=center>LOT NUMBER</td><td align=center>VINTAGE</td><td align=center>DESCRIPTION</td>
	<td align=center>ORGANIC</td><td width=30></td></big></b></tr><tr>';

	$query='SELECT * FROM lots WHERE ((lots.YEAR="'.$_SESSION['vintage'].'") AND (CLIENTCODE="'.clientid($cc).'")) ORDER BY lots.LOTNUMBER';

	//echo $query;

	$result=mysql_query($query);

	for ($i=0; $i<mysql_num_rows($result); $i++)

	{

		$row=mysql_fetch_array($result);

		echo '<form name=addlot method="POST" action="'.$PHP_SELF.'?action=modlot&clientid='.clientid($cc).'">';

		echo '<tr><td align=center><a href="javascript:confirmdelete(\''.$PHP_SELF.'?action=delete&lotid='.$row['ID'].'\')">del</a></td><td align=center width=20%>';

		echo '<a href=showlotinfo.php?lot='.$row['LOTNUMBER'].'>'.$row['LOTNUMBER'].'</a>';

		$values=explode("-",$row['LOTNUMBER']);

		if ((int) $values[2] > $maxval)

		$maxval=(int) $values[2];

		echo '<td align=center width=10%>';

		echo '<input type=hidden name=year value="'.$row['YEAR'].'">';
		echo $row['YEAR'].'</td>';

		echo '<input type=hidden value='.clientid($cc).' name="clientcode">';

		echo '<input type=hidden value='.$row['ID'].' name="rowid">';

		echo '<td align=center width=60%>';

		echo '<input type=text size=45 name=description value="'.strtoupper($row['DESCRIPTION']).'">';

		echo '<td align=center>';

		if ($row['ORGANIC']=="YES")
			echo '<input type=checkbox checked name="organic">'.'</td>';
		else
			echo '<input type=checkbox name="organic">'.'</td>';

		echo '</td>';

		echo '<td><input type=submit value=mod></td></form>';

		echo '</td>';

		echo '</form>';

		echo '</tr>';

	}

	

	echo '<form name=addlot method="POST" action="'.$PHP_SELF.'?action=addlot&clientid='.clientid($cc).'">';

	echo '<tr><td align=center width=5%></td><td align=center width=15%>';

    $yc=substr($_SESSION['vintage'],2).'-';
	echo '<input type=hidden name="lotnumber" value="'.$yc.$_SESSION['clientcode'].'-'.sprintf("%02d",$maxval+1).'">'.$yc.$_SESSION['clientcode'].'-'.sprintf("%02d",$maxval+1).'</td>';

	echo '<td align=center width=10%>';

	echo '<input type=hidden name="year" value="'.$_SESSION['vintage'].'">'.$_SESSION['vintage'].'</td>';

	echo '<td align=center width=60%>';

	echo '<input type=textbox name="description" size=45>'.'</td>';

	echo '<td align=center>';

	echo '<input type=checkbox name="organic">'.'</td>';

	echo '<input type=hidden value='.clientid($cc).' name="clientcode">';

	echo '<td><input type=submit value=add></td></form>';

	echo '</tr>';

	echo '</table>';

}

  ?>

<script language="JavaScript">

<!--

tigra_tables('table1', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

// -->

            </script>



</body>



</html>