<?php
include ("../startdb.php");
define('FPDF_FONTPATH','../../font/');
include ("../../fpdf.php");
include ("../assetfunctions.php");
include ("../queryupdatefunctions.php");
include ("../lotinforecords.php");

function theheader($pdf)
{
    $pdf->Image("../../images/leaf.jpg",10,10);
    $pdf->SetXY(10,10);
    $pdf->SetFont('Arial','B',18);
    $pdf->ln(9);
    $pdf->Cell(195,7,'COPAIN CUSTOM CRUSH',0,1,"C",0,'http://www.copaincustomcrush.com/');
    
    return $pdf;
}

function drawbox($pdf,$ul,$lr)
{
    $cp=array ($pdf->GetX(),$pdf->GetY());
    $pdf->Line($ul[0],$ul[1],$lr[0],$ul[1]);
    $pdf->Line($ul[0],$lr[1],$lr[0],$lr[1]);
    $pdf->Line($ul[0],$ul[1],$ul[0],$lr[1]);
    $pdf->Line($lr[0],$ul[1],$lr[0],$lr[1]);
    
    $pdf->SetXY($cp[0],$cp[1]);
    return $pdf;
}

function boxtext($pdf,$sizex,$title,$body, $outline=0)
{
    $lines=preg_split("/<cr>/",$body);
    
    $ul=array ($pdf->GetX(),$pdf->GetY());
    
    $pdf->SetLeftMargin($ul[0]);
    $pdf->SetRightMargin(215-($sizex+$ul[0]));
    $pdf=crlf($pdf,1);
    if ($title!='')
    {
        $pdf->SetFont('Arial','B',8);
        $pdf->Write(3,$title.": ");
    }
    $pdf->SetLeftMargin($pdf->GetX());
    $pdf->SetFont('Arial','',8);
    for ($i=0;$i<count($lines);$i++)
    {
        $pdf->Write(3,$lines[$i]);
        if ($i<(count($lines)-1))
        {
            $pdf->ln(3);
        }
        if ($pdf->GetY()>$_SESSION['maxY'])
        {
            $_SESSION['maxY']=$pdf->GetY();
        }
    }
    $pdf->ln(6);
    $lr=array ($sizex+$ul[0],$pdf->GetY());
    if ($outline==1)
    {
        $pdf=drawbox($pdf,$ul,$lr);
    }
    $pdf->SetRightMargin(10);
    $pdf->SetLeftMargin($ul[0]);
    $pdf->SetXY($ul[0]+$sizex,$ul[1]);
    return $pdf;
}

function display_asset($pdf,$assettype,$date,$clientcode,$woid,$boxwidth,$outline=0)
{
    $assets=listallocassets($assettype,$date,clientid($clientcode),$woid);
    if (count($assets)>0)
    {
        $wa="";
        for ($i=0;$i<count($assets);$i++)
        {
            $wa=$wa.$assets[$i]['name']."<cr>";
        }
        $pdf=boxtext($pdf,$boxwidth,$assettype,$wa,$outline);
        $pdf->ln(5);
    }
    else
    {
        //      $pdf=boxtext($pdf,$boxwidth,$assettype,"NONE IDENTIFIED",$outline);
    }
    $pdf->SetX(10);
    return $pdf;
}

function crlf($pdf,$space=10)
{
    $pdf->Ln($space);
    return $pdf;
}

function cp($pdf)
{
    return array($pdf->GetX(),$pdf->GetY());
}

