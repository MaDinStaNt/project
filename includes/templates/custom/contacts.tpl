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
	        
	        <h1><%IT:Localizer string="contacts" /%></h1>
	        
	        <%if contact_title_show%>
	 			<p><%=contact_title%></p>
	 		<%/if%>
	 		
	 		<%if contact_address_show%>
				<p><%=contact_address%></p>
			<%/if%>
	
			<ul class="list-in">
				<%if contact_telephone_show%>
			    	<li><%IT:Localizer string="telephone" /%>: <%=contact_telephone%></li>
			    <%/if%>
			    
			    <%if contact_fax_show%>
			    	<li><%IT:Localizer string="fax" /%>: <%=contact_fax%></li>
			    <%/if%>
			    
			    <%if contact_email_show%>
			    	<li><%IT:Localizer string="email" /%>: <a href="mailto:<%=contact_email%>"><%=contact_email%></a></li>
			    <%/if%>
			</ul>
	
			<div class="separator-page separator-indent">&nbsp;</div>
			
			
			<p><strong><%IT:Localizer string="sales" /%>:</strong></p>
	
			<ul class="contact-name">
				<%if contact_first_salesperson_show%>
			  		<li><span><%=contact_first_salesperson%></span>
			  		<%if contact_first_salesperson_contact_show%>
			  			<%=contact_first_salesperson_contact%>
			  		<%/if%>
			  		</li>
			  	<%/if%>
			  	
			  	<%if contact_second_salesperson_show%>
			  		<li><span><%=contact_second_salesperson%></span>
			  		<%if contact_second_salesperson_contact_show%>
			  			<%=contact_second_salesperson_contact%>
			  		<%/if%>
			  		</li>
			  	<%/if%>
			  	
			  	<%if contact_third_salesperson_show%>
			  		<li><span><%=contact_third_salesperson%></span>
			  		<%if contact_third_salesperson_contact_show%>
			  			<%=contact_third_salesperson_contact%>
			  		<%/if%>
			  		</li>
			  	<%/if%>
			</ul>
	
			<p><strong><%IT:Localizer string="service" /%>:</strong></p>
	
			<ul class="contact-name">
				<%if contact_first_employee_service_show%>
			  		<li><span><%=contact_first_employee_service%></span>
			  		<%if contact_first_employee_service_contact_show%>
			  			<%=contact_first_employee_service_contact%>
			  		<%/if%>
			  		</li>
			  	<%/if%>
			  	
			  	<%if contact_second_employee_service_show%>
			  		<li><span><%=contact_second_employee_service%></span>
			  		<%if contact_second_employee_service_contact_show%>
			  			<%=contact_second_employee_service_contact%>
			  		<%/if%>
			  		</li>
			  	<%/if%>
			</ul>
	        
	        <div class="separator-page separator-indent">&nbsp;</div>
	        
	        <%if contact_med_img_show%>
	       		<img src="<%=HTTP%>_r/<%=path_id%>/<%=contact_med_img%>" alt="<%=contact_med_img%>" class="map-indent" />
	       	<%/if%>
	        
	        </td>
	        
	      </tr>
	    </table>
    </div>
</div><!-- main -->