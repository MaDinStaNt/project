<?php
/**
 * @package LLA.Base
 */
/**
 * @package LLA.Base
 */
class CShoppingCard
{
	function CShoppingCard(&$app)
	{
        if (!array_key_exists('ShoppingCart', $_SESSION)) $_SESSION['ShoppingCart'] = array();
        $this->ShoppingCart = &$_SESSION['ShoppingCart'];
	}
	
	function add($id, $type)
	{
		if (!$this->is_in_cart($id, $type)) {
			$this->ShoppingCart[] = array('type' => $type, 'id' => $id);
			return true;
		}
		else {
			return false;
		}
	}
	
	function is_in_cart($id, $type) {
		foreach ($this->ShoppingCart as $key => $value) {
			if (($value['type'] == $type)&&($value['id'] == $id)) {
				return true;
			}
		}
		return false;
	}
	
	function clear() {
		$this->ShoppingCart = array();
	}
	
	function get_count($type = false) {
		if (!$type) {
			return sizeof($this->ShoppingCart);
		}
		else {
			$count = 0;
			foreach ($this->ShoppingCart as $key => $value) {
				if ($value['type'] == $type) {
					$count ++;
				}
			}
			return $count;
		}
	}
	
	function get_items() {
		return $this->ShoppingCart;
	}
	
	function delete($key) {
		unset($this->ShoppingCart[$key]);
	}
}
?>