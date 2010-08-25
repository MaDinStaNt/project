<%IT:form id="user_edit" begin="true" method="post" action="" /%>
<%it:input type="hidden" name="id" priority="template,get,post" /%>
<table cellpadding="0" cellspacing="0" class="maxw note">
<tr>
	<td><span class="note_title">Warning!</span> Fields marked with <strong>bold</strong> are obligatory
	</td>
</tr>
</table>
<table cellpadding="3" cellspacing="0" class="form">
<td colspan="2">
	<%if id%>
	<h2>User: <%=name%> (<%=email%>)</h2>
	<%else%>
	<h2>New User</h2>
	<%/if%>
</td>
<tr>
	<td width="100" align="right" nowrap><strong><label for="email"><%IT:Localizer string="email" /%>:</label></strong></td>
	<td><%it:input type="text" name="email" id="email" class="inp" /%></td>
</tr>
<tr>
	<%if id%>
		<td align="right" nowrap><label for="password"><%IT:Localizer string="password" /%>:</label></td>
	<%else%>
		<td align="right" nowrap><strong><label for="password"><%IT:Localizer string="password" /%>:</label></strong></td>
	<%/if%>
	<td><%it:input type="password" name="password" id="password" class="inp" /%></td>
</tr>
<tr>
	<td align="right" nowrap><strong><label for="name"><%IT:Localizer string="name" /%>:</label></strong></td>
	<td><%it:input type="text" name="name" id="name" class="inp" /%></td>
</tr>
<tr>
	<td align="right" nowrap><label for="address"><%IT:Localizer string="address" /%>:</label></td>
	<td><%it:input type="text" name="address" id="address" class="inp" /%></td>
</tr>
<tr>
	<td align="right" nowrap><label for="city"><%IT:Localizer string="city" /%>:</label></td>
	<td><%it:input type="text" name="city" id="city" class="inp" /%></td>
</tr>
<tr>
	<td align="right" nowrap><%IT:Localizer string="state_id" /%>:</td>
	<td><%it:input type="select" name="state_id" id="state_id" class="inpsel" /%></td>
</tr>
<tr>
	<td align="right" nowrap><label for="zip"><%IT:Localizer string="zip" /%>:</label></td>
	<td><%it:input type="text" name="zip" id="zip" class="inp_price" /%></td>
</tr>
<tr>
	<td align="right" nowrap><label for="company"><%IT:Localizer string="company" /%>:</label></td>
	<td><%it:input type="text" name="company" id="company" class="inp" /%></td>
</tr>
<tr>
	<td align="right" nowrap><strong><%IT:Localizer string="user_role_id" /%>:</strong></td>
	<td><%it:input type="select" name="user_role_id" id="user_role_id" class="inpsel" /%></td>
</tr>
<tr>
	<td align="right" nowrap><strong><label for="status"><%IT:Localizer string="status" /%>:</label></strong></td>
	<td><%it:input type="select" name="status" id="status" class="inpsel" /%></td>
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
