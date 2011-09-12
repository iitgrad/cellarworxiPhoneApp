var lotToDelete;
var woToDelete;
var lotDetailData;
var startDate;
var showLotSummaryData=0;
var updateCount=0;
var hasChanged=0;
var defaultWO;
var currentMultiLotWO;

var wineState={"WINE_ABOVE":">=14%",
	"WINE_BELOW":"<14%",
	"BOTTLED_BELOW_INBOND":"<14% (BTL)",
	"BOTTLED_ABOVE_INBOND":">=14% (BTL)"}
	
var woCodes={"":"",
	"ADDITION":"ADDITION",
	"BBL DOWN":"BBL DOWN",
	"BLEEDOFF":"BLEED OFF",
	"BLEND":"BLENDING",
	"BLENDING":"BLENDING",
	"BOTTLING":"BOTTLING",
	"FILTRATION":"FILTRATION",
	"HEAT TANK":"HEAT TANK",
	"LAB TEST":"LAB TEST",
	"OTHER":"OTHER",
	"PRESSOFF":"PRESSOFF",
	"PULL SAMPLE":"PULL SAMPLE",
	"RACKING":"RACKING",
	"SCP":"SCP",
	"SETTLING":"SETTLING",
	"TOPPING":"TOPPING",
	"WT":"WT"
	};

var totalGallons=0.0;
var totalBBLs=0.0;
var totalTankGallons=0.0;
var totalToppingGallons=0.0;
var totalCost=0.0;
var totalCaseEquivalent=0.0;


function selectWOType(e)
{
	if ($(e.target).filter("td").length==1)
		var parentTD=$(e.target);
	else
		var parentTD=$(e.target).parents("td");
	var line='<td field=woType><select onchange=updateWOTypeOnServer(event)>';
	for (i in woCodes)
	{
		line=line+'<option value="'+i+'">'+woCodes[i]+'</option>';		
	}
	line=line+'</select></td>';
	$(parentTD).replaceWith(line);
}
function updateWOTypeOnServer(e)
{
	if ($(e.target).filter("td").length==1)
		var parentTD=$(e.target);
	else
		var parentTD=$(e.target).parents("td");
	var theType=$(e.target).val();
	var rowid=findAttr("rowid",e.target);
	var elementid=uid();
	$(parentTD).attr("id",elementid)
	$(e.target).parent().html('<div onclick=selectWOType(event)>...</div>');
	$.post(postServer,{ action: "update_wo_field", field:"TYPE", elementid:elementid, parentrowid:rowid, rowid:rowid, value:theType},updateFieldOnServerComplete);	
}


function labResultsUpdateServerComplete(data)
{
	var result = JSON.parse(data);
	
	updateCount--;
	
	$("#"+result['elementid']).filter("[test="+result['test']+"]").attr("labresultid",result['labresults_id']);
	$("#"+result['elementid']).html('<div>'+result['value']+'</div>');
}


function makeLabResultsEditable(e)
{
	hasChanged=0;
	var woid=findAttr("woid",e.target);
	if ($(e.target).filter("td").length==1)
		parentTD=$(e.target);
	else
		parentTD=$(e.target).parents("td");
	$(parentTD).attr("originalText",$(parentTD).text());
	var line='<input onchange=updateLabResultsOnServer(event) type="text" size=7 value="'+$(parentTD).attr("originalText")+'"></input>';
	$(parentTD).removeAttr('onclick');
	$(parentTD).html(line);
	$(parentTD).find("input").focus();
	$(parentTD).find("input").select();
	captureKeystroke($(parentTD).find("input"),makeLabResultsEditable,".labResultsDetail");	
}
function updateLabResultsOnServer(e)
{
	updateCount++;
	
	if ($(e.target).filter("td").length==1)
		parentTD=$(e.target);
	else
		parentTD=$(e.target).parents("td");
	var newvalue=$(e.target).val();
	var rowid=findAttr("rowid",e.target);
	var woid=findAttr("woid",e.target);
	var test=findAttr("test",e.target);
	var labresultid=findAttr("labresultid",e.target);
	var test=findAttr("test",e.target);
	var elementid=uid();
	$(parentTD).attr("id",elementid)
	
	$(e.target).parent().click(makeLabResultsEditable);
	$(parentTD).find("div").remove();
	$(e.target).replaceWith('<div>...</div>');

	$.post(postServer,{ action: "update_labresult_web", elementid:elementid, labtestid:rowid, woid:woid, value:newvalue, labresultid:labresultid, test:test}, labResultsUpdateServerComplete);		
}

function updateLabTestNumber(e)
{
	var line='<input onchange=updateLabTestNumberOnServer(event) type="text" size=7 value="'+$(e.target).text()+'"></input>';
	$(e.target).parent().removeAttr('onclick');
	$(e.target).html(line);		
}

function labtest(wo,type)
{
	var data=new Array();
	data['value']="";
	data['labtestid']="";
	data['id']="";
	data['woid']="";
	data['labtestnumber']="";
	
	if (wo['labtest']!=undefined)
	{
		for (i in wo['labtest']['results'])
		{
			if (wo['labtest']['results'][i]["LABTEST"]==type)
			{
				data['value']=wo['labtest']['results'][i]["VALUE1"];			
				data['labtestid']=wo['labtest']['results'][i]["LABTESTID"];			
				data['id']=wo['labtest']['results'][i]["ID"];			
			}
		}		
		data['labtestid']=wo['labtest']['LABTESTID'];
		data['labReportNumber']=wo['labtest']['LABTESTNUMBER'];
		data['woid']=wo['ID'];
	}
	return data;
}
function showChecked(text)
{
	if (text=="COMPLETED")
	 return "checked";
	return "";
}
function labTestRequestedComplete(data)
{
	
}
function labTestRequested(e)
{	
	if ($(e.target).filter("td").length==1)
		parentTD=$(e.target);
	else
		parentTD=$(e.target).parents("td");
	var newvalue=$(e.target).val();
	var rowid=findAttr("rowid",e.target);
	var woid=findAttr("woid",e.target);
	var test=findAttr("test",e.target);
	var labresultid=findAttr("labresultid",e.target);
	var test=findAttr("test",e.target);
	var elementid="";
	if ($(e.target).attr("checked")==true)
		request="YES";
	else
		request="NO";
	$.post(postServer,{ action: "update_labresult_web", request:request, elementid:elementid, labtestid:rowid, woid:woid, value:"-999", labresultid:labresultid, test:test}, labTestRequestedComplete);		
}
function genLabTestLine2(workorder,aClass,test)
{
	if (labtest(workorder['data'],test)['id']!="")
	 	isChecked="checked";
	else
		isChecked="";
	
	var line='<td class="labTestData '+aClass+'" align=center test='+test+' action=update_labresult_web woid='+workorder['data']['ID']+' table=labresults field=VALUE1 fieldtype=text editsize=5 labresultid="'+labtest(workorder['data'],test)['id']+
		'" cellid=checkBox><input onclick="labTestRequested(event);" type=checkbox '+isChecked+'></input></td>';
	var decimals=labTestDecimals[test];
	if (test=="MSO2")
	{
		var fso2=myParseFloat(labtest(workorder['data'],"FSO2")['value']);
		var pH=myParseFloat(labtest(workorder['data'],"pH")['value']);
		if (fso2==0 | pH==0)
		{
			line=line+'<td test='+test+' action=update_labresult_web woid='+workorder['data']['ID']+' table=labresults field=VALUE1 fieldtype=text editsize=5 labresultid="'+labtest(workorder['data'],test)['id']+
				'" class="labTestData '+aClass+'" align=left><div ></div></td>';					
		}
		else
		{
			var pK=1.81;
			var value=fso2/(Math.pow(10,pH-pK)+1);
			line=line+'<td test='+test+' action=update_labresult_web woid='+workorder['data']['ID']+' table=labresults field=VALUE1 fieldtype=text editsize=5 labresultid="'+labtest(workorder['data'],test)['id']+
				'" class="labTestData '+aClass+'" align=left><div >'+Math.round(value*Math.pow(10,decimals))/Math.pow(10,decimals)+'</div></td>';					
		}
	}
	else
	{
		line=line+'<td test='+test+' action=update_labresult_web woid='+workorder['data']['ID']+' table=labresults field=VALUE1 fieldtype=text editsize=5 labresultid="'+labtest(workorder['data'],test)['id']+
			'" class="labTestData tabable '+aClass+'" editable" align=left onclick=makeLabResultsEditable(event)><div>'+labTestNumber(test,labtest(workorder['data'],test)['value'])+'</div></td>';
	}
	return line;
}

