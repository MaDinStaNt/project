<?
class CNotFoundPage extends CHTMLPage 
{
	function CNotFoundPage(&$app, $template)
	{
		parent::__construct($app, $template);
	}
	
	public function on_page_init()
	{
		parent::on_page_init();
		$this->tv['tnav_act_link'] = 'no';
	}

	public function parse_data()
	{
		if (!parent::parse_data())
			return false;
			
		return true;
	}
}
?>