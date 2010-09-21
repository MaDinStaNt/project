<?
/**
 * @package LLA.Base
 */
/*
--------------------------------------------------------------------------------
Class CSimpleArrayOutput v 1.0
template control for array output:
parameters:
	array		- name of the array variable in template vars, if variable is not array it is substituted to array($value)
	block_begin	- html tags for the begin of the whole block
	block_end	- html tags for the end of the whole block
	item_begin	- html tags for the begin of the every item in array
	item_end	- html tags for the end of the every item in array
	add_block	- 'true' - always add block_begin and block_end
				  'false' - do not use block_begin and block_end
				  '' - add block_begin and block_end only if array has items

history:
	v 1.0 - created (VK)
--------------------------------------------------------------------------------
*/

/**
 * @package LLA.Base
 */
class CSimpleArrayOutput extends CTemplateControl
{
	function CSimpleArrayOutput()
	{
		parent::CTemplateControl('SimpleArrayOutput');
	}

	function create_html($i, $bb, $be, $ib, $ie, $add, $is_html = false)
	/*
	$i - name of the array in template vars
	$bb - begin of the block
	$be - end of the block
	$ib - begin of the item
	$ie - end of the item
	$add - 'true'/'false'/'' - behaviour of the block addition
	*/
	{
		global $app;

		$out = '';
		$arr = array();
		if (isset($app->template_vars[$i]))
			if (is_array($app->template_vars[$i]))
				$arr = $app->template_vars[$i];
			else
				$arr = array($app->template_vars[$i]);

		foreach ($arr as $v)
			$out .= $ib . ($is_html?$v:htmlspecialchars($v)) . $ie;
		if ( ( ($add == '') && ($out != '') ) || ($add == 'true') )
			$out = $bb . $out . $be;
			
		if ($this->return && strlen($this->return) > 0) {
			$out .= "<script type=\"text/javascript\" src=\"".$app->template_vars['JS']."jquery/jquery.timer.js\"></script>";
			$out .= "<script type=\"text/javascript\" charset=\"utf-8\"> $(document).ready(function(){ $(\":body\").css('cursor', 'wait'); $.timer(2000, function (timer) { window.location.href = '".$this->return."'; timer.stop(); }); }); </script>";
		}
		return $out;
	}

	function process()
	{
		$i = $this->get_input_var('array');
		$bb = $this->get_input_var('block_begin');
		$be = $this->get_input_var('block_end');
		$ib = $this->get_input_var('item_begin');
		$ie = $this->get_input_var('item_end');
		$add = $this->get_input_var('add_block');
		$is_html = $this->get_input_var('is_html');
		$this->return = $this->get_input_var('return');
			
		return $this->create_html($i, $bb, $be, $ib, $ie, $add, $is_html);
	}
}
?>