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



function thetable($a,$b)

{

	$value='<table width=100% border=1><tr><td width=75 align=right>'.number_format($a,1).'</td><td width=75 align=right>'.number_format($b,1).'</td><td width=75 align=right>'.number_format($a+$b,1).'</td></tr></table>';

	return $value;

}



function thetable2($a)

{

	$value='<table width=100% border=1><tr><td width=75 align=right></td><td width=75 align=right>'.number_format($a,1).

	'</td><td width=75 align=right></td></tr></table>';

	return $value;

}



$query = 'SELECT *, wo.ID AS WOID from wo left outer join scp on (wo.ID=scp.WOID)

       WHERE (wo.TYPE="SCP" AND wo.ENDDATE>"'.date("Y-m-d",(time())).'") ORDER BY ENDDATE';

//echo $query;





   $result = mysql_query($query);

   $num_results = mysql_num_rows($result);



   $total=0;

        for ($i=0; $i <$num_results; $i++)

        {

           $row=mysql_fetch_array($result);

           $data[$row['CLIENTCODE']]['FRUIT'][$row['VARIETAL']]+=$row['ESTTONS'];

           $data[$row['CLIENTCODE']]['TOTAL']+=$row['ESTTONS'];

           $totaltons+=$row['ESTTONS'];        

        }

        

        $query='SELECT 

  SUM(bindetail.WEIGHT) AS FIELD_1,

  SUM(bindetail.TARE) AS FIELD_2,

  clients.CODE

FROM

  bindetail

  INNER JOIN wt ON (bindetail.WEIGHTAG = wt.ID)

  INNER JOIN clients ON (wt.CLIENTCODE = clients.clientid)

WHERE

  (unix_timestamp(wt.DATETIME) > unix_timestamp("2007/01/01"))

GROUP BY

  clients.CODE';

        

        $result2=mysql_query($query);

        

        for ($i=0; $i<mysql_num_rows($result2); $i++)

        {

        	$row=mysql_fetch_array($result2);

        	$data[$row['CODE']]['TONSTODATE']=($row['FIELD_1']-$row['FIELD_2'])/2000;

        	$totaltodate+=($row['FIELD_1']-$row['FIELD_2'])/2000;

        }

        echo '<table width=525 border=1 align="center">';

           echo '<tr>';

           echo '<td width=100 align=center>CLIENT</td><td width=200 align=center>VARIETAL</td>';

           echo '<td width=225 align=right><table width=100% border=1><tr><td colspan=3 align=center>TOTAL TONS</td></tr>';

           echo '<tr><td align=right width=75>TO DATE</td><td align=right width=75>EST</td><td align=right width=75>TOTAL</td></tr></table>';

           

           echo '</tr>';



           foreach ($data as $key=>$value)

        {

        	$first=0;

        	foreach ($value['FRUIT'] as $fruit=>$tons)

        	{

        		if ($first==0)

        		echo '<tr><td align=center>'.$key.'</td>';

        		else 

        		echo '<tr><td></td>';

        		echo '<td align=center>'.$fruit.'</td><td align center>'.thetable2($tons).'</td><td></td></tr>';

        		$first=1;

        	}

        	if ($first==0)

        	   echo '<tr><td align=center>'.$key.'</td>';

        	else

        	echo '<tr><td align=center></td>';

        	echo '<td align=center></td><td align=center>'.

        	thetable($value['TONSTODATE'],$value['TOTAL']).'</td></tr>';

        }

        echo '<tr><td align=center>GRAND TOTAL</td><td align=center></td><td align=center>'.thetable($totaltodate,$totaltons).'</td></tr>';

        echo '</table>';

?>



</body>



</html>

