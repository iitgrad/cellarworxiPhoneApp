<?php

  session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

</head>



<body>



<?php

   include ("startdb.php");

   include ("queryupdatefunctions.php");

   include ("assetfunctions.php");

   $_SESSION['showassetidfilter'];

   $assettypes=listassettypes();

   echo '<table width="30%" align="center"><form method="POST" action=assetpicklist.php>';

   for($i=0;$i<count($assettypes);$i++)

   {

      echo '<tr><td align="left"><input type="checkbox" name="'. $assettypes[$i]['NAME']. '" value="ON"';

      if ($_POST[$assettypes[$i]['NAME']]=="ON") echo "CHECKED";

      echo '>'. $assettypes[$i]['NAME'].'</td></tr>';

      if ($_POST[$assettypes[$i]['NAME']]=="ON")

      {

      $theassets=listassetsoftype($assettypes[$i]['ID']);

      for($j=0;$j<count($theassets);$j++)

      {

        echo '<tr><td></td><td align="left"><input type="checkbox" name="'. $theassets[$j]['NAME']. '" value="ON"';

        if ($_POST[$theassets[$j]['NAME']]=="ON") echo "CHECKED";

        echo '>'. $theassets[$j]['NAME'].'</td></tr>';

      }

      }

   }

   echo '<tr><td><input type=submit value="ENTER"></td></tr>';

   echo '</form></table>';



   $allassets=listallassets();

   for ($i=0;$i<count($allassets);$i++)

   {

      if ($_POST[$allassets[$i]['NAME']]=="ON") $showassetidfilter[$i]=$allassets[$i];

   }

   $_SESSION['showassetidfilter']=$showassetidfilter;

   if ($_GET['returnpage']!="")

     $returnpage=$_GET['returnpage'];

   echo '<center><a href='.$returnpage.'>NEXT<a></center>';

?>



</body>



</html>