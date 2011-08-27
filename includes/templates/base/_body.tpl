<script type="text/javascript">
$(function(){
	$("#<%=tnav_act_link%>").addClass('active');
});
</script>
</head>
<body>
<div id="header">
<div class="header">
	<a href="<%=HTTP%>"><img src="<%=IMAGES%>logo.gif" class="logo" width="347" height="33" alt="<%IT:Localizer string="laserliner" /%>" /></a>
	<br class="clear" />
		<div class="nav">
            <ul>
                <li id="tnav_index"><a href="<%=HTTP%>"><%IT:Localizer string="main" /%></a></li>
                <li id="tnav_partnership"><a href="<%=HTTP%>partnership.html"><%IT:Localizer string="partnership" /%></a></li>
                <li id="tnav_contacts"><a href="<%=HTTP%>contacts.html"><%IT:Localizer string="contacts" /%></a></li>
                <li id="tnav_search"><a href="<%=HTTP%>search.html"><%IT:Localizer string="search" /%></a></li>    
            </ul>
		</div>
</div>
</div><!-- header -->