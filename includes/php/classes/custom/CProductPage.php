<?
require_once(CUSTOM_CONTROLS_PATH.'pager.php');
require_once(FUNCTION_PATH.'functions.image.php');

class CProductPage extends CHTMLPage 
{
	protected $category_id;
	protected $category_uri;
	protected $product_id;
	protected $product_uri;
	
	function CProductPage(&$app, $template)
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
		if(!$this->product_uri = $this->tv['product_uri'] = InUri('product_uri')) $this->page_not_found();
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
			p.description as description,
			p.video_link as video_link,
			p.desc_filename as desc_filename,
			p.instruct_filename as instruct_filename,
			ptd.technical_data as technical_data,
			ptd.value as value,
			pe.title as equipment_title,
			pi.type as type_img,
			pi.image_filename as img,
			pi.description as img_title
			FROM product p left join product_image pi on ((pi.product_id = p.id)) left join product_technical_data ptd on((ptd.product_id = p.id)) left join product_equipment pe on((pe.product_id = p.id))
			WHERE p.uri = '{$this->product_uri}' ORDER by pi.type DESC, pi.priority DESC");
		
		if($product_rs == false || $product_rs->eof()) $this->page_not_found();
		
		$this->tv['prod_id'] = $product_rs->get_field('id');
		$this->tv['prod_title'] = $product_rs->get_field('title');
		$this->tv['prod_article'] = $product_rs->get_field('article');
		$this->tv['prod_description'] = $product_rs->get_field('description');
		$this->tv['prod_video_link'] = $product_rs->get_field('video_link');
		$this->tv['prod_desc_filename'] = $product_rs->get_field('desc_filename');
		$this->tv['prod_instruct_filename'] = $product_rs->get_field('instruct_filename');
		global $AdministratorEmail;
		$this->tv['prod_mail_link'] = $AdministratorEmail;
		
		$this->tv['prod_tech_data'] = array();
		$this->tv['prod_equip_title'] = array();
		$this->tv['prod_img'] = array();
		$this->tv['prod_img_instruction'] = array();
		$this->tv['prod_tech_data_cnt'] = 0;
		$this->tv['prod_equip_cnt'] = 0;
		$this->tv['prod_img_cnt'] = 0;
		$this->tv['prod_img_inst_cnt'] = 0;
		$inst_img_rs =  new CRecordSet();
		while (!$product_rs->eof()){
			if(trim($product_rs->get_field('technical_data')) !== '' && !in_array($product_rs->get_field('technical_data'), $this->tv['prod_tech_data'])){
				$this->tv['prod_tech_data'][] = $product_rs->get_field('technical_data');
				if(preg_match("/\n/", $product_rs->get_field('value'))){
					$exp = explode("\n", $product_rs->get_field('value'));
					if(is_array($exp)){
						$str = "<ul>";
						foreach ($exp as $value) $str .= "<li>{$value}</li>";
						$str .= "</ul>";
						$this->tv['prod_tech_value'][] = $str;
					}
					else{
						$this->tv['prod_tech_value'][] = nl2br($product_rs->get_field('value'))."<br />";
					}
				}
				else{
					$this->tv['prod_tech_value'][] = nl2br($product_rs->get_field('value'))."<br />";
				}
				$this->tv['prod_tech_data_cnt']++;
			}
			if(trim($product_rs->get_field('equipment_title')) !== '' && !in_array($product_rs->get_field('equipment_title'), $this->tv['prod_equip_title'])){
				$this->tv['prod_equip_title'][] = $product_rs->get_field('equipment_title');
				$this->tv['prod_equip_cnt']++;
			}
			if($product_rs->get_field('type_img') == TYPE_PRODUCT_IMAGE_PHOTO && !in_array($product_rs->get_field('img'), $this->tv['prod_img'])){
				$this->tv['prod_img'][] = $product_rs->get_field('img');
				$this->tv['prod_img_title'][] = $product_rs->get_field('img_title');
				//$this->tv['prod_img_numb'][] = $this->tv['prod_img_cnt'] + 1;
				$this->tv['prod_img_cnt']++;
			}
			if($product_rs->get_field('type_img') == TYPE_PRODUCT_IMAGE_INSTRUCTION && !in_array($product_rs->get_field('img'), $this->tv['prod_img_instruction'])){
				$inst_img_rs->add_row(array('img' => $product_rs->get_field('img'), 'img_title' => $product_rs->get_field('img_title')));
				$this->tv['prod_img_instruction'][] = $product_rs->get_field('img');
				$this->tv['prod_img_inst_cnt']++;
			}
			$product_rs->next();
		}
		
		if(intval($this->tv['prod_tech_data_cnt']) == 0){
			$this->tv['prod_tech_data_not_found'] = true;
		}
		if(intval($this->tv['prod_equip_cnt']) == 0){
			$this->tv['prod_equip_not_found'] = true;
		}
		if(intval($this->tv['prod_img_cnt']) == 0){
			$this->tv['prod_img_not_found'] = true;
		}
		if(intval($this->tv['prod_img_inst_cnt']) == 0){
			$this->tv['prod_img_inst_not_found'] = true;
		}
		
		if(!$inst_img_rs->eof()){
			$this->tv['inst_img_cnt_lines'] = $cnt_lines = ceil($inst_img_rs->get_record_count() / 2);
			for($i = 0; $i < $cnt_lines; $i++){
				$this->tv['inst_img_cnt_in_line'][$i] = 0;
				for($k = 0; $k < 2; $k++){
					if($inst_img_rs->eof()) continue;
					$this->tv['inst_img'][$i][$k] = $inst_img_rs->get_field('img');
					$this->tv['inst_img_title'][$i][$k] = $inst_img_rs->get_field('img_title');
					$this->tv['inst_img_cnt_in_line'][$i]++;
					$inst_img_rs->next();
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