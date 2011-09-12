<!DOCTYPE html>
<html>
<head>
  <title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link type="text/css" href="jq/jquery-ui-1/css/pepper-grinder/jquery-ui-1.8rc1.custom.css" rel="Stylesheet" />	
	<link type="text/css" href="js/cellarworx.css" rel="Stylesheet" />	
	
	<script type="text/javascript" src="jq/jquery-ui-1/js/jquery-1.4.1.min.js"></script>
	<script type="text/javascript" src="jq/jquery-ui-1/js/jquery-ui-1.8rc1.custom.min.js"></script>
	<script type="text/javascript" src="jq/ajaxify-v2/jquery.ajaxify.js"></script>
	<script type="text/javascript" src="jq/ajaxupload/ajaxupload.js"></script>
	<script type="text/javascript" src="jq/jquery.qtip-1.0.0-rc3.min.js"></script>
	<script type="text/javascript" src="jq/jquery.scrollTo-min.js"></script>
	<script type="text/javascript" src="jq/jquery.localscroll-1.2.7-min.js"></script>

	<script type="text/javascript" src="js/utilities.js"></script>
	<script type="text/javascript" src="js/outstandingWos.js"></script>
	<script type="text/javascript" src="js/lots.js"></script>
	<script type="text/javascript" src="js/clientVintage.js"></script>
	<script type="text/javascript" src="js/wt.js"></script>
	<script type="text/javascript" src="js/facilities.js"></script>
	<script type="text/javascript" src="js/labviewsummary.js"></script>
	<script type="text/javascript" src="js/detail/wodetail.js"></script>
	<script type="text/javascript" src="js/detail/boldetail.js"></script>
	<script type="text/javascript" src="js/tasks.js"></script>
	<script type="text/javascript" src="js/jeip.js"></script>
	<script type="text/javascript" src="js/expandElide.js"></script>
	<script type="text/javascript" src="js/blender.js"></script>
	<script type="text/javascript" src="js/blendTraverser.js"></script>

	<script type="text/javascript" src="server/server.js"></script>
	<script type="text/javascript">

	var defaultClient=new client();
	var defaultVintage="";
	var defaultLot="";
	var defaultTask="";
	var taskData;
	var hideShowSpeed=250;
	var inventoryDetailShowing=false;
	var labResultsDetailShowing=false;
	var costDetailShowing=false;
	var juicePanelDetailShowing=false;
	var scorpionDetailShowing=false;
	var breadCrumbs=new Array();


		
	function updateHeaderClientVintage()
	{
		var desc=defaultClient.theName+' '+defaultVintage;
		$('#clientVintageMenuItem').html(desc);
	}
	
	function showMainPage()
	{
		if (readCookie('username')==null)
		{
			$('body>*').hide();
			$('#loginPanel').show();
			$('#username').val("");
			$('#password').val("");
			$('#username').focus();
		}
		else
		{
			$('#woDetail').hide();
			$('#leftMenu').show();
			$('#loginPanel').hide();
		}
	}
	
	function loginComplete(data)
	{		
		if (data!=null)
		{
			$('#loginStatus').html("<div></div>");
			createCookie('username',data['username']);
			showMainPage();		
		}
		else
		{
			$('#loginStatus').html("<div>Login Failed</div>");
		}
	}
	function logout()
	{
		eraseCookie('username');
		showMainPage();
	}
	function checkUsernamePassword()
	{
		var username=$('#username').val();
		var password=$('#password').val();
		$('#loginStatus').html("<div>Checking...</div>");
	 	$.getJSON(server+"?action=login&username="+username+"&password="+password,"",loginComplete);					
	}
	
	</script>
	<script type="text/javascript">
		$(document).ready(function() {

			$('.ajaxify').ajaxify({
				target: '#maindiv'
			});
			
			$(".accordion").accordion({ collapsible:true, autoHeight:false, clearStyle:true });
			$(".aDate").datepicker();
			$('.rightSubPanel').hide();
			$('#loading').hide();
			$('#saveButton').hide();
			$('#uploadFileDialog').dialog({bgiframe:true, 
						resizeable:false, 
						height:140, 
						modal:true,
						autoOpen:false, 
						overlay: {
							backgroundColor: '#000',
							opacity: 0.5
						},
						buttons: {
							'Delete the Work Order': function() {
								$(this).dialog('close');
								$.post(postServer,{ action: "delete_wo", ID:findAttr("rowid",woToDelete)},function (){
									$('#lotDetail>*').remove();
									$('#defLot').html(" : "+defaultLot);
									$.getJSON(server+"?action=showlotinfo&clientid="+defaultClient.clientid+"&lot="+defaultLot,"",showLotDetail);				
									$('#showLotDetailPanel').show();
								});
							},
							Cancel: function() {
								$(this).dialog('close');
							}
						}
					});
			$('#dialogDeleteWO').dialog({bgiframe:true, 
						resizeable:false, 
						height:140, 
						modal:true,
						autoOpen:false, 
						overlay: {
							backgroundColor: '#000',
							opacity: 0.5
						},
						buttons: {
							'Delete the Work Order': function() {
								$(this).dialog('close');
								$.post(postServer,{ action: "delete_wo", ID:findAttr("rowid",woToDelete)},function (){
									$('#lotDetail>*').remove();
									$('#defLot').html(" : "+defaultLot);
									$.getJSON(server+"?action=showlotinfo&clientid="+defaultClient.clientid+"&lot="+defaultLot,"",showLotDetail);				
									$('#showLotDetailPanel').show();
								});
							},
							Cancel: function() {
								$(this).dialog('close');
							}
						}
					});
			$('#dialogDeleteLot').dialog({bgiframe:true, 
						resizeable:false, 
						height:140, 
						modal:true,
						autoOpen:false, 
						overlay: {
							backgroundColor: '#000',
							opacity: 0.5
						},
						buttons: {
							'Delete the Lot': function() {
								$(this).dialog('close');
								$.post(postServer,{ action: "deletelot", lotid:$(lotToDelete).attr('lotid')},queryShowLotsPanel);
							},
							Cancel: function() {
								$(this).dialog('close');
							}
						}
					});
				var $scrollingDiv = $(".scrollingDiv");	
				$(window).scroll(function(){			
					$scrollingDiv
						.stop()
						.animate({"marginTop": ($(window).scrollTop()) + "px"}, "slow" );			
				});
				
			// $("jqueryselector").qtip({
			//    style: { 
			//       width: 200,
			//       padding: 5,
			//       background: '#A2D959',
			//       color: 'black',
			//       textAlign: 'center',
			//       border: {
			//          width: 7,
			//          radius: 5,
			//          color: '#A2D959'
			//       },
			//       tip: 'bottomLeft',
			//       name: 'dark' // Inherit the rest of the attributes from the preset dark style
			//    }
			// });
			$('#leftMenu div').css("padding","0.4em");
			$('#leftMenu div').addClass("ui-widget ui-corner-all");
			$('#leftMenu div').find('div').mouseout(function (){
				$(this).removeClass('ui-state-default');
				});
			$('#leftMenu div').find('div').mouseover(function(){
				  $(this).addClass('ui-state-default');
			});
			
			if (readCookie('clientname')!=null)
			{
				defaultClient.theName=readCookie('clientname');
				defaultClient.clientid=readCookie('clientid');
				defaultVintage=readCookie('defaultVintage');				
				updateHeaderClientVintage();
			}
			if (readCookie('inventoryDetailShowing')=="true")
				inventoryDetailShowing=true;
			if (readCookie('costDetailShowing')=="true")
				costDetailShowing=true;
			if (readCookie('labResultsDetailShowing')=="true")
				labResultsDetailShowing=true;
			if (readCookie('juicePanelDetailShowing')=="true")
				juicePanelDetailShowing=true;
			if (readCookie('scorpionDetailShowing')=="true")
				scorpionDetailShowing=true;
			
			showMainPage();
		});
		
		function closeDiv(e)
		{
			$(e.target).parent().hide();
		}				
		function showPanel(panelName)
		{
			$('.rightSubPanel').hide();
			
			if (panelName=='showWeighTagsPanel')
			{
				queryShowWTsPanel();				
			}
			if (panelName=='showLabViewSummaryPanel')
			{
				queryLabViewSummary();				
			}
			if (panelName=='blender')
			{
				queryBlender("NEW");				
			}
			if (panelName=='blenderList')
			{
				$('#mainBlenderPanel').show();
				$('#blenderList').show();
				$('#blender').hide();
				queryBlendTraverser();				
			}
			if (panelName=='showFacilitiesPanel')
			{
				queryFacilitiesManagement();				
			}
			if (panelName=='showLotsPanel')
			{
				queryShowLotsPanel();				
			}
			if (panelName=='outstandingWorkOrdersPanel')
			{
				 queryShowOutstandingWOs();			
			}
			if (panelName=='clientVintagePanel')
			{
				 $.getJSON(server+"?action=defaults&deviceid=cellarworx&username="+readCookie("username"),"",showClients);				
			}
			if (panelName=='taskPanel')
			{
				$('#specificsSection>*').remove();
				$('#endingDataSection>*').remove();
				$("#startDate").val(todayDate());
				$("#endDate").val(todayDate());
				$("#taskType").val("");
				$("#workedBy").val("");
				$("#description").val("");
				
				$('#'+panelName).show();								
				defaultTask=null;
//				showSpecificsInTask(null);
			}
			else
				$('#'+panelName).show();								
		}
		</script>
