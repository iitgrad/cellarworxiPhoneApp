var blendLots=new Array();
var sourceStructure=new Object;
var destinationLotStructure=new Object;
var sourceLotsGallons=new Array();
var destinationLotCost;
var destinationLotVolume;
var trHeight="20px";
var blendWOID="";
var currentBlendingWO=new Object;

function blendUpdateComplete(data)
{
	var results=JSON.parse(data);
//	$('#blendRecordButton').hide();
	$('#blendRecordButton>button').text("UPDATE")
	blendWOID=results['results']['woid'];
	$("#blendWOID").text("BLENDING WO: "+blendWOID);
}
function recordBlendToServer()
{
	dataToServer=new Object;
	dataToServer['inputLots']=new Object;
	sourceLots=$("#sourceLots table tr:visible");
	for (i=1; i<sourceLots.length; i++)
	{
		var theGallons=$(sourceLots[i]).find("td").filter("[cellid=gallonsOut]");
		if ($(theGallons).text()!="")
			dataToServer['inputLots'][$(sourceLots[i]).attr('lotid')]=$(theGallons).text();
	}
	dataToServer['receivingLot']=$($("#destinationLots table tr:visible").get(1)).attr("lotid");
	jsonString=JSON.stringify(dataToServer);
	$.post(postServer,{ action: "update_wo_blending", cliendcode:defaultClient.clientcode, data:jsonString, woid:blendWOID, date:$("#blendDate").val()},blendUpdateComplete);	
}

function combineStructure(original,additional,gallons)
{
	var totalGallons=0;
	for (categories in additional)
	{
		for (items in additional[categories])
		{
			totalGallons=totalGallons+additional[categories][items];
		}
		break;
	}
	
	if (original==null)
	{
		var newStructure={"year":undefined,"variety":undefined,"appellation":undefined,"vineyard":undefined};
		for (i in newStructure)
		{
			newStructure[i]=new Object;
		}
	}
	else
		var newStructure=original;
	for (categories in additional)
	{
		for (items in additional[categories])
		{
			if (original==null)
				newStructure[categories][items]=gallons;
			else
				newStructure[categories][items]=myParseFloat(newStructure[categories][items])+myParseFloat(additional[categories][items])*(gallons/totalGallons);
		}
	}
	return newStructure;
}
function showOutputLots(data)
{
	$('#outToLots>*').remove();
	var result=data['result'];
	for (i in result)
	{
		if (result[i]['SOURCELOT']==data['inputs']['lot'])
		{
			$('<tr height='+trHeight+' lotid='+i+' onclick=getInsAndOuts(event)><td>'+result[i]['GALLONS']+'</td><td>'+i+'</td><td>'+result[i]['DESCRIPTION']+'</td></tr>').appendTo("#outToLots");
		}
	}
}
function showInputLots(data)
{
	$('#inFromLots>*').remove();
	var result=data['result'];
	for (i in result)
	{
		if (result[i]['SOURCELOT']==data['inputs']['lot'])
//		if (result[i]['SOURCELOT']==data['inputs']['lot'])
		{
			$('<tr height='+trHeight+' lotid='+i+' onclick=getInsAndOuts(event)><td>'+i+'</td><td>'+result[i]['DESCRIPTION']+'</td><td cellid=gallons>'+result[i]['GALLONS']+'</td></tr>').appendTo("#inFromLots");
		}
	}
}
function getSpecificStructure(structure, structureItem, tG)
{
	if (structure==null)
	{
		structure=new Object;
		structure[structureItem]=[{"---":0}];
	}
	var item='<div class="ui-state-default ui-corner-all"><table width=100%>';
	for (i in structure[structureItem])
	{
		item=item+'<tr>';
		if (tG!=0)
			item=item+'<td>'+i+'</td><td align=right>'+Math.round(structure[structureItem][i]/tG*1000)/10+'%</td>';
		// else
		// 	item=item+'<td>'+i+'</td><td align=right>0%</td>';
		item=item+'</tr>';
	}
	item=item+'</table></div>';
	return item;	
}
function updateStructure(data)
{
	genStructureDiv("structure",data['workorders'][data['workorders'].length-1]['structure']);
	$("#structure").show();
}

