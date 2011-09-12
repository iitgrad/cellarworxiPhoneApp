var labtests=["pH","TA","FSO2","TSO2","ALCOHOL","Glu/Fru","MALIC_ACID","VA","4EP","4EG"];
var juicePanelTests=["JPBRIX","JPTARTARIC","JPALPHA","JPAMMONIA","JPYEAST","JPPOT","JPBUFFER"];
var scorpionTests=["LACTO","PEDIO","ACETO","BRETT","ZYGO"];

function lotSelect(data,lot)
{
	var line;
	line='<select onchange=updateFieldOnServer(event)>';
	for (i in data)
	{
		line=line+"<option value="+data[i]['lotinfo']['LOTNUMBER']+">"+data[i]['lotinfo']['LOTNUMBER']+"</option>";
	}
	line=line+'</select>';
	return line;
}

function calculateTaskStatus()
{
	var count=0;
	var line="";
	for (i in taskData['wos'])
	{
		if (taskData['wos'][i]['STATUS']=="COMPLETED")
			count=count+1;
	}
	line=count+" of "+taskData['wos'].length;
	return line;
}
function updateTaskHeader(data)
{
	taskData=data;
	$.getJSON(server+"?action=showlots&allActive=1&clientcode="+defaultClient.clientid+"&vintage="+defaultVintage+"&wotype="+data['type'],"",showSpecificsInTask);			
//	$.getJSON(server+"?action=showlots&allActive=1&detail=YES&clientcode="+defaultClient.clientid+"&vintage="+defaultVintage+"&wotype="+data['type'],"",showSpecificsInTask);			
}

function updateTaskOnServerComplete(data)
{
	var result = JSON.parse(data);
	defaultTask=result['taskid'];
	$("#description").val(result['description']);
	taskData=result;			
	$.getJSON(server+"?action=showlots&clientcode="+defaultClient.clientid+"&vintage="+defaultVintage,"",showSpecificsInTask);			
}

function updateTaskOnServer(e)
{
	var taskid=defaultTask;
	var startDate=$("#taskStartDate").val();
	var endDate=$("#taskEndDate").val();
	var type=$("#taskType").val();
	var workperformedby=$("#workedBy").val();
	var description=$("#description").val();
	
	$.post(postServer,{ action: "update_task", taskid:taskid, startDate:startDate, endDate:endDate, type:type, 
			workperformedby:workperformedby, description:description, clientid:defaultClient.clientid},updateTaskOnServerComplete);
}

function taskTypeChanged(e)
{
	$('#saveButton').hide();
	$('#loading').show();

	updateTaskOnServer(e);
}


function updateWOComplete(data)
{
	var result = JSON.parse(data);
	for (i=0;i<result['lotinfo'].length;i++)
	{
		if (result['lotinfo'][i]['data']['ID']==result['woid'])
		{
			var lastwo=result['lotinfo'][i];
			break;
		}
	}
	var lastwo=result['lotinfo'][result['lotinfo'].length-1];
	var lot=lastwo['data']['LOT'];
	$("#"+lot+"notes").parents("tr:first").attr("rowid",lastwo['data']["ID"]);
	
	$("#"+lot+"checkBox").attr('checked',true);
	$("#"+lot+"notes").html('<pre>'+lastwo['data']['OTHERDESC']+'</pre>');
	$("#"+lot+"so2Add").text(lastwo['data']['SO2ADD']);
	$("#"+lot+"woid").html(result['woid']);
	$("#"+lot+"bblCount").html(lastwo['ending_bbls']);
	$("#"+lot+"toppingGallons").html(lastwo['ending_toppinggallons']);
	$("#"+lot+"tankGallons").html(addCommas(lastwo['ending_tankgallons'],0));
	$("#"+lot+"totalGallons").html(addCommas(calcGallons(lastwo),0));
	$("#"+lot+"topWithLot").html(lastwo['data']['TOPPINGLOT']);
	$("#"+lot+"woid").html(lastwo['data']['ID']);
	if (lastwo['data']['STATUS']=="COMPLETED")
		$("#"+lot+"completeCheckBox").attr('checked',true);
	else
		$("#"+lot+"completeCheckBox").attr('checked',false);
	

	$("#"+lot+"bblCount").show();
	$("#"+lot+"tankGallons").show();
	$("#"+lot+"toppingGallons").show();
	$("#"+lot+"totalGallons").show();
	$("#"+lot+"topWithLot").show();
	$("#"+lot+"so2Add").show();
	$("#"+lot+"notes").show();
	$("#"+lot+"woid").show();
	$("#"+lot+"completeCheckBox").show();
}

