<?php

	$servername=$_SERVER['SERVER_NAME'];
	$names=explode('.',$servername);

  $db = mysql_pconnect('internal-db.s26768.gridserver.com', 'db26768', 'hoover#ella');

  if (!$db)

  {

     echo 'Error: Could not connect to database.  Please try again later.';

     exit;

  }



   mysql_select_db('db26768_'.$names[0]);





?>