<?
require_once(CUSTOM_CONTROLS_PATH.'pager.php');
require_once(FUNCTION_PATH.'functions.image.php');

class CAboutPage extends CHTMLPage 
{
	function CAboutPage(&$app, $template)
	{
		parent::__construct($app, $template);
	}
	
	public function on_page_init()
	{
		parent::on_page_init();
		$this->tv['tnav_act_link'] = 'no';
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
		
		$template = $Registry->get_value('_static/_about/_template');
		$path_id = $Registry->_get_path_id(1, '_about');
		if(!$path_id){
			$this->tv['errors'] = $this->Application->Localizer->get_string('invalid_registry_path');
			return false;
		}
		$this->tv['image_1'] = '<img src="'.$this->tv['HTTP'].'_r/'.$path_id.'/'.$Registry->get_value('_static/_about/_img_1').'" title="" alt="" />';
		$this->tv['image_2'] = '<img src="'.$this->tv['HTTP'].'_r/'.$path_id.'/'.$Registry->get_value('_static/_about/_img_2').'" title="" alt="" />';
		$this->tv['image_3'] = '<img src="'.$this->tv['HTTP'].'_r/'.$path_id.'/'.$Registry->get_value('_static/_about/_img_3').'" title="" alt="" />';
		$this->tv['image_4'] = '<img src="'.$this->tv['HTTP'].'_r/'.$path_id.'/'.$Registry->get_value('_static/_about/_img_4').'" title="" alt="" />';
		$this->tv['image_5'] = '<img src="'.$this->tv['HTTP'].'_r/'.$path_id.'/'.$Registry->get_value('_static/_about/_img_5').'" title="" alt="" />';
		
		$this->tv['description'] = CTemplate::parse_string(htmlspecialchars_decode($template));
	}
}
?>