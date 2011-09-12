function addCell(insertHTML, beforeColumn, inTable, groupStart, groupEnd, colspan, groupNumber)
{
	var cellDifference=0;
	if (groupNumber>1)
	{
		var cellDifference=0;
		for (i=1;i<groupNumber;i++)
		{
			var cellCount=$(inTable).find("tr:first").find(".x"+i).length;
		    cellDifference=cellDifference+(colspan[i]-cellCount);					
		}
	}
	
	//Only add this if it hasn't already been added.
	if ($(inTable).find("tr:first").attr('elideClassesAdded')==undefined)
		$(insertHTML).insertBefore($($(inTable).find("tr:first").children("td").get(beforeColumn-cellDifference)));
	
	var theRows=$(inTable).find("tr");
	for (var i=1; i<theRows.length; i++)
	{
		if ($($(theRows[i])).attr('elideClassesAdded')==undefined)
		{
			$(insertHTML).insertBefore($($(theRows[i]).children("td").get(beforeColumn)));
		}
	}
}

function addClass(groupNumber, onColumn, inTable)
{
	var firstRow=$(inTable).find("tr:first");
	var tds=$(firstRow).find("td");
	var count=0;
	for (var i=0; i<tds.length; i++)
	{
		if ((i+count)==onColumn)
		{
			if ($(firstRow).attr('elideClassesAdded')==undefined) 
			{
				$(tds[i]).addClass("expanded");
				$(tds[i]).addClass("x"+groupNumber);							
			}
		}
		if ($(tds[i]).attr('colspan')>1)
			count=count+($(tds[i]).attr('colspan')-1);
	}
	
	var theRows=$(inTable).find("tr");
	for (var i=1; i<theRows.length; i++)
	{
		if ($($(theRows[i])).attr('elideClassesAdded')==undefined)
		{
			$($(theRows[i]).children("td").get(onColumn)).addClass("expanded");
			$($(theRows[i]).children("td").get(onColumn)).addClass("x"+groupNumber);
		}
	}
}

function refreshTableRow(row)
{
	var topRow=$(row).parents("table:first").find("tr:first");

	var groups=$(topRow).find(".elided:visible");
	for (var i=0; i<groups.length; i++)
	{
		var groupnumber=$(groups[i]).attr("groupnumber");
		$(row).find(".x"+groupnumber).hide();
		$(row).find(".e"+groupnumber).show();
	}
	var groups=$(topRow).find(".elided:hidden");
	for (var i=0; i<groups.length; i++)
	{
		var groupnumber=$(groups[i]).attr("groupnumber");
		$(row).find(".x"+groupnumber).show();
		$(row).find(".e"+groupnumber).hide();
	}
}
function elideGroup(e)
{
	var groupNumber=$(e.target).attr("groupNumber");
	$(e.target).parents("table:first").find(".x"+groupNumber).hide();
	$(e.target).parents("table:first").find(".e"+groupNumber).show();
	var cookieName=$(e.target).parents("table:first").attr('tableName')+'-'+$(e.target).attr('groupnumber');
	createCookie(cookieName,0);
}
function expandGroup(e)
{
	var groupNumber=$(e.target).attr("groupNumber");
	$(e.target).parents("table:first").find(".e"+groupNumber).hide();
	$(e.target).parents("table:first").find(".x"+groupNumber).show();
	var cookieName=$(e.target).parents("table:first").attr('tableName')+'-'+$(e.target).attr('groupnumber');
	createCookie(cookieName,1);
}

function capFirstLetter(string)
{
	if (string!=null)
	    return string.charAt(0).toUpperCase() + string.slice(1);
	return "";
}

function updateRows(theTable,groupStart,groupEnd,elideClass,colspan)
{
	//Setup all Rows with the expanded Class name
	var offset=0;
	for (var i=1;i<=10;i++)
	{
		if (groupStart[i]!=-1)
		{
			if (i>1)
				offset=offset+colspan[i-1];
			else
				offset=groupStart[i];
			for (var j=groupStart[i];j<=groupEnd[i];j++)
			{
				addClass(i,j-groupStart[i]+offset,theTable);
			}
		}
	}

	//Add in the elided table column to each row
	var offset=0;
	for (var i=1;i<=10;i++)
	{
		if (groupStart[i]!=-1)
		{
			if (i>1)
				offset=offset+colspan[i-1];
			else
				offset=groupStart[i];
			addCell('<td onclick="expandGroup(event)" groupNumber='+i+' class="elided e'+i+' '+elideClass[i]+'"></td>',offset+(i-1),theTable, groupStart, groupEnd,colspan,i);
		}
	}

	var theRows=$(theTable).find("tr");
	for (var i=0; i<theRows.length; i++)
	{
		$($(theRows[i])).attr('elideClassesAdded',1);			
	}	
}

