<?
require_once(CUSTOM_CLASSES_PATH . 'admin/mastereditpage.php');

class CImageEditPage extends CMasterEditPage
{
    /**
     * The table name.
     *
     * @var array
     */
    protected $_table = 'exhibition_image';

    protected $_module = 'Exhibitions';
    
    protected $DataBase;
    
    protected $exhibition_id;

    function CImageEditPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CMasterEditPage($app, $template);
		$this->DataBase = $this->Application->DataBase;
	}

	function on_page_init()
	{
		parent::on_page_init();
		$this->tv['exhibition_id'] = $this->exhibition_id = InGet('exhibition_id');
		if(!$this->exhibition_id){
			$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('parent', false));
			die;
		}
		
		$exhibition_rs = $this->DataBase->select_sql('exhibition');
		if($exhibition_rs == false) $exhibition_rs = new CRecordSet();
		$exhibition_rs->add_row(array('id' => '', 'title' => RECORDSET_FIRST_ITEM), INSERT_BEGIN);
		CInput::set_select_data('exhibition_id', $exhibition_rs, 'id', 'title');
		
	}

	function parse_data()
	{
		if (!parent::parse_data()) return false;
		
			parent::bind_data();
		
		return true;
	}
	
	function on_exhibition_image_submit($action){
		require_once(CUSTOM_CONTROLS_PATH . 'simpledataoutput.php');
        new CSimpleDataOutput($this);
        require_once(BASE_CONTROLS_PATH . 'simplearrayoutput.php');
        new CSimpleArrayOutput();
        
        CValidator::add('title', VRT_TEXT, 0, 255);
        CValidator::add('exhibition_id', VRT_NUMBER);
        CValidator::add('type', VRT_TEXT, 4, 12);
        ($this->id) ? CValidator::add_nr('image_filename', VRT_IMAGE_FILE, '') : CValidator::add('image_filename', VRT_IMAGE_FILE);
        CValidator::add_nr('description', VRT_TEXT, '', 5, 1000);
        
		$mod = $this->Application->get_module($this->_module);
		if (CForm::is_submit($this->_table)) {
			if (CValidator::validate_input()) {
				if ($this->id) {
					if ($mod->{'update_'.$this->_table}($this->id, $this->tv)) {
						$this->tv['_info'] = $this->Localizer->get_string('object_updated');
						$this->tv['_return_info'] =  $this->Application->Navi->getUri('parent', false). '&id=' . $this->exhibition_id;
					}
					else {
						$this->tv['_errors'] = $mod->get_last_error();
					}
				}
				else {
					if ($this->id = $this->tv['id'] = $mod->{'add_'.$this->_table}($this->tv)) {
						$this->tv['_info'] = $this->Localizer->get_string('object_added');
						$this->tv['_return_info'] =  $this->Application->Navi->getUri('parent', false). '&id=' . $this->exhibition_id;
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
			$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('parent', false). '&id=' . $this->exhibition_id);
		} 
	}
}
?>