<?php

function gallonstodate($lot,$date, $woid)

{

  $totalgallons=0;

$query='SELECT wt.VARIETY,

       wt.VINEYARD,

       wt.APPELATION,

       wt.TAGID,

       DATE_FORMAT(`wt`.`DATETIME`,'. '"'. '%m-%d-%Y' . '"'.') AS THEDATE,

       SUM( bindetail.BINCOUNT ) AS SUM_OF_BINCOUNT,

       SUM( bindetail.WEIGHT ) AS SUM_OF_WEIGHT,

       SUM( bindetail.TARE ) AS SUM_OF_TARE

FROM bindetail

   INNER JOIN wt ON (bindetail.WEIGHTAG = wt.ID)

   INNER JOIN lots ON (wt.LOT = lots.LOTNUMBER)

WHERE 

   (

      (lots.LOTNUMBER = "'.$lot.'")

   )

GROUP BY wt.VARIETY, wt.VINEYARD, wt.APPELATION, wt.TAGID, wt.DATETIME';



  $result=mysql_query($query);

  $num_rows=mysql_num_rows($result);

  for ($i=0;$i<$num_rows;$i++)

  {

    $row=mysql_fetch_array($result);

    $tons=($row['SUM_OF_WEIGHT']-$row['SUM_OF_TARE'])/2000;

    $totalgallons=$totalgallons+($tons*160);

  }

  $gallons['endingtankgallons']=$totalgallons;

  

$query='SELECT 

  `wo`.`TYPE`,

  `wo`.`ASSIGNEDTO`,

  `wo`.`OTHERDESC`,

  `wo`.`ID`,

  `wo`.`COMPLETEDDESCRIPTION`,

   wo.ENDINGTANKGALLONS,

   wo.ENDINGBARRELCOUNT,

   wo.ENDINGTOPPINGGALLONS,

 DATE_FORMAT(`wo`.`DUEDATE`,'. '"'. '%m-%d-%Y' . '"'.') AS THEDATE

FROM

  `lots`

  INNER JOIN `wo` ON (`lots`.`LOTNUMBER` = `wo`.`LOT`)

WHERE 

   (

      (lots.LOTNUMBER = "'.$lot.'") AND

      (wo.DUEDATE <= "'.$date.'") AND

      (wo.ID != "'.$woid.'")

   )

ORDER BY wo.DUEDATE DESC

LIMIT 1';

//  echo $query;

  $result=mysql_query($query);

  $num_rows=mysql_num_rows($result);

  $row=mysql_fetch_array($result);

  if (mysql_num_rows($result)>0)

  {

    $gallons['endingtankgallons']=$row['ENDINGTANKGALLONS'];

    $gallons['endingbarrelcount']=$row['ENDINGBARRELCOUNT'];

    $gallons['endingtoppinggallons']=$row['ENDINGTOPPINGGALLONS'];

  }

  return $gallons;

  }

?>

