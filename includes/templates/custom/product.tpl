<script type="text/javascript" src="<%=JS%>easyslider.js"></script>
<script type="text/javascript">

$(document).ready(function(){  
					
	$("#slider").easySlider({
		auto: false, 
		continuous: false,
		numeric: true
	});
	
	$("#cms_description ul").addClass('list-in');

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
					<div class="date">
			        	<strong><%=prod_title%></strong><br /><%IT:Localizer string="article" /%> № <strong><%=prod_article%></strong>
			        </div>
			        
			        <div class="wrap-gallery">
			   			<div id="slider">
			            	<ul>
			            		<!--<*%if prod_img_not_found%>
			            			<li><*%IT:Localizer string="images_not_found" /%></li>
			            		<*%else%>-->
				            		<%for prod_img_cnt%>
					                	<li style="line-height:280px;">
					                  		<!--<*%=prod_img_numb%>--><img src="<%=HTTP%>pub/production/product/<%=prod_id%>/250x250/<%=prod_img%>" title="<%=prod_img_title%>" alt="<%=prod_img_title%>" style="vertical-align:middle;" />
					                	</li>
				                	<%/for%>
			                	<!--<*%/if%>-->
			              	</ul>
			        	</div>
			            
			            
				        <table cellspacing="0" cellpadding="0" class="view-product">
				        	<!--<*%if prod_img_inst_not_found%>
				        		<tr>
				        			<td><*%IT:Localizer string="images_inst_not_found" /%></td>
				        		</tr>
				        	<*%else%>-->
					        	<%for inst_img_cnt_lines%>
					        		<tr>
						        		<%for inst_img_cnt_in_line%>
								        	<td><img src="<%=HTTP%>pub/production/product/<%=prod_id%>/200x150/<%=inst_img%>" title="<%=inst_img_title%>" alt="<%=inst_img_title%>" /></td>
							            <%/for%>
						            </tr>
					            <%/for%>
				            <!--<*%/if%>-->
				        </table>
			            
			        </div>
			        
			        <ul class="nav_options">
			        	<%if prod_video_link%>
			        		<li><a href="<%=prod_video_link%>"><img src="<%=IMAGES%>play.gif" width="38" height="36" alt="<%IT:Localizer string="link_to_video" /%>" /></a></li>
			        	<%/if%>
			        	<%if prod_mail_link%>
			            	<li><a href="mailto:<%=prod_mail_link%>"><img src="<%=IMAGES%>basket.gif" width="38" height="36" alt="" /></a></li>
			           	<%/if%>
			            <%if prod_desc_filename%>
			            	<li><a href="<%=HTTP%>pub/production/product/<%=prod_id%>/<%=prod_desc_filename%>" target="_blank"><img src="<%=IMAGES%>info.gif" width="38" height="36" alt="<%IT:Localizer string="link_to_description" /%>" /></a></li>
			            <%/if%>
			            <%if prod_instruct_filename%>
			            	<li><a href="<%=HTTP%>pub/production/product/<%=prod_id%>/<%=prod_instruct_filename%>" target="_blank"><img src="<%=IMAGES%>settings.gif" width="38" height="36" alt="<%IT:Localizer string="link_to_unstruction" /%>" /></a></li>
			        	<%/if%>
			        </ul>
			 		
			        
					<div id="cms_description">
						<!--<*%if prod_description%>-->
							<%=prod_description%>
						<!--<*%else%>
							<*%IT:Localizer string="description_not_found" /%>
						<*%/if%>-->
					</div>
			        
			        <table cellspacing="0" cellpadding="0" class="detail-date">
			        	<tr class="top">
			            	<td width="50%"><%IT:Localizer string="technical_data" /%></td>
			            	<td width="50%"><%IT:Localizer string="equipment" /%></td>
			            </tr>
			            <tr>
			            	<td>
			            		<!--<*%if prod_tech_data_not_found%>
			            			<*%IT:Localizer string="technical_data_not_found" /%>
			            		<*%else%>-->
			            		<%for prod_tech_data_cnt%>
			            			<strong><%=prod_tech_data%>: </strong>
			            			<%=prod_tech_value%>
				            	<%/for%>
				              <!--<*%/if%>-->
			            	</td>
			            	<td>
			            		<!--<*%if prod_equip_not_found%>
			            			<*%IT:Localizer string="equipment_not_found" /%>
			            		<*%else%>-->
				            		<%for prod_equip_cnt%>
					            		<%=prod_equip_title%><br />
					            	<%/for%>
				            	<!--<*%/if%>-->
			            	</td>
			          	</tr>
			        </table>
					
		        </td>
	        
	    	</tr>
	    </table>
        
    </div>
</div><!-- main -->
