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

<body>



<?php



include ("startdb.php");

include ("yesno.php");

include ("setcheck.php");

include ("defaultvalue.php");

include ("manageadditions.php");



if (isset($_GET['linktype'])) $_SESSION['linktype']=$_GET['linktype'];

if (isset($_GET['linkid'])) $_SESSION['linkid']=$_GET['linkid'];



if ($_GET['action']=='delfile')

{

	$query='SELECT * from files where id="'.$_GET['fileid'].'"';

	$result=mysql_query($query);

	if (mysql_num_rows($result)>0)

	{

		$row=mysql_fetch_array($result);

		unlink($row['LOCATION']);

		$query='DELETE from files where id="'.$_GET['fileid'].'"';

		mysql_query($query);

	}

}



if (isset($_FILES['upload_test']))

{

	if ($_FILES['upload_test']['error'] != UPLOAD_ERR_OK)

	{

		echo ("Upload unsuccessful!<br>\n");

	}

	

	else

	{

		$filename=split('/',$_FILES['upload_test']['tmp_name']);

		$suffix=explode('.',$_FILES['upload_test']['name']);

		if (copy($_FILES['upload_test']['tmp_name'],('/var/www/html/crushpublic/crushclient/uploadfiles/'.$filename[2].'.'.$suffix[1])))

		{

			unlink($_FILES['upload_test']['tmp_name']);

		}

		$query='INSERT into files SET '.

		'TYPEID="'.$_POST['linktype'].'", '.

		'THEID="'.$_POST['linkid'].'", '.

		'LOCATION="'.'uploadfiles/'.$filename[2].'.'.$suffix[1].'", '.

		'NAME="'.$_FILES['upload_test']['name'].'", '.

		'SIZE="'.$_FILES['upload_test']['SIZE'].'", '.

		'FILETYPE="'.$_FILES['upload_test']['FILETYPE'].'"';

		mysql_query($query);

		$linktype=$_POST['linktype'];

		$linkid=$_POST['linkid'];

		

	}

}

$query='SELECT * from files WHERE TYPEID="'.$linktype.'" AND THEID="'.$linkid.'"';

$result=mysql_query($query);

echo '<table border=1 width=50% align=center>';

for ($i=0;$i<mysql_num_rows($result);$i++)

{

	$row=mysql_fetch_array($result);

	echo '<tr>';

	echo '<td width=10% align=center><a href='.$PHP_SELF.'?action=delfile&fileid='.$row['ID'].'>del</a></td>';

	echo '<td align=center><a href='.$row['LOCATION'].'>'.$row['NAME'].'</a></td>';

	echo '</tr>';

	

}

echo '</table>';



echo '<table align=center>';

echo '<form enctype="multipart/form-data" action='.$PHP_SELF.' method=post><tr>';

echo '<td colspan=2 align=center>';

echo '<input type=hidden name="MAX_FILE_SIZE" value="1024000">';

echo '<input name="upload_test" type=file>';

echo '<input type=hidden name=linktype value='.$linktype.'>';

echo '<input type=hidden name=linkid value='.$linkid.'>';

echo '<input type=submit value="UPLOAD">';

echo '</td></tr>';

echo '</form></table>';



?>

</body>



</html>

