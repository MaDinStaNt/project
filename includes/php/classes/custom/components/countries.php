<?
class CCountries
{
	var $Application;
	var $DataBase;
	var $tv;
	var $last_error;

	function CCountries(&$app)
	{
		$this->Application = &$app;
		$this->tv = &$app->template_vars;
		$this->DataBase = &$this->Application->DataBase;
	}

	function get_last_error()
	{
		return $this->last_error;
	}

	function get()
	{
		$rs = $this->DataBase->select_sql('country');

		if ( $rs === false ) {
			$this->last_error = $this->Application->Localizer->get_string('database_error');
			return new CRecordSet();
		}

		$this->last_error = '';
		return $rs;
	}

	function get_country_code($country_id) {
		if (intval($country_id) > 0) {
			if ($country_rs = $this->get_country_by_id($country_id)) {
				return '+'.$country_rs->get_field('phone_code');
			}
		}
	}

	function get_country_code_by_abbreviation($abbreviation) {
		$country_rs = $this->DataBase->select_sql('country', array('abbreviation' => $abbreviation));
		if (($country_rs !== false)&&(!$country_rs->eof())) {
			return '+'.$country_rs->get_field('phone_code');
		}
		else {
			return '+1';
		}
	}

	function get_country_by_id($id)
	{
		$id = intval($id);
		if ( $id < 1 ) {
			$this->last_error = $this->Application->Localizer->get_string('invalid_input_data');
			return false;
		}

		$rs = $this->DataBase->select_sql('country', array('id' => $id));

		if ( $rs === false ) {
			$this->last_error = $this->Application->Localizer->get_string('database_error');
			return false;
		}

		$this->last_error = '';
		return $rs;
	}

	function add_country($arr)
	{
		$r = $this->DataBase->select_custom_sql('
            select count(*) as cnt
            from %prefix%country
            where
            title = \''.mysql_escape_string($arr['title']).'\'');
		if ( ($r===false) || ($r->get_field('cnt') > 0) )
		{
			$this->last_error = $this->Application->Localizer->get_string('title_exists');
			return false;
		}

		$insert_array = array(
		'title' => $arr['title'],
		'abbreviation' => $arr['abbreviation'],
		);

		return $this->DataBase->insert_sql('country', $insert_array);
	}

