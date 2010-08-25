<?
/**
 * @package LLA.Base
 */
/**
 */
define('FEDEX_ACCOUNT', '');
define('FEDEX_METER', '');
define('FEDEX_EXT', 'png'); // png or epl

define('FEDEX_SENDER_COMPANY', '');
define('FEDEX_SENDER_EMAIL', '');
define('FEDEX_SENDER_ADDRESS', '');
define('FEDEX_SENDER_CITY', '');
define('FEDEX_SENDER_STATE', '');
define('FEDEX_SENDER_ZIP', '');
define('FEDEX_SENDER_COUNTRY', 'US');
define('FEDEX_SENDER_PHONE', '');

define('FEDEX_POST_HOST', 'gatewaybeta.fedex.com'); // gateway.fedex.com

define("FEDEX_TOTAL", "1419");

global $FilePath;
$GLOBALS['ShowPNG'] = $FilePath . 'admin/labels/';
if (file_exists($GLOBALS['ShowPNG']))
	@chmod($GLOBALS['ShowPNG'], 0777);
else
	if (!@mkdir($GLOBALS['ShowPNG'], 0777))
	{
		@chmod($FilePath, 0777);
		@chmod($FilePath . 'admin/', 0777);
		@mkdir($GLOBALS['ShowPNG'], 0777);
	}
	
function normalize_weight($val)
{
	if (!is_numeric($val)) system_die('Invalid price - normalize_price()');
	$data_parts = explode('.', strval($val));
	if (sizeof($data_parts) == 1) return strval($val).".0";

	if (strlen($data_parts[1]) > 2)
		$data_parts[1] = substr($data_parts[1], 0, 1);
	elseif (strlen($data_parts[1]) == 1)
		$data_parts[1] = $data_parts[1];
		
	return $data_parts[0].'.'.$data_parts[1];
}

/**
 * @package LLA.Base
 */
class CFedEx extends CObject
{
	var $query;
	var $query_array;

	var $result;
	var $result_array;

	var $Application;
	var $DataBase;
	
	var $latest_ground_id = -1;
	var $lastError = '';
	
	function CFedEx(&$app)
	{
		parent::CObject();
		
		$this->Application = &$app;
		$this->DataBase = &$this->Application->get_module("DataBase");
	}
	
	function get_last_error()
	{
		return $this->lastError;
	}

	function subscribe()
	{
		$this->query_array = array( 
			0 => "211",
			1 => "Subscribe",
			10 => FEDEX_ACCOUNT,
			4003 => FEDEX_SENDER_COMPANY,
			4007 => FEDEX_SENDER_COMPANY,
			4008 => FEDEX_SENDER_ADDRESS,
			4011 => FEDEX_SENDER_CITY,
			4012 => FEDEX_SENDER_STATE,
			4013 => FEDEX_SENDER_ZIP,
			4014 => FEDEX_SENDER_COUNTRY,
			4015 => FEDEX_SENDER_PHONE
		);

		if (!$this->Post())
		{
			$this->lastError = 'Cannot connect to FedEx server';
			return FALSE;
		}
		$this->ParseReply();
		
		return $this->result_array[FEDEX_TOTAL];
	}
	
	/*function get_services()
	{
		$arr = array(
"FDXE:01" => "FedEx Priority",
"FDXE:03" => "FedEx 2day",
"FDXE:05" => "FedEx Standard Overnight",
"FDXE:06" => "FedEx First Overnight",
"FDXE:20" => "FedEx Express Saver",
"FDXE:70" => "FedEx 1day Freight",
"FDXE:80" => "FedEx 2day Freight",
"FDXE:83" => "FedEx 3day Freight",
"FDXG:90" => "FedEx Home Delivery",
"FDXG:92" => "FedEx Ground Service");

		return $arr;
	}
	*/
	