function deleteWOComplete(data)
{
	var result=JSON.parse(data);
	var lotid=result['inputs']['lotid'];
	var wotype=result['inputs']['wotype'];
	var row=$("#otherLots>table tr").filter("[lotnumber="+lotid+"]");
	$(row).attr(wotype+"WOID","NEW");
	$(row).find("."+wotype+"Data").text("");
	$(row).find("."+wotype+"Data").removeClass("editable");
	$(row).find("."+wotype+"Data").removeClass("tabable");
}

function updateWO(e,wotype)
{
	if ($(e.target).filter("td").length==1)
		var parentTD=$(e.target);
	else
		var parentTD=$(e.target).parents("td");
	var parentRow=$(parentTD).parents("tr:first");
	var lot=$(parentTD).parents("tr").attr("lotnumber");
	var taskid=defaultTask;

	if (wotype=="labtest")
	{
		if ($(e.target).attr('checked')==true)
		{
			var woid=$(parentRow).find("td").filter("[cellid=labTestWOID]").text();

			$.post(postServer,{ action: "add_labtest_wo", startDate:startDate, username:readCookie("username"), type:"LAB TEST", endDate:startDate, clientcode:defaultClient.clientcode, lot:lot,  
				 woid:woid, taskid:taskid},updateLabTestData);
		}
		else
		{
			var woid=$(parentRow).find("td").filter("[cellid=labTestWOID]").text();
			var lotid=$(parentRow).attr("lotnumber");
			$.post(postServer,{ action: "delete_wo", ID:woid, lotid:lotid, wotype:"labTest"},deleteWOComplete);			
		}
	}
	if (wotype=="topping")
	{
		if ($(e.target).attr('checked')==true)
		{
			var topWithLot=$("#"+lot+"topWithLot").val();
			var so2Add=$("#"+lot+"so2Add").text();
			var notes=$("#"+lot+"notes").text();
			var woid=$("#"+lot+"woid").text();

			$.post(postServer,{ action: "update_topping_wo", startDate:startDate, username:readCookie("username"), type:"TOPPING", endDate:startDate, clientcode:defaultClient.clientcode, lot:lot, topWithLot:topWithLot, 
				so2Add:so2Add, notes:notes, woid:woid, taskid:taskid},updateWOComplete);
		}
		else
		{
			$("#"+lot+"bblCount").hide();
			$("#"+lot+"tankGallons").hide();
			$("#"+lot+"toppingGallons").hide();
			$("#"+lot+"totalGallons").hide();
			$("#"+lot+"topWithLot").hide();
			$("#"+lot+"so2Add").hide();
			$("#"+lot+"notes").hide();
			$("#"+lot+"woid").hide();
			$("#"+lot+"completeCheckBox").hide();

			var woid=$("#"+lot+"woid").text();
			$.post(postServer,{ action: "delete_wo", ID:woid},updateWOComplete);
			$("#"+lot+"woid").text("");
		}		
	}
}

function completeWO(e)
{
	if ($(e.target).filter("td").length==1)
		var parentTD=$(e.target);
	else
		var parentTD=$(e.target).parents("td");
	var woid=findAttr("rowid",e.target);
	var table=findAttr("table",e.target);
	if ($(parentTD).find(':checked').val() !== null) {
	  $.post(postServer,{action:"update_field", table:table, rowid:woid, field:"STATUS", value:"COMPLETED"})
	}
	else
	{
	  $.post(postServer,{action:"update_field", table:table, rowid:woid, field:"STATUS", value:"ASSIGNED"})
	}
}

