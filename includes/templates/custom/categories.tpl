<script type="text/javascript">
$(function(){
	$(".products-list li:odd .box-left-product").addClass('grey-box');
});
</script>
<div id="main">
	<div class="main">
    
	    <table cellspacing="0" cellpadding="0" class="sidebar">
	    	<tr>
		        <td class="sidebar-left">
		        
		        <p class="title"><%IT:Localizer string="menu" /%></p>
		        
		        <ul class="nav-left">
		        	<li><a href="<%=HTTP%>about.html"><%IT:Localizer string="about" /%></a></li>
		            <li class="active"><a href="<%=HTTP%>categories.html"><%IT:Localizer string="production" /%></a></li>
		            <%if _pat_link%>
		            	<li><a href="<%=_pat_link%>"><%IT:Localizer string="press_and_test" /%></a></li>
		            <%/if%>
		            <%if _video_link%>
		            	<li><a href="<%=_video_link%>"><%IT:Localizer string="video" /%></a></li>
		            <%/if%>
		            <li><a href="<%=HTTP%>exhibitions.html"><%IT:Localizer string="exhibitions" /%></a></li>
		        </ul>
		        
		        <%IT:NavLeft /%>
		        
		        <ul class="banner-link">
		        	<%if _pat_link%>
		        		<li><a href="<%=_pat_link%>"><img src="<%=IMAGES%>press-and-test.gif" width="109" height="53" alt="<%IT:Localizer string="press_and_test" /%>" /></a></li>
		        	<%/if%>
		        	<%if _video_link%>
		            	<li><a href="<%=_video_link%>"><img src="<%=IMAGES%>video.gif" width="109" height="53" alt="<%IT:Localizer string="video" /%>" /></a></li>
		            <%/if%>
		        </ul>
		        
		        </td>
		        <td class="sidebar-center">
		        
			        <h1><%IT:Localizer string="production" /%></h1>
					<%if category_not_found%>
						<h1><%IT:Localizer string="categories_not_found" /%></h1>
					<%else%>
						<ul class="products-list">
							<%for cat_cnt_lines%>
								<li>
									<%for cat_cnt_in_line%>
										<div class="box-left-product"><a href="<%=HTTP%>product/<%=cat_uri%>.html" title="<%=cat_desc%>"><img src="<%=HTTP%>pub/production/category/<%=cat_id%>/135x150/<%=cat_img%>" alt="<%=cat_title%>" /></a>
									    	<div>
									      		<span><a href="<%=HTTP%>product/<%=cat_uri%>.html" title="<%=cat_desc%>"><%=cat_title%></a></span>
									      		<p><%=cat_desc%></p>
									    	</div>  
									  	</div>
									<%/for%>
								</li>
							<%/for%>
						</ul>
		        	<%/if%>
		        </td>
	        
	    	</tr>
	    </table>
        
    </div>
</div><!-- main -->
