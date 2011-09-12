<?php



function getbrixtemp($lot, $vesseltype, $vesselid, $date)

{

  $query = 'SELECT `brixtemp`.`BRIX`, `brixtemp`.`temp`   FROM

  `brixtemp` WHERE

  (`brixtemp`.`vessel` = "'.$vesselid.'" AND

   `brixtemp`.`vesseltype` = "'.$vesseltype.'" AND

   `brixtemp`.`lot` = "'.$lot.'" AND

   `brixtemp`.`DATE` = "'.$date.'")';

//   echo $query;



   $result=mysql_query($query);

   $row_nums = mysql_num_rows($result);

   $row = mysql_fetch_array($result);

//   echo 'brix...'.$row['BRIX']   ;

   return array('brix'=>$row['BRIX'],'temp'=>$row['temp']);

}

function nb($value)

{

   if ($value !== "")

     return $value;

   else

     return "&nbsp;";

}

?>