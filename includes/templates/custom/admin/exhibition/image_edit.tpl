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
	<h2><%IT:Localizer string="exhibition_image" /%>: <%=title%></h2>
	<%else%>
	<h2><%IT:Localizer string="new_exhibition_image" /%></h2>
	<%/if%>
</td>
</table>
<table cellpadding="3" cellspacing="0" class="form">
	<tr>
		<td width="100" align="right" nowrap><strong><label for="exhibition_id"><%IT:Localizer string="exhibition" /%>:</label></strong></td>
		<td><%it:input type="select" name="exhibition_id" id="exhibition_id" readonly="true" class="inp" /%></td>
	</tr>
	<tr>
		<td width="100" align="right" nowrap><strong><label for="title"><%IT:Localizer string="title" /%>:</label></strong></td>
		<td><%it:input type="text" name="title" id="title" class="inp" /%></td>
	</tr>
	<%if image_filename%>
		<tr>
			<td width="100" align="right" nowrap>&nbsp;</td>
			<td><img src="<%=HTTP%>pub/exhibition/<%=exhibition_id%>/150x150/<%=image_filename%>" /></td>
		</tr>
	<%/if%>
	<tr>
		<%if image_filename%>
			<td width="100" align="right" nowrap><label for="image_filename"><%IT:Localizer string="image_filename" /%>:</label></td>
		<%else%>
			<td width="100" align="right" nowrap><strong><label for="image_filename"><%IT:Localizer string="image_filename" /%>:</label></strong></td>
		<%/if%>
		<td><%it:input type="file" name="image_filename" id="image_filename" class="inp" /%></td>
	</tr>
	<tr>
		<td align="right" nowrap><label for="description"><%IT:Localizer string="description" /%>:</label></td>
		<td><%it:input type="textarea" name="description" id="description" class="inp" /%></td>
	</tr>
	<tr>
		<td align="right" nowrap><label for="is_core"><%IT:Localizer string="is_core" /%>:</label></td>
		<td><%it:input type="checkbox" name="is_core" id="is_core" value="1" class="" /%></td>
	</tr>
	<tr>
		<td colspan="2" align="right" class="form_buttons">
			<%it:input type="submit" name="save" id="save" value="<%IT:Localizer string="btn_save" /%>" class="butt" /%>
			<%it:input type="reset" name="reset" id="reset" value="<%IT:Localizer string="btn_reset" /%>" class="butt" /%>
			<%it:input type="button" name="close" id="close" value="<%IT:Localizer string="btn_close" /%>" class="butt" /%>
		</td>
	</tr>
</table>
<%IT:form end="true" /%>