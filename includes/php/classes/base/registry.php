<?
define('KEY_TYPE_MEMO', 1);
define('KEY_TYPE_TEXT', 2);
define('KEY_TYPE_FILE', 3);
define('KEY_TYPE_IMAGE', 7);
define('KEY_TYPE_CHECKBOX', 4);
define('KEY_TYPE_HTML', 5);
define('KEY_TYPE_SELECT', 6);

define('GROUP_RIGHT_ADD_GROUP', 2); // add sub-group in group
define('GROUP_RIGHT_EDIT_GROUP', 1); // edit group title
define('GROUP_RIGHT_DELETE_GROUP', 16); // delete entire group

define('GROUP_RIGHT_ADD_VALUE', 8); // add values in group
define('GROUP_RIGHT_EDIT_VALUE', 4); // edit values in group
define('GROUP_RIGHT_DELETE_VALUE', 32); // delete values in group

define('VALUE_RIGHT_EDIT_VALUE', 1); // edit value
define('VALUE_RIGHT_DELETE_VALUE', 2); // delete value

class CRegistrySearchParam {
        var $filter;
        var $filter_condition;
        var $sort;
        function CRegistrySearchParam() {
                $filter = null;
                $filter_condition = 'and';
                $sort = null;
        }
        function set_filter($filter_array, $filter_condition = 'and') {
                $this->filter = $filter_array;
                $this->filter_condition = $filter_condition;
        }
        function set_sort($sort_array) {
                $this->sort = $sort_array;
        }
}

class CRegistry
{
        var $Application;
        var $DataBase;

        var $_path_id_cache = array(); // internal path_id by parent_id and name
        var $_values_cache = array();

        var $m_sTemplate = 'admin/registry/_registry.tpl';

        function CRegistry(&$app)
        {
                $this->Application = &$app;
                $this->DataBase = &$app->DataBase;
                $this->tree = array();
        }

        function add_path($parent_id, $name, $description, $edit_mask = -1, $visible = true, $tip='', $xml_template = '', $form_template = '', $confirmed = 1) // public
        {
                if ($edit_mask == -1) $edit_mask = GROUP_RIGHT_EDIT_VALUE;
                $add_group = (($edit_mask & GROUP_RIGHT_ADD_GROUP) != 0);
                $edit_group = (($edit_mask & GROUP_RIGHT_EDIT_GROUP) != 0);
                $del_group = (($edit_mask & GROUP_RIGHT_DELETE_GROUP) != 0);

                $add_value = (($edit_mask & GROUP_RIGHT_ADD_VALUE) != 0);
                $edit_value = (($edit_mask & GROUP_RIGHT_EDIT_VALUE) != 0);
                $del_value = (($edit_mask & GROUP_RIGHT_DELETE_VALUE) != 0);

                if ($edit_group) {
                        $loc = &$this->Application->get_module('Localizer');
                        $loc->add_string($tip, '', 1, $tip);
                }
                $res = $this->DataBase->insert_sql(
                        'registry_tree',
                        array(
                        'parent_id' => (is_null($parent_id)?(-1):($parent_id)),
                        'name' => $name,
                        'description' => $description,
                        'description_tip' => $tip,

                        'add_subgroup' => $add_group,
                        'edit_group' => $edit_group,
                        'del_group' => $del_group,

                        'add_value' => $add_value,
                        'edit_value' => $edit_value,
                        'del_value' => $del_value,

                        'visible' => $visible,
                        'xml_template' => $xml_template,
                        'form_template' => $form_template,

                        'confirmed' => $confirmed,
                        'insert_date' => 'now()'
                        )
                );
                $this->_recalculate_index($res, (is_null($parent_id)?(-1):($parent_id)), true);
                if ($res !== false)
                        if (strlen(trim($name)) == 0) {
                                $this->DataBase->update_sql('registry_tree', array('name'=>$res), array('path_id'=>$res));
                                return $res;
                        }
                        else
                                return $res;
                else
                        return false;
        }

        function add_value($path_id, $name, $description, $key_type, $value = '', $choises = '', $edit_mask = -1, $xml_template = '') // public
        {
                $loc = &$this->Application->get_module('Localizer');
                $loc->add_string($description, '', 1, $description);
                if ($edit_mask == -1) $edit_mask = VALUE_RIGHT_EDIT_VALUE;
                return $this->DataBase->insert_sql('registry_values', array(
                        'path_id' => $path_id,
                        'name' => $name,
                        'description' => $description,
                        'key_type' => $key_type,
                        'value' => $value,
                        'choises' => $choises,
                        'xml_template' => $xml_template,
                        'edit_value' => ( ($edit_mask&VALUE_RIGHT_EDIT_VALUE)?(1):(0) ),
                        'del_value' => ( ($edit_mask&VALUE_RIGHT_DELETE_VALUE)?(1):(0) )
                        ));
        }

        function set_validator($value_id, $required, $def, $type, $add_info1 = null, $add_info2 = null, $add_info3 = null) // public
        {
                $a = array();
                $a['value_id'] = $value_id;
                $this->DataBase->delete_sql('registry_metas', $a);
                $a['type'] = $type;
                $a['def'] = $def;
                $a['required'] = $required;
                $a['add_info1'] = serialize($add_info1);
                $a['add_info2'] = serialize($add_info2);
                $a['add_info3'] = serialize($add_info3);
                return $this->DataBase->insert_sql('registry_metas', $a);
        }

        function get_node($path, $parent_id = -1)
        {
                $c_path = split('/', $path, 2);
                if ( ($parent_id==-1) && (count($c_path) == 0) ) system_die('Invalid registry path');
                if (!empty($path)) $path_id = $this->_get_path_id($parent_id, $c_path[0]);
                else $path_id = $parent_id;
                if (count($c_path) > 1)
                        return $this->get_node($c_path[1], $path_id);
                else
                        return $this->DataBase->select_sql('registry_tree', array('path_id'=>$path_id, 'confirmed'=>1));
        }

