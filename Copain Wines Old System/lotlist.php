<?php

    session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

  <base target="maincontent">

  <script language="JavaScript">

        function a(txt) {

                self.status = txt

        }



        function b() {

                self.status = ""

        }

</script>

</head>



<body>



<?php

  include ("startdb.php");

  include ("queryupdatefunctions.php");



  include ("assetfunctions.php");

  include ("totalgallons.php");



  if ($_GET['action']=="setclient")

    $_SESSION['clientcode']=$_GET['clientcode'];

	

  if ($_GET['action']=="clearclient")

    $_SESSION['clientcode']='';

	

  $cc=$_SESSION['clientcode'];

// echo '---'.$_SESSION['clientcode'];

 

  if ($cc!="")

  {

    echo '<table width=100% align=center>';

	$query='SELECT * FROM lots WHERE CLIENTCODE="'.clientid($cc).'" AND YEAR="'.$_SESSION['vintage'].'"';

	$result=mysql_query($query);

	for ($i=0; $i<mysql_num_rows($result); $i++)

	{

	  $row=mysql_fetch_array($result);

	  echo '<tr><td align=center>';

        $desc=$row['DESCRIPTION'];

	  echo '<a href=showlotinfo.php?lot='.$row['LOTNUMBER'].'>'.$row['LOTNUMBER'].'</a>';

	  echo '</td></tr>';

	}

    echo '</table>';  

  }

?>



</body>



</html>

