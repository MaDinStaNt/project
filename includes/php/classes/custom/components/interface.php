<?
class CInterface
{
    var $Application;
    var $DataBase;
    var $tv;
    var $last_error;

    function CInterface(&$app)
    {
        $this->Application = &$app;
        $this->tv = &$app->template_vars;
        $this->DataBase = &$this->Application->DataBase;
		if (!array_key_exists('PanelData', $_SESSION)) $_SESSION['PanelData'] = array();
		$this->PanelData = &$_SESSION['PanelData'];
    }

    function get_last_error()
    {
        return $this->last_error;
    }


	function set_panel_show() { 
		$this->PanelData['is_panel_show'] = true;
	}

	function set_panel_hide() { 
		$this->PanelData['is_panel_show'] = false;
	}

	function set_filter_panel_show() { 
		$this->PanelData['is_filter_panel_show'] = true;
	}

	function set_filter_panel_hide() { 
		$this->PanelData['is_filter_panel_show'] = false;
	}
};
?>