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

<body onLoad="document.addtest.value1.focus()">

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

  if ($_GET['action']=="mod")

  {

    $query='UPDATE labresults SET '.

        'labresults.VALUE1="'.$_POST['value1'].'",'.

        'labresults.UNITS1="'.$_POST['units1'].'",'.

        'labresults.LABTESTID="'.$_POST['labtestid'].'",'.

        'labresults.COMMENT="'.$_POST['comment'].'" WHERE (labresults.ID = "'.$_GET['labresultid'].'")';

//    echo $query;

    $result=mysql_query($query);

  }

  if ($_GET['action']=="add")

  {

    $query='INSERT INTO labresults SET labresults.LABTEST="'.$_POST['labtest'].'",'.

        'labresults.VALUE1="'.$_POST['value1'].'",'.

        'labresults.UNITS1="'.$_POST['units1'].'",'.

        'labresults.LABTESTID="'.$_POST['labtestid'].'",'.

        'labresults.COMMENT="'.$_POST['comment'].'"';

//    echo $query;

    $result=mysql_query($query);

  }

  if ($_GET['action']=="del")

  {

    $query='DELETE FROM labresults WHERE labresults.ID="'.$_GET['labresultid'].'"';

    $result=mysql_query($query);

  }



  $wo=getwo($_SESSION['woid']);

  $query='SELECT * FROM labtest WHERE labtest.WOID="'.$_SESSION['woid'].'"';

  $result=mysql_query($query);

  if (mysql_num_rows($result)==0)

  {

     $insertquery='INSERT INTO labtest SET labtest.WOID="'.$_SESSION['woid'].'"';

     $result=mysql_query($insertquery);

     $result=mysql_query($query);

  }

  $row=mysql_fetch_array($result);

  $labtestid=$row['ID'];

  echo '<table align=center width=50% border="1">';

  echo '<tr><td align=center colspan=5><a href=hardcopy/labresults.php?woid='.$_GET['woid'].'>PRINT</a></td></tr>';

  echo '<tr>';

  echo '<td align="center">';

  echo 'DATE: '.$wo['duedate'];

  echo '</td>';

  echo '<td>';

  echo 'LOT: <a href=showlotinfo.php?lot='.$wo['lot'].'>'.$wo['lot'].'</a>';

  echo '</td>';

  echo '<td align="center">';

  echo 'LAB TEST #:'.$row['ID'].'<br>';

  echo '</td>';

  echo '<td>';

  echo 'WO: '.'<a href=wopage.php?action=view&woid='.$_SESSION['woid'].'>'.$_SESSION['woid'].'</a>';

  echo '</td>';

  echo '<td>';

  echo 'LAB: '.DrawComboFromEnum ("labtest","lab",$_SESSION['lab'],"lab");

  echo '</td>';

  echo '</table>';

  echo '<table align=center width=50% border="1">';

  echo '<tr><td></td><td>LAB TEST</td><td align=right>RESULT</td><td align=right>UNITS</td><td align=center>COMMENTS</td></tr>';

  $query='SELECT * FROM `labtest`

     INNER JOIN `labresults` ON (`labtest`.`ID` = `labresults`.`LABTESTID`) WHERE labtest.ID="'.$row['ID'].'"';

  $result=mysql_query($query);

  $num_rows=mysql_num_rows($result);

  for ($i=0;$i<$num_rows;$i++)

  {

    $row=mysql_fetch_array($result);

    echo '<tr>';

      echo '<td>'.'<a href='.$PHP_SELF.'?action=del&woid='.$_SESSION['woid'].'&labresultid='.$row['ID'].'>del</a></td>';

      echo '<form method="POST" action="'.$PHP_SELF.'?action=mod&woid='.$_SESSION['woid'].'&labresultid='.$row['ID'].'">';

      echo '<td>'.$row['LABTEST'].'</td>';

      echo '<td>'.'<input type=textbox name="value1" value="'.$row['VALUE1'].'" size=7>'.'</td>';

      echo '<td>'.'<input type=textbox name="units1" value="'.$row['UNITS1'].'" size=7>'.'</td>';

      echo '<td>'.'<textarea name="comment" cols=50>'.$row['COMMENT'].'</textarea>'.'</td>';

      echo '<input type=hidden value='.$labtestid.' name="labtestid">';

      echo '<td>'.'<input type=submit value=mod></td></form>';

    echo '</tr>';

  }

      echo '<tr>';

      echo '<td></td>';

      echo '<form name=addtest method="POST" action="'.$PHP_SELF.'?action=add&woid='.$_SESSION['woid'].'">';

      echo '<td>'.DrawComboFromEnum("labresults","LABTEST","","labtest").'</td>';

      echo '<td>'.'<input type=textbox name="value1" size=7>'.'</td>';

      echo '<td>'.'<input type=textbox name="units1" size=7>'.'</td>';

      echo '<td>'.'<textarea name="comment" cols=50></textarea>'.'</td>';

      echo '<input type=hidden value='.$labtestid.' name="labtestid">';

      echo '<td>'.'<input type=submit value=add></td></form>';

    echo '</tr>';



  echo '<tr><td>';

  echo '</table>';

?>



</body>



</html>

