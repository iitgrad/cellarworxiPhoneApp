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

  

  function showallwobystatus($status, $clientcode)

  {

     $query='SELECT * FROM `wo`WHERE

       (`wo`.`STATUS` = "'.$status.'" AND

        `wo`.`CLIENTCODE` = "'.$clientcode.'" AND

       NOT ((wo.TYPE = "DRYICE") OR (wo.TYPE = "PUMP OVER") OR (wo.TYPE = "PUNCH DOWN"))) ORDER BY  `wo`.`DUEDATE`';



    $result=mysql_query($query);

    $num_rows=mysql_num_rows($result);

    for ($j=0;$j<$num_rows;$j++)

    {

      $wo[$j]=mysql_fetch_array($result);

    }

    return $wo;

  }



  $ci=clientinfo($_SERVER['REMOTE_USER']);

  $_SESSION['clientcode']=$ci['clientcode'];

  

	 echo '<table border="1" align="center" width="300">';

 echo '<tr><td colspan="3" align="center">WORK ORDER PANEL FOR '.strtoupper($_SERVER['REMOTE_USER']).'</td></tr><tr></tr>';

 echo '<tr><td width=50% align=right>PENDING</td><td align=center><a href=showcustwo.php?status=PENDING>'.count(showallwobystatus("PENDING",$ci['clientcode'])).'</a></td></tr>';

 echo '<tr><td width=50% align=right>ASSIGNED</td><td align=center><a href=showcustwo.php?status=ASSIGNED>'.count(showallwobystatus("ASSIGNED",$ci['clientcode'])).'</a></td></tr>';

 echo '<tr><td width=50% align=right>COMPLETED</td><td align=center><a href=showcustwo.php?status=COMPLETED>'.count(showallwobystatus("COMPLETED",$ci['clientcode'])).'</a></td></tr>';

 echo '<tr><td width=50% align=right>HOLD</td><td align=center><a href=showcustwo.php?status=HOLD>'.count(showallwobystatus("HOLD",$ci['clientcode'])).'</a></td></tr>';

  echo '</table>';

?>



</body>



</html>