function genLabTestLine(workorder,aClass,test)
{
	var line="";
	var decimals=labTestDecimals[test];
	if (test=="MSO2")
	{
		var fso2=myParseFloat(labtest(workorder['data'],"FSO2")['value']);
		var pH=myParseFloat(labtest(workorder['data'],"pH")['value']);
		if (fso2==0 | pH==0)
		{
			line=line+'<td test='+test+' action=update_labresult_web woid='+workorder['data']['ID']+' table=labresults field=VALUE1 fieldtype=text editsize=5 labresultid="'+labtest(workorder['data'],test)['id']+
				'" class="'+aClass+'" align=left><div ></div></td>';					
		}
		else
		{
			var pK=1.81;
			var value=fso2/(Math.pow(10,pH-pK)+1);
			line=line+'<td test='+test+' action=update_labresult_web woid='+workorder['data']['ID']+' table=labresults field=VALUE1 fieldtype=text editsize=5 labresultid="'+labtest(workorder['data'],test)['id']+
				'" class="'+aClass+'" align=left><div >'+Math.round(value*Math.pow(10,decimals))/Math.pow(10,decimals)+'</div></td>';					
		}
	}
	else
	{
		line=line+'<td test='+test+' action=update_labresult_web woid='+workorder['data']['ID']+' table=labresults field=VALUE1 fieldtype=text editsize=5 labresultid="'+labtest(workorder['data'],test)['id']+
			'" class="tabable '+aClass+' editable" align=left onclick=makeLabResultsEditable(event)><div >'+labTestNumber(test,labtest(workorder['data'],test)['value'])+'</div></td>';
	}
	return line;
}

function getRowValue(target,field)
{
	var value=$(target).parents("tr").find("td").filter("[field="+field+"]").find("div").text();
	return value;
}
function launchDetail(e)
{
	// $(e.target).parents("table:first").find("tr").removeClass("ui-widget-content");
	// $(e.target).parents("tr:first").addClass("ui-widget-content");
	$(e.target).parents("table:first").find("tr").hide();
	$(e.target).parents("table:first").find("tr:first").show();
	$(e.target).parents("table:first").find("tr:nth-child(2)").show();
	$(e.target).parents("tr:first").show();
	if ($(e.target).filter("td").length==1)
		var parentTD=$(e.target);
	else
		var parentTD=$(e.target).parents("td");

	$("#otherLots>*").remove();
	
	if (findAttr("taskid",e.target)>0)
	{
		$("#otherLots").show();
		$("#woDetail").hide();
		currentMultiLotWO=findAttr('rowid',parentTD);
		defaultTask=findAttr("taskid",e.target);
		$.getJSON(server+"?action=getTaskData&id="+defaultTask,"",updateTaskHeader);
	}
	else
	{
		$("#lotDetail").hide();
		updateWODetailSection(e);
	}
}


function multiLotStatusChanged(data)
{
	var result=JSON.parse(data);
	$("#"+result['elementid']).attr('taskid',result['taskid']);
	$("#"+result['elementid']).find('.showDetail').trigger('click');
}

function changeMultiLotStatus(e)
{
	var parentRow=$(e).parents("tr:first");
	var uniqueID=uid();
	$(parentRow).attr('id',uniqueID);
	var woid=findAttr("rowid",$(e));
	currentMultiLotWO=woid;
	$.post(postServer,{ action: "change_multilot_status", woid:woid, elementid:uniqueID, complete:$(e).attr("checked")},multiLotStatusChanged);	
}


