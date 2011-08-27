<script type="text/javascript">
$(function(){
	$("#nl_<%=nl_cat_act%>").addClass('active');
});
</script>
<p class="title"><%IT:Localizer string="submenu" /%></p>
<ul class="nav-left">
	<%if nl_category_not_found%>
		<li><a href="<%=HTTP%>"><%IT:Localizer string="categories_not_found" /%></a></li>
	<%else%>
		<%for nl_cat_cnt%>
			<li id="nl_<%=nl_cat_uri%>"><a href="<%=HTTP%>product/<%=nl_cat_uri%>.html"><%=nl_cat_title%></a></li>
		<%/for%>
	<%/if%>
</ul>