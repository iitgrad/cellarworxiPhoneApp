<?PHP

include ("../startdb.php");

include("pdf_utilities.php");



$pdf=new FPDF("P","mm","Letter");



$query='select * from wt where year(wt.DATETIME)="2004" order by wt.TAGID';

//echo $query;

$result=mysql_query($query);



for ($i=0;$i<mysql_num_rows($result);$i++)

{

	$row=mysql_fetch_array($result);



	$pdf->AddPage();

	$pdf=gen_wt_page($pdf,$row['TAGID']+5000);



	

}



$pdf->Output();

?>