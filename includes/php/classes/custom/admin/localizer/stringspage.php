<?php
require_once(CUSTOM_CLASSES_PATH . 'admin/masterpage.php');

class CStringsPage extends CMasterPage
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $_table = 'loc_strings';

    /**
     * The columns array.
     *
     * @var array
     */
    protected $_columns_arr = array('title' => 'Title');

    function CStringsPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CMasterPage($app, $template);
		$this->DataBase = &$this->Application->DataBase;
	}
	
	function on_page_init()
	{
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
	
	function bind_data()
	{
        $query = "SELECT ls.id as id, ll.title AS language, ls.name, ls.value
            FROM %prefix%loc_strings ls LEFT JOIN %prefix%loc_lang ll ON (ls.language_id = ll.id)
            WHERE 1=1";

        require_once(BASE_CLASSES_PATH . 'controls/navigator.php'); // base application class
        $nav = new Navigator('objects', $query, array('language' => 'language', 'name' => 'name', 'value' => 'value'), 'id', $this->Application);
        
        $header_num = $nav->add_header('Language', 'language');
        $nav->headers[$header_num]->no_escape = false;
        $nav->headers[$header_num]->set_wrap();
        $nav->set_width($header_num, '10%');

        $header_num = $nav->add_header('Name', 'name');
        $nav->headers[$header_num]->no_escape = false;
        $nav->headers[$header_num]->set_wrap();
        $nav->set_width($header_num, '20%');

        $header_num = $nav->add_header('Value', 'value');
        $nav->headers[$header_num]->no_escape = false;
        $nav->headers[$header_num]->set_wrap();
        $nav->set_width($header_num, '70%');

        $this->tv['clickLink'] = $this->Application->Navi->getUri('./'.$this->_table.'_edit/', true);

        if ($nav->size > 1)
            $this->template_vars[$this->_table.'_show_remove'] = true;
        else
            $this->template_vars[$this->_table.'_show_remove'] = false;
	}
}
?>