function doAlert(result)
{
	var parentRow=$("#"+result['elementid']).parent();
	var bblCount=$(parentRow).children("[field=ENDINGBARRELCOUNT]").text();
	var tankGallons=$(parentRow).children("[field=ENDINGTANKGALLONS]").text();
	var toppingGallons=$(parentRow).children("[field=ENDINGTOPPINGGALLONS]").text();
	var totalGallons=parseFloat(tankGallons)+parseFloat(toppingGallons)+parseFloat(bblCount)*60;
	$(parentRow).children("[field=TOTALGALLONS]").text(addCommas(totalGallons));
}

function buildToppingSpecificsAndEndingData(data)
{
	var lines="";
	var lotnumber="";
	var header='<tr class=ui-widget>';
	header=header+'<td class="header id="toggleLotCheckbox" align=center width=60px>Assign To:<input type=checkbox></input></td>';
	header=header+'<td class="header sortable" width=75px>Lot Number</td>';
	header=header+'<td class="header sortable" width=150px>Description</td>';
	header=header+'<td class="header" align=right width=35px>Tank Glns</td>';
	header=header+'<td class="header" align=right width=35px>BBLS</td>';
	header=header+'<td class="header" align=right width=35px>Topping Glns</td>';
	header=header+'<td class="header" align=right width=35px>Total Glns</td>';
	header=header+'<td class="header" align=center width=60px>Top With Lot</td>';
	header=header+'<td class="header" align=left width=35px>SO2 Add<br>(ppm)</td>';
	header=header+'<td class="header" width=150px>Notes</td>';
	header=header+'<td class="header" align=right width=50px>WO</td>'
	header=header+'<td class="header" align=center width=50px>Complete</td>'
	header=header+"</tr>";
	$('<table width=90% align=center>').appendTo("#otherLots");
	$(header).appendTo("#otherLots>table");
	for (i in data)
	{
		lines="<tr>";
		lotnumber=data[i]['lotinfo']['LOTNUMBER'];
		lines=lines+"<tr class=ui-widget action=update_field table=wo lotnumber="+lotnumber+">";
		lines=lines+'<td align=center><input id='+lotnumber+'checkBox onclick="updateWO(event,\'topping\');" type=checkbox></input></td>';
		lines=lines+'<td onclick="launchLotDetail(this)" lotnumber='+lotnumber+'>'+lotnumber+'</td>';
		lines=lines+'<td>'+data[i]['lotinfo']['DESCRIPTION']+'</td>';
		lines=lines+'<td id='+lotnumber+'tankGallons field=ENDINGTANKGALLONS fieldtype=text editsize=7 class="editable tabable" align=right runOnComplete=doAlert  onclick=makeFieldEditable(event) align=right style="display:none"></td>';
		lines=lines+'<td id='+lotnumber+'bblCount field=ENDINGBARRELCOUNT fieldtype=text editsize=7 class="editable tabable" align=right runOnComplete=doAlert onclick=makeFieldEditable(event) style="display:none"></td>';
		lines=lines+'<td id='+lotnumber+'toppingGallons field=ENDINGTOPPINGGALLONS fieldtype=text editsize=7 class="editable tabable" align=right runOnComplete=doAlert onclick=makeFieldEditable(event) align=right style="display:none"></td>';
		lines=lines+'<td id='+lotnumber+'totalGallons field=TOTALGALLONS align=right style="display:none"></td>';
		lines=lines+'<td id='+lotnumber+'topWithLot field=TOPPINGLOT fieldtype=select class="editable tabable" class=toppingLot onclick=makeFieldEditable(event) selectHTML="'+lotSelect(data)+'" align=center style="display:none"></td>';
		lines=lines+'<td id='+lotnumber+'so2Add class="editable tabable" field=SO2ADD fieldtype=text editsize=4 onclick=makeFieldEditable(event) class=so2Add align=right style="display:none"></td>';
		lines=lines+'<td id='+lotnumber+'notes class="editable tabable" field=OTHERDESC fieldtype=textarea editsize=15 onclick=makeFieldEditable(event) class=notes style="display:none"></td>';
		lines=lines+'<td id='+lotnumber+'woid align=right style="display:none"></td>';			
		lines=lines+'<td id='+lotnumber+'completeCheckBox  align=center style="display:none"><input class="completeCheckBox" onclick="completeWO(event);" type=checkbox></input></td>';
		lines=lines+'</tr>';
		$(lines).appendTo("#otherLots>table");
	}
	
	$('<div style="float:right" class="ui-icon ui-icon-grip-dotted-horizontal"</div>').appendTo($('#otherLots .sortable'));
	$('#otherLots .sortable').attr('sortDirection','none');	
	$('#otherLots .sortable').click(function (){
		sortColumn(this,1,0);
	});

	$('tr').mouseover(function() {
	  $(this).addClass('ui-state-default');
	});			
	$('tr').mouseout(function() {
	  $(this).removeClass('ui-state-default');
	});
	$('.lotCheckbox').click(function (){
		$("#saveButton").show();
	});	
	$('#toggleLotCheckbox').click(function (){
		$("#saveButton").show();
		$(".lotCheckbox").each (function () {
			$(this).attr('checked',!$(this).attr('checked'));
			updateWO(this,"topping");
		});
	});
	
	$("#otherLotsPanel").trigger('click');
	updateSpecificsAndEndingDataFromTaskData();
}

