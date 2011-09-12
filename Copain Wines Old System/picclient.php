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



if ($_GET['action']=="setclient")

{

    $_SESSION['clientcode']=$_GET['clientcode'];

    $_SESSION['lot']="";

}





if ($_GET['action']=="clearclient")

{

  $_SESSION['clientcode']='';

}



if ($_SESSION['clientcode']=="")

  $_SESSION['clientcode']=getclientcode();



echo '<table width=20% valign=center align=center>';



if (isstaff()=="YES")

$query='SELECT * FROM clients where clients.ACTIVE="YES" ORDER BY clients.CLIENTNAME';

else

//$query='SELECT * FROM users INNER JOIN groups ON (users.`group` = groups.GROUPID) INNER JOIN clients ON (groups.CLIENTID=clients.clientid) WHERE users.clientid="'.clientid($_SESSION['clientcode']).'"';

$query='SELECT *

FROM

  users

  INNER JOIN groups ON (users.`group` = groups.GROUPID)

  INNER JOIN clients ON (groups.CLIENTID = clients.clientid)

  WHERE users.username="'.$_SERVER['REMOTE_USER'].'"';



//echo $query;

$result=mysql_query($query);

for ($i=0; $i<mysql_num_rows($result); $i++)

{

    $row=mysql_fetch_array($result);

    if ($row['CODE']==$_SESSION['clientcode'])

    {

        echo '<tr><td style="border-style: solid; border-color: Sienna; border-width: 1" align=center>';

        echo $row['CLIENTNAME'];

    }

    else

    {

        echo '<tr><td align=center>';

        $link1='picclient.php?action=setclient&clientcode='.$row['CODE'];

        $link2='mainmenu.php?clientcode='.$row['CODE'];

        echo '<a href="javascript:multiLoad(\''.$link1.'\',\''.$link2.'\')">'.$row['CLIENTNAME'].'</a>';

    }

    

    echo '</td></tr>';

}







echo '</table>';

?>



</body>



</html>