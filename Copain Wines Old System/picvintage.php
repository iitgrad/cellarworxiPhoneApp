<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

     <script language="JavaScript" type="text/javascript">

 function multiLoad(doc1,doc2) {

  parent.maincontent.location.href=doc1;

  parent.contents.location.href=doc2;

 // parent.list.location.href="blank.htm";

}

</script>



</head>



<body>



<?php

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");

include ("totalgallons.php");



if ($_GET['action']=="setvintage")

$_SESSION['vintage']=$_GET['vintage'];



$cc=strtoupper($_SESSION['vintage']);



echo '<table width=20% align=center>';

$query='SELECT DISTINCT lots.YEAR from lots WHERE lots.YEAR>0 ORDER BY lots.YEAR';

$result=mysql_query($query);

for ($i=0; $i<mysql_num_rows($result); $i++)

{



	$row=mysql_fetch_array($result);

	if ($row['YEAR']==$cc)

	{

		echo '<tr><td style="border-style: solid; border-color: Sienna; border-width: 1" align=center>';

		echo $row['YEAR'];

	}

	else

	{

		echo '<tr><td align=center>';

		$link1='picvintage.php?action=setvintage&vintage='.$row['YEAR'];

		$link2='mainmenu.php?action=setvintage&vintage='.$row['YEAR'];

	//	echo '<a href="javascript:multiLoad(\''.$link1.'\',\'mainmenu.php\')">'.$row['YEAR'].'</a>';

		echo '<a href="javascript:multiLoad(\''.$link1.'\',\''.$link2.'\')">'.$row['YEAR'].'</a>';

	}

	

	echo '</td></tr>';

}

echo '</table>';



?>



</body>



</html>