function table(wo)
{
	if (wo['type']=="WT") return "wt";
	if (wo['type']=="BOL") return "bol";
	return "wo";
}
function reverse(direction)
{
	if (direction=="IN FROM") return "OUT TO";
	else return "IN FROM";
}
function addWOLine(index)
{
	var workorder=lotDetailData['workorders'][index];
	var theDate=parse_date(workorder['data']['DUEDATE']);
	var tons;
	
	var tasklink="";
	var taskid="";
	line="";
	if (workorder['data']['id']>0)
	{
		tasklink=" (T-"+workorder['data']['id']+")";
		taskid=workorder['data']['id'];
	}
	line=line+'<tr  class=ui-widget action=update_field index='+index+' table='+table(workorder)+' taskid='+workorder['data']['id']+' rowid="'+workorder['data']['ID']+'" >';
	line=line+'<td class=showOnEdit onclick="deleteWO(this)" align=center><div class="ui-icon ui-icon-trash"></div></td>';
	if (workorder['type']!="WT")
	{
		line=line+'<td align=center><input onclick=changeCompleteStatus(this) class=aCheckBox type=checkbox '+showChecked(workorder['data']['STATUS'])+'></input></td>';
		line=line+'<td refresh=YES class=editable refreshRoutine=refreshLotDetail align=left><div field=DUEDATE,ENDDATE onclick=makeFieldEditable(event) fieldtype=date>'+dateString(theDate)+'</div></td>';
		if (workorder['data']['id']>0)
			line=line+'<td align=center><input onclick=changeMultiLotStatus(this) class=aCheckBox type=checkbox checked></input></td>';
		else
			line=line+'<td align=center><input onclick=changeMultiLotStatus(this) class=aCheckBox type=checkbox></input></td>';		
	}
	else
	{
		line=line+'<td></td>';
		line=line+'<td refresh=YES class=editable refreshRoutine=refreshLotDetail align=left><div field=DUEDATE,ENDDATE onclick=makeFieldEditable(event) fieldtype=date>'+dateString(theDate)+'</div></td>';
		line=line+'<td></td>';
	}

	switch (workorder['type'])
	{
		case "WT":
			line=line+'<td align=left><div onclick="updateWTDetailSection(event)">'+workorder['data']['ID']+'</div></td>';
			line=line+'<td align=left><div>WT</div></td>';
			if (workorder['data']['wt']['vineyard']==null)
				tons=(workorder['data']['SUM_OF_WEIGHT']-workorder['data']['SUM_OF_TARE'])/2000 +" "+blankIfNull(workorder['data']['wt']['VARIETAL']);
			else
				tons=(workorder['data']['SUM_OF_WEIGHT']-workorder['data']['SUM_OF_TARE'])/2000 +" TONS OF "+blankIfNull(workorder['data']['wt']['vineyard']['APPELLATION'])+" "+blankIfNull(workorder['data']['wt']['vineyard']['NAME'])+" "+blankIfNull(workorder['data']['wt']['VARIETAL']);
			break;
		case "WO":
			if (workorder['data']['id']>0)
			{
				line=line+'<td align=left><a href="#" class=showDetail onclick="launchDetail(event)">'+workorder['data']['ID']+'</a></td>';
				line=line+'<td class=editable action=update_field table=wo rowid='+workorder['data']['ID']+' refresh=YES refreshRoutine=refreshLotDetail field=type fieldtype=select selectHTML="'+buildSelect(woCodes,workorder['data']['type'])+'" onclick=makeFieldEditable(event) align=left><div>'+workorder['data']['TYPE']+'</div></td>';														
//				line=line+'<td class=editable action=update_field table=tasks rowid='+workorder['data']['id']+' refresh=YES refreshRoutine=refreshLotDetail field=type fieldtype=select selectHTML="'+buildSelect(woCodes,workorder['data']['type'])+'" onclick=makeFieldEditable(event) align=left><div>'+workorder['data']['type']+'</div></td>';				
			}
			else
			{
				switch(workorder['data']['TYPE'])
				{
					case "BLENDING":
					{
						line=line+'<td align=left><a href="#" class=showDetail onclick="queryBlender(event)">'+workorder['data']['ID']+'</a></td>';
						break;						
					}
					default:
					{
						line=line+'<td align=left><a href="#" class=showDetail onclick="launchDetail(event)">'+workorder['data']['ID']+'</a></td>';
						break;
					}
				}
				line=line+'<td class=editable action=update_field table=wo rowid='+workorder['data']['ID']+' refresh=YES refreshRoutine=refreshLotDetail field=type fieldtype=select selectHTML="'+buildSelect(woCodes,workorder['data']['type'])+'" onclick=makeFieldEditable(event) align=left><div>'+workorder['data']['TYPE']+'</div></td>';														
			}
			break;
		case "BOL":
			line=line+'<td align=left><div onclick="updateWTDetailSection(event)">'+workorder['data']['ID']+'</div></td>';
			line=line+'<td align=left><div>BOL</div></td>';
			break;
		case "BLEND":
			line=line+'<td align=left><div onclick="queryBlender(event)">'+workorder['data']['ID']+'</div></td>';
			line=line+'<td align=left><div>BLEND</div></td>';
			break;
	}		
	line=line+'<td field=ENDINGTANKGALLONS fieldtype=text editsize=7 refresh=YES refreshRoutine=refreshLotDetail onclick=makeFieldEditable(event) class="inventoryDetail editable" align=left><div class=tankGallons>'+(parseFloat(workorder['ending_tankgallons'])).toFixed(1).toString()+'</div></td>';
	line=line+'<td field=ENDINGBARRELCOUNT fieldtype=text editsize=7 refresh=YES refreshRoutine=refreshLotDetail onclick=makeFieldEditable(event) class="inventoryDetail editable" align=left><div class=bblCount>'+workorder['ending_bbls']+'</div></td>';
	line=line+'<td field=ENDINGTOPPINGGALLONS fieldtype=text editsize=7 refresh=YES refreshRoutine=refreshLotDetail onclick=makeFieldEditable(event) class="inventoryDetail editable" align=left><div class=toppingGallons>'+workorder['ending_toppinggallons']+'</div></td>';
	line=line+'<td class=inventoryDetail align=left><div>'+addCommas(calcGallons(workorder),1)+'</div></td>';
	
	line=line+'<td field=COST fieldtype=text editsize=7 refresh=YES refreshRoutine=refreshLotDetail onclick=makeFieldEditable(event) class="costDetail editable" align=right><div class=cost>$'+addCommas(workorder['data']['COST'])+'</div></td>';
	line=line+'<td class=costDetail align=right><div>$'+addCommas(workorder['ending_cost'].toString())+'</div></td>';
	
	if ((workorder['data']['TYPE']=="LAB TEST")&(workorder['data']['labtest']!=undefined))
	{	
		line=line+'<td test=labReportNumber action=update_labresult_web woid='+workorder['data']['ID']+' table=labresults fieldtype=text editsize=5 labresultid="'+labtest(workorder['data'],"LabResultNumber")['id']+'" class="tabable labResultsDetail editable" align=left onclick=makeLabResultsEditable(event)><div >'+blankIfNull(workorder['data']['labtest']['LABTESTNUMBER'])+'</div></td>';
		
		line=line+genLabTestLine(workorder,"labResultsDetail","pH");
		line=line+genLabTestLine(workorder,"labResultsDetail","TA");
		line=line+genLabTestLine(workorder,"labResultsDetail","MSO2");
		line=line+genLabTestLine(workorder,"labResultsDetail","FSO2");
		line=line+genLabTestLine(workorder,"labResultsDetail","TSO2");
		line=line+genLabTestLine(workorder,"labResultsDetail","ALCOHOL");
		line=line+genLabTestLine(workorder,"labResultsDetail","Glu/Fru");
		line=line+genLabTestLine(workorder,"labResultsDetail","MALIC_ACID");
		line=line+genLabTestLine(workorder,"labResultsDetail","VA");
		line=line+genLabTestLine(workorder,"labResultsDetail","4EP");
		line=line+genLabTestLine(workorder,"labResultsDetail","4EG");
	}
	else
	{	
		line=line+'<td class="noBorder labResultsDetail" align=left><div></div></td>'; 
		line=line+'<td class="noBorder labResultsDetail" align=left><div></div></td>'; 
		line=line+'<td class="noBorder labResultsDetail" align=left><div></div></td>'; 
		line=line+'<td class="noBorder labResultsDetail" align=left><div></div></td>'; 
		if ((workorder['data']['TYPE']=="TOPPING")&(workorder['data']['SO2ADD']!=""))
			line=line+'<td class="noBorder labResultsDetail" align=left><div>(+'+workorder['data']['SO2ADD']+')</div></td>'; 			
		else
			line=line+'<td class="noBorder labResultsDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder labResultsDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder labResultsDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder labResultsDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder labResultsDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder labResultsDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder labResultsDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder labResultsDetail" align=left><div></div></td>';	
	}
	
	if (workorder['data']['TYPE']=="LAB TEST")
	{	
		line=line+genLabTestLine(workorder,"juicePanelDetail","JPBRIX");
		line=line+genLabTestLine(workorder,"juicePanelDetail","JPTARTARIC");
		line=line+genLabTestLine(workorder,"juicePanelDetail","JPALPHA");
		line=line+genLabTestLine(workorder,"juicePanelDetail","JPAMMONIA");
		line=line+genLabTestLine(workorder,"juicePanelDetail","JPYEAST");
		line=line+genLabTestLine(workorder,"juicePanelDetail","JPPOT");
		line=line+genLabTestLine(workorder,"juicePanelDetail","JPBUFFER");
	}
	else
	{
		line=line+'<td class="noBorder juicePanelDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder juicePanelDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder juicePanelDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder juicePanelDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder juicePanelDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder juicePanelDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder juicePanelDetail" align=left><div></div></td>';		
	}

	if (workorder['data']['TYPE']=="LAB TEST")
	{	
		line=line+genLabTestLine(workorder,"scorpionDetail","LACTO");
		line=line+genLabTestLine(workorder,"scorpionDetail","PEDIO");
		line=line+genLabTestLine(workorder,"scorpionDetail","ACETO");
		line=line+genLabTestLine(workorder,"scorpionDetail","BRETT");
		line=line+genLabTestLine(workorder,"scorpionDetail","ZYGO");		
	}
	else
	{
		line=line+'<td class="noBorder scorpionDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder scorpionDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder scorpionDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder scorpionDetail" align=left><div></div></td>';
		line=line+'<td class="noBorder scorpionDetail" align=left><div></div></td>';		
	}
	if (workorder['type']=="WT")
	{
		line=line+'<td class=description><pre>'+tons+'</pre></td>';
	}
	else if (workorder['type']=="BLEND")
	{
		var description="";
		for (j in workorder['data']['blend'])
		{
			description=description+myParseFloat(workorder['data']['blend'][j]['GALLONS']).toFixed(1)+" GLNS "+
			reverse(workorder['data']['blend'][j]['DIRECTION'])+" "+
			'<a href="#" onclick="launchLotDetail(this);" lotnumber='+workorder['data']['blend'][j]['SOURCELOT']+'><strong>'+workorder['data']['blend'][j]['SOURCELOT']+' '+workorder['data']['blend'][j]['SOURCELOTDESCRIPTION']+'</strong></a>';
			if (j<workorder['data']['blend'].length-1)
				description=description+"<br>";
		}
		if (workorder['data']['OTHERDESC'].length>0)
			line=line+'<td class=description><pre>'+description+'\n----------\n'+workorder['data']['OTHERDESC']+'</pre></td>';	
		else
			line=line+'<td class=description><pre>'+description+'</pre></td>';
	}
	else if (workorder['type']=="WO")
	{
		if (workorder['data']['TYPE']=="BLENDING")
		{
			var description="";
			for (j in workorder['data']['blend'])
			{
				description=description+myParseFloat(workorder['data']['blend'][j]['GALLONS']).toFixed(1)+" GLNS "+
				workorder['data']['blend'][j]['DIRECTION']+" "+
				'<a href="#" onclick="launchLotDetail(this);" lotnumber='+workorder['data']['blend'][j]['SOURCELOT']+'><strong>'+workorder['data']['blend'][j]['SOURCELOT']+' '+workorder['data']['blend'][j]['SOURCELOTDESCRIPTION']+'</strong></a>';
				if (j<workorder['data']['blend'].length-1)
					description=description+"<br>";
			}
			if (workorder['data']['OTHERDESC'].length>0)
				line=line+'<td class=description height="20"><pre>'+description+'\n----------\n'+workorder['data']['OTHERDESC']+'</pre></td>';	
			else
				line=line+'<td class=description height="20" ><pre>'+description+'</pre></td>';
		}
		else if (workorder['data']['TYPE']=="SCP")
		{
			var scpdata=workorder['data']['scp'];
			try {
				var description="ESTIMATED: "+scpdata['ESTTONS']+" OF "+scpdata['VARIETAL']+' FROM '+scpdata['vineyard']['APPELLATION']+" "+scpdata['vineyard']['NAME']+ " VINEYARD";					
			}
			catch (error)
			{
				var description="ESTIMATED: "+scpdata['ESTTONS']+" OF "+scpdata['VARIETAL']
			}
			line=line+'<td class=description height="20" ><pre>'+description+'<pre></td>';
		}
		else
		{
			line=line+'<td class="editable description" height="20" action=update_field table=wo field=OTHERDESC fieldtype=textarea editsize=3 onclick=makeFieldEditable(event) align=left><div id='+workorder['data']['ID']+'><pre>'+blankIfNull(workorder['data']['OTHERDESC'])+'</pre></div></td>';			
		}
	}
	else
	{
		line=line+'<td class="editable description" action=update_field table=wo field=OTHERDESC fieldtype=textarea editsize=3 onclick=makeFieldEditable(event) align=left><div id='+workorder['data']['ID']+'><pre>'+workorder['data']['OTHERDESC']+'</pre></div></td>';
	}
	line=line+'</tr>';
	
	if ($("#lotDetail>table tr").filter("[rowid="+workorder['data']['ID']+"]").length>0)
	{
		$("#lotDetail>table tr").filter("[rowid="+workorder['data']['ID']+"]").replaceWith(line);
	}
	else
		$(line).appendTo("#lotDetail>table");
		
	$("#lotDetail>table>tbody>tr:last").find('.woDate').datepicker();
	$("#lotDetail>table>tbody>tr:last").find('.woDate').hide();

}

