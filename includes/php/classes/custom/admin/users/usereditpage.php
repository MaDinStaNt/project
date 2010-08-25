<?
require_once(CUSTOM_CLASSES_PATH . 'admin/mastereditpage.php');

class CUserEditPage extends CMasterEditPage
{
    /**
     * The table name.
     *
     * @var array
     */
    protected $_table = 'user';
    
    protected $_module = 'User';

    function CUserEditPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CMasterEditPage($app, $template);
		CInput::set_select_data('state_id', $this->User->get_states(), 'id', 'title');
		CInput::set_select_data('user_role_id', $this->User->get_user_roles(), 'id', 'title');
		CInput::set_select_data('status', $this->get_user_status_array());
	}

	function on_page_init()
	{
		parent::on_page_init();
		CValidator::add('email', VRT_EMAIL, 0, 255);
		if ($this->tv['id']) {
			CValidator::add_nr('password', VRT_PASSWORD, '', 0, 255);
		}
		else {
			CValidator::add('password', VRT_PASSWORD, 0, 255);
		}
		CValidator::add('name', VRT_TEXT, 0, 255);
		CValidator::add_nr('address', VRT_TEXT, '', 0, 255);
		CValidator::add_nr('city', VRT_TEXT, '', 0, 255);
		CValidator::add_nr('state_id', VRT_TEXT, '', 0, 255);
		CValidator::add_nr('zip', VRT_US_ZIP, '');
		CValidator::add_nr('company', VRT_TEXT, '', 0, 255);
	}

	function on_user_edit_submit($action) {
		if ($action == "close") {
			$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('parent', false));
		}
		else {
			$mod = $this->Application->get_module($this->_module);
			if (CValidator::validate_input()) {
				if ($this->id) {
					if ($mod->{'update_'.$this->_table}($this->id, $this->tv)) {
						if ($this->tv['user_role_id'] != $this->_object_rs->get_field('user_role_id')) {
							$this->Application->DataBase->delete_sql('user_value', array('user_id' => $this->id));
						}
						$this->tv['_info'] = $this->Localizer->get_string('object_updated');
					}
					else {
						$this->tv['_errors'] = $mod->get_last_error();
					}
				}
				else {
					if ($this->tv['id'] = $mod->{'add_'.$this->_table}($this->tv)) {
						$this->tv['_info'] = $this->Localizer->get_string('object_added');
					}
					else {
						$this->tv['_errors'] = $mod->get_last_error();
					}
				}
			}
			else {
				$this->tv['_errors'] = CValidator::get_errors();
			}
		}
	}
	
	function parse_data()
	{
		parent::parse_data();
	}
}
?>