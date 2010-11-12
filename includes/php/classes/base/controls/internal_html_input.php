<?
/**
 * @package LLA.Base
 * @version 1.3.5.0
 */
/*
--------------------------------------------------------------------------------
Class CinternalHTMLInput v 1.4.0.0
IT:input control support class

use
        it:input type="[type]" name="[name]" your parameters /

        type:         text - usual text field
                        password - password field
                        textarea - textarea field
                        select - select box
                        radio - radio button (create some radio button with the same names)
                        checkbox - checkbox
                        file - file upload field
                        hidden - hidden field
                        html - WYSIWYG editor

                        submit - submit button
                        reset - reset button

                        button - action button, set name parameter to work as action, then check it through CForm::is_submit($name, $action)
                        image - image button, set src, set name parameter to work as action, then check it through CForm::is_submit($name, $action)
                        anchor - action button, draws anchor instead of button, acts as button

        name:        any name, after validation value with this name appears in template_vars

        confirm:        only for button, image, anchor
                                any string which will be asked when user push button or click anchor
        localizer:  if present - means that confirmation message is taken from localizer

        value:        only for radio, checkbox
                        any value

        priority: template,post,get

        target:        window to which form will be submited

        param:        only for button, anchor, image

        validation: true/false, only for button, anchor, image cause validation, for submit validation is always perfomed

history:
        v 1.4.0.0 - date/time control fixed (VK)
        v 1.3.5.0 - attributes from validator bug fixed (VK)
        v 1.3.4.0 - localizer argument
        v 1.3.3.1 - extra(=true or false) mode  of IT:Date, used for custom control output (AD) if there are no one of the elements of the date
        v 1.3.3.0 - master/slave mode of IT:Date, used for custom control output (AD)
        v 1.3.2.0 - enable/disable support, set_value suppot (VK)
        v 1.3.1.6 - fix DATE: fixing of the problem with default value of Hour and Focus behavior (AD)
        v 1.3.1.5 - fix DATE: fixing of the problem when hours equals to 00 and Focus behavior (AD)
        v 1.3.0 - HTML/XHTML support (VK)
        v 1.2.7 - input TEXT: fix error message if value is array, and display text 'Array' (AD)
        v 1.2.6 - input DATE: add %p - 12h format of an hour and AM/PM combo (AD)
        v 1.2.5 - fix DATE: add fields not present in 'format' as hidden (AD)
        v 1.2.4 - 'link' parameter for IMAGE removed (VK)
        v 1.2.3 - 'link' parameter for IMAGE added (AD)
        v 1.2.2 - validation added (VK)
        v 1.2.1 - 'class' allowed as input extra attributes (type=password & file) (AD)
        v 1.2.0 - param support (VK)
        v 1.1.2 - 'class' allowed as input extra attributes (type=text) (AD)
        v 1.1.1 - add input type DATE (AD)
        v 1.1.0 - array support added (VK)
        v 1.0.4 - priority attribute (VK)
        v 1.0.3 - target attribute (VK)
        v 1.0.2 - confirm for button added (VK)
        v 1.0.1 - image type added (VK)
        v 1.0.0 - created (VK)
--------------------------------------------------------------------------------
*/

function array_to_string($arr)
{
        $str = '';
        foreach ($arr as $key => $value)
                $str .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        return $str;
}

