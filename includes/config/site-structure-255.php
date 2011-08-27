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
				0 => array(
					'title' => 'Images', 'tag' => 'image', 'class' => 'admin/registry/ImagePage.php', 'template' => 'admin/registry/image.tpl', 'children' => array(
						0 => array(
								'title' => 'Image view', 'tag' => 'image_edit', 'mode' => 'hidden', 'class' => 'admin/registry/ImageEditPage.php', 'template' => 'admin/registry/image_edit.tpl', 'children' => array(
								0 => array(
										'title' => 'Image size view', 'tag' => 'image_size_edit', 'mode' => 'hidden', 'class' => 'admin/registry/ImageSizeEditPage.php', 'template' => 'admin/registry/image_size_edit.tpl'
								),
							),
						),
					),
				),
			),
		),
		4 => array(
			'title' => 'Продукция', 'tag' => 'production', 'class' => 'admin/product/productionpage.php', 'template' => 'admin/product/production.tpl', 'children' => array(
				0 => array(
					'title' => 'Категории продуктов', 'tag' => 'product_category', 'class' => 'admin/product/categorypage.php', 'template' => 'admin/product/category.tpl', 'children' => array(
						0 => array(
							'title' => 'Редактирование категории', 'tag' => 'product_category_edit', 'class' => 'admin/product/categoryeditpage.php', 'template' => 'admin/product/category_edit.tpl', 'mode' => 'hidden'
						),
					),
				), 
				1 => array(
					'title' => 'Продукты', 'tag' => 'product', 'class' => 'admin/product/productpage.php', 'template' => 'admin/product/product.tpl', 'children' => array(
						0 => array(
							'title' => 'Редактирование продукты', 'tag' => 'product_edit', 'class' => 'admin/product/producteditpage.php', 'template' => 'admin/product/product_edit.tpl', 'mode' => 'hidden', 'children' => array(
								0 => array(
									'title' => 'Изображение продукта', 'tag' => 'image_edit', 'class' => 'admin/product/imageeditpage.php', 'template' => 'admin/product/image_edit.tpl', 'mode' => 'hidden'
								),
								1 => array(
									'title' => 'Редактирование характеристики продукта', 'tag' => 'technical_data_edit', 'class' => 'admin/product/technicaldataeditpage.php', 'template' => 'admin/product/technical_data_edit.tpl', 'mode' => 'hidden'
								),
								2 => array(
									'title' => 'Редактирование комплектации продукта', 'tag' => 'equipment_edit', 'class' => 'admin/product/equipmenteditpage.php', 'template' => 'admin/product/equipment_edit.tpl', 'mode' => 'hidden'
								),
							),
						),
					),
				),
			),
		),
		5 => array(
			'title' => 'Выставки', 'tag' => 'exhibition', 'class' => 'admin/exhibition/exhibitionpage.php', 'template' => 'admin/exhibition/exhibition.tpl', 'children' => array(
				0 => array(
					'title' => 'Редактирование выставки', 'tag' => 'exhibition_edit', 'class' => 'admin/exhibition/exhibitioneditpage.php', 'template' => 'admin/exhibition/exhibition_edit.tpl', 'mode' => 'hidden', 'children' => array(
						0 => array(
							'title' => 'Изображение с выставки', 'tag' => 'image_edit', 'class' => 'admin/exhibition/imageeditpage.php', 'template' => 'admin/exhibition/image_edit.tpl', 'mode' => 'hidden'
						),
					),
				),
			),
		),
	),
);
?>