var bolDirection={"":"",
	"IN":"IN",
	"OUT":"OUT"
	}

var bolType={"":"",
	"BONDTOBOND":"BONDTOBOND",
	"TAXPAID":"TAXPAID"
	}

var alcoholLevel={"":"",
	"<14%":"<14%",
	">=14%":">=14%"
	}

var wineState={"":"",
	"WINE":"WINE",
	"JUICE":"JUICE",
	"GRAPES":"GRAPES",
	"BOTTLED":"BOTTLED"
	}

function showBOLDetail(data)
{
	$('#woDetail>*').remove();
	$('#woDetail').show();
	bolid=data['BOLID'];
	var title='<div class="ui-widget ui-state-highlight ui-corner-all" style="width: 90%; padding:5px; margin-bottom: 10px; margin-left: auto; margin-right: auto; text-align:center">';
	title=title+'<div rowid='+data['BOLID']+' onclick="launchBOLDetail(event)" style=float:left class="ui-icon ui-icon-refresh"></div>';
	title=title+'<strong>BOL: '+data['BOLID']+' Created: '+data['CREATIONDATE']+'</strong>';
	title=title+'<div onclick="window.open(\''+directory+'/bol.php?woid='+data['ID']+'\')" style=float:right class="ui-icon ui-icon-print"></div>';
	title=title+'</div>';
	$(title).appendTo("#woDetail");
	$('<div class="ui-widget ui-state-default ui-corner-all" id=woDetailLeft style="width:25%; padding-top:5px; padding-bottom:5px; margin-left:5%; float:left"></div>').appendTo("#woDetail");
	$('<div class="ui-widget ui-state-default ui-corner-all" id=woDetailRight style="width:65%; padding-top:5px; padding-bottom:5px; float:left"></div>').appendTo("#woDetail");

	var location="#woDetailLeft";
	$('<table action=update_field table=bol rowid='+data.BOLID+' width=90% align=center></table').appendTo(location);
	var predicate=escape('LOCATIONTYPE="FACILITY"');
	var resultFields=escape('NAME,BONDNUMBER');
	line='<tr><td align=left width=10%><b>FACILITY:</b></td><td class=editable refresh=YES refreshRoutine=refreshBOLDetail resultFields='+resultFields+' lookupTable=locations lookupIndex=ID lookupField=NAME  predicate='+predicate+' field=FACILITYID fieldtype=selectFromServer onclick=makeFieldEditable(event) align=left>'+data.NAME+'</td></tr>';
	$(line).appendTo(location+">table");
	line='<tr><td align=left wdith=10%><b>BONDED #:</b></td><td>'+data.BONDNUMBER+'</td></tr>';
	$(line).appendTo(location+">table");
	line='<tr><td align=left wdith=10%><b>TAX STATUS:</b></td><td class=editable fieldtype=select field=BONDED selectHTML="'+buildSelect(bolType,data.BONDED)+'" onclick=makeFieldEditable(event)>'+data.BONDED+'</td></tr>';
	$(line).appendTo(location+">table");
	line='<tr><td align=left wdith=10%><b>ADDRESS 1:</b></td><td>'+data.ADDRESS1+'</td></tr>';
	$(line).appendTo(location+">table");
	line='<tr><td align=left wdith=10%><b>ADDRESS 2:</b></td><td>'+data.ADDRESS2+'</td></tr>';
	$(line).appendTo(location+">table");
	line='<tr><td align=left wdith=10%><b>CITY/STATE/ZIP:</b></td><td>'+data.CITY+' '+data.STATE+'  '+data.ZIP+'</td></tr>';
	$(line).appendTo(location+">table");

	$('<div class="ui-widget ui-state-default ui-corner-all" id=woDetailBody style="width:90%; margin-left:5%; float:left"></div>').appendTo("#woDetail");
	$('<table action=update_field table=bolitems width=90% align=center></table').appendTo("#woDetailBody");
	line='<tr>';
	line=line+'<td align=center width=5%></td>';
	line=line+'<td align=left width=25%>LOT NUMBER</td>';
	line=line+'<td align=right width=5%>GALLONS</td>';
	line=line+'<td align=center width=5%>ALCOHOL</td>';
	line=line+'<td align=right width=5%>PRODUCT TYPE</td>';
	line=line+'<td align=left width=55%>DESCRIPTION</td>';
	line=line+'</tr>';
	$(line).appendTo("#woDetailBody>table");
	for (var i=0; i<data.bolitems.length; i++)
	{
		var theRow=data.bolitems[i];
		line='<tr table=bolitems rowid='+theRow.BOLITEMSID+'>';
		line=line+'<td align=center class=showOnEdit onclick="deleteBOLItem(event)" align=center><div class="ui-icon ui-icon-trash"></div></td>';
		
		var predicate=escape('CLIENTCODE="'+defaultClient.clientid+'" AND ACTIVELOT="YES"');
		var resultFields=escape('LOTNUMBER,DESCRIPTION')		
		line=line+'<td align=left class=editable lookupTable=lots lookupIndex=LOTNUMBER lookupField=LOTNUMBER resultFields='+resultFields+' predicate='+predicate+' field=LOT fieldtype=selectFromServer onclick=makeFieldEditable(event)>'+theRow['LOT']+' '+data.bolitems[i]['LOTDESCRIPTION']+'</td>';
		line=line+'<td align=right class=editable field=GALLONS fieldtype=text onclick=makeFieldEditable(event)>'+theRow['GALLONS']+'</td>';
		line=line+'<td align=left class=editable fieldtype=select field=ALC selectHTML="'+buildSelect(alcoholLevel,theRow['ALC'])+'" onclick=makeFieldEditable(event)>'+theRow['ALC']+'</td>';
		line=line+'<td align=right class=editable fieldtype=select field=TYPE selectHTML="'+buildSelect(wineState,theRow['TYPE'])+'" onclick=makeFieldEditable(event)>'+theRow['TYPE']+'</td>';
		line=line+'<td align=left class=editable fieldtype=textarea field=DESCRIPTION onclick=makeFieldEditable(event)>'+theRow['BOLDESCRIPTION']+'</td>';
		line=line+'</tr>';		
		$(line).appendTo("#woDetailBody>table");
	}
	line='<tr class=ui-widget action=update_field table=wo field=OTHERDESC value=""><td rowid=NEW onclick=addBOLItem(event) align=center><div class="ui-icon ui-icon-plus"></div></td></tr>';
	$(line).appendTo("#woDetailBody>table");
	
}

