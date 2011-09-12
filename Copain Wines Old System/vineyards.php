<?php
session_start();
?>
	<html>

	<head>
	  <title></title>
	  <link rel="stylesheet" type="text/css" href="../site.css">

<?php
include ("startdb.php");
//include ("queryupdatefunctions.php");
include ("assetfunctions.php");
include ("totalgallons.php");

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


function showcheckedyesno($value)
{
	if ($value=="YES")
		return "checked";
	else
	    return "";
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

    $selected=0;

    for ($i=0;$i<mysql_num_rows($result);$i++)
    {
        $row=mysql_fetch_array($result);
        if (strtoupper($row[$field])==strtoupper($value))
		{
			$selected=1;
	        $items.="<option selected value=\"".$row[$field]."\">".ucfirst($row[$field])."</option>\n";	
		}
        else
	        $items.="<option value=\"".$row[$field]."\">".ucfirst($row[$field])."</option>\n";
    }
    if ($selected==0)
        $items.="<option selected value=NONE>NONE</option>\n";	

    $text='<select name="'.$name.'">\n';
    $text.=$items;
    $text.="</select>\n\n";
    return $text;
}
function yesno($value)
{
	if ($value=="YES")
	   return "YES";
	return "NO";
}

if ($_GET['action']=="del")
{
	$query='delete from locations where ID="'.$_GET['id'].'" limit 1';
	mysql_query($query);
	$_GET['action']="show";	
}

if ($_GET['action']=="mod")
{	
	if ($_POST['id']=="new")
	{
		$query='insert into locations set
		    LOCATIONTYPE="VINEYARD",
		    NAME="'.strtoupper($_POST['name']).'",
		    APPELLATION="'.strtoupper($_POST['appellation']).'",
		    REGION="'.$_POST['region'].'",
		    LAT="38.580715179",
		    locations.LONG="-122.867141724",
		    ORGANIC="'.yesno($_POST['organic']).'",
		    BIODYNAMIC="'.yesno($_POST['biodynamic']).'",
		    GATECODE="'.strtoupper($_POST['gatecode']).'",
		    APPLEMOTH="'.strtoupper($_POST['applemoth']).'",
		    CLIENTID="'.$_REQUEST['clientid'].'"';
	//	echo $query;
		
	}
	else
	{
		$query='update locations set
		    NAME="'.strtoupper($_POST['name']).'",
		    APPELLATION="'.strtoupper($_POST['appellation']).'",
		    REGION="'.$_POST['region'].'",
		    ORGANIC="'.yesno($_POST['organic']).'",
		    BIODYNAMIC="'.yesno($_POST['biodynamic']).'",
		    GATECODE="'.strtoupper($_POST['gatecode']).'",
		    APPLEMOTH="'.strtoupper($_POST['applemoth']).'"
			where ID="'.$_POST['id'].'" limit 1';		
	}
	mysql_query($query);
	$_GET['action']="show";	
}

