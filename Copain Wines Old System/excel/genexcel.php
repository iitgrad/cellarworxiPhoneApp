<?php

  //require_once('OLEwriter.php');

  //require_once('BIFFwriter.php');

  require_once('Worksheet.php');

  require_once('Workbook.php');

  require ("../startdb.php") ;



  session_start();

  $thedate=$_SESSION['datefilter'];



  function buildsheet(&$theworkbook, $timeslot, $type, $thedate)

  {

     $worksheet1 =& $theworkbook->add_worksheet($type.'-'.$timeslot);



     $worksheet1->set_landscape();

     $worksheet1->center_horizontally();

     $theformat =& $theworkbook->add_format();

     $theformat->set_size(8);



     $formatbold =& $theworkbook->add_format();

     $formatbold->set_bold(2);



     $formatcenter =& $theworkbook->add_format();

     $formatcenter->set_align("center");



     $formatboldcenter =& $theworkbook->add_format();

     $formatboldcenter->set_bold(2);

     $formatboldcenter->set_align("left");



     $formatleft =& $theworkbook->add_format();

     $formatleft->set_align("left");



     $formatborder =& $theworkbook->add_format();

     $formatborder->set_border(1);



     $worksheet1->set_column(0,0,20,$theformat);

     $worksheet1->set_column(1,2,5,$theformat);

     $worksheet1->set_column(3,3,40,$theformat);

     $worksheet1->set_column(4,7,10,$theformat);



     $headerstring=$timeslot.' '.$type.' for '.$thedate;

     $worksheet1->set_header($headerstring);

     $worksheet1->write(0, 0, "LOT #",$formatcenter);

     $worksheet1->write(0, 1, "TYPE",$formatcenter);

     $worksheet1->write(0, 2, "ID",$formatcenter);

     $worksheet1->write(0, 3, "LOT DESCRIPTION",$formatleft);

     if ($type == "PUMP OVER")

        $worksheet1->write(0, 4, "DURATION",$formatcenter);

     else

        $worksheet1->write(0, 4, "STRENGTH",$formatcenter);

 //    $worksheet1->write(0, 5, "ADDS",$formatcenter);

 //    $worksheet1->write(0, 6, "EXTRAS",$formatcenter);

     $worksheet1->write(0, 5, "BRIX",$formatcenter);

     $worksheet1->write(0, 6, "TEMP",$formatcenter);

     $worksheet1->write(0, 7, "INITIALS",$formatcenter);



     $query='SELECT

         `wo`.`LOT`,

         `wo`.`VESSELTYPE`,

         `wo`.`VESSELID`,

         `wo`.`DURATION`,

         `wo`.`STRENGTH`,

         `wo`.`RELATEDADDITIONSID`,

         `wo`.`ALERT`,

          wo.CLIENTCODE, 

         `wo`.`TYPE`,

     wo.BRIX,

          lots.DESCRIPTION

     FROM

        `wo`

     left outer join lots on (lots.LOTNUMBER=wo.LOT)

     left outer join clients on (wo.CLIENTCODE=clients.CODE)

     WHERE

      (`wo`.`DUEDATE` = "'.$thedate.'") AND

      (`wo`.`DELETED` = "0") AND

      (`wo`.`TYPE` = "'.$type .'") AND

      (`wo`.`TIMESLOT` = "'.$timeslot.'")'.

       ' ORDER BY clients.GROUP, wo.CLIENTCODE, wo.BRIX DESC';

     

     

     

     $result=mysql_query($query);

     $num_rows=mysql_num_rows($result);





     for ($i=0; $i<$num_rows; $i++)

     {

        $row=mysql_fetch_array($result);

        $worksheet1->write(2+$i, 0, '['.$row['CLIENTCODE'].'] '.$row['LOT'].' ('.$row['BRIX'].')',$formatcenter);

        $worksheet1->write(2+$i, 1, $row['VESSELTYPE'],$formatcenter);

        $worksheet1->write(2+$i, 2, $row['VESSELID'],$formatcenter);

        $worksheet1->write(2+$i, 3, $row['DESCRIPTION'],$formatleft);

        if ($type=="PUMP OVER")

            $worksheet1->write(2+$i, 4, $row['DURATION'],$formatcenter);

        else

            $worksheet1->write(2+$i, 4, $row['STRENGTH'],$formatcenter);

//        if ($row['RELATEDADDITIONSID']>0)

//            $worksheet1->write(2+$i,5,"YES",$formatcenter);

//        else

//            $worksheet1->write(2+$i,5,"",$formatcenter);

//        $worksheet1->write(2+$i, 6, $row['ALERT'],$formatcenter);

        $worksheet1->write(2+$i, 5, "",$formatcenter);

        $worksheet1->write(2+$i, 6, "",$formatcenter);

        $worksheet1->write(2+$i, 7, "",$formatcenter);

     }

  }

  function HeaderingExcel($filename) {

      header("Content-type: application/vnd.ms-excel");

      header("Content-Disposition: attachment; filename=$filename" );

      header("Expires: 0");

      header("Cache-Control: must-revalidate, post-check=0,pre-check=0");

      header("Pragma: public");

      }



  HeaderingExcel('daily_PO_PD_sheet.xls');



  $workbook = new Workbook("-");



   buildsheet($workbook, "MORNING","PUMP OVER", $thedate);

   buildsheet($workbook, "MORNING","PUNCH DOWN", $thedate);

   buildsheet($workbook, "NOON","PUMP OVER", $thedate);

   buildsheet($workbook, "NOON","PUNCH DOWN", $thedate);

   buildsheet($workbook, "EVENING","PUMP OVER", $thedate);

   buildsheet($workbook, "EVENING","PUNCH DOWN", $thedate);



   $query='SELECT

  `wo`.`LOT`,

  `wo`.`VESSELTYPE`,

  `wo`.`VESSELID`,

  `wo`.`RELATEDADDITIONSID`,

  `additions`.`SUPERFOODAMT`,

  `additions`.`DAPAMOUNT`,

  `additions`.`HTAAMOUNT`,

  `additions`.`WATERAMOUNT`,

  `additions`.`GOAMOUNT`,

  `additions`.`INNOCULATIONBRAND`,

  `additions`.`INNOCULATIONAMOUNT`

