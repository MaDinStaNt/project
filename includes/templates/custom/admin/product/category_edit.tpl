<script type="text/javascript">
$(function(){
	
	$("#uri").attr({'readonly' : true}).click(function () {
		call('Inputs', 'generate_uri', ['<%=_table%>', $("#title").val()]).listen(set_uri);
	});
	
});

function set_uri(data){
	if(data.errors !== false){
		alert(data.errors);
		return false;
	}
	
	$("#uri").val(data.uri);
	return true;
}
</script>
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
	<h2><%IT:Localizer string="product_category" /%>: <%=title%></h2>
	<%else%>
	<h2><%IT:Localizer string="new_product_category" /%></h2>
	<%/if%>
</td>
<tr>
	<td width="100" align="right" nowrap><strong><label for="title"><%IT:Localizer string="title" /%>:</label></strong></td>
	<td><%it:input type="text" name="title" id="title" class="inp" /%></td>
</tr>
<tr>
	<td align="right" nowrap><strong><label for="uri"><%IT:Localizer string="uri" /%>:</label></strong></td>
	<td><%it:input type="text" name="uri" id="uri" class="inp" /%></td>
</tr>
<%if image_filename%>
<tr>
	<td align="right" nowrap>&nbsp;</td>
	<td><img src="<%=HTTP%>pub/production/category/<%=id%>/200x200/<%=image_filename%>" title="" alt="" /></td>
</tr>
<%/if%>
<tr>
	<%if id%>
		<td align="right" nowrap><label for="image_filename"><%IT:Localizer string="image_filename" /%>:</label></td>
	<%else%>
		<td align="right" nowrap><strong><label for="image_filename"><%IT:Localizer string="image_filename" /%>:</strong></label></td>
	<%/if%>
	<td><%it:input type="file" name="image_filename" id="image_filename" class="inp" /%></td>
</tr>
<tr>
	<td align="right" nowrap><label for="description"><%IT:Localizer string="description" /%>:</label></td>
	<td><%it:input type="textarea" name="description" id="description" class="inp" /%></td>
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
