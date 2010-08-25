<?
require_once(CUSTOM_CLASSES_PATH . 'admin/mastereditpage.php');

class CCountryEditPage extends CMasterEditPage
{
    /**
     * The table name.
     *
     * @var array
     */
    protected $_table = 'country';

    protected $_module = 'Countries';

    function CCountryEditPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CMasterEditPage($app, $template);
	}

	function on_page_init()
	{
		parent::on_page_init();
		CValidator::add('title', VRT_TEXT, 0, 255);
		CValidator::add('abbreviation', VRT_TEXT, 2, 2);
	}
	
	function parse_data()
	{
		parent::parse_data();
	}
}
?>