function updateCookies()
{
	eraseCookie('costDetailShowing');
	createCookie('costDetailShowing',costDetailShowing);
	eraseCookie('inventoryDetailShowing');
	createCookie('inventoryDetailShowing',inventoryDetailShowing);
	eraseCookie('labResultsDetailShowing');
	createCookie('labResultsDetailShowing',labResultsDetailShowing);
	eraseCookie('juicePanelDetailShowing');
	createCookie('juicePanelDetailShowing',juicePanelDetailShowing);
	eraseCookie('scorpionDetailShowing');
	createCookie('scorpionDetailShowing',scorpionDetailShowing);
}

function updateDescription()
{
	if (juicePanelDetailShowing | scorpionDetailShowing | labResultsDetailShowing)
		$(".description").hide();
	else
		$(".description").show();
	$(".noBorder").css('border','0px');
}


function elideEvent(event,data)
{
	if ('event'=='expanded')
		$('#lotList>table').find('.description').hide();
	else
		$('#lotList>table').find('.description').show();
}
function reShowWOs(e)
{
	$("#lotDetail>table").find("tr").show();
}
function showLotDetail(data)
{
	lotDetailData=data;
	
	
	$('#lotDetail>*').remove();
	$('#lotDetail').show();
	
	var title='<div class="ui-widget ui-state-highlight ui-corner-all" style="width: 90%; padding:5px; height:30px; margin-bottom: 10px; margin-left: auto; margin-right: auto; text-align:center">';
	title=title+'<div onclick="launchLotDetail(this)" lotnumber='+data['lotinfo']['LOTNUMBER']+' style=float:left class="ui-icon ui-icon-refresh"></div>';
	title=title+'<div id=lotDetailTitle></div>';
	title=title+'<div onclick="" style=float:right class="ui-icon ui-icon-print"></div>';
	title=title+'</div>';
	$(title).appendTo("#lotDetail");
	
//	pushBreadCrumb(data['lotinfo']['LOTNUMBER']+' - '+data['lotinfo']['DESCRIPTION'],"launchLotDetailOnDefaultLot()","lotnumber="+data['lotinfo']['LOTNUMBER']);
	setBreadCrumb(data['lotinfo']['LOTNUMBER']+' - '+data['lotinfo']['DESCRIPTION'],"launchLotDetailOnDefaultLot()","lotnumber="+data['lotinfo']['LOTNUMBER'],2);
	$(showBreadCrumbs()).appendTo("#lotDetailTitle");
	
	var line="";
	var footer="";
	var i;
	
	$('<table width=90% align=center tableName=lotDetail>').appendTo("#lotDetail");

	var header='<tr class=ui-widget>';
	header=header+'<td class="header" align=left class=showOnEdit width=5>DEL</td>';
	header=header+'<td class="header" align=left width=20>Complete</td>';
	header=header+'<td class="header" align=center width=20>Date</td>';
	header=header+'<td class="header" align=center width=20>Multi<br>Lot</td>';
	header=header+'<td class="header" align=left width=45>WO</td>';
	header=header+'<td class="header filterable" align=left width=75>Type</td>';
	
	header=header+'<td title="INVENTORY DETAIL" expandClass="inventoryDetail" class="header inventoryDetail elidable" egroup=1 align=left width=10>Tank Gallons</td>';
	header=header+'<td class="header inventoryDetail elidable" egroup=1 align=left width=10>BBLS</td>';
	header=header+'<td class="header inventoryDetail elidable" egroup=1 align=left width=10>Topping Gallons</td>';
	header=header+'<td class="header inventoryDetail elidable" egroup=1 id=woTotalGallons align=left width=10>Total Gallons</td>';
	
	header=header+'<td title="COST DETAIL" expandClass="costDetail" class="header costDetail elidable" egroup=2 align=right width=70>Cost</td>';
	header=header+'<td class="header costDetail elidable" egroup=2 align=right width=70>Resulting<br>Cost</td>';
	
	header=header+'<td title="DETAILED LAB RESULTS" callBack="elideEvent" expandClass="labResultsDetail" class="header labResultsDetail elidable" egroup=3 align=left width=50>Lab<br>Report #</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=3 align=left width=25>pH</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=3 align=left width=25>TA<br>(g/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=3 align=left width=25>MSO2<br>(mg/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=3 align=left width=25>FSO2<br>(mg/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=3 align=left width=25>TSO2<br>(mg/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=3 align=left width=25>ETHANOL<br>(% Vol.)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=3 align=left width=25>GLU/FRU<br>(g/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=3 align=left width=25>MALIC<br>(g/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=3 align=left width=25>VA<br>(g/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=3 align=left width=25>4EP<br>(ug/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=3 align=left width=25>4EG<br>(ug/L)</td>';	

	header=header+'<td title="JUICE PANEL" expandClass="juicePanelDetail" class="header juicePanelDetail elidable" egroup=4 align=left width=70>Brix<br>(degrees)</td>';
	header=header+'<td class="header juicePanelDetail elidable" egroup=4 align=left width=70>Tartaric acid<br>(g/L)</td>';
	header=header+'<td class="header juicePanelDetail elidable" egroup=4 align=left width=70>Alpha-amino compounds<br>(mg/L)</td>';
	header=header+'<td class="header juicePanelDetail elidable" egroup=4 align=left width=70>Ammonia<br>(mg/L)</td>';
	header=header+'<td class="header juicePanelDetail elidable" egroup=4 align=left width=70>Yeast assimable nitrogen<br>(mg/L)</td>';
	header=header+'<td class="header juicePanelDetail elidable" egroup=4 align=left width=70>Potassium<br>(mg/L)</td>';
	header=header+'<td class="header juicePanelDetail elidable" egroup=4 align=left width=70>Buffer capacity<br>(mM/pH)</td>';

	header=header+'<td title="SCORPION" expandClass="scorpionDetail" class="header scorpionDetail elidable" egroup=5 align=left width=35>Lacto</td>';
	header=header+'<td class="header scorpionDetail elidable" egroup=5 align=left width=35>Pedio</td>';
	header=header+'<td class="header scorpionDetail elidable" egroup=5 align=left width=35>Aceto</td>';
	header=header+'<td class="header scorpionDetail elidable" egroup=5 align=left width=35>Brett</td>';
	header=header+'<td class="header scorpionDetail elidable" egroup=5 align=left width=35>Zygo</td>';

	header=header+'<td class="header description" id=woDescription align=left width=650>Description</td>';	

	header=header+'</tr>';
	$(header).appendTo("#lotDetail>table");

	for (i in data['workorders'])
	{
		addWOLine(i);
	}
	line=line+'<tr class=ui-widget action=update_field table=wo field=OTHERDESC value=""><td rowid=NEW onclick=addWO(event) align=center><div class="ui-icon ui-icon-plus"></div></td></tr>';
	$(line).appendTo("#lotDetail>table");
		
	if (defaultWO!=null)
	{
		$("#lotDetail>table tr").removeClass("ui-widget-content");		
		$("#lotDetail>table tr").filter("[rowid="+defaultWO+"]").addClass("ui-widget-content");
	}
	$('#otherLots>*').remove();

	$('#lotDetail .filterable').click(function (){
		filterColumn(this,2,-1);
	});

	$('#lotDetail tr').mouseover(function (){
		$(this).addClass('ui-state-highlight');
		});
	$('#lotDetail tr').mouseout(function (){
		$(this).removeClass('ui-state-highlight');
		});

	makeTableElidable('#lotDetail>table:first');
	
	$("#lotDetail>table tr:last").get(0).scrollIntoView();
	$("#lotDetail").get(0).scrollIntoView(0,20);
}

