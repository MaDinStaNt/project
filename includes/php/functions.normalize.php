<?
/**
 * @package LLA.Base
 */
/**
 */
function normalize_phone($phone) {
	return preg_replace('/[^0123456789]/', '', $phone);
}

function normalize_price($val) {
  if (!is_numeric($val)) return 'invalid';//system_die('Invalid price - normalize_price('.$val.')');
  return number_format($val, 2, '.', ' ');
}

function normalize_cc($cc) {
	return ereg_replace('[^0123456789]', '', $cc);
}

function normalize_space($val) {
	if ($val >= 1024*1024*1024*1024) {
		return round($val / 1024 / 1024 / 1024 / 1024, 1) . ' Tb';
	}
	elseif ($val >= 1024*1024*1024) {
		return round($val / 1024 / 1024 / 1024, 1) . ' Gb';
	}
	elseif ($val >= 1024*1024) {
		return round($val / 1024 / 1024, 1) . ' Mb';
	}
	elseif ($val >= 1024) {
		return round($val / 1024, 1) . ' Kb';
	}
	else {
		return round($val, 1) . ' Bytes';
	}
}

function normalize_date($date_odbc)
{
    return date('M j, Y, h:i A', strtotime($date_odbc));
}

?>