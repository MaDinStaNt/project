<%IT:form id="<%=_table%>" begin="true" method="post" action="" /%>
<%it:input type="hidden" name="id" priority="template,get,post" /%>
<table cellpadding="0" cellspacing="0" class="maxw note">
<tr>
	<td><span class="note_title">Warning!</span> Fields marked with <strong>bold</strong> are obligatory
	</td>
</tr>
</table>
<table cellpadding="3" cellspacing="0" class="form">
<tr>
	<td width="100" align="right" nowrap><strong><label for="name"><%IT:Localizer string="name" /%>:</label></strong></td>
	<td><%it:input type="text" name="name" id="name" readonly="true" class="inp" /%></td>
</tr>
<tr>
	<td align="right" nowrap><strong><label for="language_id"><%IT:Localizer string="language_id" /%>:</label></strong></td>
	<td><%it:input type="select" name="language_id" id="language_id" class="inpsel" /%></td>
</tr>
<tr>
	<td width="100" align="right" nowrap><strong><label for="value"><%IT:Localizer string="value" /%>:</label></strong></td>
	<td><%it:input type="text" name="value" id="value" class="inp" /%></td>
</tr>
<tr>
	<td colspan="2" align="right" class="form_buttons">
		<%it:input type="submit" name="save" id="save" value="<%IT:Localizer string="save" /%>" class="butt" /%>
		<%it:input type="reset" name="reset" id="reset" value="<%IT:Localizer string="reset" /%>" class="butt" /%>
		<%it:input type="button" name="close" id="close" value="<%IT:Localizer string="close" /%>" class="butt" /%>
	</td>
</tr>
</table>
<%IT:form end="true" /%>
