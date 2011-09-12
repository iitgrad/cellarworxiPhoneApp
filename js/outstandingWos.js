function showWOs(data,timeFrame)
{
	$('#wolist'+timeFrame+'>*').remove();
	var now=new Date();
	var today=new Date(now.getFullYear(),now.getMonth(),now.getDate());
	var found=0;
	
	var lines="";

	var header='<tr class=ui-widget>';
	header=header+'<td class="header" id="toggleCheckbox'+timeFrame+'" align=center>Complete<br><input type=checkbox></input></td>';
	header=header+'<td class="header">Assigned</td>';
	header=header+'<td class="header sortable filterable">Client</td>';
	header=header+'<td class="header sortable filterable" width=90px align=left>Lot</td>';
	header=header+'<td class="header sortable filterable">Lot Description</td>';
	header=header+'<td class="header filterable">WO Number</td>';
	header=header+'<td class="header sortable">Due Date</td>';
	header=header+'<td class="header sortable filterable">Type</td>';
	header=header+'<td  class="header filterable"width=50%>Description</td>';
	header=header+"</tr>";
			
	for (i in data)
	{
		var theDate=parse_date(data[i]['data']['DUEDATE']);
		
		var difference=(theDate-now)/(1000*60*60*24);
		
		if ((timeFrame=="Overdue" & difference<-1) | 
			(timeFrame=="Today" & (difference > -1 & difference < 0)) |
			(timeFrame=="Tomorrow" & (difference > 0 & difference < 2)) |
			(timeFrame=="Future" & (difference > 2)))
		{
			found=1;
			lines=lines+'<tr>';
			if (data[i]['data']['TASKID']>0)
			{
				lines=lines+'<td></td>'
				lines=lines+'<td>'+data[i]['data']['WORKPERFORMEDBY']+'</td>';
				lines=lines+'<td>'+data[i]['data']['CLIENTCODE']+'</td>';
				lines=lines+'<td onClick="gotoLotDetail(\''+data[i]['data']['CLIENTID']+'\','+
															'\''+data[i]['data']['CODE']+'\','+
															'\''+data[i]['data']['CLIENTNAME']+'\','+
															'\''+data[i]['data']['LOT']+'\','+
															'\''+data[i]['data']['ID']+'\''+
															')">'+data[i]['data']['LOT']+'</td>';
				lines=lines+'<td>'+data[i]['data']['LOTDESCRIPTION']+'</td>';
				lines=lines+'<td>Task-'+data[i]['task']['id']+'</td>';
				lines=lines+'<td>'+dateString(theDate)+'</td>';
				lines=lines+'<td>'+data[i]['task']['type']+'</td>';
				lines=lines+'<td><pre>'+data[i]['task']['description']+'<pre></td>';
			}
			else
			{
				lines=lines+'<td align=center><input type=checkbox class="'+timeFrame+'Checkbox" rowid="'+data[i]['data']['ID']+'" onclick=changeCompleteStatus(this) '+showChecked(data[i]['data']['STATUS'])+'></input></td>';
				lines=lines+'<td>'+data[i]['data']['WORKPERFORMEDBY']+'</td>';
				lines=lines+'<td>'+data[i]['data']['CLIENTCODE']+'</td>';
				lines=lines+'<td><a href="#" onClick="gotoLotDetail(\''+data[i]['data']['CLIENTID']+'\','+
															'\''+data[i]['data']['CODE']+'\','+
															'\''+data[i]['data']['CLIENTNAME']+'\','+
															'\''+data[i]['data']['LOT']+'\','+
															'\''+data[i]['data']['ID']+'\''+
															')">'+data[i]['data']['LOT']+'</a></td>';
				lines=lines+'<td>'+data[i]['data']['LOTDESCRIPTION']+'</td>';
				lines=lines+'<td>'+data[i]['data']['ID']+'</td>';
				lines=lines+'<td>'+dateString(theDate)+'</td>';
				lines=lines+'<td>'+data[i]['data']['TYPE']+'</td>';
				lines=lines+'<td><pre>'+data[i]['data']['OTHERDESC']+'</pre></td>';				
			}
			lines=lines+'</tr>';
			
		}
	}
	if (found==1)
	{
		$('<table width=100% align=center>').appendTo("#wolist"+timeFrame);
		$(header).appendTo("#wolist"+timeFrame+">table");
		$(lines).insertAfter("#wolist"+timeFrame+">table tr:last");
		
		// $('<div style="float:right" class="ui-icon ui-icon-grip-dotted-horizontal"</div>').appendTo($('#wolist'+timeFrame+ ' .sortable'));
		// $('#wolist'+timeFrame+ ' .sortable').attr('sortDirection','none');	
		// $('#wolist'+timeFrame+ ' .sortable').click(function (){
		// 	sortColumn(this,1,-2);
		// });

		$('#wolist'+timeFrame+' .filterable').click(function (){
			filterColumn(this,1,0);
		});
		
		$('tr').mouseover(function() {
		  $(this).addClass('ui-state-default');
		});			
		$('tr').mouseout(function() {
		  $(this).removeClass('ui-state-default');
		});
		$('#toggleCheckbox'+timeFrame).click(function (){
			$("#saveButton").show();
			$("."+timeFrame+"Checkbox").each (function () {
				$(this).attr('checked',!$(this).attr('checked'));
			});
		});	
	}
}

function updateCompleteStatus()
{
	var tf=["Overdue","Today","Tomorrow","Future"];
	for (var i in tf)
	{
		$("."+tf[i]+"Checkbox").each (function () {
			if ($(this).attr('checked'))
			{
				$.post(postServer,{ action: "complete_wo", ID: $(this).attr('woid') });
				$(this).parent().parent().hide();		
			}
		});		
	}
}

function showOutstandingWOs(data)
{
	$('#loading').hide();
	$('#saveButton').hide();

	$("#saveButton").unbind('click');
	$("#saveButton").click( function () {
		updateCompleteStatus();
	});
	

	showWOs(data,"Overdue")
	showWOs(data,"Today")
	showWOs(data,"Tomorrow")
	showWOs(data,"Future")
}

function queryShowOutstandingWOs()
{
	$.getJSON(server+"?action=showoutstandingwos","",showOutstandingWOs);					
}
