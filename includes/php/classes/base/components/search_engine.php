<?php
/**
 * @package LLA.Base
 */
/*
--------------------------------------------------------------------------------
Class CSearchEngine v 1.1.0

main methods:
	get_items(string query, string condition, ing page)
		query - any string
		condition - "and", "or" boolean search type to use between words
		page - 0-based page number
		
		returns:
			instance of CSearchResults or null

history:
	v. 1.1.0 - content type checking (VK)
	v. 1.0.0 - created (VK)
	
Class CSearchResults v.1.0.0

properties:
	count - result count
	items - CRecordSet object with next fields:
		url - fully qualified url of the page
		title - title of the page
		hl_title - highlighted (with <b> tags) title, already html escaped
		page_text - plain page text
		hl_text - highlighted (with <b> tags) page text, already html escaped
		
history:
	v 1.0.0 - created (VK)
--------------------------------------------------------------------------------
*/

/**
 */
define('SE_TITLE', 150);
define('SE_KEYWORD', 150);
define('SE_DESCRIPTION', 150);

define('SE_H1', 35);
define('SE_H2', 30);
define('SE_H3', 25);
define('SE_H4', 20);
define('SE_H5', 15);
define('SE_H6', 10);

define('SE_STRONG', 5);
define('SE_EM', 5);
define('SE_TH', 5);

define('SE_P', 2);

define('SE_IMG', 1);
define('SE_A', 1);

require_once(BASE_CLASSES_PATH . 'components/lwg_xml.php');

/**
 * @package LLA.Base
 */
class CSearchResults
{
	var $count = 0;
	var $items = null;
	
	function CSearchResults()
	{
	}
	
	function get_count()
	{
		return $count;
	}
}

/**
 * @package LLA.Base
 */
class CSearchEngine
{
	var $Application;
	var $DataBase;
	
	var $_internal_words = array();
	var $_internal_urls = array();
	
	function CSearchEngine(&$app)
	{
		$this->Application = &$app;
		$this->DataBase = &$this->Application->DataBase;
	}
	
	function &get_items($query, $cond = 'and', $page = 0)
	{
		$words = $this->split_string($query);
		$hl_words = array();
		$words_id = array();
		foreach ($words as $word)
			if (strlen($word))
			{
				$n_word = $this->normalize_word($word, $hl_words);
				//if (strlen($n_word) > MIN_WORD_LENGTH)
				{
					$id_word = $this->get_id_word($n_word);
					if (!in_array($id_word, $words_id))
					{
						$words_id[] = $id_word;
						if (!in_array($n_word, $hl_words))
							$hl_words[] = $n_word;
					}
				}
			}
		if (count($words_id) <= 0)
			return null;
		
		$sql_query = 'select a.id_url, sum(priority)*count(*) as p
		from
			%prefix%se_url_words as a, %prefix%se_urls as b
		where
			a.id_word in ('.join(',', $words_id).') and
			a.id_url = b.id_url and
			b.parsed = 1
		group by a.id_url
		' . ( ($cond=='and')?('having count(*) = '.count($words_id)):('having count(*) > 0') ).'
		order by p desc
		';
		
		$results = $this->DataBase->select_custom_sql($sql_query);
		
		if ($results == null)
			return null;
			
		$sr = new CSearchResults();
		$sr->count = $results->get_record_count();

		$page = intval($page);
		$sql_query = '
		select a.id_url, b.url, b.title, \'\' as page_text, sum(priority)*count(*) as p
		from
			%prefix%se_url_words as a, %prefix%se_urls as b
		where
			a.id_word in ('.join(',', $words_id).') and
			a.id_url = b.id_url and
			b.parsed = 1
		group by a.id_url, b.url, b.title
		' . ( ($cond=='and')?('having count(*) = '.count($words_id)):('having count(*) > 0') ).'
		order by p desc
		';
		$sr->items = $this->DataBase->select_custom_sql($sql_query, array($page, 10));
		if ($sr->items == null)
			return null;
		$sr->items->add_field('hl_title');
		$sr->items->add_field('hl_text');
		while (!$sr->items->eof())
		{
			$get_text_rs = $this->DataBase->select_sql('se_urls', array('id_url'=>$sr->items->get_field('id_url')));
			$otxt = htmlspecialchars($get_text_rs->get_field('page_text'));
			
			$txt = $this->hl_text($otxt, $hl_words);
			$sr->items->Rows[$sr->items->current_row]->Fields['hl_text'] = ( ($txt!='')?($txt):('Found in images') );

			$otxt = htmlspecialchars($sr->items->get_field('title'));
			$txt = $this->hl_text($otxt, $hl_words);
			$sr->items->Rows[$sr->items->current_row]->Fields['hl_title'] = ( ($txt!='')?($txt):($otxt) );

			$sr->items->next();
		}
		$sr->items->first();
		
		if ($page == 0)
			$this->DataBase->insert_sql('se_queries', array('query'=>$query, 'result_count'=>$sr->count, 'query_date'=>'now()'));

		return $sr;
	}
	
