<%IT:form id="login" begin="true" method="post" action="" /%>
<table class="form form_border" cellspacing="0" cellpadding="3" align="center">
<tr>
	<td align="center" colspan="2">
		<div>
		<strong>Administration Panel</strong>
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
			<td><%it:input type="checkbox" id="form_store" name="form_store" value="1" /%></td>
			<td><label for="form_store"> - <%it:Localizer string="remember_me" /%></label></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td align="right" colspan="2">
		<%it:input class="butt" type="reset" value="Reset" /%>
		<%it:input class="butt" type="submit" value="Log In" /%>
	</td>
</tr>
</table>
<%IT:form end="true" /%>
