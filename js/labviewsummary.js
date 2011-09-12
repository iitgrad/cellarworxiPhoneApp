function mso2(with_ph,with_fso2)
{
	var fso2=myParseFloat(with_fso2);
	var pH=myParseFloat(with_ph);
	if (fso2==0 | pH==0)
	{
		return "";					
	}
	else
	{
		var pK=1.81;
		var value=fso2/(Math.pow(10,pH-pK)+1);
		
		return Math.round(value*Math.pow(10,2))/Math.pow(10,2);					
	}
}
function labViewSummaryLine(i,data)
{
	var line="<tr class=ui-widget>";
	line=line+'<td onClick="gotoLotDetail(\''+defaultClient.clientid+'\','+
												'\''+defaultClient.clientcode+'\','+
												'\''+defaultClient.theName+'\','+
												'\''+i+'\''+
												')">'+i+'</td>';
	line=line+'<td>'+data['DESCRIPTION']+'</td>';
	
	line=line+'<td align=right class=labResultsDetail>'+labTestNumber('pH',data['pH'])+'</td>';
	line=line+'<td align=right class=labResultsDetail>'+labTestNumber('TA',data['TA'])+'</td>';
	line=line+'<td align=right class=labResultsDetail>'+labTestNumber('MSO2',mso2(data['pH'],data['FSO2']))+'</td>';
	line=line+'<td align=right class=labResultsDetail>'+labTestNumber('FSO2',data['FSO2'])+'</td>';
	line=line+'<td align=right class=labResultsDetail>'+labTestNumber('TSO2',data['TSO2'])+'</td>';
	line=line+'<td align=right class=labResultsDetail>'+labTestNumber('ALCOHOL',data['ALCOHOL'])+'</td>';
	line=line+'<td align=right class=labResultsDetail>'+labTestNumber('Glu/Fru',data['Glu/Fru'])+'</td>';
	line=line+'<td align=right class=labResultsDetail>'+labTestNumber('MALIC_ACID',data['MALIC_ACID'])+'</td>';
	line=line+'<td align=right class=labResultsDetail>'+labTestNumber('VA',data['VA'])+'</td>';
	line=line+'<td align=right class=labResultsDetail>'+labTestNumber('4EP',data['4EP'])+'</td>';
	line=line+'<td align=right class=labResultsDetail>'+labTestNumber('4EG',data['4EG'])+'</td>';
	
	line=line+'<td align=right class=juicePanelDetail>'+labTestNumber('JPBRIX',data['JPBRIX'])+'</td>';
	line=line+'<td align=right class=juicePanelDetail>'+labTestNumber('JPTARTARIC',data['JPTARTARIC'])+'</td>';
	line=line+'<td align=right class=juicePanelDetail>'+labTestNumber('JPALPHA',data['JPALPHA'])+'</td>';
	line=line+'<td align=right class=juicePanelDetail>'+labTestNumber('JPAMMONIA',data['JPAMMONIA'])+'</td>';
	line=line+'<td align=right class=juicePanelDetail>'+labTestNumber('JPYEAST',data['JPYEAST'])+'</td>';
	line=line+'<td align=right class=juicePanelDetail>'+labTestNumber('JPPOT',data['JPPOT'])+'</td>';
	line=line+'<td align=right class=juicePanelDetail>'+labTestNumber('JPBUFFER',data['JPBUFFER'])+'</td>';
	
	line=line+'<td align=right class=scorpionDetail>'+labTestNumber('LACTO',data['LACTO'])+'</td>';
	line=line+'<td align=right class=scorpionDetail>'+labTestNumber('PEDIO',data['PEDIO'])+'</td>';
	line=line+'<td align=right class=scorpionDetail>'+labTestNumber('ACETO',data['ACETO'])+'</td>';
	line=line+'<td align=right class=scorpionDetail>'+labTestNumber('BRETT',data['BRETT'])+'</td>';
	line=line+'<td align=right class=scorpionDetail>'+labTestNumber('ZYGO',data['ZYGO'])+'</td>';
	line=line+'</tr>';
	return line;
}
function showLabViewSummary(data)
{
	
	$('#labViewSummaryList>*').remove();
	
	var header="<tr class=ui-widget>";
	var line="";
	header=header+'<td class="header labResultsDetail filterable" align=left width=50>Lot Number</td>';	
	header=header+'<td class="header labResultsDetail filterable" align=left width=300>Description</td>';	

	header=header+'<td title="DETAILED LAB RESULTS" expandClass="labResultsDetail" class="header labResultsDetail elidable" egroup=1 align=left width=25>pH</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=1 align=left width=25>TA<br>(g/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=1 align=left width=25>MSO2<br>(mg/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=1 align=left width=25>FSO2<br>(mg/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=1 align=left width=25>TSO2<br>(mg/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=1 align=left width=25>ETHANOL<br>(% Vol.)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=1 align=left width=25>GLU/FRU<br>(g/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=1 align=left width=25>MALIC<br>(g/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=1 align=left width=25>VA<br>(g/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=1 align=left width=25>4EP<br>(ug/L)</td>';	
	header=header+'<td class="header labResultsDetail elidable" egroup=1 align=left width=25>4EG<br>(ug/L)</td>';	
	
	header=header+'<td title="JUICE PANEL" expandClass="juicePanelDetail" class="header juicePanelDetail elidable" egroup=2 align=left width=70>Brix<br>(degrees)</td>';
	header=header+'<td class="header juicePanelDetail elidable" egroup=2 align=left width=70>Tartaric acid<br>(g/L)</td>';
	header=header+'<td class="header juicePanelDetail elidable" egroup=2 align=left width=70>Alpha-amino compounds<br>(mg/L)</td>';
	header=header+'<td class="header juicePanelDetail elidable" egroup=2 align=left width=70>Ammonia<br>(mg/L)</td>';
	header=header+'<td class="header juicePanelDetail elidable" egroup=2 align=left width=70>Yeast assimable nitrogen<br>(mg/L)</td>';
	header=header+'<td class="header juicePanelDetail elidable" egroup=2 align=left width=70>Potassium<br>(mg/L)</td>';
	header=header+'<td class="header juicePanelDetail elidable" egroup=2 align=left width=70>Buffer capacity<br>(mM/pH)</td>';
	
	header=header+'<td title="SCORPION" expandClass="scorpionDetail" class="header scorpionDetail elidable" egroup=3 align=left width=35>Lacto</td>';
	header=header+'<td class="header scorpionDetail elidable" egroup=3 align=left width=35>Pedio</td>';
	header=header+'<td class="header scorpionDetail elidable" egroup=3 align=left width=35>Aceto</td>';
	header=header+'<td class="header scorpionDetail elidable" egroup=3 align=left width=35>Brett</td>';
	header=header+'<td class="header scorpionDetail elidable" egroup=3 align=left width=35>Zygo</td>';
	header=header+'</tr>';
	for (i in data)
	{
		line=line+labViewSummaryLine(i,data[i]);
	}
	$('<table width=90% align=center tableName=labViewSummary>').appendTo("#labViewSummaryList");
	$(header).appendTo("#labViewSummaryList>table");
	$(line).appendTo("#labViewSummaryList>table");

	$('#labViewSummaryList .filterable').click(function (){
		filterColumn(this,1,0);
	});
	
	makeTableElidable("#labViewSummaryList>table");
	
	$('#labViewSummaryList>table>tbody>tr').mouseover(function (){
		$(this).addClass('ui-state-highlight');
		});
	$('#labViewSummaryList>table>tbody>tr').mouseout(function (){
		$(this).removeClass('ui-state-highlight');
		});
	
}
function queryLabViewSummary()
{
	 $.getJSON(server+"?action=labviewsummary&clientid="+defaultClient.clientid+"&vintage="+defaultVintage,"",showLabViewSummary);
}