function _strftime($format, $odbc_date)
{
        $matches = array();
        if (preg_match('/^([0-9]{1,4})\-([0-9]{1,2})\-([0-9]{1,2}) {0,1}(([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})){0,1}$/', $odbc_date, $matches) == 0)
                return $odbc_date;
        $year = ((count($matches)>1)?(intval($matches[1])):(0));
        $month = ((count($matches)>2)?(intval($matches[2])):(0));
        $day = ((count($matches)>3)?(intval($matches[3])):(0));
        $hour = ((count($matches)>5)?(intval($matches[5])):(0));
        $minute = ((count($matches)>6)?(intval($matches[6])):(0));
        $second = ((count($matches)>7)?(intval($matches[7])):(0));

        $time = (($hour*60*60) + ($minute*60) + ($second));

        $s = $format;
        $s = str_replace( '%d', (($day<10)?('0'.$day):($day)), $s );
        $s = str_replace( '%e', $day, $s );
        $s = str_replace( '%H', (($hour<10)?('0'.$hour):($hour)), $s );
        $_12h = $hour;
        if ($_12h > 12)
                $_12h = $hour - 12;
        if ($_12h == 0)
                $_12h = 12;
        $s = str_replace( '%I', (($_12h<10)?('0'.$_12h):($_12h)), $s );
        $s = str_replace( '%m', (($month<10)?('0'.$month):($month)), $s );
        $s = str_replace( '%M', (($minute<10)?('0'.$minute):($minute)), $s );
        $s = str_replace( '%p', (( ($time >= 13*60*60) && ($time < 24*60*60) )?('pm'):('am')), $s );
        $s = str_replace( '%S', (($second<10)?('0'.$second):($second)), $s );
        $_year = ($year % 100);
        $s = str_replace( '%y', (($_year<10)?('0'.$_year):($_year)), $s );
        $s = str_replace( '%Y', str_pad($year, 4, '0', STR_PAD_LEFT), $s );
        return $s;
}


/**
 * @package LLA.Base
 */
class CinternalHTMLInput extends CTemplateControl
{
        function CinternalHTMLInput() {
                parent::CTemplateControl('input');
        }

