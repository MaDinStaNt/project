<?php
/**
 * @package LLA.Base
 */
/**
 * formats one value for CSV format
 */
function format_csv_value($value)
{
	return ((strpos($value, '"') !== false)?(str_replace('"', '""', $value)):($value));
}
/**
 * can be used for recordset_to_vars_callback as callback function
 */
function format_export_csv_data(&$tv, &$r, $prefix)
{
	global $app;
	foreach ($r->Fields as $k => $v)
		$app->template_vars[$prefix.$k][count($app->template_vars[$prefix.$k])-1] = format_csv_value($r->get_field($k));
}
?>