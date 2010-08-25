<?php
require_once(BASE_CLASSES_PATH . 'adminpage.php');

class CDictionariesPage extends CAdminPage
{
    function CDictionariesPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CAdminPage($app, $template);
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