function updateSpecificsAndEndingDataFromTaskData()
{
	if (taskData==null) return;
	for (i in taskData['wos'])
	{	
		$.post(server+"?action=get_wo_data&woid="+taskData['wos'][i]['ID'],"",updateWOComplete);
	}
}

function updateLabTestData(data)
{
	updateWOData(JSON.parse(data));
}

function updateWOData(data)
{
	var lotid=data['wo']['data']['LOT'];
	var row=$("#otherLots>table tr").filter('[lotnumber='+lotid+']');
	if (data['wo']['data']['TYPE']=="PULL SAMPLE")
	{
		setCheckedCellID("pullSample",row);
		$(row).attr("pullSampleWOID",data['wo']['data']['ID']);
		
		var classList=$(row).find("td").filter("[cellid=qty]").attr('class')+" editable tabable";
		line='<td action=update_field rowid='+data['wo']['data']['ID']+' cellid=qty table=wo field=SAMPLEQTY fieldtype=text editsize=5 class="'+classList+'" align=left onclick=makeFieldEditable(event)><div style="text-align:right">'+data['wo']['data']['SAMPLEQTY']+'</div></td>';
		$(row).find("td").filter("[cellid=qty]").replaceWith(line);
		
		var classList=$(row).find("td").filter("[cellid=volume]").attr('class')+" editable tabable";
		line='<td action=update_field rowid='+data['wo']['data']['ID']+' cellid=volume table=wo field=SAMPLEVOLUME fieldtype=text editsize=5 class="'+classList+'" align=left onclick=makeFieldEditable(event)><div style="text-align:right">'+data['wo']['data']['SAMPLEVOLUME']+'</div></td>';
		$(row).find("td").filter("[cellid=volume]").replaceWith(line);
		
		$(row).find("td").filter("[cellid=pullSampleWOID]").text(data['wo']['data']['ID']);

		var classList=$(row).find("td").filter("[cellid=pullSampleWOID]").attr('class');		
		$(row).find("td").filter("[cellid=pullSampleCompleteCheckBox]").replaceWith('<td class="'+classList+'" cellid=pullSampleCompleteCheckBox rowid='+data['wo']['data']['ID']+' align=center><input onclick="completeWO(event);" type=checkbox></input></td>');
		if (data['wo']['data']['STATUS']=="COMPLETED")
			setCheckedCellID("pullSampleCompleteCheckBox",row);			
	}
	
	if (data['wo']['data']['TYPE']=="LAB TEST")
	{
		$(row).find(".labTestData").show();		
		setCheckedCellID("labTestCheckBox",row);
		
		for (var i in labtests)
		{
			var classList=$(row).find("td").filter("[cellid=labTestResult"+labtests[i]+"]").attr('class');
			$(row).find("td").filter("[cellid=labTestResult"+labtests[i]+"]").replaceWith(genLabTestLine(data.wo,classList+" labResultsDetail",labtests[i]));
		}
		for (var i in juicePanelTests)
		{
			var classList=$(row).find("td").filter("[cellid=labTestResult"+juicePanelTests[i]+"]").attr('class');
			$(row).find("td").filter("[cellid=labTestResult"+juicePanelTests[i]+"]").replaceWith(genLabTestLine(data.wo,classList+" labResultsDetail",juicePanelTests[i]));
		}
		for (var i in scorpionTests)
		{
			var classList=$(row).find("td").filter("[cellid=labTestResult"+scorpionTests[i]+"]").attr('class');
			$(row).find("td").filter("[cellid=labTestResult"+scorpionTests[i]+"]").replaceWith(genLabTestLine(data.wo,classList+" labResultsDetail",scorpionTests[i]));
		}

		$(row).find("td").filter("[cellid=labTestWOID]").text(data['wo']['data']['ID']);

		var classList=$(row).find("td").filter("[cellid=labTestCompleteCheckBox]").attr('class');
		$(row).find("td").filter("[cellid=labTestCompleteCheckBox]").replaceWith('<td class="'+classList+' labTestData" rowid='+data['wo']['data']['ID']+' align=center><input class="labTestCompleteCheckBox" onclick="completeWO(event);" type=checkbox></input></td>');
		if (data['wo']['data']['STATUS']=="COMPLETED")
			setCheckedCellID("labTestCompleteCheckBox",row);
	}
	refreshTableRow(row);
}

