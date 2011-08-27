<div id="main">
	<div class="main">
    
	    <table cellspacing="0" cellpadding="0" class="sidebar">
	    	<tr>
		        <td class="sidebar-left">
		        
		        <p class="title"><%IT:Localizer string="menu" /%></p>
		        
		        <ul class="nav-left">
		        	<li><a href="<%=HTTP%>about.html"><%IT:Localizer string="about" /%></a></li>
		            <li><a href="<%=HTTP%>categories.html"><%IT:Localizer string="production" /%></a></li>
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
		        
					<h1><%=cat_title%></h1>
					
					<%if product_not_found%>
						<h1><%IT:Localizer string="products_not_found" /%></h1>
					<%else%>
						<%for prod_cnt_lines%>
							<ul class="product-detal">
								<%for prod_cnt_in_line%>
						        	<li>
						            	<div class="box-product">
						                	<p class="title-up"><a href="<%=HTTP%>product/<%=category_uri%>/<%=prod_uri%>.html"><%=prod_title%></a></p>
						                    	<div class="articl"><%IT:Localizer string="article" /%> № <strong><%=prod_article%></strong></div>
						                		<a href="<%=HTTP%>product/<%=category_uri%>/<%=prod_uri%>.html"><img src="<%=IMAGES%>detal-button.gif" width="90" height="23" alt="<%IT:Localizer string="detal_info" /%>" class="detal" /></a>    
						                    <div class="view-in">
						                    	<div class="left" style="line-height:160px;text-align:center;">
						                       		<img src="<%=HTTP%>pub/production/product/<%=prod_id%>/120x120/<%=prod_img%>" title="<%=prod_img_title%>" alt="<%=prod_img_title%>" style="vertical-align:middle;" />
						                       	</div>
							                    <div class="right">
							                       	<p><%=prod_brief_desc%></p>
							                    </div>
							                </div>
							            </div>
						            </li>
					           	<%/for%>
					        </ul>
						<%/for%>
			        <%/if%>
			        
		        </td>
	        
	    	</tr>
	    </table>
        
    </div>
</div><!-- main -->
