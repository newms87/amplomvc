<?php
class Admin_Model_Design_Navigation extends Model
{
	public function addNavigationGroup($data)
	{
		if (!$this->validateNavigationGroup($data)) {
			return false;
		}

		$navigation_group_id = $this->insert("navigation_group", $data);

		//Add Stores
		foreach ($data['stores'] as $store_id) {
			$store_data = array(
				'navigation_group_id' => $navigation_group_id,
				'store_id'            => $store_id
			);

			$this->insert("navigation_store", $store_data);
		}

		//Add Links
		if (!empty($data['links'])) {
			$this->addNavigationLinks($navigation_group_id, $data['links']);
		}

		$this->cache->delete('navigation');

		return true;
	}

	public function editNavigationGroup($navigation_group_id, $data)
	{
		$data['navigation_group_id'] = $navigation_group_id;

		if (!$this->validateNavigationGroup($data)) {
			return false;
		}

		$this->update("navigation_group", $data, $navigation_group_id);

		//Update Stores
		if (isset($data['stores'])) {
			$this->delete("navigation_store", array("navigation_group_id" => $navigation_group_id));

			foreach ($data['stores'] as $store_id) {
				$store_data = array(
					'navigation_group_id' => $navigation_group_id,
					'store_id'            => $store_id
				);

				$this->insert("navigation_store", $store_data);
			}
		}

		//Update Links
		if (isset($data['links'])) {
			$this->delete("navigation", array("navigation_group_id" => $navigation_group_id));

			if (!empty($data['links'])) {
				$this->addNavigationLinks($navigation_group_id, $data['links']);
			}
		}

		$this->cache->delete('navigation');

		return true;
	}

	public function deleteNavigationGroup($navigation_group_id)
	{
		if (!$this->validateDeleteNavigationGroup($navigation_group_id)) {
			return false;
		}

		$this->delete("navigation_group", $navigation_group_id);

		$this->delete("navigation_store", array("navigation_group_id" => $navigation_group_id));
		$this->delete("navigation", array("navigation_group_id" => $navigation_group_id));

		$this->cache->delete('navigation');

		return true;
	}

	public function addNavigationLink($navigation_group_id, $link)
	{
		if (!$this->validateNavigationLink($navigation_group_id, $link)) {
			return false;
		}

		$link['navigation_group_id'] = $navigation_group_id;

		if (!empty($link['parent'])) {
			$link['parent_id'] = $this->queryVar("SELECT navigation_id FROM " . DB_PREFIX . "navigation WHERE `name` = '" . $this->escape($link['parent']) . "'");
		}

		$this->cache->delete('navigation');

		return $this->insert("navigation", $link);
	}

	public function addNavigationLinkTree($navigation_group_id, $links, $parent_id = 0)
	{
		$sort_order = 0;

		foreach ($links as $name => $link) {
			if (!isset($link['sort_order'])) {
				$link['sort_order'] = $sort_order++;
			}

			if (empty($link['name'])) {
				$link['name'] = $name;
			}

			if (!isset($link['status'])) {
				$link['status'] = 1;
			}

			$link['parent_id'] = $parent_id;

			$navigation_id = $this->addNavigationLink($navigation_group_id, $link);

			if (!empty($link['children'])) {
				$this->addNavigationLinkTree($navigation_group_id, $link['children'], $navigation_id);
			}
		}

		return empty($this->error);
	}

	public function addNavigationLinks($navigation_group_id, $links)
	{
		//Transform links into Tree structure (if not already)
		foreach ($links as $nav_id => &$link) {
			if (empty($link['name'])) {
				$link['name'] = $nav_id;
			}

			if (!isset($link['status'])) {
				$link['status'] = 1;
			}

			if (isset($link['parent_id']) && ($pid = $link['parent_id']) != 0) {
				if (!isset($links[$pid]['children'])) {
					$links[$pid]['children'] = array();
				}

				$links[$pid]['children'][$nav_id] = & $link;
			} else {
				$link['parent_id'] = 0;
			}
		}
		unset($link);

		foreach ($links as $key => $link) {
			if ($link['parent_id'] > 0) {
				unset($links[$key]);
			}
		}

		return $this->addNavigationLinkTree($navigation_group_id, $links);
	}

