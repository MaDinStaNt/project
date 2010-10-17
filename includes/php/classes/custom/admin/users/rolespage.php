<?php
require_once(CUSTOM_CLASSES_PATH . 'admin/masterpage.php');

class CRolesPage extends CMasterPage
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $_table = 'user_role';

    /**
     * The columns array.
     *
     * @var array
     */
    protected $_columns_arr = array();

    function CRolesPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CMasterPage($app, $template);
		$this->DataBase = &$this->Application->DataBase;
	}
	
	function on_page_init()
	{
		$this->_columns_arr = array(
			'title' => $this->Application->Localizer->get_string('title'), 
			'description' => $this->Application->Localizer->get_string('description'), 
			'id' => $this->Application->Localizer->get_string('id')
		);
		parent::on_page_init();
		parent::page_actions();
		parent::page_filters();
	}
	
	function parse_data()
	{
		if (!parent::parse_data()) return false;
		
		$this->bind_data();
		
        return true;
	}
}
?>