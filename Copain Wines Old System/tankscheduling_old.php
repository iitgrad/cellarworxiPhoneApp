<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

   <script type="text/javascript" src="popup/overlibmws.js"></script>

   <script type="text/javascript" src="popup/overlibmws_draggable.js"></script>



<title>Fermentation Protocol</title>

<link rel="stylesheet" type="text/css" href="../site.css">

    <script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>



<?php



include ("startdb.php");

include ("yesno.php");

include ("setcheck.php");

include ("defaultvalue.php");

include ("manageadditions.php");

include ("queryupdatefunctions.php");

include ("lotinforecords.php");



?>



</head>



<body>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div> 





<?php



$today=time();

//$_SESSION['clientcode']=getclientcode();

if (isset($_GET['thedate']))

{

    $today=$_GET['thedate'];

}

if (isset($_GET['assettypeid']))

{

    $assettypeid=$_GET['assettypeid'];

}

else

$assettypeid=6;



$dayofweek=strftime("%w",$today);

$start_day=$today-(86400*14);



$query='SELECT DISTINCT assets.ID, reservation.WOID, `assets`.`NAME`, wo.CLIENTCODE, wo.LOT, wo.TYPE, assets.OWNER, `assets`.`CAPACITY`, UNIX_TIMESTAMP(`wo`.`DUEDATE`) AS STARTDATE,

          UNIX_TIMESTAMP(`wo`.`ENDDATE`) AS THEENDDATE

       FROM  `assets`  LEFT OUTER JOIN `reservation` ON (`assets`.`ID` = `reservation`.`ASSETID`)

           LEFT OUTER JOIN `wo` ON (`reservation`.`WOID` = `wo`.`ID`)

       WHERE

           (`assets`.`TYPEID` = "'.$assettypeid.'")

       ORDER BY

           `assets`.`NAME` ';

//echo $query;

$result=mysql_query($query);



for ($i=0;$i<mysql_num_rows($result);$i++)

