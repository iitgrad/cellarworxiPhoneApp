<?PHP

include ("../startdb.php");

include("pdf_utilities.php");



$record=lotinforecords($_GET['lot']);



$pdf=new FPDF("P","mm","Letter");

$pdf->SetFont('Arial','B',24);

$pdf->AddPage();

$pdf->Image("../../images/leaf.jpg",90,30);

$pdf->SetXY(10,90);

$pdf->Cell(205,7,'COPAIN CUSTOM CRUSH',0,1,"C",0,'http://www.copaincustomcrush.com/');

$pdf->SetFont('Arial','B',18);

$pdf->ln(15);

$pdf->Cell(205,10,"COMPLETE LOT HISTORY",0,1,"C");

$pdf->Cell(205,10,"FOR",0,1,"C");

$pdf->Cell(205,10,$_GET['lot'],0,1,"C");



$pdf->AddPage();

$pdf=gen_lotsummary_page($pdf,$_GET['lot']);



$pdf->AddPage();

for ($i=0;$i<count($record);$i++)

{

	$row=$record[$i]['data'];

	switch ($record[$i]['type'])

	{

		case "WO":

		{

			switch ($record[$i]['data']['TYPE'])

			{

				case "SCP":

				{

					$pdf->AddPage();

					$pdf=gen_scp_page($pdf,$row['ID']);

					break;

				}

				case "PRESSOFF":

				{

					$pdf->AddPage();

					$pdf=gen_presssheet_page($pdf,$row['ID']);

					break;

				}

				case "LAB TEST":

				{

					$pdf->AddPage();

					$pdf=gen_lab_page($pdf,$row['ID']);

					break;

				}

				default:

				{

					$pdf=gen_wo_page($pdf,$row['ID']);		

					break;			

				}

			}

			break;

		}

		case "WT":

		{

			$pdf=gen_wt_page($pdf,($row['TAGID']+5000));

			break;

		}

	}

	$pdf->AddPage();

}



$pdf->Output();

?>