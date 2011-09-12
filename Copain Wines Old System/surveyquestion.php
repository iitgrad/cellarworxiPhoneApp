<?php

  session_start();

?>

<html>



<head>

<link rel="stylesheet" type="text/css" href="../site.css">

<title></title>

</head>



<body>



<?php

  include ("startdb.php");

  if (!session_is_registered('questioncount'))

  {

     session_register('questioncount');

     $questioncount=0;

  }

  $query='SELECT ID,UCASE(QUESTION) AS QUESTION,QUESTIONTYPE FROM surveyquestions';

  $result = mysql_query($query);

  $num_rows=mysql_num_rows($result);

  for ($i=0; $i<$num_rows; $i++)

  {

     $row=mysql_fetch_array($result);

     $question[]=$row;

  }

  if ($_POST['ananswer']=="SUBMIT ANSWER")

  {

     $query='SELECT * FROM surveyanswers WHERE (USERID="'.$REMOTE_USER.'" AND QUESTIONID="'.$question[$questioncount]['ID'].'")';

     $result = mysql_query($query);

     if (mysql_num_rows($result)>0)

     {

        $value=mysql_fetch_array($result);

        $query='UPDATE INTO surveyanswers SET USERID="'.$REMOTE_USER.'",'.

            'QUESTIONID="'.$question[$questioncount]['ID'].'",'.

            'YESNOANSWER="'.$_POST['yesnoquestion'].'",'.

            'SCALEANSWER="'.$_POST['scalequestion'].'",'.

            'COMMENT="'.$_POST['comment'].'" WHERE (ID="'.$value['ID'].'")';

        $result=mysql_query($query);

	$row=mysql_fetch_array($query);

	switch ($row['

     }

     else

     {

        $query='INSERT INTO surveyanswers SET USERID="'.$REMOTE_USER.'",'.

            'QUESTIONID="'.$question[$questioncount]['ID'].'",'.

            'YESNOANSWER="'.$_POST['yesnoquestion'].'",'.

            'SCALEANSWER="'.$_POST['scalequestion'].'",'.

            'COMMENT="'.$_POST['comment'].'"';

     }

     $result=mysql_query($query);

     if (($questioncount+1) < count($question)) $questioncount++;

  }

  if ($_GET['action']=="next")

    if (($questioncount+1) < count($question)) $questioncount++;

  if ($_GET['action']=="previous")

    if ($questioncount > 0) $questioncount--;





echo '<table border="1" align=center width="70%"><form method="POST" action="'.$PHP_SELF.'">';

echo '<tr><td width="75%" align=center>QUESTION ('.($questioncount+1).' of '.count($question).'):<br><br><b>'.$question[$questioncount]['QUESTION'].'</b></td>';

echo '<td width="25%">';

switch ($question[$questioncount]['QUESTIONTYPE'])

{

  case 'YESNO' :

     echo '<input type="radio" value="YES" name="yesnoquestion">YES';

     echo '<input type="radio" value="NO" name="yesnoquestion">NO';

     break;

  case 'SCALE1TO5' :

     echo '<input type="radio" value="5" name="scalequestion">STRONGLY AGREE';

     echo '<br><input type="radio" value="4" name="scalequestion">SOMEWHAT AGREE';

     echo '<br><input type="radio" value="3" name="scalequestion">NEITHER AGREE OR DISAGREE';

     echo '<br><input type="radio" value="2" name="scalequestion">SOMEWHAT DISAGREE';

     echo '<br><input type="radio" value="1" name="scalequestion">STRONGLY DISAGREE';

     break;

  default : echo "PLEASE FILL-IN COMMENT AREA BELOW";

}

echo '</td>';

echo '</tr>';

echo '<tr>';

echo '<td colspan="2" align="left">COMMENT AREA:<br><textarea name="comment" rows="5" cols="100"></textarea></td>';

echo '</tr></table>';

echo '<table border="1" align=center width="70%">';

echo '<tr>';

if ($questioncount>0) echo '<td width="33%" align="center"><a href='.$PHP_SELF.'?action=previous>PREVIOUS QUESTION</a></td>'; else echo '<td width="33%"></td>';

echo '<td width="33%" align="center"><input type=submit name="ananswer" value="SUBMIT ANSWER"></td>';

if ($questioncount+1<count($question)) echo '<td width="33%" align="center"><a href='.$PHP_SELF.'?action=next>NEXT QUESTION</a></td>'; else echo '<td width="33%"></td>';

echo '</tr>';

echo '</table></form>';



?>



</body>



</html>

