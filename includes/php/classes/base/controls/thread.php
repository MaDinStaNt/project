<?
	class CThreadControl extends CTemplateControl {
		var $id;
		var $tv;
		
		function CThreadControl( $obj, $id = 0){
			parent::CTemplateControl( 'thread', $obj );
			$this->tv = &$this->Application->template_vars;
			$this->id = $id;
			$this->tv['id'] = $this->id;
		}
		
		function process(){
			if ( $this->in_input_vars( 'details' ) )
				$this->tv['thread_details'] = $this->input_vars['details'];	
			else 
				$this->tv['thread_details'] = false;
			if ( $this->in_input_vars( 'redirect' ) )
				$this->tv['redirect'] = $this->input_vars['redirect'];
			if ( isset( $this->input_vars['onkill'] ) )
				$this->tv['onkill'] = $this->input_vars['onkill'];
			else 
				$this->tv['onkill'] = '';
			if ( $this->id > 0 ){
				return CTemplate::parse_file( BASE_CONTROLS_TEMPLATE_PATH . 'thread.tpl' );
			}
		}
		
	}
?>