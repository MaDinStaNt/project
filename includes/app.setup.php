<?
if ($module_name == 'Registry') {
    $reg = $this->get_module('Registry');
 
	/*$id_path_site_settings = $reg->add_path(null, '_site_settings', 'Site settings');

	$id_page_site_update = $reg->add_path($id_path_site_settings, '_site_update', 'Site update');
	$reg->add_value($id_page_site_update, '_stop_site_update', 'Site update', KEY_TYPE_CHECKBOX, '0');*/
	
	$id_path_static = $reg->add_path(null, '_static', 'Статические страницы');

	$id_page_path = $reg->add_path($id_path_static, '_core', 'Основное (header & footer)');
$vlv = <<<EOD
<p>laserliner.com.ua  &copy; 2011 Все права защищены.<br>Лазерная рулетка, лазерный нивелир, нивелир, пирометры, лазерный уровень, лазерный дальномер.</p>
EOD;
	$reg->add_value($id_page_path, '_foot_slogan', 'Текст футера', KEY_TYPE_HTML, $vlv);
	
	$id_value = $reg->add_value($id_page_path, '_pat_link', 'Press and Test link', KEY_TYPE_TEXT, 'http://www.umarex-laserliner.de/video/index_de.html');
	$reg->set_validator($id_value, false, '', VRT_TEXT, 0, 1000);

	$id_value = $reg->add_value($id_page_path, '_video_link', 'Video link', KEY_TYPE_TEXT, 'http://www.umarex-laserliner.de/cms/index.php?option=com_content&view=article&id=11&Itemid=18');
	$reg->set_validator($id_value, false, '', VRT_TEXT, 0, 1000);

	
	$id_page_path = $reg->add_path($id_path_static, '_main', 'Главная');
	$id_value = $reg->add_value($id_page_path, '_company_name', 'Название компании', KEY_TYPE_TEXT, 'UMAREX-Laserliner');
	$reg->set_validator($id_value, true, '', VRT_TEXT, 0, 1000);
	
	$id_value = $reg->add_value($id_page_path, '_title', 'Заголовок', KEY_TYPE_TEXT, 'Добро пожаловать ...');
	$reg->set_validator($id_value, true, '', VRT_TEXT, 0, 1000);
	
$vlv = <<<EOD
<p><strong>Laserliner</strong> - это всемирно признанный компетентный партнер. Ознакомьтесь с нашим большим ассортиментом приборов для профессионалов и домашних мастеров – это ротационные, перекрёстные и линейные лазерные приборы, влагомеры и пирометры, лазерные дальномеры и дорожные колёса, электронные поисковые приборы, электронные уровни и угломеры, штативы, рейки и многое другое.</p>
<p><strong>Демонстрационные и учебные видеоролики</strong> по работе и применению приборов Вы найдёте на нашем сайте и TS мониторах в сети гипермаркетов Епицентр, примеры применения приборов Вы увидите на их упаковке, а наглядную демонстрацию их работы  на национальных выставках. 
Мы гарантируем профессиональную консультацию и качественное сервисное обслуживание.<br>
<strong>Приборы Laserliner сертифицированы в Украине.</strong></p>
     
EOD;
	$id_value = $reg->add_value($id_page_path, '_description', 'Описание', KEY_TYPE_HTML, $vlv);
	$reg->set_validator($id_value, true, '', VRT_TEXT, 0, 10000);
	
	$id_value = $reg->add_value($id_page_path, '_img', 'Изображение (250x210)', KEY_TYPE_IMAGE, $vlv);
	$reg->set_validator($id_value, true, '', VRT_IMAGE_FILE);
	
	$id_value = $reg->add_value($id_page_path, '_catalog_pdf_link', 'Сcылка на файл каталога', KEY_TYPE_TEXT, 'pub/catalog/2011_RU.pdf');
	$reg->set_validator($id_value, false, '', VRT_TEXT, 3, 500);
	
	$id_value = $reg->add_value($id_page_path, '_video', 'Laserliner видео', KEY_TYPE_FILE, '');
	$reg->set_validator($id_value, false, '', VRT_CUSTOM_FILE, array('video/x-flv'));
	
	$id_value = $reg->add_value($id_page_path, '_video_img', 'Изображение заменяющее видео (155x155)', KEY_TYPE_IMAGE, '');
	$reg->set_validator($id_value, false, '', VRT_IMAGE_FILE);
	
	$id_page_path = $reg->add_path($id_path_static, '_contact', 'Контакты');
	$id_value = $reg->add_value($id_page_path, '_title', 'Название компании', KEY_TYPE_TEXT, '<b>ООО "Христиан Дерр"</b> - официальный представитель Laserliner в Украине.');
	$reg->set_validator($id_value, true, '', VRT_TEXT, 0, 1000);
	
	$id_value = $reg->add_value($id_page_path, '_address', 'Адрес', KEY_TYPE_MEMO, '02105 Киев, просп. Мира, 4, оф.206');
	$reg->set_validator($id_value, true, '', VRT_TEXT, 0, 1000);
	
	$id_value = $reg->add_value($id_page_path, '_telephone', 'Тел.', KEY_TYPE_TEXT, '(044) 494-20-98');
	$reg->set_validator($id_value, true, '', VRT_TEXT, 0, 500);
	
	$id_value = $reg->add_value($id_page_path, '_fax', 'Факс', KEY_TYPE_TEXT, '(044) 494-20-90');
	$reg->set_validator($id_value, true, '', VRT_TEXT, 0, 500);
	
	$id_value = $reg->add_value($id_page_path, '_email', 'Е-mail', KEY_TYPE_TEXT, 'info@laserliner.com.ua');
	$reg->set_validator($id_value, true, '', VRT_TEXT, 0, 500);
	
	/* ------------- сотрудники отдела продаж ------------------ */
	
	$id_value = $reg->add_value($id_page_path, '_first_salesperson', 'Первый сотрудник отдела продаж', KEY_TYPE_TEXT, 'Кищенко Владимир Игоревич');
	$reg->set_validator($id_value, true, '', VRT_TEXT, 0, 500);
	
	$id_value = $reg->add_value($id_page_path, '_first_salesperson_contact', 'Телефон первого сотрудника ОП', KEY_TYPE_TEXT, '063-667-54-97');
	$reg->set_validator($id_value, true, '', VRT_TEXT, 0, 500);
	
	$id_value = $reg->add_value($id_page_path, '_second_salesperson', 'Второй сотрудник отдела продаж', KEY_TYPE_TEXT, 'Резников Виталий Юрьевич');
	$reg->set_validator($id_value, false, '', VRT_TEXT, 0, 500);
	
	$id_value = $reg->add_value($id_page_path, '_second_salesperson_contact', 'Телефон второго сотрудника ОП', KEY_TYPE_TEXT, '067-584-44-09');
	$reg->set_validator($id_value, false, '', VRT_TEXT, 0, 500);
	
	$id_value = $reg->add_value($id_page_path, '_third_salesperson', 'Третий сотрудник отдела продаж', KEY_TYPE_TEXT, '');
	$reg->set_validator($id_value, false, '', VRT_TEXT, 0, 500);
	
	$id_value = $reg->add_value($id_page_path, '_third_salesperson_contact', 'Телефон третьего сотрудника ОП', KEY_TYPE_TEXT, '');
	$reg->set_validator($id_value, false, '', VRT_TEXT, 0, 500);
	
	/* ------------- сотрудники сервисной службы ------------------ */
	
	$id_value = $reg->add_value($id_page_path, '_first_employee_service', 'Первый сотрудник отдела продаж', KEY_TYPE_TEXT, 'Гетманцев Олег Александрович');
	$reg->set_validator($id_value, true, '', VRT_TEXT, 0, 500);
	
	$id_value = $reg->add_value($id_page_path, '_first_employee_service_contact', 'Телефон первого сотрудника ОП', KEY_TYPE_TEXT, '098-551-24-18');
	$reg->set_validator($id_value, true, '', VRT_TEXT, 0, 500);
	
	$id_value = $reg->add_value($id_page_path, '_second_employee_service', 'Второй сотрудник отдела продаж', KEY_TYPE_TEXT, '');
	$reg->set_validator($id_value, false, '', VRT_TEXT, 0, 500);
	
	$id_value = $reg->add_value($id_page_path, '_second_employee_service_contact', 'Телефон второго сотрудника ОП', KEY_TYPE_TEXT, '');
	$reg->set_validator($id_value, false, '', VRT_TEXT, 0, 500);
	
	$id_value = $reg->add_value($id_page_path, '_third_employee_service', 'Третий сотрудник отдела продаж', KEY_TYPE_TEXT, '');
	$reg->set_validator($id_value, false, '', VRT_TEXT, 0, 500);
	
	$id_value = $reg->add_value($id_page_path, '_third_employee_service_contact', 'Телефон третьего сотрудника ОП', KEY_TYPE_TEXT, '');
	$reg->set_validator($id_value, false, '', VRT_TEXT, 0, 500);
	
	/* --------------- изображения карты ------------------------ */
	
	$id_value = $reg->add_value($id_page_path, '_sm_img', 'Маленькое изображение (180x200)', KEY_TYPE_IMAGE, '');
	$reg->set_validator($id_value, false, '', VRT_IMAGE_FILE);	
	
	$id_value = $reg->add_value($id_page_path, '_med_img', 'Среднее изображение (480x400)', KEY_TYPE_IMAGE, '');
	$reg->set_validator($id_value, false, '', VRT_IMAGE_FILE);
	
	$id_value = $reg->add_value($id_page_path, '_big_img', 'Среднее изображение (800x600)', KEY_TYPE_IMAGE, '');
	$reg->set_validator($id_value, false, '', VRT_IMAGE_FILE);
	
	/* --------------- о компании ------------------------ */
	
	$id_page_path = $reg->add_path($id_path_static, '_about', 'О компании');
$vlv = <<<EOD
        <p><%=image_1%></p>
        <p>Продукция Umarex с фирменным товарным знаком Laserliner за несколько лет стала у профессионалов ассоциироваться с инновационно - измерительными технологиями. Под оранжевым фирменным товарным знаком и началось триумфальное шествие измерительных приборов с лазерной технологией. </p>
        <p>Автоматические ротационные лазеры приборы с системой анти - дрейфа (ADS), а также с технологией передачи радиосигнала (RFT) на приёмник лазерного луча, делают возможным выполнение различных сложных задач профессиональными строителями. Приёмник лазерного луча при помощи маркировки Spotlite, облегчает работу на стройплощадке и при проведении ремонтных работ дома.</p>
        <p align="center"><%=image_2%></p>
        <p>Лазерные приборы позволяют быстро проверить горизонт и вертикаль фундаментов и стен, проектировать наклон водопроводных и канализационных труб, планировать уклоны земельного участка, монтировать секции забора, контролировать кладку кирпича и облицовочной плитки, производить разметку маяков для монтажа потолков и заливки пола, монтировать телескопические ворота и кровлю, помогать всем без исключения делать свою работу эффективной.</p>
        <p>Инженеры Umarex постоянно работают над усовершенствованием функциональности приборов. Особое внимание уделяется и совершенствованию дизайна.</p>
        <p align="center"><%=image_3%></p>
        <p>Качество - это главный критерий. Таким образом, все лазерные уровни и нивелиры проходят контроль на оптоэлектронном оборудовании для обеспечения 100%-й точности.</p>
        <p>Команда Laserliner уделяет большое внимание не только техническому развитию приборов. Разнообразные наглядные пособия подробно демонстрируют полный ассортимент продукции. Кроме того, демонстрационные и учебные видеоролики по работе и применению приборов Вы найдёте на нашем сайте и TS мониторах в торговых сетях. Цветные иллюстрации в каталогах и на упаковках приборов дают возможность понять о разнообразии и возможностях использования их.</p>
EOD;
	$reg->add_value($id_page_path, '_template', 'Шаблон', KEY_TYPE_HTML, $vlv);
	
	$id_value = $reg->add_value($id_page_path, '_img_1', 'Первое изображение (<%=image_1%>)', KEY_TYPE_IMAGE);
	$reg->set_validator($id_value, false, '', VRT_IMAGE_FILE);
	
	$id_value = $reg->add_value($id_page_path, '_img_2', 'Второе изображение (<%=image_2%>)', KEY_TYPE_IMAGE);
	$reg->set_validator($id_value, false, '', VRT_IMAGE_FILE);
	
	$id_value = $reg->add_value($id_page_path, '_img_3', 'Третье изображение (<%=image_3%>)', KEY_TYPE_IMAGE);
	$reg->set_validator($id_value, false, '', VRT_IMAGE_FILE);
	
	$id_value = $reg->add_value($id_page_path, '_img_4', 'Четвертое изображение (<%=image_4%>)', KEY_TYPE_IMAGE);
	$reg->set_validator($id_value, false, '', VRT_IMAGE_FILE);
	
	$id_value = $reg->add_value($id_page_path, '_img_5', 'Пятое изображение (<%=image_5%>)', KEY_TYPE_IMAGE);
	$reg->set_validator($id_value, false, '', VRT_IMAGE_FILE);
	
	$id_page_path = $reg->add_path($id_path_static, '_partnership', 'Сотрудничество');
$vlv = <<<EOD
<p>Для продвижения на рынке приборов компании Umarex - Laserliner приглашаем к сотрудничеству дилеров. В основе эффективного взаимодействия с нашими партнерами лежит гибкая ценовая политика компании, взаимное доверие и прозрачность деловых отношений. Мы приглашаем Вас к сотрудничеству в рамках нашей новой региональной маркетинговой программы, которая включает в себя следующее:</p>
        
<ul class="list-page">
	<li>"Индивидуальный подход" - мы выбираем для каждого конкретного дилера именно те формы работы, которые наиболее полно отвечают его потребностям, наиболее удобны для него.</li>
    <li>"Бесплатное обучение" – наш специалист обучит ваших менеджеров по продажам или продавцов-консультантов, как правильно представить каждую единицу товара, так что вы получите готовую к работе команду.</li>
    <li>"Реальные дилерские скидки" - наши дилеры, постоянно работающие с нами, имеют хорошие скидки, с последующим увеличением процента скидок на отдельные позиции.</li>
    <li>"Мощная поддержка дилеров" - речь идет о предоставлении печатной и видео продукции, рекламной поддержке, поддержке в организации дилерской экспозиции.</li>
</ul>
        
<p>Как резюме, заметим, что последние годы мы плотно сотрудничаем с дилерами из самых различных регионов Украины. Мы констатируем, что многие наши партнёры существенно укрепили свои позиции в регионах. Мы всегда прикладываем все необходимые усилия, чтобы сотрудничество наше было плодотворным и взаимовыгодным.</p>
EOD;
	$reg->add_value($id_page_path, '_template', 'Шаблон', KEY_TYPE_HTML, $vlv);
	
}

if ($module_name == 'Localizer') {
    $loc = $this->get_module('Localizer');
}

return true;
?>