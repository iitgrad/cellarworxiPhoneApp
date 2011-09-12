<?php

  session_start();

?>

<html>



<head>

  <title></title>

<link rel="stylesheet" type="text/css" href="../site.css">

      <script type="text/javascript" src="popup/overlibmws.js"></script>

   <script type="text/javascript" src="popup/overlibmws_bubble.js"></script>



</head>



<body>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div> 

<?php



include ("startdb.php");



function filter($thestring)

{

	return preg_replace("/'/","",preg_replace("/[\n\t\r]+/","",$thestring));

}





$query = 'SELECT DISTINCT

  `fermprot`.`lot`,

  `fermprot`.`VESSELTYPE`,

  `fermprot`.`VESSELID`,

  `fermprot`.`CLIENTCODE`,

  `fermprot`.`STATUS`,

   lots.DESCRIPTION

FROM

  `fermprot`

  LEFT OUTER JOIN `brixtemp` ON (`fermprot`.`LOT` = `brixtemp`.`lot`)

  LEFT OUTER JOIN lots ON (fermprot.LOT = lots.LOTNUMBER)

WHERE

  (`fermprot`.`CLIENTCODE` = "'.$_SESSION['clientcode'].'") AND

  (lots.YEAR='.$_SESSION['vintage'].') AND

  (fermprot.STATUS="ACTIVE")

ORDER BY

  `fermprot`.`lot`';

//echo $_SESSION['clientcode'];



   $result = mysql_query($query);

   $num_results = mysql_num_rows($result);





  echo ' <font face="Franklin Gothic Book" size="2">';

        echo '<table border=1 width="70%" align="center">';

           echo '<tr>';

           echo    '<td align="center">';

           echo        "<b>"." "."</b>";

           echo    '</td>';

           echo    '<td align="center">';

           echo        "<b>".'LOT#'."</b>";

           echo    '</td>';

           echo    '<td align="center">';

           echo        "<b>".'VESSEL'."</b>";

           echo    '</td>';

           echo    '<td align="center">';

           echo        "<b>".'DATE'."</b>";

           echo    '</td>';

           echo    '<td align="center">';

           echo        "<b>".'BRIX'."</b>";

           echo    '</td>';

           echo    '<td align="center">';

           echo        "<b>".'TEMP'."</b>";

           echo    '</td>';

           echo '</tr>';

        for ($i=0; $i <$num_results; $i++)

        {

           $row=mysql_fetch_array($result);



           $query2 = 'SELECT DISTINCT `brixtemp`.`id`, `brixtemp`.`lot`, `brixtemp`.`vessel`, `brixtemp`.`vesseltype`, `brixtemp`.`BRIX`, `brixtemp`.`temp`, `fermprot`.`CLIENTCODE`,

  DATE_FORMAT(`brixtemp`.`DATE`,'. '"'. '%m-%d-%Y' . '"'.') AS THEDATE FROM `fermprot` 

            INNER JOIN `brixtemp` ON (`fermprot`.`LOT` = `brixtemp`.`lot`)

WHERE

  (`brixtemp`.`lot` = "'.$row['lot'].'" AND

   `brixtemp`.`vessel` = "'.$row['VESSELID'].'" AND

   `brixtemp`.`vesseltype` = "'.$row['VESSELTYPE'].'")

ORDER BY

  `brixtemp`.`DATE` DESC LIMIT 1';

           $result2=mysql_query($query2);



           $row2=mysql_fetch_array($result2);



           $thelink = 'viewfermcurves.php?allowadd=TRUE&lot='.$row['lot'].

                   '&vesseltype='.$row['VESSELTYPE'].'&vessel='.$row['VESSELID'];



           echo '<tr>';

           echo    '<td align="center">';

           echo        '<a href='.$thelink.'>HISTORY</a>';

           echo    '</td>';

           echo    '<td align="center">';

  //         echo        '<a href=showlotinfo.php?lot='.$row['lot'].'>'.$row['lot'].'</a>';

           		echo '<a href=showlotinfo.php?lot='.$row['lot'].'

                onmouseover="return overlib(\''.filter($row['DESCRIPTION']).'\',BUBBLE,BUBBLETYPE,\'quotation\');" 

                onmouseout="nd();">'.$row['lot'].'</a><br>';



           echo    '</td>';

           echo    '<td align="center">';

           echo        $row['VESSELTYPE'].'-'.$row['VESSELID'];

           echo    '</td>';

           echo    '<td align="center">';

           echo        $row2['THEDATE'];

           echo    '</td>';

           echo    '<td align="center">';

           echo        $row2['BRIX'];

           echo    '</td>';

           echo    '<td align="center">';

           echo        $row2['temp'];

           echo    '</td>';

           echo '</tr>';

         }

   echo '</table> ';

?>



</body>



</html>