function deleteRecord(e)
{
	if ($(e.target).filter("td").length==1)
		var parentTD=$(e.target);
	else
		var parentTD=$(e.target).parents("td:first");
	var rowid=findAttr("rowid",e.target);
	var table=findAttr("table",e.target);
	var action=findAttr("action",e.target);
	$(parentTD).parents("tr").remove();
	$.post(postServer,{action:action, rowid:rowid, table:table});
}
function facilityLine(data)
{
	var line="";
	line=line+'<tr action=update_field table=locations fieldtype=text rowid='+data['ID']+' height=20 class="ui-widget ui-corner-all" style="padding: 0.2em;">';
		line=line+'<td action=deleteRow onclick="deleteRecord(event)" align=center><div class="ui-icon ui-icon-trash"></div></td>';
		line=line+'<td field=NAME fieldsize=200 class="editable tabable" onclick=makeFieldEditable(event) align=left><div>'+data['NAME']+'</div></td>';
		line=line+'<td field=BONDNUMBER fieldsize=100 class="editable tabable" onclick=makeFieldEditable(event) align=left>'+data['BONDNUMBER']+'</td>';
		line=line+'<td field=ADDRESS1 fieldsize=200 class="editable tabable" onclick=makeFieldEditable(event) align=left>'+data['ADDRESS1']+'</td>';
		line=line+'<td field=ADDRESS2 fieldsize=200 class="editable tabable" onclick=makeFieldEditable(event) align=left>'+data['ADDRESS2']+'</td>';
		line=line+'<td field=CITY fieldsize=100 class="editable tabable" onclick=makeFieldEditable(event) align=left>'+data['CITY']+'</td>';
		line=line+'<td field=STATE fieldsize=50 class="editable tabable" onclick=makeFieldEditable(event) align=left>'+data['STATE']+'</td>';
		line=line+'<td field=ZIP fieldsize=50 class="editable tabable" onclick=makeFieldEditable(event) align=left>'+data['ZIP']+'</td>';
	line=line+'</tr>';
	return line;
}

function showFacilities(data)
{
	
	$('#genericList>*').remove();
	
	var header;
	var line="";
	header=header+'<tr><td width=30 class="showOnEdit" align=center><strong>DEL</strong></td>';
	header=header+'<td align=left width=250 class="filterable"><strong>NAME</strong></td>';
	header=header+'<td align=left width=100 class="filterable"><strong>BOND</strong></td>';
	header=header+'<td align=left width=200 class="filterable"><strong>ADDRESS1</strong></td>';
	header=header+'<td align=left width=200 class="filterable"><strong>ADDRESS2</strong></td>';
	header=header+'<td align=left width=100 class="filterable"><strong>CITY</strong></td>';
	header=header+'<td align=left width=50 class="filterable"><strong>STATE</strong></td>';
	header=header+'<td align=left width=50 class="filterable"><strong>ZIP</strong></td>';
	header=header+'</tr>';
	for (i in data)
	{
		line=line+facilityLine(data[i]);
	}
	line=line+'<tr action=update_field table=locations field=NAME value=""><td rowid=NEW onclick=addRow(event) align=center><div class="ui-icon ui-icon-plus"></div></td></tr>';
	$('<table width=90% align=center>').appendTo("#genericList");
	$(header).appendTo("#genericList>table");
	$(line).appendTo("#genericList>table");

	$('#genericList .filterable').click(function (){
		filterColumn(this,1,0);
	});
		
	$('#genericList tr').mouseover(function (){
		$(this).addClass('ui-state-default');
		});
	$('#genericList tr').mouseout(function (){
		$(this).removeClass('ui-state-default');
		});	
}
function addRowComplete(data)
{
	var result = eval('(' + data + ')');
	var line=facilityLine(result['data']);
	var lastrow=$('#genericList>table:first').find("tr:last").before(line);
//	$(line).appendTo('#genericList>table:first');
}
function addRow(e)
{
	if ($(e.target).filter("td").length==1)
		var parentTD=$(e.target);
	else
		var parentTD=$(e.target).parents("td:first");
	var rowid=findAttr("rowid",e.target);	
	var value=findAttr("value",e.target);	
	var table=findAttr("table",e.target);
	var refresh=findAttr("refresh",e.target);
	var action=findAttr("action",e.target);
	var refreshRoutine=findAttr("refreshRoutine",e.target);
	var parentrowid=findAttr("parentrowid",e.target);
	var elementid=uid();
	$(parentTD).attr("id",elementid)
	var field=findAttr("field",e.target);
	var fieldtype=findAttr("fieldtype",e.target);
	$.post(postServer,{ action:action, rowid:rowid, field:field, value:value, table:table},addRowComplete);
}
function queryFacilitiesManagement()
{
	 $.getJSON(server+"?action=showFacilities&clientcode="+defaultClient.clientid+"&vintage="+defaultVintage,"",showFacilities);
}

