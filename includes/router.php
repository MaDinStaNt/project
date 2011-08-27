<?php
$router = $app->get_module('Router');
$router->add_route('/', 'CIndexPage', 'CIndexPage.php', 'indexpage.tpl');
$router->add_route('/index.html', 'CIndexPage', 'CIndexPage.php', 'indexpage.tpl');

$router->add_route('/contacts.html', 'CContactsPage', 'CContactsPage.php', 'contacts.tpl');
$router->add_route('/about.html', 'CAboutPage', 'CAboutPage.php', 'about.tpl');
$router->add_route('/partnership.html', 'CPartnershipPage', 'CPartnershipPage.php', 'partnership.tpl');
$router->add_route('/categories.html', 'CCategoriesPage', 'CCategoriesPage.php', 'categories.tpl');
$router->add_route('/exhibitions.html', 'CExhibitionsPage', 'CExhibitionsPage.php', 'exhibitions.tpl');
$router->add_route('/search.html', 'CSearchPage', 'CSearchPage.php', 'search.tpl');
$router->add_route('/product/([\w-_]+).html', 'CCategoryPage', 'CCategoryPage.php', 'category.tpl', array(1 => 'category_uri'));
$router->add_route('/product/([\w-_]+)/([\w-_]+).html', 'CProductPage', 'CProductPage.php', 'product.tpl', array(1 => 'category_uri', 2 => 'product_uri'));

$router->add_route('/page-not-found.html', 'CNotFoundPage', 'CNotFoundPage.php', 'page_not_found.tpl');

$page = $router->get_current_page();
if (!is_null($page)) {
	$page->parse_data();
	$page->parse_state();
	$page->output_page();
}
else {
	@header('HTTP/1.0 302 Moved Temporarily');
	@header('Status: 302 Moved Temporarily');
	@header('Location: http://'.$_SERVER['SERVER_NAME'].'/page-not-found.html');
}
?>