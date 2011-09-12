<?PHP

include ("../startdb.php");

include("pdf_utilities.php");



$pdf=new FPDF("P","mm","Letter");


$color[0]="RED";
$color[1]="GREEN";
$color[2]="BLUE";
$color[3]="PURPLE";
$color[4]="ORANGE";
$color[5]="BROWN";
$color[6]="YELLOW";
$color[7]="WHITE";
$color[8]="RED-BLUE";
$color[9]="RED-PURPLE";
$color[10]="RED-ORANGE";
$color[11]="RED-BROWN";
$color[12]="RED-YELLOW";
$color[13]="BLUE-GREEN";
$color[14]="BLUE-PURPLE";
$color[15]="BLUE-ORANGE";
$color[16]="BLUE-BROWN";
$color[17]="BLUE-YELLOW";
$color[18]="BLUE-WHITE";
$color[19]="GREEN-PURPLE";
$color[20]="GREEN-ORANGE";
$color[21]="GREEN-BROWN";
$color[22]="GREEN-YELLOW";
$color[23]="GREEN-WHITE";
$color[24]="PURPLE-ORANGE";
$color[25]="PURPLE-BROWN";
$color[26]="PURPLE-YELLOW";
$color[27]="PURPLE-WHITE";
$color[28]="ORANGE-BROWN";
$color[29]="ORANGE-YELLOW";


$query='select * from scp inner join wo on (scp.WOID=wo.ID) where wo.ENDDATE="'.date("Y-m-d",time()).'" and wo.DELETED!=1 order by ESTTONS desc';

//echo $query;

$result=mysql_query($query);



for ($i=0;$i<mysql_num_rows($result);$i++)

{

	$row=mysql_fetch_assoc($result);

	$query='update scp set COLORCODE="'.$color[$i].'" where ID=".$row['ID']."';
	echo $query; exit;
	$pdf->AddPage();

	$pdf=gen_scp_page($pdf,$row['WOID'], $i);

	$pdf->AddPage();

	$pdf=gen_scp_page($pdf,$row['WOID'], $i);

	$pdf->AddPage();

	$pdf=gen_scp_page($pdf,$row['WOID'], $i);

	$pdf->AddPage();

	$pdf=gen_scp_page($pdf,$row['WOID'], $i);
	

}



$pdf->Output();

?>