<?php

session_start();

include("startdb.php");

include("queryupdatefunctions.php");

include("assetfunctions.php");

?>
<html>
<head>
  <title></title>
  <link rel="stylesheet" type="text/css" href="../site.css">
</head>

<body>

<?php  



if (!isset($_SESSION['clientcode']))

$_SESSION['clientcode']=getclientcode();



     ?>

 <!--     Begin the DHTML Calendar      -->

    <script language="JavaScript" type="text/javascript">

    function confirmdelete(url)

    {

        if (confirm('Are you SURE you want to delete this lot!!??'))

        {

            location.href=url;

        }

    }

    

  </script>

    <script language="JavaScript" src="../bigcalendar/dhtmlcal.js"></script>

     <script language="JavaScript" >

     <?php

     if ($_GET['action']=="del")

     {

        $query='DELETE FROM wo WHERE wo.ID="'.$_GET['woid'].'" limit 1';

        //      echo $query;

        delete_assets_tied_to_woid($_GET['woid']);

        $result=mysql_query($query);

        

     }

     

     

     

     //    $query="SELECT unix_timestamp(wo.DUEDATE) as STARTDATE, unix_timestamp(wo.ENDDATE) as THEENDDATE,wo.ID, wo.CLIENTCODE from wo

     //         WHERE (UCASE(wo.CLIENTCODE)=\"".strtoupper($_SESSION['clientcode'])."\") ORDER BY wo.DUEDATE";

     if ($_GET['action']=="moveleft")

     {

        $query='SELECT wo.DUEDATE from wo where wo.ID="'.$_GET['woid'].'"';

        $result=mysql_query($query);

        $row=mysql_fetch_array($result);

        

        $query='UPDATE wo SET wo.ENDDATE="'.date("Y-m-d",strtotime($row['DUEDATE'])-86400).'", wo.DUEDATE="'.date("Y-m-d",strtotime($row['DUEDATE'])-86400).'" where wo.ID="'.$_GET['woid'].'"';

        //        echo $query;

        mysql_query($query);

        

     }

     if ($_GET['action']=="moveright")

     {

        $query='SELECT wo.DUEDATE from wo where wo.ID="'.$_GET['woid'].'"';

        $result=mysql_query($query);

        $row=mysql_fetch_array($result);

        

        $query='UPDATE wo SET wo.ENDDATE="'.date("Y-m-d",strtotime($row['DUEDATE'])+86400).'", wo.DUEDATE="'.date("Y-m-d",strtotime($row['DUEDATE'])+86400).'" where wo.ID="'.$_GET['woid'].'"';

        //        echo $query;

        mysql_query($query);

        

     }

     $query='SELECT unix_timestamp(wo.DUEDATE) as STARTDATE, unix_timestamp(wo.ENDDATE) as THEENDDATE,wo.ID,

              wo.TYPE, wo.CLIENTCODE, wo.LOT from wo

           WHERE (wo.TYPE = "SCP" AND wo.DUEDATE>"2009-08-01") ORDER BY wo.DUEDATE';

 

     $abbr['PINOT NOIR']='PN';

     $abbr['CHARDONNAY']='CH';

     $abbr['GRENACHE GRIS']='GG';

     $abbr['VIOGNIER']='V';

     $abbr['CABERNET SAUVIGNON']='CS';

     $abbr['SYRAH']='S';

     $abbr['ZINFANDEL']='Z';

     $abbr['SAUVIGNON BLANC']='SB';

     $abbr['MARSANNE']='MA';

     $abbr['ROUSSANNE']='RO';

     $abbr['MOURVEDRE']='MV';

     $abbr['PETITE SIRAH']='PS';

     $abbr['GRENACHE']='G';

     $abbr['MERLOT']='ME';

     $abbr['TEMPRANILLO']='TP';

     $abbr['PRIMITIVO']='PT';
     

     

     

     $result=mysql_query($query);

     for ($i=0;$i<mysql_num_rows($result);$i++)

     {

        $row=mysql_fetch_array($result);

        if ($row['THEENDDATE']<=$row['STARTDATE'])

        {

            $enddate=strtotime(date('m/d/Y',$row['STARTDATE']).' 11:59 PM');

        }

        else

        $enddate=$row['THEENDDATE'];

        for ($dt=$row['STARTDATE']; $dt<=$enddate; $dt=$dt+86400)

        {

            $yearnum=date("Y",$dt);

            $monthnum=date("m",$dt);

            $daynum=date("d",$dt);

            

            $vesselquery='SELECT  `assets`.`TYPEID`,  `assets`.`NAME` FROM

  `wo`  INNER JOIN `reservation` ON (`wo`.`ID` = `reservation`.`WOID`)

  INNER JOIN `assets` ON (`reservation`.`ASSETID` = `assets`.`ID`)

WHERE  (`wo`.`ID` = "'.$row['ID'].'")

ORDER BY  `assets`.`NAME`';

            $vesselresults=mysql_query($vesselquery);

            $thevessels='';

            for ($v=0;$v<mysql_num_rows($vesselresults);$v++)

            {

            	$rowvessel=mysql_fetch_array($vesselresults);

            	if ($v>0)

            	   $thevessels.='<br>';

            	$thevessels.=$rowvessel['NAME'];

            }

            $scpquery='SELECT *,locations.NAME as VYD from scp left outer join locations on (locations.ID=scp.VINEYARDID) left outer join varietals on (scp.VARIETAL=varietals.NAME) where scp.WOID="'.$row['ID'].'"';

            $scpresult=mysql_query($scpquery);

            $scprow=mysql_fetch_array($scpresult);

            $total[$yearnum][$monthnum][$daynum]+=$scprow['ESTTONS'];

            //$entry=strtoupper($row['CLIENTCODE']).': '.$abbr[$scprow['VARIETAL']].'-'.substr($scprow['VINEYARD'],0,3).'-'.number_format($scprow['ESTTONS'],1).'T';

            if ($thevessels=='')

            	$entry=strtoupper($row['CLIENTCODE']).': '.$scprow['ABBREVIATION'].'-'.substr($scprow['VYD'],0,3).'-'.number_format($scprow['ESTTONS'],1).'T';

            else

            	$entry=strtoupper($row['CLIENTCODE']).': '.$scprow['ABBREVIATION'].'-'.substr($scprow['VYD'],0,3).'-'.number_format($scprow['ESTTONS'],1).'T'.'<br>['.filter($thevessels).']';

            if ((isstaff()=="YES")|(strtoupper($row['CLIENTCODE'])==strtoupper($_SESSION['clientcode'])))

            {

                $list[$yearnum][$monthnum][$daynum][]='<tr><td align=center><a href=scpcal.php?action=moveleft&woid='.$row['ID'].'><img border=0 width=12 src=../../images/arrowleft.GIF ></a><a href=scppage.php?action=view&woid='.$row['ID'].'>'.$entry.'</a><a href=scpcal.php?action=moveright&woid='.$row['ID'].'><img border=0 width=12 src=../../images/arrowright.GIF ></a></td></tr>';

            }

            else

            {

               if ($dt >= (strtotime(date("m/d/Y",time()).' 11:58 PM')-86400))

                $list[$yearnum][$monthnum][$daynum][]='<tr><td align=center><font color=darkorange>'.$entry.'</font></td></tr>';

            }

        }

        

     }



     if (count($list)>0)

     {

        foreach ($list as $y => $value)

        {

            foreach ($value as $m => $value1)

            {

                foreach ($value1 as $d=> $value2)

                {

                    $val="<table align=center>";

                    for ($i=0;$i<count($value2);$i++)

                    {

                        $val=$val.$list[$y][$m][$d][$i];

                    }

                    $dt=$m.'/'.$d.'/'.$y;

                    if ((isstaff()=="YES") | (strtotime($dt) >= (strtotime(date("m/d/Y",time()).' 11:58 PM')-86400)))

                    {

                    $val=$val.'<tr><td><hr></td></tr><tr><td align=center>TOTAL: '.$total[$y][$m][$d].'T</td></tr>';

                    $val=$val.'<tr><td align=center><a href=scppage.php?action=newscp&dateofwork='.date("m/d/Y",mktime(0,0,0,$m,$d,$y)).'><img border=0 src=../../images/blue.gif width=5></a></td></tr>';

                    }

                    $val=$val.'</table>';

                    echo 'dcEvent( '.$m.', '.$d.','.$y.', null, "'.$val.'", null, null, null, 1 );';



                }

            }

        }

     }

     $firstofmonth=mktime(0,0,0,date("m",time()),1,date("Y",time()));

 //    for ($dt=($firstofmonth-(3*(31*86400))); $dt<=($firstofmonth+(4*(31*86400))); $dt=$dt+86400)

     for ($dt=(mktime(0,0,0,8,15,date("Y",time()))); $dt<=(mktime(0,0,0,11,15,date("Y",time()))); $dt=$dt+86400)

     {

        $yearnum=date("Y",$dt);

        $monthnum=date("m",$dt);

        $daynum=date("d",$dt);

        

        // If not SUNDAY

        if (date("w",$dt)>0) 

 //       if ((date("w",$dt)>0) & !((date("n",$dt)==9)&(date("j",$dt)==5)))

 //       if ((date("w",$dt)>0)&(date("d",$dt)!=1))

        {

            $val='<table align=center><tr><td align=center><a href=scppage.php?action=newscp&dateofwork='.date("m/d/Y",mktime(0,0,0,$monthnum,$daynum,$yearnum)).'><img border=0 src=../../images/blue.gif width=5></a></td></tr></table>';

            echo 'dcEvent( '.$monthnum.', '.$daynum.','.$yearnum.', null, "'.$val.'", null, null, null, 1 );';

        }

        else

        {

        	//no fruit delivery day.....

            $val='<table align=center><tr><td align=center>NO FRUIT DELIVERY</td></tr></table>';

            echo 'dcEvent( '.$monthnum.', '.$daynum.','.$yearnum.', null, "'.$val.'", null, null, null, 1 );';        	

        }

        

     }

     

     

     ?>

     </script>  



    <script language="JavaScript">

    // the argument needs to be the object name

    // example: var x = new Calendar("x");

    var cal = new Calendar("cal");

    

    //cal.initialMonth  = date("m",time());  // 0=January; 1=February...

    //cal.initialYear   = 2004;

    

    cal.slotCount = 1; // number of slots

    

    cal.monthStartDate = new Array(1,1,1, 1,1,1, 1,1,1, 1,1,1);

    cal.longDays = new Array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat","Sun" );

    cal.longMonths   = new Array( "January", "February", "March", "April",

    "May", "June", "July", "August",

    "September", "October", "November", "December" );

    

    cal.dataSelector = 1+2+4+8;

    cal.beginMonday     = true;  // begin week with Sun or Mon

    cal.displayDeadText     = false;  // display prev/ next month events

    cal.displayDeadNumber   = false;  // display prev/ next month days

    cal.displayMonthCombo   = true;   // display month selector

    cal.displayYearCombo    = true;   // display year selector

    cal.dateBreak       = true;   // cause a break after displaying the date

    cal.bottomHeading   = false;  // display weekday names at the bottom of calendar

    cal.todayText       = "-TODAY-";  // text to appear in the current date

    cal.trackSelectedDate   = true;

    

    cal.cellWidth  = 140;        // width of date cells

    cal.cellHeight = 100;        // height of date cells

    cal.borderWidth = 1;         // width of the date cell borders (in pixels)

    

    cal.clrBorder   = "#800000";    // border color of calendar

    cal.clrCellText = "#800000";    // event text color

    cal.clrDead = "#c0c0c0";    // background color- unused this month

    cal.clrFuture   = "#ffffff";    // background color- future dates

    cal.clrHdrBg    = "#c04040";    // header background color (mon, tues...)

    cal.clrHdrText  = "#ffffff";    // header text color

    cal.clrNow  = "#ffffc0";    // background color- the current date

    cal.clrPast = "#e0e0e0";    // background color- previous dates

    cal.clrWeekend  = "#f0f0ff";    // background color- weekend dates

    

    var szFont = "Tahoma, Tahoma, Tahoma, Sans Serif";

    cal.hdrFace = szFont;

    cal.hdrSize = "2";

    cal.numFace = szFont;

    cal.numSize = "3";

    cal.cellFace    = szFont;

    cal.cellSize    = "1";

    // cal.initialize();

    cal.createDateSelect();

    </script>



    <div id="MSIE" name="MSIE">

    <ilayer id="NSALIGN" name="NSALIGN">

    <layer id="NSLAYER" name="NSLAYER">



    <script language="JavaScript">

    cal.createCalendar();

    </script>



    </layer></ilayer></div>

    <!--     End of DHTML Calendar       -->



</body>



</html>
