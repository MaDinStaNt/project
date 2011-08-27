<?
require_once(CUSTOM_CONTROLS_PATH.'pager.php');
require_once(FUNCTION_PATH.'functions.image.php');

class CSearchPage extends CHTMLPage 
{
	function CSearchPage(&$app, $template)
	{
		parent::__construct($app, $template);
	}
	
	public function on_page_init()
	{
		parent::on_page_init();
		$this->tv['tnav_act_link'] = 'tnav_search';
		$this->bind_data();
	}

	public function parse_data()
	{
		if (!parent::parse_data())
			return false;
			
		return true;
	}
	
	function bind_data(){
		$Registry = $this->Application->get_module('Registry');
		
		$this->tv['_pat_link'] = $Registry->get_value('_static/_core/_pat_link');
		$this->tv['_video_link'] = $Registry->get_value('_static/_core/_video_link');
		
	}
	
	function on_search_submit($action){
		require_once(CUSTOM_CONTROLS_PATH . 'simpledataoutput.php');
		new CSimpleDataOutput($this);
		require_once(BASE_CONTROLS_PATH . 'simplearrayoutput.php');
		new CSimpleArrayOutput();
		
		CValidator::add('string', VRT_TEXT, 0, 1000);
		if(CValidator::validate_input()){
			/*if($this->tv['radio'] == 'exact'){
				$sql = "
					(SELECT if(1=1, 'product', 'product') as type, p.title as title, concat('product/', pc.uri, '/', p.uri) as uri, p.brief_description as description FROM product p join product_category pc on((p.product_category_id = pc.id)) WHERE 1=2 OR p.title LIKE '{$this->tv['string']}' OR p.article LIKE '{$this->tv['string']}' OR p.description LIKE '{$this->tv['string']}')
				union
					(SELECT if(1=1, 'partnership', 'partnership') as type, IF(1=1, 'Сотрудничество', 'Сотрудничество') as title, if(1=1, 'partnership', 'partnership') as uri, CONCAT(TRIM(SUBSTRING(rv.value, 1, 200)), '...') as description FROM registry_tree rt join `registry_values` rv on((rv.path_id = rt.path_id)) WHERE rt.name = '_partnership' AND (rv.value LIKE '{$this->tv['string']}')) 
				union
					(SELECT if(1=1, 'about', 'about') as type, IF(1=1, 'О компании', 'О компании') as title, if(1=1, 'about', 'about') as uri, CONCAT(TRIM(SUBSTRING(rv.value, 1, 200)), '...') as description FROM registry_tree rt join `registry_values` rv on((rv.path_id = rt.path_id AND rv.name='_template')) WHERE rt.name = '_about' AND (rv.value LIKE '{$this->tv['string']}')) 
				union
					(SELECT if(1=1, 'contacts', 'contacts') as type, IF(1=1, 'Контакты', 'Контакты') as title, if(1=1, 'contacts', 'contacts') as uri, CONCAT(TRIM(SUBSTRING(rv.value, 1, 200)), '...') as description FROM registry_tree rt join `registry_values` rv on((rv.path_id = rt.path_id)) WHERE rt.name = '_contact' AND rv.name <> '_sm_img' AND rv.name <> '_med_img'  AND rv.name <> '_big_img' AND(rv.value LIKE '{$this->tv['string']}')) 
				union
					(SELECT if(1=1, 'exhibitions', 'exhibitions') as type, e.title as title, if(1=1, 'exhibitions', 'exhibitions') as uri, e.destination as description FROM exhibition e WHERE 1=2 OR e.title LIKE '{$this->tv['string']}' OR e.destination LIKE '{$this->tv['string']}') 
				union
					(SELECT if(1=1, 'categories', 'categories') as type, pc.title as title, CONCAT('product/', pc.uri) as uri, pc.description as description FROM product_category pc WHERE 1=2 OR pc.title LIKE '{$this->tv['string']}' OR pc.description LIKE '{$this->tv['string']}') ";
				
				$rs = $this->Application->DataBase->select_custom_sql($sql);
				if($rs == false || $rs->eof()){
					$this->tv['result_not_found'] = true;
					return true;
				}
				
				recordset_to_vars($rs, $this->tv, 's_cnt', 's_');
				$this->tv['s_description'] = $this->clear_code($this->tv['s_description']);
				return true;
			}*/
			if ($this->tv['radio'] == 'any_words'){
				$words = explode(" ", $this->tv['string']);
				$prod_where = "1=2";
				foreach ($words as $word) $prod_where .= " OR p.title LIKE '%{$word}%' OR p.article LIKE '%{$word}%' OR p.description LIKE '%{$word}%'";
				$partner_where = "1=2";
				foreach ($words as $word) $partner_where .= " OR rv.value LIKE '%{$word}%'";
				$about_where = "1=2";
				foreach ($words as $word) $about_where .= " OR rv.value LIKE '%{$word}%'";
				$contact_where = "1=2";
				foreach ($words as $word) $contact_where .= " OR rv.value LIKE '%{$word}%'";
				$ex_where = "1=2";
				foreach ($words as $word) $ex_where .= " OR e.title LIKE '%{$word}%' OR e.destination LIKE '%{$word}%'";
				$cat_where = "1=2";
				foreach ($words as $word) $cat_where .= " OR pc.title LIKE '%{$word}%' OR pc.description LIKE '%{$word}%'";
				
				$sql = "
					(SELECT if(1=1, 'product', 'product') as type, p.title as title, concat('product/', pc.uri, '/', p.uri) as uri, p.brief_description as description FROM product p join product_category pc on((p.product_category_id = pc.id)) WHERE {$prod_where})
				union
					(SELECT if(1=1, 'partnership', 'partnership') as type, IF(1=1, 'Сотрудничество', 'Сотрудничество') as title, if(1=1, 'partnership', 'partnership') as uri, CONCAT(TRIM(SUBSTRING(rv.value, 1, 200)), '...') as description FROM registry_tree rt join `registry_values` rv on((rv.path_id = rt.path_id)) WHERE rt.name = '_partnership' AND ({$partner_where})) 
				union
					(SELECT if(1=1, 'about', 'about') as type, IF(1=1, 'О компании', 'О компании') as title, if(1=1, 'about', 'about') as uri, CONCAT(TRIM(SUBSTRING(rv.value, 1, 200)), '...') as description FROM registry_tree rt join `registry_values` rv on((rv.path_id = rt.path_id AND rv.name='_template')) WHERE rt.name = '_about' AND ({$about_where})) 
				union
					(SELECT if(1=1, 'contacts', 'contacts') as type, IF(1=1, 'Контакты', 'Контакты') as title, if(1=1, 'contacts', 'contacts') as uri, CONCAT(TRIM(SUBSTRING(rv.value, 1, 200)), '...') as description FROM registry_tree rt join `registry_values` rv on((rv.path_id = rt.path_id)) WHERE rt.name = '_contact' AND rv.name <> '_sm_img' AND rv.name <> '_med_img'  AND rv.name <> '_big_img' AND({$contact_where})) 
				union
					(SELECT if(1=1, 'exhibitions', 'exhibitions') as type, e.title as title, if(1=1, 'exhibitions', 'exhibitions') as uri, e.destination as description FROM exhibition e WHERE {$ex_where}) 
				union
					(SELECT if(1=1, 'categories', 'categories') as type, pc.title as title, CONCAT('product/', pc.uri) as uri, pc.description as description FROM product_category pc WHERE {$cat_where})";
				
				
				
				$rs = $this->Application->DataBase->select_custom_sql($sql . $where);
				if($rs == false || $rs->eof()){
					$this->tv['result_not_found'] = true;
					return true;
				}
				
				recordset_to_vars($rs, $this->tv, 's_cnt', 's_');
				$this->tv['s_description'] = $this->clear_code($this->tv['s_description']);
				return true;
				
			}
			elseif($this->tv['radio'] == 'all_words'){
				$sql = "
					(SELECT if(1=1, 'product', 'product') as type, p.title as title, concat('product/', pc.uri, '/', p.uri) as uri, p.brief_description as description FROM product p join product_category pc on((p.product_category_id = pc.id)) WHERE 1=2 OR p.title LIKE '%{$this->tv['string']}%' OR p.article LIKE '%{$this->tv['string']}%' OR p.description LIKE '%{$this->tv['string']}%')
				union
					(SELECT if(1=1, 'partnership', 'partnership') as type, IF(1=1, 'Сотрудничество', 'Сотрудничество') as title, if(1=1, 'partnership', 'partnership') as uri, CONCAT(TRIM(SUBSTRING(rv.value, 1, 200)), '...') as description FROM registry_tree rt join `registry_values` rv on((rv.path_id = rt.path_id)) WHERE rt.name = '_partnership' AND (rv.value LIKE '%{$this->tv['string']}%')) 
				union
					(SELECT if(1=1, 'about', 'about') as type, IF(1=1, 'О компании', 'О компании') as title, if(1=1, 'about', 'about') as uri, CONCAT(TRIM(SUBSTRING(rv.value, 1, 200)), '...') as description FROM registry_tree rt join `registry_values` rv on((rv.path_id = rt.path_id AND rv.name='_template')) WHERE rt.name = '_about' AND (rv.value LIKE '%{$this->tv['string']}%')) 
				union
					(SELECT if(1=1, 'contacts', 'contacts') as type, IF(1=1, 'Контакты', 'Контакты') as title, if(1=1, 'contacts', 'contacts') as uri, CONCAT(TRIM(SUBSTRING(rv.value, 1, 200)), '...') as description FROM registry_tree rt join `registry_values` rv on((rv.path_id = rt.path_id)) WHERE rt.name = '_contact' AND rv.name <> '_sm_img' AND rv.name <> '_med_img'  AND rv.name <> '_big_img' AND(rv.value LIKE '%{$this->tv['string']}%')) 
				union
					(SELECT if(1=1, 'exhibitions', 'exhibitions') as type, e.title as title, if(1=1, 'exhibitions', 'exhibitions') as uri, e.destination as description FROM exhibition e WHERE 1=2 OR e.title LIKE '%{$this->tv['string']}%' OR e.destination LIKE '%{$this->tv['string']}%') 
				union
					(SELECT if(1=1, 'categories', 'categories') as type, pc.title as title, CONCAT('product/', pc.uri) as uri, pc.description as description FROM product_category pc WHERE 1=2 OR pc.title LIKE '%{$this->tv['string']}%' OR pc.description LIKE '%{$this->tv['string']}%') ";
				
				//$rs = $this->Application->DataBase->select_custom_sql("SELECT p.*, pc.uri as category_uri FROM product p join product_category pc on((p.product_category_id = pc.id)) WHERE p.title LIKE '%{$this->tv['string']}%'");
				$rs = $this->Application->DataBase->select_custom_sql($sql);
				if($rs == false || $rs->eof()){
					$this->tv['result_not_found'] = true;
					return true;
				}
				
				recordset_to_vars($rs, $this->tv, 's_cnt', 's_');
				$this->tv['s_description'] = $this->clear_code($this->tv['s_description']);
				return true;
			}
			
			$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
		}
		else{
			$this->tv['_errors'] = CValidator::get_errors();
		}
	}
	
	function clear_code($arr){
		if(!is_array($arr)){
			$this->tv['_errors'] = $this->Application->Localizer->get_string('invalid_input');
			return false;
		}
		
		foreach ($arr as $key => $value){
			$value = str_replace('<%=image_1%>', '', htmlspecialchars_decode($value));
			$value = str_replace('<%=image_2%>', '', htmlspecialchars_decode($value));
			$value = str_replace('<%=image_3%>', '', htmlspecialchars_decode($value));
			$value = str_replace('<%=image_4%>', '', htmlspecialchars_decode($value));
			$value = str_replace('<%=image_5%>', '', htmlspecialchars_decode($value));
			$value = str_replace('<p></p>', '', htmlspecialchars_decode($value));
			$arr[$key] = $value;
		}
		
		return $arr;
	}
}
?>