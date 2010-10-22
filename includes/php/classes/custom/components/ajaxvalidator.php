<?
class CAjaxValidator
{
    var $Application;
    var $DataBase;
    var $tv;
    var $last_error;

    function CAjaxValidator(&$app)
    {
        $this->Application = &$app;
        $this->tv = &$app->template_vars;
        $this->DataBase = &$this->Application->DataBase;
    }

    function get_last_error()
    {
        return $this->last_error;
    }

    function validate($page_class, $form_name, $field_name, $field_value, $_it_fs) { 
		global $app;
		global $internal_metas;
		$_POST[$field_name] = $field_value;
		$_POST['_it_fs'] = base64_encode($field_name);
		require_once(FUNCTION_PATH . '../router.php');
		foreach ($this->Application->Router->_routes as $key => $route) {
			if ($route['class'] == $page_class) {
				$php_path = $this->Application->Router->_routes[$key]['php_path'];
				require_once(CUSTOM_CLASSES_PATH . $php_path);
				$obj = new $page_class($app);
				$validator_method = 'load_'.$form_name.'_validator';
				if (method_exists($obj, $validator_method)) {
					call_user_method_array($validator_method, $obj, array());
					if (CValidator::validate_input()) {
						return array('field' => $field_name, 'message' => '');
					}
					else {
						$errors = CValidator::get_errors();
						$out = array();
						foreach ($errors as $field => $message) {
							return array('field' => $field, 'message' => $message);
						}
					}
				}
			}
		}
	}
	
	function is_unique($value, $id = null, $table, $field) {
		global $app;
		if (is_null($id)) {
			$rs = $app->DataBase->select_sql($table, array($field => $value));
			if (($rs !== false)&&(!$rs->eof())) {
				return array('message' => $app->Localizer->get_string($field.'_exist'));
			}
			else {
				return array('message' => '');
			}
		}
		else {
        	$rs = $app->DataBase->select_custom_sql('
            select count(*) cnt
            from %prefix%'.$table.'
            where
            ('.$field.' = \''.mysql_escape_string($value).'\') and id <> '.$id.'');
			if (($rs !== false)&&(!$rs->eof())) {
				return array('message' => $app->Localizer->get_string($field.'_exist'));
			}
			else {
				return array('message' => '');
			}
		}
	}
};
?>