<html>



<head>

<title>Fermentation Protocol</title>

</head>



<body>

<?php



$query='DELETE FROM FERMPROT WHERE ID='.$_GET['recid'];





  @ $db = mysql_pconnect('localhost', 'klm', 'kirby');

  if (!$db)

  {

     echo 'Error: Could not connect to database.  Please try again later.';

     exit;

  }

   mysql_select_db('copain');

   $result = mysql_query($query);

   if ($result)

      echo "The Fermentation Protocol has been deleted!";



   echo '<a href=http://localhost/crushpublic/crushclient/showferms.php?ccode='.$_GET['ccode'].'>Continue<a>';

?>



</body>



</html>