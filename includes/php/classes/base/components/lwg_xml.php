<?php
/**
 * @package LLA.Base
 */
/**
 */
/*
XML Parser Module version 1.1.0.1

history:
	v1.1.0.1 revised/refactored (VK)
	v1.1.0.0 RW version (VK)
	v1.0.0.0 RO version created (VK)

Interface:
CLWG_dom_xml& lwg_domxml_create()
	creates empty domxml object
CLWG_dom_xml& lwg_domxml_open_mem(String XMLData);
	returns FALSE if error found in XMLData
CLWG_dom_xml& lwg_domxml_open_file(String XMLFileName);
	returns FALSE if error found in XMLFileName

CLWG_dom_xml members:
	properties:
		String $m_Encoding;
			current xml encoding, default is windows-1252
		String $m_Version;
			current xml version, default is 1.0
	methods:
		void createXML(void)
			create empty XML document
		CLWG_dom_node& document_element(void);
			return root element
		String get_leap_content(path)
			return content of node within path, ie: get_leap_content('child_1/child_2/child_with_content');
		CLWG_dom_node&[] selectNodes(path)
			return array of nodes with the same name in the path
		String getXML(void);
			return well-formed xml document

CLWG_dom_node members:
	properties:
		Int $type;
			LWG_XML_NODE_NULL (0)
			LWG_XML_NODE_ELEMENT (1)
			LWG_XML_NODE_TEXT (3)
			LWG_XML_NODE_DOCUMENT (9)
		String $tagname;
			Name of the node

	methods:
		CLWG_dom_attribute& get_attribute(String AttributeName);
			returns attribute value or empty string if apsent
		String get_content();
			returns text node content or empty string
		String get_leap_content(path)
			returns content of node within path, ie: get_leap_content('chil_1/child_2/child_with_content');
		CLWG_dom_node&[] selectNodes(path)
			returns array of nodes with the same name in the path
		CLWG_dom_attribute&[] attributes();
			returns attributes array
		CLWG_dom_node&[] child_nodes();
			returns child nodes array

CLWG_dom_attribute members:
	properties:
		String $name;
			name of the attribute
		String $value;
			value of the attribute

// example of using
header('content-type: application/xml');
$x = lwg_domxml_open_file('xml_test.xml');
if ($x)
{
	$n = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'test', 'content');
	$t = &$x->m_Root->appendChild($n);
	$t->appendAttribute('q', 'w');
	$n->appendAttribute('t', 'e');
	echo $x->getXML();
}
else
	echo 'fatal error: ' . $GLOBALS['lwg_xml_last_error'];
*/

define('LWG_XML_DEBUG', '0'); // set 1 to turn on debug message during parsing process

define('LWG_XML_NODE_NULL', '0');
define('LWG_XML_NODE_ELEMENT', '1');
define('LWG_XML_NODE_TEXT', '3');
define('LWG_XML_NODE_DOCUMENT', '9');

define('CRLF', "\r\n");

$GLOBALS['lwg_xml_last_error'] = '';

function entitiesToString($str)
{
	if (trim(strval($str)) == '') return '';
  	$s = array('/&rsquo;/i', '/&ldquo;/i', '/&rdquo;/i');
	$r = array('\'', '"', '"');
	$str = preg_replace($s, $r, strval($str));
	if (@function_exists('html_entity_decode'))
		$str = @html_entity_decode($str);
	else
	{
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		$str = strtr($str, $trans_tbl);
	}
	return $str;
}

function file_to_xml_value($file)
{
	if (!is_file($file)) return '';
	$value = '';
	$fh = @fopen($file, 'rb');
	if ($fh) {
		$value = base64_encode(fread($fh, filesize($file)));
		fclose($fh);
	}
	return $value;
}

/**
 * @package LLA.Base
 */
class CLWG_dom_attribute {
	var $name;
	var $value;
	function CLWG_dom_attribute($name='', $value='') {
		$this->name = $name;
		$this->value = entitiesToString($value);
	}
} // CLWG_dom_attribute

/**
 * @package LLA.Base
 */
class CLWG_dom_node
{
	var $m_Attributes;
	var $m_Childs;

