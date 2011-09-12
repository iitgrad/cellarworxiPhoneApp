<?php

function filter($thestring)

{

	return preg_replace("/'/","",preg_replace("/[\n\t\r]+/","",$thestring));

}



function getlabtests($woid)

{

	$query='SELECT  `labresults`.`LABTEST` FROM

         `labtest`  INNER JOIN `labresults` ON (`labtest`.`ID` = `labresults`.`LABTESTID`) WHERE labtest.WOID="'.$woid.'"';

	$result=mysql_query($query);

	for ($i=0;$i<mysql_num_rows($result);$i++)

	{

		$row=mysql_fetch_array($result);

		$labtest[]=$row['LABTEST'];

	}

	return $labtest;

}



function isstaff()

{

    $query='SELECT * FROM users WHERE (users.username="'.$_SERVER['PHP_AUTH_USER'].'" AND users.staff="YES")';

//    echo $query;

    $result=mysql_query($query);

    if (mysql_num_rows($result)>0)

    return "YES";

    else

    return "NO";

}

function getlist($woid,$assettype="")

{

    $query='SELECT  `assets`.`NAME`, wo.`STARTSLOT` FROM

  `reservation`

  INNER JOIN wo ON (reservation.WOID=wo.ID)

  INNER JOIN `assets` ON (`reservation`.`ASSETID` = `assets`.`ID`)

  INNER JOIN `assettypes` ON (`assets`.`TYPEID` = `assettypes`.`ID`) WHERE reservation.WOID="'.$woid.'"';



  if ($assettype!="")

     $query.=' and assettypes.ID="'.$assettype.'"';

    

    $result=mysql_query($query);

    for ($i=0;$i<mysql_num_rows($result);$i++)

    {

         $row=mysql_fetch_array($result);

        $asset['name']=$row['NAME'];

        $asset['timeslot']=$row['STARTSLOT'];

        $assets[]=$asset;       

    }

    return $assets;

}



function isingroup()

{

    $query='SELECT * FROM users WHERE (users.username="'.$_SERVER['PHP_AUTH_USER'].'" AND users.group!="")';

    $result=mysql_query($query);

    if (mysql_num_rows($result))

    return "YES";

    else



    return "NO";

}



function getclientcode()

{

    $query='SELECT * from users inner join clients on (clients.clientid=users.clientid) WHERE users.username="'.$_SERVER['PHP_AUTH_USER'].'"';

    $result=mysql_query($query);

    $row=mysql_fetch_array($result);

    return $row['CODE'];

}

function lotinfo($lot)

{

    $query='SELECT * FROM lots WHERE lots.LOTNUMBER="'.$lot.'"';

    $result=mysql_query($query);

    $row=mysql_fetch_array($result);

    return $row;

}



function clientcodeoflot($lot)

{

    $query='SELECT `lots`.`LOTNUMBER`, `clients`.`CODE` FROM `lots`

            INNER JOIN `clients` ON (`lots`.`CLIENTCODE` = `clients`.`clientid`) WHERE lots.LOTNUMBER="'.$lot.'"';

    $result=mysql_query($query);

    $row=mysql_fetch_array($result);

    return strtolower($row['CODE']);

}



function DrawComboFromArray($thearray,$value,$name)

{

    if (count($thearray))

    {

        foreach ($thearray as $key=>$value2)

        {

            if ($key==$value)

            $items.="<option selected value=\"".$key."\">".$key."</option>\n";

            else

            $items.="<option value=\"".$key."\">".$key."</option>\n";

        }

    }

    $text='<select style="z-index: 5" name="'.$name.'">\n';

    $text.=$items;

    $text.="</select>\n\n";

    return $text;

    

}



function firstday($lot)

{

    $query='select DATETIME from wt WHERE LOT="'.$lot.'" order by DATETIME LIMIT 1';

    $result=mysql_query($query);

    if (mysql_num_rows($result)==1)

    {

    $row=mysql_fetch_array($result);

    return strtotime($row['DATETIME']);

    }

    else

      return strtotime("1/1/1900");



}



function DrawComboFromEnum($table,$field,$value,$name)

