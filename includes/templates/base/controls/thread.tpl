<table cellpadding="0" cellspacing="0" border="0" class="thread_control" width="300" >
<tr>
	<td>
		<table cellpadding="0" cellspacing="0" border="0" width="280" class="progress_bar" id="p<%=id%>_progress_bar" >
		<tr>
			<td align="left"><img id="p<%=id%>_progress" src="<%=IMAGES%>admin/progress.gif" alt="" border="0" height="18" width="1" /></td>
		</tr>
		</table>
	</td>
	<td class="progress_text" id="p<%=id%>_text" >50%</td>
	<td id="p<%=id%>_button_area">&nbsp;
		
	</td>
</td></tr>
<%if thread_details%>
<tr><td colspan="3" class="progress_details">
<textarea id="p<%=id%>_progress_details_area" class="progress_details_area_style" ></textarea>
</td></tr>
<%/if%>
</table>
<script language="JavaScript" type="text/javascript">
	t<%=id%> = new CThreadControl(<%=id%>);
	t<%=id%>.redirect = '<%=redirect%>';
	t<%=id%>.show_details = 
	<%if thread_details%>
		true;
	<%else%>
		false;
	<%/if%>
</script>