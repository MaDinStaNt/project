<%if have_tabs%>
	<table cellpadding="0" cellspacing="0" border="0" class="tab_control">
	<tr>
	<td class="small_font"><img src="<%=IMAGES%>tab_control/<%=img_left%>" alt="" border="0" /></td>
	<%for tab_cnt%>
		<%if tab_active%>
			<td class="<%=tab_class%>"><span class="tab_active_tab"><%=tab_name%></span></td>
		<%else%>
			<td class="<%=tab_class%>"><a href="<%=tab_link_url%>" class="tab_link_style"><%=tab_name%></a></td>
		<%/if%>
		<%if !last_tab%>
			<td class="small_font"><img src="<%=IMAGES%>tab_control/<%=img_middle%>" alt="" border="0" /></td>
		<%/if%>
	<%/for%>
	<td class="small_font"><img src="<%=IMAGES%>tab_control/<%=img_right%>" alt="" border="0" /></td>
	</tr>
	</table>
<%/if%>