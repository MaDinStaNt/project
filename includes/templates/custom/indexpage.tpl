<%IT:SimpleArrayOutput block_begin="<div style=&quot;padding-top:5px; padding-bottom:5px&quot;><div class=&quot;error&quot;>" block_end="</div></div>" item_begin="" item_end="<br />" array="_errors" /%>
<%IT:SimpleArrayOutput block_begin="<div style=&quot;padding-top:5px; padding-bottom:5px&quot;><div class=&quot;info&quot;>" block_end="</div></div>" item_begin="" item_end="<br />" array="_info" /%>
<script type="text/javascript" src="<%=JS%>swfobject_modified.js"></script>
<link rel="stylesheet" type="text/css" href="<%=CSS%>fancybox.css" media="screen" />
<script type="text/javascript" src="<%=JS%>fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<%=JS%>fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript">
  $(function() {
	   $("a.map").fancybox();
	   $(".cms_ul:even").addClass('info-ul_grey');
	   $(".cms_ul:odd").addClass('info-ul');
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
        
        <h2><%IT:Registry path="_static/_main" value="_company_name" /%></h2>
        <h1><%IT:Registry path="_static/_main" value="_title" /%></h1>
        
        <div class="box">
        	<div class="box-left" align="center">
            <%IT:Registry path="_static/_main" value="_img" /%>
            <%if catalog_link%>
            	<a href="<%=HTTP%><%=catalog_link%>"><img src="<%=IMAGES%>catalog.gif" width="149" height="49" alt="<%IT:Localizer string="catalog" /%>" /></a>
            <%/if%>
            </div>
            
            <div class="box-right">
            	<%IT:Registry path="_static/_main" value="_description" /%>
            </div>
        </div>
        
        <div class="separator-page">&nbsp;</div>
        <%if !exhibitions_not_found%>
	        <div class="indent20">
	        
		        <h1><a href="<%=HTTP%>exhibitions.html"><%IT:Localizer string="exhibitions" /%></a></h1>
		        
		        <div class="box">
		        	<div class="box-left"><img src="<%=HTTP%>pub/exhibition/<%=ex_showed_id%>/250x150/<%=ex_showed_img%>" alt="<%IT:Localizer string="photo_from_last_exhibition" /%>" /></div>
		            
		            <div class="box-right">
		            
		            
			            <div class="info-ul">
			            	<ul>
			                	<li class="left"><span class="title-span">- <%=curr_year%> -</span></li>
			                    <li class="right">&nbsp;</li>
			                </ul>
			            </div>    
			            <%for ex_cnt%>
				        	<div class="cms_ul">
					        	<ul>
					            	<li class="left"><strong><a href="<%=HTTP%>exhibitions.html"><%=ex_title%></a></strong><br />
						            <%=ex_date_begin%> - <%=ex_date_end%></li>
					                <li class="center"><%=ex_abbreviation%></li>
					                <li class="right"><%=ex_destination%></li>
					            </ul>
							</div>
						<%/for%>
			             
			        
			      	</div>
		        </div>
	        <%/if%>
        </div>
        
        </td>
        <td class="sidebar-right">
        <%if !contact_not_found%>
        	<p class="title"><%IT:Localizer string="address" /%> / <%IT:Localizer string="contacts" /%></p>

			<ul class="contact">
				<li><%=contact_title%></li>
				
				<li><%=contact_address%></li>
				
				<li><%IT:Localizer string="telephone" /%>: <%=contact_tel%><br />
				<%IT:Localizer string="fax" /%>: <%=contact_fax%><br />
				<%IT:Localizer string="email" /%>: <a href="mailto:<%=contact_email%>"><%=contact_email%></a></li>
			</ul>
			
			<%if contact_show_map%>
				<a href="<%=HTTP%>_r/<%=big_img_path%>" class="map" ><img src="<%=HTTP%>_r/<%=sm_img_path%>" alt="<%IT:Localizer string="map" /%>" /></a>
				<span class="map-link"><a href="<%=IMAGES%>map.jpg" class="map"><%IT:Localizer string="enlarge_map" /%></a></span>
			<%/if%>
		<%/if%>
        
		<%if !video_not_found%>
	        <p class="title">Laserliner</p>
	              
	        <div class="player">
	        <!-- flash -->                    
	                   <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="155" height="155" align="top">
	                      <param name="movie" value="<%=HTTP%>player/player.swf" />
	                        <param name="quality" value="high" />
	                        <param name="wmode" value="transparent" />
	                        <param name="swfversion" value="8.0.35.0" />
	                         <param name="allowFullScreen" value="true" />
	                       <param name="expressinstall" value="<%=JS%>expressInstall.swf" />
	                       <param name="flashvars" value="link_video=<%=HTTP%>_r/<%=video_path%>&link_img=<%=HTTP%>_r/<%=video_img_path%>" />
	                       <!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
	                       <!--[if !IE]>-->
	          			<object data="<%=IMAGES%>player.swf" type="application/x-shockwave-flash" width="155" height="155" align="top">
	                        <!--<![endif]-->
	                        <param name="quality" value="high" />
	                        <param name="wmode" value="transparent" />
	                         <param name="allowFullScreen" value="true" />
	                        <param name="swfversion" value="8.0.35.0" />
	                        <param name="expressinstall" value="<%=JS%>expressInstall.swf" />                        
	                       	<param name="flashvars" value="link_video=<%=HTTP%>_r/<%=video_path%>&link_img=<%=HTTP%>_r/<%=video_img_path%>" />
	                        <!--[if !IE]>-->
	                    </object>
	                     <!--<![endif]-->
	                    </object>
	                    <!-- / flash --> 
	        </div>
        <%/if%>
        </td>
      </tr>
    </table>
    
    </div>
</div><!-- main -->