function updateGallons(data, listname)
{
	var listname="#lotList";
	if (data['workorders']!=null)
	{
		var lastWO=data['workorders'][data['workorders'].length-1];
		$(listname+' #bblCount_'+data['lotinfo']['LOTNUMBER']).html(myParseFloat(lastWO['ending_bbls']).toFixed(0).toString());
		$(listname+' #tankGallons_'+data['lotinfo']['LOTNUMBER']).html(addCommas(myParseFloat(lastWO['ending_tankgallons'])));
		$(listname+' #toppingGallons_'+data['lotinfo']['LOTNUMBER']).html(myParseFloat(lastWO['ending_toppinggallons']).toFixed(1).toString());
		$(listname+' #totalGallons_'+data['lotinfo']['LOTNUMBER']).html(addCommas(calcGallons(lastWO)));
		$(listname+' #caseEquivalent'+data['lotinfo']['LOTNUMBER']).html(addCommas(calcGallons(lastWO)/2.3775));
		$(listname+' #state_'+data['lotinfo']['LOTNUMBER']).html(wineState[lastWO['end_state']]);
		$(listname+' #alcohol_'+data['lotinfo']['LOTNUMBER']).html(myParseFloat(lastWO['alcohol']).toFixed(1));
		$(listname+' #cost_'+data['lotinfo']['LOTNUMBER']).html("$"+addCommas(lastWO['ending_cost']));
		if (calcGallons(lastWO)>0)
		{
			$(listname+' #costPerCase_'+data['lotinfo']['LOTNUMBER']).html("$"+addCommas((Math.round(myParseFloat(lastWO['ending_cost'])*100)/100)/(calcGallons(lastWO)/2.3775)));
			$(listname+' #costPerBottle_'+data['lotinfo']['LOTNUMBER']).html("$"+addCommas((Math.round(myParseFloat(lastWO['ending_cost'])*100)/100)/(calcGallons(lastWO)/2.3775)/12,2));			
		}
		else
		{
			$(listname+' #costPerCase_'+data['lotinfo']['LOTNUMBER']).html("$0");
			$(listname+' #costPerBottle_'+data['lotinfo']['LOTNUMBER']).html("$0.00");			
		}

		totalBBLs=totalBBLs+myParseFloat(myParseFloat(lastWO['ending_bbls']));
		$(listname+' #totalBBLs').html(totalBBLs.toFixed(0).toString());	
		totalTankGallons=totalTankGallons+myParseFloat(myParseFloat(lastWO['ending_tankgallons']));
		$(listname+' #totalTankGallons').html(addCommas(totalTankGallons));	
		totalToppingGallons=totalToppingGallons+myParseFloat(myParseFloat(lastWO['ending_toppinggallons']));
		$(listname+' #totalToppingGallons').html(totalBBLs.toFixed(0).toString());	
		totalGallons=totalGallons+myParseFloat(calcGallons(lastWO));
		$(listname+' #grandTotal').html(addCommas(totalGallons));			
		totalCaseEquivalent=totalCaseEquivalent+myParseFloat(calcGallons(lastWO)/2.3775);
		$(listname+' #totalCaseEquivalent').html(addCommas(totalCaseEquivalent));			
		totalCost=totalCost+myParseFloat(lastWO['ending_cost']);
		$("#totalCost").html("$"+addCommas(totalCost));
		$("#totalCostPerCase").html("$"+addCommas(totalCost/totalCaseEquivalent));
		$("#totalCostPerBottle").html("$"+addCommas(totalCost/totalCaseEquivalent/12,2));			
	}
}

