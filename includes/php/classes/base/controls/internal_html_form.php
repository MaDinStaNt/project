<?
/**
 * @package LLA.Base
 * @version 1.6
 */
/**
 */
/*
--------------------------------------------------------------------------------
Only for internal purposes, provides support for public IT:form control

knowleges:
        - it:input type="button" by default don't call js form validation, by default validation is performed only for submit or image with empty action

history:
        v 1.6 - correct action attribute handling (VK)
        v 1.5 - HTML/XHTML (VK)
        v 1.4 - variables reduced (VK)
        v 1.3 - impoved get method support (VK)
        v 1.2 - multiform added (VK)
        v 1.1 - action support added (VK)
        v 1.0 - created (VK)
--------------------------------------------------------------------------------
*/

define('FORM_CRLF', "\r\n");

$current_running_form = '';
$current_running_form_get = false;
$current_running_form_index = 0;
$current_form_fields = array();

/**
 * @package LLA.Base
 */
class CinternalHTMLForm extends CTemplateControl {
        function CinternalHTMLForm() {
                parent::CTemplateControl('form');
        }
        function process() {
                global $current_running_form, $current_running_form_get, $current_form_fields, $current_running_form_index;
                $out = '';
                /*<script type="text/javascript" language="javascript" src="<%=JS%>validator.js"></script>*/
                if ($this->get_input_var('begin') == 'true') {
                        $form_name = $this->get_input_var('name');
                        if ('' == trim($form_name))
                                $form_name = $this->get_input_var('id');
                        if ('' == trim($form_name))
                                system_die('IT:Form should have name or id attribute');
                        $current_running_form_index++;
                        $current_form_fields = array();
                        $current_running_form = ($this->get_input_var('multiform') == true)?('form_'.$current_running_form_index):($form_name);
                        $current_running_form_get = (strcasecmp($this->get_input_var('method'), 'get') == 0);
                        $out .= '<script language="JavaScript" type="text/javascript"><!--' . FORM_CRLF;
//                        $out .= 'var '.$current_running_form.'noa=0;' . FORM_CRLF;
//                        $out .= 'function '.$current_running_form.'_gCheck(f) {' . FORM_CRLF;
//                        $out .= 'if ('.$current_running_form.'noa) return true;';
//                        $out .= 'if (!'.$current_running_form.'_gfieldsCheck(f)) return false;';
//                        if ($this->get_input_var('add_js') != '') $out .= CTemplate::parse_file(CUSTOM_TEMPLATE_PATH . $this->get_input_var('add_js'));
//                        $out .= 'return true;';
//                        $out .= '}' . FORM_CRLF;
                        $out .= 'function '.$current_running_form.'_submit(act,trg,prm,v) {' . FORM_CRLF;
                        $out .= 'var f=document.forms.'.$current_running_form.';';
                        $out .= 'f.target=trg;';
                        $out .= 'f.elements.param2.value=act;';
                        $out .= 'f.elements.param1.value=prm;';
                        $out .= 'if ((act!=\'\') && (!v)) {';
                        $out .= $current_running_form.'noa=1;f.submit();';
//                        $out .= '} else if ('.$current_running_form.'_gCheck(f)) {';
                        $out .= '} else {';
                        $out .= $current_running_form.'noa=1;f.submit();}';
                        $out .= '}';
                        $out .= '//--></script>' . FORM_CRLF;
                        $out .= '<form name="'.htmlspecialchars($current_running_form).'" id="'.htmlspecialchars($current_running_form).'"';
                        if (!$this->in_input_vars('method')) $out .= ' method="post"';
                        if (!$this->in_input_vars('action'))
                                if (strlen($this->Application->get_server_var('QUERY_STRING')) > 0)
                                        $out .= ' action="' . htmlspecialchars($this->Application->get_server_var('PHP_SELF') . '?' . $this->Application->get_server_var('QUERY_STRING')) . '"';
                                else
                                        $out .= ' action="' . htmlspecialchars($this->Application->get_server_var('PHP_SELF')) . '"';

                        foreach ($this->input_vars as $k => $v)
                                if (
                                        ($k != 'begin') &&
                                        ($k != 'end') &&
                                        ($k != 'onsubmit') &&
                                        (strcasecmp($k, 'name') != 0) &&
                                        (strcasecmp($k, 'add_js') != 0) &&
                                        (strcasecmp($k, 'multiform') != 0)
                                        )
                                        $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
                        $onsubmit = $this->get_input_var( 'onsubmit' );
                        if ( $onsubmit != '' )
                                $onsubmit .= ';';
//                        $out .= ' onsubmit="' . $onsubmit . ' return '.$current_running_form.'_gCheck(this);">';
                        $out .= '>';
                        if ($this->get_input_var('ajax_validation') == 'true') {
                        	$out .= '<input type="hidden" name="pageclass" id="pageclass" value="'.$this->Application->Router->CurrentPage['class'].'" />';
                        	$out .= '<script type="text/javascript" language="javascript" src="/js/validator.js"></script>';
                        }
                    	$out .= '<script type="text/javascript" language="javascript">';
                    	$out .= '$(function(){';
                    	$out .= '$("#'.htmlspecialchars($form_name).'").submit(function() {';
                    	$out .= '$(":submit").css(\'cursor\', \'wait\');';
                    	$out .= '$(":submit").parent(\'div\').addClass(\'submit_inact\');';
                    	$out .= '$(":submit").attr(\'disabled\', \'true\');';
                    	$out .= '});';
                    	$out .= '});';
                    	$out .= '</script>';
                    
                        $out .= '<input type="hidden" name="formname" id="formname" value="' . htmlspecialchars($form_name) . '"'.SINGLE_TAG_END.'>';
                        $out .= '<input type="hidden" name="param2" value=""'.SINGLE_TAG_END.'>';
                        $out .= '<input type="hidden" name="param1" value=""'.SINGLE_TAG_END.'>';
                }
                elseif ($this->get_input_var('end') == 'true') {
//                        $out .= '<script language="JavaScript" type="text/javascript"><!--' . FORM_CRLF;
//                        $out .= 'function '.$current_running_form.'_gfieldsCheck(f) {'.CValidator::get_js($current_running_form, $current_form_fields).'return true;}//--></script>';
                        $out .= (!$current_running_form_get)?('<input type="hidden" name="_it_fs" value="' . base64_encode(implode(',', $current_form_fields)) . '"'.SINGLE_TAG_END.'>'):('<input type="hidden" name="_it_fs" value="g"'.SINGLE_TAG_END.'>');
                        $out .= '</form>';
                        $current_running_form = '';
                        $current_running_form_get = false;
                        $current_form_fields = array();
                }
                else system_die('IT:Form control error: specify begin or end attribute');
                return $out;
        }
}
?>