	function get_rate_price($service, $country, $state, $zip, $weight)
	{
		$this->lastError = 'Cannot connect to FedEx server';
		list($carrier_code, $service_type) = explode(":", $service);

		$this->query_array = array(
			0 => "022", // request type: ship a package
			1 => "Check Rate", // transaction id
			10 => FEDEX_ACCOUNT, // account
			498 => FEDEX_METER, // meter
			8 => FEDEX_SENDER_STATE, // sender state
			9 => FEDEX_SENDER_ZIP, // sender zip
			117 => FEDEX_SENDER_COUNTRY, // sender country
			16 => $state, // receiver state
			17 => $zip, // receiver zip
			68 => "USD", // currency
			75 => "LBS", // weight
			116 => "1", // piece count
			1401 => normalize_weight($weight), // total weight
			1273 => "01", //package type 01 - other (ground), 01 - other (fedex)
			50 => $country, // country
			3025 => $carrier_code, // carrier code FDXG, FDXE
			1274 => $service_type // service type 92 - ground, 05 - standart overnight, 06 - first overnight
		);
		
		if (!$this->Post())
		{
			$this->lastError = "Cannot connect to FedEx server";
			return FALSE;
		}

		$this->ParseReply();
		
		if (isset($this->result_array[FEDEX_TOTAL]))
			return $this->result_array[FEDEX_TOTAL];
		else
		{
			$this->lastError = 'FedEx: '.$this->result_array[3];
			return FALSE;
		}
	}
	
	function get_services()
	{
		return array(
'FDXG:90' => 'FedEx Home Delivery',
'FDXG:92' => 'FedEx Ground Service',
'FDXE:01' => 'FedEx Priority',
'FDXE:03' => 'FedEx 2day',
'FDXE:05' => 'FedEx Standard Overnight',
'FDXE:06' => 'FedEx First Overnight',
'FDXE:20' => 'FedEx Express Saver',
'FDXE:70' => 'FedEx 1day Freight',
'FDXE:80' => 'FedEx 2day Freight',
'FDXE:83' => 'FedEx 3dayFreight',
'FDXE:01' => 'FedEx International Priority',
'FDXE:03' => 'FedEx International Economy',
'FDXE:06' => 'FedEx International First',
'FDXE:70' => 'FedEx International Priority Freight',
'FDXE:86' => 'FedEx International Economy Freight');
	}
	
	function ship_void($tr_num, $service)
	{
		list($carrier_code, $service_type) = explode(":", $service);
		$this->query_array = array( 
			0 => "023", // request type: delete
			1 => "Delete of order", // transaction id
			10 => FEDEX_ACCOUNT, // account
			498 => FEDEX_METER, // meter
			29 => $tr_num, // what to delete
			3025 => $carrier_code // carrier code FDXG, FDXE
		);
			
		if (!$this->Post())
		{
			$this->lastError = "Cannot connect to FedEx server";
			return FALSE;
		}
		$this->ParseReply();
		
		return $this->result_array;
	}
	
	function close_ground($close = 'Y')
	// Y - only download
	// N - close
	{
		$dt = strftime("%Y%m%d");
		$tm = strftime("%H%M%S");
		$this->query_array = array( 
			0 => "007", // close
			1 => "Close of the day", // transaction id
			10 => FEDEX_ACCOUNT, // account
			498 => FEDEX_METER, // meter
			3025 => "FDXG", // carrier code FDXG, FDXE
			1007 => $dt, // date
			1366 => $tm, // time
			//3014 => "1",
			1371 => $close
		);
		
		if (!$this->Post())
		{
			$this->lastError = "Cannot connect to FedEx server";
			return FALSE;
		}
		$this->ParseReply();

		if (isset($this->result_array[1372]))
		{
			global $PNGImages;
			@mkdir($PNGImages . "manifest" . "/", 0777);
			for ($i=1; $i<=$this->result_array[1372]; $i++)
			{
				$this->DebugInfo->Write("Manifest: ".$this->result_array["1005-".$i]);
				$fh = fopen($PNGImages . "manifest" . "/" . $this->result_array["1005-".$i] . ".txt", "wb");
				$bs = preg_replace('/%00/', chr(0x00), $this->result_array["1367-".$i]);
				$bs = preg_replace('/%22/', chr(0x22), $bs);
				$bs = preg_replace('/%25/', chr(0x25), $bs);
				fwrite($fh, $bs);
				fclose($fh);
			}
		}
		
		return $this->result_array;
	}