	// returns highlighted version of the text, words is array of normalized words
	function hl_text($text, $words)
	{
		$words_pos = -1;
		$re_str = '';
		for ($i=0; $i<count($words); $i++)
		{
			$re_str .= ( ($i>0)?('|'):('') ) . $words[$i];
			
			preg_match('/([^�-��-���A-Za-z0-9]|^)('.$words[$i].')([�-��-���A-Za-z0-9]*)/is', $text, $m, PREG_OFFSET_CAPTURE);
			
			if (isset($m[2]))
				if ($words_pos >= 0)
					$words_pos = min($m[2][1], $words_pos);
				else
					$words_pos = $m[2][1];
		}
		
		if ($words_pos < 0) return '';
		
		$p1 = $words_pos - 80;
		if ($p1<0) $p1 = 0;
		$p2 = $words_pos + 80;
		if ($p2 >= strlen($text)) $p2 = strlen($text)-1;
		
		while ( ($p1>0) && ($text[$p1] != ' ')) $p1--;
		while ( (($p2+1) < strlen($text)) && ($text[$p2] != ' ')) $p2++;
		
		$sub_text = '';
		if ($p1 > 0) $sub_text .= '... ';
		$sub_text .= substr($text, $p1, $p2-$p1+1);
		if ( ($p2+1) < strlen($text) ) $sub_text .= ' ...';
		return preg_replace('/([^�-��-���A-Za-z0-9]|^)(('.$re_str.')([�-��-���A-Za-z0-9]*))/is', '$1<b>$2</b>', $sub_text);
	}
	
	function _get_id_url($url)
	{
		if (isset($this->_internal_urls[$url])) return $this->_internal_urls[$url];
		$rs = $this->DataBase->select_custom_sql('select id_url from %prefix%se_urls where url = \''.$this->DataBase->internalEscape($url).'\'');
		if ($rs->eof())
			$id_url = $this->DataBase->insert_sql('se_urls', array('url'=>$url, 'title'=>'', 'parsed'=>0, 'error'=>0, 'page_text'=>'', 'page_html'=>''));
		else
			$id_url  = $rs->get_field('id_url');
		$this->_internal_urls[$url] = $id_url;
		return $id_url;
	}
	
	function _get_unparsed_url()
	{
		$rs = $this->DataBase->select_custom_sql('select url from %prefix%se_urls where parsed=0 and error=0');
		if (!$rs->eof())
			return $rs->get_field('url');
		else
			return false;
	}
	
	function get_id_word($word)
	{
		$word = strtolower($word);
		$rs = $this->DataBase->select_custom_sql('select id_word from %prefix%se_words where word = \''.$this->DataBase->internalEscape($word).'\'');
		if ($rs->eof())
			return -1;
		else
			return $rs->get_field('id_word');
	}

	function _get_id_word($word)
	{
		$word = strtolower($word);
		if (isset($this->_internal_words[$word])) return $this->_internal_words[$word];
		$rs = $this->DataBase->select_custom_sql('select id_word from %prefix%se_words where word = \''.$this->DataBase->internalEscape($word).'\'');
		if ($rs->eof())
			$id_word = $this->DataBase->insert_sql('se_words', array('word'=>strtolower($word), 'total_count'=>1));
		else
		{
			$this->DataBase->custom_sql('update %prefix%se_words set total_count=total_count+1 where id_word='.$rs->get_field('id_word'));
			$id_word = $rs->get_field('id_word');
		}
		$this->_internal_words[$word] = $id_word;
		return $id_word;
	}
	
