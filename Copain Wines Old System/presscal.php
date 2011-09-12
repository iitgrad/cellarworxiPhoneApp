<?php

include("startdb.php");

include("queryupdatefunctions.php");

include("assetfunctions.php");

include("lotinforecords.php");

session_start();

  ?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

     <script type="text/javascript" src="popup/overlibmws.js"></script>

   <script type="text/javascript" src="popup/overlibmws_draggable.js"></script>







</head>



<body>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div> 



<?php  



function printvessels($vessels)

{

	for ($i=0;$i<count($vessels);$i++)

	{

		$thevessels.='<br>'.$vessels[$i];

	}

	return $thevessels;

}



function display_cell($woid)

{

	$thewo=getwo($woid);

	$vessels=filter(lotinvessels($thewo['lot']));

	if ((isstaff()=="YES")|(strtoupper($thewo['ccode'])==strtoupper($_SESSION['clientcode'])))

	{

		$entry='<table align=center width=100%>';

//		$entry.='<tr><td colspan=3 align=center><a href=presscal.php?action=swapboth&woid='.$woid.'><img border=0 width=12 src=../../images/arrowup.GIF ></a></td></tr>';

		$entry.='<tr><td align=center><a href=presscal.php?action=moveleft&woid='.$woid.'><img border=0 width=12 src=../../images/arrowleft.GIF ></a></td><td align=center><a href=presspage.php?action=view&woid='.$woid.'><b>'.$thewo['ccode'].'</b>'.printvessels($vessels).'</a></td><td align=center><a href=presscal.php?action=moveright&woid='.$woid.'><img border=0 width=12 src=../../images/arrowright.GIF ></a></td></tr>';

//		$entry.='<tr><td align=center><a href=presscal.php?action=moveleft&woid='.$woid.'><img border=0 width=12 src=../../images/arrowleft.GIF ></a></td><td align=center><a href=presspage.php?action=view&woid='.$woid.'><b>'.$thewo['ccode'].'</b>'.'</a></td><td align=center><a href=presscal.php?action=moveright&woid='.$woid.'><img border=0 width=12 src=../../images/arrowright.GIF ></a></td></tr>';

//		$entry.='<tr><td colspan=3 align=center><a href=presscal.php?action=swappress&woid='.$woid.'><img border=0 width=12 src=../../images/arrowdown.GIF ></a></td></tr>';

		$entry.='</table>';

		//               $entry='<a href=presscal.php?action=swapboth&woid='.$thiscal['MORNING'][2][$i]['woid'].'><img border=0 width=12 src=../../images/arrowup.GIF ></a>';

		//				$entry.='<br><a href=presscal.php?action=moveleft&woid='.

		//				$thiscal['MORNING'][2][$i]['woid'].'><img border=0 width=12 src=../../images/arrowleft.GIF ></a><a href=presspage.php?action=view&woid='.$thiscal['MORNING'][2][$i]['woid'].'>'.$thiscal['MORNING'][2][$i]['ccode'].printvessels($vessels).'</a><a href=presscal.php?action=moveright&woid='.$thiscal['MORNING'][2][$i]['woid'].'><img border=0 width=12 src=../../images/arrowright.GIF ></a>';

		//				$entry.='<br><a href=presscal.php?action=swappress&woid='.$thiscal['MORNING'][2][$i]['woid'].'><img border=0 width=12 src=../../images/arrowdown.GIF ></a><br>';

	}

	else

	$entry='<b>'.$thewo['ccode'].'</b>'.printvessels($vessels);

	if ($i>0)

	$val.='<br>'.$entry;

	else

	$val.=$entry;

//	echo $val;

	return $val;

}



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

     	//     echo $query;

     	delete_assets_tied_to_woid($_GET['woid']);

     	$result=mysql_query($query);

     	

     }

     

     if ($_GET['action']=="swapboth")

     {

     	$query='select * from reservation where (reservation.WOID='.$_GET['woid'].')';

     	$result=mysql_query($query);

     	$row=mysql_fetch_array($result);

     	if ($row['ASSETID']==2)

     	$query='update reservation set ASSETID="20" WHERE (reservation.WOID='.$_GET['woid'].')';

     	else

     	$query='update reservation set ASSETID="2" WHERE (reservation.WOID='.$_GET['woid'].')';

     	mysql_query($query);

     	

     	$query='select STARTSLOT from wo WHERE  ID="'.$_GET['woid'].'"';

     	$result=mysql_query($query);

     	$row=mysql_fetch_array($result);

     	if ($row['STARTSLOT']=="MORNING")

     	$query='update wo set STARTSLOT="EVENING" WHERE ID="'.$_GET['woid'].'"';

     	else

     	$query='update wo set STARTSLOT="MORNING" WHERE ID="'.$_GET['woid'].'"';

     	mysql_query($query);

     }

     

     if ($_GET['action']=="swappress")

     {

     	$query='select * from reservation where (reservation.WOID='.$_GET['woid'].')';

     	$result=mysql_query($query);

     	$row=mysql_fetch_array($result);

     	if ($row['ASSETID']==2)

     	$query='update reservation set ASSETID="20" WHERE (reservation.WOID='.$_GET['woid'].')';

     	else

     	$query='update reservation set ASSETID="2" WHERE (reservation.WOID='.$_GET['woid'].')';

     	mysql_query($query);

     }

     

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

     $firstofmonth=mktime(0,0,0,date("m",time()),1,date("Y",time()));

     $query='SELECT unix_timestamp(wo.DUEDATE) as STARTDATE, unix_timestamp(wo.ENDDATE) as THEENDDATE, wo.MORNING, wo.NOON, wo.EVENING, wo.ID, reservation.ASSETID,

              wo.TYPE, wo.CLIENTCODE, wo.STARTSLOT, wo.LOT from wo left outer join reservation on (wo.ID=reservation.WOID)

           WHERE (wo.DUEDATE>="'.date("Y-m-d",$firstofmonth).'" and wo.TYPE = "PRESSOFF") ORDER BY wo.DUEDATE';

     

     $assetdesc[2]="S:";

     $assetdesc[20]="L:";

     

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

     		$entry=strtoupper($row['CLIENTCODE']);

     		$temp['ccode']=$entry;

     		$temp['woid']=$row['ID'];

     		$cal[$yearnum][$monthnum][$daynum][$row['STARTSLOT']][$row['ASSETID']][]=$temp;

     	}

     	

     }

     //     echo '<pre>';

     //    print_r($cal);

     

     for ($dt=$firstofmonth; $dt<=$firstofmonth+(90*86400); $dt=$dt+86400)

     {

     	$yearnum=date("Y",$dt);

     	$monthnum=date("m",$dt);

     	$daynum=date("d",$dt);

     	$thiscal=$cal[$yearnum][$monthnum][$daynum];

     	$val='<table border=1 align=center width=100%>';

     	$val.='<tr><td align=center colspan=2><b>AM</b></td></tr>';

     	$val.='<tr><td>S:</td><td align=center>';

     	if (count($thiscal['MORNING'][2])>0)

     	{

     		for ($i=0;$i<count($thiscal['MORNING'][2]);$i++)

     		{

     			$val.=display_cell($thiscal['MORNING'][2][$i]['woid']);

     		}

     		

     	}

     	else

     	{

     		$val.='<a href=presspage.php?action=newpressoff&assetid=2&startslot=MORNING&dateofwork='.date("m/d/Y",mktime(0,0,0,$monthnum,$daynum,$yearnum)).'><img border=0 src=../../images/blue.gif width=5></a>';

     	}

     	$val.='</td></tr>';

     	

     	$val.='<tr><td>L:</td><td align=center>';

     	if (count($thiscal['MORNING'][20])>0)

     	{

     		for ($i=0;$i<count($thiscal['MORNING'][20]);$i++)

     		{

     			$val.=display_cell($thiscal['MORNING'][20][$i]['woid']);

     		}

     	}

     	else

     	{

     		$val.='<a href=presspage.php?action=newpressoff&assetid=20&startslot=MORNING&dateofwork='.date("m/d/Y",mktime(0,0,0,$monthnum,$daynum,$yearnum)).'><img border=0 src=../../images/blue.gif width=5></a>';

     	}

     	$val.='</td></tr>';



     	$val.='<tr><td align=center colspan=2><b>NOON</b></td></tr>';

     	$val.='<tr><td>S:</td><td align=center>';

     	if (count($thiscal['NOON'][2])>0)

     	{

     		for ($i=0;$i<count($thiscal['NOON'][2]);$i++)

     		{

     			$val.=display_cell($thiscal['NOON'][2][$i]['woid']);

     		}

     		

     	}

     	else

     	{

     		$val.='<a href=presspage.php?action=newpressoff&assetid=2&startslot=NOON&dateofwork='.date("m/d/Y",mktime(0,0,0,$monthnum,$daynum,$yearnum)).'><img border=0 src=../../images/blue.gif width=5></a>';

     	}

     	$val.='</td></tr>';

     	

     	$val.='<tr><td>L:</td><td align=center>';

     	if (count($thiscal['NOON'][20])>0)

     	{

     		for ($i=0;$i<count($thiscal['NOON'][20]);$i++)

     		{

     			$val.=display_cell($thiscal['NOON'][20][$i]['woid']);

     		}

     	}

     	else

     	{

     		$val.='<a href=presspage.php?action=newpressoff&assetid=20&startslot=NOON&dateofwork='.date("m/d/Y",mktime(0,0,0,$monthnum,$daynum,$yearnum)).'><img border=0 src=../../images/blue.gif width=5></a>';

     	}

     	$val.='</td></tr>';

     	

     	$val.='<tr><td align=center colspan=2><b>PM</b></td></tr>';

     	$val.='<tr><td>S:</td><td align=center>';

     	if (count($thiscal['EVENING'][2])>0)

     	{

     		for ($i=0;$i<count($thiscal['EVENING'][2]);$i++)

     		{

     			$val.=display_cell($thiscal['EVENING'][2][$i]['woid']);

  /*   			if ((isstaff()=="YES")|(strtoupper($thiscal['EVENING'][2][$i]['ccode'])==strtoupper($_SESSION['clientcode'])))

     			{

     				$entry='<a href=presscal.php?action=swapboth&woid='.$thiscal['EVENING'][2][$i]['woid'].'><img border=0 width=12 src=../../images/arrowup.GIF ></a>';

     				$entry.='<br><a href=presscal.php?action=moveleft&woid='.$thiscal['EVENING'][2][$i]['woid'].'><img border=0 width=12 src=../../images/arrowleft.GIF ></a><a href=presspage.php?action=view&woid='.$thiscal['EVENING'][2][$i]['woid'].'>'.$thiscal['EVENING'][2][$i]['ccode'].'</a><a href=presscal.php?action=moveright&woid='.$thiscal['EVENING'][2][$i]['woid'].'><img border=0 width=12 src=../../images/arrowright.GIF ></a>';

     				$entry.='<br><a href=presscal.php?action=swappress&woid='.$thiscal['EVENING'][2][$i]['woid'].'><img border=0 width=12 src=../../images/arrowdown.GIF ></a><br>';

     				

     			}

     			else

     			$entry=$thiscal['EVENING'][2][$i]['ccode'];

     			if ($i>0)

     			$val.='<br>'.$entry;

     			else

     			$val.=$entry;

     			*/

     		}

     	}

     	else

     	{

     		$val.='<a href=presspage.php?action=newpressoff&assetid=2&startslot=EVENING&dateofwork='.date("m/d/Y",mktime(0,0,0,$monthnum,$daynum,$yearnum)).'><img border=0 src=../../images/blue.gif width=5></a>';

     	}

     	$val.='</td></tr>';

     	

     	$val.='<tr><td>L:</td><td align=center>';

     	if (count($thiscal['EVENING'][20])>0)

     	{

     		for ($i=0;$i<count($thiscal['EVENING'][20]);$i++)

     		{

     			$val.=display_cell($thiscal['EVENING'][20][$i]['woid']);

 /*    			if ((isstaff()=="YES")|(strtoupper($thiscal['EVENING'][20][$i]['ccode'])==strtoupper($_SESSION['clientcode'])))

     			{

     				$entry='<a href=presscal.php?action=swappress&woid='.$thiscal['EVENING'][20][$i]['woid'].'><img border=0 width=12 src=../../images/arrowup.GIF ></a><br>';

     				$entry.='<a href=presscal.php?action=moveleft&woid='.$thiscal['EVENING'][20][$i]['woid'].'><img border=0 width=12 src=../../images/arrowleft.GIF ></a><a href=presspage.php?action=view&woid='.$thiscal['EVENING'][20][$i]['woid'].'>'.$thiscal['EVENING'][20][$i]['ccode'].'</a><a href=presscal.php?action=moveright&woid='.$thiscal['EVENING'][20][$i]['woid'].'><img border=0 width=12 src=../../images/arrowright.GIF ></a>';

     				$entry.='<br><a href=presscal.php?action=swapboth&woid='.$thiscal['EVENING'][20][$i]['woid'].'><img border=0 width=12 src=../../images/arrowdown.GIF ></a>';

     			}

     			else

     			$entry=$thiscal['EVENING'][20][$i]['ccode'];

     			if ($i>0)

     			$val.='<br>'.$entry;

     			else

     			$val.=$entry;

     			*/

     		}

     	}

     	else

     	{

     		$val.='<a href=presspage.php?action=newpressoff&assetid=20&startslot=EVENING&dateofwork='.date("m/d/Y",mktime(0,0,0,$monthnum,$daynum,$yearnum)).'><img border=0 src=../../images/blue.gif width=5></a>';

     	}

     	$val.='</td></tr>';

     	

     	$val.='</table>';



//        if (date("w",$dt)!=0 & date("w",$dt)!=6)

//        {

		if ((date("w",$dt)==0) & ($dt>strtotime("11/18/06")))

		{

			            $val='<table align=center><tr><td align=center>NO PRESSINGS</td></tr></table>';

     	echo 'dcEvent( '.$monthnum.', '.$daynum.','.$yearnum.', null, "'.$val.'", null, null, null, 1 );';

		}

     	else

     	echo 'dcEvent( '.$monthnum.', '.$daynum.','.$yearnum.', null, "'.$val.'", null, null, null, 1 );';

//    }



     	/*            if ((isstaff()=="YES")|(strtoupper($row['CLIENTCODE'])==strtoupper($_SESSION['clientcode'])))

     	{

     	$list[$yearnum][$monthnum][$daynum][]='<tr><td align=center>'.$assetdesc[$row['ASSETID']].'</td><td align=center><a href=presscal.php?action=moveleft&woid='.$row['ID'].'><img border=0 width=12 src=../../images/arrowleft.GIF ></a><a href=presspage.php?action=view&woid='.$row['ID'].'>'.$entry.'</a><a href=presscal.php?action=moveright&woid='.$row['ID'].'><img border=0 width=12 src=../../images/arrowright.GIF ></a></td></tr>';

     	}

     	else

     	{

     	$list[$yearnum][$monthnum][$daynum][]='<tr><td align=center>'.$assetdesc[$row['ASSETID']].'</td><td align=center>'.$entry.'</td></tr>';

     	}

     	*/

     }

     /*     if (count($list)>0)

     {

     foreach ($list as $y => $value)

     {

     foreach ($value as $m => $value1)

     {

     foreach ($value1 as $d=> $value2)

     {

     $val="<table width=100% border=1 align=center>";

     for ($i=0;$i<count($value2);$i++)

     {

     $val=$val.$list[$y][$m][$d][$i];

     }

     $val=$val.'<tr><td align=center><a href=wopage.php?action=newpressoff&dateofwork='.date("m/d/Y",mktime(0,0,0,$m,$d,$y)).'><img border=0 src=../../images/blue.gif width=5></a></td></tr>';

     $val=$val.'</table>';

     echo 'dcEvent( '.$m.', '.$d.','.$y.', null, "'.$val.'", null, null, null, 1 );';

     

     }

     }

     }

     }

     $firstofmonth=mktime(0,0,0,date("m",time()),1,date("Y",time()));

     for ($dt=($firstofmonth-(3*(31*86400))); $dt<=($firstofmonth+(3*(31*86400))); $dt=$dt+86400)

     {

     $yearnum=date("Y",$dt);

     $monthnum=date("m",$dt);

     $daynum=date("d",$dt);

     

     $val='<table border=1 width=100% align=center><tr><td align=center>S:</td><td align=center><a href=presspage.php?action=newpressoff&assetid=2&dateofwork='.date("m/d/Y",mktime(0,0,0,$monthnum,$daynum,$yearnum)).'><img border=0 src=../../images/blue.gif width=5></a></td></tr><tr><td align=center>L:</td><td align=center><a href=wopage.php?action=newpressoff&assetid=20&dateofwork='.date("m/d/Y",mktime(0,0,0,$monthnum,$daynum,$yearnum)).'><img border=0 src=../../images/blue.gif width=5></a></td></tr></table>';

     echo 'dcEvent( '.$monthnum.', '.$daynum.','.$yearnum.', null, "'.$val.'", null, null, null, 1 );';

     

     }

     */

 

     ?>

     </script>  



    <script language="JavaScript">

    // the argument needs to be the object name

    // example: var x = new Calendar("x");

    var cal = new Calendar("cal");

    

    //cal.initialMonth  = 0;  // 0=January; 1=February...

    //cal.initialYear   = 2002;

    

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

