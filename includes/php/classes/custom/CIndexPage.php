<?
require_once(CUSTOM_CONTROLS_PATH.'pager.php');
require_once(FUNCTION_PATH.'functions.image.php');

class CIndexPage extends CHTMLPage 
{
	function CIndexPage(&$app, $template)
	{
		parent::__construct($app, $template);
	}
	
	public function on_page_init()
	{
		parent::on_page_init();
		$this->tv['tnav_act_link'] = 'tnav_index';
		
		$Registry = $this->Application->get_module('Registry');
		$catalog_link = $Registry->get_value('_static/_main/_catalog_pdf_link');
		if(!$catalog_link){
			$this->tv['catalog_link'] = false;
		}
		else 
		{
			$this->tv['catalog_link'] = CTemplate::parse_string(htmlspecialchars_decode($catalog_link));
		}
		
		$video_rs = $this->Application->DataBase->select_custom_sql("SELECT rt.path_id as path_id, rt.name as type, rv.name as name, rv.value as value FROM registry_tree rt join registry_values rv on((rt.path_id = rv.path_id)) WHERE rt.name='_main' OR rt.name='_contact'");
		if($video_rs == false || $video_rs->eof()){
			$this->tv['video_not_found'] = true;
		}
		else{
			$this->tv['video_not_found'] = true;
			$this->tv['contact_not_found'] = true;
			while (!$video_rs->eof()){
				if($video_rs->get_field('name') == '_video' && trim($video_rs->get_field('value')) !== ''){
					$this->tv['video_not_found'] = false;
					$this->tv['video_path'] = $video_rs->get_field('path_id') . "/" . $video_rs->get_field('value');
					$video_rs->next();	
					continue;
				}
				if($video_rs->get_field('name') == '_video_img' && trim($video_rs->get_field('value')) !== ''){
					$this->tv['video_img_path'] = $video_rs->get_field('path_id') . "/" . $video_rs->get_field('value');
					$video_rs->next();	
					continue;
				}
				if($video_rs->get_field('name') == '_sm_img' && trim($video_rs->get_field('value')) !== ''){
					$this->tv['sm_img_path'] = $video_rs->get_field('path_id') . "/" . $video_rs->get_field('value');
					$video_rs->next();	
					continue;
				}
				if($video_rs->get_field('name') == '_big_img' && trim($video_rs->get_field('value')) !== ''){
					$this->tv['big_img_path'] = $video_rs->get_field('path_id') . "/" . $video_rs->get_field('value');
					$video_rs->next();	
					continue;
				}
				
				if($video_rs->get_field('name') == '_title' && $video_rs->get_field('type') == '_contact' && trim($video_rs->get_field('value')) !== ''){
					$this->tv['contact_title'] = $video_rs->get_field('value');
					$this->tv['contact_not_found'] = false;
					$video_rs->next();	
					continue;
				}
				
				if($video_rs->get_field('name') == '_address' && trim($video_rs->get_field('value')) !== ''){
					$this->tv['contact_address'] = $video_rs->get_field('value');
					$this->tv['contact_not_found'] = false;
					$video_rs->next();	
					continue;
				}
				
				if($video_rs->get_field('name') == '_telephone' && trim($video_rs->get_field('value')) !== ''){
					$this->tv['contact_tel'] = $video_rs->get_field('value');
					$this->tv['contact_not_found'] = false;
					$video_rs->next();	
					continue;
				}
				
				if($video_rs->get_field('name') == '_fax' && trim($video_rs->get_field('value')) !== ''){
					$this->tv['contact_fax'] = $video_rs->get_field('value');
					$this->tv['contact_not_found'] = false;
					$video_rs->next();	
					continue;
				}
				
				if($video_rs->get_field('name') == '_email' && trim($video_rs->get_field('value')) !== ''){
					$this->tv['contact_email'] = $video_rs->get_field('value');
					$this->tv['contact_not_found'] = false;
					$video_rs->next();	
					continue;
				}
				
				$video_rs->next();	
			}
		}
		
		$this->tv['_pat_link'] = $Registry->get_value('_static/_core/_pat_link');
		$this->tv['_video_link'] = $Registry->get_value('_static/_core/_video_link');
		
		if($this->tv['sm_img_path'] && $this->tv['big_img_path']){
			$this->tv['contact_not_found'] = false;
			$this->tv['contact_show_map'] = true;
		}
		
		$this->bind_exhibitions();
	}

	public function parse_data()
	{
		if (!parent::parse_data())
			return false;
			
		return true;
	}
	
	function bind_exhibitions(){
		$this->tv['curr_year'] = $curr_year = date("Y");
		$exhibition_rs = $this->Application->DataBase->select_custom_sql("SELECT e.id as id, e.title as title, e.destination as destination, e.abbreviation as abbreviation, DATE_FORMAT(e.date_begin, '%d.%m') as date_begin, DATE_FORMAT(e.date_end, '%d.%m') as date_end, ei.image_filename as img FROM exhibition e left join exhibition_image ei on((ei.exhibition_id = e.id AND is_core = 1)) WHERE e.date_end LIKE '%{$curr_year}%' LIMIT 3");
		if($exhibition_rs == false || $exhibition_rs->eof()) {
			$this->tv['exhibitions_not_found'] = true;
			return true;
		}
		$this->tv['ex_showed_id'] = $exhibition_rs->get_field('id');
		$this->tv['ex_showed_img'] = $exhibition_rs->get_field('img');
		recordset_to_vars($exhibition_rs, $this->tv, 'ex_cnt', 'ex_');
	}
}
?>