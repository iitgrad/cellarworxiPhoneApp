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

  if ($_GET['action']=="mod")

  {

    $query='UPDATE blenditems SET '.

        'blenditems.GALLONS="'.$_POST['gallons'].'",'.

        'blenditems.BLENDID="'.$_POST['blendid'].'",'.

        'blenditems.COMMENT="'.$_POST['comment'].'" WHERE (blenditems.ID = "'.$_GET['blenditemid'].'")';

 //   echo $query;

    $result=mysql_query($query);

  }

  if ($_GET['action']=="add")

  {

    $query='INSERT INTO blenditems SET blenditems.DIRECTION="'.$_POST['direction'].'",'.

        'blenditems.SOURCELOT="'.$_POST['sourcelot'].'",'.

        'blenditems.GALLONS="'.$_POST['gallons'].'",'.

        'blenditems.BLENDID="'.$_POST['blendid'].'",'.

        'blenditems.COMMENT="'.$_POST['comment'].'"';

    //echo $query;

    $result=mysql_query($query);

  }

  if ($_GET['action']=="del")

  {

    $query='DELETE FROM blenditems WHERE blenditems.ID="'.$_GET['blenditemsid'].'"';

    $result=mysql_query($query);

  }



  $wo=getwo($_SESSION['woid']);

  $query='SELECT * FROM blend WHERE blend.WOID="'.$_SESSION['woid'].'"';

  $result=mysql_query($query);

  if (mysql_num_rows($result)==0)

  {

     $insertquery='INSERT INTO blend SET blend.WOID="'.$_SESSION['woid'].'"';

     $result=mysql_query($insertquery);

     $result=mysql_query($query);

  }

  $row=mysql_fetch_array($result);



  $blendid=$row['ID'];

  echo '<table align=center width=50% border="1">';

  echo '<tr>';

  echo '<td align="center">';

  echo 'DATE: '.$wo['duedate'];

  echo '</td>';

  echo '<td>';

  echo 'LOT: <a href=showlotinfo.php?lot='.$wo['lot'].'>'.$wo['lot'].'</a>';

  echo '</td>';

  echo '<td align="center">';

  echo 'BLEND #:'.$row['ID'].'<br>';

  echo '</td>';

  echo '<td>';

  echo 'WO: '.'<a href=wopage.php?action=view&woid='.$_SESSION['woid'].'>'.$_SESSION['woid'].'</a>';

  echo '</td>';

  echo '</table>';

  echo '<table align=center width=50% border="1">';

  echo '<tr><td></td><td>DIRECTION</td><td align=center>LOT</td><td align=right>GALLONS</td><td align=center>COMMENTS</td></tr>';

  $query='SELECT * FROM `blend`

     INNER JOIN `blenditems` ON (`blend`.`ID` = `blenditems`.`BLENDID`) WHERE blend.ID="'.$row['ID'].'"';

  $result=mysql_query($query);

  $num_rows=mysql_num_rows($result);

  for ($i=0;$i<$num_rows;$i++)

  {

    $row=mysql_fetch_array($result);

    echo '<tr>';

      echo '<td>'.'<a href='.$PHP_SELF.'?action=del&woid='.$_SESSION['woid'].'&blenditemsid='.$row['ID'].'>del</a></td>';

      echo '<form method="POST" action="'.$PHP_SELF.'?action=mod&woid='.$_SESSION['woid'].'&blenditemid='.$row['ID'].'">';

      echo '<td align=center>'.$row['DIRECTION'].'</td>';

      echo '<td align=center><a href=showlotinfo.php?lot='.$row['SOURCELOT'].'>'.$row['SOURCELOT'].'</a></td>';

      echo '<td>'.'<input type=textbox name="gallons" value="'.$row['GALLONS'].'" size=7>'.'</td>';

      echo '<td>'.'<textarea name="comment" cols=50>'.$row['COMMENT'].'</textarea>'.'</td>';

      echo '<input type=hidden value='.$blendid.' name="blendid">';

      echo '<td>'.'<input type=submit value=mod></td></form>';

    echo '</tr>';

  }

      echo '<tr>';

      echo '<td></td>';

      echo '<form method="POST" action="'.$PHP_SELF.'?action=add&woid='.$_SESSION['woid'].'">';

      echo '<td>'.DrawComboFromEnum("blenditems","DIRECTION","","direction").'</td>';

      echo '<td>'.DrawComboFromData("lots","LOTNUMBER","","sourcelot","CLIENTCODE",clientid($wo['clientcode'])).'</td>';

//      echo '<td>'.'<input type=textbox name="sourcelot" size=7>'.'</td>';

      echo '<td>'.'<input type=textbox name="gallons" size=7>'.'</td>';

      echo '<td>'.'<textarea name="comment" cols=50></textarea>'.'</td>';

      echo '<input type=hidden value='.$blendid.' name="blendid">';

      echo '<td>'.'<input type=submit value=add></td></form>';

    echo '</tr>';



  echo '<tr><td>';

  echo '</table>';

?>



</body>



</html>

