<?php
require_once('JSON.php');
require_once('../server/startdb.php');
require_once('lotinforecords.php');
require_once('staff.php');

function DuplicateMySQLRecord ($table, $id_field, $id) {
    // load the original record into an array
    $result = mysql_query("SELECT * FROM {$table} WHERE {$id_field}={$id}");
    $original_record = mysql_fetch_assoc($result);
    
    // insert the new record and get the new auto_increment id
    mysql_query("INSERT INTO {$table} (`{$id_field}`) VALUES (NULL)");
    $newid = mysql_insert_id();
    
    // generate the query to update the new record with the previous values
    $query = "UPDATE {$table} SET ";
    foreach ($original_record as $key => $value) {
        if ($key != $id_field) {
            $query .= '`'.$key.'` = "'.str_replace('"','\"',$value).'", ';
        }
    } 
    $query = substr($query,0,strlen($query)-2); # lop off the extra trailing comma
    $query .= " WHERE {$id_field}={$newid}";
    mysql_query($query);
    
    // return the new id
    return $newid;
}

	$query='SELECT blenditems.ID as BLENDITEMSID,
	 	blenditems.SOURCELOT, 
		blenditems.GALLONS, 
		blenditems.DIRECTION, 
		wo.ID,
		wo.LOT
	FROM blenditems INNER JOIN blend ON blenditems.BLENDID = blend.ID
		 INNER JOIN wo ON blend.WOID = wo.ID
	where DIRECTION="OUT TO" order by wo.ID';
	$result=mysql_query($query);
	
	$numrows=mysql_num_rows($result);
	for ($i=0;$i<$numrows;$i++)
	{
		$row[]=mysql_fetch_assoc($result);
	}
	for ($i=0;$i<$numrows;$i++)
	{
		echo '---'.$row[$i]['ID'].'<br>';
		if ($row[$i]['ID']==$row[$i+1]['ID'])
		{
			echo 'duplicate '.$row[$i]['ID'];
			$newid=DuplicateMySQLRecord ("wo", "ID", $row[$i]['ID']);
			$query='insert into blend set WOID="'.$newid.'"';
			echo $query.'<br>';
			$result=mysql_query($query);
			$newblendid=mysql_insert_id();
					
			$query='update blenditems set BLENDID="'.$newblendid.'" where ID="'.$row[$i]['BLENDITEMSID'].'"';
			echo $query.'<br>';
			$result=mysql_query($query);
		}
	}
	$query='SELECT blenditems.ID as BLENDITEMSID,
	 	blenditems.SOURCELOT, 
		blenditems.GALLONS, 
		blenditems.DIRECTION, 
		wo.ID,
		wo.LOT
	FROM blenditems INNER JOIN blend ON blenditems.BLENDID = blend.ID
		 INNER JOIN wo ON blend.WOID = wo.ID
	where DIRECTION="OUT TO" order by wo.ID';
	$result=mysql_query($query);
	for ($i=0;$i<mysql_num_rows($result);$i++)
	{
		$row=mysql_fetch_assoc($result);
		echo '<pre>';
		print_r($row);

		$query1='update wo set wo.LOT="'.$row['SOURCELOT'].'" where wo.ID="'.$row['ID'].'" limit 1';
		$result1=mysql_query($query1);

		$query2='update blenditems SET SOURCELOT="'.$row['LOT'].'", DIRECTION="IN FROM" where ID="'.$row['BLENDITEMSID'].'" limit 1';
		$result2=mysql_query($query2);

		echo $query1;
		echo '<br>';
		echo $query2;
			
	}

?>
