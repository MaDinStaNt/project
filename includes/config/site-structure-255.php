<?php
$ss =
array(
	'title' => 'LLA', 'short-title' => 'LLA', 'mode' => 'hidden',  'tag' => '', 'class' => 'admin/IndexPage.php', 'template' => 'admin/index.tpl', 'children' => array(
		0 => array(
			'title' => 'Login', 'tag' => 'login', 'mode' => 'hidden', 'class' => 'admin/LoginPage.php', 'template' => 'admin/login.tpl', 'children' => array(
			),
		),
		1 => array(
			'title' => 'Users', 'tag' => 'users', 'class' => 'admin/users/UsersPage.php', 'template' => 'admin/users/users.tpl', 'children' => array(
                0 => array(
                        'title' => 'User edit', 'tag' => 'user_edit', 'mode' => 'hidden', 'class' => 'admin/users/UserEditPage.php', 'template' => 'admin/users/user_edit.tpl'
                ),
				1 => array(
					'title' => 'User roles', 'tag' => 'roles', 'class' => 'admin/users/RolesPage.php', 'template' => 'admin/users/roles.tpl', 'children' => array(
		                0 => array(
		                        'title' => 'Role edit', 'tag' => 'user_role_edit', 'mode' => 'hidden', 'class' => 'admin/users/UserRoleEditPage.php', 'template' => 'admin/users/user_role_edit.tpl'
		            	),
					),
				),
				2 => array(
					'title' => 'User groups', 'tag' => 'groups', 'class' => 'admin/users/GroupsPage.php', 'template' => 'admin/users/groups.tpl', 'children' => array(
		                0 => array(
		                        'title' => 'Group edit', 'tag' => 'user_group_edit', 'mode' => 'hidden', 'class' => 'admin/users/UserGroupEditPage.php', 'template' => 'admin/users/user_group_edit.tpl'
		            	),
					),
				),
			),
		),
		2 => array(
			'title' => 'Localizer strings', 'tag' => 'localizer_strings', 'class' => 'admin/localizer/StringsPage.php', 'template' => 'admin/localizer/strings.tpl', 'children' => array(
                0 => array(
                    'title' => 'Localizer string view', 'tag' => 'loc_strings_edit', 'mode' => 'hidden', 'class' => 'admin/localizer/StringEditPage.php', 'template' => 'admin/localizer/string_edit.tpl', 'children' => array(
                	),
            	),
				1 => array(
					'title' => 'Languages', 'tag' => 'languages', 'class' => 'admin/localizer/LanguagesPage.php', 'template' => 'admin/localizer/languages.tpl', 'children' => array(
		                0 => array(
		                        'title' => 'Language edit', 'tag' => 'loc_lang_edit', 'mode' => 'hidden', 'class' => 'admin/localizer/LanguageEditPage.php', 'template' => 'admin/localizer/language_edit.tpl'
		            	),
					),
				),
			),
		),
		3 => array(
			'title' => 'Settings', 'tag' => 'registry', 'class' => 'admin/registry/RegistryPage.php', 'template' => 'admin/registry/_registry_out.tpl', 'children' => array(
			),
		),
	),
);
?>