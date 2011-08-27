<?
require_once(CUSTOM_CONTROLS_PATH.'pager.php');
require_once(FUNCTION_PATH.'functions.image.php');

class CCategoriesPage extends CHTMLPage 
{
	function CCategoriesPage(&$app, $template)
	{
		parent::__construct($app, $template);
	}
	
	public function on_page_init()
	{
		parent::on_page_init();
		$this->tv['tnav_act_link'] = 'no';
		require_once(CUSTOM_CONTROLS_PATH. 'navleft.php');
		new CNavLeftCtrl($this);
		$this->bind_data();
	}

	public function parse_data()
	{
		if (!parent::parse_data())
			return false;
			
		return true;
	}
	
	function bind_data(){
		$Registry = $this->Application->get_module('Registry');
		
		$this->tv['_pat_link'] = $Registry->get_value('_static/_core/_pat_link');
		$this->tv['_video_link'] = $Registry->get_value('_static/_core/_video_link');
		
		$category_rs = $this->Application->DataBase->select_custom_sql('SELECT * FROM product_category ORDER by priority DESC');
		if($category_rs == false || $category_rs->eof()){
			$this->tv['category_not_found'] = true;
			return false;
		}
		
		$this->tv['cat_cnt_lines'] = $cnt_lines = ceil($category_rs->get_record_count()/2);
		
		if($cnt_lines > 0){
			for ($i = 0; $i < $cnt_lines; $i++){
				$this->tv['cat_cnt_in_line'][$i] = 0;
				for($k = 0; $k < 2; $k++){
					if($category_rs->eof()) continue;
					$this->tv['cat_id'][$i][$k] = $category_rs->get_field('id');
					$this->tv['cat_title'][$i][$k] = $category_rs->get_field('title');
					$this->tv['cat_uri'][$i][$k] = $category_rs->get_field('uri');
					$this->tv['cat_img'][$i][$k] = $category_rs->get_field('image_filename');
					$this->tv['cat_desc'][$i][$k] = $category_rs->get_field('description');
					$this->tv['cat_cnt_in_line'][$i]++;
					$category_rs->next();
				}
			}
		}
	}
}
?>