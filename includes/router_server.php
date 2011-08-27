<?php
$router = $app->get_module('Router');
$router->add_route('/projtest/laserliner/', 'CIndexPage', 'CIndexPage.php', 'indexpage.tpl');
$router->add_route('/projtest/laserliner/index.html', 'CIndexPage', 'CIndexPage.php', 'indexpage.tpl');

$router->add_route('/projtest/laserliner/contacts.html', 'CContactsPage', 'CContactsPage.php', 'contacts.tpl');
$router->add_route('/projtest/laserliner/about.html', 'CAboutPage', 'CAboutPage.php', 'about.tpl');
$router->add_route('/projtest/laserliner/partnership.html', 'CPartnershipPage', 'CPartnershipPage.php', 'partnership.tpl');
$router->add_route('/projtest/laserliner/categories.html', 'CCategoriesPage', 'CCategoriesPage.php', 'categories.tpl');
$router->add_route('/projtest/laserliner/exhibitions.html', 'CExhibitionsPage', 'CExhibitionsPage.php', 'exhibitions.tpl');
$router->add_route('/projtest/laserliner/search.html', 'CSearchPage', 'CSearchPage.php', 'search.tpl');
$router->add_route('/projtest/laserliner/product/([\w-_]+).html', 'CCategoryPage', 'CCategoryPage.php', 'category.tpl', array(1 => 'category_uri'));
$router->add_route('/projtest/laserliner/product/([\w-_]+)/([\w-_]+).html', 'CProductPage', 'CProductPage.php', 'product.tpl', array(1 => 'category_uri', 2 => 'product_uri'));

$router->add_route('/projtest/laserliner/page-not-found.html', 'CNotFoundPage', 'CNotFoundPage.php', 'page_not_found.tpl');

$page = $router->get_current_page();
if (!is_null($page)) {
	$page->parse_data();
	$page->parse_state();
	$page->output_page();
}
else {
	@header('HTTP/1.0 302 Moved Temporarily');
	@header('Status: 302 Moved Temporarily');
	@header('Location: http://'.$_SERVER['SERVER_NAME'].'/projtest/laserliner/page-not-found.html');
}
?>