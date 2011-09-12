var blendLots=new Array();
var sourceStructure=new Object;
var destinationLotStructure=new Object;
var sourceLotsGallons=new Array();
var destinationLotCost;
var destinationLotVolume;
var trHeight="20px";
var blendWOID="";
var currentBlendingWO=new Object;
var centerLot;

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
			$('<tr height='+trHeight+' lotid='+result[i]['SUBJECTLOT']+' onclick=getInsAndOuts(event)><td width=60px>'+result[i]['GALLONS']+'</td><td>'+result[i]['SUBJECTLOT']+'</td><td>'+result[i]['DESCRIPTION']+'</td></tr>').appendTo("#outToLots");
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
		{
			$('<tr height='+trHeight+' lotid='+result[i]['SUBJECTLOT']+' onclick=getInsAndOuts(event)><td width=60px>'+result[i]['SUBJECTLOT']+'</td><td>'+result[i]['DESCRIPTION']+'</td><td cellid=gallons>'+result[i]['GALLONS']+'</td></tr>').appendTo("#inFromLots");
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
function genStructureDiv(div,data)
{
	$('#'+div+'>*').remove();
	structure=data;	
	var totalGallons=0;
	if (structure==null)
	{
		structure=new Object;
		structure={"year":null,"variety":null,"appellation":null,"vineyard":null};
		totalGallons=0;
	}
	else
	{
		for (i in structure['year'])
		{
			totalGallons=totalGallons+structure['year'][i];
		}		
	}
	var table='<table width=100%></table>';
	var header='<tr><td align=center class=title>VINTAGE</td><td align=center class=title>VARIETALS</td><td align=center class=title>APPELLATIONS</td><td align=center class=title>VINEYARDS</td></tr>';
	var vintage=getSpecificStructure(structure,"year",totalGallons);
	var varietals=getSpecificStructure(structure,"variety",totalGallons);
	var appellations=getSpecificStructure(structure,"appellation",totalGallons);
	var vineyards=getSpecificStructure(structure,"vineyard",totalGallons);
	var structureline='<tr><td>'+vintage+'</td><td>'+varietals+'</td><td>'+appellations+'</td><td>'+vineyards+'</td></tr>';
	
	$('<div style="width:90%; margin-left:auto; margin-right:auto; text-align:center; padding:10px; margin-bottom:10px; margin-top:10px" class="ui-state-highlight ui-corner-all title">LOT STRUCTURE (<span id=lotGallons></span> Gallons)</div>').appendTo('#'+div);
	
	$(table).appendTo('#'+div);
	$(header).appendTo('#'+div+'>table');
	$(structureline).appendTo('#'+div+'>table');
	$("#lotGallons").text(totalGallons);
//	$('#blenderList>table tr:last').show();
}
function getInsAndOuts(e)
{
	lotid=findAttr("lotid",e.target);
	$("#subjectLot tr").hide();
	if ($("#subjectLot tr").filter("[lotid="+lotid+"]").length>0)
		$("#subjectLot tr").filter("[lotid="+lotid+"]").show();
	else
	{
		var lotnumber=$(e.target).parents("tr:first").find("td:first").text();
		var description=$(e.target).parents("tr:first").find("td:nth-child(2)").text();
		$('<tr onclick="getInsAndOuts(event)" lotid='+lotnumber+'><td height='+trHeight+'>'+lotnumber+'</td><td>'+description+'</td></tr>').appendTo('#subjectLot');		
	}
	$.getJSON(server+"?action=lotMadeUpOfLots&lot="+lotid+"&asOfDate="+$("#blendDate").val(),"",showInputLots);
	$.getJSON(server+"?action=lotInputToLots&lot="+lotid+"&asOfDate="+$("#blendDate").val(),"",showOutputLots);
	$.getJSON(server+"?action=showlotinfo&clientid="+defaultClient.clientid+"&lot="+lotid,"",updateStructure);					
}

function setUpBlendTraverser()
{
	$('#blenderList>*').remove();
	var currentTime = new Date();
	var month = currentTime.getMonth() + 1;
	var day = currentTime.getDate();
	var year = currentTime.getFullYear();
	$('#blendDate').val(month+'/'+day+'/'+year);
	$('#blendDate').datepicker();
//	$('#blendDate').attr("refreshRoutine","getBlendTraverserForLotDueToDateChange()");
	// $('#blendDate').datepicker({
	//    onChangeMonthYear: function(year, month, inst) { 
	// 	getBlendTraverserForLotDueToDateChange(); 
	// 	}
	// });
//	$('#blendDate').bind('change',"getBlendTraverserForLotDueToDateChange()");
	$('<table width=90% align=center></table>').appendTo("#blenderList");
	line='<tr valign=center>';
	line=line+'<td width=33%><div class="ui-state-default ui-corner-all">';
		line=line+'<div style="width:90%; text-align:center; margin-top:10px; margin-bottom:10px; margin-left:auto; margin-right:auto; padding:10x" class="ui-state-highlight ui-corner-all">INCOMING LOTS</div>';
		line=line+'<div><table id=inFromLots width=100%></table></div></td>';
	line=line+'<td width=33%><div class="ui-state-default ui-corner-all">';
		line=line+'<div style="width:90%; text-align:center; margin-top:10px; margin-bottom:10px; margin-left:auto; margin-right:auto; padding:10x" class="ui-state-highlight ui-corner-all">PRIMARY LOT</div>';
		line=line+'<div><table id=subjectLot width=100%></table></div></td>';
	line=line+'<td eidth=33%><div class="ui-state-default ui-corner-all">';
		line=line+'<div style="width:90%; text-align:center; margin-top:10px; margin-bottom:10px; margin-left:auto; margin-right:auto; padding:10x" class="ui-state-highlight ui-corner-all">RECEIVING LOTS</div>';
		line=line+'<div><table id=outToLots width=100%></table></div></td>';
	line=line+'</tr>';
	$(line).appendTo("#blenderList>table");
	line='<div class="ui-state-default ui-corner-all structure" id=structure></div>';
	$(line).appendTo("#blenderList");
	$("#structure").hide();
}

function showBlendTraverserForLot(data)
{
	setUpBlendTraverser();
	for (var i in data['lots'])
	{
		var lotinfo=data['lots'][i]['lotinfo'];
		$('<tr onclick="getInsAndOuts(event)" lotid='+lotinfo['LOTNUMBER']+'><td height='+trHeight+'>'+lotinfo['LOTNUMBER']+'</td><td>'+lotinfo['DESCRIPTION']+'</td></tr>').appendTo('#subjectLot');
	}
	$("#subjectLot tr").hide();
	$("#subjectLot tr").filter('[lotid='+data['inputs']['forLot']+']').show();
	$("#subjectLot tr").filter('[lotid='+data['inputs']['forLot']+']').trigger('click');
}
function getBlendTraverserForLotDueToDateChange()
{
	$.getJSON(server+"?action=showlots&allActive=0&forLot="+centerLot+"&clientcode="+defaultClient.clientid+"&vintage="+defaultVintage,"",showBlendTraverserForLot);
}
function getBlendTraverserForLot(e)
{
	$("#blenderList").show();
	centerLot=$(e.target).text();
	$.getJSON(server+"?action=showlots&allActive=0&forLot="+$(e.target).text()+"&clientcode="+defaultClient.clientid+"&vintage="+defaultVintage,"",showBlendTraverserForLot);
}
function showBlendTraverser(data)
{
	setUpBlendTraverser();
	for (var i in data['lots'])
	{
		var lotinfo=data['lots'][i]['lotinfo'];
		$('<tr onclick="getInsAndOuts(event)" lotid='+lotinfo['LOTNUMBER']+'><td width=60px height='+trHeight+'>'+lotinfo['LOTNUMBER']+'</td><td>'+lotinfo['DESCRIPTION']+'</td></tr>').appendTo('#subjectLot');
	}
}
function queryBlendTraverser()
{
	 $.getJSON(server+"?action=showlots&allActive=1&clientcode="+defaultClient.clientid+"&vintage="+defaultVintage,"",showBlendTraverser);
}

