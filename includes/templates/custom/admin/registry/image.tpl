<%IT:form id="<%=_table%>" begin="true" method="post" action="" /%>
<div class="buttons_top">
	<%if image_show_remove%>
		<%it:input type="button" name="delete_selected" id="delete_selected" value="<%IT:Localizer string="btn_delete_selected" /%>" class="butt" confirm="<%IT:Localizer string="confirm_delete" /%>" /%>
	<%/if%>
	<%it:input type="button" name="add" id="add" value="<%IT:Localizer string="btn_add" /%>" class="butt" /%>
</div>
<table cellpadding="0" cellspacing="0" class="maxw">
<tr>
	<td class="navi_subbgr">
		<%it:dbnavigator.objects title="<%IT:Localizer string="images" /%>" enumerated="no" checkable="yes" clicklink="<%=clickLink%>id=" popuped="no" /%>
	</td>
</tr>
</table>
<div class="buttons_bottom">
	<%if image_show_remove%>
		<%it:input type="button" name="delete_selected" id="delete_selected" value="<%IT:Localizer string="btn_delete_selected" /%>" class="butt" confirm="<%IT:Localizer string="confirm_delete" /%>" /%>
	<%/if%>
	<%it:input type="button" name="add" id="add" value="<%IT:Localizer string="btn_add" /%>" class="butt" /%>
</div>
<%IT:form end="true" /%>