function gen_popd_matrix($pdf)
{
		//     $query='select type,vesseltype,convert(vesselid,signed) as vid,lot, dryice, stattemp, timeslot,strength,duration,lots.description,clients.group from wo left outer join lots on (lots.LOTNUMBER=wo.LOT) left outer join clients on (wo.CLIENTCODE=clients.CODE) where duedate=curdate() and wo.DELETED!=1 
		// and (TYPE="PUMP OVER" or wo.TYPE="PUNCH DOWN" or wo.TYPE="PRESSOFF") order by clients.group, wo.vesseltype, vid';
  $query='select type,vesseltype,convert(vesselid,signed) as vid,lot, dryice, stattemp, timeslot,strength,duration,lots.description,clients.group from wo left outer join lots on (lots.LOTNUMBER=wo.LOT) left outer join clients on (wo.CLIENTCODE=clients.CODE) where duedate=curdate() and wo.DELETED!=1 
		and (TYPE="PUMP OVER" or wo.TYPE="PUNCH DOWN") order by clients.group, wo.vesseltype, vid';

//	echo $query; exit;
	$slot['MORNING']="AM";
	$slot['NOON']="MID";
	$slot['EVENING']="PM";

	$activity['PUMP OVER']="PO";
	$activity['PUNCH DOWN']="PD";

	$result=mysql_query($query);

    $pdf->SetLeftMargin(25);

    $pdf->SetFont('Arial','',8);

	for ($i=0; $i<mysql_num_rows($result); $i++)
	{
		$row=mysql_fetch_array($result);

		$query2='select ID,TYPE from wo where wo.LOT="'.$row['lot'].'" and wo.DUEDATE=CURDATE() and wo.WORKPERFORMEDBY="CCC" AND wo.TYPE!="PUMP OVER" and wo.TYPE!="PUNCH DOWN"';
		$result2=mysql_query($query2);

		$vessel=$row['vesseltype'].'-'.$row['vid'];

		$action[$row['group']][$vessel]['stattemp']=$row['stattemp'];
		$action[$row['group']][$vessel]['lot']=$row['lot'];
		$action[$row['group']][$vessel]['description']=$row['description'];
		$action[$row['group']][$vessel]['dryice']=$row['dryice'];
		for ($j=0;$j<mysql_num_rows($result2);$j++)
		{
			$row2=mysql_fetch_array($result2);
			$thelots[$row['lot']][$row2['ID']]=$row2['TYPE'];
		}

		if ($activity[$row['type']]=="PO")
		{
			$action[$row['group']][$vessel][$slot[$row['timeslot']]]=$activity[$row['type']].'-'.$row['duration'].'mins';
		}
		else
		{
			if (($row['dryice']=="YES") & ($row['strength']==""))
				$action[$row['group']][$vessel][$slot[$row['timeslot']]]="DRYICE ONLY";
			else
				$action[$row['group']][$vessel][$slot[$row['timeslot']]]=$activity[$row['type']].'-'.$row['strength'];
		}
	}
	
	foreach ($action as $group=>$data)
	{
		$pdf->Cell(245,7,'GROUP : '.$group,0,1,"C",0);
		$pdf->ln(2);
		$pdf->Cell(15,7,"VESSEL",1,0,"C",0);
		$pdf->Cell(15,7,"LOT",1,0,"C",0);
		$pdf->Cell(50,7,"DESC",1,0,"C",0);
		$pdf->Cell(15,7,"SET STAT",1,0,"C",0);
		$pdf->Cell(15,7,"BRIX",1,0,"C",0);
		$pdf->Cell(15,7,"TMP",1,0,"C",0);
		$pdf->Cell(20,7,"AM",1,0,"C",0);
		$pdf->Cell(20,7,"MID",1,0,"C",0);
		$pdf->Cell(20,7,"PM",1,0,"C",0);
		$pdf->Cell(15,7,"DRYICE",1,0,"C",0);
		$pdf->Cell(45,7,"COMMENTS",1,1,"C",0);
		
		foreach ($data as $thevessel=>$data2)
		{
			$pdf->Cell(15,7,$thevessel,1,0,"C",0);
			$pdf->Cell(15,7,$data2['lot'],1,0,"C",0);
			$pdf->Cell(50,7,substr($data2['description'],0,25),1,0,"C",0);
			if ($data2['stattemp']>0)
				$pdf->Cell(15,7,$data2['stattemp'],1,0,"C",0);
			else
				$pdf->Cell(15,7,'',1,0,"C",0);
			$pdf->Cell(15,7,"",1,0,"C",0);
			$pdf->Cell(15,7,"",1,0,"C",0);
			$pdf->Cell(20,7,$data2['AM'],1,0,"C",0);
			$pdf->Cell(20,7,$data2['MID'],1,0,"C",0);
			$pdf->Cell(20,7,$data2['PM'],1,0,"C",0);
			$pdf->Cell(15,7,$data2['dryice'],1,0,"C",0);			
//			$pdf->Cell(60,7,$data2['comments'],1,1,"C",0);			
			$pdf->SetFont('Arial','',6);
			$comment='';
			if (count($thelots[$data2['lot']])>0)
			{
				foreach ($thelots[$data2['lot']] as $woid=>$type)
				{
				   $comment.=$woid.' ('.$type.')  ';	
				}
			}
			$pdf->Cell(45,7,$comment,1,1,"C",0);			
			$pdf->SetFont('Arial','',8);
		}
		
		$pdf->AddPage();
	} 
	return $pdf;
	   
    
    $pdf->SetFont('Arial','B',10);
//    $pdf->Cell(90,10,"WEIGHMASTER'S CERTIFICATE",0,1,"C");
    $body="THIS IS TO CERTIFY that the following described commodity was weighed, ";
    $body=$body . "measured, or counted by a weighmaster, whose signature is on this certificate, ";
    $body=$body . "who is a recognized authority of accuracy as described by Chapter 7 (commencing with ";
    $body=$body . " Section 12700) of Division 5 of California Business and Professions Code, administered ";
    $body=$body . "by the Division of Measurement Standards of the California Department of Food and Agriculture.";
    $leftmargin=10;
    $rightmargin=10;
    $pdf->SetLeftMargin(10);
    $pdf=theheader($pdf);
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(195,7,'WORK ORDER : '.$woid,0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/wopage.php?action=view&woid='.$woid);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(195,5,'LOT: '.$row['LOT'],0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/showlotinfo.php?lot='.$row['LOT']);
    $pdf->SetFont('Arial','B',8);    $pdf->Cell(195,5,''.$row['DESCRIPTION'],0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/showlotinfo.php?lot='.$row['LOT']);
    $pdf->Cell(195,5,'BARREL COUNT: '.$row['ENDINGBARRELCOUNT'],0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/showlotinfo.php?lot='.$row['LOT']);
    $pdf->ln(17);
    $pdf->Cell(65,10,'WINERY: '.$row['CLIENTNAME'],1,0,"L");
    $pdf->Cell(65,10,'SUBMISSION DATE: '.$row['CREATIONDATE'],1,0,"L");
    $pdf->Cell(65,10,'DATE OF WORK: '.$row['DUEDATE'],1,1,"L");
    $pdf->Cell(65,10,'ACTIVITY: '.$row['TYPE'],1,0,"L");
    $pdf->Cell(65,10,'REQUESTOR: '.strtoupper($row['REQUESTOR']),1,0,"L");
    $pdf->Cell(65,10,'WORK PERFORMED BY: '.$row['WORKPERFORMEDBY'],1,1,"L");
    $pdf=crlf($pdf,3);
    
    $pdf=display_asset($pdf, "WORKAREA", $row['DUEDATE'],$row['CLIENTCODE'],$woid,80);
    $pdf=display_asset($pdf, "FORKLIFT", $row['DUEDATE'],$row['CLIENTCODE'],$woid,80);
    $pdf=display_asset($pdf, "TANK", $row['DUEDATE'],$row['CLIENTCODE'],$woid,80);
    $pdf=display_asset($pdf, "PUMP", $row['DUEDATE'],$row['CLIENTCODE'],$woid,80);
    $pdf=display_asset($pdf, "PRESS", $row['DUEDATE'],$row['CLIENTCODE'],$woid,80);
    $pdf=display_asset($pdf, "BOTTLINGLINE", $row['DUEDATE'],$row['CLIENTCODE'],$woid,80);
    $pdf=display_asset($pdf, "MISCELLANEOUS", $row['DUEDATE'],$row['CLIENTCODE'],$woid,80);
    
    $_SESSION['maxY']=$pdf->GetY();
    $pdf->SetXY(10,$_SESSION['maxY']+5);
    
    switch ($row['TYPE'])
    {
        case 'LAB TEST':
        {
            $labtestquery='SELECT * FROM  `labresults` INNER JOIN `labtest` ON (`labresults`.`LABTESTID` = `labtest`.`ID`) WHERE labtest.WOID="'.$woid.'"';
            $labtestresults=mysql_query($labtestquery);
            for ($i=0;$i<mysql_num_rows($labtestresults);$i++)
            {
                $row=mysql_fetch_array($labtestresults);
                $thetests=$thetests.$row['LABTEST'].' '.$row['VALUE1'].' '.$row['UNITS1'].'
';
            }
            $pdf=boxtext($pdf,90,"LAB TEST",$thetests);
            $pdf->SetXY(10,$_SESSION['maxY']+10);
        //    break;
        }
        default:
        {
            $cp=cp($pdf);
            $pdf=boxtext($pdf, 195 ,"DESCRIPTION",$row['OTHERDESC'],1);
            $pdf->SetXY(10,$_SESSION['maxY']+10);
            $pdf=boxtext($pdf, 195 ,"COMPLETION DESCRIPTION",$row['COMPLETEDDESCRIPTION'],1);
            $pdf->SetXY(10,$_SESSION['maxY']+10);
        }
    }
    return $pdf;
}

function gen_wo_page($pdf,$woid)
{
    $query='SELECT wo.DUEDATE, wo.CLIENTCODE, wo.TYPE, lots.ORGANIC, wo.REQUESTOR, wo.LOT, wo.ENDINGBARRELCOUNT, wo.ENDINGTOPPINGGALLONS,
         wo.OTHERDESC, wo.COMPLETEDDESCRIPTION, wo.WORKPERFORMEDBY, wo.CREATIONDATE, lots.DESCRIPTION,
         clients.CLIENTNAME from wo
          INNER JOIN `clients` ON (`wo`.`CLIENTCODE` = `clients`.`CODE`) 
          INNER JOIN lots ON (`wo`.`LOT` = `lots`.`LOTNUMBER`) 
          WHERE wo.id="'.$woid.'"';
    
    $result=mysql_query($query);
    $row=mysql_fetch_array($result);
    
    $pdf->SetFont('Arial','B',10);
//    $pdf->Cell(90,10,"WEIGHMASTER'S CERTIFICATE",0,1,"C");
    $body="THIS IS TO CERTIFY that the following described commodity was weighed, ";
    $body=$body . "measured, or counted by a weighmaster, whose signature is on this certificate, ";
    $body=$body . "who is a recognized authority of accuracy as described by Chapter 7 (commencing with ";
    $body=$body . " Section 12700) of Division 5 of California Business and Professions Code, administered ";
    $body=$body . "by the Division of Measurement Standards of the California Department of Food and Agriculture.";
    $leftmargin=10;
    $rightmargin=10;
    $pdf->SetLeftMargin(10);
    $pdf=theheader($pdf);
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(195,7,'WORK ORDER : '.$woid,0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/wopage.php?action=view&woid='.$woid);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(195,5,'LOT: '.$row['LOT'],0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/showlotinfo.php?lot='.$row['LOT']);
    $pdf->SetFont('Arial','B',8);    $pdf->Cell(195,5,''.$row['DESCRIPTION'],0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/showlotinfo.php?lot='.$row['LOT']);
    $pdf->Cell(195,5,'BARREL COUNT: '.$row['ENDINGBARRELCOUNT'],0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/showlotinfo.php?lot='.$row['LOT']);
    $pdf->ln(17);
	if ($row['ORGANIC']=="YES")
	{
 		$pdf->Cell(195,10,"All equipment must be cleaned/sanitized in accordance with the CCC Organic Sanitation Protocol",0,1,"C",0);
	    $pdf->ln(17);
	}
    $pdf->Cell(65,10,'WINERY: '.$row['CLIENTNAME'],1,0,"L");
    $pdf->Cell(65,10,'SUBMISSION DATE: '.$row['CREATIONDATE'],1,0,"L");
    $pdf->Cell(65,10,'DATE OF WORK: '.$row['DUEDATE'],1,1,"L");
    $pdf->Cell(65,10,'ACTIVITY: '.$row['TYPE'],1,0,"L");
    $pdf->Cell(65,10,'REQUESTOR: '.strtoupper($row['REQUESTOR']),1,0,"L");
    $pdf->Cell(65,10,'WORK PERFORMED BY: '.$row['WORKPERFORMEDBY'],1,1,"L");
    $pdf=crlf($pdf,3);
    
    $pdf=display_asset($pdf, "WORKAREA", $row['DUEDATE'],$row['CLIENTCODE'],$woid,80);
    $pdf=display_asset($pdf, "FORKLIFT", $row['DUEDATE'],$row['CLIENTCODE'],$woid,80);
    $pdf=display_asset($pdf, "TANK", $row['DUEDATE'],$row['CLIENTCODE'],$woid,80);
    $pdf=display_asset($pdf, "PUMP", $row['DUEDATE'],$row['CLIENTCODE'],$woid,80);
    $pdf=display_asset($pdf, "PRESS", $row['DUEDATE'],$row['CLIENTCODE'],$woid,80);
    $pdf=display_asset($pdf, "BOTTLINGLINE", $row['DUEDATE'],$row['CLIENTCODE'],$woid,80);
    $pdf=display_asset($pdf, "MISCELLANEOUS", $row['DUEDATE'],$row['CLIENTCODE'],$woid,80);
    
    $_SESSION['maxY']=$pdf->GetY();
    $pdf->SetXY(10,$_SESSION['maxY']+5);
    
    switch ($row['TYPE'])
    {
        case 'LAB TEST':
        {
            $labtestquery='SELECT * FROM  `labresults` INNER JOIN `labtest` ON (`labresults`.`LABTESTID` = `labtest`.`ID`) WHERE labtest.WOID="'.$woid.'"';
            $labtestresults=mysql_query($labtestquery);
            for ($i=0;$i<mysql_num_rows($labtestresults);$i++)
            {
                $row=mysql_fetch_array($labtestresults);
                $thetests=$thetests.$row['LABTEST'].' '.$row['VALUE1'].' '.$row['UNITS1'].'
';
            }
            $pdf=boxtext($pdf,90,"LAB TEST",$thetests);
            $pdf->SetXY(10,$_SESSION['maxY']+10);
        //    break;
        }
        default:
        {
            $cp=cp($pdf);
            $pdf=boxtext($pdf, 195 ,"DESCRIPTION",$row['OTHERDESC'],1);
            $pdf->SetXY(10,$_SESSION['maxY']+10);
            $pdf=boxtext($pdf, 195 ,"COMPLETION DESCRIPTION",$row['COMPLETEDDESCRIPTION'],1);
            $pdf->SetXY(10,$_SESSION['maxY']+10);
        }
    }
    return $pdf;
}

function gen_lotsummary_page($pdf,$lot)
{
    $query='SELECT * from lots where lots.LOTNUMBER="'.$lot.'"';
    $result=mysql_query($query);
    $row=mysql_fetch_array($result);
    
    $record=lotinforecords($lot);
    
    $leftmargin=$pdf->GetX();
    $rightmargin=10;
    $pdf=theheader($pdf);
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(195,7,'LOT: '.$lot,0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/showlotinfo.php?lot='.$lot);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(195,7,$row['YEAR'].' '.$row['DESCRIPTION'],0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/showlotinfo.php?lot='.$lot);
    $pdf->ln(27);
    $pdf->SetFont('Arial','B',8);
    $pdf=boxtext($pdf,15,'','DATE<cr>',1);
    $pdf=boxtext($pdf,15,'','ID<cr>',1);
    $pdf=boxtext($pdf,90,'','DESCRIPTION<cr>',1);
    $pdf=boxtext($pdf,15,'','TANK<cr>GLNS',1);
    $pdf=boxtext($pdf,15,'','BBLS<cr>',1);
    $pdf=boxtext($pdf,15,'','TOPPING<cr>GLNS',1);
    $pdf=boxtext($pdf,15,'','TOTAL<cr>GLNS',1);
    $pdf=boxtext($pdf,15,'','CASES<cr>',1);
    $pdf->ln(11);
    $pdf->SetX(10);
    for ($i=0;$i<count($record);$i++)
    {
        $row=$record[$i]['data'];
        switch ($record[$i]['type'])
        {
            case "BLEND":
            {
                $pdf=boxtext($pdf,15,'',date("m-d-y",$row['THEDATE']));
                $pdf=boxtext($pdf,15,'','WO-'.$row['ID']);
                if ($row['DIRECTION']=="IN FROM")
                {
                    $pdf=boxtext($pdf,90,"BLEND",$row['GALLONS'].' GALLONS OUT TO '.$row['LOT']);
                    //$pdf->Cell(90,10,'BLEND'.$row['GALLONS'].' GALLONS OUT TO '.$row['LOT'],0,0,"L");
                }
                else
                {
                    $pdf=boxtext($pdf,90,"BLEND",$row['GALLONS'].' GALLONS IN FROM '.$row['LOT']);
                    //$pdf->Cell(90,10,'BLEND'.$row['GALLONS'].' GALLONS IN FROM '.$row['LOT'],0,0,"L");
                }
                
                break;
            }
            case "WT" :
            {
                $tons=($row['SUM_OF_WEIGHT']-$row['SUM_OF_TARE'])/2000;
                $pdf=boxtext($pdf,15,'',date("m-d-y",$row['THEDATE']));
                $pdf=boxtext($pdf,15,'','WT-'.(5000+$row['TAGID']));
                $pdf=boxtext($pdf,90,'',date("Y",$row['THEDATE']).'  '.$row['VINEYARD'].'  '.$row['APPELATION'].'  '.$row['VARIETY'].'  '.$tons.' TONS');
                break;
            }
            case "BOL":
            {
                $pdf=boxtext($pdf,15,'',date("m-d-y",$row['THEDATE']));
                $pdf=boxtext($pdf,15,'','BOL-'.$row['ID']);
                $pdf=boxtext($pdf,90,'',$row['NAME'].' '.$row['DIRECTION'].' '.$row['BONDED']);
                break;
            }
            case "WO" :
            {
                $pdf=boxtext($pdf,15,'',date("m-d-y",$row['THEDATE']));
                $pdf=boxtext($pdf,15,'','WO-'.$row['ID']);
                if (($row['ENDINGTANKGALLONS']!="") | ($row['ENDINGBARRELCOUNT']!="") | ($row['ENDINGTOPPINGGALLONS']!=""))
                {
                    $volume= $row['ENDINGTANKGALLONS'] + $row['ENDINGBARRELCOUNT']*60 +$row['ENDINGTOPPINGGALLONS'];
                    $totalgallons=$volume;
                }
                switch ($row['TYPE'])
                {
                    case "SCP":
                    {
                        $scpquery='SELECT * FROM scp WHERE scp.WOID="'.$row['ID'].'"';
                        $scpresult=mysql_query($scpquery);
                        $scprow=mysql_fetch_array($scpresult);
                        $pdf=boxtext($pdf,90,"SCP",$scprow['VINEYARD'].' '.$scprow['VARIETAL'].' ESTTONS:'.$scprow['ESTTONS']);
                        break;
                    }

                    case "LAB TEST":
                    {
                        $labtestquery='SELECT * FROM  `labresults` INNER JOIN `labtest` ON (`labresults`.`LABTESTID` = `labtest`.`ID`) WHERE labtest.WOID="'.$row['ID'].'" LIMIT 3';
                        $labtestresults=mysql_query($labtestquery);
                        for ($k=0;$k<mysql_num_rows($labtestresults);$k++)
                        {
                          $labrow=mysql_fetch_array($labtestresults);
                          if ($k>0)
                            $labdata.=', ';
                          $labdata.=$labrow['LABTEST'].' '.$labrow['VALUE1'].' '.$labrow['UNITS1'];
                        }
                            $pdf=boxtext($pdf,90,"LAB TEST",$labdata);
                        break;
                    }
                    case "PUMP OVER" :
                    {
                        $pdf=boxtext($pdf,90,$row['TYPE'],'  DURATION - '.$row['DURATION'].'  '.$row['VESSELTYPE'].'  '.$row['VESSELID']);
                        break;
                    }
                    case "PUNCH DOWN" :
                    {
                        $pdf=boxtext($pdf,90,$row['TYPE'],'  STRENGTH - '.$row['STRENGTH'].'  '.$row['VESSELTYPE'].'  '.$row['VESSELID']);
                        break;
                    }
                    case "DRYICE" :
                    {
                        $pdf=boxtext($pdf,90,$row['TYPE'],'VESSEL - '.$row['VESSELID']);
                        break;
                    }
                    case "BLENDING" :
                    {
                        //                  $pdf->Cell(90,10,$row['TYPE'],0,0,"C");
                        $queryblendsforwo='SELECT `blenditems`.`SOURCELOT`, `blenditems`.`GALLONS`,
                        `blenditems`.`DIRECTION`, `wo`.`LOT`,
                `blend`.`WOID`, UNIX_TIMESTAMP(`wo`.`DUEDATE`) AS THEDATE FROM `blenditems`
                INNER JOIN `blend` ON (`blenditems`.`BLENDID` = `blend`.`ID`)
                INNER JOIN`wo` ON (`blend`.`WOID` = `wo`.`ID`)
                WHERE  (`wo`.`ID` = "'.$row['ID'].'")';
                        $result2=mysql_query($queryblendsforwo);
                        //echo '<td><table border="0" width="100%"><tr><td align="center" width="22%">'.
                        $row['TYPE'].'</td><td align=center>';
                        for ($k=0;$k<mysql_num_rows($result2);$k++)
                        {
                            $row2=mysql_fetch_array($result2);
                            $pdf=boxtext($pdf,90,"BLEND",$row2['GALLONS'].' GALLONS '.$row2['DIRECTION'].' '.$row2['SOURCELOT']);
                            //echo $row2['GALLONS'].' GALLONS '. $row2['DIRECTION'].' <a href=showlotinfo.php?lot='.$row2['SOURCELOT'].'>'.$row2['SOURCELOT'].'</a><br>';
                        }
                        //echo '</td></table>';
                        break;
                    }
                    default :
                    $pdf=boxtext($pdf,90,$row['TYPE'],$row['OTHERDESC']);
                }
                
                /*$query2='SELECT  `wo`.`LOT`,`additions`.`SUPERFOODAMT`,`additions`.`DAPAMOUNT`,`additions`.`HTAAMOUNT`,
                `additions`.`GOAMOUNT`,`additions`.`WATERAMOUNT`,`additions`.`INNOCULATIONBRAND`,`additions`.`INNOCULATIONAMOUNT`
                FROM `wo`
                INNER JOIN `fpaddmap` ON (`wo`.`RELATEDADDITIONSID` = `fpaddmap`.`FERMPROTID`)
                INNER JOIN `additions` ON (`fpaddmap`.`ADDITIONID` = `additions`.`ID`)
                WHERE wo.ID="'.$row['ID'].'"';
                
                $result2=mysql_query($query2);
                $num_rows2=mysql_num_rows($result2);
                if ($additionsshown[$row['ID']]!=1)
                { $additionsshown[$row['ID']]=1;
                for ($j=0;$j<$num_rows2;$j++)
                {
                $row2=mysql_fetch_array($result2);
                echo '<tr><td></td><td></td>';
                echo '<td align="center"><table border="1" width="100%"><tr>';
                echo '<td align="center" width=22%>ADDITION</td>';
                echo '<td align="center">SF<br>'.$row2['SUPERFOODAMOUNT'].'</td>';
                echo '<td align="center">DAP<br>'.$row2['DAPAMOUNT'].'</td>';
                echo '<td align="center">HTA<br>'.$row2['HTAAMOUNT'].'</td>';
                echo '<td align="center">GO<br>'.$row2['GOAMOUNT'].'</td>';
                echo '<td align="center">H20<br>'.$row2['WATERAMOUNT'].'</td>';
                echo '<td align="center">INNBRND<br>'.$row2['INNOCULATIONBRAND'].'</td>';
                echo '<td align="center">INNAMT<br>'.$row2['INNOCULATIONAMOUNT'].'</td>';
                echo '</tr></table>';
                echo '<td align="center">'.number_format($totalgallons,2).'</td>';
                echo '</tr>';
                }*/
            }
            
        }
        $pdf=boxtext($pdf,15,'',number_format($record[$i]['ending_tankgallons'],2));
        $pdf=boxtext($pdf,15,'',number_format($record[$i]['ending_bbls'],2));
        $pdf=boxtext($pdf,15,'',number_format($record[$i]['ending_toppinggallons'],2));
        $pdf=boxtext($pdf,15,'',number_format($record[$i]['ending_toppinggallons']+$record[$i]['ending_bbls']*60+$record[$i]['ending_tankgallons'],2));
        $pdf=boxtext($pdf,15,'',number_format(.42*($record[$i]['ending_toppinggallons']+$record[$i]['ending_bbls']*60+$record[$i]['ending_tankgallons']),0));
        /*
        echo '<td align="center">'.number_format($record[$i]['ending_tankgallons'],2).'</td>';
        echo '<td align="center">'.number_format($record[$i]['ending_bbls'],0).'</td>';
        echo '<td align="center">'.number_format($record[$i]['ending_toppinggallons'],2).'</td>';
        echo '<td align="center">'.number_format($record[$i]['ending_toppinggallons']+$record[$i]['ending_bbls']*60+$record[$i]['ending_tankgallons'],2).'</td>';
        echo '<td align="center">'.number_format(.42*($record[$i]['ending_toppinggallons']+$record[$i]['ending_bbls']*60+$record[$i]['ending_tankgallons']),0).'</td>';
        */
        //$pdf->ln(20);
        $pdf->SetX(10);
        $pdf->SetLeftMargin(10);
        $pdf->SetXY(10,$_SESSION['maxY']+5);
    }
    $record=lotinforecords($lot);
    $lr=count($record);
    
    foreach ($record[$lr-1]['structure'] as $key=>$value)
    {
        $tot=0;
        $count=0;
        foreach ($value as $key2=>$value2)
        {
            $tot+=$value2;
            $count+=1;
        }
        $linenumber=0;
        foreach ($value as $key2=>$value2)
        {
            $linenumber+=1;
            if ($linenumber==$count)
            $text[$key]=$text[$key].$key2.' '.number_format($value2/$tot*100,1) . '%';
            else
            $text[$key]=$text[$key].$key2.' '.number_format($value2/$tot*100,1) . '%
';
        }
    }
    $pdf->Write(10,"FINAL STRUCTURE:");
    $pdf->ln(10);
    $pdf->SetLeftMargin(20);
    foreach ($text as $key=>$value)
    {
        if ($key=="year")
        $pdf=boxtext($pdf,30,strtoupper($key),$value,0);
        else
        $pdf=boxtext($pdf,65,strtoupper($key),$value,0);
        $pdf->SetXY(20,$_SESSION['maxY']+3);
        
    }
    return $pdf;
}

function printelement2($structure, $column)
{
    $rs='';
    if ($structure!="")
    {
        $totgallons=0;
        foreach ($structure[$column] as $key => $value)
        {
            $totgallons+=$value;
        }
        foreach ($structure[$column] as $key => $value)
        {
            if ($totgallons>0)
            {
                $rs=$rs.$key . '-'. number_format($value/$totgallons*100,0).'% ';
            }
            else
            {
                $rs=$rs.$key .'-0% ';
            }
        }
    }
    return $rs;
}

function showstructure2 ($structure,$titles=1)
{
    $rs='';
    if (count($structure)>0)
    {
        $rs=$rs.printelement2($structure, 'year');
        $rs=$rs.printelement2($structure, 'variety');
        $rs=$rs.printelement2($structure, 'appellation').'
';
        $rs=$rs.printelement2($structure, 'vineyard').'
';
    }
    return $rs;
}

function gen_bol_page($pdf,$bolid)
{
    $alcabove=0;
    $alcbelow=0;
 //   $query='SELECT * FROM bol WHERE bol.ID="'.$bolid.'"';
  	$query='SELECT bol.ID, BONDED, DIRECTION, FACILITYID, DATE, CLIENTCODE, locations.NAME,locations.ADDRESS1,locations.ADDRESS2,locations.CITY,locations.STATE,
			locations.ZIP,locations.BONDNUMBER AS BOND FROM bol LEFT OUTER JOIN locations ON bol.FACILITYID = locations.ID WHERE bol.ID="'.$bolid.'"';
  
//	echo $query; exit;
	$result=mysql_query($query);
    $bol=mysql_fetch_assoc($result);
    $pdf=theheader($pdf);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(195,7,'STRAIGHT BILL OF LADING '.$bolid,0,1,"C");
    $pdf->ln(25);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,7,('DATE: '.date("m/d/Y",strtotime($bol['DATE']))),0,1,"L");
    $pdf->ln(5);
    $pdf->SetFont('Arial','B',8);
    if ($bol['BONDED']=="BONDTOBOND")
    {
        $pdf->Cell(7,7,'X',1,0,"C");
    }
    else
    {
        $pdf->Cell(7,7,'',1,0,"C");
    }
    $pdf->Cell(70,7,'BOND TO BOND',0,1,"L");
    if ($bol['BONDED']!="BONDTOBOND")
    {
        $pdf->Cell(7,7,'X',1,0,"C");
    }
    else
    {
        $pdf->Cell(7,7,'',1,0,"C");
    }
    $pdf->Cell(70,7,'TAX PAID',0,1,"L");
    $pdf->ln(5);
    $pdf->SetFont('Arial','B',8);
    if ($bol['DIRECTION']=="IN")
    {
        $pdf->Cell(20,4,'FROM:',0,0,"L");
        $pdf->Cell(150,4,$bol['NAME'].' '.$bol['BOND'],0,1,"L");
        $pdf->Cell(20,4,'(Consignor)',0,0,"L");
        $pdf->Cell(150,4,$bol['ADDRESS1'],0,1,"L");
        if ($bol['ADDRESS2']!="")
        {
            $pdf->Cell(20,4,'',0,0,"L");
            $pdf->Cell(150,4,$bol['ADDRESS2'],0,1,"L");
        }
        $pdf->Cell(20,4,'',0,0,"L");
        $pdf->Cell(150,4,$bol['CITY'].', '.$bol['STATE'].' '.$bol['ZIP'],0,1,"L");
    }
    else
    {
        $query='SELECT clients.* from bol inner join clients on (bol.CLIENTCODE=clients.CODE) WHERE bol.ID="'.$bolid.'"';
        $result=mysql_query($query);
        $row=mysql_fetch_array($result);
        
        $pdf->Cell(20,4,'FROM:',0,0,"L");
        $pdf->Cell(150,4,'COPAIN WINE CELLARS - BW-CA-6302',0,1,"L");
        $pdf->Cell(20,4,'(Consignee)',0,0,"L");
        $pdf->Cell(150,4,'FOR WINERY: '.$row['CLIENTNAME'],0,1,"L");
        $pdf->Cell(20,4,'',0,0,"L");
        $pdf->Cell(150,4,'1160B HOPPER AVE',0,1,"L");
        $pdf->Cell(20,4,'',0,0,"L");
        $pdf->Cell(150,4,'SANTA ROSA, CA 95403',0,1,"L");
    }
    $pdf->ln(5);
    if ($bol['DIRECTION']=="OUT")
    {
        $pdf->Cell(20,4,'TO:',0,0,"L");
        $pdf->Cell(150,4,$bol['NAME'].' '.$bol['BOND'],0,1,"L");
        $pdf->Cell(20,4,'(Consignor)',0,0,"L");
        $pdf->Cell(150,4,$bol['ADDRESS1'],0,1,"L");
        if ($bol['ADDRESS2']!="")
        {
            $pdf->Cell(20,4,'',0,0,"L");
            $pdf->Cell(150,4,$bol['ADDRESS2'],0,1,"L");
        }
        $pdf->Cell(20,4,'',0,0,"L");
        $pdf->Cell(150,4,$bol['CITY'].', '.$bol['STATE'].' '.$bol['ZIP'],0,1,"L");
    }
    else
    {
        $query='SELECT clients.* from bol inner join clients on (bol.CLIENTCODE=clients.CODE) WHERE bol.ID="'.$bolid.'"';
        $result=mysql_query($query);
        $row=mysql_fetch_array($result);
        
        $pdf->Cell(20,4,'TO:',0,0,"L");
        $pdf->Cell(150,4,'COPAIN WINE CELLARS - BW-CA-6302',0,1,"L");
        $pdf->Cell(20,4,'(Consignee)',0,0,"L");
        $pdf->Cell(150,4,'FOR WINERY: '.$row['CLIENTNAME'],0,1,"L");
        $pdf->Cell(20,4,'',0,0,"L");
        $pdf->Cell(150,4,'1160B HOPPER AVE',0,1,"L");
        $pdf->Cell(20,4,'',0,0,"L");
        $pdf->Cell(150,4,'SANTA ROSA, CA 95403',0,1,"L");
    }
    $pdf->ln(10);
    $pdf->Cell(20,5,'LOT',1,0,"C");
    $pdf->Cell(20,5,'GLNS',1,0,"C");
    $pdf->Cell(100,5,'DESCRIPTION',1,0,"C");
    $pdf->Cell(20,5,'ALC',1,0,"C");
    $pdf->Cell(20,5,'SULFITES',1,1,"C");
    $pdf->SetFont('Arial','',8);
    if ($bol['DIRECTION']=="OUT")
    {
        $query='SELECT bol.DATE, lots.DESCRIPTION, bolitems.ALC, lots.LOTNUMBER, bolitems.GALLONS from bolitems inner join bol on (bolitems.BOLID=bol.ID) inner join lots on (bolitems.LOT=lots.LOTNUMBER) WHERE bol.ID="'.$bolid.'"';
        //      echo $query;
        $result=mysql_query($query);
        for ($i=0; $i<mysql_num_rows($result);$i++)
        {
            $row=mysql_fetch_array($result);
            $record=lotinforecords($row['LOTNUMBER'],'BOL',$row['DATE']);
            $lr=count($record)-1;
            $pdf->Cell(20,5,$row['LOTNUMBER'],0,0,"C");
            $pdf->Cell(20,5,number_format($row['GALLONS'],2),0,0,"C");
            if ($row['ALC']==">=14%")
            $alcabove+=$row['GALLONS'];
            else
            $alcbelow+=$row['GALLONS'];
            $pdf->Cell(100,5,strtoupper($row['DESCRIPTION']),0,0,"L");
            $pdf->Cell(20,5,$row['ALC'],0,0,"C");
            $pdf->Cell(20,5,'YES',0,1,"C");
            $pdf->Cell(40,5,'',0,0,"L");
            $pdf->SetFont('Arial','',6);
            $text=showstructure2($record[$lr-1]['structure'],0);
            $pdf->MultiCell(100,3,$text,0,"L");
            $pdf->SetFont('Arial','',8);
        }
    }
    else
    {
        $query='SELECT bol.DATE, lots.DESCRIPTION, bolitems.ALC, lots.LOTNUMBER, bolitems.GALLONS from bolitems inner join bol on (bolitems.BOLID=bol.ID) inner join lots on (bolitems.LOT=lots.LOTNUMBER) WHERE bol.ID="'.$bolid.'"';
        //      echo $query;
        $result=mysql_query($query);
        for ($i=0; $i<mysql_num_rows($result);$i++)
        {
            $row=mysql_fetch_array($result);
            $record=lotinforecords($row['LOTNUMBER'],'BOL',$row['DATE']);
            $lr=count($record)-1;
            $pdf->Cell(20,5,$row['LOTNUMBER'],0,0,"C");
            $pdf->Cell(20,5,number_format($row['GALLONS'],2),0,0,"C");
            if ($row['ALC']==">=14%")
            $alcabove+=$row['GALLONS'];
            else
            $alcbelow+=$row['GALLONS'];
            $pdf->Cell(100,5,strtoupper($row['DESCRIPTION']),0,0,"L");
            $pdf->Cell(20,5,$row['ALC'],0,0,"C");
            $pdf->Cell(20,5,'YES',0,1,"C");
            $pdf->Cell(40,5,'',0,0,"L");
            $text=showstructure2($record[$lr-1]['structure'],0);
            $pdf->Cell(100,5,$text,0,1,"L");
        }
    }
    $pdf->ln(5);
    $pdf->Line(10,$pdf->GetY(),190,$pdf->GetY());
    $pdf->Line(10,$pdf->GetY(),190,$pdf->GetY());
    $pdf->Cell(28,5,'TOTAL GALLONS <14%: ',0,0,"L");
    $pdf->Cell(20,5,$alcbelow,0,1,"R");
    $pdf->Cell(28,5,'TOTAL GALLONS >=14%: ',0,0,"L");
    $pdf->Cell(20,5,$alcabove,0,1,"R");
    $pdf->Cell(28,5,'TOTAL GALLONS: ',0,0,"L");
    $pdf->Cell(20,5,($alcabove+$alcbelow),0,1,"R");
    $pdf->ln(5);
    $pdf->Line(10,$pdf->GetY(),190,$pdf->GetY());
    $pdf->ln(10);
    $pdf->Cell(90,7,'CONSIGNOR:______________________________________ ',0,0,"L");
    $pdf->Cell(80,7,'CARRIER:________________________________________ ',0,1,"L");
    $pdf->Cell(90,7,'DATE:_________________ ',0,0,"L");
    $pdf->Cell(80,7,'DATE:_________________ ',0,1,"L");
    
    return $pdf;
}

function gen_fermprot_page($pdf,$fermprotid)
{
    $queryfermstemp='SELECT * FROM fermprot WHERE (fermprot.id="'.$_GET['fermprotid'].'")';
    $row =mysql_fetch_array(mysql_query($queryfermstemp));

    $querylot='SELECT * FROM lots WHERE (lots.LOTNUMBER="'.$row['LOT'].'")';
    $resultlot=mysql_query($querylot);
    
    $leftmargin=$pdf->GetX();
    $rightmargin=10;
    $pdf=theheader($pdf);
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(195,7,'FERMENTATION PROTOCOL',0,1,"C");
    $pdf->Cell(195,7,'LOT: '.$row['LOT'].'  VESSEL: '.$row['VESSELTYPE'].'-'.$row['VESSELID'],0,1,"C");

    $pdf->ln(25);
    
    $pdf->SetFont('Arial','B',10);    
    $pdf->Cell(65,10,'PUMP OVER: ',0,1,"L");

    $pdf->SetFont('Arial','B',9);    
    $pdf->Cell(10,10,'',0,0,"L");
    $pdf->Cell(20,10,'START',0,0,"C");
    $pdf->Cell(20,10,'END',0,0,"C");
    $pdf->Cell(20,10,'FREQ',0,0,"C");
    $pdf->Cell(20,10,'DURATION',0,0,"C");
    $pdf->Cell(20,10,'TIMESLOT',0,1,"C");

    $pdf->Cell(10,10,'',0,0,"L");
    if (strtoupper($row['PO'])=="YES")
    {
    $pdf->Cell(20,10,$row['POSTARTBRIX'],1,0,"C");
    $pdf->Cell(20,10,$row['POENDBRIX'],1,0,"C");
    $pdf->Cell(20,10,$row['POFREQ'],1,0,"C");
    $pdf->Cell(20,10,$row['PODURATION'],1,0,"C");
    $pdf->Cell(20,10,$row['TIMESLOT1'],1,1,"C");
    }
    else
    {
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,1,"C");
    }

    $pdf->Cell(10,10,'',0,0,"L");
    if (strtoupper($row['PO2'])=="YES")
    {
    $pdf->Cell(20,10,$row['POSTARTBRIX2'],1,0,"C");
    $pdf->Cell(20,10,$row['POENDBRIX2'],1,0,"C");
    $pdf->Cell(20,10,$row['POFREQ2'],1,0,"C");
    $pdf->Cell(20,10,$row['PODURATION2'],1,0,"C");
    $pdf->Cell(20,10,$row['POTIMESLOT2'],1,1,"C");
    }
    else
    {
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,1,"C");
    }

    $pdf->ln(10);
    
    $pdf->SetFont('Arial','B',10);    
    $pdf->Cell(65,10,'PUNCH DOWNS: ',0,1,"L");

    $pdf->SetFont('Arial','B',9);    
    $pdf->Cell(10,10,'',0,0,"L");
    $pdf->Cell(20,10,'START',0,0,"C");
    $pdf->Cell(20,10,'END',0,0,"C");
    $pdf->Cell(20,10,'FREQ',0,0,"C");
    $pdf->Cell(20,10,'STRENGTH',0,0,"C");
    $pdf->Cell(20,10,'TIMESLOT',0,1,"C");

    $pdf->Cell(10,10,'',0,0,"L");
    if (strtoupper($row['PD'])=="YES")
    {
    $pdf->Cell(20,10,$row['PDSTARTBRIX'],1,0,"C");
    $pdf->Cell(20,10,$row['PDENDBRIX'],1,0,"C");
    $pdf->Cell(20,10,$row['PDFREQ'],1,0,"C");
    $pdf->Cell(20,10,$row['PDSTRENGTH'],1,0,"C");
    $pdf->Cell(20,10,$row['TIMESLOT2'],1,1,"C");
    }
    else
    {
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,1,"C");
    }


    $pdf->Cell(10,10,'',0,0,"L");
    if (strtoupper($row['PD2'])=="YES")
    {
    $pdf->Cell(20,10,$row['PDSTARTBRIX2'],1,0,"C");
    $pdf->Cell(20,10,$row['PDENDBRIX2'],1,0,"C");
    $pdf->Cell(20,10,$row['PDFREQ2'],1,0,"C");
    $pdf->Cell(20,10,$row['PDSTRENGTH'],1,0,"C");
    $pdf->Cell(20,10,$row['POTIMESLOT2'],1,1,"C");
    }
    else
    {
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(20,10,'',1,1,"C");
    }

    $pdf->SetFont('Arial','B',10);    

    $pdf->ln(10);

//    $pdf->Cell(20,10,'SPECIAL INSTRUCTIONS:',0,1);
    $pdf->SetFont('Arial','',8);
    $pdf = boxtext($pdf,200,'SPECIAL INSTRUCTIONS',$row['COMMENT'],1);
//    $pdf->Cell(200,10,$row['COMMENT'],1,1,"L");
    
    $addquery = 'SELECT `additions`.`SUPERFOODAMT`,
      `additions`.`DAPAMOUNT`,
      `additions`.`HTAAMOUNT`,
      `additions`.`GOAMOUNT`,
      `additions`.`WATERAMOUNT`,
      `additions`.`INNOCULATIONBRAND`,
      `additions`.`INNOCULATIONAMOUNT`,
      `additions`.`LABTEST`,
      `additions`.`BRIX`,
      `fpaddmap`.`DATE`,
      `fpaddmap`.`ID`
    FROM
      `fpaddmap`
      INNER JOIN `fermprot` ON (`fpaddmap`.`FERMPROTID` = `fermprot`.`id`)
      INNER JOIN `additions` ON (`fpaddmap`.`ADDITIONID` = `additions`.`ID`)
    WHERE
      (`fpaddmap`.`FERMPROTID` ='.$_GET['fermprotid'].')';
    $addresult=mysql_query($addquery);

    $pdf->SetFont('Arial','B',10);
    
    $pdf->ln(20);
    $pdf->Cell(20,1,'',0,0,"C");
    $pdf->Cell(15,1,'',0,0,"C");
    $pdf->Cell(25,1,'LAB',0,0,"C");
    $pdf->Cell(15,1,'',0,0,"C");
    $pdf->Cell(15,1,'',0,0,"C");
    $pdf->Cell(15,1,'',0,0,"C");
    $pdf->Cell(15,1,'',0,0,"C");
    $pdf->Cell(15,1,'',0,0,"C");
    $pdf->Cell(15,1,'INNOC',0,0,"C");
    $pdf->Cell(15,1,'INNOC',0,1,"C");
    $pdf->Cell(20,8,'DATE',0,0,"C");
    $pdf->Cell(15,8,'BRIX',0,0,"C");
    $pdf->Cell(25,8,'TEST',0,0,"C");
    $pdf->Cell(15,8,'SF',0,0,"C");
    $pdf->Cell(15,8,'DAP',0,0,"C");
    $pdf->Cell(15,8,'HTA',0,0,"C");
    $pdf->Cell(15,8,'GO',0,0,"C");
    $pdf->Cell(15,8,'H20',0,0,"C");
    $pdf->Cell(15,8,'WITH',0,0,"C");
    $pdf->Cell(15,8,'AMOUNT',0,1,"C");

    for ($i=0; $i<mysql_num_rows($addresult); $i++)
    {
        $row=mysql_fetch_array($addresult);
        
    if ($row['BRIX']!="")
    {
    $pdf->Cell(20,10,'',1,0,"C");
    $pdf->Cell(15,10,$row['BRIX'],1,0,"C");
    }
    else
    {
    $pdf->Cell(20,10,$row['DATE'],1,0,"C");
    $pdf->Cell(15,10,'',1,0,"C");
    }
    $pdf->Cell(25,10,$row['LABTEST'],1,0,"C");
    $pdf->Cell(15,10,$row['SUPERFOODAMT'],1,0,"C");
    $pdf->Cell(15,10,$row['DAPAMOUNT'],1,0,"C");
    $pdf->Cell(15,10,$row['HTAAMOUNT'],1,0,"C");
    $pdf->Cell(15,10,$row['GOAMOUNT'],1,0,"C");
    $pdf->Cell(15,10,$row['WATERAMOUNT'],1,0,"C");
    $pdf->Cell(15,10,$row['INNOCULATIONBRAND'],1,0,"C");
    $pdf->Cell(15,10,$row['INNOCULATIONAMOUNT'],1,1,"C");

    }
    
    return $pdf;

}

