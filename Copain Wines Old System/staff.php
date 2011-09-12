<?php

session_start();

?>

<html>



<head>

  <title></title>

  <link rel="stylesheet" type="text/css" href="../site.css">

  <script language="JavaScript" type="text/javascript">

  function navigate(choice)

  {

  	var url=choice.options[choice.selectedIndex].value;

  	if (url)

  	{

  		location.href=url;

  	}

  }

  </script>

</head>



<body>



<?php

include ("startdb.php");

include ("queryupdatefunctions.php");

include ("assetfunctions.php");



function nextstates($currentstate)

{

	$state['PENDING']=array("ASSIGNED","PENDING","WAITING ON CUSTOMER","HOLD", "CANCELED");

	$state['ASSIGNED']=array("ASSIGNED","COMPLETED","CANCELED","HOLD");

	$state['HOLD']=array("ASSIGNED","HOLD","CANCELED","HOLD");

	$state['WAITING ON CUSTOMER']=array("ASSIGNED","WAITING ON CUSTOMER","CANCELED","HOLD");

	$state['COMPLETED']=array("ASSIGNED","COMPLETED","HOLD","CANCELED");

	return $state[$currentstate];

}



function showassets($assettype, $date, $returnpage, $morning, $noon, $evening)

{

	$assets=listallocassets($assettype,$date,$_SESSION['custid'],$_SESSION['woid']);

	$params='?assettype='.$assettype.'&returnpage='.$returnpage.'&morning='.$morning.'&noon='.$noon.'&evening='.$evening;

	if (count($assets)==0)

	echo '<td align="center"><a href=listassets.php'.$params.'>CHOOSE '.$assettype.'(S)</a></td>';

	else

	{

		echo '<td align="center">';

		for ($i=0;$i<count($assets);$i++)

		{

			echo '<a href=listassets.php'.$params.'>'.$assets[$i]['timeslot'].' - '.$assets[$i]['name'].'</a><br>';

		}

		echo '</td>';

	}

	

}

function showallwobystatus($status, $clientcode, $assigned="")

{

	if (isstaff()=="YES")

	{

		if ($assigned=="")

		{

			$query='SELECT * FROM `wo`WHERE

       (`wo`.`STATUS` = "'.$status.'" AND wo.WORKPERFORMEDBY="CCC" AND

       NOT ((wo.TYPE = "DRYICE") OR (wo.TYPE = "PUMP OVER") OR (wo.TYPE = "PUNCH DOWN"))) ORDER BY  `wo`.`DUEDATE`';

		}

		else

		{

			$query='SELECT * FROM `wo`WHERE

       (`wo`.`STATUS` = "'.$status.'" AND wo.WORKPERFORMEDBY="CCC" AND

         wo.ASSIGNEDTO = "'.$assigned.'" AND

       NOT ((wo.TYPE = "DRYICE") OR (wo.TYPE = "PUMP OVER") OR (wo.TYPE = "PUNCH DOWN"))) ORDER BY  `wo`.`DUEDATE`';

		}

	}

	else

	{

		$query='SELECT * FROM `wo`WHERE

       (`wo`.`STATUS` = "'.$status.'" AND wo.WORKPERFORMEDBY="CLIENT" AND

        `wo`.`CLIENTCODE` = "'.$clientcode.'" AND

       NOT ((wo.TYPE = "DRYICE") OR (wo.TYPE = "PUMP OVER") OR (wo.TYPE = "PUNCH DOWN"))) ORDER BY  `wo`.`DUEDATE`';

	}

	

	$result=mysql_query($query);

	$num_rows=mysql_num_rows($result);

	for ($j=0;$j<$num_rows;$j++)

	{

		$wo[$j]=mysql_fetch_array($result);

	}



	return $wo;

}





if ($_GET['action']=="assign")

{

	$query='UPDATE wo SET ASSIGNEDTO="'.$_POST['assigned'].'" WHERE wo.ID='.$_GET['woid'].';';

	$result=mysql_query($query);

}



if ($_GET['action']=="clearassigned")

{

	$query='UPDATE wo SET ASSIGNEDTO="" WHERE wo.ID='.$_GET['woid'].';';

	$result=mysql_query($query);

}



if ($_GET['action']=="clearstate")

{

	$showstates[$_GET['woid']]="TRUE";

}



if ($_GET['action']=="setnewstate")

{

	$showstates[$_GET['woid']]="FALSE";

	$query='UPDATE wo SET STATUS="'.$_POST['newstate'].'" WHERE wo.ID='.$_GET['woid'].';';

	$result=mysql_query($query);

}



function displaywobystatus($status,$showstates,$summary="FALSE",$assignedfilter="")

