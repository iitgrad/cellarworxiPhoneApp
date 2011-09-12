<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

     <script language="JavaScript" type="text/javascript">

     function multiLoad(doc1,doc2) {

        parent.maincontent.location.href=doc1;

        parent.contents.location.href=doc2;

//      parent.list.location.href="blank.htm";

     }

</script>



</head>



<body>



<?php

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");

include ("totalgallons.php");



if ($_GET['action']=="setlot")

{

    $_SESSION['702lot']=$_GET['lot'];

    $_SESSION['lot']="";

}







echo '<table width=30% valign=center align=center>';



  $query='SELECT * from lots inner join clients on (lots.CLIENTCODE=clients.CLIENTID) where ((lots.YEAR="'.$_SESSION['vintage'].'") AND

     (clients.CODE="'.$_SESSION['clientcode'].'"))';

  $result=mysql_query($query);



//echo $query;

    if ($_SESSION['702lot']=="")

    {

        echo '<tr><td colspan=2 style="border-style: solid; border-color: Sienna; border-width: 1" align=center>';

        echo "ALL LOTS";

    }

    else 

    {

        echo '<tr><td align=center>';

        $link1='piclot.php?action=setlot&lot=';

        $link2='mainmenu.php?clientcode='.$row['CODE'];

        echo '<tr><td colspan=2 align=center><a href="javascript:multiLoad(\''.$link1.'\',\''.$link2.'\')">'.'ALL LOTS</a></td></tr>';

    }

$result=mysql_query($query);

for ($i=0; $i<mysql_num_rows($result); $i++)

{

    $row=mysql_fetch_array($result);

    if ($row['LOTNUMBER']==$_SESSION['702lot'])

    {

        echo '<tr><td style="border-style: solid; border-color: Sienna; border-width: 1" align=center>';

        echo $row['LOTNUMBER'].'</td><td align=center>'.$row['DESCRIPTION'].'</td></tr>';

    }

    else

    {

        echo '<tr><td align=center>';

        $link1='piclot.php?action=setlot&lot='.$row['LOTNUMBER'];

        $link2='mainmenu.php?clientcode='.$row['CODE'];

        echo '<tr><td align=center><a href="javascript:multiLoad(\''.$link1.'\',\''.$link2.'\')">'.$row['LOTNUMBER'].'</td><td align=center> '.$row['DESCRIPTION'].'</a></td></tr>';

    }

    

    echo '</td></tr>';

}







echo '</table>';

?>



</body>



</html>