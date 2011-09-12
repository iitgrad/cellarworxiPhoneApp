jQuery.fn.swap = function(b){ 
    b = jQuery(b)[0]; 
    var a = this[0]; 
    var t = a.parentNode.insertBefore(document.createTextNode(''), a); 
    b.parentNode.insertBefore(a, b); 
    t.parentNode.insertBefore(b, t); 
    t.parentNode.removeChild(t); 
    return this; 
};

var labTestDecimals={"pH":2,
	"TA":1,
	"FSO2":0,
	"MSO2":2,
	"TSO2":0,
	"ALCOHOL":1,
	"Glu/Fru":1,
	"MALIC_ACID":2,
	"VA":2,
	"4EP":0,
	"4EG":0,
	"JPTARTARIC":2,
	"JPAMMONIA":0,
	"JPBRIX":1,
	"TARTARIC":2,
	"JPALPHA":0,
	"JPYEAST":0,
	"JPPOT":0,
	"LACTO":0,
	"PEDIO":0,
	"ACETO":0,
	"BRETT":0,
	"ZYGO":0,
	"JPBUFFER":1
}

function setBreadCrumb(data,link,attributes,depth)
{
	breadCrumbs.splice(depth-1);	
	var theData=new Object();
	theData['data']=data;
	theData['link']=link;
	theData['attributes']=attributes
	breadCrumbs[breadCrumbs.length]=theData;
}
function pushBreadCrumb(data,link,attributes)
{
	var theData=new Object();
	theData['data']=data;
	theData['link']=link;
	theData['attributes']=attributes
	breadCrumbs[breadCrumbs.length]=theData;
}
function popBreadCrumbs(count)
{
	breadCrumbs.splice((breadCrumbs.length-count)-1);
}
function clearBreadCrumbs()
{
	breadCrumbs=new Array();
}
function showBreadCrumbs()
{
	var line='';
	if (breadCrumbs.length>0)
	{
		var line='<div onclick="executeBreadCrumbLink(event)"'+" theLevel=0 "+breadCrumbs[0]['attributes']+' class=breadCrumb style="margin-left:20px; z-index:'+breadCrumbs.length+'">'+breadCrumbs[0]['data']+'</div>';		
		for (i=1; i<breadCrumbs.length; i++)
		{
			line=line+'<div onclick="executeBreadCrumbLink(event)"'+" theLevel="+i+" "+breadCrumbs[i]['attributes']+' class=breadCrumb style="margin-left:-15px; z-index:'+(breadCrumbs.length-i)+'; padding-left:20px">'+breadCrumbs[i]['data']+'</div>'
		}
	}
	return line;
}
function executeBreadCrumbLink(e)
{
	var parentDiv=$(e.target);	
	var level=$(parentDiv).attr("theLevel");
	var popLevel=(breadCrumbs.length-1)-level;
	var routineToCall=new Function(breadCrumbs[level]['link']);
	popBreadCrumbs(popLevel);
	
	routineToCall();
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
	
	$('<div style="width:90%; margin-left:auto; margin-right:auto; text-align:center; padding:10px; margin-bottom:10px; margin-top:10px" class="ui-state-highlight ui-corner-all title">LOT STRUCTURE</div>').appendTo('#'+div);
	
	$(table).appendTo('#'+div);
	$(header).appendTo('#'+div+'>table');
	$(structureline).appendTo('#'+div+'>table');
//	$('#blenderList>table tr:last').show();
}

function labTestNumber(test,text)
{
	if (text=="") return "";
	if (text==undefined) return "";
	var decimals=labTestDecimals[test];
	var value=myParseFloat(text);
	if (value<0) return "";
	return Math.round(value*Math.pow(10,decimals))/Math.pow(10,decimals);
}

function blankIfNull(text)
{
	if (text==null)
		return "";
	return text;
}

function getFloat(fromText)
{
	if (isNaN($(cell1).text().replace(/\$|,/g,'')))
		return 0;
	else
		return parseFloat(fromText.replace(/\$|,/g,''));
}

