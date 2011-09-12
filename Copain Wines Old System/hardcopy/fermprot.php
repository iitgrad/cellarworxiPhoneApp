<?php

include ("../startdb.php");

include("pdf_utilities.php");



$pdf=new FPDF("P","mm","Letter");

$pdf->AddPage();

$pdf=gen_fermprot_page($pdf,$_GET['fermprotid']);

$pdf->Output();



?>