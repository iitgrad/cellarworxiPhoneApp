<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="iphone.css">

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

<body>
<div class=Column>
		<a href=index.php>MAIN MENU</a><br>
		</div>



<?php

include ("../startdb.php");

include ("../queryupdatefunctions.php");

include ("../assetfunctions.php");

include ("../totalgallons.php");



if ($_GET['action']=="setclient")

$_SESSION['clientcode']=$_GET['clientcode'];



if ($_GET['action']=="clearclient")

$_SESSION['clientcode']='';



if ($_GET['action']=="addlot")

{

	if (strlen($_POST['lotnumber'])>5)

	{

		$query='SELECT * FROM lots WHERE lots.LOTNUMBER="'.strtoupper($_POST['lotnumber']).'"';

		$result=mysql_query($query);

		if (mysql_num_rows($result)==0)

		{

			$query='INSERT INTO lots SET lots.LOTNUMBER="'.strtoupper($_POST['lotnumber']).'",'.

			'lots.DESCRIPTION="'.strtoupper($_POST['description']).'",'.

			'lots.YEAR="'.$_POST['year'].'",'.

			'lots.CLIENTCODE="'.strtoupper($_POST['clientcode']).'"';

			$result=mysql_query($query);

		}

	}

}

if ($_GET['action']=="modlot")

{

	$query='UPDATE lots SET lots.DESCRIPTION="'.strtoupper($_POST['description']).'",'.

	'lots.YEAR="'.$_POST['year'].'" WHERE lots.ID="'.$_POST['rowid'].'"';

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


echo '<div class=Column>';

if ($cc!="")

{
	echo '<table width=100% align=center>';

	$query='SELECT * FROM lots WHERE ((lots.YEAR="'.$_SESSION['vintage'].'") AND (CLIENTCODE="'.clientid($cc).'")) ORDER BY lots.LOTNUMBER';

	//echo $query;

	$result=mysql_query($query);

	for ($i=0; $i<mysql_num_rows($result); $i++)

	{

		$row=mysql_fetch_array($result);

		echo '<tr>';

		echo '<td width=40%><a href=showlotinfo.php?lot='.$row['LOTNUMBER'].'>'.$row['LOTNUMBER'].'</a></td>';

		$values=explode("-",$row['LOTNUMBER']);

		if ((int) $values[2] > $maxval)

		$maxval=(int) $values[2];


		echo '<td width=60%>'.strtoupper($row['DESCRIPTION']).'</td>';

		echo '</tr>';

	}

}
echo '</div>';

  ?>

<script language="JavaScript">

<!--

tigra_tables('table1', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

// -->

            </script>



</body>



</html>