function setClientVintage(clientname,clientid,vintage)
{
	defaultClient.theName=clientname;
	defaultClient.clientid=clientid;
	eraseCookie("clientname");
	eraseCookie("clientid");
	createCookie("clientname",defaultClient.theName);
	createCookie("clientid",defaultClient.clientid);
	updateHeaderClientVintage();
	showVintagesForClient(data);
}
function findAttr(attr, t)
{
	if ($(t).attr(attr)!=undefined)
		return $(t).attr(attr);
	else
		if ($(t).parent().length==0)
			return "";
	return findAttr(attr,$(t).parent())
}
function findCellID(id, target)
{
	theRow=$(target).parents("tr:first");
	cellValue=$(theRow).find("td").filter('[cellid='+id+']').text();
	return cellValue;
}

function setCheckedCellID(id, inRow)
{
	$(inRow).find("td").filter('[cellid='+id+']').find("input").attr("checked",true);	
}
function updateCellID(id, value, inRow)
{
	$(inRow).find("td").filter('[cellid='+id+']').text(value);
}

function sumColumn(e,colnumber,startrow,endrow)
{
	var total=0;
	var table=$(e.target).parents('table:first');
	var rowCount=$(table).find('tbody>tr').length;
	for (var i=startrow;i<=rowCount-1+endrow;i++)
	{
		var cell=$($(table).find('tbody>tr')[i]).children('td')[colnumber];
		if ($(cell).is(":visible"))
		{
			total=total+getFloat($(cell).text());
		}
	}
	var cellToUpdate=$($(table).find('tbody>tr')[rowCount-1]).children('td')[colnumber];
	if ($(cellToUpdate).attr('numberFormat')=="cost")
		$(cellToUpdate).text('$'+addCommas(total,$(cellToUpdate).attr('decimals')));
	else
		$(cellToUpdate).text(addCommas(total,$(cellToUpdate).attr('decimals')));
}

function doSum(e,colnumber,startrow,endrow)
{
	var headerCells=$(e.target).parents("tr:first").find("td");
	for (var i=0;i<=headerCells.length-1;i++)
	{
		if ($(headerCells[i]).hasClass("summable"))
			sumColumn(e,i,startrow,endrow);
	}
}

function doFilter(e,colnumber,startrow,endrow)
{
	var table=$(e.target).parents('table:first');
	var rowCount=$(table).find('tbody>tr').length;
	for (var i=startrow;i<=rowCount-1+endrow;i++)
	{
		cell1=$($($(table).find('tbody>tr')[i]).children('td')[colnumber]).text();
		if (cell1.toUpperCase().indexOf($(e.target).val().toUpperCase())>=0)
			$($(table).find('tbody>tr')[i]).show();
		else
			$($(table).find('tbody>tr')[i]).hide();
	}
	doSum(e,colnumber,startrow,endrow);
}

function closeFilter(e)
{
	var table=$(e.target).parents('table:first');
	$(table).find("tr").show();
	$(e.target).parents("td:first").find('div').remove();
	e.stopPropagation();	
}

function filterColumn(t,omitHeaderRowCount,omitFooterRowCount)
{		
	$(t).find("div").remove();
	var line='<div><div style="float:left"><input onChange="doFilter(event,'+$(t).index()+','+omitHeaderRowCount+','+omitFooterRowCount+')" type=text size=10></input></div>';
	line=line+'<div onClick="closeFilter(event)" style="float:left" class="ui-icon ui-icon-close"></div></div>';
	$(line).appendTo(t);
	$(t).find("input").focus();
}

function sortColumn(t,omitHeaderRowCount,omitFooterRowCount)
{
	if ($(t).attr('sortDirection')=="none")
		$(t).attr('sortDirection','ascending');
	else if ($(t).attr('sortDirection')=="ascending")
		$(t).attr('sortDirection','descending');
	else if	($(t).attr('sortDirection')=="descending")
		$(t).attr('sortDirection','none');
		
	$(t).find('div').removeClass('ui-icon-arrowstop-1-n');
	$(t).find('div').removeClass('ui-icon-arrowstop-1-s');
	$(t).find('div').removeClass('ui-icon-grip-dotted-horizontal');
	if ($(t).attr('sortDirection')=="ascending")
		$(t).find('div').addClass('ui-icon-arrowstop-1-s');	
	else if ($(t).attr('sortDirection')=="descending")
		$(t).find('div').addClass('ui-icon-arrowstop-1-n');
	else
		$(t).find('div').addClass('ui-icon-grip-dotted-horizontal');
	sort($(t).parents('table:first'),$(t).index(),omitHeaderRowCount,omitFooterRowCount,$(t).attr('sortDirection'));
}