	var $type;
	var $tagname;
	var $content;

	function CLWG_dom_node($type, $tagname = '', $content = '')
	{
		$this->m_Attributes = array();
		$this->m_Childs = array();

		$this->type = $type;
		$this->tagname = $tagname;
		$this->content = '';
		if ( ($this->type == LWG_XML_NODE_ELEMENT) && ($content != '') )
		{
			$t = new CLWG_dom_node(LWG_XML_NODE_TEXT);
			$t->content = entitiesToString($content);
			$this->appendChild($t);
		}
	}

	function get_attribute($attr_name)
	{
		for ($i=0; $i<sizeof($this->m_Attributes); $i++)
			if ($this->m_Attributes[$i]->name == $attr_name)
				return $this->m_Attributes[$i]->value;
		//trigger_error('Attribute '.$attr_name.' not found', E_USER_NOTICE);
		return '';
	}

	function get_content()
	{
		return $this->content;
	}

	function &attributes()
	{
		return $this->m_Attributes;
	}

	function &child_nodes()
	{
		// need to be rewritten
		return $this->m_Childs;
	}

	function appendAttribute($name, $value)
	{
		$a = new CLWG_dom_attribute($name, $value);
		$this->m_Attributes[] = &$a;
	}

	function &appendChild(&$xml_node)
	{
		$this->m_Childs[] = &$xml_node;
		if ($xml_node->tagname != '')
		{
			if (!isset($this->m_Childs[$xml_node->tagname]))
				$this->m_Childs[$xml_node->tagname] = array();
			$this->m_Childs[$xml_node->tagname][] = &$xml_node;
		}
		return $xml_node;
	}

	function get_attributes_string()
	{
		if (LWG_XML_DEBUG)
		{
			echo '<debug>DEBUG: get_attributes_string</debug><br />' . CRLF;
			flush();
		}
		$out_string = '';
		$atrs = array_keys($this->m_Attributes);
		foreach ($atrs as $k)
			$out_string .= ' ' . $this->m_Attributes[$k]->name . '="' . htmlspecialchars($this->m_Attributes[$k]->value) . '"';
		return $out_string;
	}

