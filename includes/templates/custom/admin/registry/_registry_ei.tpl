<%it:form begin="true" name="reg_ex_im" enctype="multipart/form-data" /%>
<%IT:SimpleArrayOutput block_begin="<p CLASS=&quot;landingpagetext&quot;>" block_end="</p>" item_begin="<span class=&quot;error&quot;>" item_end="</span><br>" array="reg_ie_errors" /%>
<input type="hidden" name="MAX_FILE_SIZE" value="1024000000">
<table border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td align="left" valign="top">
			Export:
		</td>
		<td align="left" valign="top">
			<%it:input type="button" name="export" value="Export Files" /%>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="left" valign="top">
			Import:
		</td>
		<td align="left" valign="top">
			<%it:input type="file" name="import_file" /%>
		</td>
		<td align="left" valign="top">
			<%it:input type="button" name="import" value="Import Files" /%>
		</td>
	</tr>
</table>

<%it:form end="true" /%>