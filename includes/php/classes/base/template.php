<?
/**
 * @package LLA.Base
 */
/**
 */
require_once(BASE_CLASSES_PATH.'classfactory.php');
require_once(BASE_CLASSES_PATH.'utils.php');
if (!isset($pt_template_factory) || !is_object($pt_template_factory)) $pt_template_factory = new CClassFactory(); // Create template factory if not created
/*
--------------------------------------------------------------------------------
Class CTemplateControl v 1.5.0
You must extend this class if you want to create your own control.
Controls are put in includes/php/classes/custom/controls folder
Templates are put in /includes/templates/custom/controls folder
Implement process() function to process data and return parsed template

methods:
	string get_input_var(string $name) - returns value of input variable
	void input_vars_to_array(&$template_vars) - put all input variables into template_vars

history:
	v 1.5.0 - support for complied templates (VK)
	v 1.4.0 - array of input vars (VK)
	v 1.3.1 - on_page_init added (VK)
	v 1.3.0 - html_page propery added, form handling support (VK)
	v 1.2.5 - default value in get_input_var (AD)
	v 1.2.4 - added input_vars_to_array (VK)
	v 1.2.3 - all names are stored in lower case, xml style attributes (VK)
	v 1.2.2 - safe handling in get_input_var, process marked as pure virtual (VK)
	v 1.2.1 - renamed (VK)
	v 1.2.0 - multi-controls (VK)
	v 1.0.0 - created (VK)
--------------------------------------------------------------------------------
*/
define('PT_CONTROL_VARS', '/([a-z\_]+) *= *"([^"]*)"/i');
/**
 * @package LLA.Base
 */
class CTemplateControl {
	var $Application;
	var $html_page;
	var $input_vars;
	var $object_name;
	var $object_id = null;
	var $is_inited = false;
	function CTemplateControl($name, $object_id = null) {
		$this->Application = &$GLOBALS['app'];
		$GLOBALS['pt_template_factory']->register(strtolower($name), (!is_null($object_id))?strtolower($object_id):null, $this);
		$this->object_name = $name;
		$this->object_id = $object_id;
		$this->input_vars = array();
		$this->html_page = &$this->Application->CurrentPage;
		$this->html_page->m_Controls[] = &$this;
	}
	function parse_vars($vars_str) {
		$this->input_vars = array();
		$matches = array();
		preg_match_all(PT_CONTROL_VARS, $vars_str, $matches);
		foreach($matches[1] as $key => $val)
			if (!isset($this->input_vars[strtolower($val)]))
				$this->input_vars[strtolower($val)] = CUtils::entitiesToString($matches[2][$key]);
			else
				if (is_array($this->input_vars[strtolower($val)]))
					$this->input_vars[strtolower($val)][] = CUtils::entitiesToString($matches[2][$key]);
				else
				{	
					$a = array($this->input_vars[strtolower($val)]);
					$this->input_vars[strtolower($val)] = $a;
					$this->input_vars[strtolower($val)][] = CUtils::entitiesToString($matches[2][$key]);
				}
	}
	function in_input_vars($name) {
		return array_key_exists($name, $this->input_vars);
	}
	function get_input_var($name, $default = '') {
		$name = strtolower($name);
		return ( (isset($this->input_vars[$name]))?($this->input_vars[$name]):($default) );
	}
	function add_comments(&$var) {
		return "\n<!-- " . $this->object_name . " (" . $this->object_id . ") output start -->\n\n" . $var . "\n\n<!-- " . $this->object_name . " (" . $this->object_id . ") output end -->\n\n";
	}
	function mark_var($var_name) {
		$result = $this->object_name . '_';
		if (!is_null($this->object_id))
			$result .= $this->object_id . '_';
		return $result . $var_name;
	}
	function on_page_init()	{return true;}
	function process() {
		system_die('pure virtual function call in ' . $this->control_name);
	}
	function input_vars_to_array(&$tv) {
		foreach ($this->input_vars as $k => $v)
			$tv[$k] = $v;
	}
	function process_and_result($name, $id, $iv, $l, $add)
	{
		$c=&$GLOBALS['pt_template_factory']->get_object($name, $id);
		if (is_object($c))
		{
			if (strlen($add))
			{
				$c->parse_vars($add);
				$c->input_vars = array_merge($c->input_vars, $iv);
			}
			else
				$c->input_vars=$iv;
			if (!$c->is_inited)
			{
				$c->on_page_init();
				$c->is_inited = true;
			}
			return $c->process($l);
		}
		else
			return '';
	}
}
/*
--------------------------------------------------------------------------------
Class CTemplate v 2.0.0.1

History:
	v 2.0.0.1 - control's attributes with entities are fixed (VK)
	v 2.0.0.0 - template compilation (VK)
	v 1.9.15.0 - js escaping fixed (VK)
	v 1.9.14.0 - bug with for-process counters output fixed (VK)
	v 1.9.13.0 - on_page_init for controls added (VK)
	v 1.9.12.0 - < %^var% > now works as ' js escaping (LA, PERSON)
	v 1.9.11.0 - huge ugly bug fixed (VK, AD)
	v 1.9.10.0 - support for url encoding (VK) <%+var%>
	v 1.9.9.0 - for-process counters output added - use "<%$%>" (VK)
	v 1.9.8.0 - array parsing optimized (VK)
				array symbol replaced with "@" (VK)
	v 1.9.7.0 - parse array, variable name like "array.key"  (AD)
	v 1.9.6.0 - error message contains filename of the template now (VK)
					small bug in errors output fixed (VK)
	v 1.9.5.0 - name of controls now are in lower case (VK)
	v 1.9.4.0 - parse_string static method added (VK)
	v 1.9.3.0 - invalid nesting shows notice now (VK)
	v 1.9.2.0 - ternary conditional tags support added (VK)
	v 1.9.1.0 - ":" symbol in variable names not allowed any more. use '.' or '_' instead
	v 1.9.0.0 - multiple controls parsing in line with variables (VK)
				"@" sign in comments replaced with "*" (VK)
				show_notice metod changed (VK)
	v 1.8.6.0 - control can be anywhere in line (VK)
	v 1.8.5.0 - negation processing in IF statements (VK)
	v 1.8.4.0 - CObject as parent added, DebugInfo support (VK)
	v 1.8.3.0 - 1.8.3 losted in time ;)
	v 1.0.0.0 - created (VK)

-------------------------------------------------------------------------------
sample of using:

echo CTemplate::parse_string("template string", array of template vars);
echo CTemplate::parse_file("file name to custom template", array of template vars);

--------------------------------------------------------------------------------
*/

