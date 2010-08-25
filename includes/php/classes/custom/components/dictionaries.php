<?
class CDictionaries
{
        var $Application;
        var $DataBase;
        var $tv;
        var $last_error;

        function CDictionaries(&$app)
        {
            $this->Application = &$app;
            $this->tv = &$app->template_vars;
            $this->DataBase = &$this->Application->DataBase;
        }

        function get_last_error()
        {
            return $this->last_error;
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
            return $rs->get_field('title');
        }

        function get_state_by_id($id)
        {
            $id = intval($id);
            if ( $id < 1 ) {
                $this->last_error = $this->Application->Localizer->get_string('invalid_input_data');
                return false;
            }

            $rs = $this->DataBase->select_sql('state', array('id' => $id));

            if ( $rs === false ) {
                $this->last_error = $this->Application->Localizer->get_string('database_error');
                return false;
            }

            $this->last_error = '';
            return $rs->get_field('title');
        }
};
?>