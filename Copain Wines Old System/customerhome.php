<?php

  include("startdb.php");

  session_start();

  include("queryupdatefunctions.php");

?>

<html>



<head>

<title>COPAIN CUSTOM CRUSH</title>

  <link rel="stylesheet" type="text/css" href="../site.css">

</head>



<frameset framespacing="0" border="1" frameborder="1" rows="60,*,30">

  <frame name="banner" scrolling="no" noresize target="contents" src="banner.php">

  <frameset cols="162,*">

    <frame name="contents" src="mainmenu.php">

<?php

  if (isstaff()=="YES")

    echo '<frame name="maincontent" src="staff.php">';

  else

    echo '<frame name="maincontent" src="custhome.php">';

?>

  </frameset>

  <frame name="bottom" scrolling="no" noresize target="contents" src="footer.php">

</frameset>



</html>