</head>
<body>
	<!-- <div id="woDetail" class="popup"><div class=cancelPopup onclick="closeDiv(event)"></div></div> -->
	<div id="loginPanel" style="margin-left:auto; margin-right:auto; width:200px; padding:20px" class="inner ui-state-default ui-corner-all">
		<table align=center width=100%>
			<tr>
				<td align=right>Username:</td>
				<td align=left><input id="username" type="text" size="20"></input></td>
			</tr>
			<tr>
				<td align=right>Password:</td>
				<td align=left><input onChange="checkUsernamePassword()" id=password type=password size=20></input></td>
			</tr>
			<tr>
				<td></td><td id=loginStatus align=left></td>
			</tr>	
		</table>
	</div>
	<div id="dialogDeleteWO" title="Delete Word Order?">
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure?</p>
	</div
	<div id="dialogDeleteLot" title="Delete Lot?">
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure?</p>
	</div
	
	<div id="leftMenu" class="scrollingDiv accordion" style="width:15%; margin-right:5px; float:left">
	    <h4><a href="#"><center>Main Menu</center></a></h4>
		<div>
			<div class=leftMenuItem id="clientVintageMenuItem" onClick="showPanel('clientVintagePanel');">Client/Vintage</div>
			<div class=leftMenuItem onClick="showPanel('outstandingWorkOrdersPanel');">Outstanding Work Orders</div>
			<div class=leftMenuItem onClick="showPanel('showLotsPanel');">Lots</div>
			<div class=leftMenuItem onClick="showPanel('showWeighTagsPanel');">Weight Tags</div>
			<!-- <div onClick="showPanel('showLotDetailPanel');">Lot Detail<span id="defLot"></span></div>
			<div onClick="showPanel('taskPanel');">Task</div> -->
		</div>
	    <h4><a href="#"><center>Blending</center></a></h4>
		<div>
			<div class=leftMenuItem onClick="showPanel('blender');">Blender</div>
			<div class=leftMenuItem onClick="showPanel('blenderList');">Blend Traversal</div>
		</div>

	    <h4><a href="#"><center>Reports</center></a></h4>
		<div>
			<div class=leftMenuItem onClick="showPanel('showLabViewSummaryPanel');">Lab View Summary</div>
		</div>
	
	    <h4><a href="#"><center>Administration</center></a></h4>
		<div>
			<div class=leftMenuItem onClick="showPanel('showFacilitiesPanel');">Facilities</div>
			<div class=leftMenuItem onClick="logout();">Log Out</div>
		</div>
	</div>
	
	<!-- Show Client/Vintage Panel -->

	<!-- Right Panel -->
	<div class=rightPanel>
		<div class="ui-state-default ui-corner-all rightSubPanel" id="clientVintagePanel" style="width:100%; float:left">
		    <h4><a href="#"><center>Set Client & Vintage</center></a></h4>
			<div id="clientVintageList">
				<div id="clientList" style="width:50%; float:left">
				</div>
				<div id="vintageList" style="width:50%; float:left">
				</div>
			</div>
		</div>
		<!-- <div class="ui-state-default ui-corner-all rightSubPanel" id="showLotsPanel" style="width:100%; float:left; padding-top:20px"> -->
			<div class="ui-state-default ui-corner-all rightSubPanel" id="lotList" style="width:100%; float:left; padding-top:20px">
			</div>
		<!-- </div> -->
		<div class="ui-state-default ui-corner-all rightSubPanel" id="showWeighTagsPanel" style="width:100%; float:left; padding-top:20px">
			<div id="wtList">
			</div>
		</div>
		<div class="ui-state-default ui-corner-all rightSubPanel" id="showLabViewSummaryPanel" style="width:100%; float:left">
			<div id="labViewSummaryList">
			</div>
		</div>
		<div class="ui-state-default ui-corner-all rightSubPanel" id="showFacilitiesPanel" style="width:100%; float:left">
			<div id="genericList">
			</div>
		</div>
		<div class="ui-state-default ui-corner-all rightSubPanel" style="width:100%; float:left;" id="lotDetail">
		</div>
		<div class="ui-state-default ui-corner-all rightSubPanel" style="width:100%; float:left; padding-top:20px;" id="otherLots">
		</div>
		<div class="ui-state-default ui-corner-all rightSubPanel" style="width:100%; float:left; padding-top:20px" id="woDetail">
		</div>

		<!-- Show Outstanding Work Orders Panel -->
		<div class="ui-state-default ui-corner-all rightSubPanel" id="outstandingWorkOrdersPanel" style="width:100%; float:left">
		    <h4><a href="#"><center>Outstanding Work Orders</center></a></h4>
			<div class="accordion">
				<h4><a href="#"><center>Overdue</center></a></h4>
				<div id="wolistOverdue">
				</div>
				<h4><a href="#"><center>Today</center></a></h4>
				<div id="wolistToday">
				</div>
				<h4><a href="#"><center>Tomorrow</center></a></h4>
				<div id="wolistTomorrow">
				</div>
				<h4><a href="#"><center>Future</center></a></h4>
				<div id="wolistFuture">
				</div>
			</div>
		</div>
		
		<div class="ui-state-default ui-corner-all rightSubPanel" id="mainBlenderPanel" style="width:100%; float:left">
			<div style="width:90%; margin-bottom:20px; padding:10px; margin-left:auto; margin-right:auto; text-align:center;" class="ui-state-default ui-corner-all title">BLENDING CREATION AND TRAVERSAL
				<table width=50% align=center><tr><td align=right>EFFECTIVE DATE:</td><td align=left><input id=blendDate type=text></input></td></tr></table>
			</div>
			<div class="ui-state-default ui-corner-all" id="blender" style="width:100%;">	
				<div style="width:95%; margin-left:auto; margin-right:auto; margin-bottom:10px; margin-top:10px;" class="ui-state-default ui-corner-all">
					<table width=90% align=center>
						<tr>
							<td width=20% id=blendWOID></td>
							<td width=60% align=center><div class="title">CREATE OR EDIT A BLEND</div></td>
							<td width=20% align=right><div id=recordButton><button onClick="recordBlendToServer()">RECORD</button></div></td>
						</tr>
					</table>
				</div>
				<div  class="ui-state-default ui-corner-all" id="sourceLots" style="width:45%; float:left; padding:20px">
				</div>
				<div  class="ui-state-default ui-corner-all" id="destinationData" style="width:45%; float:right; padding:20px">
					<div class="ui-state-default ui-corner-all" id="destinationLots" style="width:100%; ">
					</div>				
					<div class="ui-state-default ui-corner-all" id="destinationStructure" style="width:100%; margin-top:50px">
					</div>				
				</div>
			</div>
			<div class="ui-state-default ui-corner-all" id="blenderList" style="width:100%;">
			</div>
		</div>
		
	</div>



</body>
</html>
