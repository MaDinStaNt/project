browser_name = navigator.appName;
browser_version = parseFloat(navigator.appVersion);

if (browser_name == "Netscape" && browser_version >= 3.0) { roll = 'true'; }
else if (browser_name == "Microsoft Internet Explorer" && browser_version >= 3.0) { roll = 'true'; }
else { roll = 'false'; }

function over(img,ref) { if (roll == 'true') { document.images[img].src = ref; } }
function out(img,ref)  { if (roll == 'true') { document.images[img].src = ref; } }

if (roll == 'true') {
	a1 = new Image; a1.src = "/images/menu_news_o.gif";
	a2 = new Image; a2.src = "/images/menu_registration_o.gif";
	a3 = new Image; a3.src = "/images/menu_models_o.gif";
	a4 = new Image; a4.src = "/images/menu_content_providers_o.gif";
	a5 = new Image; a5.src = "/images/menu_webmasters_o.gif";
	a6 = new Image; a6.src = "/images/menu_agents_o.gif";
	a7 = new Image; a7.src = "/images/menu_our_content_o.gif";
	a8 = new Image; a8.src = "/images/menu_custom_orders_o.gif";
	a9 = new Image; a9.src = "/images/menu_packages_o.gif";
	a10 = new Image; a10.src = "/images/menu_search_o.gif";
	a11 = new Image; a11.src = "/images/menu_contact_us_o.gif";
	a12 = new Image; a12.src = "/images/menu_faq_o.gif";
	a13 = new Image; a13.src = "/images/menu_license_o.gif";
	a14 = new Image; a14.src = "/images/menu_2257_o.gif";
	a15 = new Image; a15.src = "/images/menu_my_profile_o.gif";
	a16 = new Image; a16.src = "/images/menu_my_models_o.gif";
	a17 = new Image; a17.src = "/images/menu_my_orders_o.gif";
	a18 = new Image; a18.src = "/images/menu_statistics_o.gif";
	a19 = new Image; a19.src = "/images/menu_search_o.gif";
	a20 = new Image; a20.src = "/images/menu_make_money_o.gif";
}
