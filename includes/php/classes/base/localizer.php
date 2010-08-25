<?php
/**
 * @package LLA.Base
 */
/*
--------------------------------------------------------------------------------
Class CLocalizer v 1.1.3
maintains localized vars
        1. works as global module $this->Application->get_module('Localizer')
        2. works as control in templates IT:Localizer string='name'

methods:
        void add_string(string $name, string $description, int $language_id, string $value) - add string in localizer database
        string get_string(string $name[, list of arguments]) - returns value of name, can include printf format specificators, ie %s, %d

use:
        add_string method is better to use in CApplication::on_install_module event to insert required string in database during installation

        get_string($name) - simple returns string associated with $name
        get_string($name, $v1, $v2) - if value associated with $name has printf format specificator - allow you to pass arguments to it

history:
        v 1.1.3 - bug with long indexes fixed (VK)
        v 1.1.2 - bug with cache fixed (VK)
        v 1.1.1 - strings are cached now (VK)
        v 1.1.0 - formated strings added (VK)
        v 1.0.1 - description added (VK)
        v 1.0.0 - created (VK)
--------------------------------------------------------------------------------
*/

/**
 * @package LLA.Base
 * @version 1.1.3
 */
class CLocalizer extends CTemplateControl {
        var $Application;
        var $strings;
        function CLocalizer(&$app) {
            parent::CTemplateControl('Localizer');
            $this->Application = &$app;
            $this->DataBase = &$this->Application->DataBase;
            $this->tv = &$app->template_vars;
            $this->strings = array();
            $this->Application->template_vars['language_id'] = intval(InCookie('language_id', 1), 10);
        }
        function get_language_by_id($language_id)
        {
            $language_id = intval($language_id);
            if ( $language_id < 1 ) {
                $this->last_error = $this->Application->Localizer->get_string('invalid_input_data');
                return false;
            }

            $rs = $this->Application->DataBase->select_sql('loc_lang', array('id' => $language_id));

            if ( $rs === false ) {
                $this->last_error = $this->Application->Localizer->get_string('database_error');
                return false;
            }

            $this->last_error = '';
            return $rs;
        }
        function get_languages()
        {
            $rs = $this->Application->DataBase->select_sql('loc_lang', array(), array('title' => 'ASC'));
            return ((($rs !== false)&&(!$rs->eof())) ? $rs : null);
        }
        function add_loc_lang($arr)
        {
            $insert_array = array(
                'title' => $arr['title'],
                'abbreviation' => $arr['abbreviation'],
            );
            if ($language_id = $this->Application->DataBase->insert_sql('loc_lang', $insert_array))
                return $language_id;
            else
            {
                $this->last_error = $this->Application->Localizer->get_string('internal_error');
                return false;
            }

            return $language_id;
        }

        function update_loc_lang($language_id, $arr)
        {
            $update_array = array(
                'title' => $arr['title'],
                'abbreviation' => $arr['abbreviation'],
            );

            if ($this->Application->DataBase->update_sql('loc_lang', $update_array, array('id'=>$language_id)))
                return true;
            else
            {
                $this->last_error = $this->Application->Localizer->get_string('internal_error');
                return false;
            }
            return true;
        }
        function set_language($language_id)
        {
            $this->Application->template_vars['language_id'] = $language_id;
            @setcookie('language_id', $language_id);
            $this->strings = array();
        }
        function add_string($name, $description, $language_id, $value) {
            $exists_rs = $this->Application->DataBase->select_sql('loc_strings', array('language_id'=>intval($language_id, 10), 'name'=>$name));
            if ($exists_rs->eof())
                    return $this->Application->DataBase->insert_sql('loc_strings', array('language_id'=>intval($language_id, 10), 'auto_created'=>0, 'name'=>$name, 'description'=>$description, 'value'=>$value));
            else
                    return true;
        }
         