	/**
	* @return boolean
	* @param string $str
	* @param integer $i
	* @desc Loads xml from $str, starting from $i, returns last position in $i
	*/
	function loadXML($str, &$i)
	{
		// comments cutting
		if ($i == 0)
		{
			$j = strpos($str, '<!--');
			while ($j !== false)
			{
				$j2 = strpos($str, '-->', $j);
				if ($j2 === FALSE)
				{
					$GLOBALS['lwg_xml_last_error'] = 'Unexpected end of file, during comment end searching.';
					return FALSE;
				}
				$str = substr($str, 0, $j) . substr($str, $j2+3);
				$j = strpos($str, '<!--');
			}
		}
		
		if (LWG_XML_DEBUG)
		{
			echo '<debug>DEBUG: LoadXML ('.$i.': '.$str[$i].')</debug><br />' . CRLF;
			flush();
		}
		$str_len = strlen($str);
		if (LWG_XML_DEBUG)
		{
			echo '<debug>DEBUG: start searching for tag ('.$i.': '.$str[$i].')</debug><br />' . CRLF;
			flush();
		}
		while ( ($i<$str_len) && ($str[$i] != '<') ) $i++;
		if ($i == $str_len) {
			$GLOBALS['lwg_xml_last_error'] = 'Unexpected end of file, during tag start search.';
			return FALSE;
		}
		
		if (strcmp(substr($str, $i, 9), '<!DOCTYPE') == 0)
		{
			$i = strpos($str, '>', $i);
			if ($i===FALSE) {
				$GLOBALS['lwg_xml_last_error'] = 'Unexpected end of file, during doctype end searching.';
				return FALSE;
			}
			while ( ($i<$str_len) && ($str[$i] != '<') ) $i++;
			//return $i;
		}
		if (strcasecmp(substr($str, $i, 9), '<![CDATA[') == 0)
		{
			$i2 = strpos($str, ']]>', $i);
			if ($i2===FALSE) {
				$GLOBALS['lwg_xml_last_error'] = 'Unexpected end of file, during cdata end searching.';
				return FALSE;
			}
			while ( ($i<$str_len) && ($str[$i] != '<') ) $i++;
			$tmp_text = substr($str, $i+9, $i2-$i-9);
			$this->type = LWG_XML_NODE_TEXT;
			$this->content = $tmp_text;
			$i = $i2+3;
			return $i;
		}
		$i++;

		while ( ($i<strlen($str)) && ($str[$i] != ' ') && ($str[$i] != '/') && ($str[$i] != '>') )
			$this->tagname .= $str[$i++];

		if (LWG_XML_DEBUG)
		{
			echo '<debug>DEBUG: Tag: ' . $this->tagname . '</debug><br />' . CRLF;
			flush();
		}

		if ($i == $str_len) return FALSE;
		switch ($str[$i])
		{
			case ' ': // attributes are comming
			{
				if (LWG_XML_DEBUG)
				{
					echo '<debug>DEBUG: Tag: start searching attributes</debug><br />' . CRLF;
					flush();
				}
				$i++;
				while ( ($i<strlen($str)) && ($str[$i] != '/') && ($str[$i] != '>') )
				{
					$t = new CLWG_dom_attribute();
					while ( ($i<strlen($str)) && ($str[$i] != '=') )
						$t->name .= $str[$i++];
					if ($i == $str_len) return FALSE;
					$i++;
					while ( ($i<strlen($str)) && ($str[$i] != '"') )
						$i++;
					if ($i == $str_len) return FALSE;
					$i++;
					while ( ($i<strlen($str)) && ($str[$i] != '"') )
						$t->value .= $str[$i++];

					$t->value = entitiesToString($t->value);
					$this->m_Attributes[] = &$t;

					if (LWG_XML_DEBUG)
					{
						echo '<debug>DEBUG: Tag: Attribute: ['.$t->name.'] = ['.$t->value.']</debug><br />' . CRLF;
						flush();
					}

					if ($i == $str_len) return FALSE;
					$i++;
					if ($i == $str_len) return FALSE;
					while ( ($i<strlen($str)) && ($str[$i] == ' ') )
						$i++;
				}
				if ($i == $str_len) return FALSE;
				switch ($str[$i])
				{
					case '/': // self closing tag with attributes
					{
						if (LWG_XML_DEBUG)
						{
							echo '<debug>DEBUG: self closing tag with attributes ('.$this->tagname.')</debug><br />' . CRLF;
							flush();
						}
						$i++;
						if ($i == $str_len) return FALSE;
						if ($str[$i] != '>') return FALSE;
						$i++;
						return TRUE;
						break;
					}
					case '>';
					{
						if (LWG_XML_DEBUG)
						{
							echo '<debug>DEBUG: end of attributes ('.$this->tagname.')</debug><br />' . CRLF;
							flush();
						}
						$i++;
						break;
					}
				}
				break;
			}
			case '/': // self closing tag
			{
				if (LWG_XML_DEBUG)
				{
					echo '<debug>DEBUG: self closing tag ('.$this->tagname.')</debug><br />' . CRLF;
					flush();
				}
				$i++;
				if ($i == $str_len) return FALSE;
				if ($str[$i] != '>') {
					$GLOBALS['lwg_xml_last_error'] = 'Invalid tag-closing in tag '.$this->tagname.'.';
					return FALSE;
				}
				$i++;
				return TRUE;
				break;
			}
			case '>': // end of begin of node
			{
				if (LWG_XML_DEBUG)
				{
					echo '<debug>DEBUG: end of begin of node</debug><br />' . CRLF;
					flush();
				}
				$i++;
				break;
			}
		}
		if ($i == $str_len) return FALSE;

		$b = true;

		while ( ($i<$str_len) && ($b) )
		{
			if (LWG_XML_DEBUG)
			{
				echo '<debug>DEBUG: searching for content</debug><br />' . CRLF;
				flush();
			}
			$tmp_text = '';
			while ( ($i<strlen($str)) && ($str[$i] != '<') )
				$tmp_text .= $str[$i++];
			if (LWG_XML_DEBUG)
			{
				echo '<debug>DEBUG: content: '.$tmp_text.'</debug><br />' . CRLF;
				flush();
			}
			if ($i == $str_len) return FALSE;
			$i++;
			if ($i == $str_len) return FALSE;
			if ($str[$i] != '/') // new child
			{
				if ($tmp_text != '')
				{
					$t = new CLWG_dom_node(LWG_XML_NODE_TEXT);
					$t->content = entitiesToString($tmp_text);
					$this->appendChild($t);
					$tmp_text = '';
				}
				if (LWG_XML_DEBUG)
				{
					echo '<debug>DEBUG: Create new child</debug><br />' . CRLF;
					flush();
				}

				$t = new CLWG_dom_node(LWG_XML_NODE_ELEMENT);
				$i--;
				if ($t->loadXML($str, $i) === FALSE) return FALSE;
				$this->appendChild($t);
				if (LWG_XML_DEBUG)
				{
					echo '<debug>DEBUG: name of node: ' . $t->tagname . '</debug><br />' . CRLF;
					flush();
				}
			}
			else
				$b = false;
		}

		if ($tmp_text != '')
		{
			$t = new CLWG_dom_node(LWG_XML_NODE_TEXT);
			$t->content = entitiesToString($tmp_text);
			$this->appendChild($t);
		}

		$i++;
		$close_tag = '';
		while ( ($i<strlen($str)) && ($str[$i] != '>') )
			$close_tag .= $str[$i++];
		if (LWG_XML_DEBUG)
		{
			echo '<debug>DEBUG: close tag: '.$close_tag.' - '.$this->tagname.'</debug><br />' . CRLF;
			flush();
		}
		if ($i == $str_len) return FALSE;
		$i++;
		if (strcmp($close_tag, $this->tagname) != 0)
		{
			$GLOBALS['lwg_xml_last_error'] = 'You should close '.$this->tagname.' before closing of '.$close_tag.' at byte ('.$i.').';
			return FALSE;
		}
		return TRUE;
	}

