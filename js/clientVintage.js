function showClients(data)
{
	$('.rightPanel').show();
	
	$('#clientList>*').remove();
	$('#vintageList>*').remove();
	var header='<tr>';
	header=header+'<td align=center><strong>Client</strong></td>';
	header=header+'</tr>';
	var line="";
	for (i in data)
	{
		var showClass="";
		if (defaultClient.theName==i)
			showClass="ui-state-highlight";
		line=line+'<tr><td align=center><div class="ui-widget '+showClass+' ui-corner-all" style="padding: 0.15em;" clientcode='+data[i]['CODE']+' clientid="'+data[i]['CLIENTID']+'">'+i+'</div></td></tr>';
	}
	$('<table width=40% align=center>').appendTo("#clientList");
	$(header).appendTo("#clientList>table");
	$(line).appendTo("#clientList>table");
	$('#clientList div').mouseover(function (){
		$(this).addClass('ui-state-default');
		});
	$('#clientList div').mouseout(function (){
		$(this).removeClass('ui-state-default');
		});
	$('#clientList div').click(function (){
		$('#clientList .ui-state-highlight').removeClass('ui-state-highlight');
		$(this).addClass('ui-state-highlight');
		defaultClient.theName=$(this).text();
		defaultClient.clientid=$(this).attr("clientid");
		defaultClient.clientcode=$(this).attr("clientcode");
		eraseCookie("clientname");
		eraseCookie("clientcode");
		eraseCookie("clientid");
		createCookie("clientname",defaultClient.theName);
		createCookie("clientid",defaultClient.clientid);
		createCookie("clientcode",defaultClient.clientcode);
		 updateHeaderClientVintage();
		 showVintagesForClient(data);
		});
	if (defaultClient.theName!=undefined)
		showVintagesForClient(data);
}
function showVintagesForClient(data)
{
	$('#vintageList>*').remove();
	
	var header='<tr>';
	header=header+'<td align=center>Vintages</td>';
	header=header+'</tr>';
	var line="";
	var clientData=data[defaultClient.theName];
	var vintages=clientData['VINTAGES'];
	for (j in vintages)
	{
		var showClass="";
		if (defaultVintage==vintages[j])
			showClass="ui-state-highlight";
		line=line+'<tr><td align=center><div class="ui-widget '+showClass+' ui-corner-all style="padding: 0.15em;>'+vintages[j]+'</div></td></tr>';
	}
	$('<table width=40% align=center>').appendTo("#vintageList");
	$(header).appendTo("#vintageList>table");
	$(line).appendTo("#vintageList>table");
	$('#vintageList div').mouseover(function (){
		$(this).addClass('ui-state-default');
		});
	$('#vintageList div').mouseout(function (){
		$(this).removeClass('ui-state-default');
		});
	$('#vintageList div').click(function (){
		$('#vintageList .ui-state-highlight').removeClass('ui-state-highlight');
		$(this).addClass('ui-state-highlight');
		defaultVintage=$(this).text();
		eraseCookie('defaultVintage');
		createCookie("defaultVintage",defaultVintage);
		updateHeaderClientVintage();
		});
}