        function get_nodes($path, $parent_id = -1, $search_param = null) // public
        {
                $c_path = split('/', $path, 2);
                if ( ($parent_id==-1) && (count($c_path) == 0) ) system_die('Invalid registry path');
                if (!empty($path)) $path_id = $this->_get_path_id($parent_id, $c_path[0]);
                else $path_id = $parent_id;
                if (count($c_path) > 1)
                        return $this->get_nodes($c_path[1], $path_id, $search_param);
                else
                {
                        if (is_object($search_param))
                        {
                                $tmp_nodes = $this->DataBase->select_custom_sql('select a.*, b.name as tmp_name, b.value as tmp_value from %prefix%registry_tree as a, %prefix%registry_values as b where a.confirmed=1 and a.path_id = b.path_id and a.parent_id='.$path_id);
                                $original_node_fields = array('path_id', 'parent_id', 'name', 'description', 'description_tip', 'edit_group', 'add_subgroup', 'edit_value', 'add_value', 'visible', 'xml_template', 'form_template');

                                $tmp_all_nodes = array();
                                $tmp_nodes_array = array();
                                while (!$tmp_nodes->eof())
                                {
                                        if (!isset($tmp_nodes_array[$tmp_nodes->get_field('path_id')])) {
                                                $tmp_nodes_array[$tmp_nodes->get_field('path_id')] = array();
                                                $tmp_nodes_array[$tmp_nodes->get_field('path_id')]['node_name'] = $tmp_nodes->get_field('name');
                                                $tmp_nodes_array[$tmp_nodes->get_field('path_id')]['node_description'] = $tmp_nodes->get_field('description');

                                                $tmp_all_nodes[$tmp_nodes->get_field('path_id')] = array();
                                                foreach ($original_node_fields as $v)
                                                        $tmp_all_nodes[$tmp_nodes->get_field('path_id')][$v] = $tmp_nodes->get_field($v);
                                        }
                                        $tmp_nodes_array[$tmp_nodes->get_field('path_id')][$tmp_nodes->get_field('tmp_name')] = $tmp_nodes->get_field('tmp_value');
                                        $tmp_nodes->next();
                                }

                                $new_nodes_array = array();
                                $nodes_ids_array = array();
                                if (is_array($search_param->filter))
                                        foreach ($tmp_nodes_array as $k => $v)
                                        {
                                                $accept = ($search_param->filter_condition == 'and');
                                                if ($search_param->filter_condition == 'and')
                                                        foreach ($search_param->filter as $k2 => $v2)
                                                                if (is_array($v2))
                                                                        foreach ($v2 as $v3)
                                                                                $accept &= (stristr(strval($v[$k2]), strval($v3)) !== false);
                                                                else
                                                                        $accept &= (stristr(strval($v[$k2]), strval($v2)) !== false);
                                                else
                                                        foreach ($search_param->filter as $k2 => $v2)
                                                                if (is_array($v2))
                                                                        foreach ($v2 as $v3)
                                                                                $accept |= (stristr(strval($v[$k2]), strval($v3)) !== false);
                                                                else
                                                                        $accept |= (stristr(strval($v[$k2]), strval($v2)) !== false);

                                                if ($accept) {$new_nodes_array[$k] = $tmp_nodes_array[$k]; $nodes_ids_array[] = $k;}
                                        }
                                else
                                        foreach ($tmp_nodes_array as $k => $v)
                                                {$new_nodes_array[$k] = $tmp_nodes_array[$k]; $nodes_ids_array[] = $k;}

                                $cnt = count($nodes_ids_array);
                                $rs = new CRecordSet();
                                foreach ($original_node_fields as $v) $rs->add_field($v);
                                if ($cnt > 1) {
                                        for ($i=0; $i<($cnt-1); $i++) { // !!!!
                                                $min = $i;
                                                if (is_array($search_param->sort))
                                                        for ($j=($i+1); $j<$cnt; $j++) {
                                                                $id = $nodes_ids_array[$min];
                                                                $id2 = $nodes_ids_array[$j];
                                                                foreach ($search_param->sort as $k => $v) {
                                                                        $cr = strcmp($new_nodes_array[$id][$k], $new_nodes_array[$id2][$k]);
                                                                        if ($cr != 0)
                                                                        if ( ( (strcasecmp($v, 'asc')==0) && ($cr > 0) ) || ( (strcasecmp($v, 'desc')==0) && ($cr < 0) ) ) {
                                                                                $min = $j; break;
                                                                        }
                                                                        else break;
                                                                }
                                                        }
                                                $a = $tmp_all_nodes[$nodes_ids_array[$min]];
                                                $rs->add_row($a);
                                                if ($min != $i) {
                                                        $tmp = $nodes_ids_array[$i];
                                                        $nodes_ids_array[$i] = $nodes_ids_array[$min];
                                                        $nodes_ids_array[$min] = $tmp;
                                                }
                                        }
                                }
                                else if ($cnt == 1)
                                {
                                        $a = $tmp_all_nodes[$nodes_ids_array[0]];
                                        $rs->add_row($a);
                                }
                                return $rs;
                        }
                        else
                                return $this->DataBase->select_sql('registry_tree', array('parent_id'=>$path_id, 'confirmed'=>1));
                }
        }

        function get_values_by_path_id($path_id) // public
        {
                $values_arr = array();
                $values_rcd = $this->DataBase->select_custom_sql('select name, value from %prefix%registry_values where path_id='.$path_id);
                if ($values_rcd === false) return false;
                while (!$values_rcd->eof()) {
                        $values_arr[$values_rcd->get_field('name')] = $values_rcd->get_field('value');
                        $values_rcd->next();
                }
                return $values_arr;
        }

        function get_value($path, $parent_id = -1) // public
        {
                $c_path = split('/', $path, 2);
                if (count($c_path) < 2) return '';
                $path_id = $this->_get_path_id($parent_id, $c_path[0]);
                if (strstr($c_path[1], '/') !== false)
                        return $this->get_value($c_path[1], $path_id);
                else
                {
                        $values = $this->get_values_by_path_id($path_id);
                        if (isset($values[$c_path[1]]))
                                return $values[$c_path[1]];
                        else
                                return '';
                }
        }