{

    //connect to DB;

    //   echo $value;

    $query=mysql_query("SHOW COLUMNS FROM ".$table." LIKE '".$field."'") or die (mysql_error());

    if(mysql_num_rows($query)>0)

    {

        $row=mysql_fetch_row($query);

        $options=explode("','",preg_replace("/(enum|set)\('(.+?)'\)/","\\2",$row[1]));

    }

    sort($options);

//    $options[0]="";

    for ($i=0;$i<=count($options);$i++)

    {

        if ($options[$i]==$value)

        $items.="<option selected value=\"".$options[$i]."\">".ucfirst($options[$i])."</option>\n";

        else

        $items.="<option value=\"".$options[$i]."\">".ucfirst($options[$i])."</option>\n";

    }

    $text='<select style="z-index: 5" name="'.$name.'">\n';

    $text.=$items;

    $text.="</select>\n\n";

    return $text;

}



function DrawComboForLots($value,$vintage,$name, $clientcode="")

{

  //   echo $clientcode;

    list($year,$a,$b)=split('-',$value);

    if ($year|="")

    {
	//	echo $year;
    	//  $vintage="20".$year;

    }

    if ($clientcode=="")

    {

    if ($_SESSION['clientcode']=="")

    $_SESSION['clientcode']=getclientcode();

    $cc=$_SESSION['clientcode'];

    }

    else

      $cc=$clientcode;



    $query='SELECT LOTNUMBER from lots WHERE lots.CLIENTCODE="'.clientid($cc).'" AND lots.YEAR="'.$vintage.'"';

//   $query='SELECT LOTNUMBER from lots WHERE lots.CLIENTCODE="'.clientid($cc).'" ORDER BY LOTNUMBER';



 //   echo $value.'//'.$query;

    $result=mysql_query($query);

    $hasvalue=0;



    for ($i=0;$i<mysql_num_rows($result);$i++)

    {

        $row=mysql_fetch_array($result);

        if ($row['LOTNUMBER']==$value)

        {

            $hasvalue=1;

        }

    }

    

    if ($hasvalue==0)

      $items="<option value=\"---\"</option>\n";



    $result=mysql_query($query);
	$items.='<option value=""></option>\n';

    for ($i=0;$i<mysql_num_rows($result);$i++)

    {

        $row=mysql_fetch_array($result);

 //       echo $row['LOTNUMBER'];

        if ($row['LOTNUMBER']==$value)

        $items.="<option selected value=\"".$row['LOTNUMBER']."\">".ucfirst($row['LOTNUMBER'])."</option>\n";

        else

        $items.="<option value=\"".$row['LOTNUMBER']."\">".ucfirst($row['LOTNUMBER'])."</option>\n";

    }

    $text='<select name="'.$name.'">\n';

    $text.=$items;

    $text.="</select>\n\n";

    return $text;

    

}



function DrawComboForTanks($value,$lot,$name)

{

    $query='SELECT  assets.NAME

FROM

  lots

  INNER JOIN wo ON (lots.LOTNUMBER = wo.LOT)

  INNER JOIN reservation ON (wo.ID = reservation.WOID)

  INNER JOIN assets ON (reservation.ASSETID = assets.ID)

WHERE

  (lots.LOTNUMBER = "'.$lot.'") AND

  (assets.TYPEID="6" OR assets.TYPEID="8")';

 //  echo $query;

    $result=mysql_query($query);

    $hasvalue=0;

    for ($i=0;$i<mysql_num_rows($result);$i++)

    {

        $row=mysql_fetch_array($result);

        if ($row['NAME']==$value)

        {

            $hasvalue=1;

        }

    }

    

    if ($hasvalue==0)

      $items="<option value=\"---\"</option>\n";



    $result=mysql_query($query);

    for ($i=0;$i<mysql_num_rows($result);$i++)

    {

        $row=mysql_fetch_array($result);

        if ($row['NAME']==$value)

        $items.="<option selected value=\"".$row['NAME']."\">".ucfirst($row['NAME'])."</option>\n";

        else

        $items.="<option value=\"".$row['NAME']."\">".ucfirst($row['NAME'])."</option>\n";

    }

    $text='<select name="'.$name.'">\n';

    $text.=$items;

    $text.="</select>\n\n";

    return $text;

    

}

function DrawComboFromDataWithValue($table,$field,$value,$name,$index,$limitname="",$limitvalue="")

