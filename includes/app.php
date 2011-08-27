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


define('RECORDSET_FIRST_ITEM', '- Выберите из списка -');

define('TYPE_PRODUCT_IMAGE_PHOTO', 'photo');
define('TYPE_PRODUCT_IMAGE_INSTRUCTION', 'instruction');

if (file_exists( FUNCTION_PATH . 'functions.ram.debug.php'))
	require_once(FUNCTION_PATH . 'functions.ram.debug.php');

class CApp extends CApplication
{
    function CApp()
    {
    	//$this->locale = 'ru_RU';
    	//$this->codepage = 'CP1251';
        //setlocale(LC_ALL, ((!isset($_SERVER['WINDIR'])) ? sprintf('%1$s.%2$s', $this->locale, $this->codepage) : ''));

        parent::CApplication();

        //$this->Localizer->set_language(2);
        
        $this->Modules['Interface'] = array(
                'ClassName' => 'CInterface',
                'ClassPath' => CUSTOM_CLASSES_PATH . 'components/interface.php',
                'Visual'=>'0',
                'Title' => 'Interface'
        );
		$this->Modules['AjaxValidator'] = array(
				'ClassName' => 'CAjaxValidator',
				'ClassPath' => CUSTOM_CLASSES_PATH . 'components/ajaxvalidator.php',
				'Visual'=>'0',
				'Title' => 'AjaxValidator'
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
        $this->Modules['Images'] = array(
                'ClassName' => 'CImages',
                'ClassPath' => CUSTOM_CLASSES_PATH . 'components/images.php',
                'Visual'=>'0',
                'Title' => 'Images'
        );
        $this->Modules['Inputs'] = array(
                'ClassName' => 'CInputs',
                'ClassPath' => CUSTOM_CLASSES_PATH . 'components/inputs.php',
                'Visual'=>'0',
                'Title' => 'Inputs'
        );
        $this->Modules['Products'] = array(
                'ClassName' => 'CProducts',
                'ClassPath' => CUSTOM_CLASSES_PATH . 'components/products.php',
                'Visual'=>'0',
                'Title' => 'Products'
        );
        $this->Modules['Exhibitions'] = array(
                'ClassName' => 'CExhibitions',
                'ClassPath' => CUSTOM_CLASSES_PATH . 'components/exhibitions.php',
                'Visual'=>'0',
                'Title' => 'Exhibitions'
        );
   	}
    
    function on_page_init() {
        if (!parent::on_page_init())
                return false;
        global $DebugLevel;
        global $SiteName;

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
		$this->template_vars['site_name'] = $SiteName;
		$this->template_vars['ROOT'] = ROOT;

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