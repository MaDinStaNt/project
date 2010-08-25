<?
/**
 * @package LLA.Base
 */
/*
--------------------------------------------------------------------------------
Class CValidatorMeta v 1.3.0.0
for internal use by CValidator only

history:
	v 1.3.0.0 - date/time validators fixed (VK)
	v 1.2.1.0 - get_html bug fixed (VK)
	v 1.2.0.2 - min length for date disabled (VK)
	v 1.2.0.1 - default value bug fixed (VK)
	v 1.2.0.0 - multiple validation per field (VK)
	v 1.1.4.3 - js escaping, disabled controls fixed (VK)
	v 1.1.4.2 - add new image type x-png in the image types array (AD)
	v 1.1.4.1 - bug with pattern validation fixed (VK)
	v 1.1.4.0 - add min range of the year 1900 (AD)
	v 1.1.3.0 - validation of time values added (VK)
	v 1.1.2.0 - add VRT_ODBCDATETIME (yyyy-mm-dd hh:ii:ss) (AD)
	v 1.1.1.0 - add VRT_ODBCDATE (yyyy-mm-dd) (AD)
	v 1.1.0.0 - improving (VK)
	v 1.0.0.0 - created (VK)
--------------------------------------------------------------------------------
*/

/**
 */
// predefined types of validation
define('VRT_REGEXP', 1); // add_info1 - pattern, ie '/.../i';
define('VRT_EMAIL', 2); // name@server.ext
define('VRT_PASSWORD', 3); // min - 4 symbols, max - 64 symbols
define('VRT_US_PHONE', 4); // 123 123 1234
define('VRT_WORLD_PHONE', 5); // any combination up to 30 characters
define('VRT_COUNTRY', 6); // any combination of alpha up to 30 characters
define('VRT_IMAGE_FILE', 7); // add_info1 - max width, add_info2 - max height
define('VRT_CUSTOM_FILE', 8); // add_info1 - optional array of mime types
define('VRT_TEXT', 9); // add_info1 - min length, add_info2 - max length, can be null
define('VRT_US_ZIP', 10); // 12345 [6789]
define('VRT_STATE', 25); // abbreviation of US state
define('VRT_PROVINCE', 26); // any combination of alpha up to 30 characters, add_info1-3 - same as enumeration
define('VRT_DATE', 11); // date in format dd.mm.yyyy or mm/dd/yyyy
define('VRT_ODBCDATE', 30); // date in format yyyy-mm-dd
define('VRT_ODBCDATETIME', 33); // date in format yyyy-mm-dd hh:ii:ss
define('VRT_TIME', 12); // time in format hh:mm:ss [am/pm]
define('VRT_DATETIME', 13); // VRT_DATE [space] VRT_TIME
define('VRT_YEAR', 14); // yyyy, add_info1 - min year
define('VRT_MONTH', 15); // mm
define('VRT_DAY', 16); // dd
define('VRT_HOUR', 17); // hh
define('VRT_MINUTE', 18); // mm
define('VRT_SECOND', 19); // ss
define('VRT_MSECOND', 20); // msec
define('VRT_PRICE', 21); // add_info1 - max price
define('VRT_ENUMERATION', 22); // add_info1 - 1. array of possible values
							  //			  2. CRecordSet, then add_info2 - name of field
define('VRT_KEYENUMERATION', 28); // add_info1 - 1. array where keys are possible values
define('VRT_NUMBER', 23); // add_info1 - min value, add_info2 - max value, can be null
define('VRT_FLOAT', 24); // add_info1 - min value, add_info2 - max value, can be null
define('VRT_CALLBACK', 27); // add_info1 - function which check value
define('VRT_CC', 29); // CC
define('VRT_URL_HTTP', 31 ); // web url validation http://mysite.com pattern: '~^(http(s)?:\/\/)?([a-z]+[a-z,0-9]+[-]?[a-z,0-9]+\.)+[a-z]+(:[0-9]+)?(\/.*)?$~i'

require_once(FUNCTION_PATH . 'functions.normalize.php');

function is_leap_year($year)
{
	return ( ($year%4 == 0) && !( ($year%100 == 0) && ($year%400 != 0) ) );
}
function js_escape_string($str)
{
	$str = str_replace("\\", "\\\\", strval($str));
	$str = str_replace("'", "\\'", $str);
	$str = str_replace("\r", "\\r", $str);
	$str = str_replace("\n", "\\n", $str);
	$str = str_replace("\n", "\\n", $str);
	$str = str_replace("</script>", "</'+'script>", $str);
	return $str;
}