	public function editNavigationLink($navigation_group_id, $navigation_id, $link)
	{
		$link['navigation_id'] = $navigation_id;

		if (!$this->validateNavigationLink($navigation_group_id, $link)) {
			return false;
		}

		$link['navigation_group_id'] = $navigation_group_id;
		$link['navigation_id']       = $navigation_id;

		if (!empty($link['parent'])) {
			$link['parent_id'] = $this->queryVar("SELECT navigation_id FROM " . DB_PREFIX . "navigation WHERE `name` = '" . $this->escape($link['parent']) . "'");
		}

		$this->cache->delete('navigation');

		return $this->update("navigation", $link, $navigation_id);
	}

	public function deleteNavigationLink($navigation_id)
	{
		if (!$this->validateDeleteNavigationLink($navigation_id)) {
			return false;
		}

		$this->cache->delete('navigation');

		return $this->delete("navigation", $navigation_id);
	}

	public function getNavigationGroup($navigation_group_id)
	{
		$nav_group = $this->queryRow("SELECT * FROM " . DB_PREFIX . "navigation_group WHERE navigation_group_id = " . (int)$navigation_group_id);

		$nav_group['stores'] = $this->getNavigationGroupStores($navigation_group_id);
		$nav_group['links']  = $this->getNavigationGroupLinks($navigation_group_id);

		return $nav_group;
	}

	public function getNavigationGroups($data = array(), $select = '*', $total = false)
	{
		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		} elseif (!$select) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "navigation_group ng";

		//Where
		$where = "1";

		if (!empty($data['name'])) {
			$where .= " AND name like '%" . $this->escape($data['name']) . "%'";
		}

		if (isset($data['stores'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "navigation_store ns ON (ns.navigation_group_id=ng.navigation_group_id)";

			if (!is_array($data['stores'])) {
				$data['stores'] = array((int)$data['stores']);
			}

			$where .= " AND ns.store_id IN (" . implode(',', $data['stores']) . ")";
		}

		if (isset($data['status'])) {
			$where .= " AND status = '" . ($data['status'] ? 1 : 0) . "'";
		}

		//Order By & Limit
		if (!$total) {
			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
		} else {
			$order = '';
			$limit = '';
		}

		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		//Execute
		$result = $this->query($query);

		//Process Results
		if ($total) {
			return $result->row['total'];
		} else {
			foreach ($result->rows as $key => &$row) {
				$row['links']  = $this->getNavigationGroupLinks($row['navigation_group_id']);
				$row['stores'] = $this->getNavigationGroupStores($row['navigation_group_id']);
			}

			return $result->rows;
		}
	}

	public function getNavigationLinks()
	{
		$nav_groups = $this->cache->get('navigation_groups.admin');

		if (!$nav_groups) {
			$query = "SELECT ng.* FROM " . DB_PREFIX . "navigation_group ng";
			$query .= " LEFT JOIN " . DB_PREFIX . "navigation_store ns ON (ng.navigation_group_id=ns.navigation_group_id)";
			$query .= " WHERE ng.status='1' AND ns.store_id='-1'";

			$query = $this->query($query);

			$nav_groups = array();

			foreach ($query->rows as &$group) {
				$nav_group_links = $this->getNavigationGroupLinks($group['navigation_group_id']);

				$parent_ref = array();

				foreach ($nav_group_links as $key => &$link) {
					if (!empty($parent_ref[$link['navigation_id']]['children'])) {
						$link['children'] = & $parent_ref[$link['navigation_id']]['children'];
					} else {
						$link['children'] = array();
					}

					$parent_ref[$link['navigation_id']] = & $link;

					if ($link['parent_id']) {
						$parent_ref[$link['parent_id']]['children'][] = & $link;
						unset($nav_group_links[$key]);
					}
				}

				$nav_groups[$group['name']] = $nav_group_links;
			}

			$this->cache->set('navigation_groups.admin', $nav_groups);
		}

		return $nav_groups;
	}

	public function getNavigationGroupLinks($navigation_group_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "navigation WHERE navigation_group_id = '" . (int)$navigation_group_id . "' ORDER BY sort_order ASC", 'navigation_id');
	}

	public function getNavigationGroupStores($navigation_group_id)
	{
		return $this->queryColumn("SELECT store_id FROM " . DB_PREFIX . "navigation_store WHERE navigation_group_id = " . (int)$navigation_group_id);
	}

	public function getTotalNavigationGroups($data)
	{
		return $this->getNavigationGroups($data, '', true);
	}

