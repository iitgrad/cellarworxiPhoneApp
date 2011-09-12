<?php



function display_query_result($result)

{



  $num_results = mysql_num_rows($result);

  if ($num_results > 0){

 $row=mysql_fetch_array($result);

  echo '<table align="center" border="1">';

  echo '<tr>';

  $flip=false;

  $count=0;

  foreach (array_keys($row) as $key)

  {

     if ($flip & $count>3)

     echo '<td align="center">'.$key.'</td>';

     $flip = !$flip;

     $count=$count+1;

  }

  echo '</tr>';



  for ($i=0; $i<$num_results; $i++)

  {

    echo '<tr>';

    for ($j=2; $j<count($row); $j++)

    {

      echo '<td align="center">'.$row[$j].'</td>';

    }

    $row=mysql_fetch_array($result);

    echo '</tr>';

  }

  echo '</table>';      }



}



?>