function sort(table,colnumber,startrow,endrow,direction)
{	
	var rowCount=$(table).find('tbody>tr').length;
	for (var i=startrow;i<=rowCount-2+endrow;i++)
	{
		for (var j=i+1;j<=rowCount-1+endrow;j++)
		{
			cell1=$($(table).find('tbody>tr')[i]).children('td')[colnumber];
			cell2=$($(table).find('tbody>tr')[j]).children('td')[colnumber];
			
			if (isNaN($(cell1).text().replace(/\$|,/g,'')))
				item1=$(cell1).text();
			else
				item1=parseFloat($(cell1).text().replace(/\$|,/g,''));
			if (isNaN($(cell2).text().replace(/\$|,/g,'')))
				item2=$(cell2).text();
			else
				item2=parseFloat($(cell2).text().replace(/\$|,/g,''));
			
			if (direction=="ascending")
				if (item1>item2)
					$($(cell1).parents('tr:first')).swap($(cell2).parents('tr:first'));
			if (direction=="descending")
				if (item1<item2)
					$($(cell1).parents('tr:first')).swap($(cell2).parents('tr:first'));
		}
	}
}
function captureKeystroke(target,makeEditable, tabClass)
{
	$(target).keydown(function(e){
		var parentTD=$(e.target).parents("td");
		if (e.keyCode=='9') //tab key
		{
			e.stopImmediatePropagation();
			e.preventDefault();
			var allElements=$(".tabable:visible");
			var numberOfVisibleElements=allElements.length;
			var currentIndex=$(parentTD).index(".tabable:visible");
			if (e.shiftKey)
			{
				if (currentIndex==0)
					currentIndex=numberOfVisibleElements-1;
				else
					currentIndex--;
			}
			else
				currentIndex=(currentIndex+1) % numberOfVisibleElements;
			if (hasChanged==1)
				$(e.target).trigger('change');
			else
			{
				$(parentTD).html("<div>"+$(parentTD).attr("originalText")+"</div>");
				$(parentTD).click(makeEditable);				
			}
			$(allElements[currentIndex]).trigger('click');	
			return;		
		}
		if (e.keyCode=='27')  //escape key
		{
			if ($(parentTD).attr("fieldtype")=="textarea")
				$(parentTD).html("<div><pre>"+$(parentTD).attr("originalText")+"</pre></div>");
			else
				$(parentTD).html("<div>"+$(parentTD).attr("originalText")+"</div>");
			$(parentTD).click(makeEditable);
			return;			
		}
		if (e.keyCode=='13')  //enter key
		{
//			$(e.target).trigger('change');
			return;		
		}
		if (e.keyCode!=16)
			hasChanged=1;
	})	
}


function makeFieldEditable(e,theData)
{
	if ($(e.target).filter("td").length==1)
		var parentTD=$(e.target);
	else
		var parentTD=$(e.target).parents("td");
//	$(parentTD).addClass("inEditMode");
	var value=$(e.target).text();
	var field=findAttr("field",e.target);
	var editsize=findAttr("editsize",e.target);
	var fieldtype=findAttr("fieldtype",e.target);
	$(parentTD).attr("originalText",$(parentTD).text());
	$(parentTD).removeAttr("onclick");
	$(parentTD).unbind("click");
	var theID=uid();
	if (fieldtype=="date")
	{
		var line='<div field="'+field+'" fieldtype="'+fieldtype+'"><input id='+theID+' onchange=updateFieldOnServer(event) type=text value="'+value+'"></input></div>';
		$(parentTD).html(line);
		$("#"+theID).datepicker();	
	} else if (fieldtype=="text")
	{
		// var line='<div field="'+field+'" fieldtype="'+fieldtype+'"><input onchange=updateFieldOnServer(event) type=text value="'+value+'" size="'+editsize+'"></input></div>';
		var line='<input id='+theID+' onchange=updateFieldOnServer(event) type=text value="'+value+'" size="'+editsize+'"></input>';
		$(parentTD).html(line);
		$(parentTD).find("input").focus();
		$(parentTD).find("input").select();
		captureKeystroke($(parentTD).find("input"),makeFieldEditable, ".inventoryDetail")
	} else if (fieldtype=="textarea")
	{
		var textsize=Math.round($(parentTD).get(0).offsetHeight/15)+1;
		var line='<div field="'+field+'" fieldtype="'+fieldtype+'"><textarea  id='+theID+' style="width:100%" onchange=updateFieldOnServer(event) type=textarea rows="'+textsize+'">'+value+'</textarea></div>';
		$(parentTD).find("input").focus();
		$(parentTD).html(line);
		captureKeystroke($(parentTD).find("textarea"),makeFieldEditable, ".inventoryDetail")
	} else if (fieldtype=="select")
	{
		var choice=$(parentTD).text();
		$(parentTD).html($(parentTD).attr("selectHTML"));
		$(parentTD).find("select").val(choice);
	}
}
function todayDate()
{
	var currentTime = new Date()
	var month = currentTime.getMonth() + 1
	var day = currentTime.getDate()
	var year = currentTime.getFullYear()
	return (month + "/" + day + "/" + year)
}	

