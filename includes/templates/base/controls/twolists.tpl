<table cellpadding="0"  cellspacing="0">
	<tr>
		<td>
			<%#left_group_title%><br />
			<%it:input type="hidden" id="<%=left_list_id%>_idx" name="<%=left_list_id%>_idx" /%>
			<select class="twolists" size="6" multiple="true" id="<%=left_list_id%>" name="<%=left_list_id%>" ondblclick="MoveItems( '<%=left_list_id%>', '<%=right_list_id%>', 1 )" >
				<%for left_items_cnt%>
					<option value="<%=left_val%>"><%=left_title%></option>
				<%/for%>
			</select>
		</td>
		<td class="twolists_space">
			<input class="butt hand" style="width:30px; margin:2px;" type="button" value="<%#right_button_text%>" onclick="MoveItems( '<%=left_list_id%>', '<%=right_list_id%>', 1 )" /><br />
			<input class="butt hand" style="width:30px; margin:2px;" type="button" value="<%#left_button_text%>" onclick="MoveItems( '<%=left_list_id%>', '<%=right_list_id%>', 2 )" />
		</td>
		<td>
			<%#right_group_title%><br />
			<%it:input type="hidden" id="<%=right_list_id%>_idx" name="<%=right_list_id%>_idx" /%> 
			</div>
			<select class="twolists" size="6" multiple="true" id="<%=right_list_id%>" name="<%=right_list_id%>" ondblclick="MoveItems( '<%=left_list_id%>', '<%=right_list_id%>', 2 )">
				<%for right_items_cnt%>
					<option value="<%=right_val%>"><%=right_title%></option>
				<%/for%>
			</select>
		</td>
		<td id="test">
		</td>
	</tr>
</table>