// template syntax definition (in regular expressions)

define('PT_START_TAGS', '/^[ \t]*<% *(IF|FOR) +(!)?([a-z\_]+[a-z\_0-9\.]*) *%>/i');
define('PT_END_TAGS', '/^[ \t]*<%\/ *(IF|FOR) *%>/i');
define('PT_MIDDLE_TAGS', '/^[ \t]*<% *(ELSE) *%>/i');
define('PT_VARIABLE_TAGS', '/<%([~=#\^\+]+) *([a-z\_]+[a-z\_0-9\/\.]*)(@([a-z\_]+[a-z\_0-9\.]*))? *%>/i');
define('PT_TERNARY_TAGS', '/<% *(!)?([a-z\_]+[a-z\_0-9\.]*) *\? *(#?)([a-z\_]+[a-z\_0-9\.@]*) *: *(#?)([a-z\_]+[a-z\_0-9\.@]*) *%>/i');

define('PT_CONTROL_TAGS', '/<% *IT *: *([a-z\_]+[a-z\_0-9]*)(\. *([a-z\_0-9]*) *)?( +((.(?!%>))*))? *\/%>/i');
define('PT_COMMENT_TAGS', '/<%\* *([^>]*) *\/%>/i');

define('PT_COUNTER_TAGS', '/<% *\$ *%>/i');

define('PT_COMPILE', 1);
// process types definition - DO NOT CHANGE!
define('PT_ROOT', 0);
define('PT_IF', 1);
define('PT_FOR', 2);
define('PT_SILENT_IF', 3);
define('PT_SILENT_FOR', 4);
define('PT_FALSE_IF', 5);

/**
 * @package LLA.Base
 */
