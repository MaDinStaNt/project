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
	<h2>Параметры изображения</h2>
	<%else%>
	<h2>Новые параметры изображения</h2>
	<%/if%>
</td>
</table>
<table cellpadding="3" cellspacing="0" class="form">
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
	<td width="100" align="right" nowrap><strong><label for="image_width"><%IT:Localizer string="image_width" /%>:</label></strong></td>
	<td><%it:input type="text" name="image_width" id="image_width" class="inp" style="width: 50px;" /%></td>
</tr>
<tr>
	<td width="100" align="right" nowrap><strong><label for="image_height"><%IT:Localizer string="image_height" /%>:</label></strong></td>
	<td><%it:input type="text" name="image_height" id="image_height" class="inp" style="width: 50px;" /%></td>
</tr>
<tr>
	<td width="100" align="right" nowrap><strong><label for="thumbnail_method"><%IT:Localizer string="thumbnail_method" /%>:</label></strong></td>
	<td><%it:input type="select" name="thumbnail_method" id="thumbnail_method" class="inpsel" /%></td>
</tr>
<tr>
	<td colspan="2" align="right" class="form_buttons">
		<%it:input type="submit" name="save" id="save" value="<%IT:Localizer string="btn_save" /%>" class="butt" /%>
		<%it:input type="button" name="close" id="close" value="<%IT:Localizer string="btn_close" /%>" class="butt" /%>
	</td>
</tr>
</table>
<%IT:form end="true" /%>
