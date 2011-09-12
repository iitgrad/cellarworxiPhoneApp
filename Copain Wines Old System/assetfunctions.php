<?php

   function pic($value, $showdropdown, $thelist, $setpostaction, $postname, $clearaction,$label="")

   {

     $clientinfo= clientinfo($_SERVER['REMOTE_USER']);

     //echo $clientinfo['code'];

     if (isstaff()=="YES")

       $staff=TRUE;

     else

       $staff=FALSE;

     if (($value=="")&($staff!=TRUE))

         $value=strtoupper($_SESSION['clientcode']);

     if (($value=="") | ($showdropdown=="TRUE"))

     {

       echo $label;

       echo '<select size="1" name="'.$postname.'" onchange="navigate(this)">';

       $al=array_merge(array(""),$thelist);

       for ($j=0;$j<count($al);$j++)

       {

         if ($al[$j]==$value)

            echo '<option selected value="'.$setpostaction.'&'.$postname.'='.$al[$j].'">'.$al[$j].'</option>';

         else

            echo '<option value="'.$setpostaction.'&'.$postname.'='.$al[$j].'">'.$al[$j].'</option>';

       }

       echo '</select>';

     }

     else

       if ($staff==TRUE)

         echo $label.'<a href='.$clearaction.'>'.strtoupper($value).'</a><br>';

       else

         echo $label.strtoupper($value).'<br>';

   }



/*   function pic($value, $showdropdown, $thelist, $setpostaction, $postname, $clearaction,$label="")

   {

     $clientinfo= clientinfo($_SERVER['REMOTE_USER']);

     //echo $clientinfo['code'];

     if (strtoupper($clientinfo['code'])=="COP")

       $staff=TRUE;

     else

       $staff=FALSE;

     if (($value=="")&($staff!=TRUE))

         $value=strtoupper($_SESSION['clientcode']);

     if (($value=="") | ($showdropdown=="TRUE"))

     {

       echo '<form method="POST" action='.$setpostaction.'>';

       echo $label;

       echo '<select size="1" name="'.$postname.'">';

       $al=array_merge(array(""),$thelist);

       for ($j=0;$j<count($al);$j++)

       {

         if ($al[$j]==$value)

            echo '<option selected>'.$al[$j].'</option>';

         else

            echo '<option>'.$al[$j].'</option>';

       }

       echo '</select>';

       echo '   <input type="submit" value="SET">';

       echo '</form>';

     }

     else

       if ($staff==TRUE)

         echo $label.'<a href='.$clearaction.'>'.strtoupper($value).'</a><br>';

       else

         echo $label.strtoupper($value).'<br>';

   }

*/

   function checknull($woid)

   {

     if ($woid=="")

       return 'ISNULL(`reservation`.`WOID`)';

     else

       return '(`reservation`.`WOID` = "'.$woid.'")';

   }



   function assign_wo_to_reservations($date,$clientcode,$woid)

   {

      $custid=clientid($clientcode);

      $query='UPDATE reservation SET

         reservation.WOID="'.$woid.'" WHERE

         (reservation.DATEALLOCATED="'.$date.'") AND

         (reservation.CUSTID="'.$custid.'")';

     // echo $query;

     mysql_query($query);

   }



   function listassettypes()

   {

       $query='SELECT * FROM assettypes';

       $result=mysql_query($query);

       $num_rows=mysql_num_rows($result);

       for ($i=0;$i<$num_rows;$i++)

       {

          $theassettype[$i]=mysql_fetch_array($result);

       }

       return $theassettype;

   }



//   function listassetsoftypename($assettypename)

//   {

//       $query='SELECT   `assets`.`NAME` FROM  `assettypes`

//     INNER JOIN `assets` ON (`assettypes`.`ID` = `assets`.`TYPEID`)

//     WHERE  (`assettypes`.`NAME` = "'.$assettypename.'")ORDER BY  `assets`.`NAME`';

//       $result=mysql_query($query);

//       $num_rows=mysql_num_rows($result);

//       for ($i=0;$i<$num_rows;$i++)

//       {

//          $theassets[$i]=mysql_fetch_array($result);

//       }

//       return $theassets;

