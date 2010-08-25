<table cellpadding="0" cellspacing="0" class="maxw">
<tr>
	<td class="navi_subbgr">
		<table cellpadding="0" cellspacing="0" class="maxw">
		<tr>
			<td align="left"><span class="accent">Filter</span></td>
			<td align="right">
				<%if is_filter_panel_show%>
					<span id="show_filter_panel" class="hand" style="display: none; padding: 2px;"><img src="<%=IMAGES%>admin/close_tab.png" title="Hide filter" alt="Show filter" /></span>
					<span id="hide_filter_panel" class="hand" style="display: block; padding: 2px;"><img src="<%=IMAGES%>admin/open_tab.png" title="Show filter" alt="Hide filter" /></span>
				<%else%>
					<span id="show_filter_panel" class="hand" style="display: block; padding: 2px;"><img src="<%=IMAGES%>admin/close_tab.png" title="Hide filter" alt="Show filter" /></span>
					<span id="hide_filter_panel" class="hand" style="display: none; padding: 2px;"><img src="<%=IMAGES%>admin/open_tab.png" title="Show filter" alt="Hide filter" /></span>
				<%/if%>
			</td>
		</tr>
		</table>
		<div id="filter_panel"
		<%if !is_filter_panel_show%>
		 style="display: none;"
		<%/if%>
		>
		<table cellpadding="1" cellspacing="0" width="80%">
		<tr>
			<td align="right" valign="top">
				<%for filter_cnt%>
					<%if filter_type_text%>
						<div style="float: left; width: 400px;" align="left">
						<table cellpadding="1" cellspacing="0">
						<tr>
							<td align="right" class="filter_title" width="120"><label for="<%=filter_name%>"><%=filter_title%>:</label></td>
							<td><%it:input type="text" name="<%=filter_name%>" class="inp" priority="template,post,get" /%></td>
						</tr>
						</table>
						</div>
					<%/if%>
					<%if filter_type_select%>
						<div style="float: left; width: 400px;" align="left">
						<table cellpadding="1" cellspacing="0">
						<tr>
							<td align="right" class="filter_title" width="120"><label for="<%=filter_name%>"><%=filter_title%>:</label></td>
							<td><%it:input type="select" name="<%=filter_name%>" class="inpsel" priority="template,post,get" /%></td>
						</tr>
						</table>
						</div>
					<%/if%>
					<%if filter_type_date%>
						<div style="float: left; width: 400px;" align="left">
						<script type="text/javascript">
						$(function() {
							$("#daterange_from_<%$%>").datepicker({showOn: 'both', buttonImage: '<%=IMAGES%>calendar_button_x.gif', buttonImageOnly: true, dateFormat: 'dd/mm/yy'});
							$("#daterange_to_<%$%>").datepicker({showOn: 'both', buttonImage: '<%=IMAGES%>calendar_button_x.gif', buttonImageOnly: true, dateFormat: 'dd/mm/yy'});
						});
						</script>
						<table cellpadding="1" cellspacing="0">
						<tr>
							<td align="right" class="filter_title" width="120"><label for="<%=filter_name%>"><%=filter_title%>:</label></td>
							<td class="field_date"><%it:input type="text" reanonly="true" name="<%=filter_name%>_from" id="daterange_from_<%$%>" class="inp" style="width: 70px;" priority="template,post,get" /%></td>
							<td class="filter_title" align="right" width="25">to:</td>
							<td class="field_date"><%it:input type="text" reanonly="true" name="<%=filter_name%>_to" id="daterange_to_<%$%>" class="inp" style="width: 70px;" priority="template,post,get" /%></td>
						</tr>
						</table>
						</div>
					<%/if%>
				<%/for%>
			</td>
		</tr>
		<tr>
			<td>
				<div style="float: left; width: 395px;">&nbsp;
				</div>
				<div style="float: left; width: 395px;" align="right">
					<table cellpadding="1" cellspacing="0" border="0">
					<tr>
						<td>
						<%it:input type="button" name="clear" id="clear" value="Clear" class="butt" /%>&nbsp;
						<%it:input type="submit" name="filter" id="filter" value="Filter" class="butt" /%>
						</td>
					</tr>
					</table>
				</div>
			</td>
		</tr>
		</table>
		</div>
	</td>
</tr>
</table>