{

    $row=mysql_fetch_array($result);

    for ($j=0;$j<28;$j++)

    {

        $theday=$start_day+(86400*$j);

        $dateofinterest=mktime(0,0,0,strftime("%m",$theday),strftime("%d",$theday),strftime("%Y",$theday));

        $startdate=mktime(0,0,0,strftime("%m",$row['STARTDATE']),strftime("%d",$row['STARTDATE']),strftime("%Y",$row['STARTDATE']));

        $enddate=mktime(0,0,0,strftime("%m",$row['THEENDDATE']),strftime("%d",$row['THEENDDATE']),strftime("%Y",$row['THEENDDATE']));

        $index=$row['NAME'];

        

        $tank[$index]['id']=$row['ID'];

        $tank[$index]['capacity']=$row['CAPACITY'];

        $tank[$index]['owner']=$row['OWNER'];

        $tank[$index]['woid']=$row['WOID'];

        $tank[$index]['type']=$row['TYPE'];

        $tank[$index]['lot']=$row['LOT'];

        $thevesseltype=explode("-",$row['NAME']);

        $tank[$index]['vesseltype']=$thevesseltype[0];

        $tank[$index]['vesselnum']=$thevesseltype[1];

        

        

        if (($dateofinterest>=$startdate) & ($dateofinterest<=$enddate))

        {

            $woquery='SELECT wo.TYPE, wo.WORKPERFORMEDBY, wo.REQUESTOR, wo.DUEDATE, wo.ENDDATE, wo.OTHERDESC, lots.DESCRIPTION, wo.LOT, scp.VARIETAL, scp.APPELLATION from wo left outer join lots on (lots.LOTNUMBER=wo.LOT) left outer join scp on (scp.WOID=wo.ID) where wo.id="'.$row['WOID'].'"';

            $woresult=mysql_query($woquery);

            $worow=mysql_fetch_array($woresult);

            $caption=$worow['TYPE'];

            if ($worow['TYPE']=="SCP")

            {

                $scpquery='select * from scp where scp.woid="'.$row['WOID'].'"';

                $scpresult=mysql_query($scpquery);

                $scprow=mysql_fetch_array($scpresult);

                $caption.=' '.$scprow['ID'];

                $data='<table align=center>';

                //                $data.='<tr><td></td></tr>';

                $data.='<tr><td width=35% align=right>LOT:</td>';

                $data.='<td align=left><a href=showlotinfo.php?lot='.$row['LOT'].'>'.$row['LOT'].'</a></td></tr>';

                $data.='<tr><td align=right>DESCRIPTION:</td>';

                $data.='<td align=left>'.preg_replace("/'/","\\'",preg_replace("/[\n\t\r]+/","",$worow['DESCRIPTION'])).'</td></tr>';

                $data.='<tr><td align=right>EST DAYS IN TANK:</td>';

                $data.='<td align=left>'.$scprow['ESTDAYSINTANK'].'</td></tr>';

                $data.='<tr><td align=right>VARIETAL:</td>';

                $data.='<td align=left>'.preg_replace("/[\n\t\r]+/","",$worow['VARIETAL']).'</td></tr>';

                $data.='<tr><td align=right>APPELLATION:</td>';

                $data.='<td align=left>'.preg_replace("/[\n\t\r]+/","",$worow['APPELLATION']).'</td></tr>';

                $data.='</table>';

                $tank[$index]['val'][$j]='<a  href="scppage.php?action=view&woid='.$row['WOID'].'" onmouseover="return overlib(\''.$data.'\',

                CAPTION,\'<c>'.$caption.'</c>\',

                WIDTH, 300, DRAGGABLE);"

                onmouseout="nd();">'.strtoupper($row['CLIENTCODE']).'</a>';

            }

            else {

                $data='<table align=center>';

                //                $data.='<tr><td></td></tr>';

                $data.='<tr><td width=35% align=right>LOT:</td>';

                $data.='<td align=left><a href=showlotinfo.php?lot='.$row['LOT'].'>'.$row['LOT'].'</a></td></tr>';

                $data.='<tr><td align=right>DESCRIPTION:</td>';

                $data.='<td align=left>'.preg_replace("/'/","\\'",preg_replace("/[\n\t\r]+/","",$worow['DESCRIPTION'])).'</td></tr>';

                $data.='<tr><td align=right>REQUESTOR:</td>';

                $data.='<td align=left>'.preg_replace("/'/","\\'",preg_replace("/[\n\t\r]+/","",$worow['REQUESTOR'])).'</td></tr>';

                $data.='<tr><td align=right>WORK TO BE PERFORMED BY:</td>';

                $data.='<td align=left>'.preg_replace("/'/","\\'",preg_replace("/[\n\t\r]+/","",$worow['WORKPERFORMEDBY'])).'</td></tr>';

                $data.='<tr><td align=right>COMMENTS:</td>';

                $data.='<td align=left>'.preg_replace("/'/","\\'",preg_replace("/[\n\t\r]+/","",$worow['OTHERDESC'])).'</td></tr>';

                $data.='</table>';

            

            $tank[$index]['val'][$j]='<a  href="wopage.php?action=view&woid='.$row['WOID'].'" onmouseover="return overlib(\''.$data.'\',

                CAPTION,\'<c>'.$caption.'</c>\',

                WIDTH, 300, DRAGGABLE);"

                onmouseout="nd();">'.strtoupper($row['CLIENTCODE']).'</a>';

            }

            if ($scprow['ESTDAYSINTANK']=="")

            {

               $daysintank=floor((strtotime($worow['ENDDATE'])-strtotime($worow['DUEDATE']))/86400)-1;

               //$daysintank=14;

            }

            else

            $daysintank=$scprow['ESTDAYSINTANK'];

            for ($allocindex=1;$allocindex<$daysintank;$allocindex++)

            {

 //               $tank[$index]['val'][$j+$allocindex].='X';

                $tank[$index]['val'][$j+$allocindex].='<a  href="scppage.php?action=view&woid='.$row['WOID'].'" onmouseover="return overlib(\''.$data.'\',

                CAPTION,\'<c>'.$caption.'</c>\',

                WIDTH, 300, DRAGGABLE);"

                onmouseout="nd();">'.'X'.'</a>';

            }

            

        }

    }

}



echo '<pre>';

//print_r($tank);

//exit;

