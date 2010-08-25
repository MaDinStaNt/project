<?
if (!defined('NAV_PAGE_SIZE')) define('NAV_PAGE_SIZE', 10); // Default size (in rows)
if (!defined('NAV_TEMPLATE_FILE')) define('NAV_TEMPLATE_FILE', 'navigator.tpl'); // Template file
if (!defined('NAV_DEFAULT_EMPTY_MESSAGE')) define('NAV_DEFAULT_EMPTY_MESSAGE', 'There are currently no records to be shown');

/**
 * @package LLA.Base
 */
class CNavigatorHeader {
        var $name;
        var $sort_var;

        var $width;
        var $length;

        var $replacement;

        var $func;

        var $wrap;
        var $hidden;
        var $mail;
        var $is_price;
        var $align;
        var $clickable;

        var $edit_name;
        var $edit_size;
        var $edit_max;

        var $no_escape = false;

        function CNavigatorHeader($link_name, $sort_var){
                $this->wrap = false;
                $this->mail = false;
                $this->is_price = false;
                $this->clickable = true;
                if (is_null($link_name)) $this->hidden = true;
                else {
                        if (!is_null($sort_var)) $this->sort_var = strval($sort_var);
                        $this->name = $link_name;
                        $this->hidden = false;
                }
                $this->align = 'left';
                $this->valign = 'middle';
        }

        /**
         * set to true to normalize price values
         */
        function set_price($price = true)
        {
                $this->is_price = $price;
                if ($price)
                        $this->func = 'normalize_price';
                else
                        unset($this->func);
        }

        function set_width($width){
                $this->width = strval($width);
        }

        function set_length($len){
                if (!is_numeric($len)) system_die('Invalid argument', 'Header_link->set_length');
                $this->length = $len;
        }

        function set_replacement(&$rep_list){
                $this->replacement = &$rep_list;
        }

        function set_function($name){
                $this->func = $name;
        }

        function set_wrap($wrap = false){
                $this->wrap = !$wrap;
        }

        function set_visibility($val = false){
                $this->hidden = !$val;
        }

        function set_mail($mail = true){
                $this->mail = (bool)$mail;
                $this->clickable = !$this->mail;
        }

        function set_click($clickable = true){
                $this->clickable = (bool)$clickable;
        }

        function set_valign($val){
                if (!in_array($val, array('top', 'middle', 'bottom'))) system_die('Invalid align mode', 'Header_link->set_align');
                $this->valign = $val;
        }
        function set_align($val){
                if (!in_array($val, array('left', 'center', 'right'))) system_die('Invalid align mode', 'Header_link->set_align');
                $this->align = $val;
        }

        function set_edit($name, $size, $max){
                $this->edit_name = $name;
                $this->edit_size = $size;
                $this->edit_max = $max;
                $this->clickable = false;
        }
}
/**
 * @package LLA.Base
 */
class Navigator extends CTemplateControl {

        var $title; // the text of the navigator's title
        var $query; // mysql query of navigator
        var $size; // size of mySQL result for current page in rows
        var $max_size; // size of navigator's page in rows
        var $select_result; //the result resource
        var $headers; // the collection of headers
        var $page; // the number of current page
        var $page_size; // the size of navigator's page in rows
        var $max_page; // the size of navigator's page in rows
        var $click_link; // the link for clicking
        var $check_name; // the name of checkbox
        var $disabled_list; // array with disabled indexes
        var $checked_list;
        var $enumerated; // enumeration flag
        var $empty; // empty flag
        var $popuped; // popuped flag
        var $hl_fields; // highlighted fields
        var $hl_values; // the values for highlight
        var $hl_colors; // the colors for highlight
        var $sort_vars;
        var $sort_key;
        var $sort_mode;
        var $enumerated_valign = 'middle';
        var $enumerated_align = 'center';

        var $empty_message;
        var $template;

        var $var_names;
        var $additional_vars = array();

        var $id_name;

/*
--------------------------------------------------------------------------------

Navigator(sql_query[, sort_vars[, default_sort_key[, default_sort_mode]])

        default_sort_mode: true - asc, false - desc

string sql_query - sql query WITH "id" field and WITHOUT "ORDER BY", prefix should be marked as %prefix%
array sort_vars - array of sorting values (name_in_get => value_for_inserting_in_sql)

--------------------------------------------------------------------------------
*/

