<?
/**
 * @package LLA.Base
 */
/**
 * For internal use only!<br>
 * history:<br>
 * 	v 1.2.0 - predefined controls (VK)<br>
 * 	v 1.1.1 - renamed (VK)<br>
 * 	v 1.1.0 - multi-controls (VK)<br>
 * 	v 1.0.0 - created (VK)<br>
 * @package LLA.Base
 * @version 1.2.0
 */
class CClassFactory {
	var $objects = array();
	function CClassFactory(){}
	function register($name, $object_id, &$object){
		if (is_null($object_id)) $this->objects[$name] = &$object;
		else {
			if (!array_key_exists($name, $this->objects)) $this->objects[$name] = array();
			$this->objects[$name][$object_id] = &$object;
		}
	}
	function &get_object($name, $object_id = null){
		if (!array_key_exists($name, $this->objects))
			if (strcasecmp($name, 'registry')==0) {
				require_once(BASE_CONTROLS_PATH.'registry.php');
				new CRegistryControl();
			}
			elseif (strcasecmp($name, 'form')==0) {
				require_once(BASE_CONTROLS_PATH.'internal_html_form.php');
				new CinternalHTMLForm();
			}
			elseif (strcasecmp($name, 'input')==0) {
				require_once(BASE_CONTROLS_PATH.'internal_html_input.php');
				new CinternalHTMLInput();
			}
		$obj = null;
		if (is_null($object_id))
		{
			if  ( (array_key_exists($name, $this->objects)) && (!is_array($this->objects[$name])) )
				$obj = &$this->objects[$name];
		}
		else
			if (array_key_exists($name, $this->objects) && is_array($this->objects[$name]) && array_key_exists($object_id, $this->objects[$name]))
				$obj = &$this->objects[$name][$object_id];
		return $obj;
	}
}
?>