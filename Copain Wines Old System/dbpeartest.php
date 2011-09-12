<html>



<head>

  <title></title>

</head>



<body>



<?php

require_once 'DB.php';

$user = 'root';

$pass = 'kirby';

$host = 'localhost';

$db_name = 'weightags';

$dsn = "mysql://$user:$pass@$host/$db_name";

$db = DB::connect($dsn);

if (DB::isError($db))

 {    die ($db->getMessage());}

 // close conection

 $db->disconnect();



?>

</body>



</html>