function updateFieldOnServer(e)
{
	if ($(e.target).filter("td").length==1)
		var parentTD=$(e.target);
	else
		var parentTD=$(e.target).parents("td:first");
	$(".inEditMode").removeClass("inEditMode");
	var rowid=findAttr("rowid",e.target);	
	var table=findAttr("table",e.target);
	var refresh=findAttr("refresh",e.target);
	var action=findAttr("action",e.target);
	var refreshRoutine=findAttr("refreshRoutine",e.target);
	var parentrowid=findAttr("parentrowid",e.target);
	var elementid=uid();
	$(parentTD).attr("id",elementid)
	var field=findAttr("field",e.target);
	var fieldtype=findAttr("fieldtype",e.target);
	if (fieldtype=="date" | fieldtype=="text" | fieldtype=="textarea" | fieldtype=="select")
		var value=$(e.target).val();
	else if (fieldtype=="checkbox")
	{
		if ($(e.target).attr("checked"))
			var value="YES";
		else
			var value="NO";
	}
	else
		var value=$(e.target).text();
	$(e.target).attr('onChange',"");
	if (fieldtype!="checkbox")
	{
//		$(parentTD).find("div").remove();
	    $(parentTD).html('<div field="'+field+'" fieldtype="'+fieldtype+ '">...</div>');		
//		$(parentTD).text('...');		
	}
	$(parentTD).click(makeFieldEditable);
	if (findAttr("post",parentTD)=="no")
	{
		var jsonData={"field":field,"fieldtype":fieldtype,"value":value, "parentrowid":parentrowid, "elementid":elementid};
		updateFieldOnServerComplete(JSON.stringify(jsonData));		
	}
	else
	{
		$.post(postServer,{ elementid:elementid, action:action, table:table, refresh:refresh, refreshRoutine:refreshRoutine, fieldtype:fieldtype, field:field, rowid:rowid, value:value, parentrowid:parentrowid},updateFieldOnServerComplete);		
	}
}
function copyStructure(s)
{
	var dupStructure=new Object;
	if (typeof(s)=="object")
	{
		for (i in s)
		{
			dupStructure[i]=copyStructure(s[i]);
		}		
	}
	else
		dupStructure=s;
	return dupStructure;
}