        function set_value($value, $path, $parent_id = -1) // public
        {
                $c_path = split('/', $path, 2);
                if (count($c_path) < 2)
                        return false;
                $path_id = $this->_get_path_id($parent_id, $c_path[0]);
                if (strstr($c_path[1], '/') !== false)
                        return $this->set_value($value, $c_path[1], $path_id);
                else
                        return $this->DataBase->custom_sql('update %prefix%registry_values set value=\''.mysql_escape_string($value).'\' where path_id='.$path_id.' and name=\''.mysql_escape_string($c_path[1]).'\'');
        }
        function get_value_type($path, $parent_id = -1) // public
        {
                $c_path = split('/', $path, 2);
                if (count($c_path) < 2) return '';
                $path_id = $this->_get_path_id($parent_id, $c_path[0]);
                if (strstr($c_path[1], '/') !== false)
                        return $this->get_value_type($c_path[1], $path_id);
                else {
                        $r = $this->DataBase->select_custom_sql('select key_type from %prefix%registry_values where path_id='.$path_id.' and name = \''.mysql_escape_string($c_path[1]).'\'');
                        return $r->get_field('key_type');
                }
        }
        function get_values($path, $parent_id = -1) // public
        {
                $c_path = split('/', $path, 2);
                if (count($c_path) == 0) $c_path[] = '';
                $path_id = $this->_get_path_id($parent_id, $c_path[0]);
                if (count($c_path) > 1)
                        return $this->get_values($c_path[1], $path_id);
                else
                        return $this->get_values_by_path_id($path_id);
        }
        function &get_object($path, $obj_name='RegNodeObject', $parent_id = -1) // public
        {
                $c_path = split('/', $path, 2);
                if (count($c_path) == 0) $c_path[] = '';
                $path_id = $this->_get_path_id($parent_id, $c_path[0]);
                if (count($c_path) > 1)
                        return $this->get_object($c_path[1], $obj_name, $path_id);
                else
                {
                        $v_array = $this->get_values_by_path_id($path_id);
                        $obj = new $obj_name();
                        $obj->_internal_path_id = $path_id;
                        $obj->_internal_name = $c_path[0];
                        foreach ($v_array as $k => $v) $obj->$k = $v;
                        $children = $this->get_nodes('', $path_id);
                        if ($children!==false)
                                while (!$children->eof()) {
                                        $obj->{$c_path[0]}[] = &$this->get_object($children->get_field('name'), 'RegNodeObject', $path_id);
                                        $children->next();
                                }
                        $this->Application->_add_object($obj);
                        return $obj;
                }
        }
        function delete_path($path_id) // public
        {
                $r = $this->DataBase->select_sql('select path_id from %prefix%registry_tree', array('parent_id'=>$path_id));
                if ($r === false) return false;
                while (!$r->eof()) {
                        $this->delete_path($r->get_field('path_id'));
                        $r->next();
                }
                $this->DataBase->delete_sql('registry_tree', array('path_id'=>$path_id));
                $this->DataBase->delete_sql('registry_values', array('path_id'=>$path_id));
                $this->_recalculate_index($path_id, -1, false);
        }
        function _init_validator($value_id, $name, $descr, $have_value) // internal
        {
                $v = $this->DataBase->select_sql('registry_metas', array('value_id'=>$value_id));
                if ( ($v !== false) && (!$v->eof()) )
                        if ($v->get_field('required'))
                        {
                                if ( ($v->get_field('type')==VRT_IMAGE_FILE) || ($v->get_field('type')==VRT_CUSTOM_FILE) )
                                        if ($have_value)
                                                CValidator::add_in_group_nr($name, $descr, $v->get_field('type'), $v->get_field('def'), unserialize($v->get_field('add_info1')), unserialize($v->get_field('add_info2')), unserialize($v->get_field('add_info3')));
                                        else
                                                CValidator::add_in_group($name, $descr, $v->get_field('type'), unserialize($v->get_field('add_info1')), unserialize($v->get_field('add_info2')), unserialize($v->get_field('add_info3')));
                                else
                                        CValidator::add_in_group($name, $descr, $v->get_field('type'), unserialize($v->get_field('add_info1')), unserialize($v->get_field('add_info2')), unserialize($v->get_field('add_info3')));
                        }
                        else
                                CValidator::add_in_group_nr($name, $descr, $v->get_field('type'), $v->get_field('def'), unserialize($v->get_field('add_info1')), unserialize($v->get_field('add_info2')), unserialize($v->get_field('add_info3')));
        }
        function _recalculate_index($path_id, $parent_id, $insert = true) // private
        {
                if ($insert) {
                        $level = 1;
                        while ( ($parent_id != -1) && ($level < 100) ) {
                                $this->DataBase->insert_sql('registry_tree_index', array('path_id'=>$path_id, 'parent_id'=>$parent_id, 'level'=>$level));
                                $id_p_rs = $this->DataBase->select_custom_sql('select parent_id from %prefix%registry_tree where path_id='.$parent_id);
                                $parent_id = $id_p_rs->get_field('parent_id');
                                $level++;
                        }
                        $this->DataBase->insert_sql('registry_tree_index', array('path_id'=>$path_id, 'parent_id'=>$parent_id, 'level'=>$level));
                }
                else {
                        $r = $this->DataBase->select_sql('registry_tree_index', array('parent_id'=>$path_id));
                        while (!$r->eof()) {
                                $this->_recalculate_index($r->get_field('path_id'), -1, false);
                                $r->next();
                        }
                        $this->DataBase->delete_sql('registry_tree_index', array('path_id'=>$path_id));
                }
        }
        function _get_path_id($parent_id, $name) // private
        {
                $id = $parent_id.':'.$name;
                if (!isset($this->_path_id_cache[$id])) {
                        $r = $this->DataBase->select_sql('select path_id, name from %prefix%registry_tree', array('parent_id'=>$parent_id));
                        if ($r === false) return false;
                        while (!$r->eof()) {
                                $this->_path_id_cache[$parent_id.':'.$r->get_field('name')] = $r->get_field('path_id');
                                $r->next();
                        }
                        return (isset($this->_path_id_cache[$id]))?($this->_path_id_cache[$id]):(false);
                }
                else
                        return $this->_path_id_cache[$id];
        }
        function _get_path_id_by_name($parent_id, $path) // private
        {
                $c_path = split('/', $path, 2);
                if (count($c_path) == 0) system_die('Invalid registry path');
                $path_id = $this->_get_path_id($parent_id, $c_path[0]);
                if (count($c_path) > 1)
                        return $this->_get_path_id_by_name($path_id, $c_path[1]);
                else
                        return $path_id;
        }
        function _delete_non_confirmed() // private
        {
                $not_confirmed = $this->DataBase->select_sql('select path_id from %prefix%registry_tree where confirmed=0 and date_add(insert_date, interval 1 day) < now()');
                if ($not_confirmed!==false)
                        while (!$not_confirmed->eof()) {
                                $this->delete_path($not_confirmed->get_field('path_id'));
                                $not_confirmed->next();
                        }
        }
        function _internal_get_value($path, $parent_id = -1)
        {
                if (isset($this->_values_cache[$path])) return $this->_values_cache[$path];
                $c_path = split('/', $path, 2);
                if (count($c_path) < 2) return '';
                $path_id = $this->_get_path_id($parent_id, $c_path[0]);
                if (strstr($c_path[1], '/') !== false)
                        if ($parent_id == -1) {
                                $v = $this->_internal_get_value($c_path[1], $path_id);
                                $this->_values_cache[$path] = $v;
                                return $v;
                        }
                        else
                                return $this->_internal_get_value($c_path[1], $path_id);
                else
                        return $this->DataBase->select_sql('select path_id, key_type, value from %prefix%registry_values', array('path_id'=>$path_id, 'name'=>$c_path[1]));
        }

