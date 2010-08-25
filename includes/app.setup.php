<?
if ($module_name == 'Registry')
{
    $reg = &$this->get_module('Registry');
 
	$id_path_site_settings = $reg->add_path(null, '_site_settings', 'Site settings');

	$id_page_site_update = $reg->add_path($id_path_site_settings, '_site_update', 'Site update');
	$reg->add_value($id_page_site_update, '_stop_site_update', 'Site update', KEY_TYPE_CHECKBOX, '0');

}

if ($module_name == 'User')
{
}

if ($module_name == 'Localizer')
{
    $loc = &$this->get_module('Localizer');
}

return true;
?>