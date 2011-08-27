<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
	
	<script type="text/javascript" src="<%=JS%>fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
	<script type="text/javascript" src="<%=JS%>fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="<%=JS%>fancybox/jquery.fancybox-1.3.4.css" media="screen" />
 	
	<script type="text/javascript">
		$(document).ready(function() {
			
			$("a.mad").fancybox({
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'titlePosition' 	: 'over',
				'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
					return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
				}
			});
				
			
		});
	</script>
<div id="main">
	<div class="main">
	    <%IT:SimpleArrayOutput block_begin="<div style=&quot;padding-top:5px; padding-bottom:5px&quot;><div class=&quot;error&quot;>" block_end="</div></div>" item_begin="" item_end="<br />" array="_errors" /%>
		<%IT:SimpleArrayOutput block_begin="<div style=&quot;padding-top:5px; padding-bottom:5px&quot;><div class=&quot;info&quot;>" block_end="</div></div>" item_begin="" item_end="<br />" array="_info" /%>  
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
	            <li class="active"><a href="<%=HTTP%>exhibitions.html"><%IT:Localizer string="exhibitions" /%></a></li>
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
	        
		        <h1><%IT:Localizer string="Exhibitions" /%></h1>
        
 				<%if exhibitions_not_found%>
 					<h1><%IT:Localizer string="exhibitions_not_found" /%></h1>
 				<%else%>
 					<%for ex_cnt%>
				        <div class="date indent_date">
				        	<strong><%=ex_title%></strong><br /><%=ex_destination%> / <%=ex_date_begin%> – <%=ex_date_end%>
				        	<%if image_not_found%>
				        		<br /><%IT:Localizer string="image_not_found" /%>
				        	<%/if%>
				        </div>
						
				        <%if !image_not_found%>
					    	<%for cnt_lines%>
						        <ul class="gallery">
						        	<%for cnt_img_in_line%>
						        		<li><a href="<%=HTTP%>pub/exhibition/<%=img_ex_id%>/800x600/<%=img%>" rel="gallery_<%=img_ex_id%>" title="<%=a_img_title%>" class="mad"><img src="<%=HTTP%>pub/exhibition/<%=img_ex_id%>/150x100/<%=img%>" alt="<%=img_title%>" /></a></li>
						        	<%/for%>
						        </ul>
					        <%/for%>
					    <%/if%>
		        	<%/for%>
		        <%/if%>
	        </td>
	      </tr>
	    </table>
    
    </div>
</div><!-- main -->