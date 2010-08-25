<?
/**
 * @package PACATUM.Base
 */
/**
 */
define('SESSION_LIFE_TIME', 300);

class  CSession  {
    var $Application;
    var $tv;
    var $last_error;

	function CSession(&$app)
	{
        $this->Application = &$app;
        $this->tv = &$app->template_vars;
	}
	
	function session_kill()
	{
		if ($this->Application->User->is_logged()) {
			$this->Application->DataBase->delete_sql('user_session', array('user_id' => $this->Application->User->UserData['id']));
		}
	}
	
    function session_notify($user_ip)
    {
		global $HTTP_COOKIE_VARS, $HTTP_GET_VARS;
		
		$cookiename = 'session';
		$cookiepath = '';
		$cookiedomain = '';
		$cookiesecure = '';
		
		$current_time = time();
		
		if (isset($HTTP_COOKIE_VARS[$cookiename . '_data']))
		{
			$sessiondata = unserialize($HTTP_COOKIE_VARS[$cookiename . '_data']);
		}
		else
		{
			$sessiondata = array();
		}
		
		$session_id = session_id();
		
		if ( !empty($session_id) && (sizeof($sessiondata) > 0) )
		{
			if ($current_time - $sessiondata['last_update_time'] > 60) {
				$sessiondata['last_update_time'] = $current_time;
				$session_rs = $this->Application->DataBase->select_sql('user_session', array('session_id' => $session_id));
				if ( ($session_rs !== false)&&(!$session_rs->eof()) )
				{
					$ip_check_s = substr($session_rs->get_field('session_ip'), 0, 6);
					$ip_check_u = substr($user_ip, 0, 6);
					if ($ip_check_s == $ip_check_u)
					{
						$this->Application->DataBase->update_sql('user_session', array('session_time' => $current_time), array('session_id' => $session_rs->get_field('session_id')));
						$expiry_time = $current_time - SESSION_LIFE_TIME;
						$this->Application->DataBase->custom_sql("DELETE FROM %prefix%user_session WHERE session_time < ".$expiry_time." AND session_id <> '".$session_id."'");
						setcookie($cookiename . '_data', serialize($sessiondata), $current_time + 31536000, $cookiepath, $cookiedomain, $cookiesecure);
						return true;
					}
				}
				else {
					if ($this->Application->User->is_logged()) {
						$this->Application->DataBase->custom_sql("
							INSERT INTO 
								%prefix%user_session
								(session_id, user_id, session_start, session_time, session_ip) 
							VALUES 
								('".$session_id."', ".$this->Application->User->UserData['id'].", ".$current_time.", ".$current_time.", '".$user_ip."')
						");					
						setcookie($cookiename . '_data', serialize($sessiondata), $current_time + 31536000, $cookiepath, $cookiedomain, $cookiesecure);
					}
				}
			}
		}
		return true;
    }
}
?>