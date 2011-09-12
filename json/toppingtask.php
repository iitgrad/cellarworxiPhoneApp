<?php
require_once('../server/startdb.php');
include("pdf_utilities.php");

$pdf=new FPDF("L","mm","Letter");
$pdf->AddPage();
$pdf=gen_topping_task($pdf,$_GET['taskid']);
$pdf->Output();

?>