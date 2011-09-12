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



$query='SELECT SUM(`bindetail`.`WEIGHT`) AS `FIELD_1`, SUM(`bindetail`.`TARE`) AS `FIELD_2`, clients.CLIENTNAME, 

 wt.REGIONCODE, wt.VARIETY FROM `bindetail` INNER JOIN `wt` ON (`bindetail`.`WEIGHTAG` = `wt`.`ID`)

 INNER JOIN `lots` ON (`wt`.`LOT` = `lots`.`LOTNUMBER`)

 INNER JOIN `clients` ON (`lots`.`CLIENTCODE` = `clients`.`clientid`)

  WHERE (`lots`.`YEAR` = "2005") GROUP BY clients.CLIENTNAME,  `wt`.`VARIETY`, wt.REGIONCODE';





 echo $query;

$result = mysql_query($query);



$num_results = mysql_num_rows($result);

$totalweight = 0;

?>

<table id="demo_table" border="0" align="center">

  <?php

for ($i=0; $i <$num_results; $i++)

{

	$row = mysql_fetch_array($result);

    echo '<tr>';

    echo '<td align=center>';

    echo $row['CLIENTNAME'];

    echo '</td>';

    echo '<td align=center>';

    echo $row['VARIETY'];

    echo '</td>';

    echo '<td align=center>';

    echo $row['REGIONCODE'];

    echo '</td>';

    echo '<td align=right>';

    echo number_format(($row['FIELD_1']-$row['FIELD_2'])/2000,2);

    $runningtotal+=($row['FIELD_1']-$row['FIELD_2'])/2000;

    echo '</td>';

    echo '</tr>';

}

echo '<tr><td colspan=2><hr></td></tr>';

    echo '<tr>';

    echo '<td align=center>';

    echo "TOTAL";

    echo '</td>';

    echo '<td align=right>';

    echo number_format($runningtotal,2);

    echo '</td>';

    echo '</tr>';



echo '</table> ';

?>



</body>



</html>