FROM

  `wo`

  INNER JOIN `fpaddmap` ON (`wo`.`RELATEDADDITIONSID` = `fpaddmap`.`FERMPROTID`)

  INNER JOIN `additions` ON (`fpaddmap`.`ADDITIONID` = `additions`.`ID`)

WHERE

  (`wo`.`DUEDATE` = "'.$thedate.'")

ORDER BY

  `wo`.`LOT` DESC';



     $result = mysql_query($query);

     $row_nums= mysql_num_rows($result);



     $worksheet1 =& $workbook->add_worksheet("ADDITIONS");



     $worksheet1->set_landscape();

     $worksheet1->center_horizontally();

     $theformat =& $workbook->add_format();

     $theformat->set_size(10);



     $formatbold =& $workbook->add_format();

     $formatbold->set_bold(2);



     $formatcenter =& $workbook->add_format();

     $formatcenter->set_align("center");



     $formatboldcenter =& $workbook->add_format();

     $formatboldcenter->set_bold(2);

     $formatboldcenter->set_align("left");



     $formatleft =& $workbook->add_format();

     $formatleft->set_align("left");



     $formatborder =& $workbook->add_format();

     $formatborder->set_border(1);

     $headerstring='ADDITIONS for '.$thedate;

     $worksheet1->set_header($headerstring);



     $worksheet1->set_column(0,2,13,$theformat);

     $worksheet1->set_column(3,7,8,$theformat);

     $worksheet1->set_column(8,9,12,$theformat);

     $worksheet1->set_column(10,10,8,$theformat);



     $worksheet1->write(0, 0, "LOT #",$formatcenter);

     $worksheet1->write(0, 1, "VESSEL TYPE",$formatcenter);

     $worksheet1->write(0, 2, "VESSELID",$formatcenter);

     $worksheet1->write(0, 3, "SF",$formatcenter);

     $worksheet1->write(0, 4, "DAP",$formatcenter);

     $worksheet1->write(0, 5, "HTA",$formatcenter);

     $worksheet1->write(0, 6, "GO",$formatcenter);

     $worksheet1->write(0, 7, "H20",$formatcenter);

     $worksheet1->write(0, 8, "INOCBRND",$formatcenter);

     $worksheet1->write(0, 9, "INOCAMNT",$formatcenter);

     $worksheet1->write(0, 10, "INITIALS",$formatcenter);



   for ($i=0;$i<$row_nums;$i++)

   {

        $row=mysql_fetch_array($result);

        $worksheet1->write(2+$i, 0, $row['LOT'],$formatcenter);

        $worksheet1->write(2+$i, 1, $row['VESSELTYPE'],$formatcenter);

        $worksheet1->write(2+$i, 2, $row['VESSELID'],$formatcenter);

        $worksheet1->write(2+$i, 3, $row['SUPERFOODAMT'],$formatcenter);

        $worksheet1->write(2+$i, 4, $row['DAPAMOUNT'],$formatcenter);

        $worksheet1->write(2+$i, 5, $row['HTAAMOUNT'],$formatcenter);

        $worksheet1->write(2+$i, 6, $row['GOAMOUNT'],$formatcenter);

        $worksheet1->write(2+$i, 7, $row['H20AMOUNT'],$formatcenter);

        $worksheet1->write(2+$i, 8, $row['INNOCULATIONBRAND'],$formatcenter);

        $worksheet1->write(2+$i, 9, $row['INNOCULATIONAMOUNT'],$formatcenter);

        $worksheet1->write(2+$i, 10, "",$formatborder);

   }



  $workbook->close();

?>