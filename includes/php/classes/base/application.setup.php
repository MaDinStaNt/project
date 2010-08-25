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

}
?>