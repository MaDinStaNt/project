<?
require_once(CUSTOM_CLASSES_PATH . 'admin/masterpage.php');

class CLoginPage extends CAdminPage
{
	function CLoginPage(&$app, $template)
	{
		parent::CAdminPage($app, $template);
		$this->IsSecure = false;
	}

	function on_page_init() 
	{
		if ($this->Application->User->is_logged()) {
			$this->Application->CurrentPage->internalRedirect($this->tv['HTTP']."admin/");
		}
		CValidator::add('email', VRT_EMAIL, 0, 255);
		CValidator::add('password', VRT_TEXT, 0, 255);
	}

	function parse_data()
	{
		if (!parent::parse_data()) return false;
        return true;
	}
	
	function on_login_submit($action)
	{
        require_once(CUSTOM_CONTROLS_PATH . 'simpledataoutput.php');
        new CSimpleDataOutput($this);
        require_once(BASE_CONTROLS_PATH . 'simplearrayoutput.php');
        new CSimpleArrayOutput();
        if (CValidator::validate_input())
		{
			if (!$this->Application->User->login($this->tv['email'], $this->tv['password'], (($this->tv['form_store'] == 1)?true:false)))
				$this->tv['_errors'] = $this->Application->User->get_last_error();
			else
			{
				$this->Application->CurrentPage->internalRedirect($this->tv['HTTP']."admin/");
			}			
		}
		else 
		{
			$this->tv['_errors'] = CValidator::get_errors();
		}
	}
}
?>