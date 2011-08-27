<?
require_once(CUSTOM_CONTROLS_PATH.'pager.php');
require_once(FUNCTION_PATH.'functions.image.php');

class CExhibitionsPage extends CHTMLPage 
{
	function CExhibitionsPage(&$app, $template)
	{
		parent::__construct($app, $template);
	}
	
	public function on_page_init()
	{
		parent::on_page_init();
		$this->tv['tnav_act_link'] = 'no';
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
		
		$exhibition_rs = $this->Application->DataBase->select_custom_sql("
			SELECT
				e.id as id,
				e.title as title,
				e.destination as destination,
				e.date_begin as date_begin,
				e.date_end as date_end,
				ei.image_filename as image_filename,
				ei.description as image_title
			FROM exhibition e left join exhibition_image ei on ((ei.exhibition_id = e.id))
			GROUP by e.priority DESC, ei.priority DESC
		");
		
		if($exhibition_rs == false || $exhibition_rs->eof()){
			$this->tv['exhibitions_not_found'] = true;
			return false;
		}
		
		$this->tv['ex_title'] = array();
		$img_rs = new CRecordSet();
		$ex_ids = array();
		$i = -1;
		while(!$exhibition_rs->eof()){
			if(!in_array($exhibition_rs->get_field('title'), $this->tv['ex_title'])){
				$ex_ids[] = $exhibition_rs->get_field('id');
				$this->tv['ex_title'][] = $exhibition_rs->get_field('title');
				$this->tv['ex_destination'][] = $exhibition_rs->get_field('destination');
				$this->tv['ex_date_begin'][] = date("d.m.y", strtotime($exhibition_rs->get_field('date_begin')));
				$this->tv['ex_date_end'][] = date("d.m.y", strtotime($exhibition_rs->get_field('date_end')));
				$cnt_img = 0;
				$i++;
			}
			
			if(trim($exhibition_rs->get_field('image_filename')) !== ''){
				$cnt_img++;
				$ex_img_cnt[$i] = $cnt_img;
			}
			
			$exhibition_rs->next();
		}
		
		$this->tv['ex_cnt'] = sizeof($this->tv['ex_title']);
		
		foreach ($ex_ids as $key => $id){
			$exhibition_rs->find('id', $id);
			if(!$exhibition_rs->eof()){
				$curr_line = 0;
				$cnt_in_line = 0;
				while (!$exhibition_rs->eof() && $exhibition_rs->get_field('id') == $id){
					if(trim($exhibition_rs->get_field('image_filename')) !== ''){
						if($cnt_in_line == 4){
							$cnt_in_line = 0;
							$curr_line++;
						}
						$this->tv['img'][$key][$curr_line][$cnt_in_line] = $exhibition_rs->get_field('image_filename');
						$this->tv['img_title'][$key][$curr_line][$cnt_in_line] = $exhibition_rs->get_field('image_title');
						$this->tv['a_img_title'][$key][$curr_line][$cnt_in_line] = $exhibition_rs->get_field('title');
						$this->tv['img_ex_id'][$key][$curr_line][$cnt_in_line] = $exhibition_rs->get_field('id');
						$cnt_in_line++;
						$this->tv['cnt_img_in_line'][$key][$curr_line] = $cnt_in_line;
					}
					
					$exhibition_rs->next();
				}
				$this->tv['cnt_lines'][$key] = $curr_line + 1;
				$this->tv['image_not_found'][$key] = (intval(sizeof($this->tv['img'][$key][0])) == 0);
			}
		}
		
		
	}
}
?>