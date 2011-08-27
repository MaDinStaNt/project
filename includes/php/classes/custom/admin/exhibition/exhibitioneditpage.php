<?
require_once(CUSTOM_CLASSES_PATH . 'admin/mastereditpage.php');

class CExhibitionEditPage extends CMasterEditPage
{
    /**
     * The table name.
     *
     * @var array
     */
    protected $_table = 'exhibition';

    protected $_module = 'Exhibitions';
    
    protected $DataBase;
    
    protected $_where = "1=1";

    function CExhibitionEditPage(&$app, $template)
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
		
			if($this->id) $this->bind_data();
			else parent::bind_data();
			
			if($this->id){
				$this->tv['date_begin'] = date("m/d/Y", strtotime($this->tv['date_begin']));
				$this->tv['date_end'] = date("m/d/Y", strtotime($this->tv['date_end']));
			}
		
		return true;
	}
	
	function on_exhibition_submit($action){
		require_once(CUSTOM_CONTROLS_PATH . 'simpledataoutput.php');
        new CSimpleDataOutput($this);
        require_once(BASE_CONTROLS_PATH . 'simplearrayoutput.php');
        new CSimpleArrayOutput();
        
        $mod = $this->Application->get_module($this->_module);
        
        switch ($action){
        	case 'add_img':
        		$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('./image_edit/', true). "exhibition_id={$this->id}");
        		die();
        		break;
        	case 'delete_selected_img':
        		$data = InGetPost("ch", array());
	            $where="WHERE ".$this->_where;
	            if (sizeof($data) > 0) {
	            	$where .= " AND ";
	                foreach($data as $k => $v) $where.="id = ".$v." OR ";
	                $where = substr($where, 0 , -4);
	                $del_img_rs = $this->Application->DataBase->select_custom_sql('SELECT exhibition_id, image_filename FROM exhibition_image '. $where);
	                if($del_img_rs == false || $del_img_rs->eof()){
	                	$this->tv['_errors'] = $this->Application->Localizer->get_string('internal_error');
	                	return false;
	                }
	                while (!$del_img_rs->eof()) {
	                	if(trim($del_img_rs->get_field('image_filename')) !== ''){
	                		$file = ROOT. 'pub/exhibition/'. $del_img_rs->get_field('exhibition_id'). '/' . $del_img_rs->get_field('image_filename');
               				$this->Application->get_module('Images')->delete('exhibition', $file, array('exhibition_id' => $del_img_rs->get_field('exhibition_id')));
	                		@unlink($file);
	                	}
	                	$del_img_rs->next();
	                }
	                $sql = 'DELETE FROM %prefix%exhibition_image ' . $where;
	                if($this->Application->DataBase->select_custom_sql($sql)) {
	                	$del_img_rs->first();
	                	while (!$del_img_rs->eof()){
		                	$exhib_img_rs = $this->Application->DataBase->select_custom_sql("SELECT * FROM exhibition_image WHERE exhibition_id = {$del_img_rs->get_field('exhibition_id')} ORDER by priority ASC");
	                		if($exhib_img_rs == false){
	                			 $this->tv['_errors'][] = $this->Application->Localizer->get_string('database_error');
	                			 return false;
	                		}
	                		$del_img_rs->next();
	                		if($exhib_img_rs->eof()) continue;
	                		$priority = 1;
	                		while (!$exhib_img_rs->eof()){
	                			if(intval($exhib_img_rs->get_field('priority')) !== $priority){
	                				if(!$this->Application->DataBase->update_sql('exhibition_image', array('priority' => $priority), array('id' => $exhib_img_rs->get_field('id')))){
	                					$this->tv['_errors'][] = $this->Application->Localizer->get_string('database_error');
	                			 		return false;
	                				}
	                			}
	                			$priority++;
	                			$exhib_img_rs->next();
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
        		
        }
        
		if (CForm::is_submit($this->_table)) {
			CValidator::add('title', VRT_TEXT, 0, 255);
			CValidator::add('uri', VRT_TEXT, 0, 255);
			CValidator::add('destination', VRT_TEXT, 0, 255);
			CValidator::add('abbreviation', VRT_TEXT, 0, 5);
			CValidator::add('date_begin', VRT_DATE);
			CValidator::add('date_end', VRT_DATE);
			
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
						$this->tv['_return_info'] =  $this->Application->Navi->getUri('parent', false). '.exhibition_edit&id='. $this->id;
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
		
		if($this->id){
			$query = "SELECT 
	        	id,
	        	IF((SELECT COUNT(id) FROM %prefix%exhibition_image WHERE exhibition_id = {$this->id}) = 1, 
	        		'одна фотография в выставке', 
	        		IF(priority = (SELECT COUNT(id) FROM %prefix%exhibition_image WHERE exhibition_id = {$this->id}), 
	        			CONCAT('<a href=\"javascript: priority_submit(\'down\', \'\', \'', id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/down.gif\" title=\"Вниз\"></a>'), 
	        			IF(priority = 1, 
	        				CONCAT('<a href=\"javascript: priority_submit(\'up\', \'\', \'', id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/up.gif\" title=\"Вверх\"></a>'), 
	        				CONCAT('<a href=\"javascript: priority_submit(\'up\', \'\', \'', id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/up.gif\" title=\"Вверх\"></a>', ' <a href=\"javascript: priority_submit(\'down\', \'\', \'', id, '\',0);\"><img src=\"". $this->tv['HTTP'] ."/images/icons/down.gif\" title=\"Вниз\"></a>')
	        			)
	        		)
	        	) AS priority,
	        	title,
	        	concat('<img src=\"". $this->tv['HTTP'] ."pub/exhibition/', exhibition_id, '/75x75/', image_filename, '\" />') as img,
	        	concat('<img src=\"". $this->tv['HTTP'] ."images/icons/', if(is_core = 1, 'apply.gif', 'apply_disabled.gif'), '\" />') as is_core 
	            FROM %prefix%exhibition_image
	            WHERE exhibition_id = ". InGet('id') . ' AND ' . $this->_where ." GROUP by priority DESC";
	
	        require_once(BASE_CLASSES_PATH . 'controls/navigator.php'); // base application class
	        $nav = new Navigator('images', $query, array(), '', false);
	        
	        $header_num = $nav->add_header('priority', 'priority');
	        $nav->headers[$header_num]->no_escape = true;
	        $nav->headers[$header_num]->set_wrap();
	        $nav->headers[$header_num]->align = "center";
	        $nav->set_width($header_num, '10%');
	        
	        $header_num = $nav->add_header($this->Application->Localizer->get_string('title'), 'title');
	        $nav->headers[$header_num]->no_escape = true;
	        $nav->headers[$header_num]->align = "left";
	        $nav->set_width($header_num, '100%');
	
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
	
	        $this->tv['clickLink_img'] = $this->Application->Navi->getUri('./image_edit/', false). '&exhibition_id='. $this->id .'&';
	
	        if ($nav->size > 0)
	            $this->template_vars['image_show_remove'] = true;
	        else
	            $this->template_vars['image_show_remove'] = false;
		}
            
	}
	
	function on_priority_submit($action){
		if($id = InPost('param1')){
			if(!is_numeric($id)){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
				$this->bind_data();
				return false;
			}
			
			$exhib_img_rs = $this->Application->DataBase->select_sql('exhibition_image', array('id' => $id));
			if($exhib_img_rs == false || $exhib_img_rs->eof()){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
				$this->bind_data();
				return false;
			}
			$exhib_img_cnt = $this->Application->DataBase->select_custom_sql('SELECT count(id) as cnt FROM exhibition_image WHERE exhibition_id = '. $exhib_img_rs->get_field('exhibition_id'));
			if($exhib_img_cnt == false){
				$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
				$this->bind_data();
				return false;
			}
			$exhib_img_cnt = $exhib_img_cnt->get_field('cnt');
			switch ($action){
				case 'up':
					$priority = $exhib_img_rs->get_field('priority');
					$exhib_id = $exhib_img_rs->get_field('exhibition_id');
					if(intval($priority) == $product_cnt){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('exhibition_image', array('priority' => ($priority)), array('priority' => ($priority + 1), 'exhibition_id' => $exhib_id))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('exhibition_image', array('priority' => ($priority + 1)), array('priority' => ($priority), 'id' => $id))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					$this->bind_data();
					break;
				case 'down':
					$priority = $exhib_img_rs->get_field('priority');
					$exhib_id = $exhib_img_rs->get_field('exhibition_id');
					if(intval($priority) == 1){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('exhibition_image', array('priority' => ($priority)), array('priority' => ($priority - 1), 'exhibition_id' => $exhib_id))){
						$this->tv['_errors'] = $this->Application->Localizer->get_string('database_error');
						$this->bind_data();
						return false;
					}
					if(!$this->DataBase->update_sql('exhibition_image', array('priority' => ($priority - 1)), array('priority' => ($priority), 'id' => $id))){
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