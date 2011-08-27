<?
require_once(CUSTOM_CONTROLS_PATH.'pager.php');
require_once(FUNCTION_PATH.'functions.image.php');

class CCategoryPage extends CHTMLPage 
{
	protected $category_id;
	protected $category_uri;
	
	function CCategoryPage(&$app, $template)
	{
		parent::__construct($app, $template);
	}
	
	public function on_page_init()
	{
		parent::on_page_init();
		require_once(CUSTOM_CONTROLS_PATH. 'navleft.php');
		$this->tv['tnav_act_link'] = 'no';
		new CNavLeftCtrl($this);
		if(!$this->category_uri = $this->tv['category_uri'] = InUri('category_uri')) $this->page_not_found();
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
		
		$category_rs = $this->Application->DataBase->select_sql('product_category', array('uri' => $this->category_uri));
		if($category_rs == false || $category_rs->eof()) $this->page_not_found();
		$this->category_id = $category_rs->get_field('id');
		$this->tv['cat_title'] = $category_rs->get_field('title');
		
		$product_rs = $this->Application->DataBase->select_custom_sql("SELECT p.id as id, 
			p.title as title, 
			p.uri as uri, 
			p.article as article, 
			p.brief_description as brief_description, 
			pi.image_filename as image_filename, 
			pi.description as img_title
			FROM product p left join product_image pi on((pi.product_id = p.id AND pi.is_core = 1))
			WHERE p.product_category_id = {$this->category_id} ORDER by p.priority DESC");
		if($product_rs == false || $product_rs->eof()){
			$this->tv['product_not_found'] = true;
			return false;
		}
		
		$this->tv['prod_cnt_lines'] = $cnt_lines = ceil($product_rs->get_record_count()/2);
		
		if($cnt_lines > 0){
			for ($i = 0; $i < $cnt_lines; $i++){
				$this->tv['prod_cnt_in_line'][$i] = 0;
				for($k = 0; $k < 2; $k++){
					if($product_rs->eof()) continue;
					$this->tv['prod_id'][$i][$k] = $product_rs->get_field('id');
					$this->tv['prod_title'][$i][$k] = $product_rs->get_field('title');
					$this->tv['prod_article'][$i][$k] = $product_rs->get_field('article');
					$this->tv['prod_uri'][$i][$k] = $product_rs->get_field('uri');
					$this->tv['prod_img'][$i][$k] = $product_rs->get_field('image_filename');
					$this->tv['prod_img_title'][$i][$k] = $product_rs->get_field('img_title');
					$this->tv['prod_brief_desc'][$i][$k] = (trim($product_rs->get_field('brief_description')) == '') ? $this->Application->Localizer->get_string('brief_description_not_found') : $product_rs->get_field('brief_description');
					$this->tv['prod_cnt_in_line'][$i]++;
					$product_rs->next();
				}
			}
		}
	}
	
	function page_not_found(){
		$this->Application->CurrentPage->internalRedirect($this->tv['HTTP']."page-not-found.html");
		die();
	}
}
?>