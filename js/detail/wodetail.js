var workPerformedBy={"":"",
	"CLIENT":"CLIENT",
	"CCC":"CCC"
	}

var woStates={	"INCOMPLETE"		: new Object({"INCOMPLETE":"INCOMPLETE","HOLD":"HOLD","NEEDS APPROVAL":"NEEDS APPROVAL","ASSIGNED":"ASSIGNED"}),
				"HOLD" 				: new Object({"HOLD":"HOLD","NEEDS APPROVAL":"NEEDS APPROVAL","ASSIGNED":"ASSIGNED"}),
				"NEEDS APPROVAL" 	: new Object({"NEEDS APPROVAL":"NEEDS APPROVAL","DENIED":"DENIED","ASSIGNED":"ASSIGNED"}),
				"DENIED" 			: new Object({"DENIED":"DENIED","NEEDS APPROVAL":"NEEDS APPROVAL"}),
				"ASSIGNED" 			: new Object({"ASSIGNED":"ASSIGNED","COMPLETED":"COMPLETED"}),
				"COMPLETED" 		: new Object({"COMPLETED":"COMPLETED"})
}

function nextWOState(currentState)
{
	return woStates[currentState];
}

function woHeader(row)
{
	var wo=lotDetailData['workorders'][row];
	// var title='<div class="ui-widget ui-state-highlight ui-corner-all" style="width: 90%; padding:5px; margin-bottom: 10px; margin-left: auto; margin-right: auto; text-align:center">';
	// title=title+'<strong>WO: '+wo['data']['ID']+' Created: '+wo['data']['CREATIONDATE']+'</strong>';
	// title=title+'<div onclick="window.open(\''+directory+'/printing/workorder.php?woid='+wo['data']['ID']+'\')" style=float:right class="ui-icon ui-icon-print"></div>';
	// title=title+'</div>';
	
	pushBreadCrumb('WO: '+wo['data']['ID']+' CREATED: '+wo['data']['CREATIONDATE']);
	$(showBreadCrumbs()).appendTo("#woDetail");
	
//	var location="#woDetail>div";
	var location="#woDetail";
	$('<div class="ui-widget ui-state-default ui-corner-all" id=woDetailLeft style="width:25%; margin-left:5%; clear:both; margin-top:20px; float:left"></div>').appendTo(location);
	$('<div class="ui-widget ui-state-default ui-corner-all" id=woDetailRight style="width:65%; float:left; margin-top:20px;"></div>').appendTo(location);
	$('#woDetailRight').html('<div class="ui-widget ui-state-default ui-corner-all" id=woDetailDiv style="width:95%;"></div>');
	
	$('<table action=update_field table=wo rowid='+wo.data.ID+' width=90% align=center></table').appendTo("#woDetailLeft");
	line='<tr><td align=right width=50%><b>TYPE:</b></td><td class=editable field=TYPE fieldtype=select selectHTML="'+buildSelect(woCodes,wo.data.TYPE)+'" onclick=makeFieldEditable(event) align=left>'+wo.data.TYPE+'</td></tr>';
	$(line).appendTo("#woDetailLeft>table");
	line='<tr><td align=right width=50%><b>LOT:</b></td><td align=left onclick=launchLotDetail(this); lotnumber='+wo.data.LOT+'>'+wo.data.LOT+'</td></tr>';
	$(line).appendTo("#woDetailLeft>table");
	line='<tr><td align=right width=50%><b>STATE:</b></td><td class=editable field=STATUS fieldtype=select selectHTML="'+buildSelect(nextWOState(wo.data.STATUS))+'" onclick=makeFieldEditable(event) align=left>'+wo.data.STATUS+'</td></tr>';
	$(line).appendTo("#woDetailLeft>table");
	line='<tr><td align=right width=50%><b>START DATE:</b></td><td class="editable aDate" field="DUEDATE,ENDDATE" fieldtype=date onclick=makeFieldEditable(event) align=left>'+dateString(parse_date(wo.data.DUEDATE))+'</td></tr>';
	$(line).appendTo("#woDetailLeft>table");
	line='<tr><td align=right width=50%><b>END DATE:</b></td><td class="editable aDate" field=ENDDATE fieldtype=date onclick=makeFieldEditable(event) align=left>'+dateString(parse_date(wo.data.ENDDATE))+'</td></tr>';
	$(line).appendTo("#woDetailLeft>table");
	line='<tr><td align=right width=50%><b>CREATION DATE:</b></td><td align=left>'+wo.data.CREATIONDATE+'</td></tr>';
	$(line).appendTo("#woDetailLeft>table");
	line='<tr><td align=right width=50%><b>CLIENTCODE:</b></td><td align=left onclick=showPanel("showLotsPanel");>'+wo.data.CLIENTCODE+'</td></tr>';
	$(line).appendTo("#woDetailLeft>table");
	line='<tr><td align=right width=50%><b>REQUESTOR:</b></td><td align=left>'+wo.data.REQUESTOR+'</td></tr>';
	$(line).appendTo("#woDetailLeft>table");
	line='<tr><td align=right width=50%><b>WORK TO BE PERFORMED BY:</b></td><td class=editable field=WORKPERFORMEDBY fieldtype=select selectHTML="'+buildSelect(workPerformedBy,wo.data.WORKPERFORMEDBY)+'" onclick=makeFieldEditable(event) align=left>'+wo.data.WORKPERFORMEDBY+'</td></tr>';
	$(line).appendTo("#woDetailLeft>table");
	$("#woDetailLeft>table").find(".aDate").datepicker();

	$('<table action=update_field table=wo rowid='+wo.data.ID+' width=90% align=center></table').appendTo("#woDetailRight");
	line='<tr><td align=left><b>DESCRIPTION:</b></td></tr>';
	$(line).appendTo("#woDetailRight>table");
	line='<tr><td class=editable action=update_field table=wo field=OTHERDESC fieldtype=textarea editsize=3 onclick=makeFieldEditable(event) align=left><div><pre>'+wo.data.OTHERDESC+'</pre></div></td></tr>';
	$(line).appendTo("#woDetailRight>table");
	line='<tr><td><br></td></tr><tr><td align=left><b>COMPLETION DESCRIPTION:</b></td></tr>';
	$(line).appendTo("#woDetailRight>table");
	line='<tr><td class=editable action=update_field table=wo field=COMPLETEDDESCRIPTION fieldtype=textarea editsize=3 onclick=makeFieldEditable(event) align=left><div><pre>'+wo.data.COMPLETEDDESCRIPTION+'</pre></div></td></tr>';
	$(line).appendTo("#woDetailRight>table");
	$('<div class="ui-widget ui-state-default ui-corner-all" id=woDetailStructureDiv style="width:100%; margin-top:20px; float:left"></div>').appendTo("#woDetailRight");
	genStructureDiv("woDetailStructureDiv",wo.structure);
	$(location).show();
}
function updateWODetailSection(e)
{
	
	if ($(e.target).filter("td").length==1)
		var parentTD=$(e.target);
	else
		var parentTD=$(e.target).parents("td");
		
	var woid=findAttr('rowid',parentTD);
	
	var rowNumber=-1;
	for (i in lotDetailData['workorders'])
	{
		if (woid==lotDetailData['workorders'][i]['data']['ID'])
		{
			rowNumber=i;
		}
	}
	$("#woDetail>*").remove();
	$("#otherLots").hide();
	$("#woDetail").show();

	woHeader(rowNumber);
	
}