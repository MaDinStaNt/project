<%IT:form id="login" begin="true" method="post" action="" /%>
<table class="form form_border" cellspacing="0" cellpadding="3" align="center">
<tr>
	<td align="center" colspan="2">
		<div>
		<strong><%it:Localizer string="admin_panel" /%></strong>
		</div>
	</td>
</tr>
<tr>
	<td width="100"><label for="email"><strong><%it:Localizer string="email" /%>:</strong></label></td>
	<td><%it:input class="inp2" type="text" name="email" id="email" priority="template, post, get" /%></td>
</tr>
<tr>
	<td><label for="password"><strong><%it:Localizer string="password" /%>:</strong></label></td>
	<td><%it:input class="inp2" type="password" name="password" id="password" priority="template, post, get" /%></td>
</tr>
<tr>
	<td></td>
	<td>
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td><%it:input type="checkbox" id="remember_me" name="remember_me" value="1" /%></td>
			<td><label for="remember_me"> - <%it:Localizer string="remember_me" /%></label></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td align="right" colspan="2">
		<%it:input class="butt" type="reset" value="<%it:Localizer string="btn_reset" /%>" /%>
		<%it:input class="butt" type="submit" value="<%it:Localizer string="btn_log_in" /%>" /%>
	</td>
</tr>
</table>
<%IT:form end="true" /%>
