<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

    <script language="JavaScript" src="../tigra_tables/tigra_tables.js"></script>



  <base target="maincontent">



</script>

    <script language="JavaScript" type="text/javascript">

    function multiLoad(doc1,doc2)

    {

        parent.list.location.href=doc1;

        parent.maincontent.location.href=doc2;

    }

    

</script>

<?php

  include ("startdb.php");

?>



</head>



<body>



<?php



include ("yesno.php");

include ("setcheck.php");

include ("defaultvalue.php");

include ("manageadditions.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");



$ci=clientinfo($_SERVER['PHP_AUTH_USER']);
//echo '--'.$_SERVER['PHP_AUTH_USER'];



if ($_GET['vintage']|='')

  $_SESSION['vintage']=$_GET['vintage'];

  

if ($_GET['clientcode']|='')

  $_SESSION['clientcode']=$_GET['clientcode'];

  

if ($_SESSION['clientcode']=='')

$_SESSION['clientcode']=$ci['code'];

if ($_SESSION['vintage']=='')

{

    $_SESSION['vintage']='2009';

}

//echo '<img  src=logo.gif>';

echo '<table id=staff align=center width=100%>';

if (isstaff()=="YES" | isingroup()=="YES")

{
    echo '<tr><td align=center><b><a target="maincontent" href=tutorials/index.html>NEW TUTORIALS</a></b><hr></td></tr>';

    echo '<tr><td align=center><b>STAFF MENU</b></td></tr>';

    echo '<tr><td align=center><a target="maincontent" href=wofind.html>FIND WORKORDER</a></td></tr>';

    echo '<tr><td align=center><a target="maincontent" href=picclient.php>CLIENT: '.strtoupper($_SESSION['clientcode']).'</a></td></tr>';

//  echo '<tr><td align=center><a href=# onmouseover="showRelativePanel(\'Main\',event)" onmouseout="hidePanel()">CLIENT: '.strtoupper($_SESSION['clientcode']).'</a></td></tr>';

    echo '<tr><td align=center><a target="maincontent" href=picvintage.php>VINTAGE: '.$_SESSION['vintage'].'</a></td></tr>';

//    echo '<tr><td align=center><a target="maincontent" href=piclot.php>702LOT: '.$_SESSION['702lot'].'</a></td></tr>';

    echo '<tr><td align=center><a target="maincontent" href=vintagesummary.php?summary=YES>FACILITY SUMMARY</a></td></tr>';

    echo '<tr><td align=center><a target="maincontent" href=wtsummarybyclient.php>TOTAL TONS BY CLIENT</a></td></tr>';

        echo '<tr><td align=center><a target="maincontent" href=wtsummarybyvarietal.php>TOTAL TONS BY VARIETAL</a></td></tr>';

}

else

{
    echo '<tr><td align=center><b><a target="maincontent" href=tutorials/index.html>NEW TUTORIALS</a></b><hr></td></tr>';

    echo '<tr><td align=center><b>CLIENT MENU</b></td></tr>';

    echo '<tr><td align=center>CLIENT: '.strtoupper($_SESSION['clientcode']).'</td></tr>';

    echo '<tr><td align=center><a target="maincontent" href=picvintage.php>VINTAGE: '.$_SESSION['vintage'].'</a></td></tr>';

}

echo '</table>';



echo '<table id=harvest align=center width=100%>';

echo '<tr><td align=center><br><b>HARVEST</b></td></tr>';

echo '<tr><td align=center><a href=scpcal.php>FRUIT ARRIVAL SCHEDULE</a></td></tr>';

echo '<tr><td align=center><a href=presscal.php>PRESS SCHEDULE</a></td></tr>';

echo '<tr><td align=center><a href=lotmgt.php>LOT MANAGEMENT</a></td></tr>';
echo '<tr><td align=center><a href=vineyards.php?action=show>VINEYARDS</a></td></tr>';

//if (isstaff()=="YES") echo '<tr><td align=center><a href=wtpage.php?action=newwt>CREATE WT</a></td></tr>';

//echo '<tr><td align=center><a href=prod2.php>WEIGH TAGS</a></td></tr>';

if (isstaff()=="YES") echo '<tr><td align=center><a target="maincontent" href=hardcopy/scpsforday.php>SCPS FOR CLIPBOARDS</a></td></tr>';

if (isstaff()=="YES") echo '<tr><td align=center><a target="maincontent" href=daysummary2.php>SCP DAILY SUMMARY</a></td></tr>';

echo '<tr><td align=center><a href=wtsummary.php?CLIENTCODE='.clientid($_SESSION['clientcode']).'>WEIGH TAGS</a></td></tr>';

if (isstaff()=="YES") echo '<tr><td align=center><a target="maincontent" href=scpforecast.php>SCP FORECAST</a></td></tr>';

if (isstaff()=="YES") echo '<tr><td align=center><a target="maincontent" href=wtsummary.php>WEIGHT TAG SUMMARY</a></td></tr>';

echo '</table>';

echo '<table id=ferm align=center width=100%>';

echo '<tr><td align=center><br><b>FERMENTATION PROTOCOLS</b></td></tr>';

echo '<tr><td align=center><a href=fermprotsheet.php>FERMENTATION PROTOCOLS</a></td></tr>';

if (isstaff()=="YES")

echo '<tr><td align=center><a href=activefermsinfacility.php>ACTIVE FERMENTATIONS</a></td></tr>';

else

echo '<tr><td align=center><a href=activefermsbyclient.php>ACTIVE FERMENTATIONS</a></td></tr>';