function updateFieldOnServerComplete(data)
{
	var result = JSON.parse(data);
	if (result['parentrowid']=="")
	{
		if (result['fieldtype']!="checkbox")
		{
			// if (result['fieldtype']=="textarea")
			// 	$("#"+result['elementid']).html('<div field="'+result['field']+'" fieldtype="'+result['fieldtype']+'"><pre>'+result['value']+'</pre></div>');
			// else
			// 	$("#"+result['elementid']).html('<div field="'+result['field']+'" fieldtype="'+result['fieldtype']+'">'+result['value']+'</div>');				
			if (result['fieldtype']=="textarea")
				$("#"+result['elementid']).html('<div><pre>'+result['value']+'</pre></div>');
			else
				$("#"+result['elementid']).html('<div>'+result['value']+'</div>');				
			// if (result['fieldtype']=="textarea")
			// 	$("#"+result['elementid']).text(result['value']);
			// else
			// 	$("#"+result['elementid']).text(result['value']);				
			//$("#"+result['elementid']).click(makeFieldEditable);					
		}
	}	
	else
	{
		for (i in lotDetailData['workorders'])
		{
			if (lotDetailData['workorders'][i]['data']['ID']==result['thedata']['data']['ID'])
			{
				lotDetailData['workorders'][i]['data']=result['thedata']['data'];
				addWOLine(i);
			}
		}		
	}
	if (result['refresh']=="YES")
	{
		var refreshRoutine=result['refreshRoutine'];
		eval(refreshRoutine+"()");
//		refreshLotDetail();
	}
	if ($("#"+result['elementid']).attr("runOnComplete")!==undefined)
	{
		var routine=$("#"+result['elementid']).attr("runOnComplete");
		window[routine](result);
	}
}
function client(name,clientcode, clientid)
{
	this.theName=name;
	this.clientcode=clientcode;
	this.clientid=clientid;
}
function parse_date(timestamp) {  
    var date = new Date();  
	// date.setDate(1);
	//     var parts = String(string).split(/[- :]/);  
	// 
	//     date.setFullYear(parseInt(parts[0])); 
	//     date.setMonth(parseInt(parts[1])-1);  
	//     date.setDate(parseInt(parts[2])); 
	//  	if (parts.length>3)
	// {
	//     date.setHours(parseInt(parts[3]));  
	//     date.setMinutes(parseInt(parts[4]));  
	//     date.setSeconds(parseInt(parts[5]));  
	//     date.setMilliseconds(0);  	
	// }
	// var date = new Date();  
	//     var parts = String(string).split(/[- :]/);  

    // date.setFullYear(parts[0]);  
    //  date.setMonth(parts[1] - 1);  
    //  date.setDate(parts[2]);  
    //  date.setHours(parts[3]);  
    //  date.setMinutes(parts[4]);  
    //  date.setSeconds(parts[5]);  
    //  date.setMilliseconds(0);  
 	var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
	    var parts=timestamp.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
	    return new Date(parts[0],parts[1]-1,parts[2],parts[3],parts[4],parts[5]);

 //   return date;  
};
function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}
function addCommas(nStr,decimals)
{
	if (decimals==undefined)
		decimals=0;
	nStr=(Math.round(myParseFloat(nStr)*Math.pow(10,decimals))/Math.pow(10,decimals)).toString();
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function buildSelect(data,type)
{
	var line;
	line='<select onchange=updateFieldOnServer(event)>';
	for (i in data)
	{
		if (i==type)
			line=line+'<option value=&#34'+data[i]+'&#34 selected>'+i+'</option>';
		else
			line=line+'<option value=&#34'+data[i]+'&#34>'+i+'</option>';
	}
	line=line+'</select>';
	return line;
}

function uid()
{
	var name="id" + Math.random() * Math.pow(10, 17) + Math.random() * Math.pow(10, 17) + Math.random() * Math.pow(10, 17);
//	alert (name.replace(".",""));
	return name.replace(".","");
}
function yesNoChecked(val)
{
	if (val=="YES")
		return "checked";
	return "";
}

function showSave()
{
	$('#saveButton').hide();
	$('#loading').show();
}

function calcGallons(data)
{
	return (myParseFloat(data['ending_tankgallons'])+
							(myParseFloat(data['ending_bbls'])*60.0)+
							myParseFloat(data['ending_toppinggallons']));
}

function myParseFloat(str)
{
	if (isNaN(parseFloat(str)))
	 return 0.0;
	else
	 return parseFloat(str);
}

function changeCompleteStatus(e)
{
	var woid=findAttr("rowid",e);
	var checked=findAttr("checked",e);
	$.post(postServer,{ action: "update_wo_status", woid:woid, complete:checked});	
}

function dateString(theDate)
{
	var month = theDate.getMonth() + 1
	var day = theDate.getDate()
	var year = theDate.getFullYear()
	var dateS=month + "/" + day + "/" + year;
	return dateS;
}
		
/**
*
*  MD5 (Message-Digest Algorithm)
*  http://www.webtoolkit.info/
*
**/
 
var MD5 = function (string) {
 
	function RotateLeft(lValue, iShiftBits) {
		return (lValue<<iShiftBits) | (lValue>>>(32-iShiftBits));
	}
 
	function AddUnsigned(lX,lY) {
		var lX4,lY4,lX8,lY8,lResult;
		lX8 = (lX & 0x80000000);
		lY8 = (lY & 0x80000000);
		lX4 = (lX & 0x40000000);
		lY4 = (lY & 0x40000000);
		lResult = (lX & 0x3FFFFFFF)+(lY & 0x3FFFFFFF);
		if (lX4 & lY4) {
			return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
		}
		if (lX4 | lY4) {
			if (lResult & 0x40000000) {
				return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
			} else {
				return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
			}
		} else {
			return (lResult ^ lX8 ^ lY8);
		}
 	}
 
 	function F(x,y,z) { return (x & y) | ((~x) & z); }
 	function G(x,y,z) { return (x & z) | (y & (~z)); }
 	function H(x,y,z) { return (x ^ y ^ z); }
	function I(x,y,z) { return (y ^ (x | (~z))); }
 
	function FF(a,b,c,d,x,s,ac) {
		a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
		return AddUnsigned(RotateLeft(a, s), b);
	};
 
	function GG(a,b,c,d,x,s,ac) {
		a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
		return AddUnsigned(RotateLeft(a, s), b);
	};
 
	function HH(a,b,c,d,x,s,ac) {
		a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
		return AddUnsigned(RotateLeft(a, s), b);
	};
 
	function II(a,b,c,d,x,s,ac) {
		a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
		return AddUnsigned(RotateLeft(a, s), b);
	};
 
	function ConvertToWordArray(string) {
		var lWordCount;
		var lMessageLength = string.length;
		var lNumberOfWords_temp1=lMessageLength + 8;
		var lNumberOfWords_temp2=(lNumberOfWords_temp1-(lNumberOfWords_temp1 % 64))/64;
		var lNumberOfWords = (lNumberOfWords_temp2+1)*16;
		var lWordArray=Array(lNumberOfWords-1);
		var lBytePosition = 0;
		var lByteCount = 0;
		while ( lByteCount < lMessageLength ) {
			lWordCount = (lByteCount-(lByteCount % 4))/4;
			lBytePosition = (lByteCount % 4)*8;
			lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount)<<lBytePosition));
			lByteCount++;
		}
		lWordCount = (lByteCount-(lByteCount % 4))/4;
		lBytePosition = (lByteCount % 4)*8;
		lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80<<lBytePosition);
		lWordArray[lNumberOfWords-2] = lMessageLength<<3;
		lWordArray[lNumberOfWords-1] = lMessageLength>>>29;
		return lWordArray;
	};
 
	function WordToHex(lValue) {
		var WordToHexValue="",WordToHexValue_temp="",lByte,lCount;
		for (lCount = 0;lCount<=3;lCount++) {
			lByte = (lValue>>>(lCount*8)) & 255;
			WordToHexValue_temp = "0" + lByte.toString(16);
			WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length-2,2);
		}
		return WordToHexValue;
	};
 
	function Utf8Encode(string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
 
		for (var n = 0; n < string.length; n++) {
 
			var c = string.charCodeAt(n);
 
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
 
		}
 
		return utftext;
	};
 
	var x=Array();
	var k,AA,BB,CC,DD,a,b,c,d;
	var S11=7, S12=12, S13=17, S14=22;
	var S21=5, S22=9 , S23=14, S24=20;
	var S31=4, S32=11, S33=16, S34=23;
	var S41=6, S42=10, S43=15, S44=21;
 
	string = Utf8Encode(string);
 
	x = ConvertToWordArray(string);
 
	a = 0x67452301; b = 0xEFCDAB89; c = 0x98BADCFE; d = 0x10325476;
 
	for (k=0;k<x.length;k+=16) {
		AA=a; BB=b; CC=c; DD=d;
		a=FF(a,b,c,d,x[k+0], S11,0xD76AA478);
		d=FF(d,a,b,c,x[k+1], S12,0xE8C7B756);
		c=FF(c,d,a,b,x[k+2], S13,0x242070DB);
		b=FF(b,c,d,a,x[k+3], S14,0xC1BDCEEE);
		a=FF(a,b,c,d,x[k+4], S11,0xF57C0FAF);
		d=FF(d,a,b,c,x[k+5], S12,0x4787C62A);
		c=FF(c,d,a,b,x[k+6], S13,0xA8304613);
		b=FF(b,c,d,a,x[k+7], S14,0xFD469501);
		a=FF(a,b,c,d,x[k+8], S11,0x698098D8);
		d=FF(d,a,b,c,x[k+9], S12,0x8B44F7AF);
		c=FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);
		b=FF(b,c,d,a,x[k+11],S14,0x895CD7BE);
		a=FF(a,b,c,d,x[k+12],S11,0x6B901122);
		d=FF(d,a,b,c,x[k+13],S12,0xFD987193);
		c=FF(c,d,a,b,x[k+14],S13,0xA679438E);
		b=FF(b,c,d,a,x[k+15],S14,0x49B40821);
		a=GG(a,b,c,d,x[k+1], S21,0xF61E2562);
		d=GG(d,a,b,c,x[k+6], S22,0xC040B340);
		c=GG(c,d,a,b,x[k+11],S23,0x265E5A51);
		b=GG(b,c,d,a,x[k+0], S24,0xE9B6C7AA);
		a=GG(a,b,c,d,x[k+5], S21,0xD62F105D);
		d=GG(d,a,b,c,x[k+10],S22,0x2441453);
		c=GG(c,d,a,b,x[k+15],S23,0xD8A1E681);
		b=GG(b,c,d,a,x[k+4], S24,0xE7D3FBC8);
		a=GG(a,b,c,d,x[k+9], S21,0x21E1CDE6);
		d=GG(d,a,b,c,x[k+14],S22,0xC33707D6);
		c=GG(c,d,a,b,x[k+3], S23,0xF4D50D87);
		b=GG(b,c,d,a,x[k+8], S24,0x455A14ED);
		a=GG(a,b,c,d,x[k+13],S21,0xA9E3E905);
		d=GG(d,a,b,c,x[k+2], S22,0xFCEFA3F8);
		c=GG(c,d,a,b,x[k+7], S23,0x676F02D9);
		b=GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);
		a=HH(a,b,c,d,x[k+5], S31,0xFFFA3942);
		d=HH(d,a,b,c,x[k+8], S32,0x8771F681);
		c=HH(c,d,a,b,x[k+11],S33,0x6D9D6122);
		b=HH(b,c,d,a,x[k+14],S34,0xFDE5380C);
		a=HH(a,b,c,d,x[k+1], S31,0xA4BEEA44);
		d=HH(d,a,b,c,x[k+4], S32,0x4BDECFA9);
		c=HH(c,d,a,b,x[k+7], S33,0xF6BB4B60);
		b=HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);
		a=HH(a,b,c,d,x[k+13],S31,0x289B7EC6);
		d=HH(d,a,b,c,x[k+0], S32,0xEAA127FA);
		c=HH(c,d,a,b,x[k+3], S33,0xD4EF3085);
		b=HH(b,c,d,a,x[k+6], S34,0x4881D05);
		a=HH(a,b,c,d,x[k+9], S31,0xD9D4D039);
		d=HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);
		c=HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);
		b=HH(b,c,d,a,x[k+2], S34,0xC4AC5665);
		a=II(a,b,c,d,x[k+0], S41,0xF4292244);
		d=II(d,a,b,c,x[k+7], S42,0x432AFF97);
		c=II(c,d,a,b,x[k+14],S43,0xAB9423A7);
		b=II(b,c,d,a,x[k+5], S44,0xFC93A039);
		a=II(a,b,c,d,x[k+12],S41,0x655B59C3);
		d=II(d,a,b,c,x[k+3], S42,0x8F0CCC92);
		c=II(c,d,a,b,x[k+10],S43,0xFFEFF47D);
		b=II(b,c,d,a,x[k+1], S44,0x85845DD1);
		a=II(a,b,c,d,x[k+8], S41,0x6FA87E4F);
		d=II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);
		c=II(c,d,a,b,x[k+6], S43,0xA3014314);
		b=II(b,c,d,a,x[k+13],S44,0x4E0811A1);
		a=II(a,b,c,d,x[k+4], S41,0xF7537E82);
		d=II(d,a,b,c,x[k+11],S42,0xBD3AF235);
		c=II(c,d,a,b,x[k+2], S43,0x2AD7D2BB);
		b=II(b,c,d,a,x[k+9], S44,0xEB86D391);
		a=AddUnsigned(a,AA);
		b=AddUnsigned(b,BB);
		c=AddUnsigned(c,CC);
		d=AddUnsigned(d,DD);
	}
 
	var temp = WordToHex(a)+WordToHex(b)+WordToHex(c)+WordToHex(d);
 
	return temp.toLowerCase();
}