	public function resetAdminNavigationGroup()
	{
		$links = array(
			'home'       => array(
				'display_name' => 'Home',
				'href'         => 'common/home',
			),
			'content'    => array(
				'display_name' => 'Content',
				'children'     => array(
					'content_blocks'      => array(
						'display_name' => 'Blocks',
						'href'         => 'block/block',
					),
					'content_pages'       => array(
						'display_name' => 'Pages',
						'href'         => 'page',
					),
					'content_leaderboard' => array(
						'display_name' => 'Leaderboard',
						'href'         => 'module/page_headers',
					),
					'content_newsletter'  => array(
						'display_name' => 'Newsletter',
						'href'         => 'mail/newsletter',
					),
				),
			),
			'catalog'    => array(
				'display_name' => 'Catalog',
				'children'     => array(
					'catalog_attributes'   => array(
						'display_name' => 'Attribute Groups',
						'href'         => 'catalog/attribute_group',
					),
					'catalog_options'      => array(
						'display_name' => 'Options',
						'href'         => 'catalog/option',
					),
					'catalog_categories'   => array(
						'display_name' => 'Categories',
						'href'         => 'catalog/category',
					),
					'catalog_products'     => array(
						'display_name' => 'Products',
						'href'         => 'catalog/product',
						'children'     => array(
							'catalog_products_insert'        => array(
								'display_name' => 'Add Product',
								'href'         => 'catalog/product/update',
							),
							'catalog_products_product_class' => array(
								'display_name' => 'Product Classes',
								'href'         => 'catalog/product_class',
							),
						),
					),
					'catalog_manufacturer' => array(
						'display_name' => 'Manufacturers',
						'href'         => 'catalog/manufacturer',
					),
					'catalog_downloads'    => array(
						'display_name' => 'Downloads',
						'href'         => 'catalog/download',
					),
					'catalog_reviews'      => array(
						'display_name' => 'Reviews',
						'href'         => 'catalog/review',
					),
					'catalog_information'  => array(
						'display_name' => 'Information',
						'href'         => 'catalog/information',
					),
				),
			),
			'sales'      => array(
				'display_name' => 'Sales',
				'children'     => array(
					'sales_coupons'       => array(
						'display_name' => 'Coupons',
						'href'         => 'sale/coupon',
					),
					'sales_customers'     => array(
						'display_name' => 'Customers',
						'children'     => array(
							'sales_customers_customers'       => array(
								'display_name' => 'Customers',
								'href'         => 'sale/customer',
							),
							'sales_customers_customer_groups' => array(
								'display_name' => 'Customer Groups',
								'href'         => 'sale/customer_group',
							),
							'sales_customers_ip_blacklist'    => array(
								'display_name' => 'IP Blacklist',
								'href'         => 'sale/customer_blacklist',
							),
						),
					),
					'sales_orders'        => array(
						'display_name' => 'Orders',
						'href'         => 'sale/order',
					),
					'sales_gift_vouchers' => array(
						'display_name' => 'Gift Vouchers',
						'children'     => array(
							'sales_gift_vouchers_voucher_themes' => array(
								'display_name' => 'Voucher Themes',
								'href'         => 'sale/voucher_theme',
							),
							'sales_gift_vouchers_gift_vouchers'  => array(
								'display_name' => 'Gift Vouchers',
								'href'         => 'sale/voucher',
							),
						),
					),
					'sales_returns'       => array(
						'display_name' => 'Returns',
						'href'         => 'sale/return',
					),
				),
			),
			'extensions' => array(
				'display_name' => 'Extensions',
				'children'     => array(
					'plugin_plugins'       => array(
						'display_name' => 'Plugins',
						'href'         => 'plugin/plugin',
					),
					'extensions_payments'      => array(
						'display_name' => 'Payments',
						'href'         => 'extension/payment',
					),
					'extensions_modules'       => array(
						'display_name' => 'Modules',
						'href'         => 'extension/module',
					),
					'extensions_product_feeds' => array(
						'display_name' => 'Product Feeds',
						'href'         => 'extension/feed',
					),
					'extensions_order_totals'  => array(
						'display_name' => 'Order Totals',
						'href'         => 'extension/total',
					),
					'extensions_shipping'      => array(
						'display_name' => 'Shipping',
						'href'         => 'extension/shipping',
					),
				),
			),
			'users'      => array(
				'display_name' => 'Users',
				'children'     => array(
					'users_users'       => array(
						'display_name' => 'Users',
						'href'         => 'user/user',
					),
					'users_user_groups' => array(
						'display_name' => 'User Groups',
						'href'         => 'user/user_permission',
					),
				),
			),
			/*'reports'    => array(
				'display_name' => 'Reports',
				'children'     => array(
					'reports_customers' => array(
						'display_name' => 'Customers',
						'children'     => array(
							'reports_customers_credit'        => array(
								'display_name' => 'Credit',
								'href'         => 'report/customer_credit',
							),
							'reports_customers_reward_points' => array(
								'display_name' => 'Reward Points',
								'href'         => 'report/customer_reward',
							),
							'reports_customers_orders'        => array(
								'display_name' => 'Orders',
								'href'         => 'report/customer_order',
							),
						),
					),
					'reports_products'  => array(
						'display_name' => 'Products',
						'children'     => array(
							'reports_products_purchased' => array(
								'display_name' => 'Purchased',
								'href'         => 'report/product_purchased',
							),
							'reports_products_viewed'    => array(
								'display_name' => 'Viewed',
								'href'         => 'report/product_viewed',
							),
						),
					),
					'reports_sales'     => array(
						'display_name' => 'Sales',
						'children'     => array(
							'reports_sales_orders'   => array(
								'display_name' => 'Orders',
								'href'         => 'report/sale_order',
							),
							'reports_sales_tax'      => array(
								'display_name' => 'Tax',
								'href'         => 'report/sale_tax',
							),
							'reports_sales_coupons'  => array(
								'display_name' => 'Coupons',
								'href'         => 'report/sale_coupon',
							),
							'reports_sales_shipping' => array(
								'display_name' => 'Shipping',
								'href'         => 'report/sale_shipping',
							),
							'reports_sales_returns'  => array(
								'display_name' => 'Returns',
								'href'         => 'report/sale_return',
							),
						),
					),
				),
			), */
			'system'     => array(
				'display_name' => 'System',
				'children'     => array(
					'system_settings'        => array(
						'display_name' => 'Settings',
						'href'         => 'setting/store',
						'children'     => array(
							'system_settings_general'              => array(
								'display_name' => 'General',
								'href'         => 'setting/setting',
							),
							'system_settings_update'               => array(
								'display_name' => 'Update',
								'href'         => 'setting/update',
							),
							'system_settings_orders'               => array(
								'display_name' => 'Orders',
								'children'     => array(
									'system_settings_orders_order_statuses' => array(
										'display_name' => 'Order Statuses',
										'href'         => 'setting/order_status',
									),
								),
							),
							'system_settings_policies'             => array(
								'display_name' => 'Policies',
								'children'     => array(
									'system_settings_policies_shipping_policies' => array(
										'display_name' => 'Shipping Policies',
										'href'         => 'setting/shipping_policy',
									),
									'system_settings_policies_return_policies'   => array(
										'display_name' => 'Return Policies',
										'href'         => 'setting/return_policy',
									),
								),
							),
							'system_settings_returns'              => array(
								'display_name' => 'Returns',
								'children'     => array(
									'system_settings_returns_return_reasons'  => array(
										'display_name' => 'Return Reasons',
										'href'         => 'setting/return_reason',
									),
									'system_settings_returns_return_actions'  => array(
										'display_name' => 'Return Actions',
										'href'         => 'setting/return_action',
									),
									'system_settings_returns_return_statuses' => array(
										'display_name' => 'Return Statuses',
										'href'         => 'setting/return_status',
									),
								),
							),
							'system_settings_controller_overrides' => array(
								'display_name' => 'Controller Overrides',
								'href'         => 'setting/controller_override',
							),
						),
					),
					'system_mail'            => array(
						'display_name' => 'Mail',
						'children'     => array(
							'system_mail_send_email'    => array(
								'display_name' => 'Send Email',
								'href'         => 'mail/send_email',
							),
							'system_mail_mail_messages' => array(
								'display_name' => 'Mail Messages',
								'href'         => 'mail/messages',
							),
							'system_mail_error'         => array(
								'display_name' => 'Failed Messages',
								'href'         => 'mail/error',
							),
						),
					),
					'system_url_alias'       => array(
						'display_name' => 'URL Alias',
						'href'         => 'setting/url_alias',
					),
					'system_db_rules'        => array(
						'display_name' => 'DB Rules',
						'href'         => 'setting/db_rules',
					),
					'system_cron'            => array(
						'display_name' => 'Cron',
						'href'         => 'setting/cron',
					),
					'system_design'          => array(
						'display_name' => 'Design',
						'children'     => array(
							'system_design_banners'    => array(
								'display_name' => 'Banners',
								'href'         => 'design/banner',
							),
							'system_design_navigation' => array(
								'display_name' => 'Navigation',
								'href'         => 'design/navigation',
							),
							'system_design_layouts'    => array(
								'display_name' => 'Layouts',
								'href'         => 'design/layout',
							),
						),
					),
					'system_system_tools'    => array(
						'display_name' => 'System Tools',
						'href'         => 'tool/tool',
					),
					'system_logs'            => array(
						'display_name' => 'Logs',
						'href'         => 'tool/logs',
					),
					'system_localisation'    => array(
						'display_name' => 'Localisation',
						'children'     => array(
							'system_localisation_currencies'     => array(
								'display_name' => 'Currencies',
								'href'         => 'localisation/currency',
							),
							'system_localisation_languages'      => array(
								'display_name' => 'Languages',
								'href'         => 'localisation/language',
							),
							'system_localisation_taxes'          => array(
								'display_name' => 'Taxes',
								'children'     => array(
									'system_localisation_taxes_tax_classes' => array(
										'display_name' => 'Tax Classes',
										'href'         => 'localisation/tax_class',
									),
									'system_localisation_taxes_tax_rates'   => array(
										'display_name' => 'Tax Rates',
										'href'         => 'localisation/tax_rate',
									),
								),
							),
							'system_localisation_zones'          => array(
								'display_name' => 'Zones',
								'href'         => 'localisation/zone',
							),
							'system_localisation_countries'      => array(
								'display_name' => 'Countries',
								'href'         => 'localisation/country',
							),
							'system_localisation_geo_zones'      => array(
								'display_name' => 'Geo Zones',
								'href'         => 'localisation/geo_zone',
							),
							'system_localisation_stock_statuses' => array(
								'display_name' => 'Stock Statuses',
								'href'         => 'localisation/stock_status',
							),
							'system_localisation_length_classes' => array(
								'display_name' => 'Length Classes',
								'href'         => 'localisation/length_class',
							),
							'system_localisation_weight_classes' => array(
								'display_name' => 'Weight Classes',
								'href'         => 'localisation/weight_class',
							),
						),
					),
				),
			),
			'help'       => array(
				'display_name' => 'Help',
				'children'     => array(
					'help_documentation' => array(
						'display_name' => 'Documentation',
						'href'         => 'help/documentation',
					),
				),
			),
		);

		$result = $this->query("SELECT navigation_group_id FROM " . DB_PREFIX . "navigation_group WHERE name = 'admin'");

		if ($result->num_rows) {
			$this->deleteNavigationGroup($result->row['navigation_group_id']);
		}

		$data = array(
			'name'   => 'admin',
			'status' => 1,
			'stores' => array(-1),
			'links'  => $links,
		);

		return $this->addNavigationGroup($data);
	}

