<%if available%>
<%if ps%>
		<div class="navigation"></div>
		<table cellpadding="2" cellspacing="0" class="maxw">
        <%for ps%>
                <%if ps1_mode_normal%>
				 		<tr>
							<td valign="top" align="center">
								<table cellpadding="0" cellspacing="0" class="menu_inact" width="194">
								<tr>
									<td class="menu_item">
									<%if ps1_no_link%>
										<%=ps1_short_title%>
									<%else%>
										<a href="<%=ps1_uri%>"><%=ps1_short_title%></a>
									<%/if%>
									</td>
								</tr>
								</table>
							</td>
						</tr>
                <%/if%>
                <%if ps1_mode_active%>
                		<tr>
							<td valign="top" align="center">
								<%if !ps1%>
									<table cellpadding="0" cellspacing="0" class="menu_act" width="194">
									<tr>
										<td class="menu_item">
											<%=ps1_short_title%>
										</td>
									</tr>
									</table>
		                        <%else%>
									<table cellpadding="0" cellspacing="0" border="0" width="194">
									<tr>
										<td class="menu_act_sub" valign="middle">
											<div style="padding-left:23px"><%=ps1_short_title%></div>
										</td>
									</tr>
	                                <%for ps1%>
                                        <%if ps2_mode_normal%>
											<tr>
												<td class="submenu">
													<div class="submenu">
	                                                <%if ps2_no_link%>
														<%=ps2_short_title%>
	                                                <%else%>
														<a href="<%=ps2_uri%>"><%=ps2_short_title%></a>
	                                                <%/if%>
													</div>
												</td>
											</tr>
                                        <%/if%>
                                        <%if ps2_mode_active%>
											<tr>
												<td class="submenu_act">
													<div class="submenu_act">
	                                                	<%=ps2_short_title%>
													</div>
												</td>
											</tr>                                        
                                        <%/if%>
                                        <%if ps2_mode_descendant_active%>
											<tr>
												<td class="submenu_act">
													<div class="submenu_act">
		                                                <%if !ps2_no_link%>
		                                                        <a href="<%=ps2_uri%>" class="bold"><%=ps2_short_title%></a>
		                                                <%else%>
		                                                        <span class="bold"><%=ps2_short_title%></span>
		                                                <%/if%>
													</div>
												</td>
											</tr>                                        
                                        <%/if%>
	                                <%/for%>
									<tr>
										<td class="submenu_act_end"></td>
									</tr>
									</table>
		                        <%/if%>
							</td>
						</tr>
                <%/if%>    
                <%if ps1_mode_descendant_active%>
                		<tr>
							<td valign="top" align="center">
								<table cellpadding="0" cellspacing="0" border="0" width="194">
								<tr>
									<td 
									<%if ps1%>
									 class="menu_act_sub"
									<%else%>
									 class="menu_act"
									<%/if%>
									 valign="middle">
		                                <%if ps1_no_link%>
												<div style="padding-left:23px"><%=ps1_short_title%></div>
		                                <%else%>
												<div style="padding-left:23px"><a href="<%=ps1_uri%>"><%=ps1_short_title%></a></div>
		                                <%/if%>
									</td>
								</tr>
		                        <%if ps1%>
	                                <%for ps1%>
                                        <%if ps2_mode_normal%>
											<tr>
												<td class="submenu">
													<div class="submenu">
	                                                <%if ps2_no_link%>
														<%=ps2_short_title%>
	                                                <%else%>
														<a href="<%=ps2_uri%>"><%=ps2_short_title%></a>
	                                                <%/if%>
													</div>
												</td>
											</tr>
                                        <%/if%>
                                        <%if ps2_mode_active%>
											<tr>
												<td class="submenu_act">
													<div class="submenu_act">
	                                                	<%=ps2_short_title%>
													</div>
												</td>
											</tr>                                        
                                        <%/if%>
                                        <%if ps2_mode_descendant_active%>
											<tr>
												<td class="submenu_act">
													<div class="submenu_act">
		                                                <%if !ps2_no_link%>
	                                                        <a href="<%=ps2_uri%>" class="bold"><%=ps2_short_title%></a>
		                                                <%else%>
	                                                        <span class="bold"><%=ps2_short_title%></span>
		                                                <%/if%>
													</div>
												</td>
											</tr>                                        
                                        <%/if%>
	                                <%/for%>
								<tr>
									<td class="submenu_act_end"></td>
								</tr>
		                        <%/if%>
								</table>
							</td>
						</tr>
                <%/if%>
        <%/for%>
        </table>
<%/if%>
<%/if%>