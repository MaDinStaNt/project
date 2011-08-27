<?
require_once(CUSTOM_CLASSES_PATH . 'admin/mastereditpage.php');

class CCategoryEditPage extends CMasterEditPage
{
    /**
     * The table name.
     *
     * @var array
     */
    protected $_table = 'product_category';

    protected $_module = 'Products';

    function CCategoryEditPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CMasterEditPage($app, $template);
	}

	function on_page_init()
	{
		parent::on_page_init();
		CValidator::add('title', VRT_TEXT, 0, 255);
		CValidator::add('uri', VRT_TEXT, 0, 255);
		($this->id) ? CValidator::add_nr('image_filename', VRT_IMAGE_FILE, '') : CValidator::add('image_filename', VRT_IMAGE_FILE);
		CValidator::add_nr('description', VRT_TEXT, '', 0, 255);
	}

	function parse_data()
	{
		parent::parse_data();
	}
}
?>