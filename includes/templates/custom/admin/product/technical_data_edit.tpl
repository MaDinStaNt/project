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
	<td width="100" align="right" nowrap><strong><label for="product_id"><%IT:Localizer string="product_id" /%>:</label></strong></td>
	<td><%it:input type="select" name="product_id" id="product_id" readonly="true" class="inp" /%></td>
</tr>
<tr>
	<td width="100" align="right" valign="top"><strong style="padding-top:5px;"><label for="technical_data"><%IT:Localizer string="technical_data" /%>:</label></strong></td>
	<td><%it:input type="text" name="technical_data" id="technical_data" class="inp2" style="vertical-align:top;" /%> <span style="vertical-align:top;">:</span> <%it:input type="textarea" name="value" id="value" style="vertical-align:top;" class="inp" /%></td>
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