function gen_scp_page($pdf,$woid,$thecolor="RED")
{
    // $color[0]="RED";
    // $color[1]="GREEN";
    // $color[2]="BLUE";
    // $color[3]="PURPLE";
    // $color[4]="ORANGE";
    // $color[5]="BROWN";
    // $color[6]="YELLOW";
    // $color[7]="WHITE";
    // $color[8]="RED-BLUE";
    // $color[9]="RED-PURPLE";
    // $color[10]="RED-ORANGE";
    // $color[11]="RED-BROWN";
    // $color[12]="RED-YELLOW";
    // $color[13]="BLUE-GREEN";
    // $color[14]="BLUE-PURPLE";
    // $color[15]="BLUE-ORANGE";
    // $color[16]="BLUE-BROWN";
    // $color[17]="BLUE-YELLOW";
    // $color[18]="BLUE-WHITE";
    // $color[19]="GREEN-PURPLE";
    // $color[20]="GREEN-ORANGE";
    // $color[21]="GREEN-BROWN";
    // $color[22]="GREEN-YELLOW";
    // $color[23]="GREEN-WHITE";
    // $color[24]="PURPLE-ORANGE";
    // $color[25]="PURPLE-BROWN";
    // $color[26]="PURPLE-YELLOW";
    // $color[27]="PURPLE-WHITE";
    // $color[28]="ORANGE-BROWN";
    // $color[29]="ORANGE-YELLOW";

    $query='SELECT * FROM wo WHERE wo.ID="'.$woid.'"';
    $result=mysql_query($query);
    $wo=mysql_fetch_array($result);
    
    $leftmargin=$pdf->GetX();
    $rightmargin=10;
    $pdf=theheader($pdf);
    $pdf->SetFont('Arial','B',14);
    $query='SELECT *,locations.NAME as VYD, locations.APPELLATION as APL, locations.REGION FROM scp left outer join wo on (wo.ID=scp.WOID) left outer join locations on (locations.ID=scp.VINEYARDID) WHERE scp.WOID="'.$woid.'"';
    $result=mysql_query($query);
    $row=mysql_fetch_array($result);
    $pdf->SetFont('Arial','B',24);
	$pdf->Cell(195,7,$row['COLORCODE'],0,1,"R");
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(195,7,'STEMMING & CRUSHING PROTOCOL (SCP)',0,1,"C");
    $pdf->Cell(195,7,'SCP #:'.$row['ID'],0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/scppage.php?woid='.$woid);
    $pdf->Cell(195,7,'CLIENT: '.$row['CLIENTCODE'],0,1,"C");
    $pdf->ln(10);
    $pdf->SetFont('Arial','B',8);
    
    $pdf->Cell(65,10,'DATE: '.date("m/d/y",strtotime($wo['DUEDATE'])),1,0,"L");
    $pdf->Cell(65,10,'VARIETAL: '.$row['VARIETAL'],1,0,"L");
    $desc=$row['VYD'];
    if ($row['CLONE']|="") $desc = $desc . ' / '.$row['CLONE'];
    $pdf->Cell(65,10,'VINEYARD: '.$desc,1,1,"L");
    $pdf->Cell(65,10,'APPELLATION: '.$row['APL'],1,0,"L");
    $pdf->Cell(65,10,'ZONE: '.$row['REGION'],1,0,"L");
        $querytanks='SELECT `wo`.`ID`,`wo`.`DUEDATE`, `assets`.`NAME`, `wo`.`CLIENTCODE`, reservation.ID AS RESID, assettypes.ID AS ASSETTYPEID, assets.ID AS ASSETSID
        FROM  `assettypes`  INNER JOIN `assets` ON (`assettypes`.`ID` = `assets`.`TYPEID`)
                            INNER JOIN `reservation` ON (`assets`.`ID` = `reservation`.`ASSETID`)
                            INNER JOIN `wo` ON (`reservation`.`WOID` = `wo`.`ID`)
            WHERE  (wo.ID ="'.$woid.'") AND (assettypes.ID=6 OR assettypes.ID=8)';
    //echo $query;
    $resulttanks=mysql_query($querytanks);
    for ($i=0;$i<mysql_num_rows($resulttanks);$i++)
    {
        $rowtanks=mysql_fetch_array($resulttanks);
        if ($i>0)
        $thetanks.=', '.$rowtanks['NAME'];
        else
        $thetanks=$rowtanks['NAME'];
    }
        $querypress='SELECT `wo`.`ID`,`wo`.`DUEDATE`, `assets`.`NAME`, `wo`.`CLIENTCODE`, reservation.ID AS RESID, assettypes.ID AS ASSETTYPEID, assets.ID AS ASSETSID
        FROM  `assettypes`  INNER JOIN `assets` ON (`assettypes`.`ID` = `assets`.`TYPEID`)
                            INNER JOIN `reservation` ON (`assets`.`ID` = `reservation`.`ASSETID`)
                            INNER JOIN `wo` ON (`reservation`.`WOID` = `wo`.`ID`)
            WHERE  (wo.ID ="'.$woid.'") AND (assettypes.ID=2)';
    //echo $query;
    $resultpress=mysql_query($querypress);
    for ($i=0;$i<mysql_num_rows($resultpress);$i++)
    {
        $rowpress=mysql_fetch_array($resultpress);
        if ($i>0)
        $thepress.=', '.$rowpress['NAME'];
        else
        $thepress=$rowpress['NAME'];
    }

    $pdf->Cell(65,10,'EST TONS: '.$row['ESTTONS'],1,1,"L");
    $pdf->Cell(65,10,'TANKS: '.$thetanks,1,0,"L");
    $pdf->Cell(65,10,'PRESS: '.$thepress,1,0,"L");
    $pdf->Cell(65,10,'LOT: '.$wo['LOT'],1,1,"L");
    $pdf->ln(6);
    $pdf->SetFont('Arial','B',18);
    $pdf->Write(5,"SORTING:");
    $pdf->ln(6);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(10,10,'',0,0,"L");
    $pdf->Cell(70,10,'HAND SORTING REQUIRED: ',0,0,"L");
    if ($row['HANDSORTING']=="YES")
    {
        $pdf->Cell(10,10,'X',1,0,"C");
    }
    else
    {
        $pdf->Cell(10,10,'',1,0,"C");
    }
    $pdf->Cell(15,10,'YES',0,0,"L");
    if ($row['HANDSORTING']=="NO")
    {
        $pdf->Cell(10,10,'X',1,0,"C");
    }
    else
    {
        $pdf->Cell(10,10,'',1,0,"C");
    }
    $pdf->Cell(15,10,'NO',0,0,"L");
    if ($row['HANDSORTING']=="DIRECTTOPRESS")
    {
        $pdf->Cell(10,10,'X',1,0,"C");
    }
    else
    {
        $pdf->Cell(10,10,'',1,0,"C");
    }
    $pdf->Cell(15,10,'DIRECT TO PRESS',0,0,"L");
    $pdf->ln(12);
    $pdf->Cell(195,10,'(REMEMBER, CLIENT IS RESPONSIBLE FOR SORTING FRUIT)',0,1,"C");
    $pdf->ln(6);
    $pdf=boxtext($pdf,195,'SPECIAL INSTRUCTIONS',$row['SPECIALINSTRUCTIONS'],1);
    
    $pdf->ln(75);
    $pdf->SetFont('Arial','B',18);
    $pdf->Write(5,"STEMMING/CRUSHING:");
    $pdf->ln(8);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(10,10,'',0,0,"L");
    $pdf->Cell(50,10,'WHOLE CLUSTER %: ',0,0,"L");
    $pdf->Cell(15,10,$row['WHOLECLUSTER'].'%',1,0,"C");
    $pdf->Cell(50,10,'POSITION IN TANK: ',0,0,"R");
    if ($row['TANKPOSITION']=="TOP")
    {
        $pdf->Cell(10,10,'X',1,0,"C");
    }
    else
    {
        $pdf->Cell(10,10,'',1,0,"C");
    }
    $pdf->Cell(15,10,'TOP',0,0,"L");
    if ($row['TANKPOSITION']=="BOTTOM")
    {
        $pdf->Cell(10,10,'X',1,0,"C");
    }
    else
    {
        $pdf->Cell(10,10,'',1,0,"C");
    }
    $pdf->Cell(15,10,'BOTTOM',0,0,"L");
    $pdf->ln(15);
    $pdf->Cell(15,10,'');
    if ($row['CRUSHING']=="NOCRUSHING")
    {
        $pdf->Cell(10,10,'X',1,0,"C");
    }
    else
    {
        $pdf->Cell(10,10,'',1,0,"C");
    }
    $pdf->Cell(35,10,'NO CRUSHING',0,0,"L");
    if ($row['CRUSHING']=="PARTIAL")
    {
        $pdf->Cell(10,10,'X',1,0,"C");
    }
    else
    {
        $pdf->Cell(10,10,'',1,0,"C");
    }
    $pdf->Cell(45,10,'PARTIAL CRUSHING',0,0,"L");
    if ($row['CRUSHING']=="COMPLETE")
    {
        $pdf->Cell(10,10,'X',1,0,"C");
    }
    else
    {
        $pdf->Cell(10,10,'',1,0,"C");
    }
    $pdf->Cell(45,10,'COMPLETE CRUSHING',0,1,"L");
/*    $pdf->ln(4);
    $pdf->Line($pdf->GetX(),$pdf->GetY(),($pdf->GetX()+190),$pdf->GetY());
    $pdf->ln(1);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(192,5,'FOR COPAIN CUSTOM CRUSH USE ONLY',0,1,"C");
    $query='SELECT * FROM scp left outer join wo on (wo.ID=scp.WOID) WHERE scp.WOID="'.$woid.'"';
    $result=mysql_query($query);
    $row=mysql_fetch_array($result);
    $pdf->ln(3);
    $pdf->SetFont('Arial','B',8);
    $pdf->SetLeftMargin(30);
    $pdf->Cell(65,10,'APC: ',1,0,"L");
    $pdf->Cell(15,10,'',0,0);
    $pdf->Cell(65,10,'DATE: ',1,1,"L");
    $pdf->ln(2);
    $pdf->Cell(65,10,'LOT: '.$row['LOT'],1,0,"L");
    $pdf->Cell(15,10,'',0,0);
    $pdf->Cell(65,10,'TANK: '.$wo['tank'],1,1,"L");
    $pdf->Cell(65,10,'VARIETAL: '.$row['VARIETAL'],1,0,"L");
    $pdf->Cell(15,10,'',0,0);
    $pdf->Cell(65,10,'TBINS: '.$wo['tbins'],1,1,"L");
    $pdf->Cell(65,10,'NO BINS: '.$row['BINCOUNT'],1,0,"L");
    $pdf->Cell(15,10,'',0,0);
    $pdf->Cell(65,10,'WH CLSTER BINS: '.$wo['tank'],1,1,"L");
    $pdf->Cell(65,10,'TONS: '.$wo['tons'],1,0,"L");
    $pdf->Cell(15,10,'',0,0);
    $pdf->Cell(65,10,'ADDITIONS: '.$wo['tank'],1,1,"L");
    $pdf->ln(2);
    $pdf=boxtext($pdf,165,'NOTES',$row['NOTES'],1);
    $pdf->SetLeftMargin($leftmargin);
  */  
    return $pdf;
    
}

function gen_presssheet_page($pdf,$woid)
{
    $wo=getwo($woid);
    $leftmargin=$pdf->GetX();
    $rightmargin=10;
    $pdf=theheader($pdf);
    $pdf->SetFont('Arial','B',14);
    
    $query='SELECT `assets`.`NAME` FROM `reservation` INNER JOIN `assets` ON (`reservation`.`ASSETID` = `assets`.`ID`)
        WHERE (`assets`.`TYPEID` = "2") AND (`reservation`.`WOID` = '.$woid.') ORDER BY `assets`.`NAME`';
    $result=mysql_query($query);
    $row=mysql_fetch_array($result);
    
    $pdf->Cell(195,7,'PRESS SHEET',0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/presssheet.php?woid='.$woid);
    $pdf->ln(22);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(50,10,'DATE: '.$wo['duedate'],1,0,"L");
    $pdf->Cell(50,10,'LOT: '.$wo['lot'],1,0,"L");
    $pdf->Cell(50,10,'PRESS: '.$row['NAME'],1,0,"L");
    $pdf->Cell(50,10,'WO: '.$woid,1,1,"L",0,'http://www.copaincustomcrush.com/crushclient/wopage.php?action=view&woid='.$woid);
    $query='SELECT `barrelhistory`.`PRESSFRACTION`, `barrelhistory`.`BARRELNUMBER`, barrelhistory.ID,
              `barrels`.`DESCRIPTION`, `barrels`.`CLIENTCODE`, `barrels`.`FOREST`, `barrels`.`CAPACITY`
              FROM `barrelhistory` LEFT OUTER JOIN `barrels` ON (`barrelhistory`.`BARRELNUMBER` = `barrels`.`NUMBER`)
              WHERE (`barrelhistory`.`WOID` = "'.$woid.'")';
    
    $pdf->ln(10);
    $pdf->SetLeftMargin(40);
    $pdf->Cell(30,10,'BBL',0,0,"C");
    $pdf->Cell(40,10,'DESCRIPTION',0,0,"C");
    $pdf->Cell(10,10,'FRST',0,0,"C");
    $pdf->Cell(30,10,'FRACTION',0,0,"C");
    $pdf->Cell(30,10,'GALLONS',0,1,"C");
    
    
    $result=mysql_query($query);
    $numrows=mysql_num_rows($result);
    $pdf->SetFont('Arial','',8);
    for ($i=0;$i<$numrows;$i++)
    {
        $row=mysql_fetch_array($result);
        $pdf->Cell(30,5,$row['BARRELNUMBER'],1,0,"C");
        $pdf->Cell(40,5,$row['DESCRIPTION'],1,0,"C");
        $pdf->Cell(10,5,$row['FOREST'],1,0,"C");
        if ($row['PRESSFRACTION']==0)
        {
            $pdf->Cell(30,5,'FREE RUN',1,0,"C");
        }
        else
        {
            $pdf->Cell(30,5,$row['PRESSFRACTION'],1,0,"C");
        }
        $pdf->Cell(30,5,$row['CAPACITY'],1,1,"C");
        
    }
    $pdf->SetLeftMargin(10);
    return $pdf;
}

function gen_bottling_page($pdf,$woid)
{
    $wo=getwo($woid);

	$query = 'select lot from wo where (wo.ID='.$_GET['woid'].')';
	$result = mysql_query($query);
	$row=mysql_fetch_array($result);
	$lotinfo=lotinforecords($row['lot']);
	$thelotinfo=lotinfo($row['lot']);
	
    $leftmargin=$pdf->GetX();
    $rightmargin=10;
    $pdf=theheader($pdf);
    $pdf->SetFont('Arial','B',14);
        
    $pdf->Cell(195,7,'BOTTLING REPORT',0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/presssheet.php?woid='.$woid);

    $pdf->SetFont('Arial','B',14);
    $pdf->ln(10);
    $pdf->SetLeftMargin(20);
    $pdf->Cell(160,10,$thelotinfo['YEAR'].' '.$thelotinfo['DESCRIPTION'],0,0,"C");
    
    $pdf->ln(22);
    $pdf->SetFont('Arial','B',10);
    $pdf->SetLeftMargin(20);
    $pdf->Cell(50,10,'DATE: '.date("m/d/y",strtotime($wo['duedate'])),1,0,"L");
    $pdf->Cell(50,10,'LOT: '.$wo['lot'],1,0,"L");
    $pdf->Cell(50,10,'WO: '.$woid,1,1,"L",0,'http://www.copaincustomcrush.com/crushclient/wopage.php?action=view&woid='.$woid);
    $query='SELECT * FROM bottling WHERE (`bottling`.`WOID` = "'.$woid.'")';
    
    $pdf->ln(10);
    $pdf->SetLeftMargin(20);
    $pdf->Cell(40,10,$thelotinfo['YEAR'].' '.$thelotinfo['DESCRIPTION'],0,0,"C");
    
    $pdf->ln(10);
    $pdf->SetLeftMargin(20);
    $pdf->Cell(40,10,'LABEL APPROVAL',0,0,"C");
    $pdf->Cell(40,10,'EST CASE COUNT',0,0,"C");
    $pdf->Cell(40,10,'FINAL CASE COUNT',0,0,"C");
    $pdf->Cell(40,10,'GLNS PER CASE',0,1,"C");
    
  
    $result=mysql_query($query);
    $row=mysql_fetch_array($result);
    $pdf->Cell(40,5,$row['LABELAPPROVAL'],1,0,"C");
    $pdf->Cell(40,5,$row['ESTCASECOUNT'],1,0,"C");
    $pdf->Cell(40,5,$row['FINALCASECOUNT'],1,0,"C");
    $pdf->Cell(40,5,$row['GALLONSPERCASE'],1,0,"C");

    $pdf->ln(10);
    $pdf->SetLeftMargin(20);
    $pdf->Cell(40,10,'TIME',0,0,"C");
    $pdf->Cell(40,10,'AMOUNT',0,0,"C");
    $pdf->Cell(40,10,'CORRECTION',0,0,"C");
    $pdf->Cell(40,10,'CORRECTION TIME',0,1,"C");

    $query='select * from filllevels where (filllevels.BOTTLINGID = '.$row['ID'].')';
    $result=mysql_query($query);
    $numrows=mysql_num_rows($result);
    $pdf->SetFont('Arial','',8);
    for ($i=0;$i<$numrows;$i++)
    {
        $row=mysql_fetch_array($result);
        $pdf->Cell(40,5,date("h:m",strtotime($row['TIME'])),1,0,"C");
        $pdf->Cell(40,5,$row['AMOUNT'],1,0,"C");
        $pdf->Cell(40,5,$row['CORRECTION'],1,0,"C");
        $pdf->Cell(40,5,$row['CORRECTIONTIME'],1,1,"C");
    }
    
    $pdf->SetLeftMargin(10);
    return $pdf;
}

function gen_wt_page($pdf,$wt)
{
    
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(90,10,"WEIGHMASTER'S CERTIFICATE",0,1,"C");
    $body="THIS IS TO CERTIFY that the following described commodity was weighed, ";
    $body=$body . "measured, or counted by a weighmaster, whose signature is on this certificate, ";
    $body=$body . "who is a recognized authority of accuracy as described by Chapter 7 (commencing with ";
    $body=$body . " Section 12700) of Division 5 of California Business and Professions Code, administered ";
    $body=$body . "by the Division of Measurement Standards of the California Department of Food and Agriculture.";
    
    $query='SELECT wt.ID,UNIX_TIMESTAMP(wt.DATETIME) AS THEDATE, locations.ORGANIC, locations.REGION, wt.TAGID, clients.CLIENTNAME,
       wt.VARIETY, wt.VOID, locations.NAME as VINEYARD, locations.REGION as REGIONCODE, wt.LOT, clients.CODE, wt.TRUCKLICENSE, wt.TRAILERLICENSE FROM wt 
       INNER JOIN clients ON (wt.CLIENTCODE = clients.CLIENTID) left outer join locations on (locations.ID=wt.VINEYARDID) WHERE wt.TAGID="'.($wt-5000).'"';
    $result=mysql_query($query);
//echo $query; exit;
    if (mysql_num_rows($result)>0)
    {
        $row=mysql_fetch_array($result);
        $wtid=$row['ID'];
    }
    
    $pdf->SetFont('Arial','',8);
    $pdf->SetRightMargin(100);
    $pdf->Write(5,$body);
    $pdf->SetLeftMargin(10);
    $pdf->SetRightMargin(10);
    
    $pdf->SetFont('Arial','B',18);
    $pdf->SetLeftMargin(175);
    $pdf->SetY(3);
    if ($row['VOID']=="YES")
    	$pdf->Cell(50,10,'VOID',0,1,"L");
    else
	    $pdf->Cell(50,10,'COPY',0,1,"L");
    $pdf->SetLeftMargin(150);
    $pdf->SetY(10);
    $pdf->Cell(50,10,'NO: '.($row['TAGID']+5000),0,1,"L");
    $pdf->SetFont('Arial','B',12);
    $pdf->ln(4);
    $pdf->Cell(50,6,'DATE: '.date("M d, Y",$row['THEDATE']),1,1,"L");
    $pdf->Cell(50,6,'TIME: '.date("g:i A",$row['THEDATE']),1,1,"L");
    $pdf->Rect(150,40,50,30);
    $pdf->SetXY(150,41);
    $pdf->SetFont('Arial','',8);
    $pdf->Write(5,'1-Mendocino'); $pdf->ln(3);
    $pdf->Write(5,'2-Lake County'); $pdf->ln(3);
    $pdf->Write(5,'3-Sonoma County'); $pdf->ln(3);
    $pdf->Write(5,'4-Napa County'); $pdf->ln(3);
    $pdf->Write(5,'5-Solano County'); $pdf->ln(3);
    $pdf->Write(5,'6-Alameda, Costa County'); $pdf->ln(3);
    $pdf->Write(5,'7-Monterey County'); $pdf->ln(3);
    $pdf->Write(5,'8-Santa Barbara'); $pdf->ln(3);
    $pdf->SetXY(180,45);
    $pdf->SetFont('Arial','B',18);
    $pdf->Cell(10,10,substr($row['REGIONCODE'],0,1),1,0,"C");
    $pdf->SetLeftMargin(15);
    
    $pdf->SetXY(15,60);
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,5,'Consignee (Winery):',0,0,"R");
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(50,5,$row['CLIENTNAME'],1,1,"C");
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,5,'Variety:',0,0,"R");
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(50,5,$row['VARIETY'],1,1,"C");
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,5,'Vineyard:',0,0,"R");
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(50,5,$row['VINEYARD'],1,1,"C");
    
   $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,5,'Organic:',0,0,"R");
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(50,5,$row['ORGANIC'],1,1,"C");
 
   $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,5,'Lot:',0,0,"R");
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(50,5,$row['LOT'],1,1,"C");
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,5,'Commodity:',0,0,"R");
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(50,5,'Wine Grapes',1,1,"C");
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,5,'Truck License:',0,0,"R");
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(50,5,$row['TRUCKLICENSE'],1,1,"C");
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,5,'Trailer License:',0,0,"R");
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(50,5,$row['TRAILERLICENSE'],1,1,"C");
    
    $binquery='SELECT SUM(bindetail.BINCOUNT) AS SUMBINCOUNT, SUM(bindetail.WEIGHT) AS SUMWEIGHT,
                SUM(bindetail.TARE) AS SUMTARE FROM bindetail WHERE bindetail.WEIGHTAG="'.$row['ID'].'"';
    $binresult=mysql_query($binquery);
    $binrow=mysql_fetch_array($binresult);
    
    $pdf->ln(2);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,5,'Number of Bins:',0,0,"R");
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(10,10,$binrow['SUMBINCOUNT'],1,1,"C");
    
    $pdf->SetXY(140,80);
    $pdf->SetLeftMargin(140);
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(20,5,'',0,0,"R");
    $pdf->Cell(20,5,'POUNDS',0,1,"C");
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(20,5,'GROSS:',0,0,"R");
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(20,5,number_format($binrow['SUMWEIGHT'],0),1,1,"C");
    $pdf->ln(2);
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(20,5,'TARE:',0,0,"R");
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(20,5,number_format($binrow['SUMTARE'],0),1,0,"C");
    $pdf->Cell(2,5,'');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(20,5,'TONS',0,1,"C");
    $pdf->ln(2);
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(20,5,'NET:',0,0,"R");
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(20,5,number_format(($binrow['SUMWEIGHT']-$binrow['SUMTARE']),0),1,0,"C");
    $pdf->Cell(2,5,'');
    $pdf->Cell(20,5,number_format((($binrow['SUMWEIGHT']-$binrow['SUMTARE'])/2000),3),1,1,"C");
    
    $pdf->SetLeftMargin(15);
    $pdf->ln(10);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(195,5,'COPAIN CUSTOM CRUSH, LLC',0,1,"C");
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(195,5,'1160B HOPPER AVE.',0,1,"C");
    $pdf->Cell(195,5,'SANTA ROSA, CA 95403',0,1,"C");
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(195,5,'O: 707.541.7474  F: 707.541.7575',0,1,"C");
    $pdf->ln(10);
    
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(30,5,'');
    $pdf->Cell(40,5,'Deputy Weighmaster:',0,0,"R");
    $pdf->Line(88,$pdf->GetY()+5,150,$pdf->GetY()+5);

    $binlistquery='SELECT * from bindetail WHERE bindetail.WEIGHTAG="'.$wtid.'"';
    $binlistresult=mysql_query($binlistquery);
    $pdf->ln(15);

    $pdf->SetLeftMargin(20);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(150,5,"BIN DETAIL",1,1,"C");
    $pdf->Cell(30,5,"BIN CODE",1,0,"C");
    $pdf->Cell(30,5,"BIN COUNT",1,0,"C");
    $pdf->Cell(30,5,"WEIGHT (LBS)",1,0,"C");
    $pdf->Cell(30,5,"TARE (LBS)",1,0,"C");
    $pdf->Cell(30,5,"MISC",1,1,"C");
    $pdf->SetFont('Arial','',8);
    for ($k=0;$k<mysql_num_rows($binlistresult);$k++)
    {
       $rowbinlist=mysql_fetch_array($binlistresult);
       $pdf->Cell(30,5,$row['CODE'],1,0,"C");
       $pdf->Cell(30,5,$rowbinlist['BINCOUNT'],1,0,"C");
       $pdf->Cell(30,5,$rowbinlist['WEIGHT'],1,0,"C");
       $pdf->Cell(30,5,$rowbinlist['TARE'],1,0,"C");
       $pdf->Cell(30,5,$rowbinlist['MISC'],1,1,"C");
    }
    return $pdf;
}

