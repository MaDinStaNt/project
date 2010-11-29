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
	$loc->add_string('login_form_name', '', 1, 'Email');
	$loc->add_string('login_form_password', '', 1, 'Password');
	$loc->add_string('login_form_store', '', 1, 'remember me');
	$loc->add_string('admin_panel', '', 1, 'Administration Panel');
	$loc->add_string('hide_panel', '', 1, 'Hide Panel');
	$loc->add_string('show_panel', '', 1, 'Show Panel');
	$loc->add_string('you_logged_in_as', '', 1, 'You are logged in as');
	$loc->add_string('settings', '', 1, 'Settings');
	$loc->add_string('logout_question', '', 1, 'Are you sure you want to logout?');
	$loc->add_string('logout', '', 1, 'Logout');
	$loc->add_string('welcome_to_admin_panel', '', 1, 'Welcome Administration Panel! ');
	$loc->add_string('select_item_to_modify', '', 1, '	Please select an item to modify ');
	$loc->add_string('btn_delete_selected', '', 1, 'Delete selected');
	$loc->add_string('delete_confirm', '', 1, 'Are you sure you want to delete selected items?');
	$loc->add_string('localizer_strings', '', 1, 'Localizer strings');
	$loc->add_string('total', '', 1, 'total');
	$loc->add_string('page', '', 1, 'Page');
	$loc->add_string('next_page', '', 1, 'Next Page');
	$loc->add_string('last_page', '', 1, 'Last Page');
	$loc->add_string('btn_add', '', 1, 'Add');
	$loc->add_string('languages', '', 1, 'Languages');
	$loc->add_string('btn_save', '', 1, 'Save');
	$loc->add_string('any', '', 1, '- Any -');
	$loc->add_string('name', '', 1, 'Name');
	$loc->add_string('company', '', 1, 'Company');
	$loc->add_string('email', '', 1, 'Email');
	$loc->add_string('user_role_id', '', 1, 'User Role');
	$loc->add_string('create_date_from', '', 1, 'Create date from');
	$loc->add_string('last_login_from', '', 1, 'Last login from');
	$loc->add_string('status', '', 1, 'Status');
	$loc->add_string('user_role', '', 1, 'User Role');
	$loc->add_string('create_date', '', 1, 'Create date');
	                                                                               
	$loc->add_string('password', '', 1, 'Password');
	$loc->add_string('remember_me', '', 1, 'remember me');
	$loc->add_string('btn_reset', '', 1, 'Reset');
	$loc->add_string('btn_log_in', '', 1, 'Login');
	$loc->add_string('filter', '', 1, 'Filter');
	$loc->add_string('show_filter', '', 1, 'Show Filter');
	$loc->add_string('hide_filter', '', 1, 'Hide Filter');
	$loc->add_string('to', '', 1, 'to');
	$loc->add_string('btn_clear', '', 1, 'Clear');
	$loc->add_string('btn_filter', '', 1, 'Filter');
	$loc->add_string('users', '', 1, 'Users');
	$loc->add_string('active', '', 1, 'Active');
	$loc->add_string('inactive', '', 1, 'Inactive');
	$loc->add_string('suspended', '', 1, 'Suspended');
	$loc->add_string('address', '', 1, 'Address');
	$loc->add_string('city', '', 1, 'City');
	$loc->add_string('state_id', '', 1, 'State');
	$loc->add_string('zip', '', 1, 'Zip');
	$loc->add_string('btn_close', '', 1, 'Close');
	$loc->add_string('title', '', 1, 'Title');
	$loc->add_string('description', '', 1, 'Description');
	$loc->add_string('id', '', 1, 'ID');
	$loc->add_string('user_roles', '', 1, 'User Roles');
	$loc->add_string('user_groups', '', 1, 'User Groups');
	$loc->add_string('nav_default_empty_message', '', 1, 'There are currently no records to be shown');
	$loc->add_string('value', '', 1, 'Value');
	$loc->add_string('language_id', '', 1, 'Language');
	$loc->add_string('abbreviation', '', 1, 'Abbreviation');
}
?>