{

    //connect to DB;

    //   echo $value;

    if ($limitname=="")
	{
		$query='SELECT '.$field.','.$index.' FROM '.$table .' ORDER BY '.$field;
	    $result=mysql_query($query) or die (mysql_error());
	}
    else

    {

        $query='SELECT '.$field.','.$index.' FROM '.$table .' WHERE '.$limitname.'="'.$limitvalue.'" ORDER BY '.$field;

        $result=mysql_query($query) or die (mysql_error());

    }

   // echo $query;

    

    for ($i=0;$i<mysql_num_rows($result);$i++)

    {

        $row=mysql_fetch_assoc($result);

        if ($row[$index]==$value)

        $items.="<option selected value=\"".$row[$index]."\">".ucfirst($row[$field])."</option>\n";

        else

        $items.="<option value=\"".$row[$index]."\">".ucfirst($row[$field])."</option>\n";

    }

    $text='<select name="'.$name.'">\n';

    $text.=$items;

    $text.="</select>\n\n";

    return $text;

}


function DrawComboFromData($table,$field,$value,$name,$limitname="",$limitvalue="")

{

    //connect to DB;

    //   echo $value;

    if ($limitname=="")

    $result=mysql_query('SELECT '.$field.' FROM '.$table .' ORDER BY '.$field) or die (mysql_error());

    else

    {

        $query='SELECT '.$field.' FROM '.$table .' WHERE '.$limitname.'="'.$limitvalue.'" ORDER BY '.$field;

        $result=mysql_query($query) or die (mysql_error());

    }

    //  echo $query;

    

    for ($i=0;$i<mysql_num_rows($result);$i++)

    {

        $row=mysql_fetch_array($result);

        if ($row[$field]==$value)

        $items.="<option selected value=\"".$row[$field]."\">".ucfirst($row[$field])."</option>\n";

        else

        $items.="<option value=\"".$row[$field]."\">".ucfirst($row[$field])."</option>\n";

    }

    $text='<select name="'.$name.'">\n';

    $text.=$items;

    $text.="</select>\n\n";

    return $text;

}

function clientcode($clientid)

{

    $query='SELECT

  `clients`.`CLIENTNAME`,

  `clients`.`CODE`

FROM

  `clients`

WHERE

 (`clients`.`clientid` = "'.$clientid.'")';

    $result=mysql_query($query);

    $row_nums = mysql_num_rows($result);

    $row = mysql_fetch_array($result);

    

    return $row['CODE'];

}

function clientname($clientcode)

{

    $query='SELECT

  `clients`.`CLIENTNAME`,

  `clients`.`clientid`

FROM

  `clients`

WHERE

 (`clients`.`CODE` = "'.$clientcode.'")';

    $result=mysql_query($query);

    $row_nums = mysql_num_rows($result);

    $row = mysql_fetch_array($result);

    

    return $row['CLIENTNAME'];

}

function clientid($clientcode)

{

    $query='SELECT

  `clients`.`CLIENTNAME`,

  `clients`.`clientid`

FROM

  `clients`

WHERE

 (`clients`.`CODE` = "'.$clientcode.'")';

    $result=mysql_query($query);

    $row_nums = mysql_num_rows($result);

    $row = mysql_fetch_array($result);

    

    return $row['clientid'];

}



function clientinfo($username)

{

    $query='SELECT

  LCASE(`clients`.`CODE`) as FIELD1,

  `clients`.`CLIENTNAME`,

  `clients`.`CODE`,

   users.staff,

  `clients`.`clientid`

FROM

  `users`

  INNER JOIN `clients` ON (`users`.`clientid` = `clients`.`clientid`)

WHERE

  (`users`.`username` = "'.$username.'")';

    $result=mysql_query($query);

    $row_nums = mysql_num_rows($result);

    $row = mysql_fetch_array($result);

    //  print_r($row);

    

    return array('clientid'=>$row['clientid'],'code'=>$row['CODE'],'clientname'=>$row['CLIENTNAME'],'clientcode'=>$row['FIELD1'], 'staff'=>$row['staff']);

}



function genalert($lot,$vesseltype,$vesselid)

{

    $query = 'SELECT `brixtemp`.`BRIX`, `brixtemp`.`temp`   FROM

  `brixtemp` WHERE

  (`brixtemp`.`vessel` = '.$vesselid.' AND

   `brixtemp`.`vesseltype` = '.$vesseltype.' AND

   `brixtemp`.`lot` = '.$lot.')';

    

    //  echo $query;

    $result=mysql_query($query);

    $row_nums = mysql_num_rows($result);

    for ($i=0;$i<$row_nums;$i++)

    {

        $row=mysql_fetch_array($result);

        $brix[]=$row['BRIX'];

    }

    $alert = "";

    $last = count($brix)-1;

    if (count($brix)==1) return "DRY ICE";

    if (count($brix)==0) return "DRY ICE";

    if (count($brix)>1)

       if ($brix[count($brix)-1]/$brix[0]>=.9) return "DRY ICE";

    if ($brix[count($brix)-1]< -0.5) return "DRY ICE";

    

    if ($last>0)

    { if (($brix[$last]<=9)&&(($brix[$last-1]-$brix[$last])/$brix[$last]<=0.30))

    return "SLOW";

    }

    

    return '""';

    

}

