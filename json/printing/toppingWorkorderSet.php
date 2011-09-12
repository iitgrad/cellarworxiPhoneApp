<?php
require_once("../dompdf/dompdf_config.inc.php");
require_once('../../server/startdb.php');
require_once("../lotinforecords.php");
require_once("../utilities.php");

$companyInfo=getCompanyInfo();

$taskid=$_GET['taskid'];
$query='select wo.*,tasks.*,clients.CLIENTNAME, lots.DESCRIPTION as LOTDESCRIPTION from tasks left outer join wo ON tasks.id=wo.TASKID left outer join clients on (wo.CLIENTCODE=clients.CODE) left outer join lots on wo.LOT=lots.LOTNUMBER where tasks.id="'.$taskid.'" AND wo.DELETED="0" order by lots.LOTNUMBER';
// echo $query;
// exit;
$result=mysql_query($query);

for ($i=0;$i<mysql_num_rows($result);$i++)
{
	$row=mysql_fetch_assoc($result);
	$data['taskid']=$row['id'];
	$data['startdate']=$row['startdate'];
	$data['enddate']=$row['enddate'];
	$data['type']=$row['type'];
	$data['workperformedby']=$row['workperformedby'];
	$data['description']=$row['description'];
	$data['wos'][]=$row;
	$data['lotinfo'][]=lotinforecords($row['LOT'],"wo",$row['ID']);
}
 
$dompdf = new DOMPDF();
$dompdf->set_paper('letter','landscape');

$html = $html.'<html><head>';
$html = $html.'<style></style>';
$html = $html.'</head>';
$html = $html.'<body>';
$html = $html.'<br><br>';
$html = $html.'<table width=95% align=center style="font-size:.75em">';
$html = $html.'<tr><td align=left><strong>'.$companyInfo['name'].'</strong></td></tr>';
$html = $html.'<tr>';
	$html = $html.'<td width=40%>TASK ID: '.$data['taskid'].'</td>';
	$html = $html.'<td width=60%>Dates of Work: '.date("m/d/Y",strtotime($data['startdate'])).' - '.date("m/d/Y",strtotime($data['enddate'])).'</td>';	
$html = $html.'</tr>';
$html = $html.'<tr>';
	$html = $html.'<td>WINERY: '.$data['wos'][0]['CLIENTNAME'].'</td>';
	$html = $html.'<td>Work Performed By: '.$data['workperformedby'].'</td>';	
$html = $html.'</tr>';
$html = $html.'<tr>';
	$html = $html.'<td>Activity: '.$data['type'].'</td>';
	$html = $html.'<td>Submission Date: '.$data['wos'][0]['CREATIONDATE'].'</td>';
$html = $html.'</tr>';
$html = $html.'<tr>';
	$html = $html.'<td>Requestor: '.$data['wos'][0]['REQUESTOR'].'</td>';
$html = $html.'</tr>';
$html = $html.'</table>';
$html = $html.'<br><br>';
// echo '<pre>';
// print_r($data);
// exit;
if (($data['type']=="LAB TEST") | ($data['type']=="PULL SAMPLE"))
{
	$html = $html.'<table border=1 width=95% align=center  style="font-size:.75em">';
	$html = $html.'<tr>';
		$html = $html.'<td width=10%>Lot</td>';
		$html = $html.'<td width=25%>Description</td>';
		$html = $html.'<td align=right width=5%>Pull Sample</td>';
		$html = $html.'<td align=right width=5%>Qty</td>';
		$html = $html.'<td align=right width=5%>Volume</td>';
		$html = $html.'<td align=right width=20%>Lab Tests</td>';
		$html = $html.'<td width=25%>Notes</td>';
	$html = $html.'</tr>';
	
}
if ($data['type']=="TOPPING") 
{
	$html = $html.'<table border=1 width=95% align=center  style="font-size:.75em">';
	$html = $html.'<tr>';
		$html = $html.'<td width=8%>Lot</td>';
		$html = $html.'<td width=27%>Description</td>';
		$html = $html.'<td align=right width=5%>Gal</td>';
		$html = $html.'<td align=right width=5%>BBLs Before</td>';
		$html = $html.'<td align=right width=5%>BBLs After</td>';
		$html = $html.'<td align=right width=5%>Topping Matl Before</td>';
		$html = $html.'<td align=right width=5%>Topping Matl After</td>';
		$html = $html.'<td align=center width=8%>Top With</td>';
		$html = $html.'<td align=right width=5%>SO2 Add / BBL</td>';
		$html = $html.'<td width=27%>Notes</td>';
	$html = $html.'</tr>';
	for ($i=0;$i<count($data['wos']);$i++)
	{
		$wo=$data['wos'][$i];
		$so2=round((float)$wo['SO2ADD']*60*3.786*1000/60000,0);
		$lotinfo=$data['lotinfo'][$i];

		$html = $html.'<tr>';
			$html = $html.'<td>'.$wo['LOT'].'</td>';
			$html = $html.'<td>'.$wo['LOTDESCRIPTION'].'</td>';
			$gallons=$lotinfo[count($lotinfo)-1]['ending_tankgallons']+$lotinfo[count($lotinfo)-1]['ending_bbls']*60+$lotinfo[count($lotinfo)-1]['ending_toppinggallons'];
			$html = $html.'<td align=right>'.$gallons.'</td>';
			$html = $html.'<td align=right>'.$lotinfo[count($lotinfo)-1]['ending_bbls'].'</td>';
			$html = $html.'<td></td>';
			$html = $html.'<td align=right>'.$lotinfo[count($lotinfo)-1]['ending_toppinggallons'].'</td>';
			$html = $html.'<td></td>';
			$html = $html.'<td align=center>'.$wo['TOPPINGLOT'].'</td>';
			$html = $html.'<td align=right>'.$so2.'</td>';
			$html = $html.'<td>'.$wo['OTHERDESC'].'</td>';
		$html = $html.'</tr>';
	}

	$html = $html.'</table>';
	$html = $html.'<br><br>';
	$html = $html.'<table style="font-size:.75em" width=95% align=center>';
	$html = $html.'<tr>';
		$html = $html.'<td>Completion Comments:</td>';
	$html = $html.'</tr>';
	$html = $html.'<tr>';
		$html = $html.'<td width=50%>Work Performed By: ___________________________________</td>';
		$html = $html.'<td width=50%>Completion Date: ___________________________________</td>';
	$html = $html.'</tr>';
	$html = $html.'</table>';

	$html = $html.'</body></html>';
}



// $dompdf->load_html($html);
// $dompdf->render();
// $dompdf->stream("sample.pdf");
echo $html;

?>