function updateGallonsDetail(data, listname)
{
	var listname="";
	var lastWO=data['workorders'][data['workorders'].length-1];
	$(listname+' #tankGallons_'+data['lotinfo']['LOTNUMBER']).html(myParseFloat(lastWO['ending_tankgallons']).toFixed(2).toString());
	$(listname+' #bblCount_'+data['lotinfo']['LOTNUMBER']).html(myParseFloat(lastWO['ending_bbls']).toFixed(0).toString());
	$(listname+' #toppingGallons_'+data['lotinfo']['LOTNUMBER']).html(myParseFloat(lastWO['ending_toppinggallons']).toFixed(0).toString());
	$(listname+' #totalGallons_'+data['lotinfo']['LOTNUMBER']).html(myParseFloat(calcGallons(lastWO)).toFixed(2).toString());
	$(listname+' .tankGallons_'+data['lotinfo']['LOTNUMBER']).html(myParseFloat(lastWO['ending_tankgallons']).toFixed(2).toString());
}

function fillBlank(s)
{
	if (s=="")
	{
		return "---";
	}
	return s;
}
function domouseout(e)
{
	$(e.target).parents("tr:first").removeClass('rowHighlightOnMouseOver');	
	e.stopImmediatePropagation();	
}
function domousein(e)
{
	$(e.target).parents("tr:first").addClass('rowHighlightOnMouseOver');
	e.stopImmediatePropagation();	
}

