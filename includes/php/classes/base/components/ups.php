<?
/**
 * @package LLA.Base
 */
/*
--------------------------------------------------------------------------------
Class CUPS v 1.0

methods:
	string get_last_error(void)
		returns last error message
	float get_rate_price($service, $country, $state, $zip, $residential, $weight)
		$service - code of UPS service, see below
		$country - code of country US, CA
		$zip - state code
		$weight - total weight of package
		returns float if value received or FALSE if error occured
	CLWG_dom_xml ship_confirm(mixed[])
		receives array of variables
		returns xml with result of FALSE if error
	String get_digest(CLWG_dom_xml)
		returns digest from ship_confirm response
	CLWG_dom_xml ship_accept(string $digest)
		receives digest, see get_digest
		store received pictures in $LabelImages path, definition see below
		returns xml with results, or FALSE with error
	CLWG_dom_xml void($package_id, $service)
		voids package
		returns xml or FALSE
	CLWG_dom_xml track_package($package_id)
		track package
		returns xml or FALSE
	get_services()
		returns associative array of ups services where keys are service numbers

uses:
	$CUrlProxy - proxy server
	$CUrlProxyUserName - proxy user
	$CUrlProxyPassword - proxy password

history:
	v 1.0 - created (VK)
--------------------------------------------------------------------------------
*/

/**
 */
define('SHIPPER_NAME', 'test');
define('SHIPPER_PHONE', '123 123 1234');
define('SHIPPER_ADDRESS1', 'test');
define('SHIPPER_CITY', 'Orlando');
define('SHIPPER_STATE', 'FL');
define('SHIPPER_COUNTRY', 'US');
define('SHIPPER_ZIP', '12345');

define('UPS_SERVER_HOST', 'wwwcie.ups.com'); // real: www.ups.com, test: wwwcie.ups.com

define('UPS_AccessLicenseNumber', 'EBB705907D706EC0');
define('UPS_AccountNumber', 'Y8R446');
define('UPS_UserId', 'Tamaraht');
define('UPS_Password', 'wwater77');

define('UPS_TEST_MODE', '1');

global $FilePath;
$GLOBALS['LabelImages'] = $FilePath . 'admin/labels/';
/*if (@is_dir($GLOBALS['LabelImages']))
	@chmod($GLOBALS['LabelImages'], 0777);
else
	if (!@mkdir($GLOBALS['LabelImages'], 0777))
	{
		@chmod($FilePath, 0777);
		@chmod($FilePath . 'admin/', 0777);
		@mkdir($GLOBALS['LabelImages'], 0777);
	}*/

require_once('lwg_xml.php');

// internal function for sorting in track_orders
function cmp(&$a, &$b)
{
	$d1 = $a->get_leap_content('Date');
	$d2 = $b->get_leap_content('Date');

	if ($d1 < $d2) return 1;
	if ($d1> $d2) return -1;

	$t1 = $a->get_leap_content('Time');
	$t2 = $b->get_leap_content('Time');

	return -strcmp($t1, $t2);
}

/**
 * @package LLA.Base
 */
class CUPS
{
	var $track_id;

	var $query;
	var $access_query;

	var $query_xml;
	var $response_xml;

	var $last_error = '';

	var $Application;
	var $DataBase;
	var $settings;

