<h2>Welcome to LLA - Admin Panel!</h2>
<%if is_admin%>
<style type="text/css">
	/*demo page css*/
	.ui-widget {font-family:Tahoma,Arial,Helvetica,sans-serif;font-size:11px;}
</style>
<div>&nbsp;</div>
<table cellpadding="0" cellspacing="0" class="maxw">
<tr>
	<td width="49%" valign="top">
		<div style="overflow: hidden; display: block; outline-color: -moz-use-text-color; outline-style: none; outline-width: 0px; height: auto; width: 100%;" class="ui-dialog ui-widget ui-widget-content ui-corner-all " tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-apost_data_dialog"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix" unselectable="on" style="-moz-user-select: none;"><span class="ui-dialog-title" id="ui-dialog-title-apost_data_dialog" unselectable="on" style="-moz-user-select: none;">Search users (<%=res_count%>)</span></div><div id="apost_data_dialog" style="height: auto; min-height: 56px; width: auto;" class="ui-dialog-content ui-widget-content">
			<%IT:form id="search_form" begin="true" method="post" action="" /%>
			<div style="padding-bottom: 5px;"><label for="search_users">User name, Email, ID:</label></div>
			<div style="padding-bottom: 5px;" align="right"><%it:input type="text" name="search_users" id="search_users" class="inp" style="width: 99%;" priority="template,get,post" /%></div>
			<div align="right"><%it:input type="button" name="clear" id="clear" value="Clear" class="butt" /%> <%it:input type="submit" name="search" id="search" value="Search" class="butt" /%></div>
			<%IT:form end="true" /%>
			<%if res_count%>
			<p>Search Results:</p>
			<%for res_count%>
			<p>
				<a href="<%=HTTP%>admin/?r=<%=res_user_role_uri%>&id=<%=res_id%>">ID: <%=res2_id%>, <%=res2_user_role%>: <%=res2_first_name%> <%=res2_last_name%> (<%=res2_email%>)</a>
			</p>
			<%/for%>
			<%/if%>
		</div></div>
		<div>&nbsp;</div>
		<div style="overflow: hidden; display: block; outline-color: -moz-use-text-color; outline-style: none; outline-width: 0px; height: auto; width: 100%;" class="ui-dialog ui-widget ui-widget-content ui-corner-all " tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-apost_data_dialog"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix" unselectable="on" style="-moz-user-select: none;"><span class="ui-dialog-title" id="ui-dialog-title-apost_data_dialog" unselectable="on" style="-moz-user-select: none;">Awaiting Orders (<%=or_count%>)</span></div><div id="apost_data_dialog" style="height: auto; min-height: 56px; width: auto;" class="ui-dialog-content ui-widget-content">
		<%if or_count%>
		<table cellpadding="3" cellspacing="2" class="maxw">
		<tr style="background-color: #496E56; color: white; font-weight: bold;">
			<td width="6%">
				ID
			</td>
			<td width="28%">
				User Name
			</td>
			<td width="22%">
				Total Amount, $
			</td>
			<td width="20%">
				Payment Type
			</td>
			<td width="23%">
				Create date
			</td>
		</tr>
		<%for or_count%>
		<%if is_even%>
		<tr class="nav_std hand" onmouseover="setClass(this,'nav_hl hand');" onmouseout="setClass(this,'nav_std hand');">
		<%else%>
		<tr class="nav_std_hl hand" onmouseover="setClass(this,'nav_hl hand');" onmouseout="setClass(this,'nav_std_hl hand');">
		<%/if%>
			<td onclick="gotoURL('<%=HTTP%>admin/?r=orders.orders_edit&id=<%=or_id%>');"><%=or_id%></td>			
			<td onclick="gotoURL('<%=HTTP%>admin/?r=orders.orders_edit&id=<%=or_id%>');"><%=or_user_name%></td>			
			<td onclick="gotoURL('<%=HTTP%>admin/?r=orders.orders_edit&id=<%=or_id%>');"><%=or_total_amount%></td>			
			<td onclick="gotoURL('<%=HTTP%>admin/?r=orders.orders_edit&id=<%=or_id%>');"><%=or_payment_type%></td>			
			<td onclick="gotoURL('<%=HTTP%>admin/?r=orders.orders_edit&id=<%=or_id%>');"><%=or_create_date%></td>			
		</tr>
		<%/for%>
		</table>
		<%/if%>
		</div></div>
	</td>
	<td width="2%">&nbsp;</td>
	<td width="49%" valign="top">
		<div style="overflow: hidden; display: block; outline-color: -moz-use-text-color; outline-style: none; outline-width: 0px; height: auto; width: 100%;" class="ui-dialog ui-widget ui-widget-content ui-corner-all " tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-apost_data_dialog"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix" unselectable="on" style="-moz-user-select: none;"><span class="ui-dialog-title" id="ui-dialog-title-apost_data_dialog" unselectable="on" style="-moz-user-select: none;">Storages</span></div><div id="apost_data_dialog" style="height: auto; min-height: 56px; width: auto;" class="ui-dialog-content ui-widget-content">
		<%if st_count%>
		<table cellpadding="3" cellspacing="2" class="maxw">
		<tr style="background-color: #496E56; color: white; font-weight: bold;">
			<td width="15%">
				Status
			</td>
			<td width="45%">
				Title
			</td>
			<td width="20%">
				Total Space
			</td>
			<td width="20%">
				Free Space
			</td>
		</tr>
		<%for st_count%>
		<%if is_even%>
		<tr class="nav_std hand" onmouseover="setClass(this,'nav_hl hand');" onmouseout="setClass(this,'nav_std hand');">
		<%else%>
		<tr class="nav_std_hl hand" onmouseover="setClass(this,'nav_hl hand');" onmouseout="setClass(this,'nav_std_hl hand');">
		<%/if%>
			<td onclick="gotoURL('<%=HTTP%>admin/?r=storages.storage_edit&id=<%=st_id%>');"><%=st2_status%></td>			
			<td onclick="gotoURL('<%=HTTP%>admin/?r=storages.storage_edit&id=<%=st_id%>');"><%=st2_title%></td>			
			<td onclick="gotoURL('<%=HTTP%>admin/?r=storages.storage_edit&id=<%=st_id%>');"><%=st2_total_space%></td>			
			<td onclick="gotoURL('<%=HTTP%>admin/?r=storages.storage_edit&id=<%=st_id%>');"><%=st2_free_space%></td>			
		</tr>
		<%/for%>
		</table>
		<%/if%>
		</div></div>
		<div>&nbsp;</div>
		<div style="overflow: hidden; display: block; outline-color: -moz-use-text-color; outline-style: none; outline-width: 0px; height: auto; width: 100%;" class="ui-dialog ui-widget ui-widget-content ui-corner-all " tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-apost_data_dialog"><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix" unselectable="on" style="-moz-user-select: none;"><span class="ui-dialog-title" id="ui-dialog-title-apost_data_dialog" unselectable="on" style="-moz-user-select: none;">Users Online</span></div><div id="apost_data_dialog" style="height: auto; min-height: 56px; width: auto;" class="ui-dialog-content ui-widget-content">
		<%if on_count%>
		<table cellpadding="3" cellspacing="2" class="maxw">
		<tr style="background-color: #496E56; color: white; font-weight: bold;">
			<td width="21%">
				Role
			</td>
			<td width="42%">
				User
			</td>
			<td width="27%">
				Logged in at
			</td>
			<td width="10%">
				On site
			</td>
		</tr>
		<%for on_count%>
		<%if is_even%>
		<tr class="nav_std hand" onmouseover="setClass(this,'nav_hl hand');" onmouseout="setClass(this,'nav_std hand');">
		<%else%>
		<tr class="nav_std_hl hand" onmouseover="setClass(this,'nav_hl hand');" onmouseout="setClass(this,'nav_std_hl hand');">
		<%/if%>
			<td onclick="gotoURL('<%=HTTP%>admin/?r=users.user_edit&id=<%=on_id%>');"><%=on_user_role%></td>			
			<td onclick="gotoURL('<%=HTTP%>admin/?r=users.user_edit&id=<%=on_id%>');"><%=on_user%></td>			
			<td onclick="gotoURL('<%=HTTP%>admin/?r=users.user_edit&id=<%=on_id%>');"><%=on_logged_in%></td>			
			<td onclick="gotoURL('<%=HTTP%>admin/?r=users.user_edit&id=<%=on_id%>');"><%=on_on_site%> min</td>			
		</tr>
		<%/for%>
		</table>
		<%/if%>
		</div></div>
	</td>
</tr>
</table>
<%/if%>