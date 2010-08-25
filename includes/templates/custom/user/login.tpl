<div align="center" style="padding: 200px;">
<div style="width: 300px;">
<%IT:SimpleArrayOutput block_begin="<div style=&quot;padding-top:5px; padding-bottom:5px&quot;><div class=&quot;error&quot;>" block_end="</div></div>" item_begin="" item_end="<br />" array="_errors" /%>
<%IT:SimpleArrayOutput block_begin="<div style=&quot;padding-top:5px; padding-bottom:5px&quot;><div class=&quot;info&quot;>" block_end="</div></div>" item_begin="" item_end="<br />" array="_info" /%>
<%it:form begin="true" name="login" action="" /%>
<table cellpadding="2" cellspacing="1" style="border: 1px solid grey;">
<tr>
	<td class="tt" nowrap="true" align="right"><strong><label for="email"><%IT:Localizer string="email" /%>:</label></strong></td>
	<td class="tc"><%it:input type="text" name="email" id="email" class="inp size3" /%></td>
</tr>
<tr>
	<td class="tt" nowrap="true" align="right"><strong><label for="password"><%IT:Localizer string="password" /%>:</label></strong></td>
	<td class="tc"><%it:input type="password" name="password" id="password" class="inp size3" /%></td>
</tr>
<tr>
	<td class="tt" nowrap="true" align="right"></td>
	<td class="tc">
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td><%it:input type="checkbox" id="form_store" name="form_store" value="1" /%></td>
			<td><label for="form_store"><%it:Localizer string="remember_me" /%></label></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td></td>
	<td align="left">
		<%it:input type="submit" name="login" value="<%it:Localizer string="login" /%>" class="but" /%>
	</td>
</tr>
</table>
<%it:form end="true" /%>	
</div>
</div>