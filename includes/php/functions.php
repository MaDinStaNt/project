<?
/**
 * @package LLA.Base
 */
/**
 */
function system_die()
{
	@header('Status: 500 Server Error');
	if (function_exists('debug_backtrace')){
		$db = debug_backtrace();
		$db_text = '';
		for ($i=sizeof($db); $i>0; $i--) $db_text .= '<nobr> on line <b>'.( (isset($db[$i-1]['line']))?($db[$i-1]['line']):('?') ).'</b> of file <b>'.( (isset($db[$i-1]['file']))?($db[$i-1]['file']):('?') ).'</b></nobr>'.BR;
	} else $db_text = ' - debug backtrace is not available';
	if (func_num_args() > 0){
		$text = htmlspecialchars(strval(func_get_arg(0)));
		if (func_num_args()> 1) $text = '<b>' . htmlspecialchars(strval(func_get_arg(1))) . '</b>: '.$text;
	} else $text = 'Unnamed system error';
	echo '<p align="left"><b><font color="red">System error:</font></b> '.$text.BR.BR.$db_text.'</p>';
	$GLOBALS['GlobalDebugInfo']->OutPut();
	die();
}

function regexp_escape($str)
{
	return preg_quote($str, '/');
}

/*
function get_url([page_url[, acc_arr[, keep_old_arg[, https[, always_add]]]]])
string page_url - url of the page in form /root path/sub_path/name.ext or NULL to the current page
array acc_ar - map of GET method attributes
bool keep_old_arg - set to keep old GET attributes
bool https - create url with https protocol
bool always_add - always create full path info
*/
function get_url($page_url=null, $acc_arr = array(), $keep_old_arg = true, $https = false, $always_add = false){
	global $SiteUrl, $HTTPSSiteUrl;
	global $HttpName, $HttpPort, $SHttpName, $SHttpPort;
	global $RootPath, $ssl_root;
	if (is_null($page_url)) $page_url = $_SERVER['PHP_SELF'];
	if (preg_match('/^http/', $page_url)) return $page_url;
	if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on'))
		$page_url = preg_replace('/^'.regexp_escape($ssl_root).'/', '', $page_url);
	else
		$page_url = preg_replace('/^'.regexp_escape($RootPath).'/', '', $page_url);
	$page_url = preg_replace('/^'.regexp_escape('/').'/', '', $page_url);
	$url = '';
	if ($https) $url .= $SHttpName . ':' . '//' . $HTTPSSiteUrl . ':' . $SHttpPort;
	elseif (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on') || $always_add) $url .= $HttpName . ':' . '//' . $SiteUrl . ':' . $HttpPort;
	if ($https) $url .= $ssl_root;
	else $url .= $RootPath;
	$url .= $page_url;
	if (!is_array($acc_arr)) system_die();
	if ($keep_old_arg) $acc_arr = array_merge($_GET, $acc_arr);
	if (sizeof($acc_arr)> 0){
		$c = '?';
		foreach ($acc_arr as $key => $val){
			$url .= $c . $key . '=' . urlencode($val);
			$c = '&';
		}
	}
	return $url;
}
function in_post($name){for ($i=0; $i<func_num_args(); ++$i)if (!array_key_exists(func_get_arg($i), $_POST)) return false;return true;}
function in_get($name){for ($i=0; $i<func_num_args(); ++$i)if (!array_key_exists(func_get_arg($i), $_GET)) return false;return true;}
function in_get_post($name){for ($i=0; $i<func_num_args(); ++$i){$v=func_get_arg($i);if (!array_key_exists($v, $_POST) && !array_key_exists($v, $_GET)) return false;}return true;}

function str_to_bool($var){if (in_array(strtolower($var), array('true', 'yes', '1'))) return true;elseif (in_array(strtolower($var), array('false', 'no', '0'))) return false;else return (bool) $var;}
function bool_to_str($v){return ($v)?('true'):('false');}

function code2utf($num)
{
	if ($num<128) return chr($num);
	if ($num<2048) return chr(($num>>6)+192).chr(($num&63)+128);
	if ($num<65536) return chr(($num>>12)+224).chr((($num>>6)&63)+128).chr(($num&63)+128);
	if ($num<2097152) return chr(($num>>18)+240).chr((($num>>12)&63)+128).chr((($num>>6)&63)+128).chr(($num&63)+128);
	return '';
}
function utf16parse($t)
{
	$t = preg_replace('/\&\#([0-9]+)\;/me', "((\\1>255)?(utf8_decode(code2utf(\\1))):('&#\\1;'))", $t);
	return $t;
}
require_once(((get_magic_quotes_gpc())?('_in.quote.php'):('_in.php')));

function SetCacheVar($VarName, $Value, $CachId = 'common') {
if (!strlen($CachId)) return;
/*if (is_array($Value)) $_SESSION['cache'][$CachId] = array_merge($_SESSION['cache'][$CachId], $Value);
else*/ $_SESSION['cache'][$CachId][$VarName] = $Value;
}
// set variables in template_vars ($tv) to values from array or CRecordSet(current row) or CRecordSetRow
function row_to_vars(&$row, &$tv, $create_array = false, $prefix=''){
if ($create_array) $tv = array();
if (is_array($row)) foreach ($row as $k => $v) $tv[$prefix.$k] = $v;
if (strcasecmp(get_class($row), 'CRecordSet')==0) foreach ($row->Fields as $v) $tv[$prefix.$v] = $row->get_field($v);
if (strcasecmp(get_class($row), 'CRecordSetRow')==0) foreach ($row->Fields as $k => $v) $tv[$prefix.$k] = $v;
}
// set variables in template_vars ($tv) to values from CRecordSet
function recordset_to_vars(&$rs, &$tv, $counter_varname, $prefix='', $ovewrite_tv = true){
if ($rs === false) {$tv[$counter_varname] = 0;return false;}
if ( ($ovewrite_tv) || (!isset($tv[$counter_varname])) ) $tv[$counter_varname] = 0;
$tv[$counter_varname] += $rs->get_record_count();
$rs->first();
foreach ($rs->Fields as $v) if ( ($ovewrite_tv) || ((isset($tv[$prefix.$v])) && (!is_array($tv[$prefix.$v]))) ) $tv[$prefix.$v] = array();
while (!$rs->eof()) {
	foreach ($rs->Fields as $v) $tv[$prefix.$v][] = $rs->get_field($v);
	$rs->next();
}
}
function recordset_to_vars_callback(&$rs, &$tv, $counter_varname, $cb = '', $prefix='', $data=null, $ovewrite_tv = true){
if ( $rs === false ) {$tv[$counter_varname]=0;return false;}
if ( ($ovewrite_tv) || (!isset($tv[$counter_varname])) ) $tv[$counter_varname] = 0;
$tv[$counter_varname] += $rs->get_record_count();
$rs->first();
foreach ($rs->Fields as $v) {if ($ovewrite_tv || !isset($tv[$prefix.$v])) $tv[$prefix.$v] = array();}
while (!$rs->eof()) {
foreach ($rs->Fields as $v) $tv[$prefix.$v][] = $rs->get_field($v);
call_user_func($cb, $tv, $rs->get_row(), $prefix, $rs->current_row, $data);
$rs->next();
}
}
function arr_val($arr, $key_val, $def_val = '') {
if (is_array($arr) && isset($arr[$key_val])) return $arr[$key_val];
else return $def_val;
}
// should be used instead of print_r
function print_arr() {
$arg_list = func_get_args();echo '<pre>';
foreach ($arg_list as $v) {
print_r($v); echo "\n";}
echo '</pre>';}
// more convenient output
function echox ($text = ''){echo $text.BR."\n";}

function is_index(){
for ($i=0; $i<func_num_args(); ++$i){
$v=func_get_arg($i);if (!preg_match('/^[0-9]+$/', $v)) return false;}
return true;
}
function check_file($post_var_name){return ($_FILES[$post_var_name]['size']>0);}
function save_file_to_folder($post_var_name, $folder, $save_original_name = true, $full_folder = true)
{
	if (!$full_folder) {
		$p = $GLOBALS['FilePath'];
		if ( (substr($folder, -1) != '/') && (substr($folder, -1) != '\\') )
			$folder .= '/';
		$folder = str_replace('\\', '/', $folder);
		$a = explode('/', $folder);
		if (!is_dir($p)) {
			@mkdir($p, 0777);
			@chmod($p, 0777);
		}
		foreach ($a as $v)
			if (strlen($v)) {
				$p .= ($v .'/');
				if (!@is_dir($p)) {
					@mkdir($p, 0777);
					@chmod($p, 0777);
				}
			}
	
		$folder = $GLOBALS['FilePath'] . $folder;
		
	}

	$file_name = (($save_original_name)?($_FILES[$post_var_name]['name']):(time() . '_' . $_FILES[$post_var_name]['name']));
	if ($save_original_name)
	{
		$file_name = strtolower($_FILES[$post_var_name]['name']);
	}
	else 
	{
		$mark = microtime();
		$mark = substr($mark,11,11).substr($mark,2,6);
		$ext = strrchr($_FILES[$post_var_name]['name'], '.');
		$file_name = strtolower($mark.(($ext===false)?'':$ext));
	}
	if (@file_exists($folder . $file_name))
	{
		@chmod($folder . $file_name, 0777);
		@unlink($folder . $file_name);
	}
	if (@move_uploaded_file($_FILES[$post_var_name]['tmp_name'], $folder . $file_name))
		return $file_name;
	else
		return false;
}
function compare($str1, $str2){
	if ( strcasecmp($str1, $str2)===0) return true; else return false;
}
function generate_back_url($url)
{
	return base64_encode(str_replace("/", "*", substr($url, 1, strlen($url))));
}
function gen_rand_name($filename){
	$mark = microtime();
	$mark = substr($mark,11,11).substr($mark,2,6);
	$ext = strrchr($filename, '.');
	return strtolower($mark.(($ext===false)?'':$ext));
}
function get_month_name($month)
{
	$months = array(
		'1' => 'января', 
		'2' => 'февраля', 
		'3' => 'марта', 
		'4' => 'апреля', 
		'5' => 'мая', 
		'6' => 'июня', 
		'7' => 'июля', 
		'8' => 'августа', 
		'9' => 'сентября', 
		'10' => 'октября', 
		'11' => 'ноября', 
		'12' => 'декабря', 
	);
	return $months[$month];
}
function convert_template($template){
	return str_replace('&lt;', '<', str_replace('&gt;', '>', $template));
}

function translit( $cyr_str) {
$tr = array(
"Ґ"=>"G","Ё"=>"YO","Є"=>"E","Ї"=>"YI","І"=>"I",
"і"=>"i","ґ"=>"g","ё"=>"yo","№"=>"#","є"=>"e",
"ї"=>"yi","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
"Д"=>"D","Е"=>"E","Ж"=>"ZH","З"=>"Z","�?"=>"I",
"Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
"О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
"У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
"Ш"=>"SH","Щ"=>"SCH","Ъ"=>"'","Ы"=>"YI","Ь"=>"",
"Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
"в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"zh",
"з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
"ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"'",
"ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya"
);
return strtr($cyr_str,$tr);
}


?>