	function _store_link($id_url, $id_word, $p)
	{
		$rs = $this->DataBase->select_custom_sql('select id_word from %prefix%se_url_words where id_url='.intval($id_url).' and id_word ='.intval($id_word));
		if ($rs->eof())
			$this->DataBase->insert_sql('se_url_words', array('id_url'=>$id_url, 'id_word'=>$id_word, 'priority'=>$p, 'url_count'=>1));
		else
			$this->DataBase->custom_sql('update %prefix%se_url_words set priority=priority+'.intval($p).', url_count=url_count+1 where id_url='.intval($id_url).' and id_word='.intval($rs->get_field('id_word')));
	}

	function _is_parsed($id_url)
	{
		$rs = $this->DataBase->select_sql('select count(*) as cnt from %prefix%se_urls', array('id_url'=>$id_url, 'parsed'=>0, 'error'=>0));
		return ($rs->get_field('cnt') == 0);
	}
	
	function _set_parsed($id_url)
	{
		$this->DataBase->update_sql('se_urls', array('parsed'=>1), array('id_url'=>$id_url));
	}
	
	function _set_url_error($id_url, $error)
	{
		$this->DataBase->update_sql('se_urls', array('error'=>$error), array('id_url'=>$id_url));
	}
	
	function _set_url_text($id_url, $text)
	{
		$this->DataBase->update_sql('se_urls', array('page_text'=>trim($text)), array('id_url'=>$id_url));
	}
	
	function _set_url_html($id_url, $text)
	{
		$this->DataBase->update_sql('se_urls', array('page_html'=>trim($text)), array('id_url'=>$id_url));
	}

	function _set_url_title($id_url, $title)
	{
		$this->DataBase->update_sql('se_urls', array('title'=>trim($title)), array('id_url'=>$id_url));
	}
	
	// returns words from string
	function split_string($string)
	{
		$string = preg_replace('/[^�-��-ߨ�a-zA-Z0-9 ]+/s', ' ', $string);
		$string = preg_replace('/([a-zA-Z]+)([^a-zA-Z]+)/s', '$1 $2', $string);
		$string = preg_replace('/([^a-zA-Z]+)([a-zA-Z]+)/s', '$1 $2', $string);
		$string = preg_replace('/([0-9]+)([^0-9]+)/s', '$1 $2', $string);
		$string = preg_replace('/([^0-9]+)([0-9]+)/s', '$1 $2', $string);
		$string = preg_replace('/([�-��-ߨ�]+)([^�-��-ߨ�]+)/s', '$1 $2', $string);
		$string = preg_replace('/([^�-��-ߨ�]+)([�-��-ߨ�]+)/s', '$1 $2', $string);
		$string = preg_replace('/ +/s', ' ', $string);
		
		return explode(' ', trim($string));
	}
	
	function normalize_word($word, &$out_words)
	{
		$word = $this->get_normal_word($word, $out_words);
		if (!$this->is_special_word($word))
		{
			$word = preg_replace('/([uyoaqwrtpsdfghjklzxcvbnm]+)s$/is', '$1', $word);
			$word = preg_replace('/(able|ability|ed|ing|lity|or|ies|ment|al|es|ly|er|ation)$/is', '', $word);
			$word = preg_replace('/[����������������������ި]+$/is', '', $word);
		}
	
		return trim($word, "\r\n");
	}
	
	// test whether word is special (ie no modifications to this word should be performed)
	function is_special_word($word)
	{
		$rs = $this->DataBase->select_sql('se_sp_words', array('word'=>$word));
		return (!$rs->eof());
	}
	
	function get_normal_word($word, &$out_words)
	{
		$word = strtolower($word);
		$rs = $this->DataBase->select_custom_sql('select * from %prefix%se_sp_verbs where word = \''.$this->DataBase->internalEscape($word).'\' or verb_f1 = \''.$this->DataBase->internalEscape($word).'\' or verb_f2 = \''.$this->DataBase->internalEscape($word).'\' or verb_f3 = \''.$this->DataBase->internalEscape($word).'\'');
		if (!$rs->eof())
		{
			$rs = $this->DataBase->select_custom_sql('select * from %prefix%se_sp_verbs where word = \''.$this->DataBase->internalEscape($rs->get_field('word')).'\'');
			$w = $rs->get_field('word');
			if (is_array($out_words))
				while (!$rs->eof())
				{
					if (!in_array($rs->get_field('word'), $out_words)) $out_words[] = $rs->get_field('word');
					if (!in_array($rs->get_field('verb_f1'), $out_words)) $out_words[] = $rs->get_field('verb_f1');
					if (!in_array($rs->get_field('verb_f2'), $out_words)) $out_words[] = $rs->get_field('verb_f2');
					if (!in_array($rs->get_field('verb_f3'), $out_words)) $out_words[] = $rs->get_field('verb_f3');
					$rs->next();
				}
			return $w;
		}
		else
			return $word;
	}
	
