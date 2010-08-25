<%IT:form id="<%=_table%>" begin="true" method="post" action="" /%>
<div class="buttons_top">
	<%it:input type="button" name="delete_selected" id="delete_selected" value="<%IT:Localizer string="delete_selected" /%>" class="butt" confirm="Are you sure you want to delete selected items?" /%>
</div>
<table cellpadding="0" cellspacing="0" class="maxw">
<tr>
	<td class="navi_subbgr">
		<%it:dbnavigator.objects title="Localizer strings" enumerated="no" checkable="yes" clicklink="<%=clickLink%>id=" popuped="no" /%>
	</td>
</tr>
</table>
<div class="buttons_bottom">
	<%it:input type="button" name="delete_selected" id="delete_selected" value="<%IT:Localizer string="delete_selected" /%>" class="butt" confirm="Are you sure you want to delete selected items?" /%>
</div>
<%IT:form end="true" /%>
