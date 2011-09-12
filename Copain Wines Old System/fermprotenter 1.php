<html>



<head>

<title>Fermentation Protocol</title>

</head>



<body>

<?php

   include("startdb.php");

   function zeroblank($value)

   {

      if ($value == "")

        return 0;

      else

        return $value;

   }

   function yesno($value)

   {

      if (isset($value))

      {

         return '"YES"';

      }

      else

      {

         return '"NO"';

      }

   }



   $thedate = time();

   $lot='"'.$_REQUEST['LOT'].'"';

   $poyesno = yesno($_POST['POYESNO']);

   $pdyesno = yesno($_POST['PDYESNO']);

   $pdyesno2 = yesno($_POST['PDYESNO2']);





   $poduration = zeroblank($_REQUEST['PODURATION']);

   $postartbrix = zeroblank($_REQUEST['POSTARTBRIX']);

   $poendbrix = zeroblank($_REQUEST['POENDBRIX']);

   $pofreq = zeroblank($_REQUEST['POFREQ']);



   $pdstartbrix = zeroblank($_REQUEST['PDSTARTBRIX']);

   $pdendbrix = zeroblank($_REQUEST['PDENDBRIX']);

   $pdfrequency = zeroblank($_REQUEST['PDFREQUENCY']);

   $pdstrength = '"'.$_REQUEST['PDSTRENGTH'].'"';



   $pdstartbrix2 = zeroblank($_REQUEST['PDSTARTBRIX2']);

   $pdendbrix2 = zeroblank($_REQUEST['PDENDBRIX2']);

   $pdfrequency2 = zeroblank($_REQUEST['PDFREQUENCY2']);

   $pdstrength2 = '"'.$_REQUEST['PDSTRENGTH2'].'"';

   $thedate = '"'.date('F j Y').'"';



$query='INSERT INTO FERMPROT SET LOT='.$lot.',

       DATE="'.date("Y-m-d H:i:s").'",

       PO='.$poyesno.',

       PD='.$pdyesno.',

       PD2='.$pdyesno2.',



       PODURATION='.$poduration.',

       POSTARTBRIX='.$postartbrix.',

       POENDBRIX='.$poendbrix.',

       POFREQ='.$pofreq.',



       PDFREQ='.$pdfrequency.',

       PDSTARTBRIX='.$pdstartbrix.',

       PDENDBRIX='.$pdendbrix.',

       PDSTRENGTH='.$pdstrength.',



       PDFREQ2='.$pdfrequency2.',

       PDSTARTBRIX2='.$pdstartbrix2.',

       PDENDBRIX2='.$pdendbrix2.',

       PDSTRENGTH2='.$pdstrength2.',



       CLIENTCODE="'.$_GET['ccode'].'",

       STATUS="ACTIVE"';



   mysql_select_db('weightags');

   $result = mysql_query($query);

   if ($result)

      echo "Record added ok<br>";

   else

      echo "Record NOT Added!<br>";

   echo "<a href=//localhost/crushpublic/crushclient/showferms.php?ccode=".$_GET['ccode'].">Continue </a>";



?>



</body>



</html>