function addLotLine(data,i)
{
	var line='<tr rowid='+data[i]['lotinfo']['LOTNUMBER']+' class="ui-widget ui-corner-all" style="padding: 0.2em;">';
		line=line+'<td lotid="'+data[i]['lotinfo']['LOTNUMBER']+'" onclick="deleteLot(this)" align=center><a href="#" class="ui-icon ui-icon-trash"></a></td>';
		line=line+'<td align=center action=update_field rowid='+data[i]['lotinfo']['ID']+' table=lots field=ACTIVELOT fieldtype=checkbox onclick=updateFieldOnServer(event)><input class=active type=checkbox '+yesNoChecked(data[i]['lotinfo']['ACTIVELOT'])+'></input></td>';
		line=line+'<td align=left><a href="#" onclick="launchLotDetail(this);" lotnumber='+data[i]['lotinfo']['LOTNUMBER']+'>'+data[i]['lotinfo']['LOTNUMBER']+'</a></td>';
		line=line+'<td align=left action=update_field rowid='+data[i]['lotinfo']['ID']+' table=lots field=DESCRIPTION fieldtype=text editsize=40 onclick=makeFieldEditable(event)><div>'+data[i]['lotinfo']['DESCRIPTION']+'</div></td>';
		line=line+'<td class=inventoryDetail id=bblCount_'+data[i]['lotinfo']['LOTNUMBER']+' align=right><div></div></td>';
		line=line+'<td class=inventoryDetail id=tankGallons_'+data[i]['lotinfo']['LOTNUMBER']+' align=right><div></div></td>';
		line=line+'<td class=inventoryDetail id=toppingGallons_'+data[i]['lotinfo']['LOTNUMBER']+' align=right><div></div></td>';
		line=line+'<td class=inventoryDetail id=totalGallons_'+data[i]['lotinfo']['LOTNUMBER']+' align=right><div></div></td>';
		line=line+'<td class=inventoryDetail id=caseEquivalent'+data[i]['lotinfo']['LOTNUMBER']+' align=right><div></div></td>';
		line=line+'<td class=costDetail id=cost_'+data[i]['lotinfo']['LOTNUMBER']+' align=right><div></div></td>';
		line=line+'<td class=costDetail id=costPerCase_'+data[i]['lotinfo']['LOTNUMBER']+' align=right><div></div></td>';
		line=line+'<td class=costDetail id=costPerBottle_'+data[i]['lotinfo']['LOTNUMBER']+' align=right><div></div></td>';
		line=line+'<td id=alcohol_'+data[i]['lotinfo']['LOTNUMBER']+' align=right><div></div></td>';
		line=line+'<td id=state_'+data[i]['lotinfo']['LOTNUMBER']+' align=right><div></div></td>';
	line=line+'</tr>';
	if ((data[i]['lotinfo']['ACTIVELOT']=="YES") & (showLotSummaryData==1))
	{
		$.getJSON(server+"?action=showlotinfo&clientid="+defaultClient.clientid+"&lot="+data[i]['lotinfo']['LOTNUMBER'],"",updateGallons);				
	}
	return line;
}
function genLotListFooter()
{
	footer='<tr class=ui-widget>';
	footer=footer+'<td align=center><div class="ui-icon ui-icon-plus" onClick="addLot(event)"></div></td>';
	footer=footer+'<td align=center><input type=checkbox onclick=hideShowActiveLots(event)></input></td>';
	footer=footer+'<td></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td class=totalline align=right></td>';
	footer=footer+'<td class=totalline align=right></td>';
	footer=footer+'<td class=totalline align=right></td>';
	footer=footer+'<td class=totalline align=right></td>';
	footer=footer+'<td class=totalline align=right></td>';
	footer=footer+'<td class=totalline align=right></td>';
	footer=footer+'<td class=totalline align=right></td>';
	footer=footer+'<td class=totalline align=center></td>';
	footer=footer+'<td align=right></td>';
	footer=footer+'<td align=right></td>';
	footer=footer+'</tr>';
	$(footer).appendTo("#lotList>table");
	updateRow($("#lotList>table").find("tr:last"));
	
	footer='<tr class=ui-widget>';
	footer=footer+'<td class="showOnEdit" align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td align=center></td>';
	footer=footer+'<td class=inventoryDetail decimals=0 id=totalBBLs align=right></td>';
	footer=footer+'<td class=inventoryDetail decimals=0 id=totalTankGallons align=right></td>';
	footer=footer+'<td class=inventoryDetail decimals=0 id=totalToppingGallons align=right></td>';
	footer=footer+'<td class=inventoryDetail decimals=0 id=grandTotal align=right></td>';
	footer=footer+'<td class=inventoryDetail decimals=0 id=totalCaseEquivalent align=right></td>';
	footer=footer+'<td class=costDetail numberFormat=cost decimals=0 id=totalCost align=right></td>';
	footer=footer+'<td class=costDetail numberFormat=cost decimals=2 id=totalCostPerCase align=right></td>';
	footer=footer+'<td class=costDetail numberFormat=cost decimals=2 id=totalCostPerBottle align=right></td>';
	footer=footer+'<td align=right></td>';
	footer=footer+'<td align=right></td>';
	footer=footer+'</tr>';
	$(footer).appendTo("#lotList>table");
	updateRow($("#lotList>table").find("tr:last"));
}

