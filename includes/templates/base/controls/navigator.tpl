<%IF is_empty%>
<div class="navi_title"><%=empty_message%></div>
<%ELSE%>
    <table class="maxw" border="0" cellpadding="0" cellspacing="0">
    <tr>
    <td class="maxw navi_title" nowrap="nowrap"><span class="accent"><%#title%> (<%#max_size%> <%IT:Localizer string="total" /%>)</span></td>
    <%IF have_pages%>
    <%IF prev_pages%>
    <td valign="middle" class="nav-butt-img"><a href="<%#first_link%>"><img alt="<%IT:Localizer string="first_page" /%>" title="<%IT:Localizer string="first_page" /%>" src="<%=IMAGES%>admin/first.gif" border="0"<%=SINGLE_TAG_END%>></a></td>
    <td valign="middle" class="nav-butt-img"><a href="<%#prev_link%>"><img alt="<%IT:Localizer string="previous_page" /%>" title="<%IT:Localizer string="previous_page" /%>" src="<%=IMAGES%>admin/prev.gif" border="0"<%=SINGLE_TAG_END%>></a></td>
    <%/IF%>
    <%if page_count%>
    <td valign="middle" class="nav-butt accent" nowrap="nowrap">&nbsp;<%IT:Localizer string="page" /%>&nbsp;</td>
    <%FOR page_count%>
    <td valign="middle" class="nav-butt" nowrap="nowrap">
    <%IF page_is_current%>
    <span class="page_selected"><%#page%></span></td>
    <%ELSE%>
    <a href="<%#page_link%>" class="accent"><%#page%></a></td>
    <%/IF%>
    <%/FOR%>
    <%/if%>
    <%IF next_pages%>
    <td valign="middle" class="nav-butt-img"><a href="<%#next_link%>"><img alt="<%IT:Localizer string="next_page" /%>" title="<%IT:Localizer string="next_page" /%>" src="<%=IMAGES%>admin/next.gif" border="0"<%=SINGLE_TAG_END%>></a></td>
    <td valign="middle" class="nav-butt-img"><a href="<%#last_link%>"><img alt="<%IT:Localizer string="last_page" /%>" title="<%IT:Localizer string="last_page" /%>" src="<%=IMAGES%>admin/last.gif" border="0"<%=SINGLE_TAG_END%>></a></td>
    <%/IF%>
    <%/IF%>
    </tr>
    </table>
<table class="nav_t maxw" cellpadding="0" cellspacing="0" border="0">
<tr class="nav_t_ttl">
<%IF checkable%>
<td class="fld-check minw"><input type="checkbox" class="check" name="<%=check_name%>All" onclick="chbCheckAll(this.form,'<%=check_name%>',this.checked);"<%=SINGLE_TAG_END%>></td>
<%/IF%>
<%IF enumerated%>
<td class="minw" valign="middle" align="<%=enumerated_align%>">#</td>
<%/IF%>
<%FOR header_count%>
<%IF header_have_width%>
<td style="width: <%=header_width%>" nowrap="nowrap" align="<%=header_align%>" valign="middle" class="t_cl1_ttl">
<%ELSE%>
<td nowrap="nowrap" align="left" valign="middle" class="t_cl1_ttl">
<%/IF%>
<%IF header_is_link%>
<%IF header_sort%>
<a href="<%#header_link%>"><%=header_name%></a> <img alt="" src="<%=IMAGES%>admin/arr-<%=header_sort%>.gif" width="7" height="4"<%=SINGLE_TAG_END%>></td>
<%ELSE%>
<a href="<%#header_link%>"><%=header_name%></a></td>
<%/IF%>
<%ELSE%>
<%=header_name%></td>
<%/IF%>
<%/FOR%>
</tr>
<%FOR row_count%>
<%IF hover_script%>
<tr class="nav_t_r <%=row_class%>"<%=row_style%> onmouseover="setClass(this,'nav_t_r_a hand nav_row');" onmouseout="setClass(this,'nav_t_r <%=row_class%> nav_row');">
<%ELSE%>
<tr class="nav_t_r <%=row_class%>"<%=row_style%>>
<%/IF%>
<%IF checkable%>
<td class="fld-check">
<input type="hidden" name="<%=check_name%>2[]" value="<%=row_check_val%>"<%=SINGLE_TAG_END%>>
<input type="checkbox" class="check" name="<%=check_name%>[]" value="<%=row_check_val%>" onclick="chbExamAll(this.form,'<%=check_name%>','<%=check_name%>All');"<%=row_check_checked%><%=row_check_disabled%><%=SINGLE_TAG_END%>>
</td>
<%/IF%>
<%IF enumerated%>
<%IF hover_script%>
<td valign="<%=enumerated_valign%>" align="<%=enumerated_align%>" class="hand nav_row" onclick="<%=row_click%>"><%=row_number%></td>
<%ELSE%>
<td valign="<%=enumerated_valign%>" align="<%=enumerated_align%>"><%=row_number%></td>
<%/IF%>
<%/IF%>
<%FOR field_count%>
<%IF field_hover_script%>
<td valign="<%=valign%>" align="<%=align%>" class="hand nav_row" onclick="<%=field_click%>"<%=field_nowrap%>>
<%ELSE%>
<td valign="<%=valign%>" align="<%=align%>"<%=field_nowrap%>>
<%/IF%>
<%IF !field_is_editbox%>
<%=field_val%>
<%ELSE%>
<input name="<%=field_editname%>" class="edit" value="<%=field_val%>" size="<%=field_editsize%>" maxlength="<%=field_editmax%>"<%=SINGLE_TAG_END%>>
<%/IF%>
</td>
<%/FOR%>
</tr>
<%/FOR%>
</table>
<%IF have_pages%>
<table align="right" border="0" cellpadding="0" cellspacing="0">
<tr>
<%IF prev_pages%>
<td valign="middle" class="nav-butt-img"><a href="<%#first_link%>"><img alt="<%IT:Localizer string="first_page" /%>" title="<%IT:Localizer string="first_page" /%>" src="<%=IMAGES%>admin/first.gif" border="0"<%=SINGLE_TAG_END%>></a></td>
<td valign="middle" class="nav-butt-img"><a href="<%#prev_link%>"><img alt="<%IT:Localizer string="previous_page" /%>" title="<%IT:Localizer string="previous_page" /%>" src="<%=IMAGES%>admin/prev.gif" border="0"<%=SINGLE_TAG_END%>></a></td>
<%/IF%>
<%if page_count%>
<td valign="middle" class="accent" nowrap="nowrap">&nbsp;<%IT:Localizer string="page" /%>&nbsp;</td>
<%FOR page_count%>
<td valign="middle" class="nav-butt" nowrap="nowrap">
<%IF page_is_current%>
<span class="page_selected"><%#page%></span></td>
<%ELSE%>
<a href="<%#page_link%>" class="accent"><%#page%></a></td>
<%/IF%>
<%/FOR%>
<%/if%>
<%IF next_pages%>
<td valign="middle" class="nav-butt-img"><a href="<%#next_link%>"><img alt="<%IT:Localizer string="next_page" /%>" title="<%IT:Localizer string="next_page" /%>" src="<%=IMAGES%>admin/next.gif" border="0"<%=SINGLE_TAG_END%>></a></td>
<td valign="middle" class="nav-butt-img"><a href="<%#last_link%>"><img alt="<%IT:Localizer string="last_page" /%>" title="<%IT:Localizer string="last_page" /%>" src="<%=IMAGES%>admin/last.gif" border="0"<%=SINGLE_TAG_END%>></a></td>
<%/IF%>
</tr>
</table>
<%/IF%>
<%/IF%>