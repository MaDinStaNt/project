<%IT:form id="<%=_table%>" begin="true" method="post" action="" /%>
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
	<h2><%IT:Localizer string="image" /%>: <%=title%></h2>
	<%else%>
	<h2>New <%IT:Localizer string="image" /%></h2>
	<%/if%>
</td>
</table>
<table cellpadding="3" cellspacing="0" class="form">
<tr>
	<td width="100" align="right" nowrap><strong><label for="title"><%IT:Localizer string="title" /%>:</label></strong></td>
	<td><%it:input type="text" name="title" id="title" class="inp"/%></td>
</tr>
<tr>
	<td width="100" align="right" nowrap><strong><label for="system_key"><%IT:Localizer string="system_key" /%>:</label></strong></td>
	<td>
		<%if id%>
			<%it:input type="text" name="system_key" id="system_key" readonly="true" class="inp"/%>
		<%else%>
			<%it:input type="text" name="system_key" id="system_key" class="inp"/%>
		<%/if%>
	</td>
</tr>
<tr>
	<td width="100" align="right" nowrap><strong><label for="path"><%IT:Localizer string="path" /%>:</label></strong></td>
	<td>
		<%if id%>
			<%it:input type="text" name="path" id="path" readonly="true" class="inp"/%>
		<%else%>
			<%it:input type="text" name="path" id="path" class="inp"/%>
		<%/if%>
		<br /><i>e.g. pub/user/{iser_id}/avatar/</i>
	</td>
</tr>
<tr>
	<td colspan="2" align="right" class="form_buttons">
		<%it:input type="submit" name="save" id="save" value="<%IT:Localizer string="btn_save" /%>" class="butt" /%>
		<%it:input type="button" name="close" id="close" value="<%IT:Localizer string="btn_close" /%>" class="butt" /%>
	</td>
</tr>
</table>
<%if id%>
	<div class="buttons_top">
		<%if image_size_show_remove%>
			<%it:input type="button" name="delete_image_size" id="delete_image_size" value="<%IT:Localizer string="btn_delete_selected" /%>" class="butt" confirm="<%IT:Localizer string="confirm_delete" /%>" /%>
		<%/if%>
		<%it:input type="button" name="add_image_size" id="add_image_size" value="<%IT:Localizer string="btn_add" /%>" class="butt" /%>
	</div>
	<table cellpadding="0" cellspacing="0" class="maxw">
	<tr>
		<td class="navi_subbgr">
			<%it:dbnavigator.image_size title="<%IT:Localizer string="image_sizes" /%>" enumerated="no" checkable="yes" clicklink="<%=clickLink%>id=" popuped="no" /%>
		</td>
	</tr>
	</table>
	<div class="buttons_bottom">
		<%if image_size_show_remove%>
			<%it:input type="button" name="delete_image_size" id="delete_image_size" value="<%IT:Localizer string="btn_delete_selected" /%>" class="butt" confirm="<%IT:Localizer string="confirm_delete" /%>" /%>
		<%/if%>
		<%it:input type="button" name="add_image_size" id="add_image_size" value="<%IT:Localizer string="btn_add" /%>" class="butt" /%>
	</div>	
<%/if%>	
<%IT:form end="true" /%>