//   }



   function listassetsnames($assettypename)

   {

       $query='SELECT   `assets`.`NAME` FROM  `assettypes`

     INNER JOIN `assets` ON (`assettypes`.`ID` = `assets`.`TYPEID`)

     WHERE  (`assettypes`.`NAME` = "'.$assettypename.'")ORDER BY  `assets`.`NAME`';

       $result=mysql_query($query);

       $num_rows=mysql_num_rows($result);

       for ($i=0;$i<$num_rows;$i++)

       {

          $row=mysql_fetch_array($result);

          $theassets[]=$row['NAME'];

       }

       return $theassets;

           }



   function listassetsoftype($assettypeid)

   {

       $query='SELECT * FROM assets WHERE (assets.TYPEID="'.$assettypeid.'")';

       $result=mysql_query($query);

       $num_rows=mysql_num_rows($result);

       for ($i=0;$i<$num_rows;$i++)

       {

          $theassets[$i]=mysql_fetch_array($result);

       }

       return $theassets;

   }



   function listallassets()

   {

       $query='SELECT * FROM assets;';

       $result=mysql_query($query);

       $num_rows=mysql_num_rows($result);

       for ($i=0;$i<$num_rows;$i++)

       {

          $theassets[$i]=mysql_fetch_array($result);

       }

       return $theassets;

   }



   function listallclientcodes ()

   {

       $query='SELECT * FROM clients ORDER BY CODE;';

       $result=mysql_query($query);

       $num_rows=mysql_num_rows($result);

       for ($i=0;$i<$num_rows;$i++)

       {

          $row=mysql_fetch_array($result);

          $theclients[]=$row['CODE'];

       }

       return $theclients;

   }



   function listallocassets($assettype,$date,$custid,$woid="")

   {

   //List all assets allocated to customer on date of certain type

   $query='SELECT `assets`.`NAME`, `reservation`.`TIMESLOT`

      FROM `reservation`

         INNER JOIN `assets` ON (`reservation`.`ASSETID` = `assets`.`ID`)

         INNER JOIN `assettypes` ON (`assets`.`TYPEID` = `assettypes`.`ID`)

      WHERE

        (`assettypes`.`NAME` = "'.$assettype.'") AND

        (`reservation`.`DATEALLOCATED` = "'.$date.'") AND

        '.checknull($woid).' AND

        (`reservation`.`CUSTID` = "'.$custid.'")';



    //  echo $query;

      $result=mysql_query($query);

      $num_rows=mysql_num_rows($result);

      for ($i=0;$i<$num_rows;$i++)

      {

          $row=mysql_fetch_array($result);

          $theasset[$i]['timeslot']=$row['TIMESLOT'];

          $theasset[$i]['name']=$row['NAME'];

      }

      return $theasset;

 }



 function reserveasset($assetid,$date,$timeslot,$custid,$lot,$status)

 {

   if ($_SESSION['woid']=="")

   $query='INSERT INTO reservation SET

       reservation.ASSETID="'.$assetid.'",

       reservation.DATEALLOCATED="'.$date.'",

       reservation.TIMESLOT="'.$timeslot.'",

       reservation.CUSTID="'.$custid.'",

       reservation.FORLOT="'.$lot.'",

       reservation.STATUS="'.$status.'"';

    else

    $query='INSERT INTO reservation SET

       reservation.ASSETID="'.$assetid.'",

       reservation.DATEALLOCATED="'.$date.'",

       reservation.TIMESLOT="'.$timeslot.'",

       reservation.CUSTID="'.$custid.'",

       reservation.FORLOT="'.$lot.'",

       reservation.WOID="'.$_SESSION['woid'].'",

       reservation.STATUS="'.$status.'"';

    //echo $query;

     mysql_query($query);

     $HTTP_SESSION_VARS['allocassets'][]=mysql_insert_id();

 }

 function delete_assets_tied_to_woid($woid)

 {

  $query='DELETE FROM reservation WHERE

       (reservation.WOID="'.$woid.'")';

      // echo $query;

    mysql_query($query);

 }

 function deleteassetreservation($resid)

 {

   $query='DELETE FROM reservation WHERE

       (reservation.ID="'.$resid.'")';

    // echo $query;

    mysql_query($query);

 }

  function getassetlist($assettype)

  // Returns all assets of a particular type, via ID and Name

  {

     $query='SELECT

       `assets`.`NAME`,

       `assets`.`ID`

     FROM

       `assets`

     INNER JOIN `assettypes` ON (`assets`.`TYPEID` = `assettypes`.`ID`)

     WHERE

       (`assettypes`.`NAME` = "'.$assettype.'")';

     $result=mysql_query($query);

     $num_rows = mysql_num_rows($result);



     for ($i=0;$i<$num_rows;$i++)

     {

        $row=mysql_fetch_array($result);

        $wa[$i]['id']=$row['ID'];

        $wa[$i]['name']=$row['NAME'];

     }

     return $wa;

  }



  function getassetinfo($id)

  {

     $query='SELECT *

     FROM

       `assets`

     WHERE

       (`assets`.`ID` = "'.$id.'")';

     $result=mysql_query($query) ;

     $num_rows=mysql_num_rows($result);

     if ($num_rows>0)

        {

           $row=mysql_fetch_array($result);

           return $row;

        }

     return "";

  }



  function getassetname($id)

  {

     $query='SELECT

       `assets`.`NAME`,

       `assets`.`ID`,

       assettypes.NAME AS ASSETTYPENAME

     FROM

       `assets`

     INNER JOIN `assettypes` ON (`assets`.`TYPEID` = `assettypes`.`ID`)

     WHERE

       (`assets`.`ID` = "'.$id.'")';

 //      echo $query;

     $result=mysql_query($query) ;

     $num_rows=mysql_num_rows($result);

     if ($num_rows>0)

        {

           $row=mysql_fetch_array($result);

           return $row['NAME'];

        }

     return "";

  }



  function getassetnamesbyid($assettype)

  {

     $query='SELECT

       `assets`.`NAME`,

       `assets`.`ID`

     FROM

       `assets`

     INNER JOIN `assettypes` ON (`assets`.`TYPEID` = `assettypes`.`ID`)

     WHERE

       (`assettypes`.`NAME` = "'.$assettype.'")';



     $result=mysql_query($query);

     $num_rows = mysql_num_rows($result);

     for ($i=0;$i<$num_rows;$i++)

     {

        $row=mysql_fetch_array($result);

        $wa[$row['ID']]=$row['NAME'];

     }

     return $wa;

  }



  function isreserved($id, $date, $timeslot)

  {

    $query='SELECT

  `reservation`.`ID`,

  `reservation`.`ASSETID`,

  `reservation`.`DATEALLOCATED`,

  `reservation`.`TIMESLOT`,

  `reservation`.`CUSTID`,

  `reservation`.`FORLOT`,

  `reservation`.`STATUS`,

  `clients`.`CODE`,

  `assets`.`NAME`,

  `assets`.`DESCRIPTION`,

  `assets`.`CAPACITY`,

  `assets`.`OWNER`,

  `assets`.`LOCATION`, `assettypes`.`NAME` AS `NAME1`

FROM

  `reservation`

  LEFT OUTER JOIN `clients` ON (`reservation`.`CUSTID` = `clients`.`clientid`)

  LEFT OUTER JOIN `assets` ON (`reservation`.`ASSETID` = `assets`.`ID`)

  LEFT OUTER JOIN `assettypes` ON (`assets`.`TYPEID` = `assettypes`.`ID`)

           WHERE

             (`reservation`.`ASSETID` = "'.$id.'") AND

             (`reservation`.`DATEALLOCATED` = "'.$date.'") AND

             (`reservation`.`TIMESLOT` = "'.$timeslot.'");';



    mysql_query($query);

    $result=mysql_query($query);

    $num_rows = mysql_num_rows($result);

    if ($num_rows>0)

    {

      $row=mysql_fetch_array($result);

      $res['reserved']=TRUE;

      $res['name']=$row['NAME'];

      $res['clientcode']=$row['CODE'];

      $res['lot']=$row['FORLOT'];

      $res['id']=$row['ID'];

    }

    return $res;

  }



  function showstatus($id,$date,$timeslot,$assettype, $currentclientcode, $morning, $noon, $evening)

  {

      $assetinfo = getassetinfo($id);

      $res=isreserved($id,$date,$timeslot);

      $times='&morning='.$morning.'&noon='.$noon.'&evening='.$evening;

      if ($res['reserved']==TRUE)

      {

              $ccode=clientinfo($_SERVER['REMOTE_USER']);

              $ccode=strtoupper($ccode['clientcode']);

         if (strtoupper($currentclientcode)==strtoupper($res['clientcode'])|($ccode=="COP"))

         {

           echo '<a href='.$PHP_SELF.'?action=delete'.$times.'&assettype='.$assettype.'&resid='.$res['id'].'>'.$res['name'].'<br>['.$res['clientcode'].'] '.$res['lot'].'</a>  <a href=showassetschedule.php?caldate='.strtotime($date).'&assetid='.$id.'>(ASSET SCHEDULE)</a>';

         }

         else

         {

           echo $res['name'].'<br>['.$res['clientcode'].'] '.$res['lot'].'<a href=showassetschedule.php?caldate='.strtotime($date).$times.'&assetid='.$id.'>(ASSET SCHEDULE)</a>';

         }

      }

      else

      {

      if ($assetinfo['TYPEID']==6)

         echo '<a href='.$PHP_SELF.'?action=reserve&assettype='.$assettype.'&assetid='.$id.$times.'&timeslot='.$timeslot.'>'.$assetinfo['NAME'].' '.$assetinfo['DESCRIPTION']. ' '.$assetinfo['CAPACITY']. ' GL</a>';

      else

         echo '<a href='.$PHP_SELF.'?action=reserve&assettype='.$assettype.'&assetid='.$id.$times.'&timeslot='.$timeslot.'>'.$assetinfo['NAME'].' '.$assetinfo['DESCRIPTION']. '</a>';

      }

  }

?>