function showWTs(data)
{
	$('#wtList>*').remove();
	totalCost=0.0;
	
	var header;
	var line="";
	var i;
	header=header+'<tr class=ui-widget><td class="header" width=20 class="showOnEdit" align=center><strong>Delete WT</strong></td>';
	header=header+'<td class="header sortable" align=center width=50><strong>WEIGH TAG</strong></td>';
	header=header+'<td class="header sortable" align=center width=50><strong>DATE</strong></td>';
	header=header+'<td class="header sortable" align=center width=100><strong>LOT</strong></td>';
	header=header+'<td align=left width=150 class="header sortable filterable"><strong>NAME</strong></td>';
	header=header+'<td align=left width=150 class="header sortable"><strong>APPELLATION</strong></td>';
	header=header+'<td align=left width=100 class="header sortable"><strong>VARIETAL</strong></td>';
	header=header+'<td align=left width=150 class="header sortable"><strong>REGION</strong></td>';
	header=header+'<td class="header summable" align=center width=50><strong>BINS</strong></td>';
	header=header+'<td class="header sortable summable" align=right width=50><strong>TONS</strong></td>';
	header=header+'<td class="header sortable summable" align=right width=80><strong>COST</strong></td>';
	header=header+'</tr>';
	var totalBinCount=0;
	var totalTons=0;
	for (i in data)
	{
		line=line+'<tr rowid='+data[i]['data']['wt']['ID']+' class="ui-widget ui-corner-all" style="padding: 0.2em;">';
			line=line+'<td wtid="'+data[i]['data']['wt']['ID']+'" class=showOnEdit onclick="deleteWT(this)" align=center></td>';
			line=line+'<td align=center><div onclick="launchWTDetail(this)">'+(myParseFloat(data[i]['data']['wt']['TAGID'])+5000)+'</div></td>';
			line=line+'<td align=center><div>'+dateString(parse_date(data[i]['data']['wt']['DATETIME']))+'</div></td>';
			line=line+'<td align=center><div onclick="launchLotDetail(this)">'+data[i]['data']['wt']['LOT']+'</div></td>';
			line=line+'<td align=left><div>'+data[i]['data']['wt']['NAME']+'</div></td>';
			line=line+'<td align=left><div>'+data[i]['data']['wt']['LOCATION_APPELLATION']+'</div></td>';
			line=line+'<td align=left><div>'+data[i]['data']['wt']['VARIETAL']+'</div></td>';
			line=line+'<td align=left><div>'+data[i]['data']['wt']['LOCATION_REGION']+'</div></td>';
			var bincount=0;
			var weight=0;
			var tare=0;
			for (j in data[i]['data']['wt']['bindetail'])
			{
				bincount=bincount+myParseFloat(data[i]['data']['wt']['bindetail'][j]['BINCOUNT']);
				weight=weight+myParseFloat(data[i]['data']['wt']['bindetail'][j]['WEIGHT']);
				tare=tare+myParseFloat(data[i]['data']['wt']['bindetail'][j]['TARE']);
			}
			line=line+'<td align=center><div>'+bincount+'</div></td>';
			line=line+'<td align=right><div>'+((weight-tare)/2000).toFixed(3)+'</div></td>';
			line=line+'<td class=editable editsize=7 field=COST fieldtype=text refresh=YES refreshRoutine=queryShowWTsPanel table=wt action=update_field onclick=makeFieldEditable(event) align=right><div>$'+addCommas(data[i]['data']['wt']['COST'],0)+'</div></td>';
			totalBinCount=totalBinCount+bincount;
			totalTons=totalTons+(weight-tare)/2000;
			totalCost=totalCost+myParseFloat(data[i]['data']['wt']['COST']);
		line=line+'</tr>';
	}
	footer='<tr class="ui-widget">';
	footer=footer+'<td class="showOnEdit" align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td class="totalline" align=center></td>';
	footer=footer+'<td class="totalline" align=right></td>';
	footer=footer+'<td class="totalline" align=right></td>';
	footer=footer+'</tr>';
	
	var title='<div class="ui-widget ui-state-highlight ui-corner-all" style="width: 90%; padding:5px; margin-bottom: 10px; margin-left: auto; margin-right: auto; text-align:center">';
	title=title+'<strong>HARVEST: '+defaultVintage+'</strong>';
	title=title+'</div>';
	$(title).appendTo("#wtList");
	
	
	$('<table width=80% align=center>').appendTo("#wtList");
	$(header).appendTo("#wtList>table"); 
	$(line).appendTo("#wtList>table");
	$(footer).appendTo("#wtList>table");

	footer='<tr class=ui-widget>';
	footer=footer+'<td class="showOnEdit" align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td decimals=0 align=center>'+totalBinCount+'</td>';
	footer=footer+'<td decimals=3 align=right>'+totalTons.toFixed(2)+'</td>';
	footer=footer+'<td numberFormat=cost decimals=2 align=right>$'+addCommas(totalCost)+'</td>';
	footer=footer+'</tr>';

	$(footer).appendTo("#wtList>table");

	// $('<div style="float:right" class="ui-icon ui-icon-grip-dotted-horizontal"</div>').appendTo($('#wtList .sortable'));
	// $('#wtList .sortable').attr('sortDirection','none');	
	// $('#wtList .sortable').click(function (){
	// 	sortColumn(this,1,-2);
	// });

	$('#wtList .filterable').dblclick(function (){
		filterColumn(this,1,-2);
	});
	
	$('#wtList tr').mouseover(function (){
		$(this).addClass('ui-state-default');
		});
	$('#wtList tr').mouseout(function (){
		$(this).removeClass('ui-state-default');
		});	
}

function queryShowWTsPanel()
{
	 $.getJSON(server+"?action=showwts&clientcode="+defaultClient.clientid+"&vintage="+defaultVintage,"",showWTs);
}

