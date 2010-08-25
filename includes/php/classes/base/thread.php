<?

define( 'THREAD_COMMAND_START', 1 );
define( 'THREAD_COMMAND_PAUSE', 2 );
define( 'THREAD_COMMAND_RESUME', 3 );
define( 'THREAD_COMMAND_KILL', 4 );

define ( 'THREAD_STATUS_PENDING', 1  );
define ( 'THREAD_STATUS_WORKING', 2  );
define ( 'THREAD_STATUS_PAUSED', 3  );
define ( 'THREAD_STATUS_FINISHED', 4  );
define ( 'THREAD_STATUS_ERROR', 5 );

// describe functions for craphick control
define ( 'THREAD_OPTION_START', 1 );
define ( 'THREAD_OPTION_PAUSE', 2 );
define ( 'THREAD_OPTION_RESUME', 4 );
define ( 'THREAD_OPTION_STOP', 8 );


	class CThread{
		/**
		 * @var CApp
		 */
		var $Application;
		/**
		 * @var CDataBase
		 */
		var $DataBase;
		var $pid;
		var $ProcessData;
		var $name;
		var $options;
				
		
		function CThread( $class_path, $name = '', $pid = 0, $description = '', $options = THREAD_OPTION_STOP ){
			$this->Application = &$GLOBALS['app'];
			$this->DataBase = &$this->Application->DataBase;
			if ( $pid == 0 ){
				$this->options = $options;
				$this->DataBase->insert_sql( 'p_processes', array( 'name' => $name, 'description' => $description, 'user' => $this->Application->User->UserData['id'], 'status' => THREAD_STATUS_PENDING, 'class_name' => get_class( $this ), 'file' => $class_path, 'process_options' => $options ) );
				$this->pid = $this->DataBase->get_last_id();
				$this->ProcessData['pid'] = $this->pid;
				$this->ProcessData['name'] = $name;
				$this->name = $name;
				$this->ProcessData['description'] = $description;
				$this->ProcessData['user'] = $this->Application->User->UserData['id'];
			}
			else{
				$rs = $this->DataBase->select_sql( 'p_processes', array( 'id' => $pid ) );
				$this->pid = $rs->get_field( 'id' );
				row_to_vars( $rs, $this->ProcessData );
				$this->name = $rs->get_field( 'name' );
				$this->options = $rs->get_field( 'process_options' );
			}
		}

		
		/**
		 * @param string $s
		 */
		function set_error( $s ){
			$this->DataBase->update_sql( 'p_processes', array( '`status`' => THREAD_STATUS_ERROR, 'message' => $s ), array( 'id' => $this->pid ) );
		}
		
		function set_options( $options ){
			$this->options = $options;
			$this->DataBase->update_sql( 'p_processes', array( 'process_options' => $this->options ), array( 'id' => $this->pid ) );
		}
		
		function Start(){
			global $SiteUrl, $RootPath, $HttpPort;
			$s = 'http://' . $SiteUrl . ':' . $HttpPort . $RootPath . 'connectors/loader.php?' . session_name() . '=' . session_id() . '&pid=' . $this->pid;
			$ch = curl_init( $s );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 1 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_VERBOSE, 0 );
			curl_exec( $ch );
		}
		
		function _set_info( $progress, $message = '' ){
			$this->DataBase->update_sql( 'p_processes', array( 'progress' => $progress, 'responce_time' => time() ), array( 'id' => $this->pid ) );
			if ( $message != '' )
				$this->DataBase->insert_sql( 'p_proc_messages', array( 'pid' => $this->pid, '`message`' => $message, '`check`' => 0 ) );
		}
		
		function _refresh_state(){
			$rs = $this->DataBase->select_sql( 'p_processes', array( 'id' => $this->pid ) );
			row_to_vars( $rs, $this->ProcessData );
		}
		
		function _get_command(){
			$rs = $this->DataBase->select_sql( 'p_processes', array( 'id' => $this->pid ) );
			if ( ( !$rs->eof() ) && ( $rs->get_field( 'cmd' ) > 0 ) )
				return $rs->get_field( 'cmd' );
			else 
				return 0;
		}
		
		function _save_proc_state( $xml ){
			$this->DataBase->update_sql( 'p_processes', array( 'process_data' => $xml ), array( 'id' => $this->pid ) );
		}
		
		
		function _load_proc_state(){
			$rs = $this->DataBase->select_sql( 'p_processes', array( 'id' => $this->pid ) );
			return $rs->get_field( 'process_data' );
		}
		
		
		function Pause(){}
		
		function Kill(){
			$this->DataBase->update_sql( 'p_processes', array( 'cmd' => THREAD_COMMAND_KILL ), array( 'id' => $this->pid ) );				
		}
		
		function Resume(){}
		
		function Free(){
			$this->DataBase->delete_sql( 'p_processes', array( 'id' => $this->pid ) );
			$this->DataBase->delete_sql( 'p_proc_messages', array( 'pid' => $this->id ));
		}
		
		function get_info( $all_messages = false ){
			$rs = $this->DataBase->select_sql( 'p_processes', array( 'id' => $this->pid ) );
			$Result['progress'] = $rs->get_field( 'progress' );
			$Result['status'] = $rs->get_field( 'status' );
			$ar['pid'] = $this->pid;
			if ( !$all_messages )
				$ar['`check`'] = 0;
			$rs = $this->DataBase->select_sql( 'p_proc_messages', $ar );
			while ( !$rs->eof() ){
				$Result['messages'][] = $rs->get_field( 'message' );
				$ar[] = $rs->get_field( 'id' );
				$rs->next();
			}
			$s = '(' . implode( ',', $ar ) . ')';
			$this->DataBase->custom_sql( 'update %prefix%p_proc_messages set `check`=1 where id in ' . $s );
			return $Result;
		}
		
		
		function process(){	
			/*
				Here you place code of process. in code you can call _get_command() for control process;
				
			Example:
				for ( $i=0; $i < 100000; $i++ )
					$this->_set_info( intval($i / 1000) );
			*/
		}
	}
	
	
	class CLoader extends CHTMLPage {
		var $pid;
		var $DataBase;
		
		function CLoader( $app ){
			parent::CHTMLPage( $app );
			set_time_limit( 0 );
			ignore_user_abort( 0 );
			echo 'Create';
			$this->pid = InGet( 'pid', 0 );
			$this->DataBase = &$app->DataBase;
		}
		
		function create_instance( $pid ){
			$rs = $GLOBALS['app']->DataBase->select_sql( 'p_processes', array( 'id' => $pid ) );
			if ( !$rs->eof() ){
				$file = $rs->get_field('file');
				$class = $rs->get_field('class_name');
				require_once($file);
				$worker = new $class( '', $rs->get_field( 'name' ),  $pid );
				return $worker;
			}
			else 
				return false;
		}
		
		function on_page_init(){
			echo 'Init';
			if ( $this->pid > 0 ){
				$rs = $this->DataBase->select_sql( 'p_processes', array( 'id' => $this->pid ) );
				$status = $rs->get_field( 'status' );
				if ( $status == THREAD_STATUS_PENDING ){
					$this->DataBase->update_sql( 'p_processes', array( 'status' => THREAD_STATUS_WORKING, 'progress' => 0 ), array( 'id' => $this->pid ) );
					$file = $rs->get_field('file');
					$class = $rs->get_field('class_name');
					require_once($file);
					$worker = new $class( '', $rs->get_field( 'name' ),  $this->pid );
					$worker->_set_info(0);
					$worker->process();
					$worker->_set_info(100);
					$this->DataBase->update_sql( 'p_processes', array( 'status' => THREAD_STATUS_FINISHED ), array( 'id' => $this->pid ) );
				}
			}
		}
	}
	
	
	
	
	class CThreadsModule{
		/**
		 * @var CDataBase
		 */
		var $DataBase;
		
		function CThreadsModule( &$app ){
			$this->DataBase = &$app->DataBase;
		}
		
		function check_install(){
			return ( $this->DataBase->is_table( 'p_proc_messages' ) &&  $this->DataBase->is_table( 'p_processes' ) );
			
		}
		
		function install(){
			$this->DataBase->custom_sql( 'drop table if exists %prefix%p_processes' );
			$s="CREATE TABLE %prefix%p_processes (
  			`id` int(11) NOT NULL auto_increment,
  			`name` varchar(255) default NULL,
  			`user` int(11) default NULL,
  			`description` varchar(255) default NULL,
  			`status` tinyint(4) default NULL,
  			`progress` tinyint(4) default NULL,
  			`message` varchar(255) default NULL,
  			`cmd` tinyint(4) default NULL,
  			`start_time` int(10) unsigned default NULL,
  			`process_data` longtext,
  			`responce_time` int(11) default NULL,
  			`file` varchar(255) default '',
  			`class_name` varchar(255) default '',
  			`process_options` int(10) unsigned default NULL,
  			PRIMARY KEY  (`id`)) ";
			$this->DataBase->custom_sql( $s );
			$this->DataBase->custom_sql( 'drop table if exists %prefix%p_proc_messages' );
			$s = "CREATE TABLE %prefix%p_proc_messages (
  			`id` int(10) unsigned NOT NULL auto_increment,
  			`message` varchar(255) NOT NULL default '',
  			`check` tinyint(3) unsigned NOT NULL default '0',
  			`pid` int(10) unsigned NOT NULL default '0',
  			PRIMARY KEY  (`id`)) ";
			$this->DataBase->custom_sql( $s );
		}
		
	}
	
	
?>