function getInsAndOuts(e)
{
	lotid=findAttr("lotid",e.target);
	$("#subjectLot tr").hide();
	$("#subjectLot tr").filter("[lotid="+lotid+"]").show();
	$.getJSON(server+"?action=lotMadeUpOfLots&lot="+lotid+"&asOfDate="+$("#blendDate").val(),"",showInputLots);
	$.getJSON(server+"?action=lotInputToLots&lot="+lotid+"&asOfDate="+$("#blendDate").val(),"",showOutputLots);
	$.getJSON(server+"?action=showlotinfo&clientid="+defaultClient.clientid+"&lot="+lotid,"",updateStructure);					
}
function calcCostFromWOList(data)
{
	if (data==null) return 0;
	var lastItem=data.length-1;
	return myParseFloat(data[lastItem]['ending_cost']);
}
function calcGallonsFromWOList(data,offset)
{
	if (data==null) return 0;
	var lastItem=data.length-(1-offset);
	if (lastItem<0) return 0;
	var endingTankGallons=myParseFloat(data[lastItem]['ending_tankgallons']);
	var endingToppingGallons=myParseFloat(data[lastItem]['ending_toppinggallons']);
	var endingBBLCount=myParseFloat(data[lastItem]['ending_bbls']);
	return (endingTankGallons+endingToppingGallons+(endingBBLCount*60));
}
function destinationLotInfoReceived(data)
{
	if (blendWOID=="NEW")
	{
		var gallons=calcGallonsFromWOList(data['workorders'],0);
		var cost=calcCostFromWOList(data['workorders'],0);		
	}
	else
	{
		var gallons=calcGallonsFromWOList(data['workorders'],-1);
		var cost=calcCostFromWOList(data['workorders'],-1);		
	}
	var row=$("#destinationLots>table:first tr").filter('[lotid='+data['lotinfo']['LOTNUMBER']+']');
	$(row).find("td").filter('[cellid=startGallons]').text(addCommas(gallons,0));
	$(row).find("td").filter('[cellid=startCost]').text("$"+addCommas(cost/gallons*2.3775,2));
	destinationLotVolume=gallons;
	destinationLotCost=cost;
	if (data['workorders'].length<=1 || data['workorders'][data['workorders'].length-2]['structure']==null)
	{
		var newStructure={"year":undefined,"variety":undefined,"appellation":undefined,"vineyard":undefined};
		for (i in newStructure)
		{
			newStructure[i]=new Object;
		}
		destinationLotStructure=newStructure;
	}
	else
	{
		destinationLotStructure=data['workorders'][data['workorders'].length-2]['structure'];		
	}
	genStructureDiv("destinationStructure",destinationLotStructure);
	$("#destinationStructure").show();
	fieldUpdated();
}
function sourceLotInfoReceived(data)
{
	if (blendWOID=="NEW")
	{
		var gallons=calcGallonsFromWOList(data['workorders'],0);
		var cost=calcCostFromWOList(data['workorders'],0);		
	}
	else
	{
		var gallons=calcGallonsFromWOList(data['workorders'],-1);
		var cost=calcCostFromWOList(data['workorders'],-1);				
	}
	var row=$("#sourceLots>table:first tr").filter('[lotid='+data['lotinfo']['LOTNUMBER']+']');
	$(row).find("td").filter('[cellid=startGallons]').text(addCommas(gallons,0));
	$(row).find("td").filter('[cellid=endGallons]').text(addCommas(gallons,0));
	$(row).find("td").filter('[cellid=costPerGallon]').text('$'+addCommas(cost/gallons*2.3775,2));
	$(row).find("td").filter('[cellid=gallonsOut]').show();
	sourceLotsGallons[data['lotinfo']['LOTNUMBER']]=gallons;
	var myStruct=data['workorders'][data['workorders'].length-1]['structure'];
	var key=data['lotinfo']['LOTNUMBER'];
	sourceStructure[key]=myStruct;
	fieldUpdated();
}
function getGallons(e)
{
	if ($(e.target).attr('checked')==true)
	{
		lotid=findAttr("lotid",e.target);
		if (blendWOID=="NEW")
			$.getJSON(server+"?action=showlotinfo&lot="+lotid+"&detail=YES&clientid="+defaultClient.clientid,"",sourceLotInfoReceived);
		else
			$.getJSON(server+"?action=showlotinfo&lot="+lotid+"&woid="+blendWOID+"&detail=YES&clientid="+defaultClient.clientid,"",sourceLotInfoReceived);
	}
	else
	{
		$(e.target).parents("tr:first").find("td").filter('[cellid=gallonsOut]').text("");
		fieldUpdated();
//		$(e.target).parents("tr:first").find("td").filter('[cellid=gallonsOut]').hide();		
	}
}

