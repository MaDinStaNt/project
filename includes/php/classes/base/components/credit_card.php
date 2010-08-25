<?php
/**
 * @package LLA.Base
 */
/**
 * @package LLA.Base
 */
class CCreditCard
{
	function get_normalize_cc($cc) // returns normalized credit card number
	{
		return ereg_replace('[^0123456789]', '', $cc);
	}

	function get_cc_type($cc) // returns credit card type
	{
		$cc = CCreditCard::get_normalize_cc($cc);

		if (ereg('^4(.{12}|.{15})$', $cc)) return 'Visa Card';
		if (ereg('^5[1-5].{14}$', $cc)) return 'Master Card';
		if (ereg('^3[47].{13}$', $cc)) return 'American Express';

		if (ereg('^3(0[0-5].{11}|[68].{12})$', $cc)) return 'Diners Club/Carte Blanche';
		if (ereg('^6011.{12}$', $cc)) return 'Discover Card';
		if (ereg('^(3.{15}|(2131|1800).{11})$', $cc)) return 'JCB';
		if (ereg('^(2(014|149).{11})$', $cc)) return 'enRoute';
		return '';
	}

	function is_valid_cc($cc) // check credit card number for formal validity
	{
		$cc = strrev(CCreditCard::get_normalize_cc($cc));
		if($cc == 0) return false;
		$sum = 0;
		$digits = '';
		for ($i=0; $i<strlen($cc); $i++) $digits .= ($i % 2)?($cc[$i]*2):($cc[$i]);
		for ($i=0; $i<strlen($digits); $i++) $sum += $digits[$i];
		return (($sum % 10) == 0);
	}
}
?>