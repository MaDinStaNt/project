</head>
<body class="maxh">
<%if redirect_to_profile%>
<script type="text/javascript" src="<%=JS%>jquery/jquery.timer.js"></script>
<%/if%>
<script type="text/javascript" charset="utf-8">
var set_show_status = function(data) {
	void(0);
};

var set_hide_status = function(data) {
	void(0);
};

var set_filter_show_status = function(data) {
	void(0);
};

var set_filter_hide_status = function(data) {
	void(0);
};
$(document).ready(function(){
	$("#hide_panel").click(function () {
		$("#navi_panel").hide("slow");
		$("#hide_panel").hide();
		$("#show_panel").show();
		call('Interface', 'set_panel_hide', []).listen(set_hide_status);
	});
	$("#show_panel").click(function () {
		$("#navi_panel").show("slow");
		$("#show_panel").hide();
		$("#hide_panel").show();
		call('Interface', 'set_panel_show', []).listen(set_show_status);
	});
	$("#hide_filter_panel").click(function () {
		$("#filter_panel").hide("slow");
		$("#hide_filter_panel").hide();
		$("#show_filter_panel").show();
		call('Interface', 'set_filter_panel_hide', []).listen(set_filter_hide_status);
	});
	$("#show_filter_panel").click(function () {
		$("#filter_panel").show("slow");
		$("#show_filter_panel").hide();
		$("#hide_filter_panel").show();
		call('Interface', 'set_filter_panel_show', []).listen(set_filter_show_status);
	});
	<%if redirect_to_profile%>
	$.timer(3000, function (timer) {
		window.location.href = '<%=profile_url%>';
		timer.stop();
	});
	<%/if%>
});
</script>
<table cellpadding="0" cellspacing="0" class="maxw maxh">
<tr>
	<%if is_logged%>
	<td class="navigation_area">
	<div id="navi_panel"
		<%if !is_panel_show%>
		 style="display: none;"
		<%/if%>
		>
		<table cellpadding="0" cellspacing="0" class="maxw">
		<tr>
			<td class="navigation_title" nowrap="true"><a href="<%=HTTP%>"><%=site_name%></a> - <a href="<%=HTTP%>admin/"><%IT:Localizer string="admin_panel" /%></a></td>
		</tr>
		</table>
		<%it:navi /%>
	</div>
	</td>
	<td width="5" valign="middle" class="vert_line">
		<%if is_panel_show%>
			<div id="hide_panel" class="hand" style="display: block;"><img src="<%=IMAGES%>admin/slider_left.png" title="<%IT:Localizer string="hide_panel" /%>" alt="<%IT:Localizer string="hide_panel" /%>" /></div>
			<div id="show_panel" class="hand" style="display: none;"><img src="<%=IMAGES%>admin/slider_right.png" title="<%IT:Localizer string="show_panel" /%>" alt="<%IT:Localizer string="show_panel" /%>" /></div>
		<%else%>
			<div id="hide_panel" class="hand" style="display: none;"><img src="<%=IMAGES%>admin/slider_left.png" title="<%IT:Localizer string="hide_panel" /%>" alt="<%IT:Localizer string="hide_panel" /%>" /></div>
			<div id="show_panel" class="hand" style="display: block;"><img src="<%=IMAGES%>admin/slider_right.png" title="<%IT:Localizer string="show_panel" /%>" alt="<%IT:Localizer string="show_panel" /%>" /></div>
		<%/if%>
	</td>
	<%/if%>
	<td class="maxh maxw">
		<table cellpadding="0" cellspacing="0" class="maxw maxh">
		<tr>
			<td valign="top" height="90%">
				<table cellpadding="0" cellspacing="0" class="maxw">
				<tr>
					<td class="header_area">
						<table cellpadding="0" cellspacing="0" class="maxw header_content">
						<tr>
							<td align="right" valign="top">
								<%if is_logged%>
								<table cellpadding="0" cellspacing="0" class="login_area">
								<tr>
									<td class="login_area_begin"></td>
									<td valign="top" align="right">
										<%IT:form name="logout_form" begin="true" action="" /%>
										<%IT:form end="true" /%>
										<table cellpadding="0" cellspacing="0" class="login_area_panel">
										<tr>
											<td valign="middle"><%IT:Localizer string="you_logged_in_as" /%>:</td>
											<td><img src="<%=IMAGES%>admin/login_area_user.png"></td>
											<td class="login_panel_title"><a href="<%=user_edit%>id=<%=logged_user_id%>"><%=logged_user_name%> (<%=logged_user_email%>)</a></td>
											<td><img src="<%=IMAGES%>admin/login_area_settings.png"></td>
											<td class="login_panel_title"><a href="#"><%IT:Localizer string="settings" /%></a></td>
											<td><img src="<%=IMAGES%>admin/login_area_logout.png"></td>
											<td class="login_panel_title logout"><a href="JavaScript: if (confirm('<%IT:Localizer string="logout_question" /%>')) document.forms.logout_form.submit();"><%IT:Localizer string="logout" /%></a></td>
										</tr>
										</table>
									</td>
								</tr>
								</table>
								<%/if%>				
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="main_area" valign="top">
					<%IT:SimpleArrayOutput block_begin="<div class=&quot;error&quot;><table cellspacing=&quot;3&quot; cellpadding=&quot;3&quot;><tr><td align=&quot;left&quot;>" block_end="</td></tr></table></div>" item_begin="" item_end="<br />" return="<%=_return_errors%>" array="_errors" /%>
					<%IT:SimpleArrayOutput block_begin="<div class=&quot;info&quot;><table cellspacing=&quot;3&quot; cellpadding=&quot;3&quot;><tr><td align=&quot;left&quot;>" block_end="</td></tr></table></div>" item_begin="" item_end="<br />" return="<%=_return_info%>" array="_info" /%>