function hideUncheckedRows(e)
{
//	$("#sourceBlendingLotsTitle").html("<span class=title>SOURCE LOTS</span>");
	if ($(e.target).attr("checked")==true)
		$("#sourceLots>table>tbody>tr>td>input").filter("[checked=false]").parents("tr").hide();
	else
		$("#sourceLots>table>tbody>tr>td>input").filter("[checked=false]").parents("tr").show();
}
function fieldUpdated()
{
	var rows=$("#sourceLots>table>tbody>tr>td>input").filter("[type=checkbox]").filter("[checked]").parents("tr");
	
	if (rows.length>0)
		$('#blendRecordButton').show();
	else
		$('#blendRecordButton').hide();
	
	cumGallonsOut=0.0;
	cumCostOut=0.0;
//	var updatedStructure=eval(destinationLotStructure.toSource());
	var updatedStructure=copyStructure(destinationLotStructure);
	resultingGallons=0;
	for (i=0; i<rows.length; i++)
	{
		gOut=myParseFloat($(rows[i]).find("td").filter("[cellid=gallonsOut]").text());
		costOut=gOut*myParseFloat($(rows[i]).find("td").filter("[cellid=costPerGallon]").text().replace(/\$|,/g,''))/2.3775;
		cumCostOut=cumCostOut+costOut;
		resultingGallons=sourceLotsGallons[$(rows[i]).attr("lotid")]-gOut;
		$(rows[i]).find("td").filter("[cellid=resultingGallons]").text(resultingGallons);
		cumGallonsOut=cumGallonsOut+gOut;
		var ss=sourceStructure[$(rows[i]).attr("lotid")];
		updatedStructure=combineStructure(updatedStructure,sourceStructure[$(rows[i]).attr("lotid")],gOut);
		$(rows[i]).find("td").filter("[cellid=endGallons]").text(resultingGallons);
	}
	$("#destinationLots>table>tbody>tr:visible").find("td").filter('[cellid=gallons]').text(cumGallonsOut);
	$("#destinationLots>table>tbody>tr:visible").find("td").filter('[cellid=endGallons]').text(destinationLotVolume+cumGallonsOut);
	newCost=cumCostOut/cumGallonsOut*2.3775;
	$("#destinationLots>table>tbody>tr:visible").find("td").filter('[cellid=newCost]').text('$'+addCommas(newCost,2));
	rC=(cumCostOut+destinationLotCost)/(cumGallonsOut+destinationLotVolume)*2.3775;
	$("#destinationLots>table>tbody>tr:visible").find("td").filter('[cellid=resultingCost]').text('$'+addCommas(rC,2));
	
	genStructureDiv("destinationStructure",updatedStructure);
}
function blenderAddLot(e)
{
	// alert("adding lot...");
	$.post(postServer,{ action: "addlot", vintage:defaultVintage, clientid:defaultClient.clientid},addLotComplete);
}