/**
 * @package LLA.Base
 */
class CValidatorMeta {
	var $form_name;
	var $group_name;
	var $display_name;
	var $type;
	var $default_value;
	var $required;

	var $checked;
	var $last_check;

	var $min_length = -1;
	var $max_length = -1;
	var $min_numeric_value = '';
	var $max_numeric_value = '';
	var $enumeration = array();
	var $pattern = '';

	var $add_info1 = null;
	var $add_info2 = null;
	var $add_info3 = null;

	function CValidatorMeta($form_name, $group_name, $type, $def, $required, $add_info1, $add_info2, $add_info3)
	{
		$this->checked = false;
		$this->last_check = false;

		$this->add($form_name, $group_name, $type, $def, $required, $add_info1, $add_info2, $add_info3);
	}
	
	function IsEqual(&$vmeta)
	{
		return (
			($this->form_name == $vmeta->form_name) &&
			($this->group_name == $vmeta->group_name) &&
			($this->type == $vmeta->type) &&
			($this->required == $vmeta->required)
			);
	}

	function add($form_name, $group_name, $type, $def, $required, $add_info1, $add_info2, $add_info3)
	{
		$this->add_info1 = $add_info1;
		$this->add_info2 = $add_info2;
		$this->add_info3 = $add_info3;

		global $app;
		$this->form_name = $form_name;
		if (is_null($group_name))
			$this->group_name = $form_name;
		else
			$this->group_name = $group_name;
		$this->display_name = $GLOBALS['app']->Localizer->get_gstring('_validator', $this->group_name);
		$this->type = $type;
		$this->default_value = $def;
		$this->required = $required;

		switch ($this->type)
		{
			case VRT_CC:
			{
				$this->pattern = '/^[0-9\- \.]+$/';
				$this->min_length = 6;
				$this->max_length = 64;
				break;
			} // end of VRT_CC
			case VRT_REGEXP: //
			{
				$this->pattern = $add_info1;
				break;
			} // end of VRT_REGEXP
			case VRT_US_PHONE:
			{
				$this->pattern = '/^\(?[0-9]{3}\)?[\-\. ]*[0-9]{3}[\-\. ]*[0-9]{4}$/';
				$this->min_length = 10;
				$this->max_length = 64;
				break;
			} // VRT_US_PHONE
			case VRT_WORLD_PHONE:
			{
				$this->min_length = 6;
				$this->max_length = 64;
				break;
			} // end of VRT_WORLD_PHONE
			case VRT_STATE:
			{
				$this->min_length = 2;
				$this->max_length = 2;
				break;
			} // end of VRT_STATE
			case VRT_COUNTRY:
			{
				require_once(FUNCTION_PATH.'functions.country.php');
				global $cnt_list;
				$this->enumeration = array_keys($cnt_list);
				break;
			} // end of VRT_COUNTRY
			case VRT_IMAGE_FILE:
			{
				break;
			} // end of VRT_IMAGE_FILE
			case VRT_CUSTOM_FILE:
			{
				if ( (!is_null($this->add_info1)) && (!is_array($this->add_info1)) )
					$this->add_info1 = explode(',', $this->add_info1);
				break;
			} // end of VRT_CUSTOM_FILE
			case VRT_US_ZIP:
			{
				$this->pattern = '/^[0-9]{5}([0-9]{4}){0,1}$/';
				$this->min_length = 5;
				$this->max_length = 9;
				break;
			} // end of VRT_US_ZIP
			case VRT_PROVINCE:
			{
				$this->min_length = 0;
				$this->max_length = 30;

				if (is_array($add_info1))
					$this->enumeration = $add_info1;
				elseif (is_object($add_info1)) {
					$this->enumeration = array();
					while (!$add_info1->eof()) {
						$this->enumeration[] = $add_info1->get_field($add_info2);
						$add_info1->next();
					}
				}

				break;
			}
			case VRT_EMAIL:
			{
				$this->pattern = '/^ *([a-z0-9_-]+\.)*[a-z0-9_-]+@([a-z0-9-]+\.)+.*$/i';
				$this->min_length = 5;
				$this->max_length = 255;
				break;
			} // end of VRT_EMAIL
			case VRT_URL_HTTP:{
				$this->min_length = 1;
				$this->pattern = '/^([a-z,0-9]*([-][a-z,0-9]+)*\.)+[a-z]+(:[0-9]+)?(\/.*)?$/i';
//				$this->pattern = '/^(http(s)?:\/\/){1}([a-z,0-9]*([-][a-z,0-9]+)*\.)+[a-z]+(:[0-9]+)?(\/.*)?$/i';
				$this->max_length = 255;
				break;
			}
			case VRT_PASSWORD:
			{
				$this->min_length = 4;
				$this->max_length = 64;
				break;
			} // end of VRT_PASSWORD
			case VRT_NUMBER:
			{
				$this->pattern = '/^[\-\+]?[0-9]+$/';
				if (is_numeric($add_info1))
					$this->min_numeric_value = $add_info1;
				if (is_numeric($add_info2))
					$this->max_numeric_value = $add_info2;
				break;
			} // end of VRT_NUMBER
			case VRT_FLOAT:
			{
				$this->pattern = '/^[\-\+]{0,1}[0-9]+[\.]{0,1}[0-9]*$/';
				if (is_numeric($add_info1))
					$this->min_numeric_value = $add_info1;
				if (is_numeric($add_info2))
				{
					$this->max_numeric_value = $add_info2;
					$this->max_length = strlen(''.$add_info2) + 3;
				}
				break;
			} //end of VRT_FLOAT
			case VRT_TEXT:
			{
				if (!is_null($add_info1))
					$this->min_length = $add_info1;
				if (!is_null($add_info2))
					$this->max_length = $add_info2;
				break;
			} // end of VRT_TEXT
			case VRT_ENUMERATION:
			{
				if (is_array($add_info1))
					$this->enumeration = $add_info1;
				elseif (is_object($add_info1)) {
					$this->enumeration = array();
					while (!$add_info1->eof()) {
						$this->enumeration[] = $add_info1->get_field($add_info2);
						$add_info1->next();
					}
				}
				else
					system_die('invalid add_info1');
				break;
			} // end of VRT_ENUMERATION
			case VRT_PRICE:
			{
				$this->pattern = '/^[\-\+]{0,1}[0-9]+[\.]{0,1}[0-9]*$/';
				$this->min_numeric_value = 0;
				if (is_numeric($add_info1))
					$this->max_numeric_value = $add_info1;
				break;
			} // end of VRT_PRICE
			case VRT_DATE:
			{
				$this->pattern = '/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/';
				//$this->min_length = 8;
				$this->max_length = 10;
				break;
			} // end of VRT_DATE
			case VRT_ODBCDATE:
			{
				$this->pattern = '/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/';
				//$this->min_length = 8;
				$this->max_length = 10;
				break;
			} // end of VRT_ODBCDATE
			case VRT_ODBCDATETIME:
			{
				$this->pattern = '/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/';
				$this->min_length = 14;
				$this->max_length = 19;
				break;
			} // end of VRT_ODBCDATETIME
			case VRT_TIME:
			{
				$this->pattern = '/^[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2} {0,1}([aApP][mM]){0,1}$/';
				$this->min_length = 5;
				$this->max_length = 11;
				break;
			} // end of VRT_TIME
			case VRT_DATETIME:
			{
				$this->pattern = '/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/';
				$this->min_length = 14;
				$this->max_length = 19;
				break;
			} // end of VRT_DATETIME
			case VRT_YEAR:
			{
				$this->min_length = 4;
				$this->max_length = 4;
				$this->pattern = '/^[0-9]{4}$/';
				if (is_numeric($add_info1))
					$this->min_numeric_value = $add_info1;
				if (is_numeric($add_info2))
					$this->max_numeric_value = $add_info2;
				break;
			} // end of VRT_YEAR
			case VRT_MONTH:
			{
				$this->min_length = 1;
				$this->max_length = 2;
				$this->pattern = '/^[0-9]{1,2}$/';
				$this->min_numeric_value = 1;
				$this->max_numeric_value = 12;
				break;
			} // end of VRT_MONTH
			case VRT_DAY:
			{
				$this->min_length = 1;
				$this->max_length = 2;
				$this->pattern = '/^[0-9]{1,2}$/';
				$this->min_numeric_value = 1;
				$this->max_numeric_value = 31;
				break;
			} // end of VRT_DAY
			case VRT_HOUR:
			{
				$this->min_length = 1;
				$this->max_length = 2;
				$this->pattern = '/^[0-9]{1,2}$/';
				$this->min_numeric_value = 1;
				$this->max_numeric_value = 24;
				break;
			} // end of VRT_HOUR
			case VRT_MINUTE:
			{
				$this->min_length = 1;
				$this->max_length = 2;
				$this->pattern = '/^[0-9]{1,2}$/';
				$this->min_numeric_value = 1;
				$this->max_numeric_value = 60;
				break;
			} // end of VRT_MINUTE
			case VRT_SECOND:
			{
				$this->min_length = 1;
				$this->max_length = 2;
				$this->pattern = '/^[0-9]{1,2}$/';
				$this->min_numeric_value = 1;
				$this->max_numeric_value = 60;
				break;
			} // end of VRT_SECOND
			case VRT_MSECOND:
			{
				$this->min_length = 1;
				$this->max_length = 4;
				$this->pattern = '/^[0-9]${1,4}/';
				$this->min_numeric_value = 0;
				$this->max_numeric_value = 9999;
				break;
			} // end of VRT_MSECOND
		}
	}

