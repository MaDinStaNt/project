<script type="text/javascript" src="<%=JS%>jquery/jquery.timer.js"></script>
<style type="text/css">
	/*demo page css*/
	.ui-widget {font-family:Tahoma,Arial,Helvetica,sans-serif;font-size:11px;}
</style>
<%IT:form id="<%=_table%>" begin="true" method="post" action="" enctype="multipart/form-data" /%>
<%it:input type="hidden" name="id" priority="template,get,post" /%>
<table cellpadding="0" cellspacing="0" class="maxw note">
<tr>
	<td><span class="note_title"><%it:Localizer string="warning_obligatory_fields" /%>
	</td>
</tr>
</table>
<table cellpadding="3" cellspacing="0" class="form">
<td colspan="2">
	<%if id%>
	<h2><%IT:Localizer string="exhibition" /%>: <%=title%></h2>
	<%else%>
	<h2><%IT:Localizer string="new_exhibition" /%></h2>
	<%/if%>
</td>
</table>
<script type="text/javascript" language="javascript" src="<%=JS%>jquery/jquery.cookie.js"></script>
<script type="text/javascript">
$(function(){
	$('#tabs').tabs({
		cookie: { expires: 30 }
	});
	
	$('.accordion').accordion({
		animated: 'bounceslide',
		active: false,
		autoHeight: false,
		collapsible: true
	});
	
	$("#date_begin").datepicker();
	$("#date_end").datepicker();
});
</script>
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"><%IT:Localizer string="exhibition" /%></a></li>
		<%if id%>
			<li><a href="#tabs-2"><%IT:Localizer string="images" /%></a></li>
		<%/if%>
	</ul>
	<div id="tabs-1">
		<table cellpadding="3" cellspacing="0" class="form">
			<tr>
				<td width="100" align="right" nowrap><strong><label for="title"><%IT:Localizer string="title" /%>:</label></strong></td>
				<td><%it:input type="text" name="title" id="title" class="inp" /%></td>
			</tr>
			<tr>
				<td align="right" nowrap><strong><label for="destination"><%IT:Localizer string="destination" /%>:</label></strong></td>
				<td><%it:input type="text" name="destination" id="destination" class="inp" /%></td>
			</tr>
			<tr>
				<td align="right" nowrap><strong><label for="abbreviation"><%IT:Localizer string="abbreviation" /%>:</label></strong></td>
				<td><%it:input type="text" name="abbreviation" id="abbreviation" class="inp3" /%></td>
			</tr>
			<tr>
				<td align="right" nowrap><strong><label for="date_begin"><%IT:Localizer string="date_begin" /%>:</label></strong></td>
				<td><%it:input type="text" name="date_begin" id="date_begin" class="inp inpdate" /%></td>
			</tr>
			<tr>
				<td align="right" nowrap><strong><label for="date_end"><%IT:Localizer string="date_end" /%>:</label></strong></td>
				<td><%it:input type="text" name="date_end" id="date_end" class="inp inpdate" /%></td>
			</tr>
			<tr>
				<td colspan="2" align="right" class="form_buttons">
					<%it:input type="submit" name="save" id="save" value="<%IT:Localizer string="btn_save" /%>" class="butt" /%>
					<%it:input type="reset" name="reset" id="reset" value="<%IT:Localizer string="btn_reset" /%>" class="butt" /%>
					<%it:input type="button" name="close" id="close" value="<%IT:Localizer string="btn_close" /%>" class="butt" /%>
				</td>
			</tr>
		</table>
	</div>
	<%if id%>
		<div id="tabs-2">
			<div class="buttons_top">
				<%if image_show_remove%>
					<%it:input type="button" name="delete_selected_img" id="delete_selected_img" value="<%IT:Localizer string="btn_delete_selected" /%>" class="butt" confirm="<%IT:Localizer string="delete_confirm" /%>" /%>
				<%/if%>
				<%it:input type="button" name="add_img" id="add_img" value="<%IT:Localizer string="btn_add" /%>" class="butt" /%>
			</div>
			<table cellpadding="0" cellspacing="0" class="maxw">
			<tr>
				<td class="navi_subbgr">
					<%it:dbnavigator.images title="<%IT:Localizer string="exhibition_images" /%>" enumerated="no" checkable="yes" clicklink="<%=clickLink_img%>id=" popuped="no" /%>
				</td>
			</tr>
			</table>
			<div class="buttons_bottom">
				<%if image_show_remove%>
					<%it:input type="button" name="delete_selected_img" id="delete_selected_img" value="<%IT:Localizer string="btn_delete_selected" /%>" class="butt" confirm="<%IT:Localizer string="delete_confirm" /%>" /%>
				<%/if%>
				<%it:input type="button" name="add_img" id="add_img" value="<%IT:Localizer string="btn_add" /%>" class="butt" /%>
			</div>
		</div>
	<%/if%>
</div>
<%IT:form end="true" /%>

<%IT:form id="priority" begin="true" method="post" action="" /%>
&nbsp;
<%IT:form end="true" /%>