	function CUPS(&$app)
	{
		$this->Application = &$app;
		$this->DataBase = &$this->Application->DataBase;

		$ac_xml = &lwg_domxml_create();
		$ac_xml->m_Root->tagname = 'AccessRequest';
		$ac_xml->m_Root->appendAttribute('xml:lang', 'en-US');
		$ac_xml->m_Root->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'AccessLicenseNumber', UPS_AccessLicenseNumber));
		$ac_xml->m_Root->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'UserId', UPS_UserId));
		$ac_xml->m_Root->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Password', UPS_Password));

		$this->access_query = $ac_xml->getXML();

		/*$r = &$this->Application->get_module('Registry');
		$this->settings = $r->get_values('_ups');*/
	}

	function get_last_error()
	{
		return $this->last_error;
	}

	function get_digest(&$ship_confirm_xml)
	{
		if (UPS_TEST_MODE)
			return '';
		$this->last_error = '';
		return $ship_confirm_xml->get_leap_content('ShipmentDigest');
	}

	function get_rate_price($srv, $country,	$state, $city, $zip, $residential, $t_weight)
	{
		$this->last_error = '';

		$xml = &lwg_domxml_create();
		$xml->m_Root->tagname = 'RatingServiceSelectionRequest';
		$xml->m_Root->appendAttribute('xml:lang', 'en-US');

		$request = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Request', '');
			$tr_ref = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'TransactionReference', '');
			$tr_ref->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'XpciVersion', '1.0001'));
			$request->appendChild($tr_ref);
		$request->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'RequestAction', 'Rate'));
		$request->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'RequestOption', 'rate'));

		$pickuptype = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PickupType', '');
		$pickuptype->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Code', '01'));

		$cust_class = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'CustomerClassification', '');
		$cust_class->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Code', '01'));

		$shipment = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Shipment', '');

		$shipper = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Shipper', '');
		$address = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Address', '');
		$address->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PostalCode', SHIPPER_ZIP));
		$address->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'CountryCode', SHIPPER_COUNTRY));
		$shipper->appendChild($address);

		$ship_to = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'ShipTo', '');
		$address2 = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Address', '');
		$address2->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PostalCode', $zip));
		$address2->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'CountryCode', $country));
		if ($residential> 0)
			$address2->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'ResidentialAddressIndicator', ''));
		$ship_to->appendChild($address2);

		$ship_from = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'ShipFrom', '');
		$address3 = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Address', '');
		$address3->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PostalCode', SHIPPER_ZIP));
		$address3->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'CountryCode', SHIPPER_COUNTRY));
		$ship_from->appendChild($address3);

		$package = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Package', '');
		$package_type = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PackagingType', '');
		$package_type->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Code', '02'));
		$package_weight = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PackageWeight', '');
		$mes_unit = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'UnitOfMeasurement', '');
		$mes_unit->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Code', 'LBS'));
		$package_weight->appendChild($mes_unit);
		$package_weight->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Weight', $t_weight));
		$package->appendChild($package_type);
		$package->appendChild($package_weight);

		$service = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Service', '');
		if ($srv < 10)
			$srv = '0' . $srv;
		$service->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Code', $srv));

		$shipment->appendChild($shipper);
		$shipment->appendChild($ship_to);
		$shipment->appendChild($ship_from);
		$shipment->appendChild($package);
		$shipment->appendChild($service);

		$xml->m_Root->appendChild($request);
		$xml->m_Root->appendChild($pickuptype);
		$xml->m_Root->appendChild($cust_class);
		$xml->m_Root->appendChild($shipment);

		$this->query = $xml->getXML();
		$out_xml = &$this->_post('Rate');

		if (is_object($out_xml))
		{
			if ($out_xml->get_leap_content('Response/ResponseStatusCode') != '1')
			{
				$this->last_error = 'UPS: '.$out_xml->get_leap_content('Response/Error/ErrorDescription');
				return FALSE;
			}
			else
				return $out_xml->get_leap_content('RatedShipment/TotalCharges/MonetaryValue');
		}
		else
		{
			if ($this->last_error == '')
				$this->last_error = 'UPS: Unknown error';
			else
				$this->last_error = 'UPS: ' . $this->last_error;
			return FALSE;
		}
	}

	/*function ship($vars)
	{
		if ( ($c_xml = $this->ship_confirm($vars)) !== false)
		{
			$digest = $c_xml->get_leap_content('ShipmentDigest');
			$ship_id = $c_xml->get_leap_content('ShipmentIdentificationNumber');

			if ( ($a_xml = $this->ship_accept($digest)) !== false)
			{
				$packages = $a_xml->selectNodes('ShipmentResults/PackageResults');

				$this->ship_ids = array();

				foreach ($packages as $i => $v)
					$this->ship_ids[] = $v->get_leap_content('TrackingNumber');

				return $ship_id;
			}
			else
			{
				$this->void($ship_id);
				return false;
			}
		}
		else
			return false;
	}*/

	function ship_confirm($vars)
	{
		if (UPS_TEST_MODE)
			return true;
		$this->last_error = '';

		require_once(FUNCTION_PATH.'functions.normalize.php');
		require_once(FUNCTION_PATH.'functions.format.php');

		// xml creation
		$xml = &lwg_domxml_create();
		$xml->m_Root->tagname = 'ShipmentConfirmRequest';
		$xml->m_Root->appendAttribute('xml:lang', 'en-US');
			$request = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Request', '');
				$tr_ref = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'TransactionReference', '');
				$tr_ref->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'XpciVersion', '1.0001'));
				$request->appendChild($tr_ref);
			$request->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'RequestAction', 'ShipConfirm'));
			$request->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'RequestOption', 'nonvalidate'));
		$xml->m_Root->appendChild($request);

		// Shipment section
		$shipment = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Shipment', '');
			$shipper = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Shipper', '');
			$shipper->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Name', SHIPPER_NAME));
			$shipper->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'AttentionName', $this->settings['Shipper_AttentionName']));
			$shipper->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'ShipperNumber', UPS_AccountNumber));
			$shipper->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PhoneNumber', normalize_phone(SHIPPER_PHONE)));
				$address = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Address', '');
				$address->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'AddressLine1', SHIPPER_ADDRESS1));
				$address->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'City', SHIPPER_CITY));
				$address->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'StateProvinceCode', SHIPPER_STATE));
				$address->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PostalCode', SHIPPER_ZIP));
				$address->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'CountryCode', SHIPPER_COUNTRY));
			$shipper->appendChild($address);
		$shipment->appendChild($shipper);
			$shipfrom = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'ShipFrom', '');
			$shipfrom->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'CompanyName', SHIPPER_NAME));
			$shipfrom->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'AttentionName', $this->settings['Shipper_AttentionName']));
			$shipfrom->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PhoneNumber', normalize_phone(SHIPPER_PHONE)));
				$address2 = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Address', '');
				$address2->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'AddressLine1', SHIPPER_ADDRESS1));
				$address2->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'City', SHIPPER_CITY));
				$address2->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'StateProvinceCode', SHIPPER_STATE));
				$address2->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PostalCode', SHIPPER_ZIP));
				$address2->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'CountryCode', SHIPPER_COUNTRY));
			$shipfrom->appendChild($address2);
		$shipment->appendChild($shipfrom);
			$shipto = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'ShipTo', '');
			if (strlen($vars['ship_company'])> 0)
				$shipto->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'CompanyName', $vars['ship_company']));
			$shipto->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'AttentionName', format_name($vars['ship_first_name'], $vars['ship_middle_name'], $vars['ship_last_name'])));
			$shipto->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PhoneNumber', normalize_phone($vars['ship_phone_number'] . ' ' . $vars['ship_phone_ext'])));
				$address3 = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Address', '');
				$address3->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'AddressLine1', $vars['ship_address']));
				if ($vars['ship_address2'] != '')
					$address3->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'AddressLine2', $vars['ship_address2']));
				$address3->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'City', $vars['ship_city']));
				$address3->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'StateProvinceCode', $vars['ship_state']));
				$address3->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PostalCode', $vars['ship_zip_code']));
				$address3->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'CountryCode', $vars['ship_country']));
				if ($vars['ship_residential']> 0) // residential delivery
					$address3->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'ResidentialAddress', ''));
			$shipto->appendChild($address3);
		$shipment->appendChild($shipto);
			$payment = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PaymentInformation', '');
			if ( (!is_null($vars['ship_amount'])) && ($vars['ship_amount']> 0) )// prepaid, shipper will pay
			{
				$prepaid = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Prepaid', '');
					$billshipper = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'BillShipper', '');
					$billshipper->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'AccountNumber', UPS_AccountNumber));
				$prepaid->appendChild($billshipper);
				$payment->appendChild($prepaid);
			}
			else // receiver will pay
			{
				/*
				$freight = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'FreightCollect', '');
					$billreceiver = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'BillReceiver', '');
					// !!!!
				$freight->appendChild($billreceiver);
				$payment->appendChild($freight);
				*/
			}
		$shipment->appendChild($payment);
			$service = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Service', '');
			$service->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Code', $vars['service']));
		$shipment->appendChild($service);
		if ( ($vars['ship_country'] == 'PR') || ($vars['ship_country'] == 'CA') )
		{
			$invoice_total = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'InvoiceLineTotal', '');
			$invoice_total->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'CurrencyCode', 'USD'));
			$invoice_total->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'MonetaryValue', round($vars['total_amount'])));
			$shipment->appendChild($invoice_total);
		}
			$package = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Package', '');
				$package_type = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PackagingType', '');
				$package_type->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Code', '00'));
			$package->appendChild($package_type);
				$package_weight = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'PackageWeight', '');
					$uom =  new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'UnitOfMeasurement', '');
					$uom->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Code', 'LBS'));
				$package_weight->appendChild($uom);
				$package_weight->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Weight', $vars['ship_weight']));
			$package->appendChild($package_weight);
				$dimensions = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Dimensions', '');
					$uom =  new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'UnitOfMeasurement', '');
					$uom->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Code', '01'));
				$dimensions->appendChild($uom);
				$dimensions->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Length', $vars['ship_dim_length']));
				$dimensions->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Width', $vars['ship_dim_width']));
				$dimensions->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Height', $vars['ship_dim_height']));
			$package->appendChild($dimensions);

			/*if ($vars['shipping_serv'] == 7) // saturday
			{
				$serv_opt =  new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'ShipmentServiceOptions', '');
				$serv_opt->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'SaturdayDelivery', ''));
				$shipment->appendChild($serv_opt);
			}*/

		$shipment->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Description', $this->settings['ShipDescription']));
		$shipment->appendChild($package);
		$xml->m_Root->appendChild($shipment);

		$label = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'LabelSpecification', '');
			$label_print_method = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'LabelPrintMethod', '');
			$label_print_method->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Code', $this->settings['LabelPrintMethod_Code']));
		$label->appendChild($label_print_method);
		if ($this->settings['LabelPrintMethod_Code'] == 'EPL')
		{
			$label_size = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'LabelStockSize', '');
			$label_size->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Height', '4'));
			$label_size->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Width', $this->settings['LabelStockSize_Width']));
			$label->appendChild($label_size);
		}

			$label_image_format = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'LabelImageFormat', '');
			$label_image_format->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Code', 'GIF'));
		$label->appendChild($label_image_format);

		$xml->m_Root->appendChild($label);
		// end of xml creation

		$this->query = $xml->getXML();
		$out_xml = $this->_post('ShipConfirm');

		if (is_object($out_xml))
		{
			if ($out_xml->get_leap_content('Response/ResponseStatusCode') != '1')
			{
				$this->track_id = '';
				$this->last_error = 'UPS: '.$out_xml->get_leap_content('Response/Error/ErrorDescription');
				return FALSE;
			}
			else
			{
				$this->track_id = $out_xml->get_leap_content('ShipmentIdentificationNumber');
				return $out_xml;
			}
		}
		else
		{
			if ($this->last_error == '')
				$this->last_error = 'UPS: Unknown error';
			else
				$this->last_error = 'UPS: ' . $this->last_error;
			return FALSE;
		}
	}

	function ship_accept($ShipmentDigest)
	{
		if (UPS_TEST_MODE)
		{
			$this->track_id = '1ZY8R4460190011814';
			return true;
		}
		$this->last_error = '';

		// xml creation
		$xml = &lwg_domxml_create();
		$xml->m_Root->tagname = 'ShipmentAcceptRequest';
		$xml->m_Root->appendAttribute('xml:lang', 'en-US');
			$request = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Request', '');
				$tr_ref = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'TransactionReference', '');
				$tr_ref->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'XpciVersion', '1.0001'));
				$request->appendChild($tr_ref);
			$request->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'RequestAction', 'ShipAccept'));
			$request->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'RequestOption', '01'));
		$xml->m_Root->appendChild($request);

		$xml->m_Root->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'ShipmentDigest', $ShipmentDigest));
		// end of xml creation

		$this->query = $xml->getXML();
		$out_xml = $this->_post('ShipAccept');

		if (is_object($out_xml))
		{
			if ($out_xml->get_leap_content('Response/ResponseStatusCode') != '1')
			{
				$this->last_error = 'UPS: '.$out_xml->get_leap_content('Response/Error/ErrorDescription');
				return FALSE;
			}
			else
			{
				$z_id = $out_xml->get_leap_content('ShipmentResults/ShipmentIdentificationNumber');

				$pckgs = $out_xml->selectNodes('ShipmentResults/PackageResults');
				foreach ($pckgs as $i => $v)
				{
					$l_fn = $pckgs[$i]->get_leap_content('TrackingNumber');

					$ext = strtolower($pckgs[$i]->get_leap_content('LabelImage/LabelImageFormat/Code'));
					$img_file = $pckgs[$i]->get_leap_content('LabelImage/GraphicImage');
					if ($img_file)
					{
						$fh = @fopen($GLOBALS['LabelImages'] . 'label' . $l_fn. '.' . $ext, 'wb');
						if ($fh)
						{
							fwrite($fh, base64_decode($img_file));
							fclose($fh);
						}
					}

					$img_file = $pckgs[$i]->get_leap_content('LabelImage/InternationalSignatureGraphicImage');
					if ($img_file)
					{
						$fh = @fopen($GLOBALS['LabelImages'] . $l_fn . '.' . $ext, 'wb');
						if ($fh)
						{
							fwrite($fh, base64_decode($img_file));
							fclose($fh);
						}
					}

					$img_file = $pckgs[$i]->get_leap_content('LabelImage/HTMLImage');
					if ($img_file)
					{
						$fh = @fopen($GLOBALS['LabelImages'] . $l_fn . '.htm', 'wb');
						if ($fh)
						{
							fwrite($fh, base64_decode($img_file));
							fclose($fh);
						}
					}
				}
				return $out_xml;
			}
		}
		else
		{
			if ($this->last_error == '')
				$this->last_error = 'UPS: Unknown error';
			else
				$this->last_error = 'UPS: ' . $this->last_error;
			return FALSE;
		}
	}

	function void($ship_id, $service = '')
	{
		$this->last_error = '';

		// xml creation
		$xml = &lwg_domxml_create();
		$xml->m_Root->tagname = 'VoidShipmentRequest';
		$xml->m_Root->appendAttribute('xml:lang', 'en-US');
			$request = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Request', '');
				$tr_ref = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'TransactionReference', '');
				$tr_ref->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'XpciVersion', '1.0001'));
				$request->appendChild($tr_ref);
			$request->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'RequestAction', '1'));
			$request->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'RequestOption', ''));
		$xml->m_Root->appendChild($request);

		$xml->m_Root->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'ShipmentIdentificationNumber', $ship_id));
		// end of xml creation

		$this->query = $xml->getXML();
		$out_xml = $this->_post('Void');

		if (is_object($out_xml))
		{
			if ($out_xml->get_leap_content('Response/ResponseStatusCode') != '1')
			{
				$this->last_error = 'UPS: '.$out_xml->get_leap_content('Response/Error/ErrorDescription');
				return FALSE;
			}
			else
				return $out_xml;
		}
		else
		{
			if ($this->last_error == '')
				$this->last_error = 'UPS: Unknown error';
			else
				$this->last_error = 'UPS: ' . $this->last_error;
			return FALSE;
		}
	}

	function &track_package($track_id)
	{
		$this->last_error = '';

		$xml = &lwg_domxml_create();
		$xml->m_Root->tagname = 'TrackRequest';
		$xml->m_Root->appendAttribute('xml:lang', 'en-US');

		$request = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'Request', '');
		$tr_ref = new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'TransactionReference', '');
		$tr_ref->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'XpciVersion', '1.0001'));
		$request->appendChild($tr_ref);
		$request->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'RequestAction', 'Track'));
		$request->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'RequestOption', 'activity'));

		$xml->m_Root->appendChild($request);
		$xml->m_Root->appendChild(new CLWG_dom_node(LWG_XML_NODE_ELEMENT, 'TrackingNumber', $track_id));

		$this->query = $xml->getXML();
		$out_xml = &$this->_post('Track');

		if (is_object($out_xml))
		{
			if ($out_xml->get_leap_content('Response/ResponseStatusCode') != '1')
			{
				$this->last_error = 'UPS: '.$out_xml->get_leap_content('Response/Error/ErrorDescription');
				return FALSE;
			}
			else
				return $out_xml;
		}
		else
		{
			if ($this->last_error == '')
				$this->last_error = 'UPS: Unknown error';
			else
				$this->last_error = 'UPS: ' . $this->last_error;
			return FALSE;
		}
	}

	function &_post($tool)
	{
		$postHost = UPS_SERVER_HOST;

		$custom_header = 'POST /ups.app/xml/' . $tool . ' HTTP/1.0'."\n";
		$custom_header .= 'Host: '.$postHost."\n";
		$custom_header .= "Accept: */*\n";
		$custom_header .= "Content-Type: application/xml\n";
		$custom_header .= 'Content-length: '.(strlen($this->access_query)+strlen($this->query))."\n";
		$custom_header .= "\n";
		$custom_header .= $this->access_query;
		$custom_header .= $this->query;

		$GLOBALS['GlobalDebugInfo']->Write('UPS query: <pre>' . htmlspecialchars($this->query) . '</pre>');

		if (!@function_exists('curl_init'))
		{
			$this->last_error = 'CURL: CUrl is not installed';
			return false;
		}

		@set_time_limit(30);
		$this->ch = @curl_init();
		curl_setopt($this->ch, CURLOPT_URL, 'https://'.$postHost);
		curl_setopt($this->ch, CURLOPT_HEADER, 0);
		curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, '');
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_VERBOSE, 0);
		if (defined('CURLOPT_SSL_VERIFYHOST'))
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $custom_header);

		global $CUrlProxy, $CUrlProxyUserName, $CUrlProxyPassword;
		if ($CUrlProxy != '')
		{
			curl_setopt($this->ch, CURLOPT_PROXY, $CUrlProxy);
			curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $CUrlProxyUserName.':'.$CUrlProxyPassword);
		}

		//echo '<xml>' . $this->query . '</xml>';

		$result = @curl_exec($this->ch);
		$this->err_nr = @curl_errno($this->ch);
		if ( ($this->err_nr) && (!$result) )
		{
			$this->last_error = 'CURL: '.@curl_error($this->ch);
			$result = false;
			return $result;
		}
		curl_close($this->ch);

		$this->DataBase->insert_sql('ups_history', array('e_date'=>'now()', 'request'=>$this->query, 'response'=>$result));

		$this->response_xml = lwg_domxml_open_mem($result);
		if (!is_object($this->response_xml))
			$this->last_error = $GLOBALS['lwg_xml_last_error'];

		$GLOBALS['GlobalDebugInfo']->Write('UPS result: <pre>' . htmlspecialchars($result) . '</pre>');
		return $this->response_xml;
	}

	function draw_date($date_str)
	{
		return substr($date_str, 4, 2) . '/' . substr($date_str, 6, 2) . '/' . substr($date_str, 0, 4);
	}

	function draw_time($time_str)
	{
		return substr($time_str, 0, 2) . ':' . substr($time_str, 2, 2) . ':' . substr($time_str, 4, 2);
	}

	function get_services()
	{
		$ser_list = array(
'03' => 'UPS Standard',
'12' => 'UPS 3 Day Select',
'01' => 'UPS Next Day Air',
//'02' => 'UPS 2nd Day Air',
//'03' => 'UPS Ground',
//'07' => 'UPS Worldwide Express',
//'08' => 'UPS Worldwide Expedited',
//'13' => 'UPS Next Day Air Saver',
//'14' => 'UPS Next Day Air Early A.M.',
//'54' => 'UPS Worldwide Express Plus',
//'59' => 'UPS 2nd Day Air A.M.',
//'65' => 'UPS Express Saver'
);
		return $ser_list;
	}

	function get_ups_services_list($selected, $get_options = true, $draw_non_service = false)
	{
		$ser_list = $this->get_services();
		$res = '';
		if ($get_options)
		{
			if ($draw_non_service)
				$res .= '<option value="-1"'.(('-1'==$selected)?(' selected="selected"'):('')).'>Select shipping type...</option>';
			foreach ($ser_list as $k => $v)
				$res .= '<option value="' . $k . '"'.(($k==$selected)?(' selected="selected"'):('')).'>' . $v . '</option>';
		}
		else
		{
			if ($draw_non_service)
				$res .= '<input type="radio" name="shipping_serv" value="-1"'.(('-1'==$selected)?(' checked="checked"'):('')).'>Choice shipping type...<br>';
			foreach ($ser_list as $k => $v)
				$res .= '<input onclick="this.form.elements.update_only.value=\'true\'; this.form.submit();" type="radio" name="shipping_serv" value="' . $k . '"'.(($k==$selected)?(' checked="checked"'):('')).'>' . $v . '<br>';
		}

		return $res;
	}

	function draw_track_table($track_num)
	{
		$out = '';
		$o = $this->track_package($track_num);
		if ($o !== false)
		{
			$out = '<p class="SectionTitle">Tracking Information</p><br>';
			$shps = &$o->selectNodes('Shipment');
			foreach ($shps as $s => $v)
			{
				$out .= '<table border="0" cellpadding="0" cellspacing="0">';
				$out .= '<tr><td align="left" valign="top" class="text">Shipper Number:&nbsp;</td><td align="left" valign="top" class="text" width="100%">' . $v->get_leap_content("Shipper/ShipperNumber") . "</td></tr>";
				$out .= '<tr><td align="left" valign="top" class="text">Shipment ID:&nbsp;</td><td align="left" valign="top" class="text">' . $v->get_leap_content("ShipmentIdentificationNumber") . "</td></tr>";
				$out .= '<tr><td align="left" valign="top" class="text">Ship To:&nbsp;</td><td align="left" valign="top" class="text">';
					$out .= 'Address: '. $v->get_leap_content('ShipTo/Address/AddressLine1') . '<br>';
					if ($v->get_leap_content('Shipment/ShipTo/Address/AddressLine2') != '')
						$out .= $v->get_leap_content('Shipment/ShipTo/Address/AddressLine2') . '<br>';
					$out .= 'City: '. $v->get_leap_content('ShipTo/Address/City') . '<br>';
					$out .= 'Zip: '. $v->get_leap_content('ShipTo/Address/PostalCode') . '<br>';
					$out .= 'Country: '. $v->get_leap_content('ShipTo/Address/CountryCode');
				$out .= '</td></tr>';
				$out .= '<tr><td align="left" valign="top" class="text">Service:&nbsp;</td><td align="left" valign="top" class="text">';
					$out .= 'Code: '. $v->get_leap_content('Service/Code') . '<br>';
					$out .= 'Description: '. $v->get_leap_content('Service/Description');
				$out .= '</td></tr>';

				$pcg = &$v->selectNodes('Package');
				foreach($pcg as $k => $v2)
				{
					$out .= '<tr><td align="left" valign="top" class="text">Weight Unit Code:&nbsp;</td><td align="left" valign="top" class="text">' . $v2->get_leap_content("PackageWeight/UnitOfMeasurement/Code") . "</td></tr>";
					$out .= '<tr><td align="left" valign="top" class="text" nowrap="nowrap">Weight Unit Description:&nbsp;</td><td align="left" valign="top" class="text">' . $v2->get_leap_content("PackageWeight/UnitOfMeasurement/Description") . "</td></tr>";
					$out .= '<tr><td align="left" valign="top" class="text">Weight:&nbsp;</td><td align="left" valign="top" class="text">' . $v2->get_leap_content("PackageWeight/Weight") . "</td></tr>";

					$out .= '<tr><td align="left" valign="top" class="text">RefNum Code:&nbsp;</td><td align="left" valign="top" class="text">' . $v2->get_leap_content("ReferenceNumber/Code") . "</td></tr>";
					$out .= '<tr><td align="left" valign="top" class="text">RefNum Value:&nbsp;</td><td align="left" valign="top" class="text">' . $v2->get_leap_content("ReferenceNumber/Value") . "</td></tr>";

					$nodes = &$v2->selectNodes('Message');
					foreach($nodes as $i => $v3)
					{
						$out .= '<tr><td align="left" valign="top" class="text">Message Code:&nbsp;</td><td align="left" valign="top" class="text">' . $v3->get_leap_content("Code") . '</td></tr>';
						$out .= '<tr><td align="left" valign="top" class="text">Message Description:&nbsp;</td><td align="left" valign="top" class="text">' . $v4->get_leap_content("Description") . '</td></tr>';
					}

					$nodes = &$v2->selectNodes('Activity');

					$out .= '<tr><td colspan="2"><br>
					<table border="0" cellpadding="2" cellspacing="2">
						<tr align="center">
							<th colspan="4" class="MyAccountHeader">Addres</th><th rowspan=2 class="MyAccountHeader">Code</th><th rowspan=2 class="MyAccountHeader">Description</th><th rowspan=2 class="MyAccountHeader">Type</th><th rowspan=2 class="MyAccountHeader">Description</th><th rowspan=2 class="MyAccountHeader">Code</th><th rowspan=2 class="MyAccountHeader">Date</th><th rowspan=2 class="MyAccountHeader">Time</th>
						</tr>
						<tr align="center">
							<th class="MyAccountHeader">City</th><th class="MyAccountHeader">State</th><th class="MyAccountHeader">ZIP</th><th class="MyAccountHeader">Country</th>
						</tr>';
					usort($nodes, 'cmp');
					$r_clr = 'white';
					foreach ($nodes as $i => $v4)
					{
						$out .= '<tr style="background-color: '.$r_clr.';">';
						$r_clr = ($r_clr=='white')?('#d0d0d0'):('white');

						$out .= '<td class="text">' . $v4->get_leap_content('ActivityLocation/Address/City') . '</td>';
						$out .= '<td class="text">' . $v4->get_leap_content('ActivityLocation/Address/StateProvinceCode') . '</td>';
						$out .= '<td class="text">' . $v4->get_leap_content('ActivityLocation/Address/PostalCode') . '</td>';
						$out .= '<td class="text">' . $v4->get_leap_content('ActivityLocation/Address/CountryCode') . '</td>';

						$out .= '<td class="text">' . $v4->get_leap_content('ActivityLocation/Code') . '</td>';
						$out .= '<td class="text">' . $v4->get_leap_content('ActivityLocation/Description') . '</td>';

						$out .= '<td class="text">' . $v4->get_leap_content('Status/StatusType/Code') . '</td>';
						$out .= '<td class="text">' . $v4->get_leap_content('Status/StatusType/Description') . '</td>';
						$out .= '<td class="text">' . $v4->get_leap_content('Status/StatusCode/Code') . '</td>';

						$out .= '<td class="text">' . CUPS::draw_date($v4->get_leap_content('Date')) . '</td>';
						$out .= '<td class="text">' . CUPS::draw_time($v4->get_leap_content('Time')) . '</td>';

						$out .= '</tr>';
					}
					$out .= '</table><br><br></td></tr>';
				}
				$out .= '</table>';
			}
			$out .= '';
		}
		else
			$out .= '<span class="error">' . $this->get_last_error() . '</span><br>';

		return $out;
	}

	function check_install()
	{
		return ($this->DataBase->get_table_count('ups_history')>= 0);
	}

	function install()
	{
		$this->DataBase->internalQuery('DROP TABLE %prefix%ups_history');
		$this->DataBase->internalQuery('CREATE TABLE %prefix%ups_history (
			id			int not null '.$this->DataBase->auto_inc_stmt.',
			e_date		'.$this->DataBase->datetime_stmt.',
			request		'.$this->DataBase->clob_stmt.',
			response	'.$this->DataBase->clob_stmt.',

			primary key (id),
			unique index (id, e_date)
		)');
	}
}
?>