<html>



<head>

<title>FERMENTATION PROTOCOLS</title>

</head>



<body>



<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111"

  width="100%" id="AutoNumber1">

  <tr>

    <td width="7%" align="center" style="border-bottom-style: solid; border-bottom-width: 1">&nbsp;</td>

    <td width="7%" align="center" style="border-bottom-style: solid; border-bottom-width: 1">&nbsp;</td>

    <td width="28%" colspan="4" align="center" style="border-right-style: solid; border-right-width: 1; border-top-style: solid; border-top-width: 1; border-bottom-style: solid; border-bottom-width: 1">

    <div style="border-left-style: solid; border-left-width: 1; padding-left: 4">

      <b><font face="Franklin Gothic Book">PUMP OVERS</font></b></div>

    </td>

    <td width="28%" colspan="4" align="center" style="border-style: solid; border-width: 1">

    <b><font face="Franklin Gothic Book">PUNCH DOWN #1</font></b></td>

    <td width="30%" colspan="4" align="center" style="border-style: solid; border-width: 1">

    <b><font face="Franklin Gothic Book">PUNCH DOWN #2</font></b></td>

  </tr>

  <tr>

    <td width="7%" style="border-style: solid; border-width: 1"><b>

    <font face="Franklin Gothic Book" size="2">LOT</font></b></td>

    <td width="7%" style="border-style: solid; border-width: 1"><b>

    <font face="Franklin Gothic Book" size="2">DATE</font></b></td>

    <td width="7%" align="center" style="border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-bottom-style: solid; border-bottom-width: 1">

    <b><font face="Franklin Gothic Book" size="2">FREQ</font></b></td>

    <td width="7%" align="center" style="border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-bottom-style: solid; border-bottom-width: 1">

    <b><font face="Franklin Gothic Book" size="2">START<br>

    BRIX</font></b></td>

    <td width="7%" align="center" style="border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-bottom-style: solid; border-bottom-width: 1">

    <b><font face="Franklin Gothic Book" size="2">END<br>

    BRIX</font></b></td>

    <td width="7%" align="center" style="border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-bottom-style: solid; border-bottom-width: 1">

    <b><font face="Franklin Gothic Book" size="2">DURATION</font></b></td>

    <td width="7%" align="center" style="border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-bottom-style: solid; border-bottom-width: 1">

    <b><font face="Franklin Gothic Book" size="2">FREQ</font></b></td>

    <td width="7%" align="center" style="border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-bottom-style: solid; border-bottom-width: 1">

    <b><font face="Franklin Gothic Book" size="2">START<br>

    BRIX</font></b></td>

    <td width="7%" align="center" style="border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-bottom-style: solid; border-bottom-width: 1">

    <b><font face="Franklin Gothic Book" size="2">END<br>

    BRIX</font></b></td>

    <td width="7%" align="center" style="border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-bottom-style: solid; border-bottom-width: 1">

    <b><font face="Franklin Gothic Book" size="2">STRENGTH</font></b></td>

    <td width="7%" align="center" style="border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-bottom-style: solid; border-bottom-width: 1">

    <b><font face="Franklin Gothic Book" size="2">FREQ</font></b></td>

    <td width="7%" align="center" style="border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-bottom-style: solid; border-bottom-width: 1">

    <b><font face="Franklin Gothic Book" size="2">START<br>

    BRIX</font></b></td>

    <td width="8%" align="center" style="border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-bottom-style: solid; border-bottom-width: 1">

    <b><font face="Franklin Gothic Book" size="2">END<br>

    BRIX</font></b></td>

    <td width="8%" align="center" style="border-left-style: solid; border-left-width: 1; border-right-style: solid; border-right-width: 1; border-bottom-style: solid; border-bottom-width: 1">

    <b><font face="Franklin Gothic Book" size="2">STRENGTH</font></b></td>

  </tr>

  <?php



  function blankout($isyes,$value)

  {

     if ($isyes == "Yes" | $isyes == "YES")

       return $value;

     else

       return "";

  }



  if (strlen($_GET['ccode'])>0)

  {

$query='SELECT *, DATE_FORMAT(`fermprot`.`DATE`,'. '"'. '%m-%d-%Y' . '"'.') AS THEDATE

FROM

  fermprot

WHERE

  (fermprot.clientcode = "'.$_GET['ccode'].'")AND

  (fermprot.STATUS = "ACTIVE")';

  }

  else

  {

$query='SELECT *, DATE_FORMAT(`fermprot`.`DATE`,'. '"'. '%m-%d-%Y' . '"'.') AS THEDATE

FROM

  fermprot

WHERE

  (fermprot.STATUS = "ACTIVE")';

  }



  @ $db = mysql_pconnect('localhost', 'root', 'kirby');



  if (!$db)

  {

     echo 'Error: Could not connect to database.  Please try again later.';

     exit;

  }

   mysql_select_db('weightags');

   $result = mysql_query($qu0.ery);

   $num_results = mysql_num_rows($result);



  for ($i=0; $i <$num_results; $i++)

  {

     $row = mysql_fetch_array($result);

     $modstringarg = '?ccode='.$row['CLIENTCODE'].

                     '&lot='.$row['LOT'].

                     '&recid='.$row['id'].

                     '&poyesno='.$row['PO'].

                     '&pdyesno='.$row['PD'].

                     '&pdyesno2='.$row['PD2'].

                     '&pofreq='.$row['POFREQ'].

                     '&postartbrix='.$row['POSTARTBRIX'].

                     '&poduration='.$row['PODURATION'].

                     '&poendbrix='.$row['POENDBRIX'].

                     '&pdstartbrix='.$row['PDSTARTBRIX'].

                     '&pdendbrix='.$row['PDENDBRIX'].

                     '&pdfreq='.$row['PDFREQ'].

                     '&pdstrength='.$row['PDSTRENGTH'].

			         '&pdstartbrix2='.$row['PDSTARTBRIX2'].

                     '&pdendbrix2='.$row['PDENDBRIX2'].

                     '&pdfreq2='.$row['PDFREQ2'].

                     '&pdstrength2='.$row['PDSTRENGTH2'].

                     '&date='.$row['DATE'];

    echo '<tr>';

    echo '<td width="7%" style="border-style: solid; border-width: 1">

    <font face="Franklin Gothic Book" size="2">'. $row['LOT'].'<br>

         <a href=fermdelete.php?recid='.$row['id'].'&ccode='.$row['CLIENTCODE'].'>DEL<a>---<a href=fermprot.php'.$modstringarg.'>MOD<a></font></td>';

    echo '<td width="7%" style="border-style: solid; border-width: 1">

    <font face="Franklin Gothic Book" size="2">'.$row['THEDATE'].'</font></td>';

    echo '<td width="7%" align="center" style="border-style: solid; border-width: 1">

    <font face="Franklin Gothic Book" size="2">'.blankout($row['PO'],$row['POFREQ']).'</font></td>';

    echo '<td width="7%" align="center" style="border-style: solid; border-width: 1">

    <font face="Franklin Gothic Book" size="2">'.blankout($row['PO'],$row['POSTARTBRIX']).'</font></td>';

    echo '<td width="7%" align="center" style="border-style: solid; border-width: 1">

    <font face="Franklin Gothic Book" size="2">'.blankout($row['PO'],$row['POENDBRIX']).'</font></td>';

    echo '<td width="7%" align="center" style="border-style: solid; border-width: 1">

    <font face="Franklin Gothic Book" size="2">'.blankout($row['PO'],$row['PODURATION']).'</font></td>';

    echo '<td width="7%" align="center" style="border-style: solid; border-width: 1">

    <font face="Franklin Gothic Book" size="2">'.blankout($row['PD'],$row['PDFREQ']).'</font></td>';

    echo '<td width="7%" align="center" style="border-style: solid; border-width: 1">

    <font face="Franklin Gothic Book" size="2">'.blankout($row['PD'],$row['PDSTARTBRIX']).'</font></td>';

    echo '<td width="7%" align="center" style="border-style: solid; border-width: 1">

    <font face="Franklin Gothic Book" size="2">'.blankout($row['PD'],$row['PDENDBRIX']).'</font></td>';

    echo '<td width="7%" align="center" style="border-style: solid; border-width: 1">

    <font face="Franklin Gothic Book" size="2">'.blankout($row['PD'],$row['PDSTRENGTH']).'</font></td>';

    echo '<td width="7%" align="center" style="border-style: solid; border-width: 1">

    <font face="Franklin Gothic Book" size="2">'.blankout($row['PD2'],$row['PDFREQ2']).'</font></td>';

    echo '<td width="7%" align="center" style="border-style: solid; border-width: 1">

    <font face="Franklin Gothic Book" size="2">'.blankout($row['PD2'],$row['PDSTARTBRIX2']).'</font></td>';

    echo '<td width="8%" align="center" style="border-style: solid; border-width: 1">

    <font face="Franklin Gothic Book" size="2">'.blankout($row['PD2'],$row['PDENDBRIX2']).'</font></td>' ;

    echo '<td width="8%" align="center" style="border-style: solid; border-width: 1">

    <font face="Franklin Gothic Book" size="2">'.blankout($row['PD2'],$row['PDSTRENGTH2']).'</font></td>' ;

    echo '</tr>';

    }



    echo '</table>';

         echo '<br>

         <br>

         <font face="Franklin Gothic Book" size="2">

         <a href=http://localhost/crushpublic/crushclient/fermprot.php?ccode='.$_GET['ccode'].'>ADD FERMENTATION PROTOCOL<a>';

     ?>

</body>



</html>