<?
/**
 * @package LLA.Base
 */
/**
 */
function rand_pass($size = 10, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') {
	list($usec,$sec) = explode(' ', microtime());
    $srandval = ((float)$sec+(float)$usec*100000);
    mt_srand($srandval);

	$result = '';
  	$chars_len = strlen($chars)-1;
  	for ($i = 0; $i < $size; $i++)
		$result .= $chars{mt_rand(0, $chars_len)};
  	return $result;
}
?>