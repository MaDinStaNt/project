<?
	class CTabControl extends CTemplateControl {
		var $DataBase;
		var $object_id;
		var $tv;		
		var $tabs;
		var $active_tab_id = 1;

		function CTabControl($object_id = ''){
			parent::CTemplateControl('link_tabs', $object_id);
			$this->DataBase = &$GLOBALS['app']->DataBase;
			$this->tv = &$GLOBALS['app']->template_vars;
			//$this->tabs[ LINK_STATUS_ALL ] = $GLOBALS['link_status_name'];
			/*$this->object_id = $object_id;
			$this->tabs[0]['name'] = 'Test';
			$this->tabs[0]['id'] = 0;
			$this->tabs[1]['name'] = 'Test1';
			$this->tabs[1]['id'] = 1;
			$this->tabs[2]['name'] = 'Test2';
			$this->tabs[2]['id'] = 2;*/
		}
		
		function add_tab( $Name, $id ){
			$cnt = count( $this->tabs );
			$this->tabs[$cnt]['id'] = $id;
			$this->tabs[$cnt]['name'] = $Name;
		}
		
		
		function process(){
			$cnt = count( $this->tabs );
			if ( $cnt > 0 ){
				$this->tv['have_tabs'] = true;
				$this->tv['tab_cnt'] = $cnt;
				if ( ! $this->in_input_vars('clicklink') )
					system_die('Tab control error');
				else
					$url = $this->get_input_var('clicklink');
				if ( $cnt > 0 )
					$this->tv['have_tabs'] = true;
				else
					$this->tv['have_tabs'] = false;
			
				if ( $cnt == 1 ){
					$this->tv['img_left'] = 'tab_firsl_left_a.gif';
					$this->tv['img_right'] = 'tab_firsl_right_a.gif';
					$this->tv['tab_active'][0] = true;
					$this->tv['tab_name'][0] = $this->tabs[0]['name'];
					$this->tv['tab_id'][0] = $this->tabs[0]['id'];
					$this->tv['tab_class'][0] = 'active_tab';
					$this->tv['last_tab'][0] = true;
				}
				else {
					for ( $i = 0; $i < $cnt; $i++ ){
						$this->tv['tab_link_url'][$i] = $url . $this->tabs[$i]['id'];
						if ( $i == 0 ){
							if ( $this->tabs[$i]['id'] == $this->active_tab_id )
								$this->tv['img_left'] = 'tab_firsl_left_a.gif';
							else 
								$this->tv['img_left'] = 'tab_firsl_left_na.gif';
						};
					
						if ( $i == ( $cnt - 1 ) ){
							$this->tv['last_tab'][$i] = true;
							if ( $this->tabs[$i]['id'] == $this->active_tab_id )
								$this->tv['img_right'] = 'tab_firsl_right_a.gif';
							else 
								$this->tv['img_right'] = 'tab_firsl_right_na.gif';
						}
						else 
							$this->tv['last_tab'][$i] = false;
					
						if ( $this->tabs[$i]['id'] == $this->active_tab_id )
							$this->tv['tab_active'][$i] = true;
						else 
							$this->tv['tab_active'][$i] = false;
	
						if ( $i < $cnt -1 ){
							if ( $this->tabs[$i]['id'] == $this->active_tab_id )
								$this->tv['img_middle'][$i] = 'tab_middle_right_a.gif';
							elseif ( $this->tabs[$i+1]['id'] == $this->active_tab_id )
								$this->tv['img_middle'][$i] = 'tab_middle_left_a.gif';
							else 
								$this->tv['img_middle'][$i] = 'tab_middle.gif';
						}
						
						if ( $this->tabs[$i]['id'] == $this->active_tab_id )
							$this->tv['tab_class'][$i] = 'active_tab';
						else
							$this->tv['tab_class'][$i] = 'normal_tab'; 	
						
						$this->tv['tab_name'][$i] = $this->tabs[$i]['name'];

					}
				}
			}
			else 
				$this->tv['have_tabs'] = false;

			return CTemplate::parse_file( CUSTOM_TEMPLATE_PATH . 'controls/tab_control.tpl' , $this->tv );
		}
	}
?>