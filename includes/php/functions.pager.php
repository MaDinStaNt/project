<?php
/**
 * @package LLA.Deprecated
 * @deprecated
 */
/**
 */
function pager_calculate($page, $items, $items_count){
	// return array for SimpleDataOutput(tm)
	if ($page == 0) $items_count = $items;
	//$pages_count = ($items > 0) ? ceil($items_count / $items) : 1;
    $pages_count = ceil($items_count / $items);
    if ($pages_count < 2) return;

	$pager = array();
		// view all
		if ( $pages_count > 1 && $page != 0)
			$pager[] = array(
				'x_move_prev' => false,
				'x_move_next' => false,
				'x_move_first' => false,
				'x_move_last' => false,
				'page' => 1,
				'x_current' => false,
				'x_not_current' => false,
				'x_view_all' => true,
			);
		// move first
		if ( $page > 2)
			$pager[] = array(
				'x_move_prev' => false,
				'x_move_next' => false,
				'x_move_first' => true,
				'x_move_last' => false,
				'page' => 1,
				'x_current' => false,
				'x_not_current' => false,
				'x_view_all' => false,
			);
		// move prev
		if ( $page > 1)
			$pager[] = array(
				'x_move_prev' => true,
				'x_move_next' => false,
				'x_move_first' => false,
				'x_move_last' => false,
				'page' => $page-1,
				'x_current' => false,
				'x_not_current' => false,
				'x_view_all' => false,
			);

        $start = intval($page) - 4;
        if ($start < 1 ) $start = 1;
        $end = intval($start) + 9;
        if ($end > $pages_count) $end = $pages_count;

        for ($i = $start; $i <= $end; $i++){
			$pager[] = array(
				'x_move_prev' => false,
				'x_move_next' => false,
				'x_move_first' => false,
				'x_move_last' => false,
				'page' => $i,
				'x_current' => ($i == $page) ? true : false,
				'x_not_current' => ($i == $page) ? false : true,
				'x_view_all' => false,
			);
		}

		// move next
		if ( $page < $pages_count )
			$pager[] = array(
				'x_move_prev' => false,
				'x_move_next' => true,
				'x_move_first' => false,
				'x_move_last' => false,
				'page' => $page+1,
				'x_current' => false,
				'x_not_current' => false,
				'x_view_all' => false,
			);
		// move last
		if ( $page+1 < $pages_count )
			$pager[] = array(
				'x_move_prev' => false,
				'x_move_next' => false,
				'x_move_first' => false,
				'x_move_last' => true,
				'page' => $items_count,
				'x_current' => false,
				'x_not_current' => false,
				'x_view_all' => false,
			);
		return $pager;
}
?>