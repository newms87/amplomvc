<?php
$links = array(
	'home'       => array(
		'display_name' => 'Home',
		'path'         => '',
	),

	'dashboards' => array(
		'display_name' => "Dashboards",
		'path'         => 'admin/dashboard',
	),

	'content'    => array(
		'display_name' => 'Content',
		'children'     => array(
			'content_blocks' => array(
				'display_name' => 'Blocks',
				'path'         => 'admin/block',
			),
			'content_pages'  => array(
				'display_name' => 'Pages',
				'path'         => 'admin/page',
			),
		),
	),

	'users'      => array(
		'display_name' => 'Users',
		'children'     => array(
			'users_users'      => array(
				'display_name' => 'Users',
				'path'         => 'admin/user',
			),
			'users_user_roles' => array(
				'display_name' => 'User Roles',
				'path'         => 'admin/settings/role',
			),
			'users_api_users' => array(
				'display_name' => 'API Users',
				'path'         => 'admin/api_user',
			),
		),
	),

	'system'     => array(
		'display_name' => 'System',
		'children'     => array(
			'system_settings'          => array(
				'display_name' => 'Settings',
				'path'         => 'admin/settings',
			),
			'system_mail'              => array(
				'display_name' => 'Mail',
				'children'     => array(
					'system_mail_send_email'    => array(
						'display_name' => 'Send Email',
						'path'         => 'admin/mail/send_email',
					),
					'system_mail_mail_messages' => array(
						'display_name' => 'Mail Messages',
						'path'         => 'admin/mail/messages',
					),
					'system_mail_error'         => array(
						'display_name' => 'Failed Messages',
						'path'         => 'admin/mail/error',
					),
				),
			),
			'system_views'             => array(
				'display_name' => 'Views',
				'path'         => 'admin/view',
			),
			'system_navigation'        => array(
				'display_name' => 'Navigation',
				'path'         => 'admin/navigation',
			),
			'system_system_clearcache' => array(
				'display_name' => 'Clear Cache',
				'path'         => 'admin/settings/clear_cache',
				'query'        => 'redirect',
			),
			'system_system_refreshsprite' => array(
				'display_name' => 'Refresh Sprite Sheets',
				'path'         => 'admin/settings/refresh_sprite_sheets',
				'query'        => 'redirect',
			),
			'system_logs'              => array(
				'display_name' => 'Logs',
				'path'         => 'admin/logs',
			),
			'system_history'           => array(
				'display_name' => 'History',
				'path'         => 'admin/history',
			),
			'system_localisation'      => array(
				'display_name' => 'Localisation',
				'children'     => array(
					'system_localisation_currencies' => array(
						'display_name' => 'Currencies',
						'path'         => 'admin/localisation/currency',
					),
					'system_localisation_languages'  => array(
						'display_name' => 'Languages',
						'path'         => 'admin/localisation/language',
					),
					'system_localisation_zones'      => array(
						'display_name' => 'Zones',
						'path'         => 'admin/localisation/zone',
					),
					'system_localisation_countries'  => array(
						'display_name' => 'Countries',
						'path'         => 'admin/localisation/country',
					),
					'system_localisation_geo_zones'  => array(
						'display_name' => 'Geo Zones',
						'path'         => 'admin/localisation/geo_zone',
					),
				),
			),

			'system_plugins'           => array(
				'display_name' => 'Plugins',
				'path'         => 'admin/plugin',
			),
		),
	),
);