if ($_GET['action']=="show")
{
	echo '<table align=center width=70%>';
	echo '<tr><td align=center colspan=20><b>Vineyards</b></td></tr>';

	$query='select locations.*, count(wt.ID) as WTCOUNT from locations left outer join clients on (locations.CLIENTID = clients.clientid) 
		left outer join wt on (wt.VINEYARDID=locations.ID)
		where 
		LOCATIONTYPE="VINEYARD" AND clients.CODE="'.$_SESSION['clientcode'].'" group by locations.NAME ORDER BY NAME, APPELLATION';
//	echo $query;
	
	$result=mysql_query($query);
	echo '<tr>';
	echo '<td></td>';
	echo '<td>VINEYARD NAME<hr></td>';
	echo '<td>COUNT<hr></td>';
	echo '<td>APPELLATION<hr></td>';
	echo '<td>REGION<hr></td>';
	echo '<td>ORGANIC<hr></td>';
	echo '<td>BIODYNAMIC<hr></td>';
	echo '<td>GATECODE<hr></td>';
	echo '<td>APPLE MOTH CERT<hr></td>';
	echo '</tr>';

	for ($i=0;$i<mysql_num_rows($result);$i++)
	{
		$row=mysql_fetch_assoc($result);
		echo '<tr>';
		echo '<td><a href='.$_PHPSELF.'?action=del&id='.$row['ID'].'>DEL</a></td>';
		echo '<td>'.strtoupper($row['NAME']).'</td>';
		echo '<td align=center>'.$row['WTCOUNT'].'</td>';
		echo '<td>'.strtoupper($row['APPELLATION']).'</td>';
		echo '<td>'.strtoupper($row['REGION']).'</td>';
		echo '<td>'.$row['ORGANIC'].'</td>';
		echo '<td>'.$row['BIODYNAMIC'].'</td>';
		echo '<td>'.strtoupper($row['GATECODE']).'</td>';
		echo '<td>'.strtoupper($row['APPLEMOTH']).'</td>';
		echo '<td><a href='.$_PHPSELF.'?action=edit&id='.$row['ID'].'>EDIT</a></td></tr>';
		echo '</tr>';
	}
	echo '<td colspan=2><a href='.$_PHPSELF.'?action=add&clientid='.$row['CLIENTID'].'>CLICK HERE TO ADD A VINEYARD</a></td></tr>';
	echo '</table>';	
}
if ($_GET['action']=="edit")
{
	echo '<form method="POST" action='.$PHP_SELF.'?action=mod&clientid='.$row['ID'].'>';
	echo '<table border=1 align=center width=40%>';
	echo '<tr><td align=center colspan=20><b>Vineyard Data</b></td></tr>';

	$query='select * from locations where ID="'.$_GET['id'].'"';

	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);

	echo '<input type=hidden name=id value='.$_GET['id'].'>';
	echo '<tr><td align=right>VINEYARD NAME:</td><td align=left><input type=textbox value="'.strtoupper($row['NAME']).'" size=55 name="name"></td></tr>';
	echo '<tr><td align=right>Appellation:</td><td>'.DrawComboFromData("appellations","NAME",$row['APPELLATION'],"appellation").'</td></tr>';
	echo '<tr><td align=right>Region:</td><td>'.DrawComboFromData("zones","NAME",$row['REGION'],"region").'</td></tr>';
	echo '<tr><td align=right>Organic:</td><td align=center><input type=checkbox name=organic '.showcheckedyesno($row['ORGANIC']).' value="YES"></td></tr>';
	echo '<tr><td align=right>Biodynamic:</td><td align=center><input type=checkbox name=biodynamic '.showcheckedyesno($row['BIODYNAMIC']).' value="YES"></td></tr>';
	echo '<tr><td align=right>Gate Code:</td><td align=left><input type=textbox value="'.$row['GATECODE'].'" size=20 name="gatecode"></td></tr>';
	echo '<tr><td align=right>APPLE MOTH CERTIFICATE:</td><td align=left><input type=textbox value="'.$row['APPLEMOTH'].'" size=20 name="applemoth"></td></tr>';
	echo '<tr><td align=center colspan=2><input type=submit value="UPDATE"></td></tr>';
	echo '</table>';
}
if ($_GET['action']=="add")
{
	
	echo '<form method="POST" action='.$PHP_SELF.'?action=mod&clientid='.$_REQUEST['clientid'].'>';
	echo '<table border=1 align=center width=40%>';
	echo '<tr><td align=center colspan=20><b>Vineyard Data</b></td></tr>';

	echo '<input type=hidden name=id value=new>';
	echo '<input type=hidden name=clientid value="'.clientid($_SESSION['clientcode']).'">';

	echo '<tr><td align=right>VINEYARD NAME:</td><td align=left><input type=textbox value="'.strtoupper($row['NAME']).'" size=55 name="name"></td></tr>';
	echo '<tr><td align=right>Appellation:</td><td>'.DrawComboFromData("appellations","NAME",$row['APPELLATION'],"appellation").'</td></tr>';
	echo '<tr><td align=right>Region:</td><td>'.DrawComboFromData("zones","NAME",$row['REGION'],"region").'</td></tr>';
	echo '<tr><td align=right>Organic:</td><td align=center><input type=checkbox name=organic'.showcheckedyesno($row['ORGANIC']).' value="YES"></td></tr>';
	echo '<tr><td align=right>Biodynamic:</td><td align=center><input type=checkbox name=biodynamic'.showcheckedyesno($row['BIODYNAMIC']).' value="YES"></td></tr>';
	echo '<tr><td align=right>Gate Code:</td><td align=left><input type=textbox value="'.$row['GATECODE'].'" size=20 name="gatecode"></td></tr>';
	echo '<tr><td align=right>Gate Code:</td><td align=left><input type=textbox value="'.$row['APPLEMOTH'].'" size=20 name="applemoth"></td></tr>';
	echo '<tr><td align=center colspan=2><input type=submit value="UPDATE"></td></tr>';
	echo '</table>';
}
