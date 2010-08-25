<?php
/**
 * @package LLA.Base
 */
/**
 */
require_once(BASE_CLASSES_PATH.'htmlpage.php');
/**
 * Extend your class from CFirstHTMLPage to implement home page of the site
 * @package LLA.Base
 * @version 1.0
 */
class CFirstHTMLPage extends CHTMLPage {
function CFirstHTMLPage(&$app, $content) {
	parent::CHTMLPage($app, $content);
	$this->template_vars['is_not_first'] = false;
	$this->NoCache = true;
}
}
?>