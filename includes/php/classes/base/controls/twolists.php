<?

	class CTwoLists extends CTemplateControl {
		var $tv;
		var $left_group_title = 'Available:';
		var $right_group_title = 'Selected:';
		var $left_items;
		var $right_items;
		var $id;
		var $right_button_text = '>';
		var $left_button_text = '<';
		
		/**
		 * @param string $id
		 * @param array $left_list
		 * @param array $right_list
		 */
		function CTwoLists($id, $left_list, $right_list = array() ){
			parent::CTemplateControl('twolists');
			$this->tv = &$this->Application->tv;
			$this->id = $id;
			$this->left_items = $left_list;
			$this->right_items = $right_list;
			if ((count($left_list) > 0)&&(count($right_list) > 0))
			{
				$this->all_items = $left_list + $right_list;
			}
			elseif (count($left_list) > 0)
			{
				$this->all_items = $left_list;
			}
			elseif (count($right_list) > 0)
			{
				$this->all_items = $right_list;
			}
		}
		
		function on_page_init(){
			 if ($this->in_input_vars( 'left_title' ))
			 	$this->left_group_title = $this->get_input_var( 'left_title' );
			 if ($this->in_input_vars( 'right_title' ))
			 	$this->right_group_title = $this->get_input_var( 'right_title' );
		}
		
		function process(){
		 	$this->tv['style'] = "style=\"width:".$this->get_input_var( 'width' )."px;\"";
			$this->tv['left_group_title'] = $this->left_group_title;
			$this->tv['right_group_title'] = $this->right_group_title;
			$this->tv['left_list_id'] = 'twolists_left_' . $this->id;
			$this->tv['right_list_id'] = 'twolists_right_' . $this->id;
			$this->tv['right_button_text'] = $this->right_button_text;
			$this->tv['left_button_text'] = $this->left_button_text;
//indexes
			if ((strlen($this->tv[$this->tv['left_list_id'] . '_idx']) > 0)||(strlen($this->tv[$this->tv['right_list_id'] . '_idx']) > 0))
			{
				$this->left_items = array();
				if (strlen($this->tv[$this->tv['left_list_id'] . '_idx']) > 0)
				{
					$left_post_val_array = explode(";", substr($this->tv[$this->tv['left_list_id'] . '_idx'], 0, strlen($this->tv[$this->tv['left_list_id'] . '_idx']) - 1));
					foreach ($left_post_val_array as $k => $v) 
					{
						$this->left_items[$v] = $this->all_items[$v];
					}
				}
				$this->right_items = array();
				if (strlen($this->tv[$this->tv['right_list_id'] . '_idx']) > 0)
				{
					$right_post_val_array = explode(";", substr($this->tv[$this->tv['right_list_id'] . '_idx'], 0, strlen($this->tv[$this->tv['right_list_id'] . '_idx']) - 1));
					foreach ($right_post_val_array as $k => $v) 
					{
						$this->right_items[$v] = $this->all_items[$v];
					}
				}
			}
			else 
			{
				$this->tv[$this->tv['left_list_id'] . '_idx'] = "";
				if (count($this->left_items) > 0)
				{
					foreach ($this->left_items as $k => $v)
					{
						$this->tv[$this->tv['left_list_id'] . '_idx'] .= $k . ";";
					}
				}
				$this->tv[$this->tv['right_list_id'] . '_idx'] = "";
				if (count($this->right_items) > 0)
				{
					foreach ($this->right_items as $k => $v)
					{
						$this->tv[$this->tv['right_list_id'] . '_idx'] .= $k . ";";
					}
				}
			}

			$this->tv['left_items_cnt'] = count( $this->left_items );
			$this->tv['right_items_cnt'] = count( $this->right_items );
			$this->tv['left_val'] = array_keys( $this->left_items );
			$this->tv['left_title'] = array_values( $this->left_items );
			$this->tv['right_val'] = array_keys( $this->right_items );
			$this->tv['right_title'] = array_values( $this->right_items );

			return CTemplate::parse_file( BASE_TEMPLATE_PATH . 'controls/twolists.tpl', $this->tv );
		}
		
	}
?>