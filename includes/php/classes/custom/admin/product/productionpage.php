<?php
require_once(CUSTOM_CLASSES_PATH . 'admin/masterpage.php');

class CProductionPage extends CMasterPage
{
    /**
     * The table name.
     *
     * @var array
     */
    protected $_table = 'category';
    /**
     * The table name.
     *
     * @var array
     */

	function CProductionPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CMasterPage($app, $template);
		$this->DataBase = &$this->Application->DataBase;
	}
	
	function on_page_init()
	{
		parent::on_page_init();
	}
	
	function parse_data()
	{
		if (!parent::parse_data()) return false;
		
        return true;
	}
}
?>