	function ship_package(&$ship_to_info_assoc, &$pr_info, &$st_m)
	{
		$this->query_array = array( 
			0 => "021", // request type: ship a package
			1 => "Ship of the order", // transaction id
			10 => FEDEX_ACCOUNT, // account
			498 => FEDEX_METER, // meter
			
			4 => FEDEX_SENDER_COMPANY, // sender company
			5 => FEDEX_SENDER_ADDRESS, // sender address
			7 => FEDEX_SENDER_CITY, // sender city
			8 => FEDEX_SENDER_STATE, // sender state
			9 => FEDEX_SENDER_ZIP, // sender zip
			117 => FEDEX_SENDER_COUNTRY, // sender country
			183 => FEDEX_SENDER_PHONE, //phone
			
			11 => $ship_to_info_assoc->get_field("shipping_company"),
			12 => $ship_to_info_assoc->get_field("shipping_first_name") . " " . $ship_to_info_assoc->get_field("shipping_last_name"),
			13 => $ship_to_info_assoc->get_field("shipping_address"),
			14 => $ship_to_info_assoc->get_field("shipping_address2"),
			15 => $ship_to_info_assoc->get_field("shipping_city"),
			16 => $st_m->GetStateShortName($ship_to_info_assoc->get_field("id_shipping_state")),
			17 => $ship_to_info_assoc->get_field("shipping_zip"),
			18 => normalize_phone($ship_to_info_assoc->get_field("shipping_phone")),
			
			1119 => "N", // future date?
			//24 => "20040223",
			23 => "1", /* 1=Bill sender (Prepaid)
						2=Bill recipient (Collect for FedEx Express; Bill for FedEx Ground)
						3=Bill third party
						4=Bill credit card
						5=Bill recipient for FedEx Ground */
			68 => "USD",
			70 => "1", // same as 23, but for int
			75 => "LBS",
			1349 => "C",
			//1358 => "0", // FTSR 

			116 => "1", // piece count
			1401 => normalize_weight(0.5*$pr_info->get_field("total_count")), // total weight
			
			1273 => "01", //package type 01 - other (ground), 01 - other (fedex)
			
			1553 => "Y",
			1554 => "Y",
			1201 => FEDEX_SENDER_EMAIL
		);

		$sh_cntry = $ship_to_info_assoc->get_field("shipping_country");
		if ($sh_cntry == "U.S.A.")
			$sh_cntry = "US";
			
		$this->query_array[50] = $sh_cntry;
		$this->query_array[74] = $sh_cntry;
		if (FEDEX_EXT == "png")
		{
			$this->query_array[1368] = "1"; // standart label
			$this->query_array[1369] = "1"; // laser printer
			$this->query_array[1370] = "5"; // 5 - plain paper
		}
		else
		{
			$this->query_array[1368] = "1"; // standart label
			$this->query_array[1369] = "2"; // Eltron Orion, Zebra LP2443, LP2844, LP2348 Plus
			$this->query_array[1370] = "4"; // 5 - plain paper
		}
		
		if ($ship_to_info_assoc->get_field("shipping_serv") == 1)
		{
			$this->query_array[3025] = "FDXG"; // ground
			$this->query_array[1274] = "92"; // service type 92 - ground, 05 - standart overnight, 06 - first overnight
		}
		if ($ship_to_info_assoc->get_field("shipping_serv") == 2)
		{
			$this->query_array[3025] = "FDXE"; // express
			$this->query_array[1274] = "05"; // service type 92 - ground, 05 - standart overnight, 06 - first overnight
		}
		if ($ship_to_info_assoc->get_field("shipping_serv") == 3)
		{
			$this->query_array[3025] = "FDXG"; // ground
			$this->query_array[1274] = "92"; // service type 92 - ground, 05 - standart overnight, 06 - first overnight
		}
		
		if (!$this->Post())
		{
			$this->lastError = 'Cannot connect to FedEx server';
			return FALSE;
		}
		$this->ParseReply();
		
		if ( (isset($this->result_array[29])) && (isset($this->result_array[188])) )
		{
			global $PNGImages;
			$fh = 0;
			if ($this->query_array[1370] == "5")
				$fh = fopen($PNGImages . $this->result_array[29] . ".png", "wb");
			else
				$fh = fopen($PNGImages . $this->result_array[29] . ".txt", "wb");
			$bs = preg_replace('/%00/', chr(0x00), $this->result_array[188]);
			$bs = preg_replace('/%22/', chr(0x22), $bs);
			$bs = preg_replace('/%25/', chr(0x25), $bs);
			fwrite($fh, $bs);
			fclose($fh);
			
			$this->result_array[188] = "";
		}
			
		return $this->result_array;
	}

