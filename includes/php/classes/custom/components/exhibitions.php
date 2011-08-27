<?
class CExhibitions
{
	var $Application;
	var $DataBase;
	var $tv;
	var $last_error;
	var $loc;

	function CExhibitions(&$app)
	{
		$this->Application = &$app;
		$this->tv = &$app->template_vars;
		$this->DataBase = &$this->Application->DataBase;
		$this->loc = $this->Application->Localizer;
	}

	function get_last_error()
	{
		return $this->last_error;
	}
	
	function get(){
		$rs = $this->DataBase->select_sql('exhibition');
		if($rs === false || $rs->eof()){
			$this->last_error = $this->loc->get_string('objects_not_found');
			return false;
		}
		
		return $rs;
	}
	
	function add_exhibition_image($arr){
		if(!is_array($arr) || !is_numeric($arr['exhibition_id'])){
			$this->last_error = $this->loc->get_string('invalid_input');
			return false;
		}
		
		$double_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM exhibition_image WHERE title = '{$arr['title']}'");
		if($double_rs == false){
			$this->last_error = $this->loc->get_string('internal_error');
			return false;
		}
		
		if(intval($double_rs->get_field('cnt')) > 0){
			$this->last_error = $this->loc->get_string('object_exists');
			return false;
		}
		
		$priority_rs = $this->DataBase->select_custom_sql("SELECT priority FROM exhibition_image WHERE exhibition_id = {$arr['exhibition_id']} ORDER by priority DESC LIMIT 1");
		if($priority_rs == false){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		$priority = ($priority_rs->eof()) ? 1 : intval($priority_rs->get_field('priority')) + 1;
		
		$insert_arr = array(
			'title' => $arr['title'],
			'exhibition_id' => $arr['exhibition_id'],
			'description' => $arr['description'],
			'is_core' => intval($arr['is_core']),
			'priority' => $priority
		);
		
		if(intval($arr['is_core']) > 0){
			if(!$this->DataBase->update_sql('exhibition_image', array('is_core' => 0), array('is_core' => 1, 'exhibition_id' => $arr['exhibition_id']))){
				$this->last_error = $this->loc->get_string('database_error');
				return false;
			}
		}
		
		if(!$this->tv['id'] = $id = $this->DataBase->insert_sql('exhibition_image', $insert_arr)){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		if(isset($_FILES['image_filename']) && trim($_FILES['image_filename']['name']) !== ''){
			if(!file_exists(ROOT . 'pub/exhibition/')){
				@mkdir(ROOT . 'pub/exhibition/');
			}
			
			$folder = ROOT . 'pub/exhibition/'. $arr['exhibition_id'] .'/';
			if(!file_exists($folder)){
				@mkdir($folder);
			}
			
			$this->tv['image_filename'] = $filename = save_file_to_folder('image_filename', "pub/exhibition/{$arr['exhibition_id']}/", false, false);
			$this->Application->get_module('Images')->create('exhibition', $folder . $filename, array('exhibition_id' => $arr['exhibition_id']));
			
			if(!$this->DataBase->update_sql('exhibition_image', array('image_filename' => $filename), array('id' => $id))){
				$this->last_error = $this->loc->get_string('database_error');
				return false;
			}
		}
		
		return $id;
	}
	
	function update_exhibition_image($id, $arr){
		if(!is_array($arr) || !is_numeric($id) || !is_numeric($arr['exhibition_id'])){
			$this->last_error = $this->loc->get_string('invalid_input');
			return false;
		}
		
		$double_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM exhibition_image WHERE title = '{$arr['title']}' AND id <> {$id}");
		if($double_rs === false){
			$this->last_error = $this->loc->get_string('internal_error');
			return false;
		}
		
		if(intval($double_rs->get_field('cnt')) > 0){
			$this->last_error = $this->loc->get_string('object_exists');
			return false;
		}
		
		$update_arr = array(
			'title' => $arr['title'],
			'exhibition_id' => $arr['exhibition_id'],
			'description' => $arr['description'],
			'is_core' => intval($arr['is_core'])
		);
		
		if(isset($_FILES['image_filename']) && trim($_FILES['image_filename']['name']) !== ''){
			if(!file_exists(ROOT . 'pub/exhibition/')){
				@mkdir(ROOT . 'pub/exhibition/');
			}
			
			if(!file_exists(ROOT . 'pub/exhibition/')){
				@mkdir(ROOT . 'pub/exhibition/');
			}
			
			$folder = ROOT . 'pub/exhibition/'. $arr['exhibition_id'] .'/';
			if(!file_exists($folder)){
				@mkdir($folder);
			}
			
			$old_rs = $this->DataBase->select_sql('exhibition_image', array('id' => $id));
			if($old_rs == false || $old_rs->eof()){
				$this->last_error = $this->loc->get_string('internal_error');
				return false;
			}
			
			if(file_exists($folder . $old_rs->get_field('image_filename'))){
				$this->Application->get_module('Images')->delete('exhibition', $folder . $old_rs->get_field('image_filename'), array('exhibition_id' => $id));
				@unlink($folder . $old_rs->get_field('image_filename'));
			}
			
			$this->tv['image_filename'] = $filename = save_file_to_folder('image_filename', "pub/exhibition/{$arr['exhibition_id']}/", false, false);
			$this->Application->get_module('Images')->create('exhibition', $folder . $filename, array('exhibition_id' => $arr['exhibition_id']));
			
			$update_arr['image_filename'] = $filename;
		}
		
		if(intval($arr['is_core']) > 0){
			if(!$this->DataBase->update_sql('exhibition_image', array('is_core' => 0), array('is_core' => 1, 'exhibition_id' => $arr['exhibition_id']))){
				$this->last_error = $this->loc->get_string('database_error');
				return false;
			}
		}
		
		if(!$this->DataBase->update_sql('exhibition_image', $update_arr, array('id' => $id))){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		return true;
	}
	
	function add_exhibition($arr){
		if(!is_array($arr)){
			$this->last_error = $this->loc->get_string('invalid_input');
			return false;
		}
		
		$double_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM exhibition WHERE title = '{$arr['title']}'");
		if($double_rs == false){
			$this->last_error = $this->loc->get_string('internal_error');
			return false;
		}
		
		if(intval($double_rs->get_field('cnt')) > 0){
			$this->last_error = $this->loc->get_string('object_exists');
			return false;
		}
		
		$priority_rs = $this->DataBase->select_custom_sql("SELECT priority FROM exhibition ORDER by priority DESC LIMIT 1");
		if($priority_rs == false){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		$priority = ($priority_rs->eof()) ? 1 : intval($priority_rs->get_field('priority')) + 1;
		
		$insert_arr = array(
			'title' => $arr['title'],
			'destination' => $arr['destination'],
			'abbreviation' => $arr['abbreviation'],
			'date_begin' => date("Y-m-d", strtotime($arr['date_begin'])),
			'date_end' => date("Y-m-d", strtotime($arr['date_end'])),
			'priority' => $priority
		);
		
		if(!$id = $this->DataBase->insert_sql('exhibition', $insert_arr)){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		return $id;
	}
	
	function update_exhibition($id, $arr){
		if(!is_array($arr)){
			$this->last_error = $this->loc->get_string('invalid_input');
			return false;
		}
		
		$double_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM exhibition WHERE title = '{$arr['title']}' AND id <> {$id}");
		if($double_rs == false){
			$this->last_error = $this->loc->get_string('internal_error');
			return false;
		}
		
		if(intval($double_rs->get_field('cnt')) > 0){
			$this->last_error = $this->loc->get_string('object_exists');
			return false;
		}
		
		$update_arr = array(
			'title' => $arr['title'],
			'destination' => $arr['destination'],
			'abbreviation' => $arr['abbreviation'],
			'date_begin' => date("Y-m-d", strtotime($arr['date_begin'])),
			'date_end' => date("Y-m-d", strtotime($arr['date_end']))
		);
		
		if(!$id = $this->DataBase->update_sql('exhibition', $update_arr, array('id' => $id))){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		return $id;
	}
    
};
?>