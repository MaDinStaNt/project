<?
/*
--------------------------------------------------------------------------------
Class CSimpleDataOutput v 1.1.0
template control for array output:
parametrs:
	var_name	- name of the variable in template vars - array or recordset
	limit	    - number of items to show
	template	- template file name
	add_block	- 'true' - always add block_begin and block_end
				  'false' - do not use block_begin and block_end
				  '' - add block_begin and block_end only if array has items
	add_input_vars  - 'true' / 'false'

history:
	v 1.1.0 - Loops support (AD)
	v 1.0.2 - add_input_vars='true' alloy use in template
    		  input vars (AD)
	v 1.0.1 - add method set_template_file_name,
			  and can set user definded control name (AD)
	v 1.0.0 - created (AD)
--------------------------------------------------------------------------------
*/

require_once(FUNCTION_PATH . 'functions.tv.php');

class CSimpleDataOutput extends CTemplateControl
{
	var $tpl_file_name = '';
	var $cols = 0;
	var $rows = 0;

	function CSimpleDataOutput(&$html_page, $name = 'SimpleDataOutput')
	{
		parent::CTemplateControl($name);
		$this->template_vars = &$html_page->template_vars;
	}

	function set_template_file_name( $tpl_file_name )
	{
		$this->tpl_file_name = $tpl_file_name;
	}

	function set_cols( $value )
	{
		$this->cols = $value;
	}

	function set_rows( $value )
	{
		$this->rows = $value;
	}

	function process($loop_id = -1)
	{
		global $app;

		$this->template_vars = $this->template_vars;

		$var_name = $this->get_input_var('var_name');
		$tpl_file_name = $this->get_input_var('template');
		$limit = $this->get_input_var('limit', -1);
		$add = $this->get_input_var('add_block', '');

		if ( $tpl_file_name == '' )
			if ( $this->tpl_file_name == '' )
				return '';
			else
				$tpl_file_name = $this->tpl_file_name;

		$cols = $this->get_input_var('cols', $this->cols );
		$rows = $this->get_input_var('rows', $this->rows );

		//if ( !isset($this->template_vars[$var_name]) ) return ' ';
		//$data = $this->template_vars[$var_name];

		$var_name = $this->get_input_var('var_name', '');
        if ( ($var_name != '') && (isset($this->template_vars[$var_name])) )
		{
			if ( ($loop_id >= 0) && (!isset($this->template_vars[$var_name][$loop_id])) )
			{
//				echox($var_name);
//				echox($loop_id);
//				print_arr(array_keys($this->template_vars[$var_name]));
//				system_die('Invalid loop_id');
				$data = '';
			}
			else
				if ($loop_id == -1)
		        	$data = &$this->template_vars[$var_name];
				else
					$data = &$this->template_vars[$var_name][$loop_id];
		}
    	else
        	$data = '';

		if ( is_object($data) ) $data = $data->get_2darray();

		if ( $limit > -1 && is_array($data))  $data = array_slice($data, 0, $limit);

        //add control input vars
        if ( $this->get_input_var('add_input_vars', '' ) == 'true' )
			if (is_array($data))
		        foreach ( $data as $k => $v )
				{
		        	if ( is_array($v) )
		                $data[$k] = array_merge($this->input_vars, $data[$k]);
		            else
		            	$data[$k] = array_merge($this->input_vars, array('key'=> $k, 'value'=> $v) );
				}

		array_to_vars( $data, $this->template_vars, 'rows', $cols, $rows );

		//echox(count($data));

        $this->template_vars['x_input_vars'] = $this->input_vars;
		$this->template_vars['x_show_items'] = is_array($data) ? ((count($data) > 0) ? true : false) : false;
		$this->template_vars['x_show_begin'] = ( $add == '' ) ? $this->template_vars['x_show_items'] : ($add=='true') ? true : false;
		$this->template_vars['x_show_end']   = ( $add == '' ) ? $this->template_vars['x_show_items'] : ($add=='true') ? true : false;

		//print_arr($this->template_vars);

		$tpl_file_name = CUSTOM_TEMPLATE_PATH.$tpl_file_name;
		return CTemplate::parse_file($tpl_file_name, $this->template_vars);
	}
}
?>