function getbrixtemp($lot, $vesseltype, $vesselid, $date)

{

   $query='select * FROM brixtemp WHERE   (`brixtemp`.`vessel` = "'.$vesselid.'" AND

   `brixtemp`.`vesseltype` = "'.$vesseltype.'" AND

   `brixtemp`.`lot` = "'.$lot.'") order by id DESC';  

    

    $result=mysql_query($query);

    $row_nums = mysql_num_rows($result);

    $row = mysql_fetch_array($result);

    

    return array('brix'=>$row['BRIX'],'temp'=>$row['temp']);

}

function addpopdwo($lotvessel, $date, $type, $timeslot, $strength, $duration, $alert)

{

    $input=explode(' ',$lotvessel);

    $lot=$input[0];

    $input2=explode('-',$input[1]);

    $vesseltype=$input2[0];

    $vesselid=$input2[1];



    $query='SELECT * FROM wo

         WHERE (wo.LOT="'.$lot.'" AND

         wo.DUEDATE="'.$date.'" AND

         wo.TYPE="'.$type.'" AND

         wo.TIMESLOT="'.$timeslot.'" AND

         wo.ALERT="'.$alert.'" AND

         wo.VESSELTYPE="'.$vesseltype.'" AND

         wo.VESSELID="'.$vesselid.'")';

    

//        echo $query;

    $result=mysql_query($query);

    $row_nums = mysql_num_rows($result);

    //         echo '<br><br>'.$row_nums;

    if ($row_nums > 0)

    {

        $row=mysql_fetch_array($result);

        $query ='UPDATE wo SET wo.DUEDATE="'.$date.'", wo.LOT="'.$lot.'",

           wo.TIMESLOT="'.$timeslot.'",

           wo.TYPE="'.$type.'",

           wo.ENDDATE="'.$date.'",

           wo.VESSELTYPE="'.$vesseltype.'",

           wo.VESSELID="'.$vesselid.'",

           wo.STRENGTH="'.$strength.'",

           wo.DURATION="'.$duration.'",

           wo.ALERT="'.$alert.'",

           wo.STATUS="ASSIGNED",

           wo.AUTOGENERATED="NO",

           wo.DELETED=0

           WHERE (wo.ID='.$row['ID'].')';

//        echo 'RECORD ALREADY IN DB, RECORD MODIFIED';

    }

    else

    {

        $query ='INSERT INTO wo SET wo.DUEDATE="'.$date.'", wo.LOT="'.$lot.'",

           wo.TIMESLOT="'.$timeslot.'",

           wo.TYPE="'.$type.'",

           wo.ENDDATE="'.$date.'",

           wo.VESSELTYPE="'.$vesseltype.'",

           wo.VESSELID="'.$vesselid.'",

           wo.ALERT="'.$therecord['alert'].'",

           wo.STRENGTH="'.$strength.'",

           wo.AUTOGENERATED="NO",

           wo.STATUS="ASSIGNED",

           wo.DELETED=0,

           wo.DURATION="'.$duration.'"';

//        echo 'RECORD ADDED';

    }

//    echo '<br>'.$query;

    $result=mysql_query($query);

}



function brixentered($lot, $vesseltype, $vesselid, $date)

{

    $query = 'SELECT `brixtemp`.`BRIX`, `brixtemp`.`temp`   FROM

  `brixtemp` WHERE

  (`brixtemp`.`vessel` = "'.$vesselid.'" AND

   `brixtemp`.`vesseltype` = "'.$vesseltype.'" AND

   `brixtemp`.`lot` = "'.$lot.'" AND

   `brixtemp`.`DATE` = "'.$date.'")';

    

    $result=mysql_query($query);

    $row_nums = mysql_num_rows($result);

    return ($row_nums > 0);

}

function copydownuppopd($updown,$woid)