class CTemplate {

var $vars; // template vars (array)
var $template; // template (array)
var $result; // the result of template parsing (string)
var $debug_mode; // debug mode - define PT_DEBUG_MODE to "true" before creating Template for debug mode
var $compressed_mode; // compressed mode - define PT_COMPRESSED_MODE to "true" before creating Template for compressed output
var $filename;// filename of the
var $system_vars;
var $Registry;

/*
--------------------------------------------------------------------------------
Template(void) - Object constructor

Define PT_DEBUG_MODE constant to "true" before creating Template for debug mode.
Define PT_COMPRESSED_MODE constant to "true" before creating Template for compressed output.
--------------------------------------------------------------------------------
*/

function CTemplate(){
	$this->vars = array();
	$this->template = array();

	$this->debug_mode = true;
	if (defined('PT_COMPRESSED_MODE') && PT_COMPRESSED_MODE) $this->compressed_mode = true;
	else $this->compressed_mode = false;

	if ($GLOBALS['app']->is_module('Registry'))
		$this->Registry = &$GLOBALS['app']->get_module('Registry');
	$this->system_vars = array('cycle_nesting' => -1, 'cycle_counters' => array());
}

/*
--------------------------------------------------------------------------------
load_file(string _path) -  loading template from file

Where _path is a valid path to the template file (like "templates/your_template.tpl").
--------------------------------------------------------------------------------
*/

function load_file($path){
	if (!file_exists($path)) system_die('File reading error - "' . $path . '"', 'Template->load_file');
	$this->template = @file($path);
	unset($this->result);
	$this->filename = $path;
}

/*
--------------------------------------------------------------------------------
load_array(array _template) -  loading template from array

Where _template is a valid array variable with template.
--------------------------------------------------------------------------------
*/

function load_array(&$array){ // loading template from array
	if (!is_array($array)) system_die('Invalid variable set (must be array)', 'Template->load_array');
	$this->template = $array;
	unset($this->result);
	unset($this->filename);
}

/*
--------------------------------------------------------------------------------
load_string(string _template) -  loading template from array

Where _template is a valid string variable with template.
--------------------------------------------------------------------------------
*/

function load_string(&$string){ // loading template from string
	$this->template = array();
	foreach (preg_split('/\r?\n/', $string) as $tmp) array_push($this->template, $tmp."\n");
	unset($this->result);
	unset($this->filename);
}

/*
--------------------------------------------------------------------------------
show_parsed(void) -  parsing template (if needed) and showing the result of parsing
--------------------------------------------------------------------------------
*/

function show_parsed(){ // parsing template (if needed) and showing the result of parsing
	if (isset($this->result)) echo $this->result;
	else {
		$this->parse();
		echo $this->result;
	}
}

/*
--------------------------------------------------------------------------------
show_notice(string _msg) - system method for message output in debug mode
--------------------------------------------------------------------------------
*/

function show_notice($msg, $type = 0){
	if ($this->debug_mode) {
	  switch ($type) {
		  case 1: $msg = 'unknown template variable <b>' . $msg . '</b>'; break;
			case 2: $msg = 'unknown template control <b>' . $msg . '</b>'; break;
			case 3: $msg = 'invalid nesting - <b>' . $msg . '</b>'; break;
			case 4: $msg = 'invalid array variable - <b>' . $msg . '</b>'; break;
		}
		$GLOBALS['GlobalDebugInfo']->Write('<font color="red">Template Notice: </font>	' . $msg);
	}
}

/*
--------------------------------------------------------------------------------
set_var(string _name, mixed _value[, int _nesting]) - easy way to set up template variables

Where _name is the name of template variable and _value is variables's value.
Use _nesting in case of your template variable is array.

The following strings are the same:
$tpl->set_var('your_var1', 2, 1);
$tpl->vars['your_var1'][1] = 2;

--------------------------------------------------------------------------------
*/

function set_var($name, $value){ // set the template variable
	if (func_num_args()> 2){
		if (!$this->in_vars($name)) $this->vars[$name] = array();
		$this->vars[$name][func_get_arg(2)] = $value;
	} else $this->vars[$name] = $value;
}

/*
--------------------------------------------------------------------------------
unset_var(string _name) - unset the template variable
where _name is the name of template variable
--------------------------------------------------------------------------------
*/

function unset_var($name){ // unset the template variable
	if (isset($this->vars[$name])) unset($this->vars[$name]);
}

/*
--------------------------------------------------------------------------------
bool in_vars(string _name) - determine whether a variable is set
where _name is the name of template variable
returns TRUE if _name exists and FALSE otherwise.
--------------------------------------------------------------------------------
*/

function in_vars($name){ // check the template variable
	return array_key_exists($name, $this->vars);
}

/*
--------------------------------------------------------------------------------
bool is_nested_var(string _name) - system method
--------------------------------------------------------------------------------
*/

function is_nested_var($name){ // SYSTEM FUNCTION - DO NOT USE!
	$curr_var = $this->vars[$name];
	for($c = 0; $c <= $this->system_vars['cycle_nesting']; $c++){
		if (!is_array($curr_var) || !isset($curr_var[$this->system_vars['cycle_counters'][$c]])) return false;
		$curr_var = $curr_var[$this->system_vars['cycle_counters'][$c]];
	}
	return true;
}

/*
--------------------------------------------------------------------------------
mixed get_nested_var(string _name) - system method
--------------------------------------------------------------------------------
*/

function get_nested_val($name){ // SYSTEM FUNCTION - DO NOT USE!
	$curr_var = $this->vars[$name];
	for($c = 0; $c <= $this->system_vars['cycle_nesting']; $c++)
		$curr_var = $curr_var[$this->system_vars['cycle_counters'][$c]];
	if (is_array($curr_var)){
		$this->show_notice($name, 3);
		$curr_var = '<font color="red"><b>INVALID</b></font>';
	}
	return $curr_var;
}

/**
* @param string $name
* @return string
* @access protected
*/
function get_var_val($name){ // SYSTEM FUNCTION - DO NOT USE!
	if (!$this->in_vars($name)) system_die('Invalid variable name - "' . $name . '"', 'Template->get_var_val');
	if (!is_array($this->vars[$name])) return $this->vars[$name]; // common variable
	elseif ($this->is_nested_var($name)) return $this->get_nested_val($name); // array with valid nesting
	else { // array with invalid nesting
		$keys = array_keys($this->vars[$name]);
		return $this->vars[$name][$keys[0]];
	}
}

function get_var_ref($name, $nesting)
{
	if (strpos($name, '/') !== FALSE)
	{
		return '$this->Registry->get_value(\'' . $name . '\')';
	}
	else
	{
		$names = explode('@', $name, 2);
		$r = '$this->vars[\''.$names[0].'\']';
		if ($nesting)
		{
			$prev_r = $r;
			for ($i=0; $i<($nesting-1); $i++)
				$prev_r .= '[$i' . $i . ']';
			for ($i=0; $i<$nesting; $i++)
				$r .= '[$i' . $i . ']';
			$r .= ( (count($names)==2) ? ('[\'' . $names[1] . '\']') : ('') );
			$prev_r .= ( (count($names)==2) ? ('[\'' . $names[1] . '\']') : ('') );
			return '( (isset('.$prev_r.') && is_array('.$prev_r.') && isset('.$r.'))?('.$r.'):((isset($this->vars[\''.$name.'\']))?($this->vars[\''.$name.'\']):(\'\')))';
		}
		else
		{
			$r .= ( (count($names)==2) ? ('[\'' . $names[1] . '\']') : ('') );
			return '(isset('.$r.')?('.$r.'):(\'\'))';
		}
	}
}

function _process_unk_value($v, $nesting, &$dw)
{
	//$v = trim($v);
	if (strpos($v, '<%') === 0)
	{
		return $this->_process_f_value($v, $nesting, $dw);
	}
	else
	{
		$v = str_replace('\\', '\\\\', $v);
		return '\''.str_replace('\'', '\\\'', $v).'\'';
	}
}

function _process_f_value($v, $nesting, &$dw)
{
	if (strncasecmp($v, '<%IT:', 5) === 0)
		return $this->_process_control($v, $nesting, $dw);
	if ($v[2] == '=')
	{
		$name = trim(substr($v, 3, strpos($v, '%>')-3));
		$dw = strpos($v, '%>')+2;
		return $this->get_var_ref($name, $nesting);
	}
	elseif ($v[2] == '#')
	{
		$name = trim(substr($v, 3, strpos($v, '%>')-3));
		$dw = strpos($v, '%>')+2;
		return 'htmlspecialchars(' . $this->get_var_ref($name, $nesting) . ')';
	}
	elseif ($v[2] == '+')
	{
		$name = trim(substr($v, 3, strpos($v, '%>')-3));
		$dw = strpos($v, '%>')+2;
		return 'str_replace(\'+\',\'%20\',urlencode(' . $this->get_var_ref($name, $nesting) . '))';
	}
	elseif ($v[2] == '^')
	{
		$name = trim(substr($v, 3, strpos($v, '%>')-3));
		$dw = strpos($v, '%>')+2;
		return 'js_escape_string(' . $this->get_var_ref($name, $nesting) . ')';
	}
	elseif ($v[2] == '~')
	{
		$name = trim(substr($v, 3, strpos($v, '%>')-3));
		$dw = strpos($v, '%>')+2;
		return 'nl2br(' . $this->get_var_ref($name, $nesting) . ')';
	}
	elseif ($v[2] == '$')
	{
		$dw = strpos($v, '%>')+2;
		if ($nesting)
			return '(1+$i' . ($nesting-1) . ')';
		return '';
	}
	else // ternary
	{
		$epos = strpos($v, '%>');
		$full = preg_split('/[\\?\\:]/', trim(substr($v, 2, $epos-2)));
		$cond = trim($full[0]);
		$v1 = trim($full[1]);
		$v2 = trim($full[2]);
		$dw = $epos+2;
		return '( ('.$this->get_var_ref($cond, $nesting).')?('.$this->get_var_ref($v1, $nesting).'):('.$this->get_var_ref($v2, $nesting).') )';
	}
	return '';
}

function _process_control($line, $nesting, &$dw)
{
	$full = trim(substr($line, 5));
	$spos = strpos($full, ' ');
	if ($spos === FALSE)
		$spos = strlen($full);
	$names = explode('.', trim(substr($full, 0, $spos)), 2);
	
	$name = $names[0];
	$id = ( (count($names)>1)?($names[1]):('') );
	
	$atr = 'array(';
	$in_value = false;
	$tmp_n = '';
	$tmp_v = '';
	$f = false;
	$k  =$spos;
	$add_str = '\'\'';
	while ($k<strlen($full))
	{
		if ( ($full[$k] == '/') && ($full[$k+1] == '%') && ($full[$k+2] == '>') )
			break;
		if ($full[$k] == '"')
		{
			if ($in_value) // value is finished
			{
				$tmp_n = trim($tmp_n);
				$tmp_v = trim($tmp_v);
				$atr .= '\''.$tmp_n.'\'=>CUtils::entitiesToString(\''.$tmp_v.'\'),';
				$tmp_n = '';
				$tmp_v = '';
				$f = false;
			}
			$in_value = !$in_value;
		}
		else
			if ($in_value)
			{
				if ( ($full[$k] == '<') && ($full[$k+1] == '%') )
				{
					$dw = 0;
					$tmp_v .= '\'.' . $this->_process_f_value(substr($full, $k), $nesting, $dw) . '.\'';
					$k += $dw-1;
				}
				else
				{
					if ($full[$k]=='\'')
						$tmp_v .= '\\';
					elseif ($full[$k]=='\\')
						$tmp_v .= '\\';
					$tmp_v .= $full[$k];
				}
			}
			else
			{
				if ( ($full[$k] == '<') && ($full[$k+1] == '%') )
				{
					$dw = 0;
					$tmp = $this->_process_f_value(substr($full, $k), $nesting, $dw);
					$add_str .= '.'.$tmp;
					$k += $dw-1;
				}
				else
					$tmp_n .= ( ($full[$k]!='=') ? ($full[$k]) : ('') );
			}
		$k++;
	}
	if ($k < strlen($full))
		$dw = $k+3+5;
	else
		$dw = $k+5;
	$atr .= ')';
	if (strlen($id)> 0)
		return 'CTemplateControl::process_and_result(\''.strtolower($name).'\',\''.strtolower($id).'\','.$atr.','.(($nesting>0)?('$i'.($nesting-1)):('-1')).','.$add_str.')';
	else
		return 'CTemplateControl::process_and_result(\''.strtolower($name).'\',null,'.$atr.','.(($nesting>0)?('$i'.($nesting-1)):('-1')).','.$add_str.')';
}

/*
--------------------------------------------------------------------------------
parse(void) - template parsing (without output)
--------------------------------------------------------------------------------
*/

function parse(){ // parsing template
	$loop_count = -1;
	if (func_num_args()>= 1){
		$proc_type = func_get_arg(0);
		//if (!in_array($proc_type, array(PT_ROOT, PT_IF, PT_FOR, PT_SILENT_IF, PT_SILENT_FOR, PT_FALSE_IF))) system_die('Invalid process type', 'Template->parse');
	} else {
		if ( (PT_COMPILE) && (isset($this->filename)) ) // file parsing
		{
			if (file_exists($this->filename . '.php'))
				if (filemtime($this->filename . '.php') > filemtime($this->filename))
				{
					include($this->filename . '.php');
					$this->result = $r;
					return 0;
				}
			$nesting = 0;
			$tplc = '<?$r=\'\';';
			for ($i=0; $i<count($this->template); $i++)
			{
				// process
				$line = trim($this->template[$i]);
				$line = preg_replace(PT_COMMENT_TAGS, '', $line); // Remove comments
				$result = array();
				if (preg_match(PT_START_TAGS, $line, $result))
				{
					if (strcasecmp($result[1], 'FOR') == 0)
					{
						$tplc .= 'for ($i' . $nesting . '=0;$i' . $nesting . '<intval(' . $this->get_var_ref($result[3], $nesting) . ');$i' . $nesting . '++){' . "\n";
						$nesting++;
					}
					else // this line is IF opening tag
					{
						$tplc .= 'if ('.$result[2].'(bool)('.$this->get_var_ref($result[3], $nesting).')){' . "\n";
					}
				} elseif (preg_match(PT_END_TAGS, $line, $result))
				{
					$tplc .= '}' . "\n";
					if (strcasecmp($result[1], 'FOR') == 0)
						$nesting--;
				} elseif (preg_match(PT_MIDDLE_TAGS, $line, $result))
				{
					$tplc .= '}else{' . "\n";
				} elseif (preg_match('/<%/', $line, $result))
				{
					$j = 0;
					$tmp_str = '';
					while ($j<strlen($line))
					{
						if ( ($line[$j] == '<') && ($line[$j+1] == '%') )
						{
							if (strlen($tmp_str))
							{
								$dw = 0;
								$tplc .= '$r.=' . $this->_process_unk_value($tmp_str, $nesting, $dw) . ";\n";
								$tmp_str = '';
							}
							$dw = 0;
							$tplc .= '$r.=' . $this->_process_f_value(substr($line, $j), $nesting, $dw) . ";\n";
							$j += $dw;
						}
						else
						{
							$tmp_str .= $line[$j];
							$j++;
						}
					}
					if (strlen($tmp_str))
					{
						$dw = 0;
						$tplc .= '$r.=' . $this->_process_unk_value($tmp_str, $nesting, $dw) . ";\n";
					}
					if (strlen($line))
						$tplc .= '$r.="\\n";';
				}
				else
				{
					if (strlen($line))
					{
						$dw = 0;
						$tplc .= '$r.=' . $this->_process_unk_value($line, $nesting, $dw) . ".\"\\n\";\n";
					}
				}
			}
			$tplc .= '?>';
			
			$fh = fopen($this->filename . '.php', 'wt');
			if ($fh)
			{
				fwrite($fh, $tplc);
				fclose($fh);
			}
			else
				system_die('Cannot save compiled template');
			include($this->filename . '.php');
			$this->result = $r;
			return 0;
		} // if compile and filename
		$proc_type = PT_ROOT;
		unset($this->result);
	}
	if (func_num_args()> 1){
		$curr_pos = intval(func_get_arg(1));
		if (($proc_type == PT_FOR) && (func_num_args() < 3)) system_die('Undefined loop count (FOR process)', 'Template->parse');
		if (func_num_args()> 2) $loop_count = intval(func_get_arg(2));
	}
	else
		$curr_pos = 0;
	$succ_mode = false;
	while ($curr_pos < sizeof($this->template)){
		$line = $this->template[$curr_pos]; // current line
		$line = preg_replace(PT_COMMENT_TAGS, '', $line); // Remove comments
		if (preg_match(PT_START_TAGS, $line, $result)){ // this line contains one of the START tags
			$result[1] = strtoupper($result[1]);
			if ($result[1] == 'FOR'){
				if (!$this->in_vars($result[3]) && ($proc_type < PT_SILENT_IF)){ // invalid FOR variable
					$error_msg = 'Invalid FOR statement counter named "'.$result[3].'"';
					break;
				} else {
					if ($proc_type <= PT_FOR) $count = intval($this->get_var_val($result[3]));
					$this->system_vars['cycle_nesting']++;
					$nesting_saver = $this->system_vars['cycle_nesting'];
					if ($proc_type> PT_FOR) $last_pos = $this->parse(PT_SILENT_FOR, $curr_pos + 1, 0); // create invisible FOR process
					else {
						if ($count == 0) $last_pos = $this->parse(PT_SILENT_FOR, $curr_pos + 1, 0); // create invisible FOR process
						else {
							for ($c = 0; $c < $count; $c++){
								$this->system_vars['cycle_counters'][$nesting_saver] = $c;
								$this->system_vars['cycle_nesting'] = $nesting_saver;
								$last_pos = $this->parse(PT_FOR, $curr_pos + 1, $c); // create visible FOR process in loop
							}
						}
					}
					$curr_pos = $last_pos;
				}
			} else { // this line is IF opening tag
				if (!$this->in_vars($result[3]) && ($proc_type < PT_SILENT_IF)){
					$error_msg = 'Invalid IF statement variable named "'.$result[3].'"';
					break;
				} else {
					if ($proc_type>PT_FOR) $curr_type = PT_SILENT_IF;
					else {
						$var = (bool)$this->get_var_val($result[3]);
						if (strlen($result[2])> 0) $var = !$var;
						$curr_type = ($var)?PT_IF:PT_FALSE_IF;
					}
					if ($loop_count!=-1) $curr_pos = $this->parse($curr_type, $curr_pos+1, $loop_count); // create new IF process inside the loop
					else $curr_pos = $this->parse($curr_type, $curr_pos+1); // create new IF process
				}
			}
		} elseif(preg_match(PT_END_TAGS, $line, $result)){
			$result[1] = strtoupper($result[1]);
			if (((($proc_type == PT_FOR) || ($proc_type == PT_SILENT_FOR)) && ($result[1] == 'FOR')) || ((($proc_type == PT_IF) || ($proc_type == PT_SILENT_IF) || ($proc_type == PT_FALSE_IF)) && ($result[1] == 'IF'))) {
				if (($proc_type == PT_FOR) || ($proc_type == PT_SILENT_FOR)) $this->system_vars['cycle_nesting']--; // this one was the end of loop block
				$succ_mode = true;
				break;
			} else {
				$error_msg = 'Unexpected end of '.$result[1].' statement';
				break;
			}
		} elseif(preg_match(PT_MIDDLE_TAGS, $line, $result)){ // this line contains one of the MIDDLE tags (ELSE probably)
			$result[1] = strtoupper($result[1]);
			if (($proc_type == PT_FALSE_IF) && ($result[1] == 'ELSE')) {
				$proc_type = PT_IF;
			} elseif (($proc_type == PT_IF) && ($result[1] == 'ELSE')) {
				$proc_type = PT_FALSE_IF;
			} elseif($proc_type != PT_SILENT_IF) { // ELSE inside non IF process or so
				$error_msg = 'Unexpected '.$result[1].' statement '.$proc_type;
				break;
			}
		} elseif ($proc_type <= PT_FOR){ // processing of visible contents
			if (!isset($this->result)) $this->result = '';
				$matches = array();
				$line_is_control = false;

				if (preg_match_all(PT_COUNTER_TAGS, $line, $matches)){ // We have counter tags inside
					$replace = array();
					foreach ($matches[0] as $key => $val){ // process counters
						if ($loop_count >= 0) $replace[$key] = $loop_count + 1;
						else $replace[$key] = '';
					}
					$line = str_replace($matches[0], $replace, $line); // replace'em all
				}
				// processing variables

				if (preg_match_all(PT_VARIABLE_TAGS, $line, $matches)){ // Yes! We have some tags inside
					$replace = array();
					foreach ($matches[2] as $key => $val){ // go thru the matches
						if (strlen($matches[4][$key])> 0){ // process array variables
							if (isset($this->vars[$val]) && is_array($this->vars[$val]) && array_key_exists($matches[4][$key], $this->vars[$val])){
									$replace[$key] = $this->vars[$val][$matches[4][$key]];
									if ($matches[1][$key] == '#')
										$replace[$key] = htmlspecialchars($replace[$key]); // escape html entries for # tag
									if ($matches[1][$key] == '+')
										$replace[$key] = str_replace('+', '%20', urlencode($replace[$key])); // url escape for + tag
									if ($matches[1][$key] == '^')
									{
										$replace[$key] = str_replace("\\", "\\\\", $replace[$key]);
										$replace[$key] = str_replace("'", "\\'", $replace[$key]);
										$replace[$key] = str_replace("\r", "\\r", $replace[$key]);
										$replace[$key] = str_replace("\n", "\\n", $replace[$key]);
										$replace[$key] = str_replace("</script>", "</'+'script>", $replace[$key]);
									}
							} elseif (isset($this->vars[$val]) && is_object($this->vars[$val])) {
								 	$_obj = &$this->vars[$val];
									$_name = $matches[4][$key];
									$replace[$key] = $_obj->$_name;
									
									if ($matches[1][$key] == '#')
										$replace[$key] = htmlspecialchars($replace[$key]); // escape html entries for # tag
									if ($matches[1][$key] == '+')
										$replace[$key] = str_replace('+', '%20', urlencode($replace[$key])); // url escape for + tag
									if ($matches[1][$key] == '^')
									{
										$replace[$key] = str_replace("\\", "\\\\", $replace[$key]);
										$replace[$key] = str_replace("'", "\\'", $replace[$key]);
										$replace[$key] = str_replace("\r", "\\r", $replace[$key]);
										$replace[$key] = str_replace("\n", "\\n", $replace[$key]);
										$replace[$key] = str_replace("</script>", "</'+'script>", $replace[$key]);
									}
							} else {
								if ($this->debug_mode) $this->show_notice($val.$matches[3][$key], 4); // show stupid notice
								$replace[$key] = ''; // and insert complete emptyness
							}
						} else{ // process common variables
							if (isset($this->vars[$val]))
								$replace[$key] = $this->get_var_val($val);
							elseif (preg_match('/\\//', $val))
							{
								$v_row = $this->Registry->_internal_get_value($val);
								if ( ($v_row !== false) && (!$v_row->eof()) ) {
									$out = $v_row->Rows[0]->Fields['value'];
						            if ($v_row->Rows[0]->Fields['key_type'] == KEY_TYPE_IMAGE)
										$out = $GLOBALS['app']->template_vars['REGISTRY_WEB'] . $v_row->Rows[0]->Fields['id_path'] . '/' . $out;
									$replace[$key] = $out;
								}
								else
									$replace[$key] = '';
							}
							else
								$replace[$key] = '';
							
							if ($matches[1][$key] == '#')
								$replace[$key] = htmlspecialchars($replace[$key]); // escape html entries for # tag
							if ($matches[1][$key] == '+')
								$replace[$key] = str_replace('+', '%20', urlencode($replace[$key])); // url escape for + tag
							if ($matches[1][$key] == '^')
							{
								$replace[$key] = str_replace("\\", "\\\\", $replace[$key]);
								$replace[$key] = str_replace("'", "\\'", $replace[$key]);
								$replace[$key] = str_replace("\r", "\\r", $replace[$key]);
								$replace[$key] = str_replace("\n", "\\n", $replace[$key]);
								$replace[$key] = str_replace("</script>", "</'+'script>", $replace[$key]);
							}
						}
					}

					$line = str_replace($matches[0], $replace, $line); // replace'em all
				}

				// processing ternary operators

				if (preg_match_all(PT_TERNARY_TAGS, $line, $matches)){ // Yes! We have some tags inside
					foreach ($matches[2] as $key => $val){ // go thru the matches
						if (isset($this->vars[$val])){
							$var = (bool)$this->get_var_val($val);
							if (strlen($matches[1][$key])> 0) $var = !$var;
							$res_num = ($var)?4:6;
							if (isset($this->vars[$matches[$res_num][$key]])) {
								$replace[$key] = $this->get_var_val($matches[$res_num][$key]);
								if (strlen($matches[$res_num - 1][$key])> 0) $replace[$key] = htmlspecialchars($replace[$key]); // escape html entries
							} else {
								if ($this->debug_mode) $this->show_notice($res_var, 1);
								$result[$key] = '';
							}
						} else { // we have tag but haven't got variable
							if ($this->debug_mode) $this->show_notice($val, 1); // curse them out in debug mode
							$replace[$key] = ''; // and insert pretty nothing
						}
					}
					$line = str_replace($matches[0], $replace, $line); // replace'em all
				}

				// processing controls
				if (preg_match_all(PT_CONTROL_TAGS, $line, $matches)){ // Yes! This line contains control definition
					$replace = array();
					foreach ($matches[1] as $key => $name){ // go through the matches
						if (strlen($matches[3][$key])> 0) $tcontrol = &$GLOBALS['pt_template_factory']->get_object(strtolower($name), strtolower($matches[3][$key])); // here is control with id
						else $tcontrol = &$GLOBALS['pt_template_factory']->get_object(strtolower($name)); // here is control without id
						if (!is_null($tcontrol)){
							$tcontrol->parse_vars($matches[5][$key]);
							if (!$tcontrol->is_inited)
							{
								$tcontrol->on_page_init();
								$tcontrol->is_inited = true;
							}
							$replace[$key] = $tcontrol->process($loop_count);
						} else
							$replace[$key] = '';
					}
					$line = str_replace($matches[0], $replace, $line); // replace control statements with control results
				}

				// compress and delete blank lines
				$line = preg_replace('/[\r\n]*$/', '', trim($line));
				if (strlen($line)> 0) $this->result .= $line . "\n";
			}
			$curr_pos++;
		}

// And what we have here?
		if (!isset($error_msg) && ($proc_type != PT_ROOT) && !$succ_mode) $error_msg = 'Unexpected end of file'; // invalid template - show error
		if (isset($error_msg)){
			$error_txt = 'Template parsing error on line '.($curr_pos + 1);
			if (isset($this->filename))	$error_txt .= ' of file "'.$this->filename.'"';
			$error_txt .= ' - '.$error_msg;
			system_die($error_txt, 'Template->parse'); // invalid template - show error
		}
		if ($proc_type == PT_ROOT)
			if (!isset($this->result))
				$this->result = ''; // probably there were one big false IF?
		return $curr_pos; // HURRA! HURRA! This one is successfully completed!
	}

	function parse_string($string, $tv = null){ // fast access (VK)
		$tmp = new CTemplate();
		$tmp->load_string($string);
		if (is_null($tv)) $tmp->vars = &$GLOBALS['app']->template_vars;
		else $tmp->vars = $tv;
		$tmp->parse();
		return $tmp->result;
	}

	function parse_file($file_name, $tv = null){ // fast access (VK)
		$tmp = new CTemplate();
		$tmp->load_file($file_name);
		if (is_null($tv)) $tmp->vars = &$GLOBALS['app']->template_vars;
		else $tmp->vars = $tv;
		$tmp->parse();
		return $tmp->result;
	}

	function parse_array($array, $tv = null){ // fast access (VK)
		$tmp = new CTemplate();
		$tmp->load_array($array);
		if (is_null($tv)) $tmp->vars = &$GLOBALS['app']->template_vars;
		else $tmp->vars = $tv;
		$tmp->parse();
		return $tmp->result;
	}
}
?>