$checkbrixquery='SELECT * from brixtemp WHERE brixtemp.DATE>="'.date("Y-m-d",$start_day).'" and brixtemp.DATE<"'.date("Y-m-d",($start_day+86400*28)).'"';

//echo $checkbrixquery;

$checkbrixresult=mysql_query($checkbrixquery);

for ($i=0;$i<mysql_num_rows($checkbrixresult);$i++)

{

    $row=mysql_fetch_array($checkbrixresult);

    $theday=strtotime($row['DATE']);

    $dayindex=(int)(($theday-$start_day)/86400)+1;

    $theindex=$row['vesseltype'].'-'.$row['vessel'];

    if (count($tank[$theindex])>0)

    {

        if (strlen($tank[$theindex]['val'])>0)

        $tank[$theindex]['val'][$dayindex].='<br>';

        if ($row['BRIX']<5)

        $tank[$theindex]['val'][$dayindex].='<b><font color=blue>'.$row['BRIX'].'</b></font>';

        else

        {

        $tank[$theindex]['val'][$dayindex].=$row['BRIX'];

 

        }

    }

}



$pressquery='select * from wo where wo.TYPE="PRESSOFF" and wo.DUEDATE>="'.date("Y-m-d",($today-(86400*7))).'" and DUEDATE<="'.date("Y-m-d",($today+(86400*21))).'"';

$pressresult=mysql_query($pressquery);

for ($k=0;$k<mysql_num_rows($pressresult);$k++)

{

   $row=mysql_fetch_array($pressresult);



}



echo '<pre>';

//print_r($tank);

echo '</pre>';



echo '<table align=center width=100%>';

echo '<tr><td align=left><a href='.$PHP_SELF.'?assettypeid='.$assettypeid.'&thedate='.($today-(86400*28)).'>PREVIOUS 4 WEEKS</a></td>';

echo '<td></td>';

echo '<td align=right><a href='.$PHP_SELF.'?assettypeid='.$assettypeid.'&thedate='.($today+(86400*28)).'>NEXT 4 WEEKS</a></td>';

echo '</tr>';

echo '</table>';

echo '<table id=one align=center width=100%>';

echo '<tr><td align=left><a href='.$PHP_SELF.'?assettypeid='.$assettypeid.'&thedate='.($today-(86400*7)).'>PREVIOUS WEEK</a></td>';

echo '<td></td>';

echo '<td align=right><a href='.$PHP_SELF.'?assettypeid='.$assettypeid.'&thedate='.($today+(86400*7)).'>NEXT WEEK</a></td>';

echo '</tr>';

echo '</table>';

echo '<table id=one align=center width=1000>';

echo '<tr><td align=center width=130>--------ASSET--------</td>';

for ($j=0;$j<28;$j++)

{

    $theday=$start_day+(86400*$j);

    $dateofinterest=mktime(0,0,0,strftime("%m",$theday),strftime("%d",$theday),strftime("%Y",$theday));

    echo '<td align=center>'.date("m/d",$dateofinterest).'</td>';

}

echo '</tr>';

if ($_GET['returnpage']!="")

$thereturn=$_GET['returnpage'];

else

$thereturn="wopage.php";



foreach ($tank as $key=>$value)

{

    echo '<tr>';

    echo '<td width=130 align=center>';

    echo '<a href='.$thereturn.'?action=addasset&assetid='.$value['id'].'>';

    echo $key.' '.$value['capacity'].' '.$value['owner'];

    echo '</a>';

    echo '</td>';

    for ($j=0;$j<28;$j++)

    {

        echo '<td width=10 align=center>';

        if ((isstaff()=="YES")|($value['val'][$j]==strtoupper($_SESSION['clientcode'])))

        {

            if ($value['type']==SCP)

            echo '<a href=scppage.php?action=view&woid='.$value['woid'].'>'.$value['val'][$j].'</a>';

            else

            echo '<a href=wopage.php?action=view&woid='.$value['woid'].'>'.$value['val'][$j].'</a>';

        }

        else

        echo $value['val'][$j];

        

        echo '</td>';

    }

    echo '</tr>';

}

echo '</table>';

?>

<script language="JavaScript">

<!--

tigra_tables('one', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

// -->

            </script>

</body>



</html>