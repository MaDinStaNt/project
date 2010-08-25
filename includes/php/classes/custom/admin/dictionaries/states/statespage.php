<?php
require_once(CUSTOM_CLASSES_PATH . 'admin/masterpage.php');

class CStatesPage extends CMasterPage
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $_table = 'state';

    /**
     * The columns array.
     *
     * @var array
     */
    protected $_columns_arr = array('title' => 'Title', 'abbreviation' => 'Abbreviation');

    function CStatesPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CMasterPage($app, $template);
		$this->DataBase = &$this->Application->DataBase;
	}
	
	function on_page_init()
	{
		parent::on_page_init();
		parent::page_actions();
	}
	
	function parse_data()
	{
		if (!parent::parse_data()) return false;
		
		$this->bind_data();
		
        return true;
	}
}
?>