<?php
require_once("../dompdf/dompdf_config.inc.php");
require_once('../../server/startdb.php');
require_once("../lotinforecords.php");
require_once("../utilities.php");


$woid=$_GET['woid'];

$companyInfo=getCompanyInfo();

$query='select wo.*,clients.CLIENTNAME, lots.*, lots.DESCRIPTION as LOTDESCRIPTION from wo  left outer join clients on (wo.CLIENTCODE=clients.CODE) left outer join lots on wo.LOT=lots.LOTNUMBER where wo.ID="'.$woid.'" AND wo.DELETED="0" order by lots.LOTNUMBER';
$result=mysql_query($query);

$row=mysql_fetch_assoc($result);

$dompdf = new DOMPDF();
$dompdf->set_paper('letter','landscape');

$html = $html.'<html><head>';
$html = $html.'<style></style>';
$html = $html.'</head>';
$html = $html.'<body>';
$html = $html.'<br><br>';
$html = $html.'<table width=95% align=center style="font-size:.75em">';
$html = $html.'<tr><td align=center><strong>'.$companyInfo['name'].'</strong></td></tr>';
$html = $html.'<tr><td align=center>WORK ORDER : '.$row['ID'].'</td></tr>';
$html = $html.'<tr><td align=center>LOT : '.$row['LOTNUMBER'].'</td></tr>';
$html = $html.'<tr><td align=center>'.$row['DESCRIPTION'].'</td></tr>';
$html = $html.'</table>';
$html = $html.'<br><br><br>';
$html = $html.'<table align=center width=95% border=1 style="font-size:.75em">';
$html = $html.'<tr><td align=left><b>WINERY : </b>'.$row['CLIENTNAME'].'</td><td align=left><b>SUBMISSION DATE : </b>'.$row['CREATIONDATE'].'</td><td align=left><b>DATE OF WORK : </b>'.$row['DUEDATE'].'</td></tr>';
$html = $html.'<tr><td align=left><b>ACTIVITY : </b>'.$row['TYPE'].'</td><td align=left><b>REQUESTOR : </b>'.$row['REQUESTOR'].'</td><td align=left><b>WORK PERFORMED BY : </b>'.$row['WORKPERFORMEDBY'].'</td></tr>';
$html = $html.'</table>';

$html = $html.'<br><br>';
$html = $html.'<table align=center width=95% border=1 style="font-size:.75em">';
$html = $html.'<tr><td align=left><b>DESCRIPTION : </b><pre>'.$row['OTHERDESC'].'</pre></td></tr>';
$html = $html.'</table>';

$html = $html.'<br><br>';
$html = $html.'<table align=center width=95% border=1 style="font-size:.75em">';
$html = $html.'<tr><td align=left><b>COMPLETION DESCRIPTION : </b><pre>'.$row['COMPLETIONDESC'].'</pre></td></tr>';
$html = $html.'</table>';

$html = $html.'</body></html>';


// $dompdf->load_html($html);
// $dompdf->render();
// $dompdf->stream("sample.pdf");
echo $html;

?>