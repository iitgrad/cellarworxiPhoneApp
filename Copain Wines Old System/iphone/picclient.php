<?php

session_start();

?>

<html>

<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="iphone.css">

</head>

<body>

<?php

include ("../startdb.php");

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


echo '<div class=Column>';
echo '<table width=100% align=center>';

$query='SELECT * FROM clients where clients.ACTIVE="YES" ORDER BY clients.CLIENTNAME';
$result=mysql_query($query);

for ($i=0; $i<mysql_num_rows($result); $i++)

{

    $row=mysql_fetch_array($result);

    if ($row['CODE']==$_SESSION['clientcode'])

    {

        echo '<tr><td style="border-style: solid; border-color: Sienna; border-width: 1" align=center>';

        echo $row['CLIENTNAME'].' <a href=index.php>MAIN</a>';

    }

    else

    {

        echo '<tr><td align=center>';

        echo '<a href="picclient.php?action=setclient&clientcode='.$row['CODE'].'">'.$row['CLIENTNAME'].'</a>';

    }

    

    echo '</td></tr>';

}

echo '</table>';

echo '</div>'
?>


</body>

</html>