	function _parse_string($id_url, $string, $priority, $current_pos)
	{
		$string = strtolower(trim($string, "\r\n"));
		if ($string == '') return;

		$words = $this->split_string($string);
		foreach ($words as $word)
			if (strlen($word))
			{
				$word = $this->normalize_word($word, $a);
				//if (strlen($word) > MIN_WORD_LENGTH)
				{
					$id_word = $this->_get_id_word($word);
					
					if ($current_pos >= 0)
						$this->_store_pos($id_url, $id_word, $current_pos++);
					$this->_store_link($id_url, $id_word, $priority);
				}
			}
	}
	
	function _store_pos($id_url, $id_word, $pos)
	{
		$this->DataBase->insert_sql('se_url_word_pos', array('id_url'=>$id_url, 'id_word'=>$id_word, 'pos'=>$pos));
	}
	
	function _combine_url($left, $right)
	{
		if (!preg_match('/^([a-zA-Z]+):/is', $right))
		{
			$left = substr($left, 0, strrpos($left, '/'));
			do
			{
				$f = false;
				if (substr($right, 0, 3) == '../')
				{
					$left = substr($left, 0, strrpos($left, '/'));
					$right = substr($right, 3);
					$f = true;
				}
				if (substr($right, 0, 2) == './')
				{
					$right = substr($right, 2);
					$f = true;
				}
				if (substr($right, 0, 1) == '/')
				{
					$i1 = strpos($left, '://');
					$i2 = strrpos($left, '/');
					while ( ($i1+2) < $i2)
					{
						$left = substr($left, 0, $i2);
						$i2 = strrpos($left, '/');
					}
						
					$right = substr($right, 1);
					$f = true;
				}
			} while ($f === true);
		
			return $left . '/' . $right;
		}
		else
			return $right;
	}
	
