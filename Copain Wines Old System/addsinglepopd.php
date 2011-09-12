<?php

include("startdb.php");

include("queryupdatefunctions.php");

session_start();

  ?>

  <html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

</head>



<body>



<?php

 include ("yesno.php");

 include ("setcheck.php");

 include ("defaultvalue.php");

 function DrawCombo($value,$vintage,$name, $clientcode="")

{

$query = 'SELECT DISTINCT

  `fermprot`.`lot`,

  `fermprot`.`VESSELTYPE`,

  `fermprot`.`VESSELID`,

  `fermprot`.`CLIENTCODE`,

  `fermprot`.`STATUS`

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

  $result=mysql_query($query);



    if ($hasvalue==0)

      $items="<option value=\"---\"</option>\n";



    $result=mysql_query($query);

    for ($i=0;$i<mysql_num_rows($result);$i++)

    {

        $row=mysql_fetch_array($result);

        $vessel=$row['lot'].' '.$row['VESSELTYPE'].'-'.$row['VESSELID'];



        if ($vessel==$value)

        $items.="<option selected value=\"".$vessel."\">".ucfirst($vessel)."</option>\n";

        else

        $items.="<option value=\"".$vessel."\">".ucfirst($vessel)."</option>\n";

    }

    $text='<select name="'.$name.'">\n';

    $text.=$items;

    $text.="</select>\n\n";

    return $text;

    

}



 if ($type =="PO"){



   echo '<table border="1" align="center">';

   echo '<form method="POST" action="dailypopdsheet.php?action=addrec">';

   echo '<tr><td align="center" colspan="6"><b>ADD SINGLE PUMP OVER</b></td></tr>';

   echo '<tr><td align="center">LOT/VESSEL</td>

   <td align="center">DATE</td>

   <td align="center">TIMESLOT</td>

   <td align="center">DURATION</td></tr>';

   echo '<tr>';

//   echo '<td align="center"><input type="text" name="LOT" size="8"></td>';

   echo '<td align="center">'.DrawCombo("","","LOTVESSEL","").'</td>';

//   echo '<td align="center"><input type="text" name="VESSELTYPE" size="4"></td>';

//   echo '<td align="center"><input type="text" name="VESSELID" size="3"></td>';

   echo '<td align="center"><input type="text" name="DATE" size="8" value='.$_GET['date'].'></td>';

   echo '<td align="center"><input type="text" name="TIMESLOT" value="'.$_GET['timeslot'].'" size="8" value='.$_GET['timeslot'].'></td>';

   echo '<td align="center"><input type="text" name="PODURATION" size="5"</td>';

   echo '<input type="hidden" name="TYPE" value="PUMP OVER">';

   echo '</tr>';

   echo '<tr>';

   echo '<td align="center" colspan="9"><input type="submit" name="B1" value="Submit"></td></tr>';

   echo '</table>';

 }

 else

 {

   echo '<table border="1" align="center">';

   echo '<form method="POST" action="dailypopdsheet.php?action=addrec">';

   echo '<tr><td align="center" colspan="6"><b>ADD SINGLE PUNCH DOWN</b></td></tr>';

   echo '<tr><td align="center">LOT/VESSEL</td>

   <td align="center">DATE</td>

   <td align="center">STRENGTH</td>

   <td align="center">TIMESLOT</td>';

   echo '<tr>';

//   echo '<td align="center"><input type="text" name="LOT" size="8"></td>';

   echo '<td align="center">'.DrawCombo("","","LOTVESSEL","").'</td>';

//   echo '<td align="center"><input type="text" name="VESSELTYPE" size="4"></td>';

//   echo '<td align="center"><input type="text" name="VESSELID" size="3"></td>';

   echo '<td align="center"><input type="text" name="DATE" size="8" value='.$_GET['date'].'></td>';

   echo '<td align="center"><input type="text" name="STRENGTH" size="8" value=MEDIUM></td>';

   echo '<td align="center"><input type="text" name="TIMESLOT" value="'.$_GET['timeslot'].'" size="8" value='.$_GET['timeslot'].'></td>';

   echo '<input type="hidden" name="TYPE" value="PUNCH DOWN">';

   echo '</tr>';

   echo '<tr>';

   echo '<td align="center" colspan="9"><input type="submit" name="B1" value="Submit"></td></tr>';

   echo '</table>';



  }



?>



</body>



</html>