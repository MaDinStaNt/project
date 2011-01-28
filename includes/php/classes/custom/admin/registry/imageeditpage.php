<?
require_once(CUSTOM_CLASSES_PATH . 'admin/mastereditpage.php');

class CImageEditPage extends CMasterEditPage
{
    /**
     * The table name.
     *
     * @var array
     */
    protected $_table = 'image';
    
    protected $_module = 'Images';

    function CImageEditPage(&$app, $template)
	{
		$this->IsSecure = true;
		parent::CMasterEditPage($app, $template);
		$this->Images = $this->Application->get_module('Images');
	}

	function on_page_init()
	{
		parent::on_page_init();
		CValidator::add('title', VRT_TEXT, 0, 255);
		CValidator::add('system_key', VRT_REGEXP, '/^[a-z0-9-_]{1,64}$/');
		CValidator::add('path', VRT_REGEXP, '/^[a-z0-9_\/\{\}]{1,255}$/');
	}

	function parse_data()
	{
		if (!parent::parse_data())
			return false;
			
		$this->bind_image_sizes();
		
		return true;
	}
	
	function bind_image_sizes() {
        $query = "SELECT id,
			image_width,
			image_height,
			thumbnail_method
            FROM %prefix%image_size
            WHERE image_id = ".$this->id;

        require_once(BASE_CLASSES_PATH . 'controls/navigator.php'); // base application class
        $nav = new Navigator('image_size', $query, array('image_width' => 'image_width', 'image_height' => 'image_height', 'thumbnail_method' => 'thumbnail_method'), 'id', false);
        
        $header_num = $nav->add_header('Width', 'image_width');
        $nav->headers[$header_num]->no_escape = false;
        $nav->headers[$header_num]->set_wrap();
        $nav->set_width($header_num, '34%');

        $header_num = $nav->add_header('Height', 'image_height');
        $nav->headers[$header_num]->no_escape = false;
        $nav->headers[$header_num]->set_wrap();
        $nav->set_width($header_num, '33%');

        $header_num = $nav->add_header('Thumbnail Method', 'thumbnail_method');
        $nav->headers[$header_num]->no_escape = false;
        $nav->headers[$header_num]->set_wrap();
        $nav->set_width($header_num, '33%');

        $this->tv['clickLink'] = $this->Application->Navi->getUri('./image_size_edit/', true);

        if ($nav->size > 0)
            $this->template_vars['image_size_show_remove'] = true;
        else
            $this->template_vars['image_size_show_remove'] = false;
	}
	
	public function on_image_submit($action) {
		switch ($action) {
			case 'add_image_size':
				$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('./image_size_edit/', true) . 'image_id=' . $this->id);
			break;
			case 'delete_image_size':
				$data = InGetPost("ch", array());
	            $where="WHERE image_id = ".$this->id;
	            if (sizeof($data) > 0) {
	            	$where .= " AND ";
	                foreach($data as $k => $v) $where.="id = ".$v." OR ";
	                $where = substr($where, 0 , -4);
	                $sql = 'DELETE FROM %prefix%image_size ' . $where;
	                if($this->Application->DataBase->select_custom_sql($sql)) {
	                	$this->tv['_info'][] = $this->Application->Localizer->get_string('objects_deleted');
	                } 
	                else $this->tv['_errors'][] = $this->Application->Localizer->get_string('internal_error');
	           } 
	           else $this->tv['_info'][] = $this->Application->Localizer->get_string('noitem_selected');
			break;
			case 'close':
				$this->Application->CurrentPage->internalRedirect($this->Application->Navi->getUri('parent', false));
			break;
			default:
				if (CValidator::validate_input()) {
					if ($this->id) {
						if ($this->Images->update_image($this->id, $this->tv)) {
							$this->tv['_info'] = $this->Localizer->get_string('object_updated');
							$this->tv['_return_info'] =  $this->Application->Navi->getUri('parent', false);
						}
						else {
							$this->tv['_errors'] = $this->Images->get_last_error();
						}
					}
					else {
						if ($this->tv['id'] = $this->Images->add_image($this->tv)) {
							$this->tv['_info'] = $this->Localizer->get_string('object_added');
							$this->tv['_return_info'] =  $this->Application->Navi->getUri('parent', false);
						}
						else {
							$this->tv['_errors'] = $this->Images->get_last_error();
						}
					}
				}
				else {
					$this->tv['_errors'] = CValidator::get_errors();
				}
			break;
		}
		$this->bind_image_sizes();
	}
}
?>