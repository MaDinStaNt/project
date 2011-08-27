
<div id="main">
	<div class="main">
	    <%IT:SimpleArrayOutput block_begin="<div style=&quot;padding-top:5px; padding-bottom:5px&quot;><div class=&quot;error&quot;>" block_end="</div></div>" item_begin="" item_end="<br />" array="_errors" /%>
		<%IT:SimpleArrayOutput block_begin="<div style=&quot;padding-top:5px; padding-bottom:5px&quot;><div class=&quot;info&quot;>" block_end="</div></div>" item_begin="" item_end="<br />" array="_info" /%>  
	    <table cellspacing="0" cellpadding="0" class="sidebar">
	      <tr>
	        <td class="sidebar-left">
	        
	        <p class="title"><%IT:Localizer string="menu" /%></p>
	        
	        <ul class="nav-left">
	        	<li class="active"><a href="<%=HTTP%>about.html"><%IT:Localizer string="about" /%></a></li>
	            <li><a href="categories.html"><%IT:Localizer string="production" /%></a></li>
	            <%if _pat_link%>
	            	<li><a href="<%=_pat_link%>"><%IT:Localizer string="press_and_test" /%></a></li>
	            <%/if%>
	            <%if _video_link%>
	            	<li><a href="<%=_video_link%>"><%IT:Localizer string="video" /%></a></li>
	            <%/if%>
	            <li><a href="exhibitions.html"><%IT:Localizer string="exhibitions" /%></a></li>
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
	        
		        <h1><%IT:Localizer string="about_company" /%></h1>
		        <%=description%>
		        
	        </td>
	      </tr>
	    </table>
    
    </div>
</div><!-- main -->