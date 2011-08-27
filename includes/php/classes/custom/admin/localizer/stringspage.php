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
    
    protected $DataBase;

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
		$lang_rs = $this->DataBase->select_sql('loc_lang');
		if($lang_rs == false) $lang_rs = new CRecordSet();
		$lang_rs->add_row(array('id' => '', 'title' => RECORDSET_FIRST_ITEM));
		
		$this->_filters = array(
	    	'ls#language_id' => array(
	    		'title' => $this->Application->Localizer->get_string('language'),	
	    		'type' => FILTER_SELECT,
	    		'data' => array($lang_rs, 'id', 'title'),
	    		'condition' => CONDITION_EQUAL
	    	),
	    	'ls#name' => array(
	    		'title' => $this->Application->Localizer->get_string('name'),	
	    		'type' => FILTER_TEXT,
	    		'data' => null,
	    		'condition' => CONDITION_LIKE
	    	),
	    	'ls#value' => array(
	    		'title' => $this->Application->Localizer->get_string('value'),	
	    		'type' => FILTER_TEXT,
	    		'data' => null,
	    		'condition' => CONDITION_LIKE
	    	),
	    );
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
            WHERE ". $this->_where;

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

        if ($nav->size > 0)
            $this->template_vars[$this->_table.'_show_remove'] = true;
        else
            $this->template_vars[$this->_table.'_show_remove'] = false;
	}
}
?>