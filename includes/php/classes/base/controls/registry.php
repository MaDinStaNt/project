<?
/**
 * @package LLA.Base
 */
/*
Class CRegistryControl v 1.0.3
template control for registry values output
parametrs:
	path
	value
	html_escape true/false
	url_escape true/false
    parse true/false

history:
	v 1.0.3 - bug connected with previous changes fixed (VK)
	v 1.0.2 - add "parse" arg - parse value as template (AD)
    v 1.0.1 - image support fixed (VK)
	v 1.0.0 - created (VK)
*/
/**
 * @package LLA.Base
 */
class CRegistryControl extends CTemplateControl {
	var $Registry;
	function CRegistryControl() {
		parent::CTemplateControl('Registry');
		$this->Registry = &$GLOBALS['app']->get_module('Registry');
	}
	function process() {
		$html_escape = str_to_bool($this->get_input_var('html_escape'));
		$url_escape = str_to_bool($this->get_input_var('url_escape'));
        $parse_text = str_to_bool($this->get_input_var('parse'));
		$v_row = $this->Registry->_internal_get_value($this->input_vars['path'] . '/' . $this->input_vars['value']);
		if ( ($v_row !== false) && (!$v_row->eof()) ) {
			$out = $v_row->Rows[0]->Fields['value'];
			if ($html_escape) $out = htmlspecialchars($out);
			if ($url_escape) $out = urlencode($out);
			
			$out = convert_template($out);
            
            // parse text as template
            if ($parse_text) $out = CTemplate::parse_string($out, $this->html_page->template_vars);
			
            if ($v_row->Rows[0]->Fields['key_type'] == KEY_TYPE_IMAGE) {
				$out = $v_row->Rows[0]->Fields['path_id'] . '/' . $out;
				if ( (@file_exists(REGISTRY_FILES_STORAGE .  $out)) && (!is_dir(REGISTRY_FILES_STORAGE .  $out)) ) {
					$img = @getimagesize(REGISTRY_FILES_STORAGE .  $out);
					$w = intval($img[0]);
					$h = intval($img[1]);
					if ($this->get_input_var('max_width') != '')
						if ($w > intval($this->get_input_var('max_width'))) {
							$h = intval($this->get_input_var('max_width'))*$h/$w;
							$w = intval($this->get_input_var('max_width'));
						}
					if ($this->get_input_var('max_height') != '')
						if ($h > intval($this->get_input_var('max_height'))) {
							$w = intval($this->get_input_var('max_height'))*$w/$h;
							$h = intval($this->get_input_var('max_height'));
						}
					$out = '<img width="'.$w.'" height="'.$h.'" src="'.$GLOBALS['app']->template_vars['REGISTRY_WEB'] . $out .'" border="0" alt=""'.SINGLE_TAG_END.'>';
				}
				else
					$out = '';
			}
		}
		else
			$out = 'invalid path '.$this->input_vars['path'].' and value ' . $this->input_vars['value'];
		return $out;
	}
}
?>