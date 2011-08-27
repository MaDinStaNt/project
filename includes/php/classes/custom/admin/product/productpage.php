<?php
require_once(CUSTOM_CLASSES_PATH . 'admin/masterpage.php');

class CProductPage extends CMasterPage
{
    /**
     * The table name.
     *
     * @var array
     */
    protected $_table = 'product';
    /**
     * The table name.
     *
     * @var array
     */
    protected $_filters = array();
    protected $DataBase;

	function CproductPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CMasterPage($app, $template);
		$this->DataBase = &$this->Application->DataBase;
		$pc_rs = $this->DataBase->select_sql('product_category');
		if($pc_rs == false) $pc_rs = new CRecordSet();
		$pc_rs->add_row(array('id' => '', 'title' => RECORDSET_FIRST_ITEM), INSERT_BEGIN);
		
	    $this->_filters = array(
	    	'pc#id' => array(
	    		'title' => $this->Application->Localizer->get_string('product_category'),	
	    		'type' => FILTER_SELECT,
	    		'data' => array($pc_rs, 'id', 'title'),
	    		'condition' => CONDITION_EQUAL
	    	),
	    	'p#title' => array(
	    		'title' => $this->Application->Localizer->get_string('title'),	
	    		'type' => FILTER_TEXT,
	    		'data' => null,
	    		'condition' => CONDITION_LIKE
	    	),
	    	'p#article' => array(
	    		'title' => $this->Application->Localizer->get_string('article'),	
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
        	p.id,
        	IF((SELECT COUNT(id) FROM %prefix%product WHERE product_category_id = p.product_category_id) = 1, 'один продукт в категории', IF(p.priority = (SELECT COUNT(id) FROM %prefix%product WHERE product_category_id = p.product_category_id), CONCAT('<a href=\"javascript: priority_submit(\'down\', \'\', \'', p.id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/down.gif\" title=\"Вниз\"></a>'), IF(p.priority = 1, CONCAT('<a href=\"javascript: priority_submit(\'up\', \'\', \'', p.id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/up.gif\" title=\"Вверх\"></a>'), CONCAT('<a href=\"javascript: priority_submit(\'up\', \'\', \'', p.id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/up.gif\" title=\"Вверх\"></a>', ' <a href=\"javascript: priority_submit(\'down\', \'\', \'', p.id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/down.gif\" title=\"Вниз\"></a>'))))
        	AS priority,
        	pc.title as pc_title,
        	p.title as title, 
        	p.article as article,
        	concat('<img src=\"". $this->tv['HTTP'] ."pub/production/product/', p.id, '/75x75/', pi.image_filename, '\" />') as img
            FROM %prefix%product p join product_category pc on((p.product_category_id = pc.id)) left join product_image pi on((pi.is_core = 1 AND pi.type = '". TYPE_PRODUCT_IMAGE_PHOTO . "' AND pi.product_id = p.id))
            WHERE ".$this->_where ." GROUP by pc.priority DESC, p.priority DESC";

        require_once(BASE_CLASSES_PATH . 'controls/navigator.php'); // base application class
        $nav = new Navigator('objects', $query, array(), '', false);
        
        $header_num = $nav->add_header('priority', 'priority');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->set_wrap();
        $nav->headers[$header_num]->align = "center";
        $nav->set_width($header_num, '10%');
        
        $header_num = $nav->add_header($this->Application->Localizer->get_string('product_category'), 'pc_title');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->align = "left";
        $nav->set_width($header_num, '30%');
        
        $header_num = $nav->add_header($this->Application->Localizer->get_string('title'), 'title');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->align = "left";
        $nav->set_width($header_num, '40%');
        
        $header_num = $nav->add_header($this->Application->Localizer->get_string('article'), 'article');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->align = "left";
        $nav->set_width($header_num, '20%');

        $header_num = $nav->add_header($this->Application->Localizer->get_string('img'), 'img');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->set_wrap();
        $nav->set_width($header_num, '75px');

        $this->tv['clickLink'] = $this->Application->Navi->getUri('./product_edit/', true);

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
                	$this->removeDirRec(ROOT. 'pub/production/product/'. $v . '/');
                }
                $where = substr($where, 0 , -4);
                $sql = 'DELETE FROM %prefix%'.$this->_table.' ' . $where;
                $del_product_rs = $this->Application->DataBase->select_custom_sql("SELECT product_category_id FROM {$this->_table} {$where}");
                if($del_product_rs == false || $del_product_rs->eof()){
                	$this->tv['_errors'][] = $this->Application->Localizer->get_string('internal_error');
                	return false;
                }
                if($this->Application->DataBase->select_custom_sql($sql)) {
                	while(!$del_product_rs->eof()){
                		$cat_prod_rs = $this->Application->DataBase->select_custom_sql("SELECT * FROM product WHERE product_category_id = {$del_product_rs->get_field('product_category_id')} ORDER by priority ASC");
                		if($cat_prod_rs == false){
                			 $this->tv['_errors'][] = $this->Application->Localizer->get_string('database_error');
                			 return false;
                		}
                		$del_product_rs->next();
                		if($cat_prod_rs->eof()) continue;
                		$priority = 1;
                		while (!$cat_prod_rs->eof()){
                			if(intval($cat_prod_rs->get_field('priority')) !== $priority){
                				if(!$this->Application->DataBase->update_sql('product', array('priority' => $priority), array('id' => $cat_prod_rs->get_field('id')))){
                					$this->tv['_errors'][] = $this->Application->Localizer->get_string('database_error');
                			 		return false;
                				}
                			}
                			$priority++;
                			$cat_prod_rs->next();
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
			
			$product_rs = $this->Application->DataBase->select_sql('product', array('id' => $id));
			if($product_rs == false || $product_rs->eof()){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
				$this->bind_data();
				return false;
			}
			$product_cnt = $this->Application->DataBase->select_custom_sql('SELECT count(id) as cnt FROM product WHERE product_category_id = '. $product_rs->get_field('product_category_id'));
			if($product_cnt == false){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
				$this->bind_data();
				return false;
			}
			$product_cnt = $product_cnt->get_field('cnt');
			switch ($action){
				case 'up':
					$priority = $product_rs->get_field('priority');
					$product_category_id = $product_rs->get_field('product_category_id');
					if(intval($priority) == $product_cnt){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('product', array('priority' => ($priority)), array('priority' => ($priority + 1), 'product_category_id' => $product_category_id))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('product', array('priority' => ($priority + 1)), array('priority' => ($priority), 'id' => $id))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					$this->bind_data();
					break;
				case 'down':
					$priority = $product_rs->get_field('priority');
					$product_category_id = $product_rs->get_field('product_category_id');
					if(intval($priority) == 1){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('product', array('priority' => ($priority)), array('priority' => ($priority - 1), 'product_category_id' => $product_category_id))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('product', array('priority' => ($priority - 1)), array('priority' => ($priority), 'id' => $id))){
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