	function _parse_tag($base_url, $page_url, $id_url, &$xml_node, $priority, &$final_text, &$current_pos, $noindex = false)
	{
		echo " \r\n";
		flush();
		if ($xml_node->type == LWG_XML_NODE_ELEMENT)
			switch ($xml_node->tagname)
			{
				case 'script':
				case 'style':
				case 'no_index':
				{
					$noindex = true;
					break;
				}
				case 'a':
				{
					$full_url = trim($xml_node->get_attribute('href'), "\t\r\n");
					
					if (strlen($full_url) > 0)
					{
						$full_url = $this->_combine_url($base_url . $page_url, $full_url);
						
						if (preg_match('/^'.regexp_escape($base_url).'/is', $full_url))
						{
							$page_url = substr($full_url, strlen($base_url));
							$page_url = explode('#', $page_url);
							$page_url[0] = trim($page_url[0], "\t\r\n");
							if (strlen($page_url[0]) > 0)
							{
								echo '<font color="green">URL: ' . htmlspecialchars($page_url[0]) . '</font><br>';
								$id_url_to = $this->_get_id_url($page_url[0]);
								$this->_create_url_link($id_url, $id_url_to);
							}
						}
						elseif (!preg_match('/^([a-zA-Z]+):/is', $full_url))
						{
							echo '<font color="red">URL: '.htmlspecialchars($full_url) . '</font><br>';
						}
					}

					$priority += SE_A;
					if ($noindex === false)
						$this->_parse_string($id_url, trim($xml_node->get_attribute('title'), "\t\r\n"), $priority, -1);
					break;
				}
				case 'area':
				{
					$full_url = trim($xml_node->get_attribute('href'), "\t\r\n");
					if (strlen($full_url) > 0)
					{
						$full_url = $this->_combine_url($base_url . $page_url, $full_url);
						
						if (preg_match('/^'.regexp_escape($base_url).'/is', $full_url))
						{
							$page_url = substr($full_url, strlen($base_url));
							$page_url = explode('#', $page_url);
							$page_url[0] = trim($page_url[0], "\t\r\n");
							if (strlen($page_url[0]) > 0)
							{
								echo '<font color="green">URL: ' . htmlspecialchars($page_url[0]) . '</font><br>';
								$id_url_to = $this->_get_id_url($page_url[0]);
								$this->_create_url_link($id_url, $id_url_to);
							}
						}
						elseif (!preg_match('/^([a-zA-Z]+):/is', $full_url))
						{
							echo '<font color="red">URL: '.htmlspecialchars($full_url) . '</font><br>';
						}
					}
					
					$priority += SE_IMG;
					if ($noindex===false)
					{
						$this->_parse_string($id_url, $xml_node->get_attribute('alt'), $priority, -1);
						$this->_parse_string($id_url, $xml_node->get_attribute('title'), $priority, -1);
					}
					$priority -= SE_IMG;
					break;
				}
				case 'title':
				{
					$priority += SE_TITLE;
					$this->_set_url_title($id_url, $xml_node->get_leap_content(''));
					break;
				}
				case 'meta':
				{
					switch ($xml_node->get_attribute('name'))
					{
						case 'keywords':
						{
							$priority += SE_KEYWORD;
							if ($noindex===false)
								$this->_parse_string($id_url, $xml_node->get_attribute('content'), $priority, -1);
							$priority -= SE_KEYWORD;
							break;
						}
						case 'description':
						{
							$priority += SE_DESCRIPTION;
							if ($noindex===false)
								$this->_parse_string($id_url, $xml_node->get_attribute('content'), $priority, -1);
							$priority -= SE_DESCRIPTION;
							break;
						}
						case 'page_title':
						{
							$this->_set_url_title($id_url, $xml_node->get_attribute('content'));
							break;
						}
					}
					break;
				}
				case 'h1':
				{
					$priority += SE_H1;
					break;
				}
				case 'h2':
				{
					$priority += SE_H2;
					break;
				}
				case 'h3':
				{
					$priority += SE_H3;
					break;
				}
				case 'h4':
				{
					$priority += SE_H4;
					break;
				}
				case 'h5':
				{
					$priority += SE_H5;
					break;
				}
				case 'h6':
				{
					$priority += SE_H6;
					break;
				}
				case 'img':
				{	
					$priority += SE_IMG;
					if ($noindex===false)
					{
						$this->_parse_string($id_url, $xml_node->get_attribute('alt'), $priority, -1);
						$this->_parse_string($id_url, $xml_node->get_attribute('title'), $priority, -1);
					}
					$priority -= SE_IMG;
					break;
				}
				case 'strong':
				{	
					$priority += SE_STRONG;
					break;
				}
				case 'em':
				{	
					$priority += SE_EM;
					break;
				}
				case 'th':
				{	
					$priority += SE_TH;
					break;
				}
				case 'p':
				{	
					$priority += SE_P;
					break;
				}
				default:
				{
					break;
				}
			}

		if (trim($xml_node->content, "\r\n") != '')
		{
			if ($noindex===false)
			{
				$final_text .= $xml_node->content . ' ';
				$this->_parse_string($id_url, $xml_node->content, $priority, $current_pos);
			}
		}
		
		// parse childs
		$nodes = &$xml_node->child_nodes();
		foreach ($nodes as $key => $v)
			if (is_numeric($key))
				$this->_parse_tag($base_url, $page_url, $id_url, $nodes[$key], $priority, $final_text, $current_pos, $noindex);
	}
	
	function _open_url($url_string, $user_agent)
	{
		if (!function_exists('curl_init'))
		{
			$GLOBALS['lwg_xml_last_error'] = 'XML: CURL is not installed.';
			return false;
		}
		$ch = @curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		if (defined('CURLOPT_SSL_VERIFYHOST'))
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		
		global $CUrlProxy, $CUrlProxyUserName, $CUrlProxyPassword;
		if ($CUrlProxy != '')
		{
			curl_setopt($ch, CURLOPT_PROXY, $CUrlProxy);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $CUrlProxyUserName.':'.$CUrlProxyPassword);
		}
		
		if (strlen($user_agent) > 0)
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		
		$response = @curl_exec($ch);
		$cErr = @curl_errno($ch);
		
