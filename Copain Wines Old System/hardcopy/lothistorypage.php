<?PHP

include ("../startdb.php");

include("pdf_utilities.php");

$pdf=new FPDF("P","mm","Letter");

$pdf->AddPage();

$pdf=gen_lotsummary_page($pdf,$_GET['lot']);

$pdf->Output();

?>