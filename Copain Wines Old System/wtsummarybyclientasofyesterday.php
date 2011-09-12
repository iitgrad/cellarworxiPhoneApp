<?php

session_start();

?>

<html>



<head>

  <title></title>

<link rel="stylesheet" type="text/css" href="../site.css">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>Progressive Enhancement</title>

    <style type="text/css">
        /*margin and padding on body element
          can introduce errors in determining
          element position and are not recommended;
          we turn them off as a foundation for YUI
          CSS treatments. */
        body {
            margin:0;
        	padding:0;
        }
</style>

<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.5.2/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.5.2/build/datatable/assets/skins/sam/datatable.css" />
<script type="text/javascript" src="http://yui.yahooapis.com/2.5.2/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.5.2/build/element/element-beta-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.5.2/build/datasource/datasource-beta-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.5.2/build/datatable/datatable-beta-min.js"></script>

</head>



<body class=" yui-skin-sam">

<?php



include ("startdb.php");



$query='SELECT 

  lots.YEAR,

  SUM(`bindetail`.`WEIGHT`) AS `FIELD_1`,

  SUM(`bindetail`.`TARE`) AS `FIELD_2`,

  `wt`.`CLIENTCODE`,

  `clients`.`CLIENTNAME`

FROM

  `bindetail`

  INNER JOIN `wt` ON (`bindetail`.`WEIGHTAG` = `wt`.`ID`)

  INNER JOIN `lots` ON (`wt`.`LOT` = `lots`.`LOTNUMBER`)

  INNER JOIN `clients` ON (`lots`.`CLIENTCODE` = `clients`.`clientid`)

WHERE

  (`lots`.`YEAR` > "2002") and
  (dayofyear(wt.DATETIME)<=dayofyear(now()))

GROUP BY

  `wt`.`CLIENTCODE`,

  `clients`.`CLIENTNAME`,

  lots.YEAR';





//echo $query;



$result = mysql_query($query);



$num_results = mysql_num_rows($result);

$totalweight = 0;

for ($i=0;$i<mysql_num_rows($result);$i++)

{

    $row=mysql_fetch_array($result);

    $thetotal[$row['CLIENTNAME']][$row['YEAR']]+= $row['FIELD_1']-$row['FIELD_2'];

}

//echo '<pre>';

//print_r($thetotal);

//echo '</pre>';

//exit;

?>

<div id="markup">

<table id="history">

  <thead>
	<tr>
    <td width="20%" align="center"><b>CLIENT NAME</b></td>

    <td align=right width="10%"><b>2003 TONS</b></td>

    <td align=right width="10%"><b>2004 TONS</b></td>

    <td align=right width="10%"><b>2005 TONS</b></td>

    <td align=right width="10%"><b>2006 TONS</b></td>

    <td align=right width="10%"><b>2007 TONS</b></td>

    <td align=right width="10%"><b>2008 TONS</b></td>

  </tr>
</thead>
<tbody>

<?php

foreach ($thetotal as $key=>$value)

{

//	$row = mysql_fetch_array($result);

    echo '<tr>';

    echo '<td>';

    echo $key;

    echo '</td>';

    for ($j=2003;$j<=2008;$j++)

    {

        echo '<td>';

        if ($value[$j]>0)

        echo number_format($value[$j]/2000,2);

        else

        echo '';

        echo '</td>';

        $yearsum[$j]+=$value[$j];

    }

    echo '</tr>';

}
//echo '</tbody>';

// echo '<tfoot>';

//echo '<tr><td colspan=99><hr></td></tr>';

    echo '<tr>';

    echo '<td>';

    echo "TOTAL";

    echo '</td>';

    for ($j=2003;$j<=2008;$j++)

    {

    echo '<td>';

    echo number_format($yearsum[$j]/2000,2);

    echo '</td>';

    }

    echo '</tr>';

//echo "</tfoot>";

echo '</tbody>';

echo '</table> ';

?>
</div>

<script type="text/javascript">
YAHOO.util.Event.addListener(window, "load", function() {
    YAHOO.example.EnhanceFromMarkup = new function() {
        var myColumnDefs = [
            {key:"clientname",label:"Client Name",formatter:YAHOO.widget.DataTable.formatText,sortable:false},
            {key:"tons03",label:"2003 Tons",formatter:YAHOO.widget.DataTable.formatNumber,sortable:false},
            {key:"tons04",label:"2004 Tons",formatter:YAHOO.widget.DataTable.formatFloat,sortable:true},
            {key:"tons05",label:"2005 Tons",formatter:YAHOO.widget.DataTable.formatNumber,sortable:false},
            {key:"tons06",label:"2006 Tons",formatter:YAHOO.widget.DataTable.formatNumber,sortable:false},
            {key:"tons07",label:"2007 Tons",formatter:YAHOO.widget.DataTable.formatNumber,sortable:false},
            {key:"tons08",label:"2008 Tons",formatter:YAHOO.widget.DataTable.formatNumber,sortable:false}
        ];


        this.myDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get("history"));
        this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
        this.myDataSource.responseSchema = {
            fields: [{key:"clientname",parser:YAHOO.util.DataSource.parseString},
				{key:"tons03",parse:YAHOO.util.DataSource.parseNumber},
				{key:"tons04",parse:YAHOO.util.DataSource.parseFloat},
				"tons05","tons06","tons07","tons08"]
        };

        this.myDataTable = new YAHOO.widget.DataTable("markup", myColumnDefs, this.myDataSource);
    };
});
</script>

</body>



</html>

