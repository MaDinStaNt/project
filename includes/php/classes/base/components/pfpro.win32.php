<?php
/**
 * @package LLA.Base
 */
/**
 */
putenv('PFPRO_CERT_PATH=C:\\payflowpro\\win32\\certs\\');

$READ_PHP_INI = 0;
/* Define Constants Here		Pull Constants from PHP.INI
--------------------------	--------------------------- */
define('pfpro_defaulthost',		($READ_PHP_INI == 0) ? 'test-payflow.verisign.com'	: get_cfg_var('pfpro.defaulthost'));
define('pfpro_defaultport',		($READ_PHP_INI == 0) ? 443							: get_cfg_var('pfpro.defaultport'));
define('pfpro_defaulttimeout',	($READ_PHP_INI == 0) ? 30							: get_cfg_var('pfpro.defaulttimeout'));
define('pfpro_proxyaddress',	($READ_PHP_INI == 0) ? NULL							: get_cfg_var('pfpro.proxyaddress'));
define('pfpro_proxyport',		($READ_PHP_INI == 0) ? NULL							: get_cfg_var('pfpro.proxyport'));
define('pfpro_proxylogin',		($READ_PHP_INI == 0) ? NULL							: get_cfg_var('pfpro.proxylogin'));
define('pfpro_proxypassword',	($READ_PHP_INI == 0) ? NULL							: get_cfg_var('pfpro.proxypassword'));

define('PFPRO_EXE_PATH', 'C:\payflowpro\win32\bin\pfpro.exe');

function pfpro_init()
{
	/* This function is here for
		compatibility only. Returns
		NULL (nothing) */
	return(NULL);
}


function pfpro_cleanup()
{
	/* This function is here for
		compatibility only. Returns
		NULL (nothing) */
	return(NULL);
}


function pfpro_version()
{
	$result = array();
	@exec(PFPRO_EXE_PATH, $result);
	if ( (count($result) == 0) || (strlen($result[0]) < 4) )
		return false;

	$version = substr($result[0], strlen($result[0])-4, 4);
	return($version);
}


function pfpro_process($transaction, $url=pfpro_defaulthost, $port=pfpro_defaultport, $timeout=pfpro_defaulttimeout,
							$proxy_url=pfpro_proxyaddress, $proxy_port=pfpro_proxyport, $proxy_logon=pfpro_proxylogin,
							$proxy_password=pfpro_proxypassword)
{
	if(!(is_array($transaction)))
		return(NULL);

	/* destruct (transaction) array into (trans) string
		and dynamically add LENGTH TAGS */
	foreach($transaction as $val1=>$val2)
		$parmsArray[] = $val1 . '[' . strlen($val2) . ']=' . $val2;
	$parmsString = implode($parmsArray, '&');

	$trans = PFPRO_EXE_PATH . ' ';
	$trans .= $url . ' ';
	$trans .= $port . ' "';
	$trans .= $parmsString . '" ';
	$trans .= $timeout . ' ';
	$trans .= $proxy_url . ' ';
	$trans .= $proxy_port . ' ';
	$trans .= $proxy_logon . ' ';
	$trans .= $proxy_password;

	/* run transaction, if result blank, return(NULL) */
	@exec($trans, $result);
	if (!is_array($result))
		return NULL;
	if (!isset($result[0]))
		return null;
	if($result[0] == NULL)
		return(NULL);

	/* replace any '&' that are surrounded by spaces -- this assumes
		the '&' isn't a delimiter, but instead part of a message string
		and converting it to 'ASCII(38)' will prevent the explode function from
		thinking it's actually a delimiter. */
	$result[0] = str_replace(' & ', ' ASCII(38) ', $result[0]);

	/* construct (pfpro) array out of (result) string */
	$valArray = explode('&', $result[0]);

	foreach($valArray as $val)
	{
			$valArray2 = explode('=', $val);
			$pfpro[$valArray2[0]] = str_replace('ASCII(38)', '&', $valArray2[1]);
	}

	return($pfpro);
}


function pfpro_process_raw($transaction, $autoLenTags=1, $url=pfpro_defaulthost, $port=pfpro_defaultport, $timeout=pfpro_defaulttimeout,
									$proxy_url=pfpro_proxyaddress, $proxy_port=pfpro_proxyport, $proxy_logon=pfpro_proxylogin,
									$proxy_password=pfpro_proxypassword)
{
	if(!(is_string($transaction)))
		return(NULL);

	/* Check to see if autoLenTags is enabled */
	if($autoLenTags)
	{
		$transaction = str_replace(' & ', ' ASCII(38) ', $transaction);
		$transArray = explode('&', $transaction);

		foreach($transArray as $val)
			list($val1[], $val2[]) = explode('=', $val, 2);

		$cnt = count($transArray);
		for($x=0; $x<$cnt; $x++)
		{
			$val2[$x] = str_replace('ASCII(38)', '&', $val2[$x]);
			$a[] = $val1[$x] . '[' . strlen($val2[$x]) . ']=' . $val2[$x];
		}

		$transaction = implode('&', $a);
	}

	$trans = PFPRO_EXE_PATH . ' ';
	$trans .= $url . ' ';
	$trans .= $port . ' "';
	$trans .= $transaction . '" ';
	$trans .= $timeout . ' ';
	$trans .= $proxy_url . ' ';
	$trans .= $proxy_port . ' ';
	$trans .= $proxy_logon . ' ';
	$trans .= $proxy_password;

	/* run transaction, if result blank, return(NULL) */
	@exec($trans, $result);
	if($result[0] == NULL)
		return(NULL);

	return($result[0]);
}
?>