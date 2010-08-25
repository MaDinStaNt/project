<%IT:form id="<%=_table%>" begin="true" method="post" action="" /%>
<div class="buttons_top">
</div>
<table cellpadding="0" cellspacing="0" class="maxw">
<tr>
	<td class="navi_subbgr">
		<%it:dbnavigator.objects title="User roles" enumerated="no" checkable="yes" clicklink="<%=clickLink%>id=" popuped="no" /%>
	</td>
</tr>
</table>
<div class="buttons_bottom">
</div>
<%IT:form end="true" /%>