function addBOLItem()
{
	$.post(postServer,{action: "addBOLItem", bolid:bolid},refreshBOLDetail);
}
function deleteBOLItem(e)
{
	bolitem=findAttr("rowid",e.target);
	$.post(postServer,{action: "deleteBOLItem", bolitem:bolitem});
	$("#woDetailBody>table>tbody>tr").filter("[rowid="+bolitem+"]").remove();
}
function refreshBOLDetail()
{
	if ($('#bolList').is(":visible"))
	{
		$('#bolList>table>tbody>tr').hide();
		$('#bolList>table>tbody>tr:first').show();
		$('#bolList>table>tbody>tr').filter('[rowid='+bolid+']').show();		
	}
	if ($('#lotDetail').is(":visible"))
	{
		$('#lotDetail>table>tbody>tr').hide();
		$('#lotDetail>table>tbody>tr:first').show();
		$('#lotDetail>table>tbody>tr').filter('[rowid='+bolid+']').show();				
	}
	$.getJSON(server+"?action=showboldetail&bolid="+bolid,"",showBOLDetail);	
}

function launchBOLDetail(e)
{
	bolid=findAttr("rowid",e.target);
	refreshBOLDetail();
}

function showBOLs(data)
{
	$('#bolList>*').remove();
				
	var line="";
	var header;
	var i;
//	header='<tr><td align=center colspan=99><button>Edit</button></td></tr>';
	header=header+'<tr class="title ui-widget"><td width=25px class="showOnEdit header" align=center><strong>Delete BOL</strong></td>';
//	header=header+'<td align=center width=25px class="title header"><strong>Active</strong></td>';
	header=header+'<td align=left class="filterable" width=5px class="title header sortable filterable"><strong>BOL Number</strong></td>';
	header=header+'<td align=left class="filterable" width=10px class="title header sortable filterable"><strong>DIRECTION</strong></td>';
	header=header+'<td align=left class="filterable" width=10px class="title header sortable filterable"><strong>BOND/TAXPAID</strong></td>';
	header=header+'<td align=left class="filterable" width=10px class="title header sortable filterable"><strong>DATE</strong></td>';
	header=header+'<td align=left class="filterable" width=10px class="title header sortable filterable"><strong>BOND NUMBER</strong></td>';
	header=header+'<td align=left class="filterable" class="title header filterable" width=300px><strong>Description</strong></td>';
	header=header+'</tr>';
	$('<table width=90% align=center tableName=bolList>').appendTo("#bolList");
	$(header).appendTo("#bolList>table"); 
	line='';
	for (i in data)
	{
		line='<tr rowid='+data[i].BOLID+'>';
		line=line+'<td align=center class=showOnEdit onclick="deleteRow(this)" align=center><div class="ui-icon ui-icon-trash"></div></td>';
		line=line+'<td><a href="#" onclick="launchBOLDetail(event);" bolnumber='+data[i].BOLID+'>'+data[i].BOLID+'</a></td>';
		line=line+'<td>'+data[i].DIRECTION+'</td>';
		line=line+'<td>'+data[i].BONDED+'</td>';
		line=line+'<td>'+data[i].DATE+'</td>';
		line=line+'<td>'+data[i].BONDNUMBER+'</td>';
		line=line+'<td>'+data[i].NAME+'</td>';
		line=line+'</tr>';
		$(line).appendTo("#bolList>table");
	}	
//	genLotListFooter();
	$("#bolList>table").find(".active").filter("[checked=false]").parents("tr").hide();

	$('#bolList .filterable').click(function (){
		filterColumn(this,1,0);
	});
		
	$('#bolList tr').mouseover(function (){
		$(this).addClass('ui-state-default');
		});
	$('#bolList tr').mouseout(function (){
		$(this).removeClass('ui-state-default');
		});	
}
function queryShowBOLsPanel()
{
	 $.getJSON(server+"?action=showbols&clientcode="+defaultClient.clientid+"&vintage="+defaultVintage,"",showBOLs);
}
