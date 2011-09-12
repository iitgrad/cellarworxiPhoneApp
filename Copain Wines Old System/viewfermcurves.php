<html>



<head>

  <title></title>

<link rel="stylesheet" type="text/css" href="../site.css">

</head>



<body>

<?php



include ("startdb.php");



if ($_GET['modification']=="DEL")

{

    $query='DELETE FROM brixtemp WHERE ID='.$_GET['recid'];

    $result = mysql_query($query);

}



if ($_GET['modification']=="ADD")

{

   if ($_POST['temp']<45)

      $thetemp = $_POST['temp']*9/5+32;

   else

      $thetemp = $_POST['temp'];

   $query = 'INSERT INTO brixtemp SET LOT="'.$_GET['lot'].'",

       DATE="'.$_POST['Date'].'",

       vessel="'.$_GET['vessel'].'",

       vesseltype="'.$_GET['vesseltype'].'",

       BRIX='.$_POST['brix'].',

       temp='.$thetemp.';';



   $result = mysql_query($query);



}

if ($_GET['modification']=="MOD")

{

   $query = 'UPDATE brixtemp SET brixtemp.brix='.$_POST['brix'].',

    brixtemp.temp='.$_POST['temp'].' WHERE (brixtemp.id="'.$_GET['recid'].'")';

   echo $query;

//    mysql_query($query);

}



$query = 'SELECT DISTINCT

  `brixtemp`.`id`,

  `brixtemp`.`lot`,

  `brixtemp`.`vessel`,

  `brixtemp`.`vesseltype`,

  `brixtemp`.`BRIX`,

  `brixtemp`.`temp`,

  `fermprot`.`CLIENTCODE`,

  DATE_FORMAT(`brixtemp`.`DATE`,'. '"'. '%m-%d-%Y' . '"'.') AS THEDATE

FROM

  `fermprot`

  INNER JOIN `brixtemp` ON (`fermprot`.`LOT` = `brixtemp`.`lot`)

WHERE

  (`brixtemp`.`lot` = "'.$_GET['lot'].'" AND

   `brixtemp`.`vessel` = "'.$_GET['vessel'].'" AND

   `brixtemp`.`vesseltype` = "'.$_GET['vesseltype'].'")

ORDER BY

  `brixtemp`.`DATE`';

  $result = mysql_query($query);

   $num_results = mysql_num_rows($result);





  echo ' <table width="100%" border="1" >

    <tr><td width="100%" align="center" colspan="3"><b>';

       echo '<a href=showlotinfo.php?lot='.$_GET['lot'].'>'.$_GET['lot'].'</a>  '.$_GET['vesseltype'].'  '.$_GET['vessel'].'

    </b><br></td>

    </tr>

    <tr>

      <td align="center">';

        echo '<table width="100%" align="center" border="1" bordercolor="#111111" >';

           echo '<tr>';

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

           echo '<tr>';

           echo    '<td align="center">';

           echo        $row['THEDATE'];

           echo    '</td>';

           echo    '<td align="center">';

           echo        $row['BRIX'];

           echo    '</td>';

           echo    '<td align="center">';

           echo        $row['temp'];

           echo    '</td>';

           if ($_GET['allowadd']=="TRUE")

           {

             echo    '<td align="center">';

             echo        '<a href=viewfermcurves.php?lot='.$_GET['lot'].

                '&vesseltype='.$_GET['vesseltype'].

                '&allowadd=FALSE'.

                '&vessel='.$_GET['vessel'].

                '&modification=MOD'.

                '&recid='.$row['id'].'>mod</a>';

             echo '/';

             echo        '<a href=viewfermcurves.php?lot='.$_GET['lot'].

                '&vesseltype='.$_GET['vesseltype'].

                '&allowadd=TRUE'.

                '&vessel='.$_GET['vessel'].

                '&modification=DEL'.

                '&recid='.$row['id'].'>del</a>';

             echo    '</td>';

           }

           echo '</tr>';

         }

         echo '<tr></tr><tr>';



      if ($_GET['allowadd']=="TRUE")

     {

     echo '<form method="POST" action=viewfermcurves.php?lot='.

           $_GET['lot'].'&vesseltype='.$_GET['vesseltype'].'&allowadd=TRUE&vessel='.$_GET['vessel'].'&modification=ADD>';

           echo    '<td align="center">';

           echo        '<input type="text" name="Date" value="'.date("Y-m-d",time()).'" size="10" tabindex="1">';

           echo    '</td>';

           echo    '<td align="center">';

           echo        '<input type="text" name="brix" size="10" tabindex="2">';

           echo    '</td>';

           echo    '<td align="center">';

           echo        '<input type="text" name="temp" size="10" tabindex="3">';

           echo    '</td>';

           echo '</tr>';

           echo '<tr></tr><tr>';

           echo '<form method="POST" action=addfermdatapoint.php>';

           echo    '<td align="center">';

           echo    '</td>';

           echo    '<td align="center">';

           echo        '<input type="submit" value="Add Data Point" name="B1">';

           echo    '</td>';

           echo    '<td align="center">';

           echo    '</td>';

           echo '</tr>';

     }



   echo '</table>  </td>';

   //Add remove graph function

   $httprefgraphoff='viewfermcurves.php?lot='.$_GET['lot'].'&vesseltype='.$_GET['vesseltype'].'&allowadd=TRUE&vessel='.$_GET['vessel'].'&graphon=OFF>';

   $httprefgraphon='viewfermcurves.php?lot='.$_GET['lot'].'&vesseltype='.$_GET['vesseltype'].'&allowadd=TRUE&vessel='.$_GET['vessel'].'&graphon=ON>';

   $graphon=$_GET['graphon'];

   if($graphon == "ON")

      echo '<td align="center"><a href='.$httprefgraphoff.'<-Graph Off</a></td>';

   else

      echo '<td align="center"><a href='.$httprefgraphon.'>Graph On-></a></td>';

       if ($graphon == "ON")

       {

           echo '<td align="center"> <img src="graphbrixtemp.php?lot='.

           $_GET['lot'].'&vesseltype='.$_GET['vesseltype'].'&addpoint=TRUE&vessel='.$_GET['vessel'].'" border=0 align =center width=600 height= 400></td> ';

         }

   echo '</tr>

   </table><br><br>   ';

   echo '<center><a href=activefermsbyclient.php?ccode='.$row['CLIENTCODE'].'>ACTIVE FERMENTATION LIST</a>';

?>

</font>

</body>



</html>

