<%IT:form id="<%=_table%>" begin="true" method="post" action="" /%>
<%it:input type="hidden" name="id" priority="template,get,post" /%>
<table cellpadding="0" cellspacing="0" class="maxw note">
<tr>
	<td><span class="note_title"><%it:Localizer string="warning_obligatory_fields" /%>
	</td>
</tr>
</table>
<table cellpadding="3" cellspacing="0" class="form">
<tr>
	<td width="100" align="right" nowrap><strong><label for="id"><%IT:Localizer string="id" /%>:</label></strong></td>
	<td><%it:input type="text" name="id" id="id" class="inp" /%></td>
</tr>
<tr>
	<td width="100" align="right" nowrap><strong><label for="title"><%IT:Localizer string="title" /%>:</label></strong></td>
	<td><%it:input type="text" name="title" id="title" class="inp" /%></td>
</tr>
<tr>
	<td align="right" nowrap><strong><label for="description"><%IT:Localizer string="description" /%>:</label></strong></td>
	<td><%it:input type="text" name="description" id="description" class="inp" /%></td>
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
