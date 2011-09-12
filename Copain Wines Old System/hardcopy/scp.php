<?php

include ("../startdb.php");

include("pdf_utilities.php");



$pdf=new FPDF("P","mm","Letter");

$pdf->AddPage();

$pdf=gen_scp_page($pdf,$_GET['woid']);

$pdf->Output();



?>