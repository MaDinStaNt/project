<?php
/**
 * @package LLA.Base
 */
/**
 */
/*
class CPayFlow - Verisign PayFlow credit card checking

history:
	v 1.0 - created stub (VK)
*/

define('CC_TEST_MODE', '1');
define('CC_FREEZE', '1');

define('PF_USER', '');
define('PF_PASSWORD', '!');

if (!function_exists('pfpro_version'))
{
	require_once('pfpro.win32.php');
}

require_once(FUNCTION_PATH . 'functions.normalize.php');

function dump_array($arr)
{
	$str = "";
	foreach ($arr as $k => $v)
		$str .= "[".$k."] = ".$v."<br>\r\n";
	return $str;
}

/**
 * @package LLA.Base
 */
class CPayFlow
{
	var $Application;

	var $last_error;
	var $trans_id;

	function CPayFlow(&$app)
	{
		$this->Application = &$app;
		$this->trans_id = -1;
		if (@function_exists('pfpro_version'))
			$GLOBALS['GlobalDebugInfo']->Write('Verisign PayFlow version: '.pfpro_version());
	}

	function n_date($date)
	{
		$date_parts = explode('/', $date);

		$date_parts[0] = str_pad($date_parts[0], 2, '0', STR_PAD_LEFT);
		if (strlen($date_parts[1]) > 2) $date_parts[1] = substr($date_parts[1], -2);
		if (strlen($date_parts[1]) < 2) $date_parts[1] = str_pad($date_parts[0], 2, '0', STR_PAD_LEFT);
		return $date_parts[0] . $date_parts[1];
	}

	function capture($data, $amount, $freeze = true, $iteration = 1, $add_info = null)
	{
		$this->last_error = '';

		/*if (CC_TEST_MODE)
		{
			$this->trans_id = '-1';
			return true;
		}*/

		$request['ACCT'] = normalize_cc($data['gen']);
		$request['CVV2'] = $data['gen2'];
		$request['AMT'] = normalize_price($amount);
		$request['EXPDATE'] = $this->n_date($data['gen3']);

		if ($freeze)
			if ($iteration == 1)
				$request['TRXTYPE'] = 'A';
			else
			{
				if (strcasecmp($data['cc_trans_id'], '-1') != 0)
				{
					$request['TRXTYPE'] = 'D';
					$request['ORIGID'] = $data['cc_trans_id'];
				}
				else
					$request['TRXTYPE'] = 'S';
			}
		else
			$request['TRXTYPE'] = 'S';

		if (is_array($add_info))
			foreach ($add_info as $k => $v)
				$request[$k] = $v;

		$response = $this->_post($request);
		if ($response === false)
			return false;
		else
		{
			if (!isset($response['RESULT']))
				return false;

			if ($response['RESULT'] != '0')
			{
				$this->last_error = 'Credit Card Processing: '.$response['RESPMSG'];
				return false;
			}
			else
			{
				$this->trans_id = $response['PNREF'];
				return true;
			}
		}
	}

	function refund($amount, $x_trans_id)
	{
		$this->last_error = '';

		$request['TRXTYPE'] = 'C';
		$request['AMT'] = $amount;
		$request['ORIGID'] = $x_trans_id;

		$response = $this->_post($request);
		if ($response === false)
			return false;
		else
		{
			if (!isset($response['RESULT']))
				return false;

			if ($response['RESULT'] != '0')
			{
				$this->last_error = 'Credit Card Processing: '.$response['RESPMSG'];
				return false;
			}
			else
			{
				$this->trans_id = $response['PNREF'];
				return true;
			}
		}
	}

	function void($x_trans_id)
	{
		$this->last_error = '';

		$request['TRXTYPE'] = 'V';
		$request['ORIGID'] = $x_trans_id;

		$response = $this->_post($request);
		if ($response === false)
			return false;
		else
		{
			if (!isset($response['RESULT']))
				return false;

			if ($response['RESULT'] != '0')
			{
				$this->last_error = 'Credit Card Processing: '.$response['RESPMSG'];
				return false;
			}
			else
			{
				$this->trans_id = $response['PNREF'];
				return true;
			}
		}
	}

	function _post($data)
	{
		$data['PARTNER'] = 'VeriSign';
		$data['USER'] = PF_USER;
		$data['PWD'] = PF_PASSWORD;
		$data['VENDOR'] = PF_USER;
		$data['TENDER'] = 'C';

		$GLOBALS['GlobalDebugInfo']->Write('PayFlow Request: '.dump_array($data));

		global $ProxyServer, $ProxyPort, $CUrlProxyUserName, $CUrlProxyPassword;

		if (!function_exists('pfpro_process'))
		{
			$this->last_error = 'PFPro is not installed';
			return false;
		}
		@set_time_limit(30);
		$response = pfpro_process(
			$data,
			(CC_TEST_MODE)?('test-payflow.verisign.com'):('payflow.verisign.com'),
			443,
			15,
			$ProxyServer,
			$ProxyPort,
			$CUrlProxyUserName,
			$CUrlProxyPassword
		);

		if (!is_array($response))
		{
			$GLOBALS['GlobalDebugInfo']->Write('PayFlow Response: Error');
			$this->last_error = 'Cannot connect to server';

			return false;
		}
		else
		{
			$GLOBALS['GlobalDebugInfo']->Write('PayFlow Response: '.dump_array($response));
			return $response;
		}
	}

	function get_last_error()
	{
		return $this->last_error;
	}
}
?>