function pickTargetLot(e)
{
	$("#targetBlendLotTitle").text("DESTINATION LOT");
	lotid=findAttr('lotid',e.target);
	$(e.target).parents("tr:first").removeAttr('onclick');
	$(e.target).parents("tr:first").bind('click',blenderAddLot);
	e.stopPropagation();	
	$.getJSON(server+"?action=lotInputToLots&lot="+lotid+"&woid="+blendWOID+"&asOfDate="+$("#blendDate").val(),"",showValidInputLots);
	if (lotid=="new")
	{
		var data={"lotinfo":{"LOTNUMBER":"new"},"workorders":[{"structure":null}]};
		destinationLotInfoReceived(data);
	}
	else
	{
		$.getJSON(server+"?action=showlotinfo&lot="+lotid+"&woid="+blendWOID+"&detail=YES&clientid="+defaultClient.clientid,"",destinationLotInfoReceived);					
	}
}
function showValidInputLots(data)
{
	for (i in data['result'])
	{
		for (j in blendLots)
		{
			if (i==blendLots[j]['LOTNUMBER'])
			{
				blendLots.splice(j,1);
				break;
			}
		}
	}
	for (j in blendLots)
	{
		if (data['inputs']['lot']==blendLots[j]['LOTNUMBER'])
		{
			blendLots.splice(j,1);
			break;
		}
	}
	$('#sourceLots>*').remove();
	
	line='<tr>';
	line=line+'<td class=title>LOT NUMBER</td>';
	line=line+'<td class=title>DESCRIPTION</td>';
	line=line+'<td align=right class=title>CURRENT<br>GALLONS</td>';
	line=line+'<td align=right class=title>GALLONS<br>IN</td>';
	line=line+'<td align=right class=title>RESULTING<br>GALLONS</td>';
	line=line+'<td align=right class=title>CURRENT<br>COST</td>';
	line=line+'<td align=right class=title>COST<br>IN</td>';
	line=line+'<td align=right class=title>RESULTING<br>COST</td>';
	line=line+'</tr>';
	$(line).prependTo("#destinationLots>table");
	
	$('#destinationLots>table tr').hide();
	$('#destinationLots>table tr:first').show();
	$('#destinationLots>table tr').filter('[lotid='+data['inputs']['lot']+']').show();
	
	$('<div id=sourceBlendingLotsTitle style="width:90%; margin-left:auto; margin-right:auto; text-align:center; padding:10px; margin-bottom:10px; margin-top:10px" class="ui-state-highlight ui-corner-all">'+
		'<strong>SELECT THE LOTS YOU WANT TO BLEND OUT FROM</strong><BR><BR>CLICK THIS BOX TO HIDE/SHOW UNUSED LOTS: <input onchange="hideUncheckedRows(event)" type=checkbox></input></div>').appendTo('#sourceLots');
	
	$('<table width=100%></table>').appendTo("#sourceLots");
	line='<tr>';
	line=line+'<td class=title>INCLUDE</td>';
	line=line+'<td class=title>LOT NUMBER</td>';
	line=line+'<td class=title>DESCRIPTION</td>';
	line=line+'<td align=right class=title>CURRENT<br>GALLONS</td>';
	line=line+'<td align=right class=title>GALLONS<br>OUT</td>';
	line=line+'<td align=right class=title>RESULTING<br>GLNS</td>';
	line=line+'<td align=right class=title>COST<br>per CASE</td>';
	line=line+'</tr>';
	$(line).appendTo("#sourceLots>table");
	
	for (i in blendLots)
	{
		line='<tr post=no parentrowid="" lotid='+blendLots[i]['LOTNUMBER']+'>';
		line=line+'<td width=5% cellid=getGallons><input onchange="getGallons(event)" type=checkbox></input></td>';
		line=line+'<td onclick="getBlendTraverserForLot(event)" cellid=lotid width=10%>'+blendLots[i]['LOTNUMBER']+'</td>';
		line=line+'<td cellid=description width=45% cellid>'+blendLots[i]['DESCRIPTION']+'</td>';		
		line=line+'<td align=right width=10% cellid=startGallons></td>';		
		line=line+'<td align=right width=10% class="editable tabable" runOnComplete="fieldUpdated" field=gallonsOut fieldtype=text editsize=5 onclick="makeFieldEditable(event)" cellid=gallonsOut><div></div></td>';		
		line=line+'<td align=right width=10% cellid=endGallons></td>';		
		line=line+'<td align=right width=10% cellid=costPerGallon></td>';		
		line=line+'</tr>';
		$(line).appendTo("#sourceLots>table");
	}
	$('#sourceLots>table tr').find("td").filter('[cellid=gallonsOut]').hide();
	
	if (currentBlendingWO!=null)
	{
		if (currentBlendingWO['data']['blend']!=undefined)
		{
			for (i=0;i<currentBlendingWO['data']['blend'].length;i++)
			{
				var tableRow=$('#sourceLots>table tr').filter('[lotid='+currentBlendingWO['data']['blend'][i]['SOURCELOT']+']');
				$(tableRow).find("td input").trigger('click');
				$(tableRow).find("td").filter('[cellid=gallonsOut]').html('<div>'+myParseFloat(currentBlendingWO['data']['blend'][i]['GALLONS'],0)+'</div>');			
			}			
		}
	}
	fieldUpdated();
}
function showBlender(data)
{
	$('#sourceLots>*').remove();	
	$('#destinationLots>*').remove();	
	$('#destinationMakeup>*').remove();
	if (data['inputs']['woid']=="NEW")
		var currentTime = new Date();
	else
		var currentTime=parse_date(data['blendwo']['data']['DUEDATE']);		
	var month = currentTime.getMonth() + 1;
	var day = currentTime.getDate();
	var year = currentTime.getFullYear();
	$('#blendDate').val(month+'/'+day+'/'+year);		
	$('#blendDate').datepicker();
	$('<div id=targetBlendLotTitle style="width:90%; margin-left:auto; margin-right:auto; text-align:center; padding:10px; margin-bottom:10px; margin-top:10px" class="ui-state-highlight ui-corner-all title">PICK A LOT YOU WANT TO BLEND INTO</div>').appendTo('#destinationLots');
	
	$('<table width=100%></table').appendTo("#destinationLots");
		
	if (data['inputs']['woid']=="NEW")
	{
		line='<tr onclick="pickTargetLot(event)" lotid=new>';
		line=line+'<td height='+trHeight+' align=center width=10% class="ui-icon ui-icon-plus"></td>';
		line=line+'<td width=45%>NEW LOT</td>';		
		line=line+'<td align=right width=5% cellid=currentGallons></td>';		
		line=line+'<td align=right width=5% cellid=gallons></td>';		
		line=line+'<td align=right width=5% cellid=endGallons></td>';		
		line=line+'<td align=right width=5% cellid=startCost></td>';		
		line=line+'<td align=right width=5% cellid=newCost></td>';		
		line=line+'<td align=right width=5% cellid=resultingCost></td>';		
		line=line+'</tr>';		
	}
	$(line).appendTo("#destinationLots>table");
	blendLots=new Array();
	for (var i in data['lots'])
	{
		var lotinfo=data['lots'][i]['lotinfo'];
		blendLots.push(lotinfo);
		line='<tr lotid='+blendLots[i]['LOTNUMBER']+'>';
		line=line+'<td onclick="pickTargetLot(event)" cellid=lotid height='+trHeight+'>'+blendLots[i]['LOTNUMBER']+'</td>';
		line=line+'<td cellid=description>'+blendLots[i]['DESCRIPTION']+'</td>';		
		line=line+'<td align=right cellid=startGallons></td>';		
		line=line+'<td align=right cellid=gallons></td>';		
		line=line+'<td align=right cellid=endGallons></td>';		
		line=line+'<td align=right cellid=startCost></td>';		
		line=line+'<td align=right cellid=newCost></td>';		
		line=line+'<td align=right cellid=resultingCost></td>';		
		line=line+'</tr>';
		$(line).appendTo("#destinationLots>table");
	}
	if (data['inputs']['woid']!="NEW")
	{
		$("#destinationLots>table tr").hide();
		$("#destinationLots>table tr").filter("[lotid="+data['blendwo']['data']['LOT']+"]").show();
		currentBlendingWO=data['blendwo'];
		$("#destinationLots>table tr").filter("[lotid="+data['blendwo']['data']['LOT']+"]").find("td").filter('[cellid=lotid]').trigger('click');
	}	
}

function queryBlender(e)
{
	$('#blendRecordButton').hide();
	$(e.target).parents("table:first").find("tr").hide();
	$(e.target).parents("table:first").find("tr:first").show();
	$(e.target).parents("table:first").find("tr:nth-child(2)").show();
	$(e.target).parents("tr:first").show();
	
	woid=$(e.target).text();
	if (woid=="") woid="NEW";
	
	$('#mainBlenderPanel').show();
	$('#blenderList').hide();
	$('#blender').show();
	
	currentBlendingWO=null;
	
	blendWOID=woid;
	if (woid!="NEW")
	{
		$("#blendWOID").text("BLENDING WO: "+blendWOID);
		$('#blendRecordButton>button').text("UPDATE");
	}
	else
	{
		$("#blendWOID").text("");
		$('#blendRecordButton>button').text("RECORD")
	}
	
	$("#destinationStructure").hide();
	$.getJSON(server+"?action=showLotsForBlending&allActive=1&woid="+blendWOID+"&clientcode="+defaultClient.clientid+"&vintage="+defaultVintage,"",showBlender);
}