{

	

	$pwo=showallwobystatus($status,'',$assignedfilter);

	if ($summary=="TRUE")

	if (count($pwo)>0)

	echo '<a href="showwo.php?status='.$status.'&assignedto='.$assignedfilter.'">'.count($pwo).'</a><br>';

	else

	echo '<br>';

	else

	{

		echo '<table align="center" width="100%">';

		echo '<tr><td colspan="7" align="center">ALL '.$status.' WORK ORDERS</td></tr>';

		echo '<tr>';

		echo '<td align="center" width="10%">WO ID</td>';

		echo '<td align="center" width="10%">DUE DATE</td>';

		echo '<td align="center" width="10%">CLIENT<BR>CODE</td>';

		echo '<td align="center" width="10%">LOT</td>';

		echo '<td align="center" width="15%">ACTIVITY</td>';

		echo '<td align="center" width="15%">ASSIGNED</td>';

		echo '<td align="center" width="15%">ACTION</td>';

		echo '</tr>';

		for ($i=0;$i<count($pwo);$i++)

		{

			echo '<tr>'.

			'<td align="center"><a href=wopage.php?action=view&returnpage='.$_SERVER['PHP_SELF'].'&woid='.$pwo[$i]['ID'].'>'.$pwo[$i]['ID'].'</a>'.'</td>'.

			'<td align="center">'.$pwo[$i]['DUEDATE'].'</td>'.

			'<td align="center">'.$pwo[$i]['CLIENTCODE'].'</td>'.

			'<td align="center">'.$pwo[$i]['LOT'].'</td>'.

			'<td align="center">'.$pwo[$i]['TYPE'].'</td>';

			

			echo '<td align="center">';

			pic($pwo[$i]['ASSIGNEDTO'],FALSE,

			listassetsnames("INDIVIDUAL"),

			$PHP_SELF.'?action=assign&woid='.$pwo[$i]['ID'],

			"assigned",

			$PHP_SELF.'?action=clearassigned&woid='.$pwo[$i]['ID']);

			echo '</td>';

			

			$states=nextstates($pwo[$i]['STATUS']);

			echo '<td align="center">';

			pic($pwo[$i]['STATUS'],

			$showstates[$pwo[$i]['ID']],

			nextstates($pwo[$i]['STATUS']),

			$PHP_SELF.'?action=setnewstate&woid='.$pwo[$i]['ID'],

			"newstate",

			$PHP_SELF.'?action=clearstate&woid='.$pwo[$i]['ID']);

			echo '</td>';

			echo '</tr>';

		}

		echo '</table>';

	}

}



if (!isset($_SESSION['clientcode']))

{

	$ci=clientinfo($_SERVER['REMOTE_USER']);

	$_SESSION['clientcode']=$ci['code'];

}





if (!isset($_SESSION['vintage']))

{

	$_SESSION['vintage']="2006";

}

//echo 'DEFAULT VINTAGE:'.$_SESSION['vintage'];



if (isstaff()=="YES")

{

	

	echo '<table border="1" align="center" width="300">';

	echo '<tr><td colspan="3" align="center">WORK ORDER PANEL FOR '.strtoupper($_SERVER['REMOTE_USER']).'</td></tr><tr></tr>';

	echo '<tr><td></td><td align="center">ALL</td><td align="center">ASSIGNED TO ME</td></tr><tr></tr>';

	echo '<tr><td align="right" width="40%">PENDING</td>';

	echo '<td align="center" width="30%">';

	displaywobystatus("PENDING",$showstates,"TRUE");

	echo '</td>';

	echo '<td align="center" width="30%">';

	displaywobystatus("PENDING",$showstates,"TRUE",strtoupper($_SERVER['REMOTE_USER']));

	echo '</td></tr>';

	echo '<tr><td align="right" width="40%">ASSIGNED</td>';

	echo '<td align="center" width="30%">';

	displaywobystatus("ASSIGNED",$showstates,"TRUE");

	echo '</td>';

	echo '<td align="center" width="30%">';

	displaywobystatus("ASSIGNED",$showstates,"TRUE",strtoupper($_SERVER['REMOTE_USER']));

	echo '</td></tr>';

/*	echo '<tr><td align="right" width="40%">COMPLETED</td>';

	echo '<td align="center" width="30%">';

	displaywobystatus("COMPLETED",$showstates,"TRUE");

	echo '</td>';

	echo '<td align="center" width="30%">';

 	displaywobystatus("COMPLETED",$showstates,"TRUE",strtoupper($_SERVER['REMOTE_USER']));

	echo '</td></tr>';

*/	

	echo '<tr><td align="right" width="40%">HOLD</td>';

	echo '<td align="center" width="30%">';

	displaywobystatus("HOLD",$showstates,"TRUE");

	echo '</td>';

	echo '<td align="center" width="30%">';

	displaywobystatus("HOLD",$showstates,"TRUE",strtoupper($_SERVER['REMOTE_USER']));

	echo '</td></tr>';

	echo '</table>';

}

else

{

	$ci=clientinfo($_SERVER['REMOTE_USER']);

	$_SESSION['clientcode']=$ci['clientcode'];

	

	echo '<table border="1" align="center" width="300">';

	echo '<tr><td colspan="3" align="center">WORK ORDER PANEL FOR '.strtoupper($_SERVER['REMOTE_USER']).'</td></tr><tr></tr>';

	echo '<tr><td width=50% align=right>PENDING</td><td align=center><a href=showcustwo.php?status=PENDING>'.count(showallwobystatus("PENDING",$ci['clientcode'])).'</a></td></tr>';

	echo '<tr><td width=50% align=right>ASSIGNED</td><td align=center><a href=showcustwo.php?status=ASSIGNED>'.count(showallwobystatus("ASSIGNED",$ci['clientcode'])).'</a></td></tr>';

	echo '<tr><td width=50% align=right>COMPLETED</td><td align=center><a href=showcustwo.php?status=COMPLETED>'.count(showallwobystatus("COMPLETED",$ci['clientcode'])).'</a></td></tr>';

	echo '<tr><td width=50% align=right>HOLD</td><td align=center><a href=showcustwo.php?status=HOLD>'.count(showallwobystatus("HOLD",$ci['clientcode'])).'</a></td></tr>';

	echo '</table>';

}

?>



</body>



</html>