{

    $therecord = getwo($woid);

    

    switch ($updown)

    {

        case "cup" :

        switch ($therecord['timeslot'])

        {

            case "MORNING" : $newtimeslot="EVENING"; break;

            case "NOON" : $newtimeslot="MORNING"; break;

            case "EVENING" : $newtimeslot="NOON"; break;

        } break;

        case "cdown" :

        switch ($therecord['timeslot'])

        {

            case "MORNING" : $newtimeslot="NOON"; break;

            case "NOON" : $newtimeslot="EVENING"; break;

            case "EVENING" : $newtimeslot="MORNING"; break;

        } break;

    }

    

    $query ='INSERT INTO wo SET wo.DUEDATE="'.$therecord['duedate'].'", wo.LOT="'.$therecord['lot'].'",

           wo.TIMESLOT="'.$newtimeslot.'",

           wo.ENDDATE="'.$therecord['duedate'].'",

           wo.TYPE="'.$therecord['type'].'",

           wo.VESSELTYPE="'.$therecord['vesseltype'].'",

           wo.VESSELID="'.$therecord['vesselid'].'",

           wo.STRENGTH="'.$therecord['strength'].'",

           wo.ALERT="'.$therecord['alert'].'",

           wo.AUTOGENERATED="NO",

           wo.STATUS="ASSIGNED",

           wo.DELETED=0,

           wo.DURATION="'.$therecord['duration'].'"';

    

    mysql_query($query);

    

}function getbrixtempid($lot, $vesseltype, $vesselid, $date)

{

    $query = 'SELECT `brixtemp`.`id` FROM

  `brixtemp` WHERE

  (`brixtemp`.`vessel` = "'.$vesselid.'" AND

   `brixtemp`.`vesseltype` = "'.$vesseltype.'" AND

   `brixtemp`.`lot` = "'.$lot.'" AND

   `brixtemp`.`DATE` = "'.$date.'")';

    

    $result=mysql_query($query);

    $row = mysql_fetch_array($result);

    return ($row['id']);

}



function wostatus($init)

{

    

    if ($init == "")

    return "ASSIGNED";

    else

    return "COMPLETED";

}



function updatepopdwo($woid, $lot, $vesseltype, $vesselid, $date, $brix, $temp, $strength, $duration, $init, $alert)

{

    $brixtempid = getbrixtempid($lot, $vesseltype, $vesselid, $date);

    

    $query = 'UPDATE wo

             SET wo.DURATION="'.$duration.'",

                 wo.STRENGTH="'.$strength.'",

                 wo.ALERT="'.$alert.'",

                 wo.STATUS="'.wostatus($init).'",

                 wo.COMPLETIONDATE="'.date("Y-m-d").'",

                 wo.COMPLETEBY="'.$init.'"

             WHERE

                 wo.id='.$woid;

    //   echo $query . '<br>';

    mysql_query($query);

    

    if ($brixtempid > 0)

    {

        //already added for this date so will be updated instead.

        $query = 'UPDATE brixtemp

             SET brixtemp.BRIX="'.$brix.'",

                 brixtemp.TEMP="'.$temp.'"

             WHERE

                 brixtemp.id='.$brixtempid;

    }

    else

    $query = 'INSERT INTO brixtemp

             SET brixtemp.BRIX="'.$brix.'",

              brixtemp.temp="'.$temp.'",

              brixtemp.lot="'.$lot.'",

              brixtemp.vessel="'.$vesselid.'",

              brixtemp.vesseltype="'.$vesseltype.'",

              brixtemp.DATE="'.$date.'"';

    

    //   echo "brix temp query ->".$query.'<BR>';

    if ($temp > 45)  mysql_query($query);

    

}



function updatewoinitials($woid,$initials)

{  $query = 'UPDATE wo SET wo.COMPLETEBY="'.$initials.'", wo.STATUS="COMPLETED" WHERE wo.ID="'.$woid.'"';

$result=mysql_query($query);

}



function deletewo($woid)

{  //$query = 'UPDATE wo SET wo.DELETED=1 WHERE wo.ID="'.$woid.'"';

   $query = 'DELETE FROM wo WHERE wo.ID="'.$woid.'"';

   //echo $query;

$result=mysql_query($query);

}



function deleteautogenerated($date, $timeslot)

{ 

     $query = 'DELETE FROM wo WHERE (wo.TIMESLOT="'.$timeslot.'" AND wo.AUTOGENERATED="YES" AND wo.DUEDATE="'.$date.'")';

//   echo $query;

//   exit;

    $result=mysql_query($query);

}