function showLots(data)
{
	data=data['lots'];
	
	$('#lotList>*').remove();
	
	if (showLotSummaryData==1)
		toggleButton="<center><button onclick=toggleLotDetail(event)>Turn Off Detail</button></center>";
	else
		toggleButton="<center><button onclick=toggleLotDetail(event)>Turn On Detail</button></center>";
		
	totalGallons=0.0;
	totalBBLs=0.0;
	totalCost=0.0;
	totalCaseEquivalent=0;
	totalTankGallons=0;
	totalToppingGallons=0;
	
	var title='<div class="ui-widget ui-state-highlight ui-corner-all" style="width: 90%; height:30px; padding:5px; margin-bottom: 10px; margin-left: auto; margin-right: auto; text-align:center">';
	title=title+'<div></div>';
	title=title+'<div id=lotListTitle></div>';
	title=title+'<div onclick="" style=float:right class="ui-icon ui-icon-print"></div>';
	title=title+'</div>';
	$(title).appendTo("#lotList");
	clearBreadCrumbs();
	pushBreadCrumb(defaultClient.theName,'queryShowLotsPanel()');
	$(showBreadCrumbs()).appendTo("#lotListTitle");
	
	// var title='<div class="ui-widget ui-state-highlight breadCrumb" style="margin-left:20px;">';
	// title=title+'<a href=# onclick="reShowWOs(event)"><strong>LOTS: '+defaultClient.theName+'</strong></a>';
	// title=title+'</div>';
	//  $(title).appendTo("#lotList");
	
	var line="";
	var header;
	var i;
//	header='<tr><td align=center colspan=99><button>Edit</button></td></tr>';
	header=header+'<tr class="title ui-widget"><td width=25px class="showOnEdit header" align=center><strong>Delete Lot</strong></td>';
	header=header+'<td align=center width=25px class="title header"><strong>Active</strong></td>';
	header=header+'<td align=left width=50px class="title header sortable filterable"><strong>Lot Number</strong></td>';
	header=header+'<td align=left class="title header filterable" width=300px><strong>Description</strong></td>';
	header=header+'<td title="INVENTORY" expandClass=inventoryDetail align=right class="title header inventoryDetail summable elidable" egroup=1 width=50px><strong>BBLS</strong></td>';
	header=header+'<td align=right class="title header inventoryDetail summable elidable" egroup=1 width=50px><strong>Tank Gallons</strong></td>';
	header=header+'<td align=right class="title header inventoryDetail summable elidable" egroup=1 width=50px><strong>Topping Gallons</strong></td>';
	header=header+'<td align=right class="title header inventoryDetail sortable elidable summable" egroup=1 width=50px><strong>Total Gallons</strong></td>';
	header=header+'<td align=right class="title header inventoryDetail summable elidable" egroup=1 width=50px><strong>Case<br>Equivalent</strong></td>';
	header=header+'<td title="COST DATA" expandClass=costDetail align=right class="title header costDetail summable elidable" egroup=2 ><strong>Cost</strong></td>';
	header=header+'<td align=right class="title header sortable summable elidable costDetail" egroup=2><strong>Cost<br>(per Case)</strong></td>';
	header=header+'<td align=right class="title header sortable summable elidable costDetail" egroup=2><strong>Cost<br>(per Btl)</strong></td>';
	header=header+'<td align=right width=50px class="title header" ><strong>Alcohol</strong></td>';
	header=header+'<td align=right width=50px class="title header" ><strong>State</strong></td>';
	header=header+'</tr>';
	for (i in data)
	{
		line=line+addLotLine(data,i);
	}
	
	$(toggleButton).appendTo("#lotList");
	$('<table width=90% align=center tableName=lotlist>').appendTo("#lotList");
	$(header).appendTo("#lotList>table"); 
	$(line).appendTo("#lotList>table");
	genLotListFooter();
	$("#lotList>table").find(".active").filter("[checked=false]").parents("tr").hide();

	// $('<div style="float:right" class="ui-icon ui-icon-grip-dotted-horizontal"</div>').appendTo($('#lotList .sortable'));
	// $('#lotList .sortable').attr('sortDirection','none');	
	// $('#lotList .sortable').click(function (){
	// 	sortColumn(this,1,-2);
	// });

	$('#lotList .filterable').click(function (){
		filterColumn(this,2,-2);
	});
	
	makeTableElidable("#lotList>table:first");
	makeTableElidable("#lotList>table:first");
	
	$('#lotList tr').mouseover(function (){
		$(this).addClass('ui-state-default');
		});
	$('#lotList tr').mouseout(function (){
		$(this).removeClass('ui-state-default');
		});	
}
function hideShowActiveLots(e)
{
	if ($(e.target).attr("checked"))
		$(e.target).parents("table").find("tr").show();
	else
		$(e.target).parents("table").find(".active").filter("[checked=false]").parents("tr").hide();
}
function deleteLot(e)
{
	lotToDelete=e;
	$("#dialogDeleteLot").dialog('open');
}
function deleteWO(e)
{
	woToDelete=e;
	$("#dialogDeleteWO").dialog('open');
}
function addWOComplete(data)
{
//	var result = eval('(' + data + ')');
	// var result = JSON.parse(data);
	// if (lotDetailData['workorders']==null)
	// 	lotDetailData['workorders']=new Array();
	// lotDetailData['workorders'].push(result['newwo']);
	// $("#lotDetail>table>tbody>tr:last").remove();
	// addWOLine(lotDetailData['workorders'].length-1);
	// var justAddedRow=$("#lotDetail>table>tbody>tr:last");
	// line='<tr action=update_field table=wo field=OTHERDESC value=""><td rowid=NEW onclick=addWO(event) align=center><div class="ui-icon ui-icon-plus"></div></td></tr>';
	// $(line).appendTo("#lotDetail>table");	
	// 
	// updateRow(justAddedRow);
	// updateRow($("lotDetail>table").find("tr:last"));

	$.getJSON(server+"?action=showlotinfo&clientid="+defaultClient.clientid+"&lot="+defaultLot,"",refreshLotDetailComplete);					
	
//	updateEllisions();
}
function addWO(e)
{
	$(e.target).parent().html("adding...");
	var clientname=defaultClient.theName;
	$("#woDetail").hide();
	$.post(postServer,{ action: "update_wo", ID:"NEW",vintage:defaultVintage,username:readCookie("username"),LOT:defaultLot, STATUS:"INCOMPLETE", CLIENTNAME:clientname, WORKPERFORMEDBY:"CCC"},addWOComplete);
}
function addLotComplete(theNewLotData)
{
	var result=JSON.parse(theNewLotData);
	var data=new Array();
	data.push(result);
	var line=addLotLine(data,0);

	$("#lotList>table>tbody>tr:last").remove();
	$("#lotList>table>tbody>tr:last").remove();
	$(line).appendTo("#lotList>table");
	updateRow($('#lotList>table:first').find("tr:last"));
	genLotListFooter();
	// $(footer).appendTo("#lotList>table");
	// updateRow($('#lotList>table:first').find("tr:last"));
}
function addLot(e)
{
//	var line='<td align=left>adding...</td>';
//	$(e.target).parent().html(line);
	$.post(postServer,{ action: "addlot", vintage:defaultVintage, clientid:defaultClient.clientid},addLotComplete);
}
function queryShowLotsPanel()
{
	$(".rightSubPanel").hide();
	$("#lotList").show();
	 $.getJSON(server+"?action=showlots&allActive=0&clientcode="+defaultClient.clientid+"&vintage="+defaultVintage,"",showLots);
}
function refreshLotDetail()
{
	$("#woDetail").hide();
	$.getJSON(server+"?action=showlotinfo&clientid="+defaultClient.clientid+"&lot="+defaultLot,"",refreshLotDetailComplete);					
}
function refreshLotDetailComplete(data)
{
		$('.mainPanel').hide();
	//	$('#loading').show();
		$('#lotDetail>*').remove();
		$('#defLot').html(" : "+defaultLot);
		$('#showLotDetailPanel').show();
		$('#lotList .ui-state-highlight').removeClass('ui-state-highlight');
		showLotDetail(data);	
}
function launchLotDetailOnDefaultLot()
{
	$(".rightSubPanel").hide();
	$('#lotDetail>*').remove();
	$('#defLot').html(" : "+defaultLot);
	$.getJSON(server+"?action=showlotinfo&clientid="+defaultClient.clientid+"&lot="+defaultLot,"",showLotDetail);	
	$('#showLotDetailPanel').show();

	$("#lotList").find("tr").hide();
	$("#lotList").find("tr").filter("[rowid="+defaultLot+"]").show();
	$("#lotList").find("tr:first").show();
	$("#lotList").find("tr:nth-child(2)").show();
	
}
function launchLotDetail(e)
{
	defaultLot=$(e).attr('lotnumber');
	launchLotDetailOnDefaultLot();
}

function toggleLotDetail(e)
{
	if (showLotSummaryData==1)
		showLotSummaryData=0;
	else
		showLotSummaryData=1;
	queryShowLotsPanel(e);
}

function gotoLotDetail(clientid,clientcode,clientname,lotnumber, wo)
{
	defaultClient.clientid=clientid;
	defaultClient.clientcode=clientcode;
	defaultClient.theName=clientname;
	defaultVintage="20"+lotnumber.substr(0,2);
	
	updateHeaderClientVintage();
	eraseCookie("clientname");
	eraseCookie("clientcode");
	eraseCookie("clientid");
	createCookie("clientname",defaultClient.theName);
	createCookie("clientid",defaultClient.clientid);
	createCookie("clientcode",defaultClient.clientcode);
	eraseCookie('defaultVintage');
	createCookie("defaultVintage",defaultVintage);
	
	$('#defLot').html(" : "+lotnumber);
	defaultLot=lotnumber;
	defaultWO=wo;
	$('.mainPanel').hide();
	$('#outstandingWorkOrdersPanel').hide();
	$('#lotDetail>*').remove();
	
	$.getJSON(server+"?action=showlotinfo&clientid="+defaultClient.clientid+"&lot="+defaultLot,"",showLotDetail);				
	$('#showLotDetailPanel').show();		
}
