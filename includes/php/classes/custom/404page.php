<?
class C404Page extends CHTMLPage 
{
	function C404Page(&$app, $template)
	{
		parent::__construct($app, $template);
	}
	
	public function on_page_init()
	{
		parent::no_html('404page.tpl');
	}

	public function parse_data()
	{
		if (!parent::parse_data())
			return false;
			
		return true;
	}
}
?>