function getTaskData()
{
	if (taskData==null) return;
	for (i in taskData['wos'])
	{	
		$.getJSON(server+"?action=get_wo_data&woid="+taskData['wos'][i]['ID'],"",updateWOData);
	}	
}
function updatePullSampleWOComplete(data)
{
	result=JSON.parse(data);
	updateWOData(result.results);
	taskData.wos.push(result.results.data.wo.data)
}

function updatePullSampleWO(e)
{
	if ($(e.target).filter("td").length==1)
		var parentTD=$(e.target);
	else
		var parentTD=$(e.target).parents("td");
				
	lotid=findAttr("lotnumber",e.target);
	woid=findAttr("pullSampleWOID",e.target);

	if ($(e.target).is("input"))
		if (!$(e.target).attr("checked"))
		{
			$.post(postServer,{ action: "delete_wo", ID:woid, lotid:lotid, wotype:"pullSample"},deleteWOComplete);
		}
	else
	{
		qty=findCellID("qty",e);
		volume=findCellID("volume",e);
		$.post(postServer,{ action: "update_pullsample_wo", username:readCookie("username"), type:"PULL SAMPLE", startdate:taskData.startdate, enddate:taskData.enddate, clientcode:defaultClient.clientcode, lot:lotid, taskid:taskData.taskid, qty:qty, 
			volume:volume, woid:woid},updatePullSampleWOComplete);
	}
}

function updateLabTestWOComplete(data)
{
	
}

function updateLabTestRequest(e)
{
	
}