	function getXML()
	{
		if ($this->type != LWG_XML_NODE_TEXT)
			if (sizeof($this->m_Childs))
			{
				$out_string = '<' . $this->tagname . $this->get_attributes_string(). '>';
				$chlds = array_keys($this->m_Childs);
				foreach($chlds as $k)
					if (is_numeric($k))
						$out_string .= $this->m_Childs[$k]->getXML();
				$out_string .= '</' . $this->tagname . '>';
				return $out_string;
			}
			else
				return '<' . $this->tagname . $this->get_attributes_string(). ' />';
		else
			return htmlspecialchars($this->content);
	}

	function get_leap_content($path, $default = '')
	{
		if ($path != '')
		{
			$step = explode('/', $path, 2);
			if (isset($this->m_Childs[$step[0]]))
				if (sizeof($step) > 1) // down level
					return $this->m_Childs[$step[0]][0]->get_leap_content($step[1], $default);
				else
					if (isset($this->m_Childs[$step[0]][0]->m_Childs[0])) // not empty node
						return $this->m_Childs[$step[0]][0]->m_Childs[0]->content;
					else
						return $default;
			else
				return $default;
		}
		else
		{
			if (isset($this->m_Childs[0]))
				return $this->m_Childs[0]->content;
			else
				return $default;
		}
	}

	function &selectNodes($path)
	{
		$step = explode('/', $path, 2);
		if (isset($this->m_Childs[$step[0]]))
		{
			if (sizeof($step) > 1)
				return $this->m_Childs[$step[0]][0]->selectNodes($step[1]);
			else
				return $this->m_Childs[$step[0]];
		}
		else
		{
			$r = array();
			return $r;
		}
	}
} // CLWG_dom_node

/**
 * @package LLA.Base
 */
class CLWG_dom_xml
{
	var $m_Root;
	var $m_Encoding;
	var $m_Version = '1.0';

	function CLWG_dom_xml()
	{
		$this->m_Root = 0;
		$this->m_Encoding = 'windows-1252';
	}

	function &document_element()
	{
		return $this->m_Root;
	}

	function get_leap_content($path)
	{
		return $this->m_Root->get_leap_content($path);
	}

