<div align="center" style="padding: 10px;">
<%IT:SimpleArrayOutput block_begin="<div style=&quot;padding-top:5px; padding-bottom:5px&quot;><div class=&quot;error&quot;>" block_end="</div></div>" item_begin="" item_end="<br />" array="_errors" /%>
<%IT:SimpleArrayOutput block_begin="<div style=&quot;padding-top:5px; padding-bottom:5px&quot;><div class=&quot;info&quot;>" block_end="</div></div>" item_begin="" item_end="<br />" array="_info" /%>
<h2>Change Password</h2>
<%it:form begin="true" name="change_password" action="" /%>
<table cellpadding="2" cellspacing="1" width="450">
<tr>
	<td width="150" class="tt" nowrap="true" align="right"><strong><label for="old_password"><%IT:Localizer string="old_password" /%>:</label></strong></td>
	<td class="tc"><%it:input type="password" name="old_password" id="old_password" class="sel size4" priority="template, post,get" /%></td>
</tr>
<tr>
	<td width="150" class="tt" nowrap="true" align="right"><strong><label for="new_password"><%IT:Localizer string="new_password" /%>:</label></strong></td>
	<td class="tc"><%it:input type="password" name="new_password" id="new_password" class="sel size4" priority="template, post,get" /%></td>
</tr>
<tr>
	<td width="150" class="tt" nowrap="true" align="right"><strong><label for="re_password"><%IT:Localizer string="re_password" /%>:</label></strong></td>
	<td class="tc"><%it:input type="password" name="re_password" id="re_password" class="sel size4" priority="template, post,get" /%></td>
</tr>
<tr>
	<td></td>
	<td align="left">
		<%it:input type="submit" name="change" value="Change" class="but" /%>
	</td>
</tr>
</table>
<%it:form end="true" /%>
</div>