        function Navigator($object_id, $query, $sort_vars = array(), $default_sort_key = null, $default_sort_mode = true, $default_page_size = -1, $colorize = true, $is_second_db = false){
                parent::CTemplateControl('dbnavigator', $object_id);

                $this->headers = array();
                $this->query = $query;

                $this->id_name = 'id';

                $cache_id = $this->mark_var('data');

                $this->hl_fields = array();
                $this->hl_values = array();
                $this->hl_colors = array();

                $this->checked_list = null;
                $this->disabled_list = null;

                $this->sort_vars = $sort_vars;

                $this->page_size = $default_page_size;;
                $this->enumerated = true;
                
                $this->colorize = $colorize;
				if ($is_second_db) {
                	$result = $this->Application->DataBase2->select_custom_sql($this->query) or system_die('Invalid query - "' . $this->query . '"', 'Navigator');
				}
                else {
                	$result = $this->Application->DataBase->select_custom_sql($this->query) or system_die('Invalid query - "' . $this->query . '"', 'Navigator');
                }
                $this->max_size = $result->get_record_count();
                if ($this->page_size == -1)
                        $this->page_size = NAV_PAGE_SIZE;
                if ($this->page_size == -2)
                        $this->page_size = $this->max_size;

                $this->Application->template_vars[$object_id.'_navigator_empty'] = ($this->max_size==0);
                //$this->Application->DataBase->free($result);

                $this->template = new CTemplate();
                $this->template->vars = $GLOBALS['app']->template_vars;
                $this->template->load_file(BASE_CONTROLS_TEMPLATE_PATH.NAV_TEMPLATE_FILE);

                if ($this->max_size > 0)
                {
                        $this->empty = false;
                        $this->max_page = ceil($this->max_size / $this->page_size);

                        $this->var_names = array();

                        $this->var_names['page'] = $this->mark_var('page');
                        $this->var_names['sort'] = $this->mark_var('sort');
                        $this->var_names['sortmode'] = $this->mark_var('sortmode');

                        $page = InPostGetCache($this->var_names['page'], 1, $cache_id);
                        if ($page) {
                                if (is_index($page)){
                                        $this->page = $page;
                                        if (!$this->empty && ($this->page > $this->max_page)) $this->page = 1;
                                } else $this->page = 1;
                        } else $this->page = 1;

                        $sort = InPostGetCache($this->var_names['sort'], '', $cache_id);
                        if ($sort){

                                $sort_key = $sort;

                                if (array_key_exists($sort_key, $this->sort_vars)) $this->sort_key = $sort_key;
                                elseif (!is_null($default_sort_key)) $this->sort_key = $default_sort_key;

                                $sortmode = InPostGetCache($this->var_names['sortmode'], '', $cache_id);

                                if ($sortmode){
                                        if ($sortmode == 'desc') $this->sort_mode = false;
                                        elseif ($sortmode == 'asc') $this->sort_mode = true;
                                        else $this->sort_mode = true;
                                } else $this->sort_mode = true;
                        } elseif (!is_null($default_sort_key)) {
                                $this->sort_key = $default_sort_key;
                                $this->sort_mode = $default_sort_mode;
                        }

                        if (!is_null($this->sort_key)) {
                                if (isset($this->sort_vars[$this->sort_key]))
                                {
                                        $this->query .= ' ORDER BY '.$this->sort_vars[$this->sort_key];
                                        if (!$this->sort_mode) $this->query .= ' DESC';
                                }
                        }
						if ($is_second_db) {
	                        $this->query = $this->Application->DataBase2->_set_limit($this->query, array($this->page, $this->page_size));
						}
						else {
	                        $this->query = $this->Application->DataBase->_set_limit($this->query, array($this->page, $this->page_size));
						}

						if ($is_second_db) {
	                        $this->select_result = $this->Application->DataBase2->internalQuery($this->query);
						}
						else {
	                        $this->select_result = $this->Application->DataBase->internalQuery($this->query);
						}
                        if (!$this->select_result)
                                return;
                        if ($is_second_db) {
                        	$this->size = $this->Application->DataBase2->internalNumRows($this->select_result);
                        }
                        else {
                        	$this->size = $this->Application->DataBase->internalNumRows($this->select_result);
                        }

                        if ($this->sort_mode) $sort_mode = 'asc'; else $sort_mode = 'desc';

                        SetCacheVar($this->var_names['sortmode'], $sort_mode, $cache_id);
                        SetCacheVar($this->var_names['sort'], $this->sort_key, $cache_id);
                        SetCacheVar($this->var_names['page'], $this->page, $cache_id);

                } else $this->empty = true;
        }