	function build_query()
	{
		$this->query = "";
		foreach ($this->query_array as $key => $val)
			$this->query .= "$key,\"".ereg_replace('"', '', $val)."\"";

		$this->query .= '99,""';
	}

	function PostRaw($raw_data)
	{
		global $SiteUrl;
		$custom_header = "POST /GatewayDC HTTP/1.0\n";
		$custom_header .= "Referer: ".$SiteUrl."\n";
		$custom_header .= "Host: ".FEDEX_POST_HOST."\n";
		$custom_header .= "Accept: image/gif, image/jpeg, image/pjpeg, text/plain, text/html, */*\n";
		$custom_header .= "Content-Type: text/plain\n";
		$custom_header .= "Content-length: ".strlen($raw_data)."\n";
		$custom_header .= "\n";
		$custom_header .= $raw_data;
		
		$this->DebugInfo->Write("FedEx Post Query Array: " . $this->query_array);
		$this->DebugInfo->Write("FedEx Post Query: " . $this->query);
		
		@set_time_limit(120);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://".FEDEX_POST_HOST);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "");
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		if (defined("CURLOPT_SSL_VERIFYHOST"))
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $custom_header);
		
		global $CUrlProxy, $CUrlProxyUserName, $CUrlProxyPassword;
		if ($CUrlProxy != "")
		{
			curl_setopt($ch, CURLOPT_PROXY, $CUrlProxy);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $CUrlProxyUserName.":".$CUrlProxyPassword);
		}

		$this->result = curl_exec($ch);

		if ($this->query_array[0] == "007")
		{
			$this->latest_ground_id = $this->DataBase->insert_sql("fe_cod",
			array(
			"cod_time" => "now()",
			"request" => $raw_data,
			"response" => $this->result
			));
		}

		$this->DebugInfo->Write("FedEx Result: " . $this->result);
		$this->err_nr = curl_errno($ch);
		if ($this->err_nr)
		{
			$err_str = curl_error($ch) . "<br />\r\n";
			foreach(curl_getinfo($ch) as $k => $v)
				$err_str .= "$k: $v<br />\r\n";

			$this->DebugInfo->Write("Cannot connect to FedEx Server (".$this->err_nr.")");
			curl_close($ch);
			
			return FALSE;
		}
		curl_close($ch);
		
		return TRUE;
	}
	
	function Post()
	{
		$this->build_query();
		return $this->PostRaw($this->query);
	}

	function ParseReply()
	{
		$n = 0;
		$this->result_array = array();
		preg_match_all('/([0-9\-]+),"([^"]*)"/' ,$this->result ,$this->result_tmp ); 
		foreach ($this->result_tmp[1] as $val)
		{
			$this->result_array[$val] = $this->result_tmp[2][$n];
			$n++;
		}
		$this->DebugInfo->Write("FedEx Clean Result Array: " . $this->result_array);
	}
	
	function check_install()
	{
		return ($this->DataBase->get_table_count("fe_cod") >= 0);
	}
	
	function install()
	{
		$this->DataBase->internalQuery('DROP TABLE %prefix%fe_cod');
		$this->DataBase->internalQuery('
CREATE TABLE %prefix%fe_cod (
id INT UNSIGNED NOT NULL '.$this->DataBase->auto_inc_stmt.',
cod_time '.$this->DataBase->datetime_stmt.' NOT NULL,
request '.$this->DataBase->clob_stmt.',
response '.$this->DataBase->clob_stmt.',
PRIMARY KEY (id))');
	}
	
	function get_admin_names()
	{
		return 'FedEx Shipping';
	}
	
	function run_admin_interface($module, $sub_module)
	{
		$htmlOut = '';
		$htmlOut .= '
<h1 class="section">Account Info</h1>
<p class="text">
<strong>FedEx Server</strong>: '.FEDEX_POST_HOST.'<br />
</p>';
		if (InGetPost("cod", "") == "true")
			$this->close_ground();
		$htmlOut .= '<h2 class="section2">Manifest Info</h2>
<form method="post" action="'.get_url(null, array("action"=>"run_module", "module"=>$module, "sub_module"=>$sub_module, "cod" => "true"), false).'" onSubmit="return (confirm(\'Are you sure to close the day and create new manifest?\'));">
<table border="0" cellpadding="2" cellspacing="0">
<tr>
	<td class="text">
<input type="hidden" name="close" value="N" />
<input type="image" src="'.$GLOBALS['app']->template_vars['IMAGES'].'buttons/create.gif" />
	</td>
</tr>
</table>
</form>
';
		if ($this->latest_ground_id > -1)
		{
			global $ShowPNG;
			$tbl = $this->DataBase->select_sql("fe_cod", array("id"=>$this->latest_ground_id));
			$this->result = $tbl->get_field("response");
			$this->ParseReply();
			if (isset($this->result_array[1372]))
				for ($j=1; $j<=$this->result_array[1372]; $j++)
				{
					$htmlOut .= '<script type="text/javascript" language="JavaScript"><!--
window.open(\''.($ShowPNG . "manifest" . "/" . $this->result_array["1005-".$j].'.txt').'\');
//--></script>';
				}
		}
		$htmlOut .= '<p class="text">Previous 10 FedEx Ground Close of the Day transactions</p>
<table border="0" cellpadding="2" cellspacing="0">
<tr><th class="thead1">#</th><th class="thead1">Date</th><th class="thead1">Result</th></tr>';
		$tbl = $this->DataBase->select_sql("fe_cod", 0, array("id"=>"desc"));
		for ($i=0; ($i<count($tbl->Rows))&&($i<10); $i++)
		{
			$htmlOut .= '<tr>';
			$this->result = $tbl->Rows[$i]->get_field("response");
			$this->ParseReply();
			$out = $tbl->Rows[$i]->get_field("cod_time");
			$dt = substr($out, 0, 8);
			$tm = substr($out, 8, 6);
			$out = substr($out, 4, 2) . "/" . substr($out, 6, 2) . "/" . substr($out, 0, 4) . " " . substr($out, 8, 2) . ":" . substr($out, 10, 2) . ":" . substr($out, 12, 2);
			$htmlOut .=  '<td class="text">'.$tbl->Rows[$i]->get_field("id").'</td>';
			$htmlOut .=  '<td class="text">'.$out.'</td>';
			$htmlOut .=  '<td class="text">';
			if (isset($this->result_array[1372]))
				for ($j=1; $j<=$this->result_array[1372]; $j++)
				{
					global $ShowPNG;
					$htmlOut .=  '<a target="_blank" href="'.$ShowPNG . "manifest" . "/" . $this->result_array["1005-".$j].'.txt">Manifest #'.$this->result_array["1005-".$j].'</a><br />';
				}
			else
				$htmlOut .=  '<span class="error">' . $this->result_array[3] . '</span>';
			$htmlOut .=  '</td>';
			$htmlOut .=  '</tr>';
		}
		$htmlOut .= '</table>';
		
		return $htmlOut;
	}
	
	function special_feature($run)
	{
		if ($run)
		{
			$this->subscribe();
			return dump_array($this->result_array);
		}
		else
			return "FedEx Subscription";
	}
}
?>