	function validate_input(&$errors, &$infos) {
		global $app;
		if ($this->checked) return $this->last_check;

		global $input_fields;
		if (!in_array($this->form_name, $input_fields)) {
			if (!$this->required)
				if (!isset($app->template_vars[$this->form_name]))
					$app->template_vars[$this->form_name] = $this->default_value;
			return true;
		}

		$this->checked = true;
		$this->last_check = true;

		$value = $this->default_value;
		if ( ($this->type == VRT_IMAGE_FILE) || ($this->type == VRT_CUSTOM_FILE) ) {
			$value = '';
			$app->template_vars[$this->form_name.'_file_content'] = '';
			$app->template_vars[$this->form_name.'_file_type'] = '';
			if (isset($_FILES[$this->form_name])) {
				if ( (intval($_FILES[$this->form_name]['size']) > 0) && (intval($_FILES[$this->form_name]['error']) == 0) ) {
					$fh = @fopen($_FILES[$this->form_name]['tmp_name'], 'rb');
					if ($fh) {
						$value = $_FILES[$this->form_name]['name'];
						$app->template_vars[$this->form_name.'_file_type'] = $_FILES[$this->form_name]['type'];
						$app->template_vars[$this->form_name.'_file_content'] = fread($fh, filesize($_FILES[$this->form_name]['tmp_name']));
						fclose($fh);
					}
				}
				if ( (intval($_FILES[$this->form_name]['error']) != 0) && (intval($_FILES[$this->form_name]['error']) != 4) )
				{
					$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_invalid_file', $this->display_name);
					$this->last_check = false;
					return false;
				}
			}
		}
		else
			$value = InPostGet($this->form_name, $this->default_value);

		if (is_array($value))
			foreach ($value as $k => $v)
				$value[$k] = trim($value[$k]);
		else
		{
			$value = trim($value);
			if ( (strlen($this->default_value)>0) && (strlen($value) == 0) )
				$value = $this->default_value;
		}

		if ( ($this->type == VRT_STATE) || ($this->type == VRT_PROVINCE) )
		{
			$states_mod = &$GLOBALS['app']->get_module('States');
			$value = $states_mod->get_state_name($value);
		}
		$app->template_vars[$this->form_name] = $value;

		if ( (!$this->required) && ($value == '') )
		{
			$this->last_check = true;
			return true;
		}

		if ( (!is_array($value)) && (strlen($value)==0) )
		{
			$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_empty_string', $this->display_name);
			$this->last_check = false;
			return false;
		}

		if ($this->min_length > 0)
			if (strlen($value) < $this->min_length)
			{
				$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_min_length', $this->display_name, $this->min_length);
				$this->last_check = false;
				return false;
			}

		if ($this->max_length > 0)
			if (strlen($value) > $this->max_length)
			{
				$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_max_length', $this->display_name, $this->max_length);
				$this->last_check = false;
				return false;
			}

		if (strlen($this->min_numeric_value) != 0)
		{
			$tmp = ((!is_array($value))?(array($value)):($value) );
			foreach ($tmp as $vlv)
				if (doubleval($vlv) < doubleval($this->min_numeric_value))
				{
					$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_min_value', $this->display_name, $this->min_numeric_value);
					$this->last_check = false;
					return false;
				}
		}

		if (strlen($this->max_numeric_value))
		{
			$tmp = ((!is_array($value))?(array($value)):($value) );
			foreach ($tmp as $vlv)
				if (doubleval($vlv) > doubleval($this->max_numeric_value))
				{
					$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_max_value', $this->display_name, $this->max_numeric_value);
					$this->last_check = false;
					return false;
				}
		}

		if (count($this->enumeration))
		{
			$tmp = ((!is_array($value))?(array($value)):($value) );
			foreach ($tmp as $vlv)
				if (!in_array($vlv, $this->enumeration))
				{
					$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_valid_value', $this->display_name);
					$this->last_check = false;
					return false;
				}
		}
		if ($this->type == VRT_KEYENUMERATION)
		{
			$tmp = ((!is_array($value))?(array($value)):($value) );
			foreach ($tmp as $vlv)
				if (!isset($this->add_info1[$vlv]))
				{
					$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_valid_value', $this->display_name);
					$this->last_check = false;
					return false;
				}
		}

		if (strlen($this->pattern))
		{
			$tmp = ((!is_array($value))?(array($value)):($value) );
			foreach ($tmp as $vlv)
				if (!preg_match($this->pattern, $vlv))
				{
					$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_valid_value', $this->display_name);
					$this->last_check = false;
					return false;
				}
		}

		if ( ($this->type == VRT_CUSTOM_FILE) || ($this->type == VRT_IMAGE_FILE) )
		{
			if ($app->template_vars[$this->form_name.'_file_content'] == '')
			{
				$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_invalid_file', $this->display_name);
				$this->last_check = false;
				return false;
			}
		}

		if ( ($this->type == VRT_CUSTOM_FILE) && (!is_null($this->add_info1)) )
		{
			if (!in_array($_FILES[$this->form_name]['type'], $this->add_info1))
			{
				$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_invalid_file', $this->display_name);
				$this->last_check = false;
				return false;
			}
		}

		if ($this->type == VRT_IMAGE_FILE)
		{
			$image_types = array('image/x-xbitmap', 'image/bmp', 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/tiff', 'image/x-icon');
			if (!in_array($_FILES[$this->form_name]['type'], $image_types))
			{
				$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_not_image_file', $this->display_name);
				$this->last_check = false;
				return false;
			}
		}

		if ($this->type == VRT_CALLBACK)
			if (!call_user_func($this->add_info1, $this->group_name, $value))
			{
				$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_valid_value', $this->display_name);
				$this->last_check = false;
				return false;
			}

		if ($this->type == VRT_CC)
		{
			require_once(BASE_CLASSES_PATH . 'components/credit_card.php');
			if (!CCreditCard::is_valid_cc($value))
			{
				$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_valid_value', $this->display_name);
				$this->last_check = false;
				return false;
			}
			else
			{
				$value = CCreditCard::get_normalize_cc($value);
				$app->template_vars[$this->form_name] = $value;
			}
		}

		if ( ($this->type == VRT_TIME) || ($this->type == VRT_DATE) || ($this->type == VRT_DATETIME) || ($this->type == VRT_ODBCDATE) || ($this->type == VRT_ODBCDATETIME) )
		{
			$matches = array();
			$year = 0;
			$month = 0;
			$day = 0;
			$hour = 0;
			$minute = 0;
			$second = 0;
			if ($this->type == VRT_TIME)
			{
				preg_match('/^([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}) {0,1}([aApP][mM]){0,1}$/', $value, $matches);
				$year = intval(date('Y'), 10);
				$month = 1;
				$day = 1;
				$hour = intval($matches[1]);
				$minute = intval($matches[2]);
				$second = intval($matches[3]);
				if (isset($matches[4]))
					if (strcasecmp($matches[4], 'pm') == 0)
						$hour += 12;
			}
			if ($this->type == VRT_DATE)
			{
				preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $value, $matches);
				$year = intval($matches[3]);
				$month = intval($matches[1]);
				$day = intval($matches[2]);
				$value = str_pad(strval($year), 4, '0', STR_PAD_LEFT) . '-' . str_pad(strval($month), 2, '0', STR_PAD_LEFT) . '-' . str_pad(strval($day), 2, '0', STR_PAD_LEFT);
			}
			if ($this->type == VRT_DATETIME)
			{
				preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/', $value, $matches);
				$year = intval($matches[3]);
				$month = intval($matches[1]);
				$day = intval($matches[2]);
				$hour = intval($matches[4]);
				$minute = intval($matches[5]);
				$second = intval($matches[6]);
				$value = str_pad(strval($year), 4, '0', STR_PAD_LEFT) . '-' . str_pad(strval($month), 2, '0', STR_PAD_LEFT) . '-' . str_pad(strval($day), 2, '0', STR_PAD_LEFT) . '-' . str_pad(strval($hour), 2, '0', STR_PAD_LEFT) . '-' . str_pad(strval($minute), 2, '0', STR_PAD_LEFT) . '-' . str_pad(strval($second), 2, '0', STR_PAD_LEFT);
			}
			if ($this->type == VRT_ODBCDATE)
			{
				preg_match('/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})$/', $value, $matches);
				$year = intval($matches[1]);
				$month = intval($matches[2]);
				$day = intval($matches[3]);
				$value = str_pad(strval($year), 4, '0', STR_PAD_LEFT) . '-' . str_pad(strval($month), 2, '0', STR_PAD_LEFT) . '-' . str_pad(strval($day), 2, '0', STR_PAD_LEFT);
			}
			if ($this->type == VRT_ODBCDATETIME)
			{
				preg_match('/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/', $value, $matches);
				$year = intval($matches[1]);
				$month = intval($matches[2]);
				$day = intval($matches[3]);
				$hour = intval($matches[4]);
				$minute = intval($matches[5]);
				$second = intval($matches[6]);
				$value = str_pad(strval($year), 4, '0', STR_PAD_LEFT) . '-' . str_pad(strval($month), 2, '0', STR_PAD_LEFT) . '-' . str_pad(strval($day), 2, '0', STR_PAD_LEFT) . '-' . str_pad(strval($hour), 2, '0', STR_PAD_LEFT) . '-' . str_pad(strval($minute), 2, '0', STR_PAD_LEFT) . '-' . str_pad(strval($second), 2, '0', STR_PAD_LEFT);
			}

			if ($year < 1900)
			{
				$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_invalid_year', $this->display_name);
				$this->last_check = false;
				return false;
			}

			if ( ($month<1) || ($month>12) )
			{
				$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_invalid_date', $this->display_name);
				$this->last_check = false;
				return false;
			}

			$months = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);
			if (is_leap_year($year)) $months[2] = 29;
			if ( ($day<1) || ($day>$months[$month]) )
			{
				$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_invalid_date', $this->display_name);
				$this->last_check = false;
				return false;
			}

			if ( ($hour<0) || ($hour>23) )
			{
				$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_invalid_time', $this->display_name);
				$this->last_check = false;
				return false;
			}
			if ( ($minute<0) || ($minute>59) )
			{
				$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_invalid_time', $this->display_name);
				$this->last_check = false;
				return false;
			}
			if ( ($second<0) || ($second>59) )
			{
				$errors[$this->group_name] = $GLOBALS['app']->Localizer->get_gstring('_validator_message', 'validator_invalid_time', $this->display_name);
				$this->last_check = false;
				return false;
			}
		}

