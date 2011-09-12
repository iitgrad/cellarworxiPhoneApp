<?php
    session_start();
?>
<html>

<head>
  <title></title>
  <link rel="stylesheet" type="text/css" href="../site.css">
    <script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>
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
    $_SESSION['clientcode']='';

  if ($_GET['action']=="del")
  {
     $query='DELETE FROM bol WHERE bol.ID="'.$_GET['bolid'].'"'; 
	 $result=mysql_query($query);
  }

  if ($_GET['action']=="addlot")
  {
    $query='INSERT INTO lots SET lots.LOTNUMBER="'.$_POST['lotnumber'].'",'.
	  'lots.DESCRIPTION="'.$_POST['description'].'",'.
	  'lots.YEAR="'.$_POST['year'].'",'.
	  'lots.CLIENTCODE="'.$_POST['clientcode'].'"';
//	echo $query;
	$result=mysql_query($query);
  }
	
 echo '<table width=70% align=center><tr><td align=center><big><b>BILL OF LADING MANAGEMENT PAGE  ';
 echo '</b></big></td></tr><tr>';

  echo '</tr></table>';
  $cc=$_SESSION['clientcode'];
 
  if ($cc!="")
  {
    echo '<table id=bol width=60% align=center><tr valign=bottom><td></td><td align=center>BILL OF LADING<br>NUMBER<hr></td><td align=center>DATE<hr></td><td align=center>DIRECTION<hr></td><td align=center>NAME<hr></td></tr><tr>';
	$query='SELECT * FROM bol WHERE CLIENTCODE="'.$cc.'"';
	$result=mysql_query($query);
	for ($i=0; $i<mysql_num_rows($result); $i++)
	{
	  $row=mysql_fetch_array($result);
	  echo '<tr><td align=center width=10%>';
	  echo '<a href=bolmgt.php?action=del&bolid='.$row['ID'].'>del</a></td><td align=center>';
	  echo '<a width=10% href=bolpage.php?bolid='.$row['ID'].'>'.$row['ID'].'</a>';
	  echo '<td align=center width=10%>'.date("m/d/Y",strtotime($row['DATE'])).'</td>';
	  echo '<td align=center width=10%>'.$row['DIRECTION'].'</td>';
	  echo '<td align=center>'.$row['NAME'].'</td>';
	  echo '</tr>';
	}
    
    echo '</table>';  
  ?>
<script language="JavaScript">
<!--
tigra_tables('bol', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');
// -->
			</script>
<?php
  }
  echo '<br><table align=center width=50%>';
  echo '<td align=center><a href="bolpage.php?action=new">CREATE BILL OF LADING</a></td>';
  echo '</table>';

?>

</body>

</html>