	function update_country($id, $arr)
	{
		$r = $this->DataBase->select_custom_sql('
            select count(*) cnt
            from %prefix%country
            where
            (
            './*login = \''.mysql_escape_string($arr['login']).'\' or*/'
            title = \''.mysql_escape_string($arr['title']).'\'
            ) and
            id <> '.$id.'');
		if ($r->get_field('cnt') > 0)
		{
			$this->last_error = $this->Application->Localizer->get_string('title_exists');
			return false;
		}

		$update_array = array(
		'title' => $arr['title'],
		'abbreviation' => $arr['abbreviation'],
		);

		if ($this->DataBase->update_sql('country', $update_array, array('id'=>$id)))
		return true;
		else
		{
			$this->last_error = $this->Application->Localizer->get_string('internal_error');
			return false;
		}
		return true;
	}

	function delete_country($id)
	{
		$this->DataBase->delete_sql('country', array('id'=>$id));
		return true;
	}

    function check_install() {
            return $this->DataBase->is_table('country');
    }

    function install() {
    	$this->DataBase->custom_sql("DROP TABLE IF EXISTS `country`");
    	
		$this->DataBase->custom_sql("
			CREATE TABLE `country` (
				`id` INTEGER(11) NOT NULL AUTO_INCREMENT,
				`title` VARCHAR(255) COLLATE utf8_general_ci NOT NULL DEFAULT '',
				`abbreviation` CHAR(2) COLLATE utf8_general_ci NOT NULL DEFAULT '',
				`phone_code` VARCHAR(32) COLLATE utf8_general_ci DEFAULT NULL,
				`tmp` INTEGER(11) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`)
			)ENGINE=InnoDB
			CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
		");
$vlv = <<<EOD
INSERT INTO `country` (`id`, `title`, `abbreviation`, `phone_code`, `tmp`) VALUES
  (1,'United States','US','1',0),
  (2,'Afghanistan','AF','93',0),
  (3,'Albania','AL','355',0),
  (4,'Algeria','DZ','213',0),
  (5,'American Samoa','AS','1684',0),
  (6,'Andorra','AD','376',0),
  (7,'Angola','AO','244',0),
  (8,'Anguilla','AI','1264',0),
  (9,'Antigua & Barbuda','AG','1268',0),
  (10,'Argentina','AR','54',0),
  (11,'Armenia','AM','374',0),
  (12,'Aruba','AW','297',0),
  (13,'Australia','AU','61',0),
  (14,'Austria','AT','43',0),
  (15,'Azerbaijan','AZ','994',0),
  (16,'Azores','AP','351',0),
  (17,'Bahamas','BS','1242',0),
  (18,'Bahrain','BH','973',0),
  (19,'Bangladesh','BD','880',0),
  (20,'Barbados','BB','1246',0),
  (21,'Belarus','BY','375',0),
  (22,'Belgium','BE','32',0),
  (23,'Belize','BZ','501',0),
  (24,'Benin','BJ','229',0),
  (25,'Bermuda','BM','1441',0),
  (26,'Bhutan','BT','975',0),
  (27,'Bolivia','BO','591',0),
  (28,'Bonaire','BL','599',0),
  (29,'Bosnia & Herzegovina','BA','387',0),
  (30,'Botswana','BW','267',0),
  (31,'Brazil','BR','55',0),
  (32,'Brunei','BN','673',0),
  (33,'Bulgaria','BG','359',0),
  (34,'Burkina Faso','BF','226',0),
  (35,'Burundi','BI','257',0),
  (36,'Cambodia','KH','855',0),
  (37,'Cameroon','CM','237',0),
  (38,'Canada','CA','1',0),
  (39,'Canary Islands','IC',NULL,0),
  (40,'Cape Verde','CV','238',0),
  (41,'Cayman Islands','KY','1345',0),
  (42,'Central African Republic','CF','236',0),
  (43,'Chad','TD','235',0),
  (44,'Chile','CL','56',0),
  (45,'China','CN','',0),
  (46,'Colombia','CO','57',0),
  (47,'Republic of the Congo','CG','242',0),
  (48,'Democratic Republic of the Congo','CD','243',0),
  (49,'Cook Islands','CK','682',0),
  (50,'Costa Rica','CR','506',0),
  (51,'Croatia','HR','385',0),
  (52,'Curacao','CB',NULL,0),
  (53,'Cyprus','CY','357',0),
  (54,'Czech Republic','CZ','420',0),
  (55,'Denmark','DK','45',0),
  (56,'Djibouti','DJ','253',0),
  (57,'Dominica','DM','1767',0),
  (58,'Dominican Republic','DO','1809',0),
  (59,'East Timor (Timor-Leste)','TL','670',0),
  (60,'Ecuador','EC','593',0),
  (61,'Egypt','EG','20',0),
  (62,'El Salvador','SV','503',0),
  (63,'England','EN','44',0),
  (64,'Equatorial Guinea','GQ','240',0),
  (65,'Eritrea','ER','291',0),
  (66,'Estonia','EE','372',0),
  (67,'Ethiopia','ET','251',0),
  (68,'Faroe Islands','FO','298',0),
  (69,'Fiji','FJ','679',0),
  (70,'Finland','FI','358',0),
  (71,'France','FR','33',0),
  (72,'French Guiana','GF','689',0),
  (73,'French Polynesia','PF','689',0),
  (74,'Gabon','GA','241',0),
  (75,'Gambia','GM','220',0),
  (76,'Georgia','GE','995',0),
  (77,'Germany','DE','49',0),
  (78,'Ghana','GH','233',0),
  (79,'Gibraltar','GI','350',0),
  (80,'Greece','GR','30',0),
  (81,'Greenland','GL','299',0),
  (82,'Grenada','GD','1473',0),
  (83,'Guadeloupe','GP',NULL,0),
  (84,'Guam','GU','1671',0),
  (85,'Guatemala','GT','502',0),
  (86,'Guernsey','GG',NULL,0),
  (87,'Guinea','GN','224',0),
  (88,'Guinea-Bissau','GW','245',0),
  (89,'Guyana','GY','592',0),
  (90,'Haiti','HT','509',0),
  (91,'Holland','HO','31',0),
  (92,'Honduras','HN','504',0),
  (93,'Hong Kong','HK','852',0),
  (94,'Hungary','HU','36',0),
  (95,'Iceland','IS','354',0),
  (96,'India','IN','91',0),
  (97,'Indonesia','ID','62',0),
  (98,'Iraq','IQ','964',0),
  (99,'Ireland','IE',NULL,0),
  (100,'Israel','IL','972',0),
  (101,'Italy','IT','39',0),
  (102,'Ivory Coast','CI','225',0),
  (103,'Jamaica','JM','1876',0),
  (104,'Japan','JP','81',0),
  (105,'Jersey','JE','',0),
  (106,'Jordan','JO','962',0),
  (107,'Kazakhstan','KZ','7',0),
  (108,'Kenya','KE','254',0),
  (109,'Kiribati','KI','686',0),
  (110,'Kosrae','KO',NULL,0),
  (111,'Kuwait','KW','965',0),
  (112,'Kyrgyzstan','KG','996',0),
  (113,'Laos','LA','856',0),
  (114,'Latvia','LV','371',0),
  (115,'Lebanon','LB','961',0),
  (116,'Lesotho','LS','266',0),
  (117,'Liberia','LR','231',0),
  (118,'Liechtenstein','LI','423',0),
  (119,'Lithuania','LT','370',0),
  (120,'Luxembourg','LU','352',0),
  (121,'Macau','MO','853',0),
  (122,'Macedonia','MK','389',0),
  (123,'Madagascar','MG','261',0),
  (124,'Madeira','ME',NULL,0),
  (125,'Malawi','MW','265',0),
  (126,'Malaysia','MY','60',0),
  (127,'Maldives','MV','960',0),
  (128,'Mali','ML','223',0),
  (129,'Malta','MT','356',0),
  (130,'Marshall Islands','MH','692',0),
  (131,'Martinique','MQ',NULL,0),
  (132,'Mauritania','MR','222',0),
  (133,'Mauritius','MU','230',0),
  (134,'Mexico','MX','52',0),
  (135,'Micronesia','FM','691',0),
  (136,'Moldova','MD','373',0),
  (137,'Monaco','MC','377',0),
  (138,'Mongolia','MN','976',0),
  (139,'Montserrat','MS','1664',0),
  (140,'Morocco','MA','212',0),
  (141,'Mozambique','MZ','258',0),
  (142,'Namibia','NA','264',0),
  (143,'Nepal','NP','977',0),
  (144,'Netherlands','NL','31',0),
  (145,'Netherlands Antilles','AN','599',0),
  (146,'New Caledonia','NC','687',0),
  (147,'New Zealand','NZ','64',0),
  (148,'Nicaragua','NI','505',0),
  (149,'Niger','NE','227',0),
  (150,'Nigeria','NG','234',0),
  (151,'Norfolk Island','NF','672',0),
  (152,'Northern Ireland','NB',NULL,0),
  (153,'Northern Mariana Islands','MP','1670',0),
  (154,'Norway','NO','47',0),
  (155,'Oman','OM','968',0),
  (156,'Pakistan','PK','92',0),
  (157,'Palau','PW','680',0),
  (158,'Panama','PA','507',0),
  (159,'Papua New Guinea','PG','675',0),
  (160,'Paraguay','PY','595',0),
  (161,'Peru','PE','51',0),
  (162,'Philippines','PH','63',0),
  (163,'Poland','PL','48',0),
  (164,'Ponape','PO',NULL,0),
  (165,'Portugal','PT','351',0),
  (166,'Puerto Rico','PR','1',0),
  (167,'Qatar','QA','974',0),
  (168,'Reunion','RE',NULL,0),
  (169,'Romania','RO','40',0),
  (170,'Rota','RT',NULL,0),
  (171,'Russia','RU','7',0),
  (172,'Rwanda','RW','250',0),
  (173,'Saba','SS',NULL,0),
  (174,'Saipan','SP',NULL,0),
  (175,'San Marino','SM','378',0),
  (176,'Saudi Arabia','SA','966',0),
  (177,'Scotland','SF',NULL,0),
  (178,'Senegal','SN','221',0),
  (179,'Serbia & Montenegro','CS','381',0),
  (180,'Seychelles','SC','248',0),
  (181,'Sierra Leone','SL','232',0),
  (182,'Singapore','SG','65',0),
  (183,'Slovakia','SK','421',0),
  (184,'Slovenia','SI','386',0),
  (185,'Solomon Islands','SB','677',0),
  (186,'South Africa','ZA','27',0),
  (187,'South Korea','KR','82',0),
  (188,'Spain','ES','34',0),
  (189,'Sri Lanka','LK','94',0),
  (190,'St. Barthelemy','NT',NULL,0),
  (191,'St. Christopher','SW',NULL,0),
  (192,'St. Croix','SX',NULL,0),
  (193,'St. Eustatius','EU',NULL,0),
  (194,'St. John','UV',NULL,0),
  (195,'St. Kitts & Nevis','KN',NULL,0),
  (196,'St. Lucia','LC',NULL,0),
  (197,'St. Maarten','MB',NULL,0),
  (198,'St. Martin','TB',NULL,0),
  (199,'St. Thomas','VL',NULL,0),
  (200,'St. Vincent & the Grenadines','VC',NULL,0),
  (201,'Suriname','SR','597',0),
  (202,'Swaziland','SZ','268',0),
  (203,'Sweden','SE','46',0),
  (204,'Switzerland','CH','41',0),
  (205,'Syria','SY','963',0),
  (206,'Tahiti','TA',NULL,0),
  (207,'Taiwan','TW','886',0),
  (208,'Tajikistan','TJ','992',0),
  (209,'Tanzania','TZ','255',0),
  (210,'Thailand','TH','66',0),
  (211,'Tinian','TI',NULL,0),
  (212,'Togo','TG','228',0),
  (213,'Tonga','TO','676',0),
  (214,'Tortola','ZZ',NULL,0),
  (215,'Trinidad & Tobago','TT',NULL,0),
  (216,'Truk','TU',NULL,0),
  (217,'Tunisia','TN','216',0),
  (218,'Turkey','TR','90',0),
  (219,'Turkmenistan','TM','993',0),
  (220,'Turks & Caicos Islands','TC','1649',0),
  (221,'Tuvalu','TV','688',0),
  (222,'Uganda','UG','256',0),
  (223,'Ukraine','UA','380',0),
  (224,'Union Island','UI',NULL,0),
  (225,'United Arab Emirates','AE','971',0),
  (226,'United Kingdom','GB','44',0),
  (227,'Uruguay','UY','598',0),
  (228,'Uzbekistan','UZ','998',0),
  (229,'Vanuatu','VU','678',0),
  (230,'Vatican City','VA','39',0),
  (231,'Venezuela','VE','58',0),
  (232,'Vietnam','VN','84',0),
  (233,'Virgin Gorda','VR',NULL,0),
  (234,'Virgin Islands, British','VG',NULL,0),
  (235,'Virgin Islands, US','VI','1340',0),
  (236,'Wales','WL',NULL,0),
  (237,'Wallis & Futuna Islands','WF',NULL,0),
  (238,'Western Samoa','WS',NULL,0),
  (239,'Yap','YA',NULL,0),
  (240,'Yemen','YE','967',0),
  (241,'Zambia','ZM','260',0),
  (242,'Zimbabwe','ZW','263',0),
  (250,'Korea','KR',NULL,1),
  (251,'Sudan','SD',NULL,1),
  (252,'Libya','LY',NULL,1),
  (253,'Iran','IR',NULL,1),
  (254,'China (Hong Kong S.A.R.)','HK',NULL,1),
  (255,'China (Macau S.A.R.)','MO',NULL,1),
  (258,'Palestinian National Authority','PS',NULL,1);
EOD;
		$this->DataBase->custom_sql($vlv);
    }
};
?>