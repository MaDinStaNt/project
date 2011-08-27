<?
require_once(CUSTOM_CONTROLS_PATH.'pager.php');
require_once(FUNCTION_PATH.'functions.image.php');

class CContactsPage extends CHTMLPage 
{
	function CContactsPage(&$app, $template)
	{
		parent::__construct($app, $template);
	}
	
	public function on_page_init()
	{
		parent::on_page_init();
		$this->tv['tnav_act_link'] = 'tnav_contacts';
		
		$Registry = $this->Application->get_module('Registry');
		$catalog_link = $Registry->get_value('_static/_main/_catalog_pdf_link');
		if(!$catalog_link){
			$this->tv['catalog_link'] = false;
		}
		else 
		{
			$this->tv['catalog_link'] = CTemplate::parse_string(htmlspecialchars_decode($catalog_link));
		}
		
		$contact_rs = $this->Application->DataBase->select_custom_sql("SELECT rt.path_id as path_id, rv.name as name, rv.value as value FROM registry_tree rt join registry_values rv on((rt.path_id = rv.path_id)) WHERE rt.name='_contact'");
		if($contact_rs == false || $contact_rs->eof()){
			$this->tv['contact_not_found'] = true;
		}
		else{
			$this->tv['contact_not_found'] = true;
			while (!$contact_rs->eof()){
				$type = $contact_rs->get_field('name');
				$this->tv['path_id'] = $contact_rs->get_field('path_id');
				
				if(trim($contact_rs->get_field('value')) !== ''){
					$this->tv['contact_not_found'] = false;
					$this->tv["contact{$type}_show"] = true;
					$this->tv["contact{$type}"] = $contact_rs->get_field('value');
				}
				
				$contact_rs->next();	
			}
		}
		
		$this->tv['_pat_link'] = $Registry->get_value('_static/_core/_pat_link');
		$this->tv['_video_link'] = $Registry->get_value('_static/_core/_video_link');
		
	}

	public function parse_data()
	{
		if (!parent::parse_data())
			return false;
			
		return true;
	}
}
?>