        function get_unchecked($check_name = 'ch')
        {
                $checked_fields = InPostGet($check_name, array());
                $all_fields = InPostGet($check_name.'2', array());
                if (!is_array($all_fields)) $all_fields = array();
                if (!is_array($checked_fields)) $checked_fields = array();
                return array_diff($all_fields, $checked_fields);
        }

        function set_add_get_var($name, $value)
        {
                $this->additional_vars[$name] = $value;
        }

        function set_valign($val){
                if (!in_array($val, array('top', 'middle', 'bottom'))) system_die('Invalid align mode', 'Header_link->set_align');
                $this->enumerated_valign = $val;
        }
        function set_align($val){
                if (!in_array($val, array('left', 'center', 'right'))) system_die('Invalid align mode', 'Header_link->set_align');
                $this->enumerated_align = $val;
        }

        function set_click($link, $popuped = false){
                $this->click_link = $link;
                $this->popuped = (bool) $popuped;
        }

        function set_page_size($size = 0){
                $this->page_size = intval($size);
        }

        function reset_page(){
                SetCacheVar( $this->var_names['page'], 1, $this->mark_var('data') );
        }

        function set_check($val = true, $name = 'ch'){
                if ($val) $this->check_name = $name;
                else $this->check_name = null;
        }

        function set_enumeration($val = true){
                $this->enumerated = (bool) $val;
        }

        function set_highlight($field_name, $field_value){
                $last = array_push($this->hl_fields, $field_name) - 1;
                $this->hl_values[$last] = $field_value;
                if (func_num_args() > 2) $this->hl_colors[$last] = func_get_arg(2);
        }

        function add_header($sort_name, $sort_var){
                $res = array_push($this->headers, new CNavigatorHeader($sort_name, $sort_var));
                return ($res - 1);
        }

        function set_width($num, $width){
                $this->headers[$num]->set_width($width);
        }

        function set_title($val){
                $this->title = $val;
        }

        function set_page($page){
                if (!is_index($page)) system_die('Invalid arguments', 'Navigator->set_page');
                $this->page = func_get_arg(0);
        }

        function set_pager_link($val){
                $this->pager_link = $val;
        }

        function set_empty_message($val){
                $this->empty_message = $val;
        }

        function set_disabled_list(&$dis_list){
                $this->disabled_list = &$dis_list;
        }

        function set_checked_list(&$ch_list) {
                $this->checked_list = &$ch_list;
        }

        function in_headers($name){
                if (array_key_exists($name, $this->headers));
        }

        function &get_header($name){
                return $this->headers[$name];
        }

