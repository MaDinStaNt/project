<?php
require_once(CUSTOM_CLASSES_PATH . 'admin/masterpage.php');

class CExhibitionPage extends CMasterPage
{
    /**
     * The table name.
     *
     * @var array
     */
    protected $_table = 'exhibition';
    /**
     * The table name.
     *
     * @var array
     */
    protected $_filters = array();
    protected $DataBase;

	function CExhibitionPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CMasterPage($app, $template);
		$this->DataBase = &$this->Application->DataBase;
		$pc_rs = $this->DataBase->select_sql('product_category');
		if($pc_rs == false) $pc_rs = new CRecordSet();
		$pc_rs->add_row(array('id' => '', 'title' => RECORDSET_FIRST_ITEM), INSERT_BEGIN);
		
	    $this->_filters = array(
	    	'e#title' => array(
	    		'title' => $this->Application->Localizer->get_string('title'),	
	    		'type' => FILTER_TEXT,
	    		'data' => null,
	    		'condition' => CONDITION_LIKE
	    	),
	    	'e#destination' => array(
	    		'title' => $this->Application->Localizer->get_string('destination'),	
	    		'type' => FILTER_TEXT,
	    		'data' => null,
	    		'condition' => CONDITION_LIKE
	    	),
	    	'e#date_begin' => array(
	    		'title' => $this->Application->Localizer->get_string('date_begin_from'),	
	    		'type' => FILTER_DATE,
	    		'data' => null,
	    		'condition' => CONDITION_LIKE
	    	),
	    	'e#date_end' => array(
	    		'title' => $this->Application->Localizer->get_string('date_end_from'),	
	    		'type' => FILTER_DATE,
	    		'data' => null,
	    		'condition' => CONDITION_LIKE
	    	),
	    );
	}
	
	function on_page_init()
	{
		parent::on_page_init();
		$this->page_actions();
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
        $query = "SELECT 
        	e.id as id,
        	IF((SELECT COUNT(id) FROM %prefix%exhibition) = 1, 
        		'одна выставка', 
        		IF(e.priority = (SELECT COUNT(id) FROM %prefix%exhibition), 
        			CONCAT('<a href=\"javascript: priority_submit(\'down\', \'\', \'', e.id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/down.gif\" title=\"Вниз\"></a>'), 
        			IF(e.priority = 1, CONCAT('<a href=\"javascript: priority_submit(\'up\', \'\', \'', e.id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/up.gif\" title=\"Вверх\"></a>'), 
        				CONCAT('<a href=\"javascript: priority_submit(\'up\', \'\', \'', e.id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/up.gif\" title=\"Вверх\"></a>', ' <a href=\"javascript: priority_submit(\'down\', \'\', \'', e.id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/down.gif\" title=\"Вниз\"></a>')
        			)
        		)
        	)
        	AS priority,
        	e.title as title,
        	e.destination as destination, 
        	e.date_begin as date_begin,
        	e.date_end as date_end, 
        	concat('<img src=\"". $this->tv['HTTP'] ."pub/exhibition/', e.id, '/75x75/', ei.image_filename, '\" />') as img
            FROM %prefix%exhibition e left join exhibition_image ei on((ei.exhibition_id = e.id AND ei.is_core = 1))
            WHERE ".$this->_where." ORDER by e.priority DESC";

        require_once(BASE_CLASSES_PATH . 'controls/navigator.php'); // base application class
        $nav = new Navigator('objects', $query, array(), '', false);
        
        $header_num = $nav->add_header('priority', 'priority');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->set_wrap();
        $nav->headers[$header_num]->align = "center";
        $nav->set_width($header_num, '12%');
        
        $header_num = $nav->add_header($this->Application->Localizer->get_string('title'), 'title');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->align = "left";
        $nav->set_width($header_num, '40%');
        
        $header_num = $nav->add_header($this->Application->Localizer->get_string('destination'), 'destination');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->align = "left";
        $nav->set_width($header_num, '30%');
        
        $header_num = $nav->add_header($this->Application->Localizer->get_string('date_begin'), 'date_begin');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->align = "center";
        $nav->set_width($header_num, '9%');
        
        $header_num = $nav->add_header($this->Application->Localizer->get_string('date_end'), 'date_end');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->align = "center";
        $nav->set_width($header_num, '9%');

        $header_num = $nav->add_header($this->Application->Localizer->get_string('img'), 'img');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->align = "center";
        $nav->headers[$header_num]->set_wrap();
        $nav->set_width($header_num, '75px');

        $this->tv['clickLink'] = $this->Application->Navi->getUri('./exhibition_edit/', true);

        if ($nav->size > 0)
            $this->template_vars[$this->_table . '_show_remove'] = true;
        else
            $this->template_vars[$this->_table . '_show_remove'] = false;
	}
	
	function page_actions() {
		if (CForm::is_submit($this->_table, 'add')) {
			$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('./'.$this->_table.'_edit/', false));
		}

		if (CForm::is_submit($this->_table, 'delete_selected')) {
			$data = InGetPost("ch", array());
            $where="WHERE ".$this->_where;
            if (sizeof($data) > 0) {
            	$where .= " AND ";
                foreach($data as $k => $v){
                	$where.="id = ".$v." OR ";
                	$this->removeDirRec(ROOT. 'pub/exhibition/'. $v . '/');
                }
                $where = substr($where, 0 , -4);
                $sql = 'DELETE FROM %prefix%'.$this->_table.' ' . $where;
                if($this->Application->DataBase->select_custom_sql($sql)) {
                	$exhib_rs = $this->Application->DataBase->select_sql('exhibition');
                	if($exhib_rs !== false && !$exhib_rs->eof()){
                		$priority = 1;
                		while (!$exhib_rs->eof()){
                			if(intval($exhib_rs->get_field('priority')) !== $priority){
                				if(!$this->Application->DataBase->update_sql('exhibition', array('priority' => $priority), array('id' => $exhib_rs->get_field('id')))){
                					$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
                					return false;
                				}
                			}
                			$priority++;
                			$exhib_rs->next();
                		}
                	}
                	$this->tv['_info'][] = $this->Application->Localizer->get_string('objects_deleted');
                } 
                else $this->tv['_errors'][] = $this->Application->Localizer->get_string('internal_error');
           } 
           else $this->tv['_info'][] = $this->Application->Localizer->get_string('noitem_selected');
		}
	}
	
	function removeDirRec($dir)
	{
	    if ($objs = glob($dir."/*")) {
	        foreach($objs as $obj) {
	            if(is_dir($obj)) $this->removeDirRec($obj);
	            elseif(file_exists($obj)) unlink($obj);
	        }
	    }
	    (file_exists($dir)) ? rmdir($dir) : null;
	}
	
	function on_priority_submit($action){
		if($id = InPost('param1')){
			if(!is_numeric($id)){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
				$this->bind_data();
				return false;
			}
			
			$exhib_rs = $this->Application->DataBase->select_sql('exhibition', array('id' => $id));
			if($exhib_rs == false || $exhib_rs->eof()){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
				$this->bind_data();
				return false;
			}
			$exhib_cnt = $this->Application->DataBase->select_custom_sql('SELECT count(id) as cnt FROM exhibition');
			if($exhib_cnt == false){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
				$this->bind_data();
				return false;
			}
			$exhib_cnt = $exhib_cnt->get_field('cnt');
			switch ($action){
				case 'up':
					$priority = $exhib_rs->get_field('priority');
					if(intval($priority) == $exhib_cnt){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('exhibition', array('priority' => ($priority)), array('priority' => ($priority + 1)))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('exhibition', array('priority' => ($priority + 1)), array('priority' => ($priority), 'id' => $id))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					$this->bind_data();
					break;
				case 'down':
					$priority = $exhib_rs->get_field('priority');
					if(intval($priority) == 1){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('exhibition', array('priority' => ($priority)), array('priority' => ($priority - 1)))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('exhibition', array('priority' => ($priority - 1)), array('priority' => ($priority), 'id' => $id))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					$this->bind_data();
					break;
			}
			
		}
	}
}
?>