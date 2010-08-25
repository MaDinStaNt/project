<?
/**
 * @package LLA.Deprecated
 * @deprecated
 */
/**
 */
/*
--------------------------------------------------------------------------------
Templates extra functions v 1.0.3 (DEPRECATED)

array_to_vars(&$data, &$tv, $counter_varname, $cols = 0, $rows = 0)
	prepare vars for template parser and create extra vars
    such row number, start/end of row/col/cell

	$counter_varname - create counter
    $cols - calculate table mode: create extra vars
    $rows - calculate table mode: create extra vars

array_merge_recursive2()
    If arrays have the same numeric or string key,
    the later value will overwrite the original value.

    Original array_merge_recursive() will not overwrite the original value
    if arrays have numeric key.

array_merge_rows($arr, $sample)
	Usefull to restore array structure, add not existing elements
    $sample - default element values of row (array)

history:
	v 1.0.3 - add pager_calculate() (AD)
	v 1.0.2 - add array_merge_rows() (AD)
    v 1.0.1 - add array_merge_recursive2() (AD)
    v 1.0.0 - created (AD)
--------------------------------------------------------------------------------
*/

// ultra-super-giga smart function
function array_to_vars(&$data, &$tv, $counter_varname, $cols = 0, $rows = 0, $prefix='') {
	if (!is_array($data)) {
		$tv[$prefix.$counter_varname] = 0;
		return false;
	}

    reset($data);
    if (is_array(current($data)))
		foreach (current($data) as $k2 => $v2) $tv[$prefix.$k2] = array();
	else{
		$tv[$prefix.'value'] = array();
        $tv[$prefix.'key'] = array();
    }

    $tv[$prefix.'x_bool'] = array();
    $tv[$prefix.'x_num'] = array();
	$tv[$prefix.'x_item_first'] = array();
	$tv[$prefix.'x_item_last'] = array();
	$tv[$prefix.'x_row_item_first'] = array();
	$tv[$prefix.'x_row_item_last'] = array();
	$tv[$prefix.'x_col'] = array();
	$tv[$prefix.'x_row'] = array();
	$tv[$prefix.'x_start_row'] = array();
	$tv[$prefix.'x_end_row'] = array();
	$tv[$prefix.'x_row_last'] = array();
	$tv[$prefix.'x_row_first'] = array();
	$tv[$prefix.'x_cell'] = array();
	$tv[$prefix.$counter_varname] = count($data);
	
    $i = 0;
    $t_x_col = 0;
	$t_x_row = 0;
	
    $max_row = (( ($rows <= 0) && ($cols > 0) ) ? (ceil( $tv[$prefix.$counter_varname] / $cols )) : ($rows));
    
    foreach ($data as $k => $v)
    {
        $i++;
		if ( $rows <= 0 )
			$t_x_row = ($cols>0) ? ceil( $i / $cols ) : $i;
		else{
			$t_x_row = ceil( $i / $cols );  
			if ( $t_x_row > $rows ) break;
		}

		$tv[$prefix.'x_row'][] = $t_x_row;

		if ( $cols <= 0 ) {
			$t_x_col = $tv[$prefix.'x_col'][] = 1;
			$tv[$prefix.'x_start_row'][] = true;
			$tv[$prefix.'x_end_row'][] = true;
		}else{
			$t_x_col = $tv[$prefix.'x_col'][] = ( (($i % $cols) == 0) ? ($cols) : ($i % $cols) );
			$tv[$prefix.'x_start_row'][] = ( $t_x_col == 1 ) ? true : false;
			$tv[$prefix.'x_end_row'][] = ( $t_x_col == $cols ) ? true : false;
		}

		$tv[$prefix.'x_cell'][] = true;
        array_push($tv[$prefix.'x_bool'], ($i&1));
        array_push($tv[$prefix.'x_num'],  $i);
		array_push($tv[$prefix.'x_item_first'],  ($i == 1) ? true : false );
		array_push($tv[$prefix.'x_item_last'],  ($i == $tv[$prefix.$counter_varname]) ? true : false );
		array_push($tv[$prefix.'x_row_item_first'],  ( $t_x_col == 1 ) ? true : false );
		array_push($tv[$prefix.'x_row_item_last'],  ( $t_x_col == $cols ) ? true : false );
		array_push($tv[$prefix.'x_row_last'],  ( $t_x_row == $max_row ) ? true : false );
		array_push($tv[$prefix.'x_row_first'],  ( $t_x_row == 1 ) ? true : false );

        if (is_array($v))
	        foreach ($v as $k2 => $v2)
	            array_push($tv[$prefix.$k2], $v[$k2]);
        else
		{
			$tv[$prefix.'value'][]= $v;
            $tv[$prefix.'key'][]= $k;
        }
    }

	if ( ($cols > 0) && ($t_x_col < $cols) )
		for ( $i = $t_x_col+1; $i <= $cols; $i++ ) {
			//echox($cols);
			$tv[$prefix.'x_cell'][] = false;
			$tv[$prefix.'x_num'][] = false;
			$tv[$prefix.'x_start_row'][] = false;
			$tv[$prefix.'x_end_row'][] = ( $i == $cols ) ? true : false;
			$tv[$prefix.'x_row_item_first'][] =  ( $t_x_col == 1 ) ? true : false;
			$tv[$prefix.'x_row_item_last'][] = ( $i == $cols ) ? true : false ;
			$tv[$prefix.'x_row_last'][] =  true;
			$tv[$prefix.'x_row_first'][] =  ( $t_x_row == 1 ) ? true : false ;
		}

	if ( isset($tv[$prefix.'x_num']) )
		$tv[$prefix.$counter_varname] = count($tv[$prefix.'x_num']);
	else
		$tv[$prefix.$counter_varname] = 0;
}

function array_merge_recursive2() {
	$res = null;
   	foreach(func_get_args() as $arr) if (is_array($arr)) {
    	if (!is_array($res)) {
       		$res = $arr;
        	continue;
     	}

        foreach($arr as $k => $val) {
	        if (is_array($val) && isset($res[$k]))
	            $res[$k] = array_merge_recursive2($res[$k],$val);
	        else
	            $res[$k] = $val;
	    }
   	}
	return $res;
}

function array_merge_rows($arr, $sample) {
	if (is_array($arr) && is_array($sample))
	    foreach($arr as $k => $v) {
	        if (!is_array($v)) $v = array();
	        $arr[$k] = array_merge($sample, $v);
	    }
    return $arr;
}
?>