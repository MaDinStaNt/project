<?
require_once(CUSTOM_CLASSES_PATH . 'admin/mastereditpage.php');

class CImageEditPage extends CMasterEditPage
{
    /**
     * The table name.
     *
     * @var array
     */
    protected $_table = 'product_image';

    protected $_module = 'Products';
    
    protected $DataBase;
    
    protected $product_id;

    function CImageEditPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CMasterEditPage($app, $template);
		$this->DataBase = $this->Application->DataBase;
	}

	function on_page_init()
	{
		parent::on_page_init();
		$this->tv['product_id'] = $this->product_id = InGet('product_id');
		if(!$this->product_id){
			$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('parent', false));
			die;
		}
		
		$product_rs = $this->DataBase->select_sql('product');
		if($product_rs == false) $product_rs = new CRecordSet();
		$product_rs->add_row(array('id' => '', 'title' => RECORDSET_FIRST_ITEM), INSERT_BEGIN);
		CInput::set_select_data('product_id', $product_rs, 'id', 'title');
		
		$type_rs = new CRecordSet();
		$type_rs->add_row(array('id' => '', 'title' => RECORDSET_FIRST_ITEM));
		$type_rs->add_row(array('id' => TYPE_PRODUCT_IMAGE_PHOTO, 'title' => $this->Application->Localizer->get_string('photo')));
		$type_rs->add_row(array('id' => TYPE_PRODUCT_IMAGE_INSTRUCTION, 'title' => $this->Application->Localizer->get_string('instruction')));
		CInput::set_select_data('type', $type_rs, 'id', 'title');
	}

	function parse_data()
	{
		if (!parent::parse_data()) return false;
		
			parent::bind_data();
			if($this->id){
				if($this->tv['type'] == TYPE_PRODUCT_IMAGE_INSTRUCTION) $this->tv['size'] = '75x50';
				if($this->tv['type'] == TYPE_PRODUCT_IMAGE_PHOTO) $this->tv['size'] = '75x75';
			}
		
		return true;
	}
	
	function on_product_image_submit($action){
		require_once(CUSTOM_CONTROLS_PATH . 'simpledataoutput.php');
        new CSimpleDataOutput($this);
        require_once(BASE_CONTROLS_PATH . 'simplearrayoutput.php');
        new CSimpleArrayOutput();
        
        CValidator::add('title', VRT_TEXT, 0, 255);
        CValidator::add('product_id', VRT_NUMBER);
        CValidator::add('type', VRT_TEXT, 4, 12);
        ($this->id) ? CValidator::add_nr('image_filename', VRT_IMAGE_FILE, '') : CValidator::add('image_filename', VRT_IMAGE_FILE);
        CValidator::add_nr('description', VRT_TEXT, '', 5, 1000);
        
		$mod = $this->Application->get_module($this->_module);
		if (CForm::is_submit($this->_table)) {
			if (CValidator::validate_input()) {
				if ($this->id) {
					if ($mod->{'update_'.$this->_table}($this->id, $this->tv)) {
						$this->tv['_info'] = $this->Localizer->get_string('object_updated');
						$this->tv['_return_info'] =  $this->Application->Navi->getUri('parent', false). '&id=' . $this->product_id;
					}
					else {
						$this->tv['_errors'] = $mod->get_last_error();
					}
				}
				else {
					if ($this->id = $this->tv['id'] = $mod->{'add_'.$this->_table}($this->tv)) {
						$this->tv['_info'] = $this->Localizer->get_string('object_added');
						$this->tv['_return_info'] =  $this->Application->Navi->getUri('parent', false). '&id=' . $this->product_id;
					}
					else {
						$this->tv['_errors'] = $mod->get_last_error();
					}
				}
			}
			else {
				$this->tv['_errors'] = CValidator::get_errors();
			}
			
			if($this->tv['type'] == TYPE_PRODUCT_IMAGE_INSTRUCTION) $this->tv['size'] = '75x50';
			if($this->tv['type'] == TYPE_PRODUCT_IMAGE_PHOTO) $this->tv['size'] = '75x75';
			$this->custom_bind_data();
		}
		elseif (CForm::is_submit($this->_table, 'close')) {
			$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('parent', false). '&id=' . $this->product_id);
		} 
	}
}
?>