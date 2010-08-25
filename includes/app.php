<?php
require_once('config/_config.inc.php');
require_once(FUNCTION_PATH . 'functions.php');
require_once(BASE_CLASSES_PATH . 'application.php');
require_once(BASE_CLASSES_PATH . 'firsthtmlpage.php'); 

define('OBJECT_ACTIVE', 1);
define('OBJECT_NOT_ACTIVE', 0);
define('OBJECT_SUSPENDED', 2);

define('TH_IMAGE_WIDTH', 150);
define('TH_IMAGE_HEIGHT', 225);

define('MD_IMAGE_WIDTH', 250);
define('MD_IMAGE_HEIGHT', 375);

define('OR_IMAGE_WIDTH', 60);
define('OR_IMAGE_HEIGHT', 90);

if (file_exists( FUNCTION_PATH . 'functions.ram.debug.php'))
	require_once(FUNCTION_PATH . 'functions.ram.debug.php');

class CApp extends CApplication
{
    function CApp()
    {
        parent::CApplication();
        $this->Modules['Interface'] = array(
                'ClassName' => 'CInterface',
                'ClassPath' => CUSTOM_CLASSES_PATH . 'components/interface.php',
                'Visual'=>'0',
                'Title' => 'Interface'
        );
        $this->Modules['Dictionaries'] = array(
                'ClassName' => 'CDictionaries',
                'ClassPath' => CUSTOM_CLASSES_PATH . 'components/dictionaries.php',
                'Visual'=>'0',
                'Title' => 'Dictionaries'
        );
         $this->Modules['Countries'] = array(
                'ClassName' => 'CCountries',
                'ClassPath' => CUSTOM_CLASSES_PATH . 'components/countries.php',
                'Visual'=>'0',
                'Title' => 'Countries'
        );
        $this->Modules['States'] = array(
                'ClassName' => 'CStates',
                'ClassPath' => CUSTOM_CLASSES_PATH . 'components/states.php',
                'Visual'=>'0',
                'Title' => 'States'
        );
   	}
    
    function on_page_init() {
        if (!parent::on_page_init())
                return false;
        global $DebugLevel;

	    $client_ip = ( !empty($HTTP_SERVER_VARS['REMOTE_ADDR']) ) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ( ( !empty($HTTP_ENV_VARS['REMOTE_ADDR']) ) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : getenv('REMOTE_ADDR') );
	    $this->Session->session_notify($client_ip);
        
        require_once(CUSTOM_CONTROLS_PATH.'navi.php');
        new CNavi($this);
        
        $this->template_vars['PAGE_TITLE'] = "";
        $this->template_vars['PAGE_KEYWORDS'] = "";
		$this->template_vars['PAGE_DESCRIPTION'] = "";
        
		$this->template_vars['copyright_year'] = date('Y');
		
		$this->template_vars['last_modified'] = gmdate('D, d M Y 00:00:00', time() - 24*60*60) . ' GMT';
		
		$this->template_vars['is_debug_mode'] = ($DebugLevel == 255);
		
		$this->template_vars['user_edit'] = $this->Navi->getUri('/users/user_edit/', true);
		
		$this->tv['ROOT'] = ROOT;

		return true;
    }
    
    function on_install_module($module_name)
    {
        parent::on_install_module($module_name);
        include('app.setup.php');
        return true;
    }
}

function on_php_error($code, $message, $filename='', $linenumber=-1, $context=array()) {
    if (intval($code) != 2048)
    {
        //system_die('Error '.$code.' ('.$message.') occured in '.$filename.' at '.$linenumber.'');
    }
}
@ob_start();
@set_error_handler('on_php_error');
@session_start();
$GLOBALS['app'] = new CApp();
?>