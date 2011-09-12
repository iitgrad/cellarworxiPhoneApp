<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

</head>

<body>

<?php



include ("../startdb.php");

include("pdf_utilities.php");



$wo=getwo($_GET['woid']);

$query = 'select lot from wo where (wo.ID='.$_GET['woid'].')';

$result = mysql_query($query);

$row=mysql_fetch_array($result);



$lotinfo=lotinforecords($row['lot']);

echo '<pre>';

print_r ($lotinfo);



$thelotinfo=lotinfo($row['lot']);

print_r($thelotinfo);

echo '</pre>';



exit;

$pdf=new FPDF("P","mm","Letter");

$pdf->AddPage();

$pdf=gen_bottling_page($pdf,$_GET['woid']);

$pdf->Output();



?>

</body>

</html>