<?
require_once(CUSTOM_CLASSES_PATH . 'admin/mastereditpage.php');

class CProductEditPage extends CMasterEditPage
{
    /**
     * The table name.
     *
     * @var array
     */
    protected $_table = 'product';

    protected $_module = 'Products';
    
    protected $DataBase;
    
    protected $_where = "product_id = ";

    function CProductEditPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CMasterEditPage($app, $template);
		$this->DataBase = $this->Application->DataBase;
	}

	function on_page_init()
	{
		parent::on_page_init();
	}

	function parse_data()
	{
		if (!parent::parse_data()) return false;
		
			$this->bind_data();
		
		return true;
	}
	
	function on_product_submit($action){
		require_once(CUSTOM_CONTROLS_PATH . 'simpledataoutput.php');
        new CSimpleDataOutput($this);
        require_once(BASE_CONTROLS_PATH . 'simplearrayoutput.php');
        new CSimpleArrayOutput();
        
        $mod = $this->Application->get_module($this->_module);
        
        switch ($action){
        	case 'add_img':
        		$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('./image_edit/', true). "product_id={$this->id}");
        		die();
        		break;
        	case 'delete_selected_img':
        		$data = InGetPost("ch", array());
	            $where="WHERE ".$this->_where . $this->id;
	            if (sizeof($data) > 0) {
	            	$where .= " AND ";
	                foreach($data as $k => $v) $where.="id = ".$v." OR ";
	                $where = substr($where, 0 , -4);
	                $del_img_rs = $this->Application->DataBase->select_custom_sql('SELECT type, product_id, image_filename FROM product_image '. $where);
	                if($del_img_rs == false || $del_img_rs->eof()){
	                	$this->tv['_errors'] = $this->Application->Localizer->get_string('internal_error');
	                	return false;
	                }
	                while (!$del_img_rs->eof()) {
	                	if(trim($del_img_rs->get_field('image_filename')) !== ''){
	                		$file = ROOT. 'pub/production/product/'. $del_img_rs->get_field('product_id'). '/' . $del_img_rs->get_field('image_filename');
	                		if(file_exists($file)){
	                			if($del_img_rs->get_field('type') == TYPE_PRODUCT_IMAGE_PHOTO){
	                				$this->Application->get_module('Images')->delete('product_image', $file, array('product_id' => $del_img_rs->get_field('product_id')));
	                			}
	                			elseif ($del_img_rs->get_field('type') == TYPE_PRODUCT_IMAGE_INSTRUCTION){
	                				$this->Application->get_module('Images')->delete('product_image_instruction', $file, array('product_id' => $del_img_rs->get_field('product_id')));
	                			}
	                			
	                			@unlink($file);
	                		}
	                	}
	                	
	                	$del_img_rs->next();
	                }
	                $sql = 'DELETE FROM %prefix%product_image ' . $where;
	                if($this->Application->DataBase->select_custom_sql($sql)) {
	                	$del_img_rs->first();
	                	while(!$del_img_rs->eof()){
	                		$type_img_rs = $this->Application->DataBase->select_custom_sql("SELECT * FROM product_image WHERE product_id = {$del_img_rs->get_field('product_id')} AND type = '{$del_img_rs->get_field('type')}' ORDER by priority ASC");
	                		if($type_img_rs == false){
	                			 $this->tv['_errors'][] = $this->Application->Localizer->get_string('database_error');
	                			 return false;
	                		}
	                		$del_img_rs->next();
	                		if($type_img_rs->eof()) continue;
	                		$priority = 1;
	                		while (!$type_img_rs->eof()){
	                			if(intval($type_img_rs->get_field('priority')) !== $priority){
	                				if(!$this->Application->DataBase->update_sql('product_image', array('priority' => $priority), array('id' => $type_img_rs->get_field('id')))){
	                					$this->tv['_errors'][] = $this->Application->Localizer->get_string('database_error');
	                			 		return false;
	                				}
	                			}
	                			$priority++;
	                			$type_img_rs->next();
	                		}
	                	}
	                	$this->tv['_info'][] = $this->Application->Localizer->get_string('objects_deleted');
	                	$this->bind_data();
	                	return true;
	                } 
	                else $this->tv['_errors'][] = $this->Application->Localizer->get_string('internal_error');
	           } 
	           else $this->tv['_info'][] = $this->Application->Localizer->get_string('noitem_selected');
        		break;
        		
        	case 'add_td':
        		$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('./technical_data_edit/', true). "product_id={$this->id}");
        		die;
        		break;
        	case 'delete_selected_td':
        		$data = InGetPost("ch", array());
	            $where="WHERE ".$this->_where. $this->id;
	            if (sizeof($data) > 0) {
	            	$where .= " AND ";
	                foreach($data as $k => $v) $where.="id = ".$v." OR ";
	                $where = substr($where, 0 , -4);
	                $sql = 'DELETE FROM %prefix%product_technical_data ' . $where;
	                if($this->Application->DataBase->select_custom_sql($sql)) {
	                	$this->tv['_info'][] = $this->Application->Localizer->get_string('objects_deleted');
	                	$this->bind_data();
	                	return true;
	                } 
	                else $this->tv['_errors'][] = $this->Application->Localizer->get_string('internal_error');
	           } 
	           else $this->tv['_info'][] = $this->Application->Localizer->get_string('noitem_selected');
        		break;
        	case 'add_eq':
        		$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('./equipment_edit/', true). "product_id={$this->id}");
        		die;
        		break;
        	case 'delete_selected_eq':
        		$data = InGetPost("ch", array());
	            $where="WHERE ".$this->_where. $this->id;
	            if (sizeof($data) > 0) {
	            	$where .= " AND ";
	                foreach($data as $k => $v) $where.="id = ".$v." OR ";
	                $where = substr($where, 0 , -4);
	                $sql = 'DELETE FROM %prefix%product_equipment ' . $where;
	                if($this->Application->DataBase->select_custom_sql($sql)) {
	                	$this->tv['_info'][] = $this->Application->Localizer->get_string('objects_deleted');
	                	$this->bind_data();
	                	return true;
	                } 
	                else $this->tv['_errors'][] = $this->Application->Localizer->get_string('internal_error');
	           } 
	           else $this->tv['_info'][] = $this->Application->Localizer->get_string('noitem_selected');
        		break;
        }
        
		if (CForm::is_submit($this->_table)) {
			CValidator::add('title', VRT_TEXT, 0, 255);
			CValidator::add('uri', VRT_TEXT, 0, 255);
			CValidator::add('article', VRT_TEXT, 0, 255);
			CValidator::add('product_category_id', VRT_NUMBER);
			CValidator::add_nr('brief_description', VRT_TEXT, '', 0, 500);
			CValidator::add_nr('description', VRT_TEXT, '', 0, 10000);
			CValidator::add_nr('video_link', VRT_TEXT, '', 0, 255);
			CValidator::add_nr('desc_filename', VRT_CUSTOM_FILE, '', array('application/pdf', 'application/msword'));
			CValidator::add_nr('instruct_filename', VRT_CUSTOM_FILE, '', array('application/pdf', 'application/msword'));
			
			if (CValidator::validate_input()) {
				if ($this->id) {
					
					if ($mod->{'update_'.$this->_table}($this->id, $this->tv)) {
						$this->tv['_info'] = $this->Localizer->get_string('object_updated');
						$this->tv['_return_info'] =  $this->Application->Navi->getUri('parent', false);
					}
					else {
						$this->tv['_errors'] = $mod->get_last_error();
					}
				}
				else {
					if ($this->id = $this->tv['id'] = $mod->{'add_'.$this->_table}($this->tv)) {
						$this->tv['_info'] = $this->Localizer->get_string('object_added');
						$this->tv['_return_info'] =  $this->Application->Navi->getUri('parent', false). '.product_edit&id='. $this->id;
					}
					else {
						$this->tv['_errors'] = $mod->get_last_error();
					}
				}
			}
			else {
				$this->tv['_errors'] = CValidator::get_errors();
			}
			$this->custom_bind_data();
		}
		elseif (CForm::is_submit($this->_table, 'close')) {
			$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('parent', false));
		} 
	}
	
	function bind_data(){
		parent::bind_data();
		
		$pc_rs = $this->DataBase->select_sql('product_category');
		if($pc_rs == false) $pc_rs = new CRecordSet();
		$pc_rs->add_row(array('id' => '', 'title' => RECORDSET_FIRST_ITEM), INSERT_BEGIN);
		CInput::set_select_data('product_category_id', $pc_rs, 'id', 'title');
		
		$query = "SELECT 
        	core.id as id,
        	IF((SELECT COUNT(id) FROM %prefix%product_image WHERE product_id = {$this->id} AND type = core.type) = 1, 
        		CONCAT('одно изображение типа ', core.type,' в продукте'), 
        		IF(core.priority = (SELECT COUNT(id) FROM %prefix%product_image WHERE product_id = {$this->id} AND type = core.type), 
        			CONCAT('<a href=\"javascript: priority_submit(\'down\', \'\', \'', core.type, '_', core.id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/down.gif\" title=\"Вниз\"></a>'), 
        			IF(core.priority = 1, 
        				CONCAT('<a href=\"javascript: priority_submit(\'up\', \'\', \'', core.type, '_', core.id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/up.gif\" title=\"Вверх\"></a>'), 
        				CONCAT('<a href=\"javascript: priority_submit(\'up\', \'\', \'', core.type, '_', core.id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/up.gif\" title=\"Вверх\"></a>', ' <a href=\"javascript: priority_submit(\'down\', \'\', \'', core.type, '_', core.id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/down.gif\" title=\"Вниз\"></a>')
        			)
        		)
        	)
        	AS priority,
        	core.title as title,
        	core.type as type, 
        	concat('<img src=\"". $this->tv['HTTP'] ."pub/production/product/', product_id, if(type = '". TYPE_PRODUCT_IMAGE_PHOTO ."', '/75x75/', '/75x50/'), image_filename, '\" />') as img,
        	concat('<img src=\"". $this->tv['HTTP'] ."images/icons/', if(is_core = 1, 'apply.gif', 'apply_disabled.gif'), '\" />') as is_core 
            FROM %prefix%product_image core
            WHERE ".$this->_where.$this->id ." GROUP by core.type ASC, core.priority DESC";

        require_once(BASE_CLASSES_PATH . 'controls/navigator.php'); // base application class
        $nav = new Navigator('images', $query, array('title' => 'title', 'type' => 'type', 'img' => 'img', 'is_core' => 'is_core'), 'create_date', false);
        
        $header_num = $nav->add_header('priority', 'priority');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->set_wrap();
        $nav->headers[$header_num]->align = "center";
        $nav->set_width($header_num, '20%');
        
        $header_num = $nav->add_header($this->Application->Localizer->get_string('title'), 'title');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->align = "left";
        $nav->set_width($header_num, '70%');
        
        $header_num = $nav->add_header($this->Application->Localizer->get_string('type'), 'type');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->align = "left";
        $nav->set_width($header_num, '10%');

        $header_num = $nav->add_header($this->Application->Localizer->get_string('img'), 'img');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->set_wrap();
        $nav->headers[$header_num]->align = "center";
        $nav->set_width($header_num, '75px');
        
        $header_num = $nav->add_header($this->Application->Localizer->get_string('is_core'), 'is_core');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->set_wrap();
        $nav->headers[$header_num]->align = "center";
        $nav->set_width($header_num, '75px');

        $this->tv['clickLink_img'] = $this->Application->Navi->getUri('./image_edit/', false). '&product_id='. $this->id .'&';

        if ($nav->size > 0)
            $this->template_vars['image_show_remove'] = true;
        else
            $this->template_vars['image_show_remove'] = false;
            
        $query = "SELECT 
        	id,
        	technical_data,
        	value
            FROM %prefix%product_technical_data
            WHERE ".$this->_where.$this->id;

        require_once(BASE_CLASSES_PATH . 'controls/navigator.php'); // base application class
        $nav = new Navigator('techdata', $query, array('technical_data' => 'technical_data', 'value' => 'value'), 'create_date', false);
        
        $header_num = $nav->add_header($this->Application->Localizer->get_string('technical_data'), 'technical_data');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->align = "right";
        $nav->set_width($header_num, '30%');
        
        $header_num = $nav->add_header($this->Application->Localizer->get_string('value'), 'value');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->align = "left";
        $nav->set_width($header_num, '70%');

        $this->tv['clickLink_td'] = $this->Application->Navi->getUri('./technical_data_edit/', false). '&product_id='. $this->id .'&';

        if ($nav->size > 0)
            $this->template_vars['td_show_remove'] = true;
        else
            $this->template_vars['td_show_remove'] = false; 
            
        $query = "SELECT 
        	id,
        	title
            FROM %prefix%product_equipment
            WHERE ".$this->_where.$this->id;

        require_once(BASE_CLASSES_PATH . 'controls/navigator.php'); // base application class
        $nav = new Navigator('equipment', $query, array('title' => 'title'), 'create_date', false);
        
        $header_num = $nav->add_header($this->Application->Localizer->get_string('title'), 'title');
        $nav->headers[$header_num]->no_escape = true;
        $nav->headers[$header_num]->align = "left";
        $nav->set_width($header_num, '100%');
        
        $this->tv['clickLink_eq'] = $this->Application->Navi->getUri('./equipment_edit/', false). '&product_id='. $this->id .'&';

        if ($nav->size > 0)
            $this->template_vars['eq_show_remove'] = true;
        else
            $this->template_vars['eq_show_remove'] = false;   
	}
	
	function on_priority_submit($action){
		if($id = InPost('param1')){
			$ids = explode("_", $id);
			if(!is_array($ids)){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
				$this->bind_data();
				return false;
			}
			$id = $ids[1];
			$type = $ids[0];
			if(!is_numeric($id)){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
				$this->bind_data();
				return false;
			}
			
			$product_img_rs = $this->Application->DataBase->select_sql('product_image', array('id' => $id));
			if($product_img_rs == false || $product_img_rs->eof()){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
				$this->bind_data();
				return false;
			}
			$product_img_cnt = $this->Application->DataBase->select_custom_sql('SELECT count(id) as cnt FROM product_image WHERE product_id = '. $product_img_rs->get_field('product_id') .' AND type = "'.$type.'"');
			if($product_img_cnt == false){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
				$this->bind_data();
				return false;
			}
			$product_img_cnt = $product_img_cnt->get_field('cnt');
			switch ($action){
				case 'up':
					$priority = $product_img_rs->get_field('priority');
					$product_id = $product_img_rs->get_field('product_id');
					if(intval($priority) == $product_img_cnt){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('product_image', array('priority' => ($priority)), array('priority' => ($priority + 1), 'product_id' => $product_id, 'type' => $type))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('product_image', array('priority' => ($priority + 1)), array('priority' => ($priority), 'id' => $id, 'type' => $type))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					$this->bind_data();
					break;
				case 'down':
					$priority = $product_img_rs->get_field('priority');
					$product_id = $product_img_rs->get_field('product_id');
					if(intval($priority) == 1){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('product_image', array('priority' => ($priority)), array('priority' => ($priority - 1), 'product_id' => $product_id, 'type' => $type))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('product_image', array('priority' => ($priority - 1)), array('priority' => ($priority), 'id' => $id))){
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