	public function validateNavigationGroup($data)
	{
		if (isset($data['name']) && !$this->validation->text($data['name'], 3, 64)) {
			$this->error['name'] = _l("Navigation Group Name must be between 3 and 64 characters!");
		}

		if (!empty($data['links'])) {
			foreach ($data['links'] as $key => $link) {
				$this->validateNavigationLink($key, $link);
			}
		}

		return empty($this->error);
	}

	public function validateDeleteNavigationGroup()
	{
		return empty($this->error);
	}

	public function validateNavigationLink($navigation_id, $link)
	{
		if (!empty($link['name'])) {
			$link_name = $link['name'];
		} elseif (!empty($link['display_name'])) {
			$link_name = $link['display_name'];
		} else {
			$link_name = $navigation_id;
		}

		if (empty($link_name) || !$this->validation->text($link_name, 1, 45)) {
			$this->error["links[$navigation_id][name]"] = _l("The name for the link %s must be between 1 and 45 characters!", $link_name);
		}

		if (empty($link['display_name']) || !$this->validation->text($link['display_name'], 1, 255)) {
			$this->error["links[$navigation_id][display_name]"] = _l("The Display Name for the link %s must be between 1 and 255 characters!", $link_name);
		}

		return empty($this->error);
	}

	public function validateDeleteNavigationLink()
	{
		return empty($this->error);
	}
}
