<?
if ($module_name == 'Localizer')
{
	$loc = &$this->get_module('Localizer');

	$loc->add_string('validator_empty_string', 'Message when field is empty', 1, 'Please enter a value in %1$s field.');
	$loc->add_string('validator_min_length', 'Message when field is shorter than should be', 1, 'Please enter at least %2$d characters in %1$s field.');
	$loc->add_string('validator_max_length', 'Message when field is longer than should be', 1, 'Please enter not more than %2$d characters in %1$s field.');
	$loc->add_string('validator_min_value', 'Message when field is smaller than should be', 1, 'Please enter a value greater or equal than %2$s in %1$s field.');
	$loc->add_string('validator_max_value', 'Message when field is smaller than should be', 1, 'Please enter a value smaller or equal than %2$s in %1$s field.');
	$loc->add_string('validator_valid_value', 'Message when invalid value', 1, 'Please enter a valid value in %1$s field.');
	$loc->add_string('validator_not_image_file', 'Message when user try to upload not image file', 1, 'Please select an image file in %1$s field.');
	$loc->add_string('validator_invalid_file', 'Message when user try to upload invalid type of file', 1, 'Please select correct file type in %1$s field.');

	$loc->add_string('validator_invalid_date', 'Message when user enters wrong date', 1, 'Please enter valid date in %1$s field.');
	$loc->add_string('validator_invalid_time', 'Message when user enters wrong time', 1, 'Please enter valid time in %1$s field.');
	
	$loc->add_string('validator_invalid_year', 'Message when user enters wrong year', 1, 'Please enter a valid year in %1$s field.');
	$loc->add_string('validator_invalid_month', 'Message when user enters wrong month', 1, 'Please enter a valid month in %1$s field.');
	$loc->add_string('validator_invalid_day', 'Message when user enters wrong day', 1, 'Please enter a valid day in %1$s field.');
	$loc->add_string('validator_invalid_hour', 'Message when user enters wrong hour', 1, 'Please enter a valid hour in %1$s field.');
	$loc->add_string('validator_invalid_min', 'Message when user enters wrong minute', 1, 'Please enter a valid minute in %1$s field.');
	$loc->add_string('validator_invalid_sec', 'Message when user enters wrong second', 1, 'Please enter a valid second in %1$s field.');

	$loc->add_string('validator_need_number', 'Message when user enters wrong number', 1, 'Please enter a valid number in %1$s field.');

	// import  
	/*  
	$loc->add_string('import_validator_empty_string', 'Message when field is empty', 1, 'Please enter %1$s.');
	$loc->add_string('import_validator_min_length', 'Message when field is shorter than should be', 1, 'Please enter at least %2$d characters in %1$s.');
	$loc->add_string('import_validator_max_length', 'Message when field is longer than should be', 1, 'Please enter not more than %2$d characters in %1$s.');
	$loc->add_string('import_validator_min_value', 'Message when field is smaller than should be', 1, 'Please enter a value greater or equal to %2$s in %1$s.');
	$loc->add_string('import_validator_max_value', 'Message when field is smaller than should be', 1, 'Please enter a value smaller or equal to %2$s in %1$s.');
	$loc->add_string('import_validator_valid_value', 'Message when invalid value', 1, 'Please enter a valid %1$s.');
	$loc->add_string('import_validator_not_image_file', 'Message when user try to upload not image file', 1, 'Please select an image file in %1$s.');
	$loc->add_string('import_validator_invalid_file', 'Message when user try to upload invalid type of file', 1, 'Please select correct file type in %1$s.');

	$loc->add_string('import_validator_invalid_date', 'Message when user enters wrong date', 1, 'Please enter valid date in %1$s.');
	$loc->add_string('import_validator_invalid_time', 'Message when user enters wrong time', 1, 'Please enter valid time in %1$s.');
	
	$loc->add_string('import_validator_invalid_year', 'Message when user enters wrong year', 1, 'Please enter a valid year in %1$s.');
	$loc->add_string('import_validator_invalid_month', 'Message when user enters wrong month', 1, 'Please enter a valid month in %1$s.');
	$loc->add_string('import_validator_invalid_day', 'Message when user enters wrong day', 1, 'Please enter a valid day in %1$s.');
	$loc->add_string('import_validator_invalid_hour', 'Message when user enters wrong hour', 1, 'Please enter a valid hour in %1$s.');
	$loc->add_string('import_validator_invalid_min', 'Message when user enters wrong minute', 1, 'Please enter a valid minute in %1$s.');
	$loc->add_string('import_validator_invalid_sec', 'Message when user enters wrong second', 1, 'Please enter a valid second in %1$s.');

	$loc->add_string('import_validator_need_number', 'Message when user enters wrong number', 1, 'Please enter a valid number in %1$s.');
	*/
	$loc->add_string('warning_obligatory_fields', 'warning_obligatory_fields', 1, 'Warning!</span> Fields marked with <strong>bold</strong> are obligatory');
	$loc->add_string('login_form_name', 'login_form_name', 1, '');
	$loc->add_string('login_form_password', 'login_form_password', 1, '');
	$loc->add_string('login_form_store', 'login_form_store', 1, '');
	$loc->add_string('admin_panel', 'admin_panel', 1, '');
	$loc->add_string('hide_panel', 'hide_panel', 1, '');
	$loc->add_string('show_panel', 'show_panel', 1, '');
	$loc->add_string('you_logged_in_as', 'you_logged_in_as', 1, '');
	$loc->add_string('settings', 'settings', 1, '');
	$loc->add_string('logout_question', 'logout_question', 1, '');
	$loc->add_string('logout', 'logout', 1, '');
	$loc->add_string('welcome_to_admin_panel', 'welcome_to_admin_panel', 1, '');
	$loc->add_string('select_item_to_modify', 'select_item_to_modify', 1, '');
	$loc->add_string('btn_delete_selected', 'btn_delete_selected', 1, '');
	$loc->add_string('delete_confirm', 'delete_confirm', 1, '');
	$loc->add_string('localizer_strings', 'localizer_strings', 1, '');
	$loc->add_string('total', 'total', 1, '');
	$loc->add_string('page', 'page', 1, '');
	$loc->add_string('next_page', 'next_page', 1, '');
	$loc->add_string('last_page', 'last_page', 1, '');
	$loc->add_string('btn_add', 'btn_add', 1, '');
	$loc->add_string('languages', 'languages', 1, '');
	$loc->add_string('btn_save', 'btn_save', 1, '');
	$loc->add_string('any', 'any', 1, '');
	$loc->add_string('name', 'name', 1, '');
	$loc->add_string('company', 'company', 1, '');
	$loc->add_string('email', 'email', 1, '');
	$loc->add_string('user_role_id', 'user_role_id', 1, '');
	$loc->add_string('create_date_from', 'create_date_from', 1, '');
	$loc->add_string('last_login_from', 'last_login_from', 1, '');
	$loc->add_string('status', 'status', 1, '');
	$loc->add_string('user_role', 'user_role', 1, '');
	$loc->add_string('create_date', 'create_date', 1, '');
	$loc->add_string('', '', 1, '');
	$loc->add_string('', '', 1, '');
	$loc->add_string('', '', 1, '');
	$loc->add_string('', '', 1, '');
	$loc->add_string('', '', 1, '');
	$loc->add_string('', '', 1, '');
	$loc->add_string('', '', 1, '');
	$loc->add_string('', '', 1, '');
	$loc->add_string('', '', 1, '');
}
?>