function buildPullSampleLabTestSheet(data)
{
	$("otherLots>*").remove();
	var stdWidth="40px";
	var lines="";
	var lotnumber="";
	var header='<tr class=ui-widget>';
	header=header+'<td class="header sortable" width=75px>Lot Number</td>';
	header=header+'<td class="header sortable" width=150px>Description</td>';
	header=header+'<td title="Sample Pull" expandClass=samplePull egroup=1 class="header elidable samplePull" align=center width=40px>Pull<br>Sample</td>';
	header=header+'<td class="header elidable samplePull" egroup=1 align=center width=40px>Sample<br>Qty</td>';
	header=header+'<td class="header elidable samplePull" egroup=1 align=center width=40px>Sample<br>Amount</td>';
	header=header+'<td class="header elidable samplePull" egroup=1 width=40px>WO ID</td>';
	header=header+'<td class="header elidable samplePull" egroup=1 align=center width=60px>Sample<br>Complete</td>';
	header=header+'<td title="DETAILED LAB RESULTS" expandClass=labResultsDetail egroup=2 class="elidable labResultsDetail header sortable" width=40px align=center>Lab<br>Test</td>';
	header=header+'<td class="header elidable labResultsDetail" egroup=2 align=center colspan=1 width='+stdWidth+'>LAB<br>REPORT<br>#</td>';
	header=header+'<td class="header elidable labResultsDetail" egroup=2 align=center colspan=2 width='+stdWidth+'>pH</td>';
	header=header+'<td class="header elidable labResultsDetail" egroup=2 align=center colspan=2 width='+stdWidth+'>TA</td>';
	header=header+'<td class="header elidable labResultsDetail" egroup=2 align=center colspan=2 width='+stdWidth+'>FSO2</td>';
	header=header+'<td class="header elidable labResultsDetail" egroup=2 align=center colspan=2 width='+stdWidth+'>TSO2</td>';
	header=header+'<td class="header elidable labResultsDetail" egroup=2 align=center colspan=2 width='+stdWidth+'>ETHANOL</td>';
	header=header+'<td class="header elidable labResultsDetail" egroup=2 align=center colspan=2 width='+stdWidth+'>GLU/FRU</td>';
	header=header+'<td class="header elidable labResultsDetail" egroup=2 align=center colspan=2 width='+stdWidth+'>MALIC</td>';
	header=header+'<td class="header elidable labResultsDetail" egroup=2 align=center colspan=2 width='+stdWidth+'>VA</td>';
	header=header+'<td class="header elidable labResultsDetail" egroup=2 align=center colspan=2 width='+stdWidth+'>4EP</td>';
	header=header+'<td class="header elidable labResultsDetail" egroup=2 align=center colspan=2 width='+stdWidth+'>4EG</td>';

	header=header+'<td title="JUICE PANEL" expandClass=juicePanelDetail class="header elidable juicePanelDetail" egroup=3 align=center colspan=2 width='+stdWidth+'>JPBRIX</td>';
	header=header+'<td class="header elidable juicePanelDetail" egroup=3 align=center colspan=2 width='+stdWidth+'>JPTARTARIC</td>';
	header=header+'<td class="header elidable juicePanelDetail" egroup=3 align=center colspan=2 width='+stdWidth+'>JPALPHA</td>';
	header=header+'<td class="header elidable juicePanelDetail" egroup=3 align=center colspan=2 width='+stdWidth+'>JPAMMONIA</td>';
	header=header+'<td class="header elidable juicePanelDetail" egroup=3 align=center colspan=2 width='+stdWidth+'>JPYEAST</td>';
	header=header+'<td class="header elidable juicePanelDetail" egroup=3 align=center colspan=2 width='+stdWidth+'>JPPOT</td>';
	header=header+'<td class="header elidable juicePanelDetail" egroup=3 align=center colspan=2 width='+stdWidth+'>JPBUFFER</td>';

	header=header+'<td title="SCORPION" expandClass=scorpionDetail class="header elidable scorpionDetail" egroup=4 align=center colspan=2 width='+stdWidth+'>LACTO</td>';
	header=header+'<td class="header elidable scorpionDetail" egroup=4 align=center colspan=2 width='+stdWidth+'>PEDIO</td>';
	header=header+'<td class="header elidable scorpionDetail" egroup=4 align=center colspan=2 width='+stdWidth+'>ACETO</td>';
	header=header+'<td class="header elidable scorpionDetail" egroup=4 align=center colspan=2 width='+stdWidth+'>BRETT</td>';
	header=header+'<td class="header elidable scorpionDetail" egroup=4 align=center colspan=2 width='+stdWidth+'>ZYGO</td>';

	header=header+'<td class="header" align=center width=40px>WO ID</td>';
	header=header+'<td class="header" align=center width=20px>Complete</td>';
	header=header+"</tr>";
	$('<table width=90% align=center taskid='+taskData.taskid+'>').appendTo("#otherLots");
	$(header).appendTo("#otherLots>table");
	for (i in data)
	{
		lines='<tr table=wo lotnumber='+data[i]['lotinfo']['LOTNUMBER']+' pullSampleWOID="NEW" labTestWOID="NEW">';
		lotnumber=data[i]['lotinfo']['LOTNUMBER'];
		lines=lines+'<td onclick="launchLotDetail(this)" lotnumber='+lotnumber+'>'+lotnumber+'</td>';
		lines=lines+'<td>'+data[i]['lotinfo']['DESCRIPTION']+'</td>';
		lines=lines+'<td align=center cellid=pullSample><input onclick="updatePullSampleWO(event);" type=checkbox></input></td>';
		lines=lines+'<td class=pullSampleData align=right cellid=qty></td>';
		lines=lines+'<td class=pullSampleData align=right cellid=volume></td>';
		lines=lines+'<td class=pullSampleData cellid=pullSampleWOID align=center></td>';
		lines=lines+'<td class=pullSampleData cellid=pullSampleCompleteCheckBox align=center></td>';
		lines=lines+'<td align=center cellid=labTestCheckBox><input onclick="updateWO(event,\'labtest\');" type=checkbox></input></td>';
		lines=lines+'<td align=right cellid=labReport></td>';
		
		for (j in labtests)
		{
			lines=lines+'<td width=10px class="labTestData labResultsDetail" align=center test='+labtests[i]+' action=update_labresult_web table=labresults field=VALUE1 fieldtype=text editsize=5 cellid=checkBox><input onclick="labTestRequested(event);" type=checkbox></input></td>';
			lines=lines+'<td class="labTestData" cellid=labTestResult'+labtests[j]+'></td>';
		}
		for (j in juicePanelTests)
		{
			lines=lines+'<td width=10px class="labTestData juicePanelDetail" align=center test='+juicePanelTests[i]+' action=update_labresult_web table=labresults field=VALUE1 fieldtype=text editsize=5 cellid=checkBox><input onclick="labTestRequested(event);" type=checkbox></input></td>';
			lines=lines+'<td class="labTestData" cellid=labTestResult'+juicePanelTests[j]+'></td>';
		}
		for (j in scorpionTests)
		{
			lines=lines+'<td width=10px class="labTestData scorpionDetail" align=center test='+scorpionTests[i]+' action=update_labresult_web table=labresults field=VALUE1 fieldtype=text editsize=5 cellid=checkBox><input onclick="labTestRequested(event);" type=checkbox></input></td>';
			lines=lines+'<td class="labTestData" cellid=labTestResult'+scorpionTests[j]+'></td>';
		}
		
		lines=lines+'<td class=labTestData cellid=labTestWOID align=center></td>';
		lines=lines+'<td class=labTestData cellid=labTestCompleteCheckBox align=center></td>';
		lines=lines+'</tr>';
		$(lines).appendTo("#otherLots>table");
	}

	$("#otherLots>table").get(0).scrollIntoView();
	makeTableElidable("#otherLots>table");

	$('tr').mouseover(function() {
	  $(this).addClass('ui-state-default');
	});			
	$('tr').mouseout(function() {
	  $(this).removeClass('ui-state-default');
	});
	
	$("#otherLotsPanel").trigger('click');
	getTaskData();
}
function showSpecificsInTask(data)
{
	$(".rightSubPanel").hide();
	$('#otherLots').show();
	$('#otherLots>*').remove()
	
	var title='<div class="ui-widget ui-state-highlight ui-corner-all" style="width: 90%; padding:5px; height:30px; margin-bottom: 10px; margin-left: auto; margin-right: auto; text-align:center">';
	title=title+'<div taskid='+taskData.taskid+' onclick="launchDetail(event);" style=float:left class="ui-icon ui-icon-refresh"></div>';
	title=title+'<div id=otherLotsTitle></div>';
	title=title+'<div onclick="window.open(\''+directory+'/printing/toppingWorkorderSet.php?taskid='+taskData.taskid+'\')" style=float:right class="ui-icon ui-icon-print"></div>';
	title=title+'</div>';
	$(title).appendTo("#otherLots");
	
	pushBreadCrumb('MULTI LOT :  WORKORDER '+currentMultiLotWO,"launchLotDetailOnDefaultLot()");
	$(showBreadCrumbs()).appendTo("#otherLotsTitle");
	
	
	
	if (data != null)
	{
		if (data['wotype']=="TOPPING")
		{
			buildToppingSpecificsAndEndingData(data['lots']);
		}
		else if (data['wotype']=="LAB TEST" | data['wotype']=="PULL SAMPLE")
		{
			buildPullSampleLabTestSheet(data['lots']);
		}				
	}
}