        function add_loc_strings($arr)
        {
            $insert_array = array(
                'name' => $arr['name'],
                'value' => $arr['value'],
             );

            return $this->DataBase->insert_sql('loc_strings', $insert_array);
        }

        function update_loc_strings($id, $arr)
        {
            $update_array = array(
                'name' => $arr['name'],
                'value' => $arr['value'],
                );

            if ($this->DataBase->update_sql('loc_strings', $update_array, array('id'=>$id)))
                return true;
            else
            {
                $this->last_error = $this->Application->Localizer->get_string('internal_error');
                return false;
            }
            return true;
        }

        function get_string($name) {
            $language_id = (isset($this->Application->template_vars['language_id']))?($this->Application->template_vars['language_id']):(1);
            if (func_num_args() == 0) system_die('Invalid arguments of CLocalizer::get_string');
            if (count($this->strings)==0) {
                    $r = $this->Application->DataBase->internalQuery('select name, value from %prefix%loc_strings where language_id='.$language_id);
                    if ($r!==false) while ($row = $this->Application->DataBase->internalFetchArray($r)) $this->strings[$row[0]] = $row[1];
            }
            if ($name != '') {
                    $value = '';
                    if (isset($this->strings[$name])) $value = $this->strings[$name];
                    else {
                            $r = $this->Application->DataBase->select_sql('loc_strings', array('language_id'=>$language_id, 'name'=>$name));
                            if ( (is_object($r)) && (!$r->eof()) ) $value = $r->Rows[0]->Fields['value'];
                            else {
                                    $this->Application->DataBase->insert_sql('loc_strings', array('language_id'=>$language_id, 'auto_created'=>'1', 'name'=>$name, 'description'=>$name, 'value'=>$name));
                                    return $name;
                            }
                    }
                    if (func_num_args() > 1) {
                            $args = func_get_args();
                            $args[0] = $value;
                            $value = call_user_func_array('sprintf', $args);
                    }
                    return $value;
            }
            else
                    system_die('Empty localizer name');
        }
        function get_gstring($group_name, $key_name)
        {
            $arr = func_get_args();
            array_shift($arr);
            return call_user_func_array(array($this, 'get_string'), $arr);
        }
        function process() {
        		if ($this->get_input_var('htmlspecialchars') == 'true') {
	                return nl2br(htmlspecialchars($this->get_gstring($this->get_input_var('group'), $this->get_input_var('string'), $this->get_input_var('param'))));
        		}
        		else {
	                return nl2br($this->get_gstring($this->get_input_var('group'), $this->get_input_var('string'), $this->get_input_var('param')));
        		}
        }
        function get_admin_names($level=null) {
            if ( $level === -1 ) return -1; // mean function support multilevel menu
            if ( $level === 0 )
                    if ( $GLOBALS['DebugLevel'] ) {
                            $menu[1]['AdminMenu']['su']['def'] = $this->Application->Localizer->get_string('module_localizer');;
                            return $menu;
                } else
                        return '';
            return ($GLOBALS['DebugLevel'])?($this->Application->Localizer->get_string('module_localizer')):array();
        }
        function run_admin_interface($module, $sub_module) {
        	
            require_once(CUSTOM_CONTROLS_PATH . 'simpledataoutput.php');
            new CSimpleDataOutput($this);
            require_once(BASE_CONTROLS_PATH . 'simplearrayoutput.php');
            new CSimpleArrayOutput();

            $this->Application->add_submodule($this->Application->Localizer->get_string('submodule_loc_languages'), 'Languages');

        	$this->Application->template_vars['current_module_name'] = 'Localizer';
            
            if(InPostGet('submodule','') == 'Languages')
            {
                $this->language_id = InGetPost('language_id', false);

                CValidator::add('title', VRT_TEXT, 1, 250);
                CValidator::add('abbreviation', VRT_TEXT, 1, 6);

                if (CForm::is_submit('language_list_form', 'add'))
                {
                    $this->tv['language_edit_form'] = true;

                    $this->tv['language_form_head'] = "New language";
                    $this->tv['create_date_formatted'] = date('j-M-y g:i A');
                    $this->id = "";
                }

                if (CForm::is_submit('language_list_form', 'remove'))
                {
                    $data = InGetPost("ch", array());
                    $where='WHERE ';
                    if (sizeof($data) > 0) {
                        foreach($data as $k => $v) $where.="id=".$v." OR ";
                        $where = substr($where, 0 , -4);
                        $sql = 'DELETE FROM %prefix%language ' . $where;
                        if($this->DataBase->select_custom_sql($sql)) {
                                $this->Application->CurrentPage->redirect(get_url(NULL));
                        } else $this->tv['_language_error'][] = $this->Application->Localizer->get_string('internal_error');
                   } else $this->tv['_language_info'][] = $this->Application->Localizer->get_string('noitem');
                }

                if (CForm::is_submit('language_form', 'cancel'))
                {
                    $this->Application->CurrentPage->redirect($this->tv['ROOT']."admin/index.php?action=run_module&module=".$this->tv['current_module']."&submodule=".$this->tv['current_submodule']);
                }

                if (CForm::is_submit('language_form'))
                {
                    $this->tv['language_edit_form'] = true;
                    if (CValidator::validate_input())
                    {
                        if (($this->language_id)&&(strlen($this->tv['language_id']) > 0))
                        {
                            //update
                            if ($this->update_language($this->tv['language_id'], $this->tv))
                                $this->tv['_language_info'][] = $this->Application->Localizer->get_string('language_is_updated');
                            else
                                $this->tv['_language_error'][] = $this->get_last_error();
                        }
                        else
                        {
                            //add
                            if ($language_id = $this->add_language($this->tv))
                            {
                                $this->tv['_language_info'][] = $this->Application->Localizer->get_string('language_is_added');
                                $this->tv['language_id'] = $language_idd;
                            }
                            else
                            {
                                $this->tv['_language_error'][] = $this->get_last_error();
                            }
                        }
                    }
                    else
                    {
                        $this->tv['_language_error'] = CValidator::get_errors();
                    }
                }

                $query = "SELECT id, title, abbreviation FROM %prefix%loc_lang WHERE 1=1";

                require_once(BASE_CLASSES_PATH . 'controls/navigator.php'); // base application class
                $nav = new Navigator('language', $query, array('title' => 'title', 'abbreviation' => 'abbreviation'), 'title', $this->Application);

                $header_num = $nav->add_header('Title', 'title');
                $nav->headers[$header_num]->no_escape = false;
                $nav->headers[$header_num]->set_function('set_wrap_language');
                    function set_wrap_language($language)
                    {
                            return wordwrap($language, 15, " ", 1);
                    }
                $nav->headers[$header_num]->set_wrap();
                $nav->set_width($header_num, '80%');

                $header_num = $nav->add_header('Abbreviation', 'abbreviation');
                $nav->headers[$header_num]->no_escape = false;
                $nav->headers[$header_num]->set_function('set_wrap_abbreviation');
                    function set_wrap_abbreviation($abbreviation)
                    {
                            return wordwrap($abbreviation, 15, " ", 1);
                    }
                $nav->headers[$header_num]->set_wrap();
                $nav->set_width($header_num, '20%');

                $nav->set_highlight('id', $this->language_id);
	            $languages = array(1);
	            $nav->set_disabled_list($languages);

                if ($nav->size > 0)
                    $this->tv['language_show_remove'] = true;
                else
                    $this->tv['language_show_remove'] = false;

                if ($this->language_id)
                {
                    $this->tv['language_edit_form'] = true;
                    row_to_vars($this->get_language_by_id($this->language_id), $this->tv);

                    $this->tv['create_date_formatted'] = format_dates_intervals(array($this->tv['create_date']), 'j-M-y g:i A');
                    $this->tv['language_id'] = $this->language_id;
                    $this->tv['language_form_head'] = $this->tv['title'];
                }

                return CTemplate::parse_file(CUSTOM_TEMPLATE_PATH.'admin/_localizer_language.tpl');
            }
        	
        	
        	
        	$id_string = InGetPost('loc_strings_id_string');
            if ($id_string != '') {
                    if (InGetPost('loc_strings_action') == 'save')
                $this->Application->DataBase->update_sql('loc_strings', array('description'=>InGetPost('loc_strings_description'), 'value'=>InGetPost('loc_strings_value', '')), array('id'=>$id_string));
                    $csr = $this->Application->DataBase->select_sql('loc_strings', array('id' => $id_string));
                    $this->Application->template_vars['loc_strings_edit'] = true;
                    $this->Application->template_vars['loc_strings_id_string'] = $csr->get_field('id');
                    $this->Application->template_vars['loc_strings_name'] = $csr->get_field('name');
                    $this->Application->template_vars['loc_strings_description'] = $csr->get_field('description');
                    $this->Application->template_vars['loc_strings_value'] = $csr->get_field('value');
            }
            else
                    $this->Application->template_vars['loc_strings_edit'] = false;

            require_once(BASE_CLASSES_PATH . 'controls/navigator.php'); // base application class
            $query = 'SELECT a.id as id, a.name, a.value, a.description, b.title as lang_name FROM %prefix%loc_strings as a, %prefix%loc_lang as b where a.language_id=b.id and a.auto_created=1';
            $nav = new Navigator('loc_strings', $query, array('name'=>'a.name', 'description' => 'a.description', 'lang_name' => 'b.language_id'), 'name');
            $nav->set_enumeration(false);
            $header_num = $nav->add_header('Int Name', 'name');
            $header_num = $nav->add_header('Value', 'value');
            $header_num = $nav->add_header('Description', 'description');
            $header_num = $nav->add_header('Language', 'lang_name');
           	$nav->set_highlight('id', $id_string);

            return CTemplate::parse_file(CUSTOM_TEMPLATE_PATH.'admin/_localizer_strings.tpl');
        }

        
        function check_install() {
                return ( ($this->DataBase->is_table('loc_lang')) && ($this->DataBase->is_table('loc_strings')) );
        }
        
