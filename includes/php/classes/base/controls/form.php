<?
/**
 * @package LLA.Base
 */
/*
CForm::is_submit($name, $action = null, $check_action = true);

CInput::show($name)
CInput::hide($name) // shows/hides it:input controls on template

CInput::enabled($name)
CInput::disable($name) // enabled/disables input
CInput::set_value($name, $value) // change button caption

CInput::set_select_data($name, $assoc_array) to produce data for IT:input type="select" from assoc_array
CInput::set_select_data($name, $record_set, $id_name, $text_name) to produce data for IT:input type="select" from CRecordSet
*/
/**
 * @package LLA.Base
 */
class CForm {
	/**
	 * Checks whether the form with name was submitted
	 * @access public
	 * @param string $name name of the form
	 * @param string $action name of the action
	 * @param boolean $check_action set to false if you wanna check whether form was submitted or not, without checking action
	 * @return boolean
	 *
	 * @uses InPostGet()
	 */
	function is_submit($name, $action = null, $check_action = true) {
		$r = (strcasecmp(InPostGet('formname'), $name)==0);
		if ($check_action)
			if (is_null($action)) return ( $r && (0==strlen(InPostGet('param2'))) );
			else return ( $r && (strcasecmp(InPostGet('param2'), $action)==0) );
		else return $r;
	}
	/**
	 * Returns additional argument
	 * @access public
	 * @return boolean
	 *
	 * @uses InPostGet()
	 */
	function get_param() {
		return InPostGet('param1');
	}
}

/**
 * @package LLA.Base
 */
class CInput {
	function show($name)
	{
		$GLOBALS['app']->template_vars[$name.':hidden'] = false;
	}
	function hide($name)
	{
		$GLOBALS['app']->template_vars[$name.':hidden'] = true;
	}
	function enable($name)
	{
		$GLOBALS['app']->template_vars[$name.':disabled'] = false;
	}
	function disable($name)
	{
		$GLOBALS['app']->template_vars[$name.':disabled'] = true;
	}
	function set_value($name, $value)
	{
		$GLOBALS['app']->template_vars[$name.':value'] = $value;
	}
	function set_select_data($name, $arr, $id='', $text='', $cb='') {
		global $app;
		$app->template_vars[$name.':select'] = array();
		$app->template_vars[$name.':escaped'] = false;
		if (is_array($arr))
		{
			$app->template_vars[$name.':escaped'] = ($id===true);
			foreach ($arr as $k => $v) $app->template_vars[$name.':select'][$k] = $v;
		}
		elseif (strcasecmp(get_class($arr), 'CRecordSet') == 0)
		{
			$app->template_vars[$name.':escaped'] = false;
			for ($i=0; $i<count($arr->Rows); $i++)
				if ($cb=='') $app->template_vars[$name.':select'][$arr->Rows[$i]->Fields[$id]] = $arr->Rows[$i]->Fields[$text];
				else $app->template_vars[$name.':select'][$arr->Rows[$i]->Fields[$id]] = call_user_func($cb, $arr->Rows[$i]);
		}
	}
}
?>