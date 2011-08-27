<?
class CInputs
{
	var $Application;
	var $DataBase;
	var $tv;
	var $last_error;

	function CInputs(&$app)
	{
		$this->Application = &$app;
		$this->tv = &$app->template_vars;
		$this->DataBase = &$this->Application->DataBase;
		$this->Localizer = $this->Application->Localizer;
	}

	function get_last_error()
	{
		return $this->last_error;
	}
	
	function generate_uri($table, $title){
		$uri = $this->generate_uri_ereg(translit($title));
		
		$cnt_rs = $this->DataBase->select_custom_sql("SELECT count(id) as cnt FROM {$table} WHERE uri='{$uri}'");
		if($cnt_rs === false){
			return  array('errors' => $this->Localizer->get_string('internal_error'));
		}
		
		if($cnt_rs->get_field('cnt') > 0){
			return  array('errors' => $this->Localizer->get_string('object_or_uri_exists'));
		}
		
		return array('uri' => $uri, 'errors' => false);
	}
	
	function generate_uri_ereg($string) {
        //return ereg_replace('\-{2,}', '-', ereg_replace('[^a-z0-9]', '-', strtolower($string)));
        $str = ereg_replace('[^a-z0-9]{1,}', '-', strtolower($string));
        if ($str[strlen($str) - 1] == "-") {
        	$str = substr($str, 0, strlen($str) - 1);
        }
        return $str;
    }
    
};
?>