if (isstaff()=="YES") echo '<tr><td align=center><a target="maincontent" href=dailypopdsheet.php>GENERATE PO/PD SHEET</a></td></tr>';
if (isstaff()=="YES") echo '<tr><td align=center><a target="maincontent" href=dailypopdsheet2.php>UPDATE BRIX/TEMP</a></td></tr>';

echo '</table>';

if (isstaff()=="YES")
{
	echo '<table id=one align=center width=100%>';

	echo '<tr><td align=center><br><b>SUMMARIES</b></td></tr>';

	echo '<tr><td align=center><a href=wtsummarybyclient.php>WT SUMMARY BY CLIENT</a></td></tr>';
	echo '<tr><td align=center><a href=wtsummarybyclientasofyesterday.php>WT SUMMARY BY CLIENT TO DATE</a></td></tr>';
	echo '<tr><td align=center><a href=wtsummarybyvarietal.php>WT SUMMARY BY VARIETAL</a></td></tr>';

	echo "</table>";
}
echo '<table id=one align=center width=100%>';

echo '<tr><td align=center><br><b>SUMMARIES</b></td></tr>';

//echo '<tr><td align=center><a href=showlotinfo.php>LOT DETAIL</a></td></tr>';

echo '<tr><td align=center><a href=labtest_list.php>OUSTANDING WORK ORDERS</a></td></tr>';

echo '<tr><td align=center><a href=vintagesummary.php?clientcode='.$_SESSION['clientcode'].'&vintage='.$_SESSION['vintage'].'>VINTAGE SUMMARY</a></td></tr>';

echo '<tr><td align=center><a href=labview.php?clientcode='.$_SESSION['clientcode'].'&vintage='.$_SESSION['vintage'].'>LAB VIEW SUMMARY</a></td></tr>';

echo '<tr><td align=center><a href=bolmgt.php>BILL OF LADINGS</a></td></tr>';

$startdate=date("m",time()).'/1/'.date("Y",time());

$enddate=(date("m",time())+1).'/1/'.date("Y",time());

if (isstaff()=="YES") echo '<tr><td align=center><a href=702view.php?ccode=&month='.date("m",time()).'&year='.date("Y",time()).'>702 VIEW</a></td></tr>';

echo '</table>';



echo '<table id=schedules align=center width=100%>';

echo '<tr><td align=center><br><b>CALENDARS</b></td></tr>';

echo '<tr><td align=center><a href=wocal.php>WO CALENDAR</a></td></tr>';

//echo '<tr><td align=center><a href=assetsched.php>ASSET CALENDAR</a></td></tr>';

echo '<tr><td align=center><a href=tankscheduling.php>TANK SCHEDULES</a></td></tr>';

echo '<tr><td align=center><a href=tankscheduling.php?assettypeid=8>TBIN SCHEDULES</a></td></tr>';

echo '<tr><td align=center><a href=tankscheduling.php?assettypeid=14>PORTA TANK SCHEDULES</a></td></tr>';

//echo '<tr><td align=center><a href=tankscheduling.php?assettypeid=9>WORK AREAS</a></td></tr>';

//echo '<tr><td align=center><a href=tankscheduling.php?assettypeid=11>FORKLIFTS</a></td></tr>';

//echo '<tr><td align=center><a href=tankscheduling.php?assettypeid=3>EQUIPMENT</a></td></tr>';

//echo '<tr><td align=center><a href=assetsched.php?assetid=30>BOTTLING LINE CALENDAR</a></td></tr>';

echo '</table>';





echo '<table id=two align=center width=100%>';

echo '<tr><td align=center><br><b>ACTIVITIES</b></td></tr>';

echo '<tr><td align=center><a href=wopage.php?action=new>CREATE WORK ORDER</a></td></tr>';

if (isstaff()=="YES")

echo '<tr><td align=center><a href=listlots.php?action=new>CREATE MULTIPLE WORK ORDERS</a></td></tr>';

echo '<tr><td align=center><a href=bolmgt.php>CREATE BILL OF LADING</a></td></tr>';

//if (isstaff()=="YES") echo '<tr><td align=center><a href=barrelinventory.php>BBL MANAGEMENT</a></td></tr>';

echo '<tr><td align=center><a href=barrelinventory.php>BBL MANAGEMENT</a></td></tr>';

echo '</table>';







if (isstaff()=="YES") 

{

        echo '<table id=staff align=center width=100%>';

            echo '<tr><td align=center><br><b>OTHER</b></td></tr>';

        echo '<tr><td align=center><a href=../inventory/index.php>INVENTORY</a></td></tr>';

        echo '<tr><td align=center><a href=../mailinglist/index.php>MAILING LIST</a></td></tr>';

        echo '<tr><td align=center><a href=showtankutilization.php>SHOW TANK UTILIZATION</a></td></tr>';

        echo '</table>';

}

echo '</table>';

?>

            <script language="JavaScript">

            <!--

            tigra_tables('one', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

            tigra_tables('two', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

<?php

if (isstaff()=="YES")echo 'tigra_tables(\'staff\', 1, 0, \'#ffffff\', \'PapayaWhip\', \'LightSkyBlue\', \'#cccccc\');';

?>



            tigra_tables('staff', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

            tigra_tables('harvest', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

            tigra_tables('schedules', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

            tigra_tables('ferm', 1, 0, '#ffffff', 'PapayaWhip', 'LightSkyBlue', '#cccccc');

            // -->

            </script>

</body>



</html>

