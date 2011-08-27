<?
class CProducts
{
	var $Application;
	var $DataBase;
	var $tv;
	var $last_error;
	var $loc;

	function CProducts(&$app)
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
		$rs = $this->DataBase->select_sql('product');
		if($rs === false || $rs->eof()){
			$this->last_error = $this->loc->get_string('objects_not_found');
			return false;
		}
		
		return $rs;
	}

	function add_product_category($arr){
		if(!is_array($arr)){
			$this->last_error = $this->loc->get_string('invalid_input');
			return false;
		}
		
		$double_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM product_category WHERE title = '{$arr['title']}' OR uri = '{$arr['uri']}'");
		if($double_rs == false){
			$this->last_error = $this->loc->get_string('internal_error');
			return false;
		}
		
		if(intval($double_rs->get_field('cnt')) > 0){
			$this->last_error = $this->loc->get_string('object_exists');
			return false;
		}
		
		$priority_rs = $this->DataBase->select_custom_sql("SELECT priority FROM product_category ORDER by priority DESC LIMIT 1");
		if($priority_rs == false){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		$priority = ($priority_rs->eof()) ? 1 : intval($priority_rs->get_field('priority')) + 1;
		
		$insert_arr = array(
			'title' => $arr['title'],
			'uri' => $arr['uri'],
			'description' => $arr['description'],
			'priority' => $priority
		);
		
		if(!$this->tv['id'] = $id = $this->DataBase->insert_sql('product_category', $insert_arr)){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		if(isset($_FILES['image_filename']) && trim($_FILES['image_filename']['name']) !== ''){
			if(!file_exists(ROOT . 'pub/production/')){
				@mkdir(ROOT . 'pub/production/');
			}
			
			if(!file_exists(ROOT . 'pub/production/category/')){
				@mkdir(ROOT . 'pub/production/category/');
			}
			
			$folder = ROOT . 'pub/production/category/'. $id .'/';
			if(!file_exists($folder)){
				@mkdir($folder);
			}
			
			$this->tv['image_filename'] = $filename = save_file_to_folder('image_filename', "pub/production/category/{$id}/", false, false);
			$this->Application->get_module('Images')->create('product_category', $folder . $filename, array('category_id' => $id));
			
			if(!$this->DataBase->update_sql('product_category', array('image_filename' => $filename), array('id' => $id))){
				$this->last_error = $this->loc->get_string('database_error');
				return false;
			}
		}
		
		return $id;
	}
	
	function update_product_category($id, $arr){
		if(!is_array($arr) || !is_numeric($id)){
			$this->last_error = $this->loc->get_string('invalid_input');
			return false;
		}
		
		$double_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM product_category WHERE (title = '{$arr['title']}' OR uri = '{$arr['uri']}') AND id <> {$id}");
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
			'uri' => $arr['uri'],
			'description' => $arr['description']
		);
		
		if(isset($_FILES['image_filename']) && trim($_FILES['image_filename']['name']) !== ''){
			if(!file_exists(ROOT . 'pub/production/')){
				@mkdir(ROOT . 'pub/production/');
			}
			
			if(!file_exists(ROOT . 'pub/production/category/')){
				@mkdir(ROOT . 'pub/production/category/');
			}
			
			$folder = ROOT . 'pub/production/category/'. $id .'/';
			if(!file_exists($folder)){
				@mkdir($folder);
			}
			
			$old_rs = $this->DataBase->select_sql('product_category', array('id' => $id));
			if($old_rs == false || $old_rs->eof()){
				$this->last_error = $this->loc->get_string('internal_error');
				return false;
			}
			
			if(file_exists($folder . $old_rs->get_field('image_filename'))){
				$this->Application->get_module('Images')->delete('product_category', $folder . $old_rs->get_field('image_filename'), array('category_id' => $id));
				@unlink($folder . $old_rs->get_field('image_filename'));
			}
			
			$this->tv['image_filename'] = $filename = save_file_to_folder('image_filename', "pub/production/category/{$id}/", false, false);
			$this->Application->get_module('Images')->create('product_category', $folder . $filename, array('category_id' => $id));
			
			$update_arr['image_filename'] = $filename;
		}
		
		if(!$this->DataBase->update_sql('product_category', $update_arr, array('id' => $id))){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		return true;
	}
	
	function add_product($arr){
		if(!is_array($arr)){
			$this->last_error = $this->loc->get_string('invalid_input');
			return false;
		}
		
		$double_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM product WHERE (title = '{$arr['title']}' OR uri = '{$arr['uri']}')");
		if($double_rs == false){
			$this->last_error = $this->loc->get_string('internal_error');
			return false;
		}
		
		if(intval($double_rs->get_field('cnt')) > 0){
			$this->last_error = $this->loc->get_string('object_exists');
			return false;
		}
		
		$priority_rs = $this->DataBase->select_custom_sql("SELECT priority FROM product WHERE product_category_id = {$arr['product_category_id']} ORDER by priority DESC LIMIT 1");
		if($priority_rs == false){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		$priority = ($priority_rs->eof()) ? 1 : intval($priority_rs->get_field('priority')) + 1;
		
		$insert_arr = array(
			'title' => $arr['title'],
			'uri' => $arr['uri'],
			'product_category_id' => $arr['product_category_id'],
			'article' => $arr['article'],
			'brief_description' => $arr['brief_description'],
			'description' => $arr['description'],
			'video_link' => $arr['video_link'],
			'priority' => $priority
		);
		
		if(!$id = $this->DataBase->insert_sql('product', $insert_arr)){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		$update_arr = array();
		
		if(isset($_FILES['desc_filename']) && strlen($_FILES['desc_filename']['name']) > 0){
			if(!file_exists(ROOT . 'pub/production/')){
				@mkdir(ROOT . 'pub/production/');
			}
			
			if(!file_exists(ROOT . 'pub/production/product/')){
				@mkdir(ROOT . 'pub/production/product/');
			}
			
			$folder = ROOT . 'pub/production/product/'. $id .'/';
			if(!file_exists($folder)){
				@mkdir($folder);
			}
			
			$update_arr['desc_filename'] = save_file_to_folder('desc_filename', $folder);
		}
		
		if(isset($_FILES['instruct_filename']) && strlen($_FILES['instruct_filename']['name']) > 0){
			if(!file_exists(ROOT . 'pub/production/')){
				@mkdir(ROOT . 'pub/production/');
			}
			
			if(!file_exists(ROOT . 'pub/production/product/')){
				@mkdir(ROOT . 'pub/production/product/');
			}
			
			$folder = ROOT . 'pub/production/product/'. $id .'/';
			if(!file_exists($folder)){
				@mkdir($folder);
			}
			
			$update_arr['instruct_filename'] = save_file_to_folder('instruct_filename', $folder);
		}
		
		if(!empty($update_arr)){
			if(!$this->DataBase->update_sql('product', $update_arr, array('id' => $id))){
				$this->last_error = $this->loc->get_string('database_error');
				return false;
			}
		}
		
		return $id;
	}
	
	function update_product($id, $arr){
		if(!is_array($arr) || !is_numeric($id)){
			$this->last_error = $this->loc->get_string('invalid_input');
			return false;
		}
		
		$double_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM product WHERE (title = '{$arr['title']}' OR uri = '{$arr['uri']}') AND id <> {$id}");
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
			'uri' => $arr['uri'],
			'product_category_id' => $arr['product_category_id'],
			'article' => $arr['article'],
			'brief_description' => $arr['brief_description'],
			'description' => $arr['description'],
			'video_link' => $arr['video_link'],
		);
		
		$old_rs = $this->DataBase->select_sql('product', array('id' => $id));
		if($old_rs == false || $old_rs->eof()){
			$this->last_error = $this->loc->get_string('internal_error');
			return false;
		}
		
		$folder = ROOT . 'pub/production/product/'. $id .'/';
		(trim($old_rs->get_field('desc_filename')) !== '') ? $old_desc_file = $folder . $old_rs->get_field('desc_filename') : null;
		if(isset($_FILES['desc_filename']) && strlen($_FILES['desc_filename']['name']) > 0){
			if(!file_exists(ROOT . 'pub/production/')){
				@mkdir(ROOT . 'pub/production/');
			}
			
			if(!file_exists(ROOT . 'pub/production/product/')){
				@mkdir(ROOT . 'pub/production/product/');
			}
			
			
			if(!file_exists($folder)){
				@mkdir($folder);
			}
			
			if($old_desc_file && file_exists($old_desc_file)) @unlink($old_desc_file);
			
			$update_arr['desc_filename'] = save_file_to_folder('desc_filename', $folder);
		}
		
		if($old_desc_file && intval($arr['delete_desc_filename']) > 0 && file_exists($old_desc_file))
		{
			@unlink($old_desc_file);
			$update_arr['desc_filename'] = '';
		}
		
		(trim($old_rs->get_field('instruct_filename')) !== '') ? $old_instruct_file = $folder . $old_rs->get_field('instruct_filename') : null;
		if(isset($_FILES['instruct_filename']) && strlen($_FILES['instruct_filename']['name']) > 0){
			if(!file_exists(ROOT . 'pub/production/')){
				@mkdir(ROOT . 'pub/production/');
			}
			
			if(!file_exists(ROOT . 'pub/production/product/')){
				@mkdir(ROOT . 'pub/production/product/');
			}
			
			if(!file_exists($folder)){
				@mkdir($folder);
			}
			
			if($old_instruct_file && file_exists($old_instruct_file)) @unlink($old_instruct_file);
			
			$update_arr['instruct_filename'] = save_file_to_folder('instruct_filename', $folder);
		}
		
		if($old_instruct_file && intval($arr['delete_instruct_filename']) > 0 && file_exists($old_instruct_file))
		{
			@unlink($old_instruct_file);
			$update_arr['instruct_filename'] = '';
		}
		
		if(!$id = $this->DataBase->update_sql('product', $update_arr, array('id' => $id))){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		return $id;
	}
	
	function add_product_image($arr){
		if(!is_array($arr) || !is_numeric($arr['product_id'])){
			$this->last_error = $this->loc->get_string('invalid_input');
			return false;
		}
		
		$double_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM product_image WHERE title = '{$arr['title']}' AND product_id = '{$arr['product_id']}'");
		if($double_rs == false){
			$this->last_error = $this->loc->get_string('internal_error');
			return false;
		}
		
		if(intval($double_rs->get_field('cnt')) > 0){
			$this->last_error = $this->loc->get_string('object_exists');
			return false;
		}
		
		$priority_rs = $this->DataBase->select_custom_sql("SELECT priority FROM product_image WHERE product_id = {$arr['product_id']} AND type = '{$arr['type']}' ORDER by priority DESC LIMIT 1");
		if($priority_rs == false){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		$priority = ($priority_rs->eof()) ? 1 : intval($priority_rs->get_field('priority')) + 1;
		
		$insert_arr = array(
			'title' => $arr['title'],
			'product_id' => $arr['product_id'],
			'type' => $arr['type'],
			'description' => $arr['description'],
			'is_core' => intval($arr['is_core']),
			'priority' => $priority
		);
		
		if(intval($arr['is_core']) > 0){
			if(!$this->DataBase->update_sql('product_image', array('is_core' => 0), array('is_core' => 1, 'type' => $arr['type'], 'product_id' => $arr['product_id']))){
				$this->last_error = $this->loc->get_string('database_error');
				return false;
			}
		}
		
		if(!$this->tv['id'] = $id = $this->DataBase->insert_sql('product_image', $insert_arr)){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		if(isset($_FILES['image_filename']) && trim($_FILES['image_filename']['name']) !== ''){
			if(!file_exists(ROOT . 'pub/production/')){
				@mkdir(ROOT . 'pub/production/');
			}
			
			if(!file_exists(ROOT . 'pub/production/product/')){
				@mkdir(ROOT . 'pub/production/product/');
			}
			
			$folder = ROOT . 'pub/production/product/'. $arr['product_id'] .'/';
			if(!file_exists($folder)){
				@mkdir($folder);
			}
			
			$this->tv['image_filename'] = $filename = save_file_to_folder('image_filename', "pub/production/product/{$arr['product_id']}/", false, false);
			if($arr['type'] === TYPE_PRODUCT_IMAGE_PHOTO){
				$this->Application->get_module('Images')->create('product_image', $folder . $filename, array('product_id' => $arr['product_id']));
			}
			elseif($arr['type'] === TYPE_PRODUCT_IMAGE_INSTRUCTION){
				$this->Application->get_module('Images')->create('product_image_instruction', $folder . $filename, array('product_id' => $arr['product_id']));
			}
			
			if(!$this->DataBase->update_sql('product_image', array('image_filename' => $filename), array('id' => $id))){
				$this->last_error = $this->loc->get_string('database_error');
				return false;
			}
		}
		
		return $id;
	}
	
	function update_product_image($id, $arr){
		if(!is_array($arr) || !is_numeric($id) || !is_numeric($arr['product_id'])){
			$this->last_error = $this->loc->get_string('invalid_input');
			return false;
		}
		
		$double_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM product_image WHERE title = '{$arr['title']}' AND product_id = '{$arr['product_id']}' AND id <> {$id}");
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
			'product_id' => $arr['product_id'],
			'type' => $arr['type'],
			'description' => $arr['description'],
			'is_core' => intval($arr['is_core'])
		);
		
		if(intval($arr['is_core']) > 0){
			if(!$this->DataBase->update_sql('product_image', array('is_core' => 0), array('is_core' => 1, 'type' => $arr['type'], 'product_id' => $arr['product_id']))){
				$this->last_error = $this->loc->get_string('database_error');
				return false;
			}
		}
		
		if(isset($_FILES['image_filename']) && trim($_FILES['image_filename']['name']) !== ''){
			if(!file_exists(ROOT . 'pub/production/')){
				@mkdir(ROOT . 'pub/production/');
			}
			
			if(!file_exists(ROOT . 'pub/production/product/')){
				@mkdir(ROOT . 'pub/production/product/');
			}
			
			$folder = ROOT . 'pub/production/product/'. $arr['product_id'] .'/';
			if(!file_exists($folder)){
				@mkdir($folder);
			}
			
			$old_rs = $this->DataBase->select_sql('product_image', array('id' => $id));
			if($old_rs == false || $old_rs->eof()){
				$this->last_error = $this->loc->get_string('internal_error');
				return false;
			}
			
			if(file_exists($folder . $old_rs->get_field('image_filename'))){
				if($old_rs->get_field('type') == TYPE_PRODUCT_IMAGE_PHOTO){
					$this->Application->get_module('Images')->delete('product_image', $folder . $old_rs->get_field('image_filename'), array('product_id' => $id));
				}
				elseif($old_rs->get_field('type') == TYPE_PRODUCT_IMAGE_INSTRUCTION){
					$this->Application->get_module('Images')->delete('product_image_instruction', $folder . $old_rs->get_field('image_filename'), array('product_id' => $id));
				}
				
				@unlink($folder . $old_rs->get_field('image_filename'));
			}
			
			$this->tv['image_filename'] = $filename = save_file_to_folder('image_filename', "pub/production/product/{$arr['product_id']}/", false, false);
			if($arr['type'] == TYPE_PRODUCT_IMAGE_PHOTO){
				$this->Application->get_module('Images')->create('product_image', $folder . $filename, array('product_id' => $arr['product_id']));
			}
			elseif($arr['type'] == TYPE_PRODUCT_IMAGE_INSTRUCTION){
				$this->Application->get_module('Images')->create('product_image_instruction', $folder . $filename, array('product_id' => $arr['product_id']));
			}
			
			$update_arr['image_filename'] = $filename;
		}
		
		if(!$this->DataBase->update_sql('product_image', $update_arr, array('id' => $id))){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		return true;
	}
	
	function add_product_technical_data($arr){
		if(!is_array($arr) || !is_numeric($arr['product_id'])){
			$this->last_error = $this->loc->get_string('invalid_input');
			return false;
		}
		
		$double_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM product_technical_data WHERE technical_data = '{$arr['technical_data']}' AND product_id = '{$arr['product_id']}'");
		if($double_rs == false){
			$this->last_error = $this->loc->get_string('internal_error');
			return false;
		}
		
		if(intval($double_rs->get_field('cnt')) > 0){
			$this->last_error = $this->loc->get_string('object_exists');
			return false;
		}
		
		$insert_arr = array(
			'technical_data' => $arr['technical_data'],
			'value' => $arr['value'],
			'product_id' => $arr['product_id']
		);
		
		if(!$id = $this->DataBase->insert_sql('product_technical_data', $insert_arr)){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		return $id;
	}
	
	function update_product_technical_data($id, $arr){
		if(!is_array($arr) || !is_numeric($id) || !is_numeric($arr['product_id'])){
			$this->last_error = $this->loc->get_string('invalid_input');
			return false;
		}
		
		$double_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM product_technical_data WHERE technical_data = '{$arr['technical_data']}' AND product_id = '{$arr['product_id']}' AND id <> {$id}");
		if($double_rs == false){
			$this->last_error = $this->loc->get_string('internal_error');
			return false;
		}
		
		if(intval($double_rs->get_field('cnt')) > 0){
			$this->last_error = $this->loc->get_string('object_exists');
			return false;
		}
		
		$update_arr = array(
			'technical_data' => $arr['technical_data'],
			'value' => $arr['value'],
			'product_id' => $arr['product_id']
		);
		
		if(!$id = $this->DataBase->update_sql('product_technical_data', $update_arr, array('id' => $id))){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		return $id;
	}
	
	function add_product_equipment($arr){
		if(!is_array($arr) || !is_numeric($arr['product_id'])){
			$this->last_error = $this->loc->get_string('invalid_input');
			return false;
		}
		
		$double_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM product_equipment WHERE title = '{$arr['title']}' AND product_id = '{$arr['product_id']}'");
		if($double_rs == false){
			$this->last_error = $this->loc->get_string('internal_error');
			return false;
		}
		
		if(intval($double_rs->get_field('cnt')) > 0){
			$this->last_error = $this->loc->get_string('object_exists');
			return false;
		}
		
		$insert_arr = array(
			'title' => $arr['title'],
			'product_id' => $arr['product_id']
		);
		
		if(!$id = $this->DataBase->insert_sql('product_equipment', $insert_arr)){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		return $id;
	}
	
	function update_product_equipment($id, $arr){
		if(!is_array($arr) || !is_numeric($id) || !is_numeric($arr['product_id'])){
			$this->last_error = $this->loc->get_string('invalid_input');
			return false;
		}
		
		$double_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM product_equipment WHERE title = '{$arr['title']}' AND product_id = '{$arr['product_id']}' AND id <> {$id}");
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
			'product_id' => $arr['product_id']
		);
		
		if(!$id = $this->DataBase->update_sql('product_equipment', $update_arr, array('id' => $id))){
			$this->last_error = $this->loc->get_string('database_error');
			return false;
		}
		
		return $id;
	}
    
};
?>