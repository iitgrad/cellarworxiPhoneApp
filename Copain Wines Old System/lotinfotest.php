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

    function navigatechecked(choice)

    {

      var url=choice.value;

      if (choice.checked)

         url=url+"YES";

      else

         url=url+"NO";

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

  include ("totalgallons.php");

  include ("lotinforecords.php");



  $result=lotinforecords($_GET['lot']);

//  echo showstructure($result[count($result)-1]['structure']);

echo '<pre>';

  print_r($result);

  echo '</pre>';

?>



</body>



</html>