		if ($this->last_check)
			$infos[$this->group_name] = $this->form_name.' is valid';

		$app->template_vars[$this->form_name] = $value;
		return $this->last_check;
	}

	function get_js($current_running_form, $current_form_fields) {
		global $app;
		$loc = &$GLOBALS['app']->Localizer;

		$out_js = '';

		/*if ( ($this->type == VRT_IMAGE_FILE) || ($this->type == VRT_CUSTOM_FILE) )
			return $out_js;*/
		if (in_array($this->form_name, $current_form_fields))
		{
			$out_js .= 'if (f.elements["'.$this->form_name.'"]) {';
			$out_js .= 'var el_val = GetValue(f.elements["'.$this->form_name.'"]);';
			if ($this->required)
				$out_js .= 'if (trim(el_val).length == 0) return ShowAlert(\'' . js_escape_string($loc->get_gstring('_validator_message', 'validator_empty_string', $this->display_name)) . '\', f, \''.$this->form_name.'\');';
			else
				$out_js .= 'if (trim(el_val).length > 0) {'."\n";
			if ($this->type == VRT_CC)
                $out_js .= 'if (!isValidCC(el_val)) return ShowAlert(\'' . js_escape_string($loc->get_gstring('_validator_message', 'validator_invalid_cc', $this->display_name)) . '\', f, \''.$this->form_name.'\');';
			if (strlen($this->min_numeric_value))
			{
				$out_js .= 'if (isNaN(el_val)) return ShowAlert(\'' . js_escape_string($loc->get_gstring('_validator_message', 'validator_need_number', $this->display_name, $this->min_numeric_value)) . '\', f, \''.$this->form_name.'\');';
				$out_js .= 'if (parse_int(el_val) < '.$this->min_numeric_value.') return ShowAlert(\'' . js_escape_string($loc->get_gstring('_validator_message', 'validator_min_value', $this->display_name, $this->min_numeric_value)) . '\', f, \''.$this->form_name.'\');';
            }
			if (strlen($this->max_numeric_value))
			{
				$out_js .= 'if (isNaN(el_val)) return ShowAlert(\'' . js_escape_string($loc->get_gstring('_validator_message', 'validator_need_number', $this->display_name, $this->min_numeric_value)) . '\', f, \''.$this->form_name.'\');';
				$out_js .= 'if (parse_int(el_val) > '.$this->max_numeric_value.') return ShowAlert(\'' . js_escape_string($loc->get_gstring('_validator_message', 'validator_max_value', $this->display_name, $this->max_numeric_value)) . '\', f, \''.$this->form_name.'\');';
            }
			if ($this->min_length > 0)
			{
                    $out_js .= 'if (trim(el_val).length < '.$this->min_length.') return ShowAlert(\'' . js_escape_string($loc->get_gstring('_validator_message', 'validator_min_length', $this->display_name, $this->min_length)) . '\', f, \''.$this->form_name.'\');';
            }
            if ($this->type == VRT_EMAIL)
                $out_js .= 'if (!isValidEmail(el_val)) return ShowAlert(\'' . js_escape_string($loc->get_gstring('_validator_message', 'validator_valid_email', $this->display_name)) . '\', f, \''.$this->form_name.'\');';
            if ($this->type == VRT_URL_HTTP )
                $out_js .= 'if (!isValidUrlHTTP(el_val)) return ShowAlert(\'' . js_escape_string($loc->get_gstring('_validator_message', 'validator_valid_url', $this->display_name)) . '\', f, \''.$this->form_name.'\');';
			if (strlen($this->pattern))
			{
				$out_js .= 'if (!checkRE(el_val, \''.js_escape_string($this->pattern).'\')) return ShowAlert(\'' . js_escape_string($loc->get_gstring('_validator_message', 'validator_valid_value', $this->display_name)) . '\', f, \''.$this->form_name.'\');';
			}
			if (!$this->required)
				$out_js .= '}';
			$out_js .= '}';
		}

		return $out_js;
	}

	function get_html($type) {
		if ( (strcasecmp($type, 'text')==0) || (strcasecmp($type, 'password')==0) ) {
			if ($this->max_length > 0)
				return array('maxlength' => $this->max_length);
			elseif (strlen($this->max_numeric_value) > 0)
				return array('maxlength' => strlen($this->max_numeric_value));
		}

		if (strcasecmp($type, 'file')==0)
		{
			$c = array();
			if ( ($this->type == VRT_CUSTOM_FILE) && (is_array($this->add_info1)) )
				$c = $this->add_info1;
			if ($this->type == VRT_IMAGE_FILE)
				$c = array('image/x-xbitmap', 'image/bmp', 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/tiff', 'image/x-icon');
			if (count($c))
				return array('accept' => join(',', $c));
		}
		return array();
	}

	function format_value($type, $value) {
		if ($this->type == VRT_PRICE)
			if (preg_match('/^[\-\+]{0,1}[0-9]+[\.]{0,1}[0-9]*$/', $value))
				return normalize_price($value);
			else
				return $value;
		return $value;
	}
}
/*
--------------------------------------------------------------------------------
Class CValidator v 1.2.1.0
Validator of input variables

methods:
	add(
		$form_name - name of the variable in the form
		$type - on of the predefined types (see above)
		$add_info1 - depends on $type, see above for details
		$add_info2 - --/--
		$add_info3 - --/--
		)

	bool validate_input() - validates input of the page

	int validity($name) - returns status of one variable:
							V_INVALID - invalid value
							V_VALID - valid value
							V_NOT_CHECKED - value not checked

	array get_errors() - returns associative array of errors

	array get_infos() - returns associative array of information messages

use:
	CValidator::add('var1', VRT_TEXT, 0, 10);
	CValidator::add('var2', VRT_PASSWORD);
	CValidator::add('var3', VRT_NUMBER, 5, 6); // var3 is between 5 and 6

	// set choises values for select boxes, if needed, see CInput::set_select_data($name, $assoc_array)
	CInput::set_select_data('var3', array('5' => 'Five', '6' => 'Six'));

	if (CForm::is_submit('form_name'))
		if (CValidator::validate_input())
			echo 'cool';
		else
			$this->template_vars['variable name'] = CValidator::get_errors();
	else
	{
		// set default values in template_vars
		$this->template_vars['var1'] = 'default text for var1 field';
		$this->template_vars['var2'] = ''; // this is password field so default value cannot be set
		$this->template_vars['var3'] = '6'; // Six string will be selected in select box var3
	}

	in html template should be output of the array 'variable name', see IT:SimpleArrayOutput

history:
	v 1.2.1.0 - get_html bug fixed (VK)
	v 1.2.0.0 - javascript generation fixed (VK)
	v 1.1.0.0 - validity method is added (VK)
	v 1.0.0.0 - created (VK)
--------------------------------------------------------------------------------
*/

