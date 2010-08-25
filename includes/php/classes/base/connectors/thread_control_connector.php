<?
require_once( BASE_CLASSES_PATH . 'thread.php' );
require_once( BASE_CLASSES_PATH . 'connector.php' );

define( 'THREAD_CON_COMMAND_GET_INFO', 'GetInfo'  );
define( 'THREAD_CON_COMMAND_INIT', 'Init'  );
define( 'THREAD_CON_COMMAND_KILL', 'Kill'  );
define( 'THREAD_CON_COMMAND_FREE', 'Free'  );

	
	class CThreadControlConnector extends CConnector {
		var $command;
		var $pid;
		var $DataBase;
		var $thread;
		var $Application;
		
		function CThreadControlConnector( &$app ){
			$this->Application = $app;
			$this->DataBase = &$this->Application->DataBase;
			parent::CConnector( $app );
			$this->command = InGet( 'command', 0 );
			$this->pid = InGet( 'pid', 0 );
		}
		
		function on_create(){
			$this->response_body = '<responce>';
			$this->response_footer = '</responce>';
			$rs = $this->DataBase->select_sql( 'p_processes', array( 'id' => $this->pid) );
			if ( ( $this->pid > 0 ) && ( !$rs->eof() ) )
				$this->raise( 2 );
		}
		
		function on_page_init(){
			$this->thread = &CLoader::create_instance( $this->pid );
			$info = $this->thread->get_info();
			switch ( $this->command ){
				case THREAD_CON_COMMAND_INIT:{
					$info = $this->thread->get_info( true );
					$rs = $this->DataBase->select_sql( 'p_processes', array( 'id' => $this->pid ) );
					if ( !$rs->eof() ){
						$options = $rs->get_field( 'process_options' );
						$this->response_body .= '<options ';
						if ( ( $options & THREAD_OPTION_START ) > 0 )
							$this->response_body .= ' start="1"';
						if ( ( $options & THREAD_OPTION_STOP ) > 0 )
							$this->response_body .= ' stop="1"';
						if ( ( $options & THREAD_OPTION_PAUSE ) > 0 )
							$this->response_body .= ' pause="1"';
						if ( ( $options & THREAD_OPTION_RESUME ) > 0 )
							$this->response_body .= ' resume="1"';
						$this->response_body .= ' />';
					}
					else 
						$this->raise( 3 );
				}
				case THREAD_CON_COMMAND_GET_INFO:{
					$this->response_body .= '<progress>'; 
					if( $info['progress'] != ''  ) 
						 $this->response_body .= $info['progress'] ;
					else 
					 	$this->response_body .= 0;
					$this->response_body .= '</progress>';
					$this->response_body .= '<status>' . $info['status'] . '</status>';
					$this->response_body .= '<messages>';
					foreach ( $info['messages'] as $v )
						$this->response_body .= '<msg><![CDATA[' . $v . ']]></msg>';
					$this->response_body .= '</messages>';
					break;
				}
				case THREAD_CON_COMMAND_KILL:{
					$this->thread->Kill();
					break;
				}
				case THREAD_CON_COMMAND_FREE:{
					;//$this->thread->Free();
				}
			}
		}

		
		function on_page_error( $error, $message = '' ){
			$this->response_body .= '<error>' . $error . '</error>';
		}
		
	}
?>