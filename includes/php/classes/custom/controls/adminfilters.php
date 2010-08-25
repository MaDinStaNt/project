<?

class CAdminFiltersCtrl extends CTemplateControl
{
    var $tv;
    var $structure_id;
    var $_filters;

    function CAdminFiltersCtrl(&$html_page, $filters)
    {
        parent::CTemplateControl('AdminFilters');
        $this->DataBase = &$this->Application->DataBase;
        $this->tv = &$this->Application->template_vars;
        $this->_filters = $filters;
		if (!array_key_exists('Filtes', $_SESSION)) $_SESSION['Filtes'] = array();
		$this->Filtes = &$_SESSION['Filtes'];
    }

    function process()
    {
		//filters
		if (isset($this->_filters) && (sizeof($this->_filters) > 0)) {
			foreach ($this->_filters as $name => $filter) {
				$this->Application->template_vars['filter_name'][] = $name;
				$this->Application->template_vars['filter_type_text'][] = ($filter['type'] == FILTER_TEXT);
				$this->Application->template_vars['filter_type_select'][] = ($filter['type'] == FILTER_SELECT);
				$this->Application->template_vars['filter_type_date'][] = ($filter['type'] == FILTER_DATE);
				$this->Application->template_vars['filter_title'][] = $filter['title'];
				if (!is_null($filter['data'])) {
					CInput::set_select_data($name, $filter['data'][0], $filter['data'][1], $filter['data'][2]);
				}
			}
			$this->Application->template_vars['filter_cnt'] = sizeof($this->Application->template_vars['filter_name']);
		}
		if (isset($this->Filtes[$this->tv['_table']])) {
			foreach ($this->Filtes[$this->tv['_table']] as $name => $value) {
				if (is_array($value)) {
					if (strlen($value['from']) > 0) {
						$date_arr = explode("-", $value['from']);
						$this->Application->template_vars[$name.'_from'] = $date_arr[2]."/".$date_arr[1]."/".$date_arr[0];
					}
					if (strlen($value['to']) > 0) {
						$date_arr = explode("-", $value['to']);
						$this->Application->template_vars[$name.'_to'] = $date_arr[2]."/".$date_arr[1]."/".$date_arr[0];
					}
				}
				else {
					$this->Application->template_vars[$name] = $value;
				}
			}
		}
    	return CTemplate::parse_file(CUSTOM_CONTROLS_TEMPLATE_PATH.'adminfilters.tpl', $this->tv);
    }
}
?>