$internal_metas = array();
$input_fields = array();
$internal_errors = array();
$internal_infos = array();

/**
 * @package LLA.Base
 */
class CValidator {
	function add($name, $type, $add_info1 = null, $add_info2 = null, $add_info3 = null) {
		global $internal_metas;
		$internal_metas[$name][] = new CValidatorMeta($name, null, $type, '', true, $add_info1, $add_info2, $add_info3);
	}

	function add_nr($name, $type, $def, $add_info1 = null, $add_info2 = null, $add_info3 = null) {
		global $internal_metas;
		$internal_metas[$name][] = new CValidatorMeta($name, null, $type, $def, false, $add_info1, $add_info2, $add_info3);
	}

	function add_in_group($name, $group_name, $type, $add_info1 = null, $add_info2 = null, $add_info3 = null) {
		global $internal_metas;
		$internal_metas[$name][] = new CValidatorMeta($name, $group_name, $type, '', true, $add_info1, $add_info2, $add_info3);
	}

	function add_in_group_nr($name, $group_name, $type, $def, $add_info1 = null, $add_info2 = null, $add_info3 = null) {
		global $internal_metas;
		$internal_metas[$name][] = new CValidatorMeta($name, $group_name, $type, $def, false, $add_info1, $add_info2, $add_info3);
	}