function gen_lab_page($pdf,$woid)
{
    $wo=getwo($woid);
    $leftmargin=$pdf->GetX();
    $rightmargin=10;
    $pdf=theheader($pdf);
    $pdf->SetFont('Arial','B',14);
    
    $wo=getwo($woid);
    $query='SELECT * FROM labtest WHERE labtest.WOID="'.$woid.'"';
    $result=mysql_query($query);
    $row=mysql_fetch_array($result);
    $pdf->Cell(195,7,'LAB RESULTS SHEET',0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/labtest.php?woid='.$woid);
    $pdf->Cell(195,7,'LAB TEST #:'.$row['ID'],0,1,"C",0,'http://www.copaincustomcrush.com/crushclient/labtest.php?woid='.$woid);
    $pdf->ln(22);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(50,10,'DATE: '.date("m/d/Y",strtotime($wo['duedate'])),1,0,"L");
    $pdf->Cell(50,10,'LOT: '.$wo['lot'],1,0,"L");
    $pdf->Cell(50,10,'LAB: '.$row['LAB'],1,0,"L");
    $pdf->Cell(50,10,'WO: '.$woid,1,1,"L",0,'http://www.copaincustomcrush.com/crushclient/wopage.php?action=view&woid='.$woid);
    
    $query='SELECT * FROM labresults WHERE labresults.LABTESTID="'.$row['ID'].'"';
    $pdf->ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(50,5,'TEST',1,0,"C");
    $pdf->Cell(60,5,'RESULT',1,0,"C");
    $pdf->Cell(90,5,'COMMENT',1,1,"C");
    
    $pdf->SetFont('Arial','',8);
    $result=mysql_query($query);
    for ($i=0;$i<mysql_num_rows($result);$i++)
    {
        $row=mysql_fetch_array($result);
        $pdf->Cell(50,5,$row['LABTEST'],1,0,"C");
        $pdf->Cell(60,5,$row['VALUE1'].' '.$row['UNITS1'],1,0,"C");
        $pdf->Cell(90,5,$row['COMMENT'],1,1,"C");
        
    }
    $pdf->SetLeftMargin(10);
    
    return $pdf;
}
?>