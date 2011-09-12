<?php

    session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

    <script language="JavaScript" type="text/javascript">

    function navigate(choice)

    {

      var url=choice.options[choice.selectedIndex].value;

      if (url)

      {

        location.href=url;

      }

    }

    function navigatechecked(choice)

    {

      var url=choice.value;

      if (choice.checked)

         url=url+"YES";

      else

         url=url+"NO";

      if (url)

      {

        location.href=url;

      }

    }

  </script>

</head>



<body>



<?php

  include ("startdb.php");

  include ("queryupdatefunctions.php");

  include ("assetfunctions.php");

  include ("totalgallons.php");

  if ($_GET['action']=="setclient")

    $_SESSION['clientcode']=$_GET['clientcode'];

  if ($_GET['action']=="clearclient")

    $_SESSION['clientcode']="";

  if ($_GET['lot'])

    $_SESSION['lot']=$_GET['lot'];

  if ($_GET['woid'])

    $_SESSION['woid']=$_GET['woid'];

  if ($_GET['action']=='modbottling')

  {

  	$query='UPDATE bottling SET '.

  	   'LABELAPPROVAL="'.$_POST['labelapproval'].'",'.

  	   'GALLONSPERCASE="'.$_POST['gallonspercase'].'",'.

  	   'ESTCASECOUNT="'.$_POST['estcasecount'].'",'.

  	   'FINALCASECOUNT="'.$_POST['finalcasecount'].'" WHERE ID="'.$_POST['bottlingid'].'"';

  	//   echo $query;

  	   mysql_query($query);

  }

  

    if ($_GET['action']=="mod")

  {

    $query='UPDATE filllevels SET '.

        'TIME="'.date("Y-m-d d:i",strtotime($_POST['thetime'])).'",'.

        'AMOUNT="'.$_POST['theamount'].'",'.

        'CORRECTION="'.$_POST['thecorrection'].'",'.

        'CORRECTIONTIME="'.date("Y-m-d h:i",strtotime($_POST['thecorrectiontime'])).'" WHERE (filllevels.ID = "'.$_GET['filllevelsid'].'")';

//    echo $query;

    $result=mysql_query($query);

  }

  if ($_GET['action']=="add")

  {

    $query='INSERT INTO filllevels SET BOTTLINGID="'.$_POST['bottlingid'].'",'.

        'TIME="'.date("Y-m-d h:i",strtotime($_POST['thetime'])).'",'.

        'AMOUNT="'.$_POST['theamount'].'",'.

        'CORRECTION="'.$_POST['thecorrection'].'",'.

        'CORRECTIONTIME="'.date("Y-m-d h:i",strtotime($_POST['thecorrectiontime'])).'"';

 //          echo $query;

    $result=mysql_query($query);

  }

  if ($_GET['action']=="del")

  {

    $query='DELETE FROM filllevels WHERE filllevels.ID="'.$_GET['filllevelsid'].'"';

    $result=mysql_query($query);

  }



  $wo=getwo($_SESSION['woid']);

  $query='SELECT * FROM bottling WHERE WOID="'.$_SESSION['woid'].'"';

  $result=mysql_query($query);

  if (mysql_num_rows($result)==0)

  {

     $insertquery='INSERT INTO bottling SET WOID="'.$_SESSION['woid'].'"';

     $result=mysql_query($insertquery);

     $result=mysql_query($query);

  }

  $row=mysql_fetch_array($result);

  $bottlingid=$row['ID'];

  echo '<table align=center width=80% border="1">';

  echo '<tr><td align=center colspan=5><a href=hardcopy/bottlingreport.php?woid='.$_SESSION['woid'].'>PRINT</a></td></tr>';

  echo '<tr>';

  echo '<td align="center">';

  echo 'DATE: '.date("m/d/Y",strtotime($wo['duedate']));

  echo '</td>';

  echo '<td align=center>';

  echo 'LOT: <a href=showlotinfo.php?lot='.$wo['lot'].'>'.$wo['lot'].'</a>';

  echo '</td>';

  echo '<td align="center">';

  echo 'BOTTLING REPORT #:'.$row['ID'].'<br>';

  echo '</td>';

  echo '<td align=center>';

  echo 'WO: '.'<a href=wopage.php?action=view&woid='.$_SESSION['woid'].'>'.$_SESSION['woid'].'</a>';

  echo '</td>';

  echo '</tr>';

  echo '<form method=post action='.$PHP_SELF.'?action=modbottling>';

  echo '<tr>';

  echo '<td align="center">';

  echo 'LABEL APPROVAL #: <input type=textbox size=7 name=labelapproval value="'.$row['LABELAPPROVAL'].'">';

  echo '</td>';

  echo '<td align="center">';

  echo 'EST CASE COUNT: <input type=textbox size=7 name=estcasecount value="'.$row['ESTCASECOUNT'].'">';

  echo '</td>';

  echo '<td align="center">';

  echo 'FINAL CASE COUNT: <input type=textbox size=7 name=finalcasecount value="'.$row['FINALCASECOUNT'].'">';

  echo '</td>';

  echo '<td align="center">';

  if ($row['GALLONSPERCASE']==0)

  echo 'GALLONS PER CASE: <input type=textbox size=4 name=gallonspercase value="2.3775">';

  else

  echo 'GALLONS PER CASE: <input type=textbox size=4 name=gallonspercase value="'.number_format($row['GALLONSPERCASE'],4).'">';

  echo '</td>';

  echo '<td align="center">';

  echo '<input type=hidden name=bottlingid value='.$bottlingid.'>';

  echo '<input type=submit name=b1 value="UPDATE">';

  echo '</td>';

  echo '</tr>';

  echo '</form>';

  echo '</table>';

  echo '<table align=center width=50% border="1">';

  echo '<tr><td></td><td  align=center>TIME</td><td  align=center>AMOUNT</td><td  align=center>CORRECTION</td><td align=center>CORRECTION TIME</td></tr>';

  $query='SELECT * FROM `bottling`

     INNER JOIN `filllevels` ON (`bottling`.`ID` = `filllevels`.`BOTTLINGID`) WHERE bottling.ID="'.$row['ID'].'"';

  $result=mysql_query($query);

  $num_rows=mysql_num_rows($result);

  for ($i=0;$i<$num_rows;$i++)

  {

    $row=mysql_fetch_array($result);

    echo '<tr>';

      echo '<td align=center>'.'<a href='.$PHP_SELF.'?action=del&woid='.$_SESSION['woid'].'&filllevelsid='.$row['ID'].'>del</a></td>';

      echo '<form method="POST" action="'.$PHP_SELF.'?action=mod&woid='.$_SESSION['woid'].'&filllevelsid='.$row['ID'].'">';

      echo '<td align=center>'.'<input type=textbox name="thetime" value="'.date("h:i A",strtotime($row['TIME'])).'" size=7>'.'</td>';

      echo '<td align=center>'.'<input type=textbox name="theamount" value="'.$row['AMOUNT'].'" size=7>'.'</td>';

      echo '<td align=center>'.DrawComboFromEnum("filllevels","CORRECTION",$row['CORRECTION'],"thecorrection").'</td>';

      echo '<td align=center>'.'<input type=textbox name="thecorrectiontime" value="'.date("h:i A",strtotime($row['CORRECTIONTIME'])).'" size=7>'.'</td>';

      echo '<input type=hidden value='.$row['ID'].' name="filllevelsid">';

      echo '<td align=center>'.'<input type=submit value=mod></td></form>';

    echo '</tr>';

  }

      echo '<tr>';

      echo '<td></td>';

      echo '<form method="POST" action="'.$PHP_SELF.'?action=add&woid='.$_SESSION['woid'].'">';

      echo '<td align=center>'.'<input type=textbox name="thetime" value="'.date("h:i A",time()).'" size=7>'.'</td>';

      echo '<td align=center>'.'<input type=textbox name="theamount" size=7>'.'</td>';

      echo '<td align=center>'.DrawComboFromEnum("filllevels","CORRECTION","","thecorrection").'</td>';

      echo '<td align=center>'.'<input type=textbox name="thecorrectiontime" value="'.date("h:i A",time()).'" size=10>'.'</td>';

      echo '<input type=hidden value='.$bottlingid.' name="bottlingid">';

      echo '<td align=center>'.'<input type=submit value=add></td></form>';

    echo '</tr>';



  echo '<tr><td>';

  echo '</table>';

?>



</body>



</html>

