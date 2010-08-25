<?
/**
 * @package LLA.Base
 */
/*
--------------------------------------------------------------------------------
Class CTaxModule v 2.0.0.0

methods:
	percent get_tax($country_abbr, $state_abbr, $city_name, $zip_postal_code) - get percent rate of tax in state

history:
	v 2.0.0.0 - data from registry (VK)
	v 1.1.0.0 - revised, moved to template (VK)
	v 1.0.0.1 - created (VK)

$t->get_tax('US', '', '', '')
$t->get_tax('US', 'CA', '', '')
$t->get_tax('US', 'CA', 'City1', '')
$t->get_tax('US', 'CA', 'City2', '24010')
$t->get_tax('CUS', '', '', '24011')

--------------------------------------------------------------------------------
*/

/**
 * @package LLA.Base
 */
class CTaxModule
{
	var $Registry;
	var $last_error;

	function CTaxModule(&$app)
	{
		$this->Registry = &$app->get_module('Registry');
	}
	function get_last_error()
	{
		return $this->last_error;
	}
	function get_tax($country, $state, $city, $zip)
	{
		$this->last_error = '';
		$tax = $this->_get_tax($state, $city, $zip, -1);
		if ($tax === false) // error
			return false;
		return $tax;
	}
	function _get_tax($state, $city, $zip, $id_path)
	{
		// state look up
		$states = $this->Registry->get_nodes('_taxes', -1);
		if ($states === false)
		{
			$this->last_error = 'Cannot get States from registry';
			return false;
		}
		$sum_tax = 0;
		while (!$states->eof())
		{
			$v = $this->Registry->get_values_by_id_path($states->get_field('id_path'));
			if ($v === false) {
				$this->last_error = 'Cannot get State values from registry';
				return false;
			}
			if (strcasecmp($v['abbr'], $state) == 0)
				$sum_tax += doubleval($v['tax']);
			$states->next();
		}
		return $sum_tax;
	}
}
?>