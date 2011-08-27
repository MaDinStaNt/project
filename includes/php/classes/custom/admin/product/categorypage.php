<?php
require_once(CUSTOM_CLASSES_PATH . 'admin/masterpage.php');

class CCategoryPage extends CMasterPage
{
    /**
     * The table name.
     *
     * @var array
     */
    protected $_table = 'product_category';
    /**
     * The table name.
     *
     * @var array
     */
    protected $_filters = array();

	function CCategoryPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CMasterPage($app, $template);
		$this->DataBase = &$this->Application->DataBase;
	    $this->_filters = array(
	    	'title' => array(
	    		'title' => $this->Application->Localizer->get_string('title'),	
	    		'type' => FILTER_TEXT,
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
        	id,
        	IF(priority = (SELECT COUNT(id) FROM %prefix%product_category), CONCAT('<a href=\"javascript: priority_submit(\'down\', \'\', \'', id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/down.gif\" title=\"Вниз\"></a>'), IF(priority = 1, CONCAT('<a href=\"javascript: priority_submit(\'up\', \'\', \'', id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/up.gif\" title=\"Вверх\"></a>'), CONCAT('<a href=\"javascript: priority_submit(\'up\', \'\', \'', id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/up.gif\" title=\"Вверх\"></a>', ' <a href=\"javascript: priority_submit(\'down\', \'\', \'', id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/down.gif\" title=\"Вниз\"></a>')))
        	AS priority,
        	title,
        	concat('<img src=\"". $this->tv['HTTP'] ."pub/production/category/', id, '/75x75/', image_filename, '\" />') as img
            FROM %prefix%product_category
            WHERE ".$this->_where. " GROUP by priority DESC";

        require_once(BASE_CLASSES_PATH . 'controls/navigator.php'); // base application class
        $nav = new Navigator('objects', $query, array(), '', false);
        
        $header_num = $nav->add_header('priority', 'priority');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->set_wrap();
        $nav->headers[$header_num]->align = "center";
        $nav->set_width($header_num, '5%');
        
        $header_num = $nav->add_header($this->Application->Localizer->get_string('title'), 'title');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->align = "left";
        $nav->set_width($header_num, '95%');

        $header_num = $nav->add_header($this->Application->Localizer->get_string('img'), 'img');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->set_wrap();
        $nav->set_width($header_num, '75px');

        $this->tv['clickLink'] = $this->Application->Navi->getUri('./product_category_edit/', true);

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
                	$this->removeDirRec(ROOT. 'pub/production/category/'. $v . '/');
                }
                $where = substr($where, 0 , -4);
                $sql = 'DELETE FROM %prefix%'.$this->_table.' ' . $where;
                if($this->Application->DataBase->select_custom_sql($sql)) {
                	$product_category_rs = $this->Application->DataBase->select_sql('product_category');
                	if($product_category_rs !== false && !$product_category_rs->eof()){
                		$priority = 1;
                		while (!$product_category_rs->eof()){
                			if(intval($product_category_rs->get_field('priority')) !== $priority){
                				if(!$this->Application->DataBase->update_sql('product_category', array('priority' => $priority), array('id' => $product_category_rs->get_field('id')))){
                					$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
                					return false;
                				}
                			}
                			$priority++;
                			$product_category_rs->next();
                		}
                	}
                	$this->tv['_info'][] = $this->Application->Localizer->get_string('objects_deleted');
                } 
                else $this->tv['_errors'][] = $this->Application->Localizer->get_string('internal_error');
           } 
           else $this->tv['_info'][] = $this->Application->Localizer->get_string('noitem_selected');
		}
	}
	
	function on_priority_submit($action){
		if($id = InPost('param1')){
			if(!is_numeric($id)){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
				$this->bind_data();
				return false;
			}
			
			$product_category_rs = $this->Application->DataBase->select_sql('product_category', array('id' => $id));
			if($product_category_rs == false || $product_category_rs->eof()){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
				$this->bind_data();
				return false;
			}
			$product_category_cnt = $this->Application->DataBase->select_custom_sql('SELECT count(id) as cnt FROM product_category');
			if($product_category_cnt == false){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
				$this->bind_data();
				return false;
			}
			$product_category_cnt = $product_category_cnt->get_field('cnt');
			switch ($action){
				case 'up':
					$priority = $product_category_rs->get_field('priority');
					if(intval($priority) == $product_category_cnt){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('product_category', array('priority' => ($priority)), array('priority' => ($priority + 1)))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('product_category', array('priority' => ($priority + 1)), array('priority' => ($priority), 'id' => $id))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					$this->bind_data();
					break;
				case 'down':
					$priority = $product_category_rs->get_field('priority');
					if(intval($priority) == 1){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('product_category', array('priority' => ($priority)), array('priority' => ($priority - 1)))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('product_category', array('priority' => ($priority - 1)), array('priority' => ($priority), 'id' => $id))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					$this->bind_data();
					break;
			}
			
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
}
?>