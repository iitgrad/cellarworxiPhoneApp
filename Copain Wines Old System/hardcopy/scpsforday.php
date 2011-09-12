<?PHP

include ("../startdb.php");

include("pdf_utilities.php");



$pdf=new FPDF("P","mm","Letter");



$query='select * from scp inner join wo on (scp.WOID=wo.ID) where wo.ENDDATE="'.date("Y-m-d",time()).'"';

//echo $query;

$result=mysql_query($query);



for ($i=0;$i<mysql_num_rows($result);$i++)

{

	$row=mysql_fetch_array($result);



	$pdf->AddPage();

	$pdf=gen_scp_page($pdf,$row['WOID']);

	$pdf->AddPage();

	$pdf=gen_scp_page($pdf,$row['WOID']);

	$pdf->AddPage();

	$pdf=gen_scp_page($pdf,$row['WOID']);

	$pdf->AddPage();

	$pdf=gen_scp_page($pdf,$row['WOID']);
	

}



$pdf->Output();

?>