		$content_type = explode('/', curl_getinfo($ch, CURLINFO_CONTENT_TYPE), 2);
		if ( ( ($cErr) && (!$response) ) || ($content_type[0] != 'text') )
			return false;
		else
			return $response;
	}
	
	function _parse_url($base_url, $page_url)
	{
		$id_url = $this->_get_id_url($page_url);
		if ($this->_is_parsed($id_url)) return true;

		$xml_text = $this->_open_url($base_url . $page_url, 'OfficeP SE Spider v1.0 (www.officep.net)');
		if ($xml_text !== false)
		{
			$xml = &lwg_domxml_open_mem($xml_text);
			$this->_set_url_html($id_url, $xml_text);
			if ($xml === false)
			{
				$this->_set_url_error($id_url, 404);
				echo '<font color="red">' . htmlspecialchars($GLOBALS['lwg_xml_last_error']) . '</font><br>';
				return false;
			}
			else
			{
				// parse xml
				$final_text = '';
				$t1 = get_formatted_microtime();
				$current_pos = 0;
				$this->_parse_tag($base_url, $page_url, $id_url, $xml->m_Root, 1, $final_text, $current_pos);
				$t2 = get_formatted_microtime();
				echo 'SE parsing: '.($t2-$t1).'<br />' . "\r\n";
				$this->_set_url_text($id_url, $final_text);
				$this->_set_parsed($id_url);
				return true;
			}
		}
		else
		{
			$this->_set_url_error($id_url, 406);
		}
	}
	
	function on_index_submit($action)
	{
		@ob_end_clean();
		
		@header('content-type: text/html; charset=windows-1252');
		@header('connection: Keep-Alive');
		
		echo '<html><head><title></title></head><body>';
		echo '<strong>Start Site Indexing</strong><br />' . "\r\n";
		flush();
		//$GLOBALS['DebugLevel'] = 255;
		
		$base_url = $GLOBALS['HttpName'] . '://'.$GLOBALS['SiteUrl'];
		if (intval($GLOBALS['HttpPort']) != 80)
			$base_url .= ':'.$GLOBALS['HttpPort'];
		
		$this->DataBase->custom_sql('delete from %prefix%se_url_words');
		$this->DataBase->custom_sql('delete from %prefix%se_url_links');
		$this->DataBase->custom_sql('delete from %prefix%se_url_word_pos');
		//$this->DataBase->custom_sql('TRUNCATE table %prefix%se_urls');
		//$this->DataBase->custom_sql('TRUNCATE table %prefix%se_words');
		
		$this->DataBase->custom_sql('delete from %prefix%se_urls where error<>0');

		$this->DataBase->custom_sql('update %prefix%se_urls set parsed=0, error=0');
		$this->DataBase->custom_sql('update %prefix%se_words set total_count=0');
		
		$rs = $this->DataBase->select_sql('se_words');
		while (!$rs->eof())
		{
			$this->_internal_words[$rs->get_field('word')] = $rs->get_field('id_word');
			$rs->next();
		}
		
		$success = 0;
		$failed = 0;
		
		$this->_get_id_url($GLOBALS['RootPath']);
		while ( ($url = $this->_get_unparsed_url()) !== false)
		{
			echo '<nobr>'.str_pad('-', 1024, '-') . '</nobr><br />' . "\r\n";
			echo 'Parsing '.$base_url.$url.'<br />' . "\r\n";
			flush();
			set_time_limit(6000);
			$t1 = get_formatted_microtime();
			if ($this->_parse_url($base_url, $url) === true)
				$success++;
			else
				$failed++;
			$t2 = get_formatted_microtime();
			echo 'Time: '.($t2-$t1).'<br />' . "\r\n";
			flush();
		}
		echo '<strong>finished</strong><br />' . "\r\n";
		echo '<script language="JavaScript" type="text/javascript">alert(\'Finished, success: '.$success.', failed: '.$failed.'\');</script>' . "\r\n";
		echo '</body></html>' . "\r\n";
		flush();
		die();
		
		return true;
	}
	
	function _create_url_link($id_from, $id_to)
	{
		$rs = $this->DataBase->select_sql('se_url_links', array('id_url_from'=>$id_from, 'id_url_to'=>$id_to));
		if (!$rs->eof())
			$this->DataBase->custom_sql('update se_url_links set cnt=cnt+1 where id_url_from='.$id_from.' and id_url_to='.$id_to);
		else
			$this->DataBase->insert_sql('se_url_links', array('id_url_from'=>$id_from, 'id_url_to'=>$id_to, 'cnt'=>1));
	}
	
	function get_admin_names()
	{
		return 'Search Engine';
	}
	
	function run_admin_interface($module, $sub_module)
	{
		if (in_get('run_index'))
			$this->on_index_submit('');
			
		return CTemplate::parse_file(CUSTOM_TEMPLATE_PATH . 'admin/_se.tpl');
	}
	
	function check_install()
	{
		return (
			($this->DataBase->is_table('se_urls')) &&
			($this->DataBase->is_table('se_words')) &&
			($this->DataBase->is_table('se_sp_words')) &&
			($this->DataBase->is_table('se_url_words'))
			);
	}
	
	function install()
	{
		if ($this->DataBase->is_table('se_urls'))
			$this->DataBase->internalQuery('DROP TABLE %prefix%se_urls');
		$this->DataBase->internalQuery('
CREATE TABLE %prefix%se_urls (
id_url		int not null '.$this->DataBase->auto_inc_stmt.' primary key,
url			varchar(255) not null,
title		varchar(255) not null default \'\',
parsed		int not null default 0,
error		int not null default 0,
page_text	'.$this->DataBase->clob_stmt.',
page_html	'.$this->DataBase->clob_stmt.'
		)');

		$this->DataBase->internalQuery('CREATE INDEX idx_se_urls_1 on %prefix%se_urls (url)');
		$this->DataBase->internalQuery('CREATE INDEX idx_se_urls_2 on %prefix%se_urls (parsed)');
		$this->DataBase->internalQuery('CREATE INDEX idx_se_urls_3 on %prefix%se_urls (error)');

		if ($this->DataBase->is_table('se_url_links'))
			$this->DataBase->internalQuery('DROP TABLE %prefix%se_url_links');
		$this->DataBase->internalQuery('
CREATE TABLE %prefix%se_url_links (
id_url_from		int not null,
id_url_to		int not null,
cnt				int not null,
primary key (id_url_from, id_url_to)
		)');

		$this->DataBase->internalQuery('CREATE INDEX idx_se_url_links_1 on %prefix%se_url_links (id_url_from)');
		$this->DataBase->internalQuery('CREATE INDEX idx_se_url_links_2 on %prefix%se_url_links (id_url_to)');
		$this->DataBase->internalQuery('CREATE INDEX idx_se_url_links_3 on %prefix%se_url_links (cnt)');
		
		if ($this->DataBase->is_table('se_words'))
			$this->DataBase->internalQuery('DROP TABLE %prefix%se_words');
		$this->DataBase->internalQuery('
CREATE TABLE %prefix%se_words (
id_word		int not null '.$this->DataBase->auto_inc_stmt.' primary key,
word		varchar(255) not null,
total_count	int not null default 0
		)');

		$this->DataBase->internalQuery('CREATE INDEX idx_se_words_1 on %prefix%se_words (word)');
		$this->DataBase->internalQuery('CREATE INDEX idx_se_words_2 on %prefix%se_words (total_count)');

		if ($this->DataBase->is_table('se_url_words'))
			$this->DataBase->internalQuery('DROP TABLE %prefix%se_url_words');
		$this->DataBase->internalQuery('
CREATE TABLE %prefix%se_url_words (
id_url		int not null,
id_word		int not null,
priority	int not null default 0,
url_count	int not null default 0,

primary key (id_url, id_word)
		)');

		$this->DataBase->internalQuery('CREATE INDEX idx_se_url_words_1 on %prefix%se_url_words (id_url)');
		$this->DataBase->internalQuery('CREATE INDEX idx_se_url_words_2 on %prefix%se_url_words (id_word)');
		$this->DataBase->internalQuery('CREATE INDEX idx_se_url_words_3 on %prefix%se_url_words (priority)');
		$this->DataBase->internalQuery('CREATE INDEX idx_se_url_words_4 on %prefix%se_url_words (url_count)');

		if ($this->DataBase->is_table('se_url_word_pos'))
			$this->DataBase->internalQuery('DROP TABLE %prefix%se_url_word_pos');
		$this->DataBase->internalQuery('
CREATE TABLE %prefix%se_url_word_pos (
id_url	int not null,
id_word	int not null,
pos		int not null,

primary key (id_url, id_word, pos)
		)');

		$this->DataBase->internalQuery('CREATE INDEX idx_seurl_wordpos1 on %prefix%se_url_word_pos (id_url)');
		$this->DataBase->internalQuery('CREATE INDEX idx_seurl_wordpos2 on %prefix%se_url_word_pos (id_word)');
		$this->DataBase->internalQuery('CREATE INDEX idx_seurl_wordpos3 on %prefix%se_url_word_pos (pos)');

		if ($this->DataBase->is_table('se_sp_words'))
			$this->DataBase->internalQuery('DROP TABLE %prefix%se_sp_words');
		$this->DataBase->internalQuery('
CREATE TABLE %prefix%se_sp_words (
id_sp_word		int not null '.$this->DataBase->auto_inc_stmt.',
word			varchar(255),
primary key (id_sp_word)
		)');
		$this->DataBase->internalQuery('CREATE UNIQUE INDEX idxu_ssw_w on %prefix%se_sp_words (word)');

		if ($this->DataBase->is_table('se_sp_verbs'))
			$this->DataBase->internalQuery('DROP TABLE %prefix%se_sp_verbs');
		$this->DataBase->internalQuery('
CREATE TABLE %prefix%se_sp_verbs (
id_verb		int not null '.$this->DataBase->auto_inc_stmt.',
word		varchar(255),
verb_f1		varchar(255),
verb_f2		varchar(255),
verb_f3		varchar(255),
primary key (id_verb)
		)');
		
		$this->DataBase->internalQuery('CREATE INDEX idx_se_sp_verbs_1 on %prefix%se_sp_verbs (word)');
		$this->DataBase->internalQuery('CREATE INDEX idx_se_sp_verbs_2 on %prefix%se_sp_verbs (verb_f1)');
		$this->DataBase->internalQuery('CREATE INDEX idx_se_sp_verbs_3 on %prefix%se_sp_verbs (verb_f2)');
		$this->DataBase->internalQuery('CREATE INDEX idx_se_sp_verbs_4 on %prefix%se_sp_verbs (verb_f3)');

		if ($this->DataBase->is_table('se_queries'))
			$this->DataBase->internalQuery('DROP TABLE %prefix%se_queries');
		$this->DataBase->internalQuery('
CREATE TABLE %prefix%se_queries (
id_query		int not null '.$this->DataBase->auto_inc_stmt.',
query			varchar(255),
result_count	int not null default 0,
query_date		'.$this->DataBase->datetime_stmt.' not null,
primary key (id_query)
		)');
		
		// special words initialization, usually acronyms
		$this->DataBase->insert_sql('se_sp_words', array('word'=>'cms'));
		$this->DataBase->insert_sql('se_sp_words', array('word'=>'sql'));
		
		// irregular verbs initialization
		$this->DataBase->insert_sql('se_sp_verbs', array('word'=>'be', 'verb_f1'=>'be', 'verb_f2'=>'was', 'verb_f3'=>'been'));
		$this->DataBase->insert_sql('se_sp_verbs', array('word'=>'be', 'verb_f1'=>'be', 'verb_f2'=>'were', 'verb_f3'=>'been'));
		$this->DataBase->insert_sql('se_sp_verbs', array('word'=>'be', 'verb_f1'=>'am', 'verb_f2'=>'was', 'verb_f3'=>'been'));
		$this->DataBase->insert_sql('se_sp_verbs', array('word'=>'be', 'verb_f1'=>'am', 'verb_f2'=>'were', 'verb_f3'=>'been'));
		$this->DataBase->insert_sql('se_sp_verbs', array('word'=>'be', 'verb_f1'=>'are', 'verb_f2'=>'was', 'verb_f3'=>'been'));
		$this->DataBase->insert_sql('se_sp_verbs', array('word'=>'be', 'verb_f1'=>'are', 'verb_f2'=>'were', 'verb_f3'=>'been'));
		$this->DataBase->insert_sql('se_sp_verbs', array('word'=>'be', 'verb_f1'=>'is', 'verb_f2'=>'was', 'verb_f3'=>'been'));
		$this->DataBase->insert_sql('se_sp_verbs', array('word'=>'be', 'verb_f1'=>'is', 'verb_f2'=>'were', 'verb_f3'=>'been'));
		
		$this->DataBase->insert_sql('se_sp_verbs', array('word'=>'have', 'verb_f1'=>'have', 'verb_f2'=>'had', 'verb_f3'=>'had'));
		$this->DataBase->insert_sql('se_sp_verbs', array('word'=>'have', 'verb_f1'=>'has', 'verb_f2'=>'had', 'verb_f3'=>'had'));

		$this->DataBase->insert_sql('se_sp_verbs', array('word'=>'do', 'verb_f1'=>'do', 'verb_f2'=>'did', 'verb_f3'=>'done'));
		$this->DataBase->insert_sql('se_sp_verbs', array('word'=>'go', 'verb_f1'=>'go', 'verb_f2'=>'went', 'verb_f3'=>'gone'));
	}
}
?>