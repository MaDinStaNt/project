<?php
require_once(BASE_CLASSES_PATH . 'adminpage.php');

class CRegistryPage extends CAdminPage
{
    function CRegistryPage(&$app, $template)
	{
		$this->IsSecure = true;
		$this->Application = &$app;
		$this->Registry = &$this->Application->get_module('Registry');
		$this->tv = &$this->Application->tv;
		parent::CAdminPage($app, $template);
	}
	
	function on_page_init()
	{
		return parent::on_page_init();
	}
	
	function parse_data()
	{
        if (!parent::parse_data()) return false;
        
        $this->tv['action_form'] = substr($_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI']));
        $this->tv['registry_output'] = $this->Registry->run_admin_interface();
        return true;
	}
}
?>