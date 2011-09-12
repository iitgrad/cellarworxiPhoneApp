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



$query='SELECT 

  lots.YEAR,

  SUM(`bindetail`.`WEIGHT`) AS `FIELD_1`,

  SUM(`bindetail`.`TARE`) AS `FIELD_2`,

  `wt`.`CLIENTCODE`,

  `clients`.`CLIENTNAME`

FROM

  `bindetail`

  INNER JOIN `wt` ON (`bindetail`.`WEIGHTAG` = `wt`.`ID`)

  INNER JOIN `lots` ON (`wt`.`LOT` = `lots`.`LOTNUMBER`)

  INNER JOIN `clients` ON (`lots`.`CLIENTCODE` = `clients`.`clientid`)

WHERE

  (`lots`.`YEAR` > "2002")

GROUP BY

  `wt`.`CLIENTCODE`,

  `clients`.`CLIENTNAME`,

  lots.YEAR';





//echo $query;



$result = mysql_query($query);



$num_results = mysql_num_rows($result);

$totalweight = 0;

for ($i=0;$i<mysql_num_rows($result);$i++)

{

    $row=mysql_fetch_array($result);

    $thetotal[$row['CLIENTNAME']][$row['YEAR']]+= $row['FIELD_1']-$row['FIELD_2'];

}

//echo '<pre>';

//print_r($thetotal);

//echo '</pre>';

//exit;

?>

<table id="demo_table" border="0" align="center">

  <tr>

    <td width="20%" align="center"><b>CLIENT NAME</b></td>

    <td align=right width="10%"><b>2003 TONS</b></td>

    <td align=right width="10%"><b>2004 TONS</b></td>

    <td align=right width="10%"><b>2005 TONS</b></td>

    <td align=right width="10%"><b>2006 TONS</b></td>

    <td align=right width="10%"><b>2007 TONS</b></td>

  </tr>

<tr><td colspan=99><hr></td></tr>

<?php

foreach ($thetotal as $key=>$value)

{

//	$row = mysql_fetch_array($result);

    echo '<tr>';

    echo '<td align=center>';

    echo $key;

    echo '</td>';

    for ($j=2003;$j<=2007;$j++)

    {

        echo '<td align=right>';

        if ($value[$j]>0)

        echo number_format($value[$j]/2000,2);

        else

        echo '';

        echo '</td>';

        $yearsum[$j]+=$value[$j];

    }

    echo '</tr>';

}

echo '<tr><td colspan=99><hr></td></tr>';

    echo '<tr>';

    echo '<td align=center>';

    echo "TOTAL";

    echo '</td>';

    for ($j=2003;$j<=2007;$j++)

    {

    echo '<td align=right>';

    echo number_format($yearsum[$j]/2000,2);

    echo '</td>';

    }

    echo '</tr>';



echo '</table> ';

?>



</body>



</html>

