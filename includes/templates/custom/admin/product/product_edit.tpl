<script type="text/javascript" src="<%=JS%>jquery/jquery.timer.js"></script>
<style type="text/css">
	/*demo page css*/
	.ui-widget {font-family:Tahoma,Arial,Helvetica,sans-serif;font-size:11px;}
</style>
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
	<h2><%IT:Localizer string="product" /%>: <%=title%></h2>
	<%else%>
	<h2><%IT:Localizer string="new_product" /%></h2>
	<%/if%>
</td>
</table>
<script type="text/javascript" language="javascript" src="<%=JS%>jquery/jquery.cookie.js"></script>
<script type="text/javascript">
$(function(){
	$('#tabs').tabs({
		cookie: { expires: 30 }
	});
	
	$('.accordion').accordion({
		animated: 'bounceslide',
		active: false,
		autoHeight: false,
		collapsible: true
	});
	
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
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"><%IT:Localizer string="product" /%></a></li>
		<%if id%>
			<li><a href="#tabs-2"><%IT:Localizer string="images" /%></a></li>
			<li><a href="#tabs-3"><%IT:Localizer string="technical_data" /%></a></li>
			<li><a href="#tabs-4"><%IT:Localizer string="equipment" /%></a></li>
		<%/if%>
	</ul>
	<div id="tabs-1">
		<table cellpadding="3" cellspacing="0" class="form">
			<tr>
				<td width="100" align="right" nowrap><strong><label for="product_category_id"><%IT:Localizer string="product_category" /%>:</label></strong></td>
				<td><%it:input type="select" name="product_category_id" id="product_category_id" class="inp" /%></td>
			</tr>
			<tr>
				<td width="100" align="right" nowrap><strong><label for="title"><%IT:Localizer string="title" /%>:</label></strong></td>
				<td><%it:input type="text" name="title" id="title" class="inp" /%></td>
			</tr>
			<tr>
				<td align="right" nowrap><strong><label for="uri"><%IT:Localizer string="uri" /%>:</label></strong></td>
				<td><%it:input type="text" name="uri" id="uri" class="inp" /%></td>
			</tr>
			<tr>
				<td align="right" nowrap><strong><label for="article"><%IT:Localizer string="article" /%>:</strong></label></td>
				<td><%it:input type="text" name="article" id="article" class="inp" /%></td>
			</tr>
			<tr>
				<td align="right" nowrap><label for="brief_description"><%IT:Localizer string="brief_description" /%>:</label></td>
				<td><%it:input type="textarea" name="brief_description" id="brief_description" class="inp" /%></td>
			</tr>
			<tr>
				<td align="right" nowrap><label for="video_link"><%IT:Localizer string="video_link" /%>:</label></td>
				<td><%it:input type="text" name="video_link" id="video_link" class="inp" /%></td>
			</tr>
			<tr>
				<td align="right" nowrap><label for="desc_filename"><%IT:Localizer string="desc_filename" /%>: <br />(<%IT:Localizer string="format_pdf_doc" /%>)</label></td>
				<td><%it:input type="file" name="desc_filename" id="desc_filename" class="inp" /%>
					<%if desc_filename%>
						<br /><a href="<%=HTTP%>pub/production/product/<%=id%>/<%=desc_filename%>" target="_blank" title="<%IT:Localizer string="download_file" /%>"><%IT:Localizer string="download" /%></a>
						<br /><%it:input type="checkbox" name="delete_desc_filename" id="delete_desc_filename" value="1" /%> <label for="delete_desc_filename"><%IT:Localizer string="delete_file" /%></label>
					<%/if%>
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><label for="instruct_filename"><%IT:Localizer string="instruct_filename" /%>: <br />(<%IT:Localizer string="format_pdf_doc" /%>)</label></td>
				<td><%it:input type="file" name="instruct_filename" id="instruct_filename" class="inp" /%>
					<%if instruct_filename%>
						<br /><a href="<%=HTTP%>pub/production/product/<%=id%>/<%=instruct_filename%>" target="_blank" title="<%IT:Localizer string="download_file" /%>"><%IT:Localizer string="download" /%></a>
						<br /><%it:input type="checkbox" name="delete_instruct_filename" id="delete_instruct_filename" value="1" /%> <label for="delete_instruct_filename"><%IT:Localizer string="delete_file" /%></label>
					<%/if%>
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><label for="description"><%IT:Localizer string="description" /%>:</label></td>
				<td><%it:input type="htmlarea" name="description" id="description" class="inp" /%></td>
			</tr>
			<tr>
				<td colspan="2" align="right" class="form_buttons">
					<%it:input type="submit" name="save" id="save" value="<%IT:Localizer string="btn_save" /%>" class="butt" /%>
					<%it:input type="reset" name="reset" id="reset" value="<%IT:Localizer string="btn_reset" /%>" class="butt" /%>
					<%it:input type="button" name="close" id="close" value="<%IT:Localizer string="btn_close" /%>" class="butt" /%>
				</td>
			</tr>
		</table>
	</div>
	<%if id%>
		<div id="tabs-2">
			<div class="buttons_top">
				<%if image_show_remove%>
					<%it:input type="button" name="delete_selected_img" id="delete_selected_img" value="<%IT:Localizer string="btn_delete_selected" /%>" class="butt" confirm="<%IT:Localizer string="delete_confirm" /%>" /%>
				<%/if%>
				<%it:input type="button" name="add_img" id="add_img" value="<%IT:Localizer string="btn_add" /%>" class="butt" /%>
			</div>
			<table cellpadding="0" cellspacing="0" class="maxw">
			<tr>
				<td class="navi_subbgr">
					<%it:dbnavigator.images title="<%IT:Localizer string="product_images" /%>" enumerated="no" checkable="yes" clicklink="<%=clickLink_img%>id=" popuped="no" /%>
				</td>
			</tr>
			</table>
			<div class="buttons_bottom">
				<%if image_show_remove%>
					<%it:input type="button" name="delete_selected_img" id="delete_selected_img" value="<%IT:Localizer string="btn_delete_selected" /%>" class="butt" confirm="<%IT:Localizer string="delete_confirm" /%>" /%>
				<%/if%>
				<%it:input type="button" name="add_img" id="add_img" value="<%IT:Localizer string="btn_add" /%>" class="butt" /%>
			</div>
		</div>
		<div id="tabs-3">
			<div class="buttons_top">
				<%if td_show_remove%>
					<%it:input type="button" name="delete_selected_td" id="delete_selected_td" value="<%IT:Localizer string="btn_delete_selected" /%>" class="butt" confirm="<%IT:Localizer string="delete_confirm" /%>" /%>
				<%/if%>
				<%it:input type="button" name="add_td" id="add_td" value="<%IT:Localizer string="btn_add" /%>" class="butt" /%>
			</div>
			<table cellpadding="0" cellspacing="0" class="maxw">
			<tr>
				<td class="navi_subbgr">
					<%it:dbnavigator.techdata title="<%IT:Localizer string="product_technical_data" /%>" enumerated="no" checkable="yes" clicklink="<%=clickLink_td%>id=" popuped="no" /%>
				</td>
			</tr>
			</table>
			<div class="buttons_bottom">
				<%if td_show_remove%>
					<%it:input type="button" name="delete_selected_td" id="delete_selected_td" value="<%IT:Localizer string="btn_delete_selected" /%>" class="butt" confirm="<%IT:Localizer string="delete_confirm" /%>" /%>
				<%/if%>
				<%it:input type="button" name="add_td" id="add_td" value="<%IT:Localizer string="btn_add" /%>" class="butt" /%>
			</div>
		</div>
		<div id="tabs-4">
			<div class="buttons_top">
				<%if eq_show_remove%>
					<%it:input type="button" name="delete_selected_eq" id="delete_selected_eq" value="<%IT:Localizer string="btn_delete_selected" /%>" class="butt" confirm="<%IT:Localizer string="delete_confirm" /%>" /%>
				<%/if%>
				<%it:input type="button" name="add_eq" id="add_eq" value="<%IT:Localizer string="btn_add" /%>" class="butt" /%>
			</div>
			<table cellpadding="0" cellspacing="0" class="maxw">
			<tr>
				<td class="navi_subbgr">
					<%it:dbnavigator.equipment title="<%IT:Localizer string="product_equipment" /%>" enumerated="no" checkable="yes" clicklink="<%=clickLink_eq%>id=" popuped="no" /%>
				</td>
			</tr>
			</table>
			<div class="buttons_bottom">
				<%if eq_show_remove%>
					<%it:input type="button" name="delete_selected_eq" id="delete_selected_eq" value="<%IT:Localizer string="btn_delete_selected" /%>" class="butt" confirm="<%IT:Localizer string="delete_confirm" /%>" /%>
				<%/if%>
				<%it:input type="button" name="add_eq" id="add_eq" value="<%IT:Localizer string="btn_add" /%>" class="butt" /%>
			</div>
		</div>
	<%/if%>
</div>
<%IT:form end="true" /%>
<%IT:form id="priority" begin="true" method="post" action="" /%>
&nbsp;
<%IT:form end="true" /%>
