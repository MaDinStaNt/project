<?
require_once(CUSTOM_CONTROLS_PATH.'pager.php');
require_once(FUNCTION_PATH.'functions.image.php');

class CPartnershipPage extends CHTMLPage 
{
	function CPartnershipPage(&$app, $template)
	{
		parent::__construct($app, $template);
	}
	
	public function on_page_init()
	{
		parent::on_page_init();
		$this->tv['tnav_act_link'] = 'tnav_partnership';
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
		
		$this->tv['description'] = $Registry->get_value('_static/_partnership/_template');
	}
}
?>