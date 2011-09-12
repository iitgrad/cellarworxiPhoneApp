<?php

include("startdb.php");

include("queryupdatefunctions.php");

session_start();

  ?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">





</head>



<body>

<?php  

   $_SESSION['clientcode']=getclientcode();

     

     ?>

 <!--     Begin the DHTML Calendar      -->

    <script language="JavaScript" src="../bigcalendar/dhtmlcal.js"></script>

     <script language="JavaScript" >

     <?php

 //    $query="SELECT unix_timestamp(wo.DUEDATE) as STARTDATE, unix_timestamp(wo.ENDDATE) as THEENDDATE,wo.ID, wo.CLIENTCODE from wo

 //         WHERE (UCASE(wo.CLIENTCODE)=\"".strtoupper($_SESSION['clientcode'])."\") ORDER BY wo.DUEDATE";

     $query='SELECT unix_timestamp(wo.DUEDATE) as STARTDATE, unix_timestamp(wo.ENDDATE) as THEENDDATE,wo.ID, wo.TYPE, wo.CLIENTCODE, wo.LOT from wo

           WHERE (NOT ((wo.TYPE = "DRYICE") OR (wo.TYPE = "PUMP OVER") OR (wo.TYPE = "PUNCH DOWN"))) and (NOT (wo.STATUS="COMPLETED")) ORDER BY wo.DUEDATE';

 //    $query='SELECT unix_timestamp(wo.DUEDATE) as STARTDATE, unix_timestamp(wo.ENDDATE) as THEENDDATE,wo.ID, wo.TYPE, wo.CLIENTCODE, wo.LOT from wo

 //          WHERE (NOT ((wo.TYPE = "DRYICE") OR (wo.TYPE = "PUMP OVER") OR (wo.TYPE = "PUNCH DOWN"))) ORDER BY wo.DUEDATE';

     

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

     		if ((isstaff()=="YES")|(strtoupper($row['CLIENTCODE'])==strtoupper($_SESSION['clientcode'])))

     		{

     			$list[$yearnum][$monthnum][$daynum][]='<tr><td align=center><a href=wopage.php?action=view&woid='.$row['ID'].'>'.strtoupper($row['CLIENTCODE']).'-'.$row['TYPE'].'</a></td></tr>';

     		}

     		else

     		{

     			$list[$yearnum][$monthnum][$daynum][]='<tr><td align=center>'.$row['ID'].' ('.strtoupper($row['CLIENTCODE']).')</td></tr>';

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

     				$val=$val.'</table>';

     				echo 'dcEvent( '.$m.', '.$d.','.$y.', null, "'.$val.'", null, null, null, 1 );';

     				

     			}

     		}

     	}

     }

     ?>

     </script>	



    <script language="JavaScript">

    // the argument needs to be the object name

    // example: var x = new Calendar("x");

    var cal = new Calendar("cal");

    

    //cal.initialMonth	= 0;  // 0=January; 1=February...

    //cal.initialYear	= 2002;

    

    cal.slotCount = 1; // number of slots

    

    cal.monthStartDate = new Array(1,1,1, 1,1,1, 1,1,1, 1,1,1);

    cal.longDays = new Array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat","Sun" );

    cal.longMonths   = new Array( "January", "February", "March", "April",

    "May", "June", "July", "August",

    "September", "October", "November", "December" );

    

    cal.dataSelector = 1+2+4+8;

    cal.beginMonday		= true;  // begin week with Sun or Mon

    cal.displayDeadText 	= false;  // display prev/ next month events

    cal.displayDeadNumber 	= false;  // display prev/ next month days

    cal.displayMonthCombo 	= true;   // display month selector

    cal.displayYearCombo 	= true;   // display year selector

    cal.dateBreak 		= true;   // cause a break after displaying the date

    cal.bottomHeading 	= false;  // display weekday names at the bottom of calendar

    cal.todayText 		= "-TODAY-";  // text to appear in the current date

    cal.trackSelectedDate	= false;

    

    cal.cellWidth  = 90;		 // width of date cells

    cal.cellHeight = 90;  		 // height of date cells

    cal.borderWidth = 1;		 // width of the date cell borders (in pixels)

    

    cal.clrBorder	= "#800000";	// border color of calendar

    cal.clrCellText	= "#800000";	// event text color

    cal.clrDead	= "#c0c0c0";	// background color- unused this month

    cal.clrFuture	= "#ffffff";	// background color- future dates

    cal.clrHdrBg	= "#c04040";	// header background color (mon, tues...)

    cal.clrHdrText	= "#ffffff";	// header text color

    cal.clrNow	= "#ffffc0";	// background color- the current date

    cal.clrPast	= "#e0e0e0";	// background color- previous dates

    cal.clrWeekend	= "#f0f0ff";	// background color- weekend dates

    

    var szFont = "Tahoma, Tahoma, Tahoma, Sans Serif";

    cal.hdrFace	= szFont;

    cal.hdrSize	= "2";

    cal.numFace	= szFont;

    cal.numSize	= "3";

    cal.cellFace	= szFont;

    cal.cellSize	= "1";

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

