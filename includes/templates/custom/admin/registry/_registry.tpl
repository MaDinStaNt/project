<%IT:form name="registry_form" begin="true" enctype="multipart/form-data" action="<%=HTTP%><%=action_form%>" /%>
<input type="hidden" name="MAX_FILE_SIZE" value="<%#MAX_FILE_SIZE%>">
<%IT:input type="hidden" name="path_id" priority="template,post,get" /%>
<%IT:input type="hidden" name="create_new_registry_item" priority="template,post,get" /%>

<script language="JavaScript" type="text/javascript"><!--
        function create_new_registry_item(path_id) {
                document.forms['registry_form'].elements['param2'].value = 'add_subgroup';
                document.forms['registry_form'].submit();
        }
        function set_id_item(path_id) {
                document.forms['registry_form'].elements['path_id'].value = path_id;
                document.forms['registry_form'].submit();
        }
//-->
</script>
<table cellpadding="0" cellspacing="0" width="100%" height="400">
<tr>
<td valign="top" align="left" width="30%" style="border-right: 1px solid #E1E1E1;">
    <table border="0" cellpadding="10" cellspacing="0" class="maxw">
    <tr>
        <td align="left" valign="top" class="tree_out">
        <%it:TreeView mouse_action="nodesel(event);" action="%qs%=%id%" /%>
        </td>
    </tr>
    </table>
</td>
<td>
	&nbsp;
</td>
<td valign="top">
<%if path_editor_mode%>
	<%IT:SimpleArrayOutput block_begin="<div class=&quot;error&quot;><table cellspacing=&quot;3&quot; cellpadding=&quot;3&quot;><tr><td align=&quot;left&quot;>" block_end="</td></tr></table></div>" item_begin="" item_end="<br />" array="_registry_messages" /%>
	<%IT:SimpleArrayOutput block_begin="<div class=&quot;info&quot;><table cellspacing=&quot;3&quot; cellpadding=&quot;3&quot;><tr><td align=&quot;left&quot;>" block_end="</td></tr></table></div>" item_begin="" item_end="<br />" array="_registry_info" /%>
	<table cellpadding="0" cellspacing="0" class="maxw note">
	<tr>
		<td><span class="note_title">Warning!</span> Fields marked with <strong>bold</strong> are obligatory
		</td>
	</tr>
	</table>
    <table cellspacing="0" cellpadding="0" class="maxw form">
    <tr>
        <td>
            <%if current_path_parent_id%>
            <table cellspacing="0" cellpadding="0" class="maxw maxh">
            <tr>
                <td><h2><%=current_path_description%></h2></td>
            </tr>
            <%/if%>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table border="0" cellpadding="2" cellspacing="2">
            <%if current_path_parent_id%>
                <%if current_path_edit_group%>
                <tr>
                    <td align="right" width="150"><b><%=current_path_description_tip%>:</b><span class="acc">*</span></td>
                    <td align="left"><%IT:input type="text" name="current_path_description" size="40" class="inp" priority="template,post,get" /%></td>
                </tr>
                <%/if%>
            <%/if%>
            <%if value_count%>
            <%for value_count%>
                <tr>
                <td align="right" width="150">
                    <label for="value_v_<%=value_name%>">
                    <%if value_validator_req%>
                    <b><%=value_description%>:</b>
                    <%else%>
                    <%=value_description%>:
                    <%/if%>
                    </label>
                </td>
                <td  align="left">
                    <%if ch%>
	                    <%IT:input type="<%=value_edit_type%>" name="value_v_<%=value_name%>" id="value_v_<%=value_name%>" priority="template,post,get" <%=value_input_add%> /%>
                    <%else%>
                        <%if htm%>
                            <%IT:input type="<%=value_edit_type%>" class="inp" name="value_v_<%=value_name%>" id="value_v_<%=value_name%>" style="width:100%" priority="template,post,get" <%=value_input_add%> /%>
                        <%else%>
                            <%IT:input type="<%=value_edit_type%>" class="inp" name="value_v_<%=value_name%>" id="value_v_<%=value_name%>" priority="template,post,get" <%=value_input_add%> /%>
                        <%/if%>
                    <%/if%>
                    <%if value_type_file%>
                            <%if value_value%>
                                    <br /><a href="<%=REGISTRY_WEB%><%=current_path_path_id%>/<%=value_value%>" target="_blank">download</a>
                                    <%if !value_validator_req%>
                                            <%it:input type="checkbox" name="del_<%=value_value_id%>" id="del_<%=value_value_id%>" value="1" /%> Delete
                                    <%/if%>
                            <%/if%>
                    <%/if%>
                    <%if value_type_image%>
                            <%if value_value%>
                            <br /><a href="<%=REGISTRY_WEB%><%=current_path_path_id%>/<%=value_value%>" target="_blank"><img src="<%=REGISTRY_WEB%><%=current_path_path_id%>/<%=value_value%>" width="60" border="0"></a>
                                    <%if !value_validator_req%>
                                    <%it:input type="checkbox" name="del_<%=value_value_id%>" id="del_<%=value_value_id%>" value="1" /%> Delete
                                    <%/if%>
                            <%/if%>
                    <%/if%>
                </td>
            </tr>
            <%/for%>
            <%/if%>
            <tr>
		    <tr>
		        <td colspan="2" align="right" class="form_buttons">
		            <%if value_count%>
		            <%if current_path_parent_id%>
		                    <%if !current_path_edit_group%>
		                            <%IT:input type="button" class="butt hand" value="Save" /%>
		                    <%/if%>
		            <%/if%>
		            <%/if%>
		
		            <%if current_path_parent_id%>
		                    <%if current_path_edit_group%>
		                            <%IT:input type="submit" value="Save" class="butt hand" /%>
		                            &nbsp;<%IT:input type="button" name="delete_group" confirm="Are you sure?" value="Delete" class="butt hand" /%>
		                    <%/if%>
		            <%/if%>
		        </td>
		    </tr>
            </table>
        </td>
    </tr>
    </table>
<%else%>
Please select an item to modify
<%/if%>
</td>
</tr>
</table>
<%IT:form end="true" /%>