        function process($loop)
        {
                global $app;
                $name = preg_replace('/\[\]/', '', $this->get_input_var('name'));
                $type = strtolower($this->get_input_var('type'));
                $priority = explode(',', $this->get_input_var('priority', 'post,get,template'));
                $value = null;
                static $is_wysiwyg_included = false;
                foreach ($priority as $v) {
                        $v = trim($v);
                        if (strcasecmp($v, 'post')==0)
                                if (in_post($name)) {
                                        $value = InPost($name);
                                        break;
                                }
                        if (strcasecmp($v, 'get')==0)
                                if (in_get($name)) {
                                        $value = InGet($name);
                                        break;
                                }
                        if (strcasecmp($v, 'template')==0)
                                if (isset($app->template_vars[$name])) {
                                                $value = $app->template_vars[$name];
                                        break;
                                }
                }

                if (intval($this->get_input_var('index', -1)) >= 0)
                        $loop = intval($this->get_input_var('index', -1));
                if ((is_array($value)) && ($loop>=0))
                        if (isset($value[$loop]))
                                $value = $value[$loop];
                        else
                                $value = '';

                global $current_form_fields;
                if (!is_array($current_form_fields)) $current_form_fields = array();
                if (!in_array($name, $current_form_fields)) $current_form_fields[] = $name;
                $value = CValidator::format_value($name, $type, $value);
                $out = '';

                if ( (isset($app->template_vars[$name.':hidden'])) && ($app->template_vars[$name.':hidden']) )
                        return '';

                if ( (isset($app->template_vars[$name.':disabled'])) && ($app->template_vars[$name.':disabled']) )
                        $this->input_vars['disabled'] = 'disabled';

                if (isset($app->template_vars[$name.':value']))
                        $this->input_vars['value'] = strtolower($app->template_vars[$name.':value']);

                switch ($type)
                {
                        case 'color':{
                                $out .= '<input type="hidden" id="'.$name.'" name="'.$name.'" value="'.htmlspecialchars($value).'"'.SINGLE_TAG_END.'><div id="'.$name.'_div" class="StartDialogDiv" style="background-color:'. htmlspecialchars($value) . '" onclick="ColorDlg.show(\''.$name.'\');" ></div><script language="JavaScript" type="text/javascript">ColorDlg_BasePath=\''.$app->tv['JS'].'dialogs/color/\'</script>';
                                break;
                        }
                        case 'htmlarea':
                        {
                        		if (!$is_wysiwyg_included) {
					                $out .= '
		                            <!-- tinyMCE -->
		                            <script language="javascript" type="text/javascript" src="'.$app->template_vars['JS'].'tiny_mce/tiny_mce.js"></script>
		                            <script language="javascript" type="text/javascript">
		                                tinyMCE.init({
		                                    mode : "specific_textareas",
		                                    editor_selector : "mceEditor",
		                                    theme : "'.((strlen($this->input_vars['theme']) > 0) ? $this->input_vars['theme'] : "advanced").'",
		                                    plugins : "advlink,searchreplace,contextmenu,paste,insertdatetime,preview,print,fullscreen",
		                                    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,separator,sub,sup,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,formatselect,fontselect,fontsizeselect,forecolor,backcolor,|,insertdate,inserttime",
		                                    theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,separator,undo,redo,separator,search,replace,separator,bullist,numlist,separator,outdent,indent,blockquote,separator,link,unlink,anchor,charmap,hr,removeformat,cleanup,|,preview,print,fullscreen,|,code",
		                                    theme_advanced_buttons3 : "",
		                                    theme_advanced_toolbar_location : "top",
		                                    theme_advanced_toolbar_align : "left",
		                                    theme_advanced_path_location : "bottom",
		                                    content_css : "'.$app->template_vars['CSS'].'weditor.css",
		                                    extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
		                                    external_image_list_url : "'.$app->template_vars['JS'].'tiny_mce/documents-wysiwyg-imagelist.js.php"
		                                });
		                            </script>
		                            <!-- /tinyMCE -->
					                ';
					                $is_wysiwyg_included = true;
                        		}
                                $out .= '<textarea mce_editable="true" class="mceEditor"';
                                $out .= ( (isset($this->input_vars['class']))?(''):(' class="input"') );
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'priority') != 0) &&
                                                (strcasecmp($k, 'type') != 0)
                                                )
                                                $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                                //$out .= CValidator::get_html($name, $type);
                                $out .= '>' . htmlspecialchars(strval($value)) . '</textarea>';
                                break;
                        } // end of 'textarea'

                        case 'disabled':
                        {
                                $out .= '<input type="hidden" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'"'.SINGLE_TAG_END.'><span';
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'name')!=0) &&
                                                (strcasecmp($k, 'type')!=0) &&
                                                (strcasecmp($k, 'value')!=0) &&
                                                (strcasecmp($k, 'priority')!=0)
                                                )
                                                $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                                $out .= '>';
                                $out .= htmlspecialchars($value);
                                $out .= '</span>';
                                break;
                        }
                        case 'reset':
                        {
                                if (isset($this->input_vars['src']))
                                {
                                        global $current_running_form ;
                                        $out .= '<a href="javascript:document.forms.'.$current_running_form.'.reset();"><img';
                                        foreach ($this->input_vars as $k => $v)
                                                if (
                                                        (strcasecmp($k, 'type')!=0) &&
                                                        (strcasecmp($k, 'priority')!=0)
                                                        )
                                                        $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                                        if (!isset($this->input_vars['alt']))
                                                $out .= ' alt=""';
                                        $out .= ' border="0"'.SINGLE_TAG_END.'></a>';
                                }
                                else
                                {
                                        $out .= '<input type="reset"';
                                        foreach ($this->input_vars as $k => $v)
                                                if (
                                                        (strcasecmp($k, 'type')!=0) &&
                                                        (strcasecmp($k, 'priority')!=0)
                                                        )
                                                        $out .= ' ' . $k . '="' . ($v) . '"';
                                        $out .= ''.SINGLE_TAG_END.'>';
                                }
                                break;
                        }
                        case 'submit':
                        {
                                $out .= '<input type="submit"';
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'type')!=0) &&
                                                (strcasecmp($k, 'validation')!=0) &&
                                                (strcasecmp($k, 'priority')!=0)
                                                )
                                                $out .= ' ' . $k . '="' . ($v) . '"';
                                $out .= ''.SINGLE_TAG_END.'>';
                                break;
                        } // end of 'submit'
                        case 'button':
                        {
                                $out .= '<input type="button"';
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'type') != 0) &&
                                                (strcasecmp($k, 'priority')!=0) &&
                                                (strcasecmp($k, 'target') != 0) &&
                                                (strcasecmp($k, 'param') != 0) &&
                                                (strcasecmp($k, 'validation')!=0) &&
                                                (strcasecmp($k, 'confirm') != 0)
                                                )
                                                $out .= ' ' . $k . '="' . ($v) . '"';
                                //if (isset($this->input_vars['name']))
                                {
                                        $out .= ' onclick="';
                                        if (isset($this->input_vars['confirm']))
                                        {
                                                if (isset($this->input_vars['localizer']))
                                                {
                                                        $str = $GLOBALS['app']->Localizer->get_string($this->input_vars['confirm']);
                                                        $out .= 'if (confirm(\''.js_escape_string($str).'\')) ';
                                                }
                                                else
                                                        $out .= 'if (confirm(\''.js_escape_string($this->input_vars['confirm']).'\')) ';
                                        }
                                        $prm = $this->get_input_var('param');
                                        $out .= $GLOBALS['current_running_form'].'_submit(\''.$this->get_input_var('name', '').'\', \''.$this->get_input_var('target').'\', \''.$prm.'\','.$this->get_input_var('validation', '0').');"';
                                }
                                $out .= ''.SINGLE_TAG_END.'>';
                                break;
                        } // end of 'button'
                        case 'anchor':
                        {
                                $out .= '<a';
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'type') != 0) &&
                                                (strcasecmp($k, 'name') != 0) &&
                                                (strcasecmp($k, 'priority') != 0) &&
                                                (strcasecmp($k, 'confirm') != 0) &&
                                                (strcasecmp($k, 'target') != 0) &&
                                                (strcasecmp($k, 'param') != 0) &&
                                                (strcasecmp($k, 'validation')!=0) &&
                                                (strcasecmp($k, 'value') != 0)
                                                )
                                                $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                                $out .= ' href="JavaScript: ';
                                if (isset($this->input_vars['confirm']))
                                {
                                        if (isset($this->input_vars['localizer']))
                                        {
                                                $str = $GLOBALS['app']->Localizer->get_string($this->input_vars['confirm']);
                                                $out .= 'if (confirm(\''.js_escape_string($str).'\')) ';
                                        }
                                        else
                                                $out .= 'if (confirm(\''.js_escape_string($this->input_vars['confirm']).'\')) ';
                                }
                                $prm = $this->get_input_var('param');
                                $out .= $GLOBALS['current_running_form'].'_submit(\''.$this->get_input_var('name').'\', \''.$this->get_input_var('target').'\', \''.$prm.'\','.$this->get_input_var('validation', '0').');"';
                                $out .= '>';
                                $out .= $this->input_vars['value'];
                                $out .= '</a>';
                                break;
                        } // end of 'anchor'
                        case 'image':
                        {
                                $out .= '<a';
                                if (isset($this->input_vars['class']))
                                        $out .= ' class="' . htmlspecialchars($this->input_vars['class']) . '"';
                                $out .= ' href="JavaScript: ';
                                if (isset($this->input_vars['confirm']))
                                        $out .= 'if (confirm(\''.htmlspecialchars($this->input_vars['confirm']).'\')) ';
                                $prm = $this->get_input_var('param');
                                $out .= $GLOBALS['current_running_form'].'_submit(\''.$this->get_input_var('name').'\', \''.$this->get_input_var('target').'\', \''.$prm.'\','.$this->get_input_var('validation', '0').');"';
                                $out .= '>';
                                $out .= '<img';
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'type') != 0) &&
                                                (strcasecmp($k, 'priority') != 0) &&
                                                (strcasecmp($k, 'name') != 0) &&
                                                (strcasecmp($k, 'value') != 0) &&
                                                (strcasecmp($k, 'validation')!=0) &&
                                                (strcasecmp($k, 'confirm') != 0)
                                                )
                                                $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                                if (!isset($this->input_vars['alt']))
                                        $out .= ' alt=""';
                                $out .= ' border="0"'.SINGLE_TAG_END.'></a>';
                                break;
                        } // end of 'image'
                        case 'text':
                        {
                                if (is_array($value)) $value = 'Array';
                                $out .= '<input type="text"';
                                if (!$this->in_input_vars('value')) $out .= ' value="'.htmlspecialchars($value).'"';
                                if (!$this->in_input_vars('class')) $out .= ' class="input"';
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'type') != 0) &&
                                                (strcasecmp($k, 'value') != 0) &&
                                                (strcasecmp($k, 'priority') != 0)
                                                )
                                                $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                                $out .= array_to_string(CValidator::get_html($name, $type));
                                $out .= ''.SINGLE_TAG_END.'>';
                                break;
                        } // end of 'text'
                        case 'date':
                        {
                                // input is in ODBC date format
                                // output - YYYY-MM-DD - when no %h in format
                                // output - YYYY-MM-DD HH:MM:SS - %h exists in format

                                $format = ( (isset($this->input_vars['format'])) ? ($this->input_vars['format']) : ('%m/%d/%Y') );
                                if( trim($value) !== '')
                                        $value = _strftime($format, $value); // $value is in ODBC foramt
                                $out .= '<input type="text"';
                                if (!$this->in_input_vars('value')) $out .= ' value="'.htmlspecialchars($value).'"';
                                if (!$this->in_input_vars('class')) $out .= ' class="input"';
                                if (!$this->in_input_vars('id')) $out .= ' id="'.htmlspecialchars($name).'"';
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'type') != 0) &&
                                                (strcasecmp($k, 'value') != 0) &&
                                                (strcasecmp($k, 'id') != 0) &&
                                                (strcasecmp($k, 'button_class') != 0) &&
                                                (strcasecmp($k, 'priority') != 0)
                                                )
                                                $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                                $out .= array_to_string(CValidator::get_html($name, $type));
                                $out .= ''.SINGLE_TAG_END.'> ';
                                $out .= '<input value=".." type="button" id="' . htmlspecialchars($name) . '_activator" name="' . htmlspecialchars($name) . '_activator"';
                                if ($this->in_input_vars('button_class')) $out .= ' class="'.htmlspecialchars($this->input_vars['button_class']).'"';
                                $out .= ''.SINGLE_TAG_END.'>';

                                $out .= "<script language=\"JavaScript\" type=\"text/javascript\"><!--\nCalendar.setup({inputField: ('" . $name . "'), date: ('" . $value . "'), ifFormat: '".$format."', daFormat: '".$format."', showsTime: ".((stristr($format, '%h')!==FALSE)?('true'):('false')).", timeFormat: '".((stristr($format, '%p')!==FALSE)?('12'):('24'))."', button: ('" . $name . "_activator'), singleClick: true});//--></script>";
                                break;
                        } // end of 'date'
                        case 'file':
                        {
                                $out .= '<input type="file"';
                                $out .= ( (isset($this->input_vars['class']))?(''):(' class="input"') );
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'priority') != 0) &&
                                                (strcasecmp($k, 'type') != 0)
                                                )
                                                $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                                $out .= array_to_string(CValidator::get_html($name, $type));
                                $out .= ''.SINGLE_TAG_END.'>';
                                break;
                        } // end of 'text'
                        case 'hidden':
                        {
                                $out .= '<input type="hidden"';
                                if (!$this->in_input_vars('value'))
                                        $out .= ' value="'.htmlspecialchars($value).'"';
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'priority') != 0) &&
                                                (strcasecmp($k, 'type') != 0)
                                                )
                                                $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                                $out .= array_to_string(CValidator::get_html($name, $type));
                                $out .= ''.SINGLE_TAG_END.'>';
                                break;
                        } // end of 'hidden'
                        case 'password':
                        {
                				$showpass = (isset($this->input_vars['showpass']) && strcasecmp($this->input_vars['showpass'], 'true')==0);
                                if ($showpass) $out .= '<input type="password" value="'.htmlspecialchars($value).'"'; else $out .= '<input type="password" value=""';
                                $out .= ( (isset($this->input_vars['class']))?(''):(' class="input"') );
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'priority') != 0) &&
                                                (strcasecmp($k, 'showpass') != 0) &&
                                                (strcasecmp($k, 'type') != 0)
                                                )
                                                $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                                $out .= array_to_string(CValidator::get_html($name, $type));
                                $out .= ''.SINGLE_TAG_END.'>';
                                break;
                        } // end of 'password'
                        case 'select':
                        {
                				$confirm = false;
                                $out .= '<select';
                                $out .= ( (isset($this->input_vars['class']))?(''):(' class="input"') );
                                if (isset($this->input_vars['multiple'])) $out .= ' name="'.$this->input_vars['name'].'[]"';
                                else $out .= ' name="'.$this->input_vars['name'].'"';
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'priority') != 0) &&
                                                (strcasecmp($k, 'name') != 0) &&
                                                (strcasecmp($k, 'type') != 0)
                                                )
                                                $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                                $out .= '>';
                                if (!isset($app->template_vars[$name.":select"]))
                                        system_die("Specify data for select [".$name."] with set_select_data");
                                if (!is_array($app->template_vars[$name.":select"]))
                                        system_die("Specify data for select [".$name."] with set_select_data");
                                if ($app->template_vars[$name.':escaped']){
                                        foreach ($app->template_vars[$name.":select"] as $k => $v)
                                                if ( ( (is_array($value)) && (in_array($k, $value)) ) || ( (!is_array($value)) && (strcmp($value, $k) == 0) ) ){
                                                        $out .= '<option value="'.($k).'" selected="selected">'.($v).'</option>';
                                                }else
                                                        $out .= '<option value="'.($k).'">'.($v).'</option>';
                                }else{
                                        foreach ($app->template_vars[$name.":select"] as $k => $v)
                                                if ( ( (is_array($value)) && (in_array($k, $value)) ) || ( (!is_array($value)) && (strcmp($value, $k) == 0) ) ){
                                                        $out .= '<option value="'.htmlspecialchars($k).'" selected="selected">'.htmlspecialchars($v).'</option>';
                                                }else
                                                        $out .= '<option value="'.htmlspecialchars($k).'">'.htmlspecialchars($v).'</option>';
                }
                                $out .= '</select>';
                                break;
                        } // end of 'select'
                        case 'textarea':
                        {
                                $out .= '<textarea';
                                $out .= ( (isset($this->input_vars['class']))?(''):(' class="input"') );
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'priority') != 0) &&
                                                (strcasecmp($k, 'type') != 0)
                                                )
                                                $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                                $out .= array_to_string(CValidator::get_html($name, $type));
                                $out .= '>' . htmlspecialchars(strval($value)) . '</textarea>';
                                break;
                        } // end of 'textarea'
                        case 'radio':
                        {
                                $out .= '<input type="radio"';
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'priority') != 0) &&
                                                (strcasecmp($k, 'type') != 0)
                                                )
                                                $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                                if (strcmp($value, $this->get_input_var('value')) == 0)
                                        $out .= ' checked="checked"';
                                $out .= array_to_string(CValidator::get_html($name, $type));
                                $out .= ''.SINGLE_TAG_END.'>';
                                break;
                        } // end of 'radio'
                        case 'checkbox':
                        {
                                $out .= '<input type="checkbox"';
                                foreach ($this->input_vars as $k => $v)
                                        if (
                                                (strcasecmp($k, 'priority') != 0) &&
                                                (strcasecmp($k, 'type') != 0)
                                                )
                                                $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                                if ($this->in_input_vars('value'))
                                {
                                        if ($value == $this->get_input_var('value'))
                                                $out .= ' checked="checked"';
                                }
                                else
                                        $out .= ' value="1"';
                                $out .= array_to_string(CValidator::get_html($name, $type));
                                $out .= ''.SINGLE_TAG_END.'>';
                                break;
                        } // end of 'checkbox'
                        default:
                        {
                                $out .= 'invalid type of the input control "'.$type.'"';
                        }
                } // end of switch
                return $out;
        }
}
?>