        function on_page_init()
        {
                if (parent::in_input_vars('title')) $this->set_title(parent::get_input_var('title'));
                if (parent::in_input_vars('enumerated')) $this->set_enumeration(str_to_bool(parent::get_input_var('enumerated')));
                if (parent::in_input_vars('checkable')) $this->set_check(str_to_bool(parent::get_input_var('checkable')), $this->get_input_var('check_name', 'ch'));
                if (parent::in_input_vars('clicklink')){
                        $popuped = (parent::in_input_vars('popuped')) ? str_to_bool(parent::get_input_var('popuped')) : false;
                        $this->set_click(parent::get_input_var('clicklink'), $popuped);
                }
                if (!$this->empty) {

                        $this->template->set_var('object_name', $this->object_name);
                        $this->template->set_var('object_id', $this->object_id);

                        $this->template->set_var('is_empty', false);
                        if ($is_second_db) {
	                        $fields_num = $this->Application->DataBase2->internalNumFields($this->select_result);
                        }
                        else {
                        	$fields_num = $this->Application->DataBase->internalNumFields($this->select_result);
                        }

                        $real_field_num = $fields_num;
                        if ($this->enumerated) $real_field_num++;
                        if (!is_null($this->click_link) && is_null($this->check_name)) $real_field_num--;

                        $row_count = 0;
                        if (!is_null($this->page)) {
                                $page_group = ceil($this->page / 10);
                                $start_page = ($page_group - 1) * 10 + 1;
                                $end_page = $start_page + 9;
                                if ($end_page > $this->max_page) {
                                        $new_start = $start_page - $end_page + $this->max_page;
                                        if ($new_start > 0) $start_page = $new_start;
                                        $end_page = $this->max_page;
                                }
                        }

                        $this->template->set_var('real_field_num', $real_field_num);

                        if (!is_null($this->title)) $this->template->set_var('title', $this->title);
                        else $this->template->set_var('title', '');

                        $this->template->set_var('max_size', $this->max_size);

                        if (!is_null($this->page) && ($this->max_page > 1)) {

                        $new_get_vars = $this->additional_vars;
                        if (!is_null($this->sort_key)){
                                $new_get_vars[$this->var_names['sort']] = $this->sort_key;
                                if (!$this->sort_mode) $new_get_vars[$this->var_names['sortmode']] = 'desc';
                        }

                        $this->template->set_var('have_pages', true);
                        $link = (isset($this->pager_link)) ? $this->pager_link : '';

                        $req_uri_arr = explode('?', $_SERVER['REQUEST_URI']);
                        $req_uri = substr($req_uri_arr[0], 1, strlen($req_uri_arr[0]));
                        if ($this->page > 1) {
                                $this->template->set_var('prev_pages', true);
                                $new_get_vars[$this->var_names['page']] = '1';
                                $this->template->set_var('first_link', get_url($req_uri, $new_get_vars));
                                $new_get_vars[$this->var_names['page']] = strval($this->page - 1);
                                $this->template->set_var('prev_link', get_url($req_uri, $new_get_vars));
                        } else $this->template->set_var('prev_pages', false);

                        $c = $start_page;
                        $tpl_pages = array();
                        $tpl_is_currents = array();
                        $tpl_links = array();

                        while (($c < $start_page + 10) && ($c <= $this->max_page)) {
                                $curr_item = $c - $start_page;
                                $this->template->set_var('page', $c, $curr_item);
                                $this->template->set_var('page_is_current', ($c == $this->page) ? true : false, $curr_item);
                                $new_get_vars[$this->var_names['page']] = strval($c);
                                $this->template->set_var('page_link', get_url($req_uri, $new_get_vars), $curr_item);
                                $c++;
                        }

                        $this->template->set_var('page_count', $c - $start_page);

                        if ($this->page < $this->max_page) {
                                        $this->template->set_var('next_pages', true);
                                $new_get_vars[$this->var_names['page']] = strval($this->page + 1);
                                $this->template->set_var('next_link', get_url($req_uri, $new_get_vars));
                                $new_get_vars[$this->var_names['page']] = strval($this->max_page);
                                $this->template->set_var('last_link', get_url($req_uri, $new_get_vars));
                        } else $this->template->set_var('next_pages', false);
                } else $this->template->set_var('have_pages', false);

// Navigator header out --------------------------------------------------------

                if (!is_null($this->check_name)) {
                        $this->template->set_var('checkable', true);
                        $this->template->set_var('check_name', $this->check_name);
                } else $this->template->set_var('checkable', false);

                if ($this->enumerated) {
                        $this->template->set_var('enumerated', true);
                        $this->template->set_var('enumerated_valign', $this->enumerated_valign);
                        $this->template->set_var('enumerated_align', $this->enumerated_align);
                }

                $count = 0;
                $tpl_count = 0;

                if ( ($this->enumerated) || (!is_null($this->check_name)) )
                {
                        $this->template->set_var('header_css_class', '');
                        $this->template->set_var('field_css_class', '');
                }
                else
                {
                        $this->template->vars['header_css_class'] = array();
                        $this->template->vars['field_css_class'] = array();
                }

                foreach ($this->headers as $cur_link) {
                        if (!$cur_link->hidden)
                        {
                                if (is_array($this->template->vars['header_css_class']))
                                        $this->template->vars['header_css_class'][$tpl_count] = ( ($tpl_count==0)?('left_border'):('') );
                                if (!is_null($cur_link->width)) {
                                        $this->template->set_var('header_have_width', true, $tpl_count);
                                        $this->template->set_var('header_width', $cur_link->width , $tpl_count);
                                        $this->template->set_var('header_align', $cur_link->align , $tpl_count);
                                } else $this->template->set_var('header_have_width', false, $tpl_count);

                                if (array_key_exists($cur_link->sort_var, $this->sort_vars)) {
                                        $this->template->set_var('header_is_link', true, $tpl_count);
                                        $new_get_vars = $this->additional_vars;

                                        if (!is_null($this->sort_key) && ($cur_link->sort_var == $this->sort_key)){

                                                if ($this->sort_mode){
                                                        $new_get_vars[$this->var_names['sortmode']] = 'desc';
                                                        $this->template->set_var('header_sort', 'up', $tpl_count);
                                                } else {
                                                        $new_get_vars[$this->var_names['sortmode']] = 'asc';
                                                        $this->template->set_var('header_sort', 'down', $tpl_count);
                                                }

                                        } else {
                                                $new_get_vars[$this->var_names['sortmode']] = 'asc';
                                                $this->template->set_var('header_sort', false, $tpl_count);
                                        }

                                        $new_get_vars[$this->var_names['sort']] = $cur_link->sort_var;
                                        $new_get_vars[$this->var_names['page']] = 1;
                                        $req_uri_arr = explode('?', $_SERVER['REQUEST_URI']);
                                        $req_uri = substr($req_uri_arr[0], 1, strlen($req_uri_arr[0]));
                                        $link = get_url($req_uri, $new_get_vars);
                                        $this->template->set_var('header_link', $link, $tpl_count);

                                } else $this->template->set_var('header_is_link', false, $tpl_count);

                                $this->template->set_var('header_name', $cur_link->name, $tpl_count);
                                $tpl_count++;
                        }
                        $count++;
                }
                if (!is_null($this->click_link) || !is_null($this->check_name)) $count++;
				
                if ($is_second_db) {
	                $field_obj = $this->Application->DataBase2->internalFetchField($this->select_result, 0);
                }
                else {
                	$field_obj = $this->Application->DataBase->internalFetchField($this->select_result, 0);
                }

                if (is_null($this->click_link) && is_null($this->check_name) && ($field_obj->name == $this->id_name) ) $count++;

                while ($count < $fields_num) {
                        $this->template->set_var('header_name', 'undefined', $tpl_count);
                        $this->template->set_var('header_have_width', false, $tpl_count);
                        $this->template->set_var('header_is_link', false, $tpl_count);
                        $count++;
                        $tpl_count++;
                }

                $this->template->set_var('header_count', $tpl_count);

// Navigator body out ----------------------------------------------------------

                $color_flag = true;

                if (!is_null($this->check_name)) {
                        $this->template->set_var('check_name', $this->check_name);
                        $this->template->set_var('checkable', true);
                } else $this->template->set_var('checkable', false);

                if ($is_second_db) {
	                $field_obj = $this->Application->DataBase2->internalFetchField($this->select_result, 0);
                }
                else {
	                $field_obj = $this->Application->DataBase->internalFetchField($this->select_result, 0);
                }
                if ($field_obj->name == $this->id_name)
                        $start_field = 1;
                else
                        $start_field = 0;

                $this->template->set_var('enumerated', $this->enumerated);
                $this->template->set_var('hover_script', $this->click_link);

                $this->template->vars['field_hover_script'] = array();
                $this->template->vars['field_click'] = array();
                $this->template->vars['field_nowrap'] = array();
                $this->template->vars['field_is_editbox'] = array();
                $this->template->vars['field_editname'] = array();
                $this->template->vars['field_editsize'] = array();
                $this->template->vars['field_editmax'] = array();
                $this->template->vars['valign'] = array();
                $this->template->vars['align'] = array();

                $this->template->vars['field_val'] = array();

                $field_count = 0;

                //mysql_data_seek($this->select_result, 0);
                $cnt = 0;
                if ($is_second_db) {
	                while ($cnt++ < $this->Application->DataBase2->_int_data_seek) $this->Application->DataBase2->internalFetchAssoc($this->select_result);
                }
                else {
	                while ($cnt++ < $this->Application->DataBase->_int_data_seek) $this->Application->DataBase->internalFetchAssoc($this->select_result);
                }

                while ($row = (($is_second_db) ? ($this->Application->DataBase2->internalFetchArray($this->select_result)) : ($this->Application->DataBase->internalFetchArray($this->select_result)))) {
                        $field_count = 0;
                        $hl_flag = false;
                        unset($hl_color);

                        foreach ($this->hl_fields as $key => $field) {
                                if ($row[$field] == $this->hl_values[$key]) {
                                        $hl_flag = true;
                                        if (array_key_exists($key, $this->hl_colors)) $hl_color = $this->hl_colors[$key];
                                        break;
                                }
                        }

                        if ($this->colorize)
                        {
	                        if ($hl_flag && !isset($hl_color))
	                        {
	                            if ($color_flag)
	                                $this->template->set_var('row_class', 'nav_t_r1 hl', $row_count);
	                            else
	                                $this->template->set_var('row_class', 'nav_t_r2 hl', $row_count);
	                        }
	                        elseif (!$hl_flag && $color_flag)
	                            $this->template->set_var('row_class', 'nav_t_r1', $row_count);
	                        elseif (!$hl_flag)
	                            $this->template->set_var('row_class', 'nav_t_r2', $row_count);
                        }
                        else 
                        {
                        	$this->template->set_var('row_class', 'nav_t_r1 hl', $row_count);
                        	$this->template->set_var('row_class', 'nav_t_r1', $row_count);
                        }

                        if (isset($hl_color)) $this->template->set_var('row_style', ' style="background-color:' . $hl_color . '" ', $row_count);
                        else $this->template->set_var('row_style', '', $row_count);

                        $temp_str = '';

                        if (!is_null($this->click_link)) {
                                $cur_id = $row[$this->id_name];
                                $cr_link = str_replace('%value%', $cur_id, $this->click_link);
                                if (stristr($cr_link, 'JavaScript')) $this->template->set_var('row_click', $cr_link, $row_count);
                                else $this->template->set_var('row_click', ($this->popuped ? 'popup' : 'goto') . "URL('" . htmlspecialchars($this->click_link) . $cur_id . "');", $row_count);
                        }

                        if (!is_null($this->check_name)) {
                                $cur_id = $row[$this->id_name];
                                $this->template->set_var('row_check_val', $cur_id, $row_count);
                                if (!is_null($this->disabled_list) && in_array($cur_id, $this->disabled_list)) $this->template->set_var('row_check_disabled', ' disabled="disabled"', $row_count);
                                else $this->template->set_var('row_check_disabled', '', $row_count);

                                if (!is_null($this->checked_list))
                                        $this->template->set_var('row_check_checked', (in_array($cur_id, $this->checked_list))?' checked="checked"':'', $row_count);
                                else
                                        $this->template->set_var('row_check_checked', '', $row_count);
                        }

                        if ($this->enumerated) $this->template->set_var('row_number', strval($row_count + $this->page_size * ($this->page - 1) + 1), $row_count);

                        $this->template->vars['field_hover_script'][$row_count] = array();
                        $this->template->vars['field_click'][$row_count] = array();
                        $this->template->vars['field_nowrap'][$row_count] = array();
                        $this->template->vars['field_is_editbox'][$row_count] = array();
                        $this->template->vars['field_editname'][$row_count] = array();
                        $this->template->vars['field_editsize'][$row_count] = array();
                        $this->template->vars['field_editmax'][$row_count] = array();

                        $this->template->vars['field_val'][$row_count] = array();

                        $this->template->vars['valign'][$row_count] = array();
                        $this->template->vars['align'][$row_count] = array();

                        if (is_array($this->template->vars['field_css_class']))
                                $this->template->vars['field_css_class'][$row_count] = array();

                        $real_field_count = 0;

                        for($count = $start_field; $count < $fields_num; $count++) {
                                if (!isset($this->headers[$field_count]) || !$this->headers[$field_count]->hidden) {
                                        if (is_array($this->template->vars['field_css_class']))
                                                $this->template->vars['field_css_class'][$row_count][$real_field_count] = ( ($real_field_count==0)?('left_border'):('') );

                                        if (!is_null($this->click_link) && (!isset($this->headers[$field_count]) || $this->headers[$field_count]->clickable)){
                                                $this->template->vars['field_hover_script'][$row_count][$real_field_count] = true;
                                                $cr_link = str_replace('%value%', $cur_id, $this->click_link);
                                                if (stristr($cr_link, 'JavaScript')) $this->template->vars['field_click'][$row_count][$real_field_count] = $cr_link;
                                                else $this->template->vars['field_click'][$row_count][$real_field_count] = ($this->popuped ? 'popup' : 'goto') . "URL('" . htmlspecialchars($this->click_link) . $cur_id . "');";
                                        } else $this->template->vars['field_hover_script'][$row_count][$real_field_count] = false;
                                        if (!isset($this->headers[$field_count]) || $this->headers[$field_count]->wrap) $this->template->vars['field_nowrap'][$row_count][$real_field_count] = '';
                                        else $this->template->vars['field_nowrap'][$row_count][$real_field_count] = ' nowrap="nowrap"';
                                        if (isset($this->headers[$field_count]) && $this->headers[$field_count]->mail) {
// is email
                                                $this->template->vars['field_val'][$row_count][$real_field_count] = '<a href="mailto:'.htmlspecialchars($row[$count]).'">'.htmlspecialchars($row[$count]).'</a>';
                                        } else {
                                                if (isset($this->headers[$field_count]) && !is_null($this->headers[$field_count]->edit_name)) {
// is editbox
                                                        $this->template->vars['field_is_editbox'][$row_count][$real_field_count] = true;
                                                        $this->template->vars['field_val'][$row_count][$real_field_count] = htmlspecialchars($row[$count]);
                                                        $this->template->vars['field_editname'][$row_count][$real_field_count] = $this->headers[$field_count]->edit_name . $cur_id;
                                                        $this->template->vars['field_editsize'][$row_count][$real_field_count] = $this->headers[$field_count]->edit_size;
                                                        $this->template->vars['field_editmax'][$row_count][$real_field_count] = $this->headers[$field_count]->edit_max;
                                                } else {
                                                        $this->template->vars['field_is_editbox'][$row_count][$real_field_count] = false;
                                                        if (isset($this->headers[$field_count]) && (!is_null($this->headers[$field_count]->func))) {
// have function
                                                                if ($this->headers[$field_count]->no_escape)
                                                                        $this->template->vars['field_val'][$row_count][$real_field_count] = (call_user_func($this->headers[$field_count]->func, $row[$count], $row));
                                                                else
                                                                        $this->template->vars['field_val'][$row_count][$real_field_count] = htmlspecialchars(call_user_func($this->headers[$field_count]->func, $row[$count], $row));
                                                        } elseif (strlen($row[$count]) == 0) {
// is empty
                                                                        $this->template->vars['field_val'][$row_count][$real_field_count] = '&nbsp;';
                                                        } elseif (isset($this->headers[$field_count]) && (!is_null($this->headers[$field_count]->replacement))) {
// have replacement
                                                                $this->template->vars['field_val'][$row_count][$real_field_count] = htmlspecialchars($this->headers[$field_count]->replacement[$row[$count]]);
                                                        } else {
// simple one
                                                                $res = trim($row[$count]);
                                                                if (isset($this->headers[$field_count]) && !is_null($this->headers[$field_count]->length)) $res = wordwrap($res, $this->headers[$field_count]->length, "\n", 1);
                                                                if (isset($this->headers[$field_count]) && $this->headers[$field_count]->no_escape)
                                                                        $this->template->vars['field_val'][$row_count][$real_field_count] = $res;
                                                                else
                                                                        $this->template->vars['field_val'][$row_count][$real_field_count] = nl2br(htmlspecialchars($res));
                                                        }
                                                }
                                        }
                                        $this->template->vars['field_val'][$row_count][$real_field_count] = ( ($this->headers[$field_count]->is_price)?('$'):('') ) . $this->template->vars['field_val'][$row_count][$real_field_count];
                                        $this->template->vars['valign'][$row_count][$real_field_count] = $this->headers[$field_count]->valign;
                                        $this->template->vars['align'][$row_count][$real_field_count] = $this->headers[$field_count]->align;
                                        $real_field_count++;
                                }
                                $field_count++;
                        }
                        $row_count++;
                        $color_flag = !$color_flag;
                }

                $this->template->set_var('field_count', $tpl_count);
                $this->template->set_var('row_count', $row_count);

                } else {
                        $this->template->set_var('is_empty', true);
                        if (!is_null($this->empty_message)) $this->template->set_var('empty_message', $this->empty_message);
                        else $this->template->set_var('empty_message', NAV_DEFAULT_EMPTY_MESSAGE);
                }
        }

        function process(){
                $this->template->parse();
                return $this->template->result;
        }
}
?>