function setwotype($woid,$thetype)

{

    $query = 'UPDATE wo SET wo.TYPE="'.$thetype.'", wo.AUTOGENERATED="NO" WHERE wo.ID="'.$woid.'"';

    $result=mysql_query($query);

}



function setnewtimeslot($woid,$newtimeslot)

{

    

    $query = 'UPDATE wo SET wo.TIMESLOT="'.$newtimeslot.'", wo.AUTOGENERATED="NO" WHERE wo.ID="'.$woid.'"';

    //  echo $query;

    $result=mysql_query($query);

}



function setdefaults()

{

   if ($_SESSION['vintage']=="")

     $_SESSION['vintage']=2009;

   if ($_SESSION['clientcode']=="")

     $_SESSION['clientcode']=getclientcode();

}



function copynextday($woid)

{

    

    $therecord = getwo($woid);

    $newdate = strtotime($therecord['duedate'])+86400;

    $query ='INSERT INTO wo SET wo.DUEDATE="'.date("Y-m-d",$newdate).'", wo.LOT="'.$therecord['lot'].'",

           wo.TYPE="'.$therecord['type'].'",

           wo.ENDDATE="'.date("Y-m-d",$newdate).'",

           wo.VESSELTYPE="'.$therecord['vesseltype'].'",

           wo.VESSELID="'.$therecord['vesselid'].'",

           wo.TIMESLOT="'.$therecord['timeslot'].'",

           wo.STRENGTH="'.$therecord['strength'].'",

           wo.ALERT="'.$therecord['alert'].'",

           wo.AUTOGENERATED="NO",

           wo.STATUS="ASSIGNED",

           wo.DELETED=0,

           wo.DURATION="'.$therecord['duration'].'"';

    //  echo $query;

    mysql_query($query);

}



function getwo($woid)

{  $query = 'SELECT * from wo WHERE ( wo.id = '.$woid.' AND (ISNULL(wo.DELETED) OR wo.DELETED=0))';

$result=mysql_query($query);

$row = mysql_fetch_array($result);



return array ('type'=> $row['TYPE'],

'id'=>$row['ID'],

'duedate'=> $row['DUEDATE'],

'lot'=> $row['LOT'],

'vesseltype'=> $row['VESSELTYPE'],

'type'=> $row['TYPE'],

'vesselid'=> $row['VESSELID'],

'workperformedby'=> $row['WORKPERFORMEDBY'],

'otherdesc'=> $row['OTHERDESC'],

'timeslot'=> $row['TIMESLOT'],

'duration'=> $row['DURATION'],

'strength'=> $row['STRENGTH'],

'ccode'=> $row['CLIENTCODE'],

'clientid'=> clientid($row['CLIENTCODE']),

'status'=> $row['STATUS'],

'fermprotid'=> $row['FERMPROTID'],

'relatedadditionsid'=> $row['RELATEDADDITIONSID'],

'clientcode'=> strtoupper($row['CLIENTCODE']),

'enddate'=> $row['ENDDATE'],

'completeby'=> $row['COMPLETEBY'],

'assignedto'=> $row['ASSIGNEDTO']);

}

function swappopd($woid)

{

    $therecord = getwo($woid);

    

    if ($therecord['type']=="PUNCH DOWN")

    {

        setwotype($woid,"PUMP OVER");

    }

    else

    {

        setwotype($woid,"PUNCH DOWN");

    }

}



function downuppopd($updown,$woid)

{

    $therecord = getwo($woid);

    //    echo $updown . '--'. $therecord['timeslot'];

    

    switch ($updown)

    {

        case "up" :

        switch ($therecord['timeslot'])

        {

            case "MORNING" : $newtimeslot="EVENING"; break;

            case "NOON" : $newtimeslot="MORNING"; break;

            case "EVENING" : $newtimeslot="NOON"; break;

        } break;

        case "down" :

        switch ($therecord['timeslot'])

        {

            case "MORNING" : $newtimeslot="NOON"; break;

            case "NOON" : $newtimeslot="EVENING"; break;

            case "EVENING" : $newtimeslot="MORNING"; break;

        } break;

    }

    

    setnewtimeslot($woid,$newtimeslot);

}



function nb($value)

{

    if ($value !== "")

    return $value;

    else

    return "&nbsp;";

}

?>
