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

  wt.ID, YEAR(wt.DATETIME) as YEAR, DAYOFYEAR(wt.DATETIME) as DAYOFYEAR,

  SUM(bindetail.WEIGHT) AS FIELD_1,

  SUM(bindetail.TARE) AS FIELD_2

FROM

  wt

  INNER JOIN bindetail ON (wt.ID = bindetail.WEIGHTAG) GROUP BY wt.ID';





//echo $query;



$result = mysql_query($query);



$num_results = mysql_num_rows($result);

//echo $num_results;

$totalweight = 0;

for ($i=0;$i<mysql_num_rows($result);$i++)

{

    $row=mysql_fetch_array($result);

    $thetotal[$row['DAYOFYEAR']][$row['YEAR']]+= $row['FIELD_1']-$row['FIELD_2'];

}



$thisday=date("z",time());

for ($i=200; $i<=365; $i++)

{

	for ($j=2003; $j<=2007 ; $j++)

	{

		if ($i<=$thisday+1)

		$year[$j]+=($thetotal[$i][$j])/2000;

	}

}

echo '<pre>';

print_r($year);

echo '</pre>';



exit;

?>

<table id="demo_table" border="0" align="center">

  <tr>

    <td width="20%" align="center"><b>CLIENT NAME</b></td>

 //   <td align=right width="10%"><b>2002 TONS</b></td>

    <td align=right width="10%"><b>2003 TONS</b></td>

    <td align=right width="10%"><b>2004 TONS</b></td>

    <td align=right width="10%"><b>2005 TONS</b></td>

    <td align=right width="10%"><b>2006 TONS</b></td>

    <td align=right width="10%"><b>2007 TONS</b></td>

  </tr>

<tr><td colspan=5><hr></td></tr>

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

echo '<tr><td colspan=5><hr></td></tr>';

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