        function run_admin_interface($module, $sub_module)
        {
                require_once(BASE_CONTROLS_PATH.'simplearrayoutput.php');
                new CSimpleArrayOutput();
                $template_vars = &$this->Application->template_vars;
                $path_id = InPostGet('path_id', -1);
                if (CForm::is_submit('registry_form', 'add_subgroup'))
                        if (CForm::get_param())
                                $path_id = CForm::get_param();

                $node = $this->DataBase->select_sql('registry_tree', array('path_id'=>$path_id));

                if ($node === false) return false;
                $template_vars['cur_action_add'] = false;
                if (!$node->eof())
                {
                        $values = $this->DataBase->select_custom_sql('select a.value_id, a.name, a.description, a.value from %prefix%registry_values as a, %prefix%registry_metas as b where a.value_id = b.value_id and path_id = '.$path_id);
                        if ($values !== false)
                                while (!$values->eof()) {
                                        $this->_init_validator($values->get_field('value_id'), 'value_v_'.$values->get_field('name'), $values->get_field('description'), strlen($values->get_field('value')));
                                        $values->next();
                                }
                        if (CForm::is_submit('registry_form', '')) // save
                        {
                                if (CValidator::validate_input())
                                {
                                        $up = array('confirmed'=>1);
                                        if (in_post('current_path_description')) $up['description']=InPostGet('current_path_description');
                                        $this->DataBase->update_sql('registry_tree', $up, array('path_id'=>$path_id));
                                        $values = $this->DataBase->select_sql('registry_values', array('path_id'=>$path_id));
                                        while (!$values->eof()) {
                                                $v = InPostGet('value_v_'.$values->get_field('name'));
                                                if (intval(InPostGet('del_'.$values->get_field('value_id'))) == 1) {
                                                        if ($values->get_field('value') != '') {
                                                                if (is_file(REGISTRY_FILES_STORAGE . $path_id . '/' . $values->get_field('value')))
                                                                        @unlink(REGISTRY_FILES_STORAGE . $path_id . '/' . $values->get_field('value'));
                                                                $v = '';
                                                        }
                                                }
                                                elseif ( ($values->get_field('key_type') == KEY_TYPE_FILE) || ($values->get_field('key_type') == KEY_TYPE_IMAGE) )
                                                {
                                                        $upload = true;
                                                        if ($values->get_field('key_type') == KEY_TYPE_IMAGE)
                                                        {
                                                                $image_types = array('image/bmp', 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/tiff', 'image/x-icon');
                                                                if (!in_array($_FILES['value_v_'.$values->get_field('name')]['type'], $image_types))
                                                                        $upload = false;
                                                        }
                                                        if ($upload)
                                                        {
                                                                global $FilePath;
                                                                if (!is_dir(rtrim(REGISTRY_FILES_STORAGE, '/\\'))) @mkdir(rtrim(REGISTRY_FILES_STORAGE, '/\\'), 0777);
                                                                if (!is_dir(REGISTRY_FILES_STORAGE . $path_id)) @mkdir(REGISTRY_FILES_STORAGE . $path_id, 0777);
                                                                if (!is_dir(REGISTRY_FILES_STORAGE . $path_id . '/' . $values->get_field('name'))) @mkdir(REGISTRY_FILES_STORAGE . $path_id . '/' . $values->get_field('name'), 0777);
                                                                if (
                                                                        (intval($_FILES['value_v_'.$values->get_field('name')]['size']) > 0) &&
                                                                        (intval($_FILES['value_v_'.$values->get_field('name')]['error']) == 0)
                                                                        )
                                                                {
                                                                        if ($values->get_field('value') != '')
                                                                                if (is_file(REGISTRY_FILES_STORAGE . $path_id . '/' . $values->get_field('value')))
                                                                                        @unlink(REGISTRY_FILES_STORAGE . $path_id . '/' . $values->get_field('value'));
                                                                        if (!move_uploaded_file($_FILES['value_v_'.$values->get_field('name')]['tmp_name'], REGISTRY_FILES_STORAGE . $path_id . '/' . $values->get_field('name') . '/' . $_FILES['value_v_'.$values->get_field('name')]['name']))
                                                                                $this->Application->template_vars['_registry_messages'][] = 'Cannot save '.REGISTRY_FILES_STORAGE . $path_id . '/' . $values->get_field('name') . '/' . $_FILES['value_v_'.$values->get_field('name')]['name'].'';
                                                                        $v = $values->get_field('name') . '/' . $_FILES['value_v_'.$values->get_field('name')]['name'];
                                                                }
                                                                else
                                                                        $v = $values->get_field('value');
                                                        }
                                                        else
                                                                $v = $values->get_field('value');
                                                }
                                                if (!isset($this->Application->template_vars['_registry_messages']))
                                                        $this->DataBase->update_sql('registry_values',
                                                                array('value'=>$v),
                                                                array('path_id'=>$path_id, 'name'=>$values->get_field('name'))
                                                                );
                                                $values->next();
                                        }
                                        if (!isset($this->Application->template_vars['_registry_messages']))
                                        {
                                                $this->Application->template_vars['_registry_info'][] = $this->Application->Localizer->get_string('item_is_updated');
                                                //$path_id = $node->get_field('parent_id');
                                                //$this->Application->CurrentPage->redirect($this->Application->template_vars['HTTP'] . 'admin/?action=run_module&module=Registry&path_id='.$path_id);
                                        }
                                }
                                else
                                        $this->Application->template_vars['_registry_messages'] = CValidator::get_errors();
                        }
                        else
                                $this->_delete_non_confirmed();
//                        if ((CForm::is_submit('registry_form', 'add_subgroup')) || (InPostGet('create_new_registry_item', '') == 'true')) {
                        if (CForm::is_submit('registry_form', 'add_subgroup')) {
                                // add sub_group
//                                echo "555-".$path_id;
                                $templates['create_new_registry_item'] = '';
                                require_once(BASE_CLASSES_PATH . 'components/lwg_xml.php');
                                $gt = &lwg_domxml_open_file(REGISTRY_XML . $node->get_field('xml_template'));
                                if (is_object($gt)) {
                                        $template_vars['cur_action_add'] = true;
                                        $paths = &$gt->selectNodes('path');
                                        foreach ($paths as $k => $v) {
                                                $edit_mask = 0;
                                                if (str_to_bool($paths[$k]->get_leap_content('add_subgroup', 0))) $edit_mask |= GROUP_RIGHT_ADD_GROUP;
                                                if (str_to_bool($paths[$k]->get_leap_content('edit_group', 1))) $edit_mask |= GROUP_RIGHT_EDIT_GROUP;
                                                if (str_to_bool($paths[$k]->get_leap_content('delete_group', 1))) $edit_mask |= GROUP_RIGHT_DELETE_GROUP;
                                                if (str_to_bool($paths[$k]->get_leap_content('add_value', 0))) $edit_mask |= GROUP_RIGHT_ADD_VALUE;
                                                if (str_to_bool($paths[$k]->get_leap_content('edit_value', 1))) $edit_mask |= GROUP_RIGHT_EDIT_VALUE;
                                                if (str_to_bool($paths[$k]->get_leap_content('delete_value', 0))) $edit_mask |= GROUP_RIGHT_DELETE_VALUE;

                                                $path_id = $this->add_path($path_id, '', 'New '.$node->get_field('description'), $edit_mask, true, $paths[$k]->get_leap_content('description_tip'), $paths[$k]->get_leap_content('xml_template'), $paths[$k]->get_leap_content('form_template'), $paths[$k]->get_leap_content('confirmed'));
                                                $groups = &$paths[$k]->selectNodes('group');
                                                foreach ($groups as $k2 => $v2) {
                                                        $edit_mask = 0;
                                                        if (str_to_bool($groups[$k2]->get_leap_content('add_subgroup', 0))) $edit_mask |= GROUP_RIGHT_ADD_GROUP;
                                                        if (str_to_bool($groups[$k2]->get_leap_content('edit_group', 0))) $edit_mask |= GROUP_RIGHT_EDIT_GROUP;
                                                        if (str_to_bool($groups[$k2]->get_leap_content('delete_group', 0))) $edit_mask |= GROUP_RIGHT_DELETE_GROUP;
                                                        if (str_to_bool($groups[$k2]->get_leap_content('add_value', 0))) $edit_mask |= GROUP_RIGHT_ADD_VALUE;
                                                        if (str_to_bool($groups[$k2]->get_leap_content('edit_value', 1))) $edit_mask |= GROUP_RIGHT_EDIT_VALUE;
                                                        if (str_to_bool($groups[$k2]->get_leap_content('delete_value', 0))) $edit_mask |= GROUP_RIGHT_DELETE_VALUE;
                                                        $description = $groups[$k2]->get_leap_content('description');
                                                        $this->add_path($path_id, $groups[$k2]->get_leap_content('name'), $groups[$k2]->get_leap_content('description'), $edit_mask, true, $groups[$k2]->get_leap_content('description_tip'), $groups[$k2]->get_leap_content('xml_template'), $groups[$k2]->get_leap_content('form_template'));
                                                }
                                                $values = &$paths[$k]->selectNodes('value');
                                                foreach ($values as $k2 => $v2) {
                                                        $edit_mask = 0;
                                                        if (str_to_bool($values[$k2]->get_leap_content('edit_value', 1))) $edit_mask |= VALUE_RIGHT_EDIT_VALUE;
                                                        if (str_to_bool($values[$k2]->get_leap_content('delete_value', 0))) $edit_mask |= VALUE_RIGHT_DELETE_VALUE;
                                                        $value_id = $this->add_value($path_id, $values[$k2]->get_leap_content('name'), $values[$k2]->get_leap_content('description'), constant($values[$k2]->get_leap_content('type')), '', $values[$k2]->get_leap_content('choises'), $edit_mask);
                                                        $validator = $values[$k2]->selectNodes('validator');
                                                        foreach ($validator as $k3 => $v3) {
                                                                $req = $validator[$k3]->get_leap_content('required', 1);
                                                                $def = $validator[$k3]->get_leap_content('default', '');
                                                                $type = $validator[$k3]->get_leap_content('type');
                                                                if (defined($type)) $type = constant($type);
                                                                $a1 = $validator[$k3]->get_leap_content('add_info1');
                                                                $a2 = $validator[$k3]->get_leap_content('add_info2');
                                                                $a3 = $validator[$k3]->get_leap_content('add_info3');
                                                                if ($a1 === false) $a1 = null;
                                                                if ($a2 === false) $a2 = null;
                                                                if ($a3 === false) $a3 = null;
                                                                $this->set_validator($value_id, $req, $def, $type, $a1, $a2, $a3);
                                                        }
                                                }
                                        }
                                }
                                else
                                        $this->Application->template_vars['_registry_g_messages'] = 'Cannot open '.REGISTRY_XML . $node->get_field('xml_template').'. '.$GLOBALS['lwg_xml_last_error'];
                        }
                        if (CForm::is_submit('registry_form', 'delete_group')) {
                                $this->delete_path($path_id);
                                $path_id = $node->get_field('parent_id');
                                $this->Application->CurrentPage->redirect($this->Application->template_vars['HTTP'] . 'admin/?action=run_module&module=Registry&path_id='.$path_id);
                        }
                }
                $node = $this->DataBase->select_sql('registry_tree', array('path_id'=>$path_id));
                if ($node->eof()) {
                        $path_id = -1;
                        $template_vars['current_path_parent_id'] = false;
                        $template_vars['current_path_edit_group'] = false;
                } else {
                        row_to_vars($node, $template_vars, false, 'current_path_');
                        if (strlen($node->get_field('form_template'))) $this->m_sTemplate = $node->get_field('form_template');
                        if ($node->get_field('edit_group')) CValidator::add_in_group('current_path_description', $node->get_field('description_tip'), VRT_TEXT, 0, 255);
                }

                if ( ($path_id==-1) || (intval($node->get_field('confirmed')) == 1) ) {
                        $is_group = false;
                        if (isset($node))
                                if (!$node->eof())
                                        $is_group = $node->get_field('xml_template');

                        $this->get_current_tree($path_id, $is_group);
                        //$nodes = $this->DataBase->select_custom_sql('select * from %prefix%registry_tree where parent_id='.$path_id.' and confirmed=1');
                        //recordset_to_vars($nodes, $template_vars, 'node_count', 'node_');
                } else {
//                        $template_vars['node_count'] = 0;

                        $nodes = $this->DataBase->select_sql('registry_tree', array('parent_id' => $path_id, 'confirmed' => 1, 'visible' => 1));
                        if ($nodes->eof()) {
                                $nodes = $this->DataBase->select_sql('registry_tree', array('path_id' => $path_id));
                                if ($nodes->eof()) {
                                        $this->Application->template_vars['_registry_list_messages'][] = 'There is no item with such data';
                                        $this->get_current_tree(-1);
                                } else $this->get_current_tree($nodes->get_field('parent_id'), $nodes->get_field('xml_template'));
                        }

                }

                function values_func($q, $row, $prefix, $index, $data)
                {
                        global $app;
                        $tv = &$app->template_vars;
                        $types = array('', 'textarea', 'text', 'file', 'checkbox', 'htmlarea', 'select', 'file');
                        if ( (intval($row->get_field('edit_value')) == 1) && (intval($data) == 1) )
                            $tv[$prefix.'edit_type'][] = $types[$row->get_field('key_type')];
                        else
                            $tv[$prefix.'edit_type'][] = 'disabled';
                        $tv[$prefix.'edit_checkbox'][] = (strcasecmp($types[$row->get_field('key_type')], 'checkbox') == 0);
                        $tv['value_v_'.$row->get_field('name')] = $row->get_field('value');
                        if ($row->get_field('key_type') == KEY_TYPE_MEMO) 
                        {
                        		$tv[$prefix.'input_add'][] = 'cols="45" rows="15"'; 
                        		$tv['ch'][]=false; 
                        		$tv['htm'][]=false;
                        }
                        elseif ($row->get_field('key_type') == KEY_TYPE_CHECKBOX) 
                        {
                        	$tv[$prefix.'input_add'][] = 'value="1"';  
                        	$tv['ch'][]=true; 
                        	$tv['htm'][]=false;
                        }
                        elseif ($row->get_field('key_type') == KEY_TYPE_HTML) 
                        {
                        	$tv[$prefix.'input_add'][] = 'cols="45" rows="15" height="400"';  
                        	$tv['ch'][]=false; 
                        	$tv['htm'][]=true;
                        }
                        elseif ($row->get_field('key_type') == KEY_TYPE_SELECT) 
                        {
                                $tv['ch'][]=false;
                                $tv['htm'][]=false;
                                $a = split(';', $row->get_field('choises'));
                                $b = array();
                                foreach ($a as $v)
                                        if ($v != '') {
                                                list($i, $v2) = split('=', $v); $b[$i] = $v2;
                                        }
                                CInput::set_select_data('value_v_'.$row->get_field('name'), $b);
                        }
                        else
                        {
                        		$tv['ch'][]=false; 
                        		$tv['htm'][]=false;
                                $tv[$prefix.'input_add'][] = '';
                        }
                        $vm = $GLOBALS['app']->DataBase->select_sql('registry_metas', array('value_id'=>$row->get_field('value_id')));
                        $tv['value_validator_req'][] = ( ($vm!==false) && (!$vm->eof()) && (intval($vm->get_field('required'))==1) );
                        $tv['value_type_file'][] = ($row->get_field('key_type') == KEY_TYPE_FILE);
                        $tv['value_type_image'][] = ($row->get_field('key_type') == KEY_TYPE_IMAGE);

                        if (($row->get_field('key_type') == KEY_TYPE_MEMO) || ($row->get_field('key_type') == KEY_TYPE_HTML))
                                $tv['value_type_textarea'][] = ' valign="top"';
                        else
                                $tv['value_type_textarea'][] = '';
                                
                }
                $values = $this->DataBase->select_sql('registry_values', array('path_id'=>$path_id), array('value_id'=>'asc'));
                if (!$values->eof())
                        recordset_to_vars_callback($values, $template_vars, 'value_count', 'values_func', 'value_', $node->get_field('edit_value'));
                else
                        $template_vars['value_count'] = 0;

                $template_vars['path_editor_mode'] = ($template_vars['current_path_parent_id'] && $template_vars['current_path_edit_group']) || ($template_vars['value_count'] > 0);
                $values = $this->DataBase->select_custom_sql('select a.value_id, a.name, a.description, a.value from %prefix%registry_values as a, %prefix%registry_metas as b where a.value_id = b.value_id and path_id = '.$path_id);
                if ($values !== false)
                        while (!$values->eof()) {
                                $this->_init_validator($values->get_field('value_id'), 'value_v_'.$values->get_field('name'), $values->get_field('description'), strlen($values->get_field('value')));
                                $values->next();
                        }
                $template_vars['path_id'] = $path_id;

               return CTemplate::parse_file(CUSTOM_TEMPLATE_PATH . $this->m_sTemplate, $template_vars);
        } // end of run_admin_interface

        function get_tree_recursive($id, & $tree)
        {
            $sql = "SELECT * FROM %prefix%registry_tree WHERE parent_id=".$id;
            $tree_rs = $this->Application->DataBase->select_custom_sql($sql);
            while (!$tree_rs->eof()) {
                if($tree_rs->get_field('path_id') == -1)
                {
                    $tree->addNode($tree_rs->get_field('description'), NULL, $tree_rs->get_field('path_id'));
                }
                else
                {
                    $this->get_tree_recursive($tree_rs->get_field('path_id'), $tree->addNode($tree_rs->get_field('description'), NULL, $tree_rs->get_field('path_id')));
                }
                $tree_rs->next();
            }
        }

        function get_current_tree($id_cur_path, $is_group = true)
        {
                require_once(CUSTOM_CONTROLS_PATH . 'treeview.php');

                $tree = new CTreeView($this, 'tree');
                $tree_rs = $this->get_tree_recursive(-1, $tree);
        }

        function close_children_items($id_item)
        {
                $node = $this->DataBase->select_sql('registry_tree', array('parent_id' => $id_item, 'confirmed' => 1, 'visible' => 1));
                while (!$node->eof()) {
                        $open_item = array_search($node->get_field('path_id'), $_SESSION['registry_tree']['opened_items']);
                        if ($open_item !== false) {
                                unset($_SESSION['registry_tree']['opened_items'][$node->get_field('path_id')]);
                                $this->close_children_items($node->get_field('path_id'));
                        }
                        $node->next();
                }
        }

        function get_all_items($id_item, $parent_id = -1, $level = 0)
        {
                $nodes = $this->DataBase->select_sql('registry_tree', array('parent_id' => $id_item, 'confirmed' => 1, 'visible' => 1));
                while (!$nodes->eof()) {
                        $this->Application->template_vars['node_count']++;
                        $this->Application->template_vars['node_path_id'][] = $nodes->get_field('path_id');
                        $this->Application->template_vars['node_parent_id'][] = $nodes->get_field('parent_id');
                        $this->Application->template_vars['node_description'][] = $nodes->get_field('description');
                        $this->Application->template_vars['node_level'][] = $level;

                        if ($nodes->get_field('xml_template'))
                                $this->Application->template_vars['node_add_box'][] = true;
                        else
                                $this->Application->template_vars['node_add_box'][] = false;

                        if ($nodes->get_field('path_id') <> $this->Application->template_vars['cur_path_id']) {
                                $this->Application->template_vars['node_active'][] = false;
                                $this->Application->template_vars['node_visible'][] = false;
                                $this->Application->template_vars['node_active_box'][] = false;
                        } else {
                                $this->Application->template_vars['node_active'][] = true;
                                $this->Application->template_vars['node_visible'][] = true;
                                if ($this->Application->template_vars['close_id'] <> $nodes->get_field('path_id'))
                                        $this->Application->template_vars['node_active_box'][] = true;
                                else
                                        $this->Application->template_vars['node_active_box'][] = false;
                        }


                        $subnodes = $this->DataBase->select_sql('registry_tree', array('parent_id' => intval($nodes->get_field('path_id')), 'confirmed' => 1));
                        if ($subnodes->eof()) {
                                if (trim($nodes->get_field('xml_template')) != '')
                                        $this->Application->template_vars['node_is_parent'][] = true;
                                else
                                        $this->Application->template_vars['node_is_parent'][] = false;

                                $this->Application->template_vars['node_is_box'][] = false;
                        } else {
                                $this->Application->template_vars['node_is_parent'][] = true;
                                $this->Application->template_vars['node_is_box'][] = true;
                        }

                        $this->get_all_items($nodes->get_field('path_id'), $nodes->get_field('parent_id'), ($level + 1));

                        $nodes->next();
                }
        }

        function items_expand($id_item)
        {
                if ($id_item > 0) {
                        $item = array_search($id_item, $this->Application->template_vars["node_path_id"]);

                        if (($item !== false)) {
                                $this->Application->template_vars["node_visible"][$item] = true;

                                if ($this->Application->template_vars['close_id'] <> $id_item)
                                        $this->Application->template_vars['node_active_box'][$item] = true;
                                else
                                        $this->Application->template_vars['node_active_box'][$item] = false;

                                if (isset($this->Application->template_vars["node_parent_id"][$item])) {
                                        $cur_level_items = array_keys($this->Application->template_vars["node_parent_id"], $id_item);
                                        for ($i=0; $i<count($cur_level_items); $i++) {
                                                $this->Application->template_vars["node_visible"][$cur_level_items[$i]] = true;
                                        }

                                        $this->items_expand($this->Application->template_vars["node_parent_id"][$item]);
                                }
                        }
                } elseif ($id_item == -1) {
                        $cur_level_items = array_keys($this->Application->template_vars["node_parent_id"], $id_item);
                        for ($i=0; $i<count($cur_level_items); $i++) {
                                $this->Application->template_vars["node_visible"][$cur_level_items[$i]] = true;
                        }
                }
        }

        function _get_full_path($path_id) // private
        {
                $path = $this->DataBase->select_custom_sql('select path_id, parent_id, name from %prefix%registry_tree where path_id='.$path_id);
                if ($path === false) return false;
                if ($path->get_field('parent_id') == -1) return $path->get_field('name');
                else return $this->_get_full_path($path->get_field('parent_id')) . '/' . $path->get_field('name');
        }
        
		function get_admin_names($level=null) {
		    return 'Settings';
		}
        
		function special_feature($run)
        {
                if (!$run)
                {
                        return "Images Import/Export";
                }
                else
                {
                        require_once(BASE_CLASSES_PATH . 'components/lwg_xml.php');
                        CValidator::add('import_file', VRT_CUSTOM_FILE);
                        if (CForm::is_submit('reg_ex_im', 'export'))
                        {
                                $images = $this->DataBase->select_custom_sql('select * from %prefix%registry_values where key_type in ('.KEY_TYPE_FILE.','.KEY_TYPE_IMAGE.')');
                                $xml = &lwg_domxml_create();
                                $xml->m_Root->tagname = 'Registry';
                                if ($images !== false)
                                        while(!$images->eof())
                                        {
                                                $path_string = $this->_get_full_path($images->get_field('path_id'));
                                                $node = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'File', '');
                                                $node->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'path', $path_string));
                                                $node->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'name', $images->get_field('name')));
                                                $node->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'description', $images->get_field('description')));
                                                $node->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'key_type', $images->get_field('key_type')));
                                                $node->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'value', $images->get_field('value')));
                                                $node->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'choises', $images->get_field('choises')));
                                                $node->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'xml_template', $images->get_field('xml_template')));
                                                $node->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'content', file_to_xml_value(REGISTRY_FILES_STORAGE . $images->get_field('path_id') . '/' . $images->get_field('value'))));
                                                $xml->m_Root->appendChild($node);
                                                $images->next();
                                        }
                                @ob_end_clean();
                                header('Content-type: application/octet-stream');
                                header('Cache-control: private');
                                header('Content-disposition: attachment; filename=registry.xml.gz');
                                header('Pragma: public');
                                @ob_end_clean();
                                echo @gzcompress($xml->getXML(), 9);
                                die();
                        }
                        if (CForm::is_submit('reg_ex_im', 'import'))
                        {
                                if (CValidator::validate_input())
                                {
                                        $xml_file = $this->Application->template_vars['import_file_file_content'];
                                        if (strncmp($xml_file, '<?xml ', 6) != 0)
                                                $xml_file = @gzuncompress($xml_file);
                                        $xml = &lwg_domxml_open_mem($xml_file);
                                        if ($xml === false)
                                        {
                                                require_once(BASE_CONTROLS_PATH.'simplearrayoutput.php');
                                                new CSimpleArrayOutput();
                                                $this->Application->template_vars['reg_ie_errors'] = $GLOBALS['lwg_xml_last_error'];
                                        }
                                        else
                                        {
                                                require_once(FUNCTION_PATH.'functions.files.php');
                                                deldir(REGISTRY_FILES_STORAGE);
                                                $info = array();
                                                $info[0] = 0;
                                                $files = $xml->selectNodes('File');
                                                foreach ($files as $i => $v)
                                                {
                                                        $path_id = $this->_get_path_id_by_name(-1, $v->get_leap_content('path'));
                                                        $k = array('name' => $v->get_leap_content('name'), 'path_id' => $path_id,);
                                                        $up = array('value' => $v->get_leap_content('value'));
                                                        $this->DataBase->update_sql('registry_values', $up, $k);
                                                        if (!is_dir(rtrim(REGISTRY_FILES_STORAGE, '/\\'))) @mkdir(rtrim(REGISTRY_FILES_STORAGE, '/\\'), 0777);
                                                        @chmod(rtrim(REGISTRY_FILES_STORAGE, '/\\'), 0777);
                                                        if (!is_dir(REGISTRY_FILES_STORAGE . $path_id)) @mkdir(REGISTRY_FILES_STORAGE . $path_id, 0777);
                                                        @chmod(REGISTRY_FILES_STORAGE . $path_id, 0777);
                                                        if (!is_dir(REGISTRY_FILES_STORAGE . $path_id . '/' . $v->get_leap_content('name'))) @mkdir(REGISTRY_FILES_STORAGE . $path_id . '/' . $v->get_leap_content('name'), 0777);
                                                        @chmod(REGISTRY_FILES_STORAGE . $path_id . '/' . $v->get_leap_content('name'), 0777);
                                                        if ($v->get_leap_content('value') != '') {
                                                                if (is_file(REGISTRY_FILES_STORAGE . $path_id . '/' . $v->get_leap_content('value')))
                                                                        @unlink(REGISTRY_FILES_STORAGE . $path_id . '/' . $v->get_leap_content('value'));
                                                                $fh = @fopen(REGISTRY_FILES_STORAGE . $path_id . '/' . $v->get_leap_content('value'), 'wb');
                                                                if ($fh) {
                                                                        fwrite($fh, base64_decode($v->get_leap_content('content')));
                                                                        fclose($fh);
                                                                        $info[0]++;
                                                                }
                                                                else
                                                                        echo 'Error opening '.REGISTRY_FILES_STORAGE . $path_id . '/' . $v->get_leap_content('value') . ' for writing<br>';
                                                        }
                                                }
                                                $info[0] = 'Uploaded '.$info[0].' from '.count($files).' files';
                                                require_once(BASE_CONTROLS_PATH.'simplearrayoutput.php');
                                                new CSimpleArrayOutput();
                                                $this->Application->template_vars['reg_ie_errors'] = $info;
                                        }
                                }
                                else {
                                        require_once(BASE_CONTROLS_PATH.'simplearrayoutput.php');
                                        new CSimpleArrayOutput();
                                        $this->Application->template_vars['reg_ie_errors'] = CValidator::get_errors();
                                }
                        }
                        
                        return CTemplate::parse_file(CUSTOM_TEMPLATE_PATH . 'admin/registry/_registry_ei.tpl');
                }
        }
        
        function check_install() {
                return ( ($this->DataBase->is_table('registry_tree')) && ($this->DataBase->is_table('registry_values')) && ($this->DataBase->is_table('registry_tree_index')) && ($this->DataBase->is_table('registry_metas')) );
        }
        
        function install() {
        	$this->DataBase->custom_sql("DROP TABLE IF EXISTS `registry_metas`");
        	$this->DataBase->custom_sql("DROP TABLE IF EXISTS `registry_tree`");
        	$this->DataBase->custom_sql("DROP TABLE IF EXISTS `registry_tree_index`");
        	$this->DataBase->custom_sql("DROP TABLE IF EXISTS `registry_values`");

			$this->DataBase->custom_sql("
				CREATE TABLE `registry_metas` (
					`value_id` INTEGER(11) NOT NULL,
					`type` INTEGER(11) NOT NULL,
					`def` VARCHAR(255) COLLATE utf8_general_ci DEFAULT NULL,
					`required` INTEGER(11) NOT NULL,
					`add_info1` TEXT COLLATE utf8_general_ci,
					`add_info2` TEXT COLLATE utf8_general_ci,
					`add_info3` TEXT COLLATE utf8_general_ci,
				PRIMARY KEY (`value_id`)
				)ENGINE=InnoDB
				CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
			");
			$this->DataBase->custom_sql("
				CREATE TABLE `registry_tree` (
					`path_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
					`parent_id` INTEGER(11) NOT NULL DEFAULT '-1',
					`name` VARCHAR(255) COLLATE utf8_general_ci DEFAULT NULL,
					`description` VARCHAR(255) COLLATE utf8_general_ci DEFAULT NULL,
					`description_tip` VARCHAR(255) COLLATE utf8_general_ci DEFAULT NULL,
					`add_subgroup` INTEGER(11) NOT NULL DEFAULT '0',
					`edit_group` INTEGER(11) NOT NULL DEFAULT '0',
					`del_group` INTEGER(11) NOT NULL DEFAULT '0',
					`add_value` INTEGER(11) NOT NULL DEFAULT '0',
					`edit_value` INTEGER(11) NOT NULL DEFAULT '0',
					`del_value` INTEGER(11) NOT NULL DEFAULT '0',
					`visible` INTEGER(11) NOT NULL DEFAULT '1',
					`xml_template` VARCHAR(255) COLLATE utf8_general_ci DEFAULT NULL,
					`form_template` VARCHAR(255) COLLATE utf8_general_ci DEFAULT NULL,
					`confirmed` INTEGER(11) NOT NULL DEFAULT '0',
					`insert_date` DATETIME NOT NULL,
				PRIMARY KEY (`path_id`),
				UNIQUE KEY `id_parent` (`parent_id`, `path_id`),
				UNIQUE KEY `id_parent_3` (`parent_id`, `path_id`, `confirmed`),
				UNIQUE KEY `confirmed` (`confirmed`, `path_id`),
				UNIQUE KEY `id_parent_4` (`parent_id`, `path_id`, `name`),
				KEY `id_parent_2` (`parent_id`, `confirmed`),
				KEY `visible` (`visible`),
				KEY `confirmed_2` (`confirmed`)
				)ENGINE=InnoDB
				CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'			
			");
			$this->DataBase->custom_sql("
				CREATE TABLE `registry_tree_index` (
					`path_id` INTEGER(11) NOT NULL,
					`parent_id` INTEGER(11) NOT NULL,
					`level` INTEGER(11) NOT NULL,
				PRIMARY KEY (`path_id`, `parent_id`, `level`),
				KEY `id_path` (`path_id`, `parent_id`),
				KEY `id_path_2` (`path_id`, `level`),
				KEY `id_parent` (`parent_id`, `path_id`),
				KEY `id_path_3` (`path_id`),
				KEY `id_parent_2` (`parent_id`),
				KEY `level` (`level`)
				)ENGINE=InnoDB
				CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
			");
			$this->DataBase->custom_sql("
				CREATE TABLE `registry_values` (
					`value_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
					`path_id` INTEGER(11) NOT NULL,
					`name` VARCHAR(255) COLLATE utf8_general_ci DEFAULT NULL,
					`description` VARCHAR(255) COLLATE utf8_general_ci DEFAULT NULL,
					`key_type` INTEGER(11) NOT NULL DEFAULT '0',
					`value` TEXT COLLATE utf8_general_ci,
					`choises` TEXT COLLATE utf8_general_ci,
					`xml_template` VARCHAR(255) COLLATE utf8_general_ci DEFAULT NULL,
					`edit_value` INTEGER(11) NOT NULL DEFAULT '1',
					`del_value` INTEGER(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`value_id`),
				UNIQUE KEY `id_path` (`path_id`, `value_id`),
				UNIQUE KEY `id_path_2` (`path_id`, `name`),
				KEY `edit_value` (`edit_value`),
				KEY `del_value` (`del_value`)
				)ENGINE=InnoDB
				CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
			");
        }
}

// internal class for get_object
class RegNodeObject {}
?>