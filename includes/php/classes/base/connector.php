<?
/*	
	Base class for process XMLHTTP requests (AJAX applications)
*/
	
	$DebugLevel = 0;

	define( 'CONNECTOR_ERROR_ACCESS_DENIED', 1 );
	define( 'CONNECTOR_ERROR_UNKNOWN_ERROR', 2 );
	define( 'CONNECTOR_ERROR_NONE', 0 );
	
	class  CConnector extends CHTMLPage  {
		var $session_id;
		var $session_name;
		var $response_head; 
		var $response_body;
		var $response_footer;
		var $errors = array();
		var $RAW_POST_DATA;

		function CConnector( &$app ){
			$this->RAW_POST_DATA = readfile( 'php://input' );
 			parent::CHTMLPage( $app );
 			$this->response_head = '<?xml version="1.0" encoding="UTF-8"?>';
 			$this->response_body = '';
 			$this->response_footer = '';

 			$this->Charset = 'utf-8';
 			$this->ContentType = 'text/xml';
 			$this->session_id = session_id();
 			$this->session_name = session_name();
 			$this->UserLevel = USER_LEVEL_ADMIN;
 			$this->IsSecure = true;
 			$this->NoCache = true;

			$this->on_create();
			
			$this->no_html_data($this->response_head . $this->response_body . $this->response_footer);
		}
		
		function draw_content(){
			$this->binary_data = $this->response_head . $this->response_body . $this->response_footer;
			return parent::draw_content();
		}
		
		function parse_data(){
			$r = parent::parse_data();
			if ( ! $r )
				$this->on_page_error( CONNECTOR_ERROR_UNKNOWN_ERROR );
			return $r;	
		}
		
		function access_denied(){		
			$this->on_page_error( CONNECTOR_ERROR_ACCESS_DENIED );		
		}
		
		function raise ( $error, $message ='' ){
			$this->on_page_error( $error, $message );
		}
		
		// new events 
		function on_page_error( $error, $message = '' ){
		}
		
		function on_create(){ 
		}
	}
?>