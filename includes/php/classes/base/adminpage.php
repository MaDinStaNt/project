<?
/**
 * @package LLA.Base
 */
/**
 * Administrative page
 * history:<br>
 *        v 1.2.0 - Multilevel menu support (AD)<br>
 *        v 1.1.2 - fix: when select one submodule -> all submodules of module draw as active (AD)<br>
 *        v 1.1.1 - form handling support (VK)<br>
 *        v 1.1.0 - module2 support (LA, PERSON)<br>
 *        v 1.0.0 - created (VK)<br>
 *
 * @package LLA.Base
 * @version 1.2.0
 */
class CAdminPage extends CHTMLPage
{
        function CAdminPage(&$app, $content)
        {
                parent::CHTMLPage($app, $content);

                $this->NoCache = true;
                $this->IsSecure = true;
                $this->UserLevel = USER_LEVEL_GLOBAL_ADMIN;

                $this->h_header = '_admin_head.tpl';
                $this->h_body = '_admin_body.tpl';
                $this->h_footer = '_admin_foot.tpl';
                $this->template_vars['admin_page'] = true;
				if ($this->Application->User->is_logged()) {
					if ($this->Application->User->UserData['user_role_id'] < USER_LEVEL_GLOBAL_ADMIN) {
						$this->Application->User->logout();
					}
				}
				
        }

        function on_page_init()
        {
            $res = parent::on_page_init();
            return $res;
        }

        function parse_data()
        {
                if (!parent::parse_data()) return false;

                return true;
        }
}
?>