	function validate_input() {
		global $internal_metas;
		global $input_fields;
		global $internal_errors, $internal_infos;

		$it_fs = InPostGet('_it_fs', '');
		if ( (strlen($it_fs) == 0) || (strcasecmp($it_fs, 'g') == 0) )
			$input_fields = array_keys($_GET);
		else
			$input_fields = array_merge(array_keys($_POST), explode(',', base64_decode(InPostGet('_it_fs', ''))));
		foreach ($input_fields as $k)
			$GLOBALS['app']->template_vars[$k] = InPostGet($k);

		$valid = true;
		$mts = array_keys($internal_metas);
		foreach ($mts as $k)
		{
			$mts2 = array_keys($internal_metas[$k]);
			foreach ($mts2 as $idx)
				$valid &= $internal_metas[$k][$idx]->validate_input($internal_errors, $internal_infos);
		}

		return $valid;
	}

	function get_infos() {
		return $GLOBALS['internal_infos'];
	}

	function get_errors() {
		return $GLOBALS['internal_errors'];
	}

	function validity($name) {
		global $internal_errors;
		global $internal_infos;
		if (isset($internal_errors[$name]))
			return V_INVALID;
		if (isset($internal_infos[$name]))
			return V_VALID;
		return V_NOT_CHECKED;
	}

	function get_js($current_running_form, $current_form_fields) {
		global $internal_metas;
		$out = '';
		$mts = array_keys($internal_metas);
		foreach ($mts as $k)
		{
			$mts2 = array_keys($internal_metas[$k]);
			foreach ($mts2 as $idx)
				$out .= $internal_metas[$k][$idx]->get_js($current_running_form, $current_form_fields);
		}
		return $out;
	}

	function get_html($name, $type) {
		global $internal_metas;
		if (isset($internal_metas[$name]))
		{
			$out = array();
			$mts = array_keys($internal_metas[$name]);
			foreach ($mts as $idx)
			{
				$va = $internal_metas[$name][$idx]->get_html($type);
				if (count($va))
					if (count($out))
						array_merge($out, $va);
					else
						$out = $va;
			}
			return $out;
		}
		else
			return array();
	}

	function format_value($name, $type, $value) {
		global $internal_metas;
		if (isset($internal_metas[$name]))
		{
			$mts = array_keys($internal_metas[$name]);
			foreach ($mts as $idx)
				$value = $internal_metas[$name][$idx]->format_value($type, $value);
		}
		return $value;
	}

	function clear()
	{
		global $internal_metas;
		$internal_metas = array();
	}
}
?>