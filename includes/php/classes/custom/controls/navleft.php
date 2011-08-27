<?
class CNavLeftCtrl extends CTemplateControl
{
    var $tv;
    var $DataBase;

    function CNavLeftCtrl(&$html_page)
    {
        parent::CTemplateControl('NavLeft');
        $this->tv = &$this->Application->tv;
        $this->DataBase = &$this->Application->DataBase;
    }

    function process()
    {
		$this->bind_categories();
		$this->tv['nl_cat_act'] = InUri('category_uri');
    	return CTemplate::parse_file(CUSTOM_CONTROLS_TEMPLATE_PATH.'nav_left.tpl', $this->tv);
    }
    
    function bind_categories(){
    	$category_rs = $this->DataBase->select_custom_sql('SELECT * FROM product_category GROUP by priority DESC');
		if($category_rs == false || $category_rs->eof()){
			$this->tv['nl_category_not_found'] = true;
			return false;
		}
		
		recordset_to_vars($category_rs, $this->tv, 'nl_cat_cnt', 'nl_cat_');
    }
}
?>