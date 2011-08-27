
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
	        	
	        	<%IT:SimpleArrayOutput block_begin="<div style=&quot;padding-top:5px; padding-bottom:5px&quot;><div class=&quot;error&quot;>" block_end="</div></div>" item_begin="" item_end="<br />" array="_errors" /%>
				<%IT:SimpleArrayOutput block_begin="<div style=&quot;padding-top:5px; padding-bottom:5px&quot;><div class=&quot;info&quot;>" block_end="</div></div>" item_begin="" item_end="<br />" array="_info" /%> 
		
		        <h1>Поиск</h1>
        
        
	        	<%IT:form id="search" begin="true" method="post" action="" /%>
	       
	        
			        <ul class="seach-box">
			        	<li><%it:input class="in" type="text" name="string" id="string" priority="template, post, get" /%></li>
			            <li><%it:input class="search-b" type="button" name="search" id="search" value="" /%></li>
			        </ul>
			       
			        
			        <ul class="search-list">
			        	<li><%it:input type="radio" id="all_words" name="radio" value="all_words" checked="true" /%><label for="all_words"><%IT:Localizer string="all_words" /%></label></li>
			            <li><%it:input type="radio" id="any_words" name="radio" value="any_words" /%><label for="any_words"><%IT:Localizer string="any_words" /%></label></li>
			            <!--<li><*%it:input type="radio" id="exact" name="radio" value="exact" /%><label for="exact"><*%IT:Localizer string="exact" /%></label></li>-->
			        </ul>
		        
		        <%IT:form end="true" /%>
		        
		        <div style="margin-top:25px;">
			        <%if result_not_found%>
			        	<%IT:Localizer string="nothing_not_found" /%>
			        <%else%>
			        	<%for s_cnt%>
					        <div style="margin-top:15px;">
					        	<a href="<%=HTTP%><%=s_uri%>.html" target="_blank" title="<%=s_title%>"><%=s_title%></a>
					        	<p><%=s_description%></p>
					        </div>
				        <%/for%>
		        	<%/for%>
	        	</div>
	        
	 		 <div class="reset-s"></div>  
	        
	        </td>
	      </tr>
	    </table>
    
    </div>
</div><!-- main -->