function makeTableElidable(table)
{
	var theTable=$(table).find("tbody:first");
	var theRows=$(theTable).find("tr");
	var headerColumns=$(theRows).filter(":first").find("td");
	var groupStart=new Array();
	var groupEnd=new Array();
	var expandedTitle=new Array();
	var expandClass=new Array();
	var elideClass=new Array();
	var colspan=new Array();
	var colSpanExtra=new Array();
	var totalColSpanExtra=0;
	if ($(theTable).attr('elidable')==undefined)
	{
		colSpanExtra[0]=0;
		for (var i=1;i<=10;i++)
		{
			colspan[i]=0;
			colSpanExtra[i]=0;
			$.each($(theRows).filter(":first").find("[egroup="+i+"]"),function (index,value){
				if ($(value).attr('colspan')>1)
				{
					colspan[i]=colspan[i]+$(value).attr('colspan');
					// totalColSpanExtra=totalColSpanExtra+$(value).attr('colspan')-1;
				}
				else
					colspan[i]++;
			});
			colSpanExtra[i]=totalColSpanExtra;
			groupStart[i]=$(theRows).filter(":first").find("[egroup="+i+"]:first").index()+colSpanExtra[i-1];
			expandedTitle[i]=$(theRows).filter(":first").find("[egroup="+i+"]:first").attr('title');
			expandClass[i]=$(theRows).filter(":first").find("[egroup="+i+"]:first").attr('expandClass');
			elideClass[i]='elided'+capFirstLetter($(theRows).filter(":first").find("[egroup="+i+"]:first").attr('expandClass'));
			groupEnd[i]=groupStart[i]+colspan[i]+colSpanExtra[i-1]-1;
		}		
	}
	// else
	// {
	// 	for (var i=1;i<=10;i++)
	// 	{
	// 		groupStart[i]=-1;
	// 		groupEnd[i]=-1;
	// 	}
	// 	groupCount=1;
	// 	var rowTD=$(theRows).filter(":first").find("td");
	// 	for (var i=1; i<rowTD.length; i++)
	// 	{
	// 		if ($(rowTD[i]).attr('groupStart')!=undefined)
	// 		{
	// 			groupStart[groupCount]=parseInt($(rowTD[i]).attr('groupStart'));
	// 			groupEnd[groupCount]=parseInt($(rowTD[i]).attr('groupEnd'));
	// 			expandClass[groupCount]=$(rowTD[i]).attr('expandClass');
	// 			elideClass[groupCount]=$(rowTD[i]).attr('elideClass');
	// 			groupCount++;				
	// 		}
	// 	}
	// }
	if ($(theTable).attr('elidable')==undefined)
	{
		var currentLocation=0;
		var topLine="<tr elideClassesAdded=1 height=20px>";
		for (var i=1;i<=10;i++)
		{
			if (groupStart[i]!=-1)
			{
				if ((groupStart[i]-currentLocation)>0)
					topLine=topLine+'<td colspan='+(groupStart[i]-currentLocation)+'></td>';
				topLine=topLine+'<td onclick="expandGroup(event)" width=8 class="elided e'+i+' '+elideClass[i]+'" expandClass='+expandClass[i]+' elideClass='+elideClass[i]+' groupNumber='+i+' groupStart='+groupStart[i]+' theColSpans='+colspan[i]+' groupEnd='+groupEnd[i]+'></td>';
				topLine=topLine+'<td align=center onclick="elideGroup(event)" class="ui-widget expanded x'+i+' '+expandClass[i]+'" groupNumber='+i+' colspan='+(groupEnd[i]-groupStart[i]+1)+'>'+expandedTitle[i]+'</td>';
				currentLocation=groupEnd[i]+1;			
			}
		}
		topLine=topLine+"</tr>";		
	}
	
	updateRows(theTable,groupStart,groupEnd,elideClass,colspan);
	
	//	topLine=topLine+"</tr>";
	if ($(theTable).attr('elidable')==undefined)
	{
		$(topLine).insertBefore($(theRows).filter(":first"));
	}
	$(theTable).find(".elided").show();
	$(theTable).find(".expanded").hide();
	$(theTable).attr('elidable',1);
	
	//Record Cookies for state of Ellision per table
	for (var i=1;i<=10;i++)
	{
		var cookieName=$(table).attr('tableName')+'-'+i;
		var expand=readCookie(cookieName);
		if (expand==1)
		{
			$(table).find("tr:first").find("td:visible").filter("[groupnumber="+i+"]").trigger('click')
		}
	}
}

function updateRow(theRow)
{
	var theTable=$(theRow).parents("table:first");
	if ($(theTable).find("tbody").attr("elidable")==undefined)
		return;
	var groupStart=new Array();
	var groupEnd=new Array();
	var elideClass=new Array();
	var colspan=new Array();
	var tds=$(theTable).find("tr:first").find("td");
	for (var i=1;i<=10;i++)
	{
		groupStart[i]=-1;
		groupEnd[i]=-1;
	}
	
	for (i=0;i<tds.length;i++)
	{
		if ($(tds[i]).attr("groupstart")!=undefined)
		{
			groupNumber=parseInt($(tds[i]).attr("groupNumber"));
			groupStart[groupNumber]=parseInt($(tds[i]).attr("groupstart"));
			groupEnd[groupNumber]=parseInt($(tds[i]).attr("groupend"));
			colspan[groupNumber]=parseInt($(tds[i]).attr("theColSpans"));
			elideClass[groupNumber]=$(tds[i]).attr("elideClass");
		}
	}
	updateRows(theTable,groupStart,groupEnd,elideClass,colspan);
	refreshTableRow(theRow);
}