	function &selectNodes($path)
	{
		return $this->m_Root->selectNodes($path);
	}

	function createXML()
	{
		$this->m_Root = new CLWG_dom_node(LWG_XML_NODE_DOCUMENT);
	}

	/**
	* @return boolean
	* @param string $xml_string
	* @desc loads xml from $xml_string and parses it into object tree
	*/
	function loadXML($xml_string)
	{
		$GLOBALS['lwg_xml_last_error'] == '';
		// check xml tag
		if (eregi('<\?xml', $xml_string))
		{
			// check xml version
			$xml_version = array();
			if ( (eregi('<\?xml version="([0-9\.]+)".*\?>', $xml_string, $xml_version)) && ($xml_version[1] == 1.0) )
			{
				// initialize root
				$this->m_Root = new CLWG_dom_node(LWG_XML_NODE_DOCUMENT);
				$i = 0;
				return $this->m_Root->loadXML(preg_replace('|<\?xml.*?\?>|', '', $xml_string), $i);
			}
			else
			{
				$GLOBALS['lwg_xml_last_error'] = 'XML: Cannot find version attribute in xml tag';
				return FALSE;
			}
		}
		else
		{
			$GLOBALS['lwg_xml_last_error'] = 'XML: Cannot find xml tag';
			return FALSE;
		}
	}

	function getXML()
	{
		if (is_object($this->m_Root))
		{
			$out_string = '<?xml version="' . htmlspecialchars($this->m_Version) . '" encoding="' . htmlspecialchars($this->m_Encoding) . '" ?>' . CRLF;
			$out_string .= $this->m_Root->getXML() . CRLF;

			return $out_string;
		}
		else
			trigger_error('Invalid XML Object', E_USER_ERROR);

	}
} // CLWG_dom_xml

function &lwg_domxml_create()
{
	$GLOBALS['lwg_xml_last_error'] = '';
	$lwg_xml = new CLWG_dom_xml();
	$lwg_xml->createXML();
	return $lwg_xml;
}

function &lwg_domxml_open_mem($xml_string)
{
	$GLOBALS['lwg_xml_last_error'] = '';
	$lwg_xml = new CLWG_dom_xml();

	if ($lwg_xml->loadXML($xml_string))
		return $lwg_xml;
	else
		return FALSE;
}

$gLWG_XMLLastFileContent = '';

function &lwg_domxml_open_url($url_string, $user_agent = '')
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
	if (defined("CURLOPT_SSL_VERIFYHOST"))
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	global $CUrlProxy, $CUrlProxyUserName, $CUrlProxyPassword;
	if ($CUrlProxy != '')
	{
		curl_setopt($ch, CURLOPT_PROXY, $CUrlProxy);
		curl_setopt($ch, CURLOPT_PROXYUSERPWD, $CUrlProxyUserName.":".$CUrlProxyPassword);
	}
	
	if (strlen($user_agent) > 0)
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
	
	$response = @curl_exec($ch);
	$cErr = @curl_errno($ch);
	if ( ($cErr) && (!$response) )
	{
		$GLOBALS['lwg_xml_last_error'] = 'XML: Cannot open url '.$url_string;
		return false;
	}
	else
	{
		$GLOBALS['gLWG_XMLLastFileContent'] = $response;
		$xml = &lwg_domxml_open_mem($response);
		return $xml;
	}
}

function &lwg_domxml_open_file($xml_file)
{
	$GLOBALS['lwg_xml_last_error'] = '';
	if ( ($xml_file!='') && (file_exists($xml_file)) )
		return internal_read_xml_file($xml_file);
	else
	{
		$GLOBALS['lwg_xml_last_error'] = 'XML: Cannot find file '.$xml_file;
		return FALSE;
	}
}

function &internal_read_xml_file($xml_file)
{
	$hFile = @fopen($xml_file, 'r');
	if ($hFile)
	{
		$content = fread($hFile, filesize($xml_file));
		fclose($hFile);
		return lwg_domxml_open_mem($content);
	}
	else
	{
		$GLOBALS['lwg_xml_last_error'] = 'XML: Cannot open file '.$xml_file;
		return false;
	}
}
?>