        function install() {
        	$this->DataBase->custom_sql("DROP TABLE IF EXISTS `loc_strings`");
        	$this->DataBase->custom_sql("DROP TABLE IF EXISTS `loc_lang`");
			$this->DataBase->custom_sql("
				CREATE TABLE `loc_lang` (
					`id` INTEGER(11) NOT NULL AUTO_INCREMENT,
					`title` VARCHAR(64) COLLATE utf8_general_ci DEFAULT NULL,
					`abbreviation` VARCHAR(6) COLLATE utf8_general_ci DEFAULT NULL,
				PRIMARY KEY (`id`)
				)ENGINE=InnoDB
				CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
			");
        	$this->DataBase->custom_sql("INSERT INTO `loc_lang` (`id`, `title`, `abbreviation`) VALUES (1,'English','en'), (2,'Русский','ru')");

			$this->DataBase->custom_sql("
				CREATE TABLE `loc_strings` (
					`id` INTEGER(11) NOT NULL AUTO_INCREMENT,
					`language_id` INTEGER(11) NOT NULL DEFAULT '1',
					`auto_created` INTEGER(11) NOT NULL,
					`name` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '',
					`value` VARCHAR(255) COLLATE utf8_general_ci NOT NULL DEFAULT '',
					`description` VARCHAR(255) COLLATE utf8_general_ci NOT NULL DEFAULT '',
				PRIMARY KEY (`id`),
				UNIQUE KEY `idxu_ls_idname` (`language_id`, `name`),
				KEY `idxu_ls_auto` (`auto_created`),
				KEY `id_lang` (`language_id`),
				CONSTRAINT `ibs_loc_strings_fk` FOREIGN KEY (`language_id`) REFERENCES `loc_lang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
				)ENGINE=InnoDB
				CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
			");
        }
}
?>