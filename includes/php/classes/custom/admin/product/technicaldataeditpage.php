<?
require_once(CUSTOM_CLASSES_PATH . 'admin/mastereditpage.php');

class CTechnicaldataEditPage extends CMasterEditPage
{
    /**
     * The table name.
     *
     * @var array
     */
    protected $_table = 'product_technical_data';

    protected $_module = 'Products';
    
    protected $DataBase;
    
    protected $product_id;

    function CTechnicaldataEditPage(&$app, $template)
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

	}

	function parse_data()
	{
		if (!parent::parse_data()) return false;
		
			parent::bind_data();
		
		return true;
	}
	
	function on_product_technical_data_submit($action){
		require_once(CUSTOM_CONTROLS_PATH . 'simpledataoutput.php');
        new CSimpleDataOutput($this);
        require_once(BASE_CONTROLS_PATH . 'simplearrayoutput.php');
        new CSimpleArrayOutput();
        
        CValidator::add('technical_data', VRT_TEXT, 0, 255);
        CValidator::add('value', VRT_TEXT, 0, 1000);
        CValidator::add('product_id', VRT_NUMBER);
        
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
			$this->custom_bind_data();
		}
		elseif (CForm::is_submit($this->_table, 'close')) {
			$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('parent', false). '&id=' . $this->product_id);
		} 
	}
}
?>