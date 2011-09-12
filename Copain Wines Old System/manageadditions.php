<?php

   function manage_additions($fermprotid,$ccode)

   {

     if ($fermprotid !="")

     {

     $query = 'SELECT

      `additions`.`SUPERFOODAMT`,

      `additions`.`DAPAMOUNT`,

      additions.DAYCOUNT,

      `additions`.`HTAAMOUNT`,

      `additions`.`GOAMOUNT`,

      additions.BLEEDAMOUNT,

      `additions`.`WATERAMOUNT`,

      `additions`.`INNOCULATIONBRAND`,

      `additions`.`BRIX`,

      `additions`.`LABTEST`,

       fermprot.LOT,

      `additions`.`INNOCULATIONAMOUNT`,

     `fpaddmap`.`ID`

    FROM

      `fpaddmap`

      INNER JOIN `fermprot` ON (`fpaddmap`.`FERMPROTID` = `fermprot`.`id`)

      INNER JOIN `additions` ON (`fpaddmap`.`ADDITIONID` = `additions`.`ID`)

    WHERE

      (`fpaddmap`.`FERMPROTID` ='.$fermprotid.') order by fpaddmap.DATE, additions.DAYCOUNT, additions.BRIX DESC, additions.ID';



     //echo $query;



     $result=mysql_query($query);

     $row_nums=mysql_num_rows($result);



     echo '<form method="POST" action="fermprot.php?modification=addaddition&recid='.$fermprotid.'&ccode='.$ccode.'">';

     echo '<table border="1" width="100%">';

     $lotquery='select LOT from fermprot where id="'.$fermprotid.'"';

     $lotresult=mysql_query($lotquery);

     $lotrow=mysql_fetch_array($lotresult);

     echo '<tr><td align=left colspan=11>FRUIT ARRIVAL: '.date("m-d-Y",firstday($lotrow['LOT'])).'</td></tr>';

     echo '<tr align="center">';

     echo '<td align=center>DAY<br>COUNT</td><td>BRIX</td><td>LABTEST</td><td>SF</td><td>DAP</td><td>HTA</td><td>BLEED</td><td>H2O</td><td>INOC<br>WITH</td><td>INOC<br>AMT</td><td></td>';

     echo '</tr>';

     for ($i=0; $i<$row_nums; $i++)

     {

        $row=mysql_fetch_array($result);

        echo '<tr>';

//        echo '<td align="center">'.$row['DATE'].'</td>';

        echo '<td align="center">'.$row['DAYCOUNT'].'</td>';

        echo '<td align="center">'.$row['BRIX'].'</td>';

        echo '<td align="center">'.$row['LABTEST'].'</td>';

        if ($row['LABTEST']=="")

        {

        echo '<td align="center">'.$row['SUPERFOODAMT'].'</td>';

        echo '<td align="center">'.$row['DAPAMOUNT'].'</td>';

        echo '<td align="center">'.$row['HTAAMOUNT'].'</td>';

        echo '<td align="center">'.$row['BLEEDAMOUNT'].'</td>';

        echo '<td align="center">'.$row['WATERAMOUNT'].'</td>';

        echo '<td align="center">'.$row['INNOCULATIONBRAND'].'</td>';

        echo '<td align="center">'.$row['INNOCULATIONAMOUNT'].'</td>';

        }

        else

        echo '<td colspan=7></td>';

        echo '<td align="center"><a href=fermprot.php?modification=deladdition&recid='.$fermprotid.'&ccode='.$ccode.'&additionid='.$row['ID'].'>del</a></td>';

        echo '</tr>';

     }

     echo '<tr>';

//     echo '<td align="center"><input  value="'.date("Y-m-d",time()).'" type="text" name="DATE" size="8"</td>';

     echo '<td align="center"><input type="text" name="DAYCOUNT" size="3"></td>';

     echo '<td align="center"><input type="text" name="BRIX" size="3"></td>';

//     echo '<td align="center"><input type="text" name="LABTEST" size="5"></td>';

     echo '<td align="center">'.DrawComboFromEnum("labresults","LABTEST","","LABTEST").'</td>';

     echo '<td align="center"><input type="text" name="SF" size="3"></td>';

     echo '<td align="center"><input type="text" name="DAP" size="3"></td>';

     echo '<td align="center"><input type="text" name="HTA" size="3"></td>';

     echo '<td align="center"><input type="text" name="BLEED" size="3"></td>';

     echo '<td align="center"><input type="text" name="H20" size="3"></td>';

     echo '<td align="center"><input type="text" name="INOCBRAND" size="3"></td>';

     echo '<td align="center"><input type="text" name="INOCAMT" size="3"></td>';

     echo '<input type="hidden" name="FERMPROTRECID" value='.$fermprotid.'>';

     echo '</tr>';

     echo '<tr><td align="center" colspan="9"><input type="submit" value="ADD RECORD" name="B1"></td></tr>';

     echo '</table>';

     }

   }



?>