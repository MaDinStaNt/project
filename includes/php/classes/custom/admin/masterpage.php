<?php
require_once(BASE_CLASSES_PATH . 'adminpage.php');
define('FILTER_TEXT', 1);
define('FILTER_SELECT', 2);
define('FILTER_DATE', 3);

define('CONDITION_EQUAL', 1);
define('CONDITION_LIKE', 2);

class CMasterPage extends CAdminPage
{
    /**
     * Default navigator columns.
     *
     * @var array
     */
    protected $_columns_arr = array('title' => 'Title');

    /**
     * Default sort column.
     *
     * @var array
     */
    protected $_sort_column = 'title';
    
    /**
     * Default where condition.
     *
     * @var array
     */
    protected $_where = '1=1';
    
    /**
     * Default filters array.
     *
     * @var array
     */
    protected $_filters = array();

    function CMasterPage(&$app, $template)
	{
		parent::CAdminPage($app, $template);
		$this->DataBase = &$this->Application->DataBase;
		$this->Localizer = &$this->Application->Localizer;
		$this->User = &$this->Application->User;
		$this->template_vars = &$app->template_vars;
		if (!array_key_exists('Filtes', $_SESSION)) $_SESSION['Filtes'] = array();
		$this->Filtes = &$_SESSION['Filtes'];
	}
	
	function on_page_init()
	{
		require_once(CUSTOM_CONTROLS_PATH . 'simpledataoutput.php');
		new CSimpleDataOutput($this);
		require_once(BASE_CONTROLS_PATH . 'simplearrayoutput.php');
		new CSimpleArrayOutput();

		$this->tv['_table'] = $this->_table;
		
		require_once(CUSTOM_CONTROLS_PATH . 'adminfilters.php');
		new CAdminFiltersCtrl($app, $this->_filters);
	}
	
	function page_actions() {
		if (CForm::is_submit($this->_table, 'add')) {
			$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('./'.$this->_table.'_edit/', false));
		}

		if (CForm::is_submit($this->_table, 'delete_selected')) {
			$data = InGetPost("ch", array());
            $where="WHERE ".$this->_where;
            if (sizeof($data) > 0) {
            	$where .= " AND ";
                foreach($data as $k => $v) $where.="id = ".$v." OR ";
                $where = substr($where, 0 , -4);
                $sql = 'DELETE FROM %prefix%'.$this->_table.' ' . $where;
                if($this->Application->DataBase->select_custom_sql($sql)) {
                	$this->tv['_info'][] = $this->Application->Localizer->get_string('objects_deleted');
                } 
                else $this->tv['_errors'][] = $this->Application->Localizer->get_string('internal_error');
           } 
           else $this->tv['_info'][] = $this->Application->Localizer->get_string('noitem_selected');
		}
	}
	
	function page_filters() {
		if (CForm::is_submit($this->_table, 'clear')) {
			foreach ($this->_filters as $name => $filter) {
				if ($filter['type'] == FILTER_DATE) {
					$this->tv[$name.'_from'] = '';
					$this->tv[$name.'_to'] = '';
				}
				else {
					$this->tv[$name] = '';
				}
			}
			$this->Filtes[$this->_table] = array();
		}
			
		if (CForm::is_submit($this->_table)) {
			if (CValidator::validate_input()) {
				$this->Filtes[$this->_table] = array();
				foreach ($this->_filters as $name => $filter) {
					if (array_key_exists($name.'_from', $this->tv)&&(strlen($this->tv[$name.'_from']) > 0)) {
						$date_arr = explode("/", $this->tv[$name.'_from']);
						$this->_where .= " AND ".str_replace("#", ".", $name)." >= '".$date_arr[2]."-".$date_arr[1]."-".$date_arr[0]."'";
						$this->Filtes[$this->_table][$name]['from'] = $date_arr[2]."-".$date_arr[1]."-".$date_arr[0];
					}
					if (array_key_exists($name.'_to', $this->tv)&&(strlen($this->tv[$name.'_to']) > 0)) {
						$date_arr = explode("/", $this->tv[$name.'_to']);
						$this->_where .= " AND ".str_replace("#", ".", $name)." <= '".$date_arr[2]."-".$date_arr[1]."-".$date_arr[0]."'";
						$this->Filtes[$this->_table][$name]['to'] = $date_arr[2]."-".$date_arr[1]."-".$date_arr[0];
					}
					if (isset($this->tv[$name]) && strlen($this->tv[$name]) > 0) {
						if ($filter['condition'] == CONDITION_EQUAL) {
							$this->_where .= " AND ".str_replace("#", ".", $name)." = '".$this->tv[$name]."'";
						}
						elseif ($filter['condition'] == CONDITION_LIKE) {
							$this->_where .= " AND ".str_replace("#", ".", $name)." LIKE '".$this->tv[$name]."%'";
						}
						$this->Filtes[$this->_table][$name] = $this->tv[$name];
					}
				}
			}
		}
        //filters
		if (isset($this->Filtes[$this->_table])) {
			foreach ($this->Filtes[$this->_table] as $name => $value) {
				foreach ($this->_filters as $fname => $filter) {
					if ($fname == $name) {
						if (is_array($value)) {
							if (strlen($value['from']) > 0) {
								$this->_where .= " AND ".str_replace("#", ".", $name)." >= '".$value['from']."'";
							}
							if (strlen($value['to']) > 0) {
								$this->_where .= " AND ".str_replace("#", ".", $name)." <= '".$value['to']."'";
							}
						}
						elseif ($filter['condition'] == CONDITION_EQUAL) {
							$this->_where .= " AND ".str_replace("#", ".", $name)." = '".$value."'";
						}
						elseif ($filter['condition'] == CONDITION_LIKE) {
							$this->_where .= " AND ".str_replace("#", ".", $name)." LIKE '".$value."%'";
						}
					}
				}
			}
		}
	}

	function bind_data()
	{
        $sort_arr = array();
        $column_width = ceil(100 / sizeof($this->_columns_arr));
        $query = "SELECT id, ";

        foreach ($this->_columns_arr as $column => $title) {
        	$sort_arr[$column] = $column;
        	$query .= $column . ", ";
        }
        $query = substr($query, 0, strlen($query) - 2);
        $query .= " FROM %prefix%".$this->_table." WHERE 1 = 1";
        
        require_once(BASE_CLASSES_PATH . 'controls/navigator.php'); // base application class
        $nav = new Navigator('objects', $query, $sort_arr, $this->_sort_column, $this->Application);
        
        foreach ($this->_columns_arr as $column => $title) {
	        $header_num = $nav->add_header($title, $column);
	        $nav->headers[$header_num]->no_escape = true;
	        $nav->headers[$header_num]->align = "left";
	        $nav->set_width($header_num, $column_width.'%');
        }

        $this->tv['clickLink'] = $this->Application->Navi->getUri('./'.$this->_table.'_edit/', true);

        if ($nav->size > 0)
            $this->template_vars[$this->_table.'_show_remove'] = true;
        else
            $this->template_vars[$this->_table.'_show_remove'] = false;
	}
}

?>