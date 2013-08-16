<?php
class Admin_Model_Design_Navigation extends Model
{
	public function addNavigationGroup($data)
	{
		$navigation_group_id = $this->insert("navigation_group", $data);

		//Add Stores
		foreach ($data['stores'] as $store_id) {
			$store_data = array(
				'navigation_group_id' => $navigation_group_id,
				'store_id' => $store_id
			);

			$this->insert("navigation_store", $store_data);
		}

		//Add Links
		$parent = array();
		$sort_index = 0;

		foreach ($data['links'] as $link_id => $link) {
			$link['navigation_group_id'] = $navigation_group_id;

			if (!isset($link['sort_order'])) {
				$link['sort_order'] = $sort_index++;
			}

			if ($link['parent_id']) {
				if (!isset($parent[$link['parent_id']])) {
					$msg = "ModelDesignNavigation::addNavigationGroup(): There was an error resolving the parent_id, $link[parent_id]!";
					trigger_error($msg);
					$this->mail->send_error_email($msg);

					$this->add_message('error', "There was an error saving Navigation group to the database! The Web Admin has been notified. Please try again later");
				}
				else {
					$link['parent_id'] = $parent[$link['parent_id']];
				}
			}

			$nav_id = $this->insert("navigation", $link);

			$parent[$link_id] = $nav_id;
		}

		$this->cache->delete('navigation');
	}

	public function editNavigationGroup($navigation_group_id, $data)
	{
		$this->update("navigation_group", $data, $navigation_group_id);

		//Update Stores
		if (isset($data['stores'])) {
			$this->delete("navigation_store", array("navigation_group_id" => $navigation_group_id));

			foreach ($data['stores'] as $store_id) {
				$store_data = array(
					'navigation_group_id' => $navigation_group_id,
					'store_id' => $store_id
				);

				$this->insert("navigation_store", $store_data);
			}
		}


		//Update Links
		if (isset($data['links'])) {
			$this->delete("navigation", array("navigation_group_id" => $navigation_group_id));

			$parent = array();
			$sort_index = 0;

			foreach ($data['links'] as $link_id => $link) {
				$link['navigation_group_id'] = $navigation_group_id;

				if (empty($link['sort_order'])) {
					$link['sort_order'] = $sort_index++;
				}

				if ($link['parent_id']) {
					if (!isset($parent[$link['parent_id']])) {
						$msg = "ModelDesignNavigation::addNavigationGroup(): There was an error resolving the parent_id!";
						trigger_error($msg);
						$this->mail->send_error_email($msg);

						$this->add_message('error', "There was an error saving Navigation group to the database! The Web Admin has been notified. Please try again later");
					}
					else {
						$link['parent_id'] = $parent[$link['parent_id']];
					}
				}

				$nav_id = $this->insert("navigation", $link);

				$parent[$link_id] = $nav_id;
			}
		}

		$this->cache->delete('navigation');
	}

	public function deleteNavigationGroup($navigation_group_id)
	{
		$this->delete("navigation_group", $navigation_group_id);

		$this->delete("navigation_store", array("navigation_group_id" => $navigation_group_id));
		$this->delete("navigation", array("navigation_group_id" => $navigation_group_id));

		$this->cache->delete('navigation');
	}

	public function addNavigationLink($navigation_group_id, $link)
	{
		$link['navigation_group_id'] = $navigation_group_id;

		$this->insert("navigation", $link);

		$this->cache->delete('navigation');
	}

	public function deleteNavigationLink($navigation_id)
	{
		$this->delete("navigation", $navigation_id);

		$this->cache->delete('navigation');
	}

	public function getNavigationGroup($navigation_group_id)
	{
		$nav_group = $this->queryRow("SELECT * FROM " . DB_PREFIX . "navigation_group WHERE navigation_group_id = " . (int)$navigation_group_id);

		$nav_group['stores'] = $this->getNavigationGroupStores($navigation_group_id);
		$nav_group['links'] = $this->getNavigationGroupLinks($navigation_group_id);

		return $nav_group;
	}

	public function getNavigationGroups($data = array(), $select = '*', $total = false) {
		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		}
		elseif (!$select) {
			$select = '*';
		}

		//From
		$from = "FROM " . DB_PREFIX . "navigation_group ng";

		//Where
		$where = "WHERE 1";

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
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}

		//The Query
		$query = "SELECT $select $from $where $order $limit";

		//Execute
		$result = $this->query($query);

		//Process Results
		if ($total) {
			return $result->row['total'];
		}
		else {
			foreach ($result->rows as $key => &$row) {
				$row['links'] = $this->getNavigationGroupLinks($row['navigation_group_id']);
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
						$link['children'] = &$parent_ref[$link['navigation_id']]['children'];
					}
					else {
						$link['children'] = array();
					}

					$parent_ref[$link['navigation_id']] = &$link;

					if ($link['parent_id']) {
						$parent_ref[$link['parent_id']]['children'][] = &$link;
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
		$result = $this->query("SELECT * FROM " . DB_PREFIX . "navigation WHERE navigation_group_id = '" . (int)$navigation_group_id . "' ORDER BY sort_order ASC");

		return $result->rows;
	}

	public function getNavigationGroupStores($navigation_group_id)
	{
		return $this->queryColumn("SELECT store_id FROM " . DB_PREFIX . "navigation_store WHERE navigation_group_id = " . (int)$navigation_group_id);
	}

	public function getTotalNavigationGroups($data)
	{
		return $this->getNavigationGroups($data, '', true);
	}

	public function reset_admin_navigation_group()
	{
		$links = array(
			'home' => array(
				'display_name'	=> 'Home',
				'name'			=> 'home',
				'title'			=> '',
				'href'			=> 'common/home',
				'query'			=> '',
				'is_route'		=> 1,
				'parent_id'		=> '',
				'sort_order'	=> 0,
				'status'			=> 1,
			),

			'content' => array(
				'display_name'	=> 'Content',
				'name'			=> 'content',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '',
				'sort_order'	=> 1,
				'status'			=> 1,
			),

				'content_blocks' => array(
					'display_name'	=> 'Blocks',
					'name'			=> 'content_blocks',
					'title'			=> '',
					'href'			=> 'block/block',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'content',
					'sort_order'	=> 0,
					'status'			=> 1,
				),

				'content_pages' => array(
					'display_name'	=> 'Pages',
					'name'			=> 'content_pages',
					'title'			=> '',
					'href'			=> 'page/page',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'content',
					'sort_order'	=> 1,
					'status'			=> 1,
				),

				'content_featured_products' => array(
					'display_name'	=> 'Featured Products',
					'name'			=> 'content_featured_products',
					'title'			=> '',
					'href'			=> 'module/featured',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'content',
					'sort_order'	=> 2,
					'status'			=> 1,
				),

				'content_leaderboard' => array(
					'display_name'	=> 'Leaderboard',
					'name'			=> 'content_leaderboard',
					'title'			=> '',
					'href'			=> 'module/page_headers',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'content',
					'sort_order'	=> 3,
					'status'			=> 1,
				),

				'content_newsletter' => array(
					'display_name'	=> 'Newsletter',
					'name'			=> 'content_newsletter',
					'title'			=> '',
					'href'			=> 'mail/newsletter',
					'query'			=> '',
					'is_route'		=> 4,
					'parent_id'		=> 'content',
					'sort_order'	=> 4,
					'status'			=> 1,
				),

			'catalog' => array(
				'display_name'	=> 'Catalog',
				'name'			=> 'catalog',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '',
				'sort_order'	=> 4,
				'status'			=> 1,
			),

				'catalog_attributes' => array(
					'display_name'	=> 'Attribute Groups',
					'name'			=> 'catalog_attributes',
					'title'			=> '',
					'href'			=> 'catalog/attribute_group',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 1,
					'status'			=> 1,
				),

				'catalog_options' => array(
					'display_name'	=> 'Options',
					'name'			=> 'catalog_options',
					'title'			=> '',
					'href'			=> 'catalog/option',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 1,
					'status'			=> 1,
				),

				'catalog_categories' => array(
					'display_name'	=> 'Categories',
					'name'			=> 'catalog_categories',
					'title'			=> '',
					'href'			=> 'catalog/category',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 2,
					'status'			=> 1,
				),

				'catalog_products' => array(
					'display_name'	=> 'Products',
					'name'			=> 'catalog_products',
					'title'			=> '',
					'href'			=> 'catalog/product',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 3,
					'status'			=> 1,
				),

					'catalog_products_insert' => array(
						'display_name'	=> 'Add Product',
						'name'			=> 'catalog_products_insert',
						'title'			=> '',
						'href'			=> 'catalog/product/update',
						'query'			=> '',
						'parent_id'		=> 'catalog_products',
						'sort_order'	=> 0,
						'status'			=> 1,
					),

				'catalog_designers' => array(
					'display_name'	=> 'Designers',
					'name'			=> 'catalog_designers',
					'title'			=> '',
					'href'			=> 'catalog/manufacturer',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 4,
					'status'			=> 1,
				),

				'catalog_downloads' => array(
					'display_name'	=> 'Downloads',
					'name'			=> 'catalog_downloads',
					'title'			=> '',
					'href'			=> 'catalog/download',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 5,
					'status'			=> 1,
				),

				'catalog_reviews' => array(
					'display_name'	=> 'Reviews',
					'name'			=> 'catalog_reviews',
					'title'			=> '',
					'href'			=> 'catalog/review',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 6,
					'status'			=> 1,
				),

				'catalog_information' => array(
					'display_name'	=> 'Information',
					'name'			=> 'catalog_information',
					'title'			=> '',
					'href'			=> 'catalog/information',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'catalog',
					'sort_order'	=> 7,
					'status'			=> 1,
				),

			'sales' => array(
				'display_name'	=> 'Sales',
				'name'			=> 'sales',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '',
				'sort_order'	=> 5,
				'status'			=> 1,
			),

				'sales_affiliates' => array(
					'display_name'	=> 'Affiliates',
					'name'			=> 'sales_affiliates',
					'title'			=> '',
					'href'			=> 'sale/affiliate',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'sales',
					'sort_order'	=> 0,
					'status'			=> 1,
				),

				'sales_coupons' => array(
					'display_name'	=> 'Coupons',
					'name'			=> 'sales_coupons',
					'title'			=> '',
					'href'			=> 'sale/coupon',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'sales',
					'sort_order'	=> 1,
					'status'			=> 1,
				),

				'sales_customers' => array(
					'display_name'	=> 'Customers',
					'name'			=> 'sales_customers',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'sales',
					'sort_order'	=> 2,
					'status'			=> 1,
				),

					'sales_customers_customers' => array(
						'display_name'	=> 'Customers',
						'name'			=> 'sales_customers_customers',
						'title'			=> '',
						'href'			=> 'sale/customer',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'sales_customers',
						'sort_order'	=> 0,
						'status'			=> 1,
					),

					'sales_customers_customer_groups' => array(
						'display_name'	=> 'Customer Groups',
						'name'			=> 'sales_customers_customer_groups',
						'title'			=> '',
						'href'			=> 'sale/customer_group',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'sales_customers',
						'sort_order'	=> 1,
						'status'			=> 1,
					),

					'sales_customers_ip_blacklist' => array(
						'display_name'	=> 'IP Blacklist',
						'name'			=> 'sales_customers_ip_blacklist',
						'title'			=> '',
						'href'			=> 'sale/customer_blacklist',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'sales_customers',
						'sort_order'	=> 2,
						'status'			=> 1,
					),

				'sales_orders' => array(
					'display_name'	=> 'Orders',
					'name'			=> 'sales_orders',
					'title'			=> '',
					'href'			=> 'sale/order',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'sales',
					'sort_order'	=> 3,
					'status'			=> 1,
				),

				'sales_gift_vouchers' => array(
					'display_name'	=> 'Gift Vouchers',
					'name'			=> 'sales_gift_vouchers',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'sales',
					'sort_order'	=> 4,
					'status'			=> 1,
				),

					'sales_gift_vouchers_voucher_themes' => array(
						'display_name'	=> 'Voucher Themes',
						'name'			=> 'sales_gift_vouchers_voucher_themes',
						'title'			=> '',
						'href'			=> 'sale/voucher_theme',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'sales_gift_vouchers',
						'sort_order'	=> 0,
						'status'			=> 1,
					),

					'sales_gift_vouchers_gift_vouchers' => array(
						'display_name'	=> 'Gift Vouchers',
						'name'			=> 'sales_gift_vouchers_gift_vouchers',
						'title'			=> '',
						'href'			=> 'sale/voucher',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'sales_gift_vouchers',
						'sort_order'	=> 1,
						'status'			=> 1,
					),

				'sales_returns' => array(
					'display_name'	=> 'Returns',
					'name'			=> 'sales_returns',
					'title'			=> '',
					'href'			=> 'sale/return',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'sales',
					'sort_order'	=> 5,
					'status'			=> 1,
				),

			'extensions' => array(
				'display_name'	=> 'Extensions',
				'name'			=> 'extensions',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '',
				'sort_order'	=> 6,
				'status'			=> 1,
			),

				'extensions_plugins' => array(
					'display_name'	=> 'Plugins',
					'name'			=> 'extensions_plugins',
					'title'			=> '',
					'href'			=> 'extension/plugin',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'extensions',
					'sort_order'	=> 0,
					'status'			=> 1,
				),

				'extensions_payments' => array(
					'display_name'	=> 'Payments',
					'name'			=> 'extensions_payments',
					'title'			=> '',
					'href'			=> 'extension/payment',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'extensions',
					'sort_order'	=> 1,
					'status'			=> 1,
				),

				'extensions_modules' => array(
					'display_name'	=> 'Modules',
					'name'			=> 'extensions_modules',
					'title'			=> '',
					'href'			=> 'extension/module',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'extensions',
					'sort_order'	=> 2,
					'status'			=> 1,
				),

				'extensions_product_feeds' => array(
					'display_name'	=> 'Product Feeds',
					'name'			=> 'extensions_product_feeds',
					'title'			=> '',
					'href'			=> 'extension/feed',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'extensions',
					'sort_order'	=> 3,
					'status'			=> 1,
				),

				'extensions_order_totals' => array(
					'display_name'	=> 'Order Totals',
					'name'			=> 'extensions_order_totals',
					'title'			=> '',
					'href'			=> 'extension/total',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'extensions',
					'sort_order'	=> 4,
					'status'			=> 1,
				),

				'extensions_shipping' => array(
					'display_name'	=> 'Shipping',
					'name'			=> 'extensions_shipping',
					'title'			=> '',
					'href'			=> 'extension/shipping',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'extensions',
					'sort_order'	=> 5,
					'status'			=> 1,
				),

			'users' => array(
				'display_name'	=> 'Users',
				'name'			=> 'users',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '',
				'sort_order'	=> 7,
				'status'			=> 1,
			),

				'users_users' => array(
					'display_name'	=> 'Users',
					'name'			=> 'users_users',
					'title'			=> '',
					'href'			=> 'user/user',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'users',
					'sort_order'	=> 0,
					'status'			=> 1,
				),

				'users_user_groups' => array(
					'display_name'	=> 'User Groups',
					'name'			=> 'users_user_groups',
					'title'			=> '',
					'href'			=> 'user/user_permission',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'users',
					'sort_order'	=> 1,
					'status'			=> 1,
				),

			'reports' => array(
				'display_name'	=> 'Reports',
				'name'			=> 'reports',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '',
				'sort_order'	=> 8,
				'status'			=> 1,
			),

				'reports_affiliates' => array(
					'display_name'	=> 'Affiliates',
					'name'			=> 'reports_affiliates',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'reports',
					'sort_order'	=> 0,
					'status'			=> 1,
				),

					'reports_affiliates_commission' => array(
						'display_name'	=> 'Commission',
						'name'			=> 'reports_affiliates_commission',
						'title'			=> '',
						'href'			=> 'report/affiliate_commission',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'reports_affiliates',
						'sort_order'	=> 0,
						'status'			=> 1,
					),

				'reports_customers' => array(
					'display_name'	=> 'Customers',
					'name'			=> 'reports_customers',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'reports',
					'sort_order'	=> 1,
					'status'			=> 1,
				),

					'reports_customers_credit' => array(
						'display_name'	=> 'Credit',
						'name'			=> 'reports_customers_credit',
						'title'			=> '',
						'href'			=> 'report/customer_credit',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'reports_customers',
						'sort_order'	=> 0,
						'status'			=> 1,
					),

					'reports_customers_reward_points' => array(
						'display_name'	=> 'Reward Points',
						'name'			=> 'reports_customers_reward_points',
						'title'			=> '',
						'href'			=> 'report/customer_reward',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'reports_customers',
						'sort_order'	=> 1,
						'status'			=> 1,
					),

					'reports_customers_orders' => array(
						'display_name'	=> 'Orders',
						'name'			=> 'reports_customers_orders',
						'title'			=> '',
						'href'			=> 'report/customer_order',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'reports_customers',
						'sort_order'	=> 2,
						'status'			=> 1,
					),

				'reports_products' => array(
					'display_name'	=> 'Products',
					'name'			=> 'reports_products',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'reports',
					'sort_order'	=> 2,
					'status'			=> 1,
				),

					'reports_products_purchased' => array(
						'display_name'	=> 'Purchased',
						'name'			=> 'reports_products_purchased',
						'title'			=> '',
						'href'			=> 'report/product_purchased',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'reports_products',
						'sort_order'	=> 0,
						'status'			=> 1,
					),

					'reports_products_viewed' => array(
						'display_name'	=> 'Viewed',
						'name'			=> 'reports_products_viewed',
						'title'			=> '',
						'href'			=> 'report/product_viewed',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'reports_products',
						'sort_order'	=> 1,
						'status'			=> 1,
					),

				'reports_sales' => array(
					'display_name'	=> 'Sales',
					'name'			=> 'reports_sales',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'reports',
					'sort_order'	=> 3,
					'status'			=> 1,
				),

					'reports_sales_orders' => array(
						'display_name'	=> 'Orders',
						'name'			=> 'reports_sales_orders',
						'title'			=> '',
						'href'			=> 'report/sale_order',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'reports_sales',
						'sort_order'	=> 0,
						'status'			=> 1,
					),

					'reports_sales_tax' => array(
						'display_name'	=> 'Tax',
						'name'			=> 'reports_sales_tax',
						'title'			=> '',
						'href'			=> 'report/sale_tax',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'reports_sales',
						'sort_order'	=> 1,
						'status'			=> 1,
					),

					'reports_sales_coupons' => array(
						'display_name'	=> 'Coupons',
						'name'			=> 'reports_sales_coupons',
						'title'			=> '',
						'href'			=> 'report/sale_coupon',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'reports_sales',
						'sort_order'	=> 2,
						'status'			=> 1,
					),

					'reports_sales_shipping' => array(
						'display_name'	=> 'Shipping',
						'name'			=> 'reports_sales_shipping',
						'title'			=> '',
						'href'			=> 'report/sale_shipping',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'reports_sales',
						'sort_order'	=> 3,
						'status'			=> 1,
					),

					'reports_sales_returns' => array(
						'display_name'	=> 'Returns',
						'name'			=> 'reports_sales_returns',
						'title'			=> '',
						'href'			=> 'report/sale_return',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'reports_sales',
						'sort_order'	=> 4,
						'status'			=> 1,
					),

			'system' => array(
				'display_name'	=> 'System',
				'name'			=> 'system',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '',
				'sort_order'	=> 9,
				'status'			=> 1,
			),

				'system_settings' => array(
					'display_name'	=> 'Settings',
					'name'			=> 'system_settings',
					'title'			=> '',
					'href'			=> 'setting/store',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 0,
					'status'			=> 1,
				),

					'system_settings_general' => array(
							'display_name'	=> 'General',
							'name'			=> 'system_settings_general',
							'title'			=> '',
							'href'			=> 'setting/setting',
							'query'			=> '',
							'is_route'		=> 1,
							'parent_id'		=> 'system_settings',
							'sort_order'	=> 0,
							'status'			=> 1,
						),

					'system_settings_update' => array(
							'display_name'	=> 'Update',
							'name'			=> 'system_settings_update',
							'title'			=> '',
							'href'			=> 'setting/update',
							'query'			=> '',
							'is_route'		=> 1,
							'parent_id'		=> 'system_settings',
							'sort_order'	=> 1,
							'status'			=> 1,
						),

					'system_settings_orders' => array(
							'display_name'	=> 'Orders',
							'name'			=> 'system_settings_orders',
							'title'			=> '',
							'href'			=> '',
							'query'			=> '',
							'is_route'		=> 0,
							'parent_id'		=> 'system_settings',
							'sort_order'	=> 2,
							'status'			=> 1,
						),

							'system_settings_orders_order_statuses' => array(
								'display_name'	=> 'Order Statuses',
								'name'			=> 'system_settings_orders_order_statuses',
								'title'			=> '',
								'href'			=> 'setting/order_status',
								'query'			=> '',
								'is_route'		=> 1,
								'parent_id'		=> 'system_settings_orders',
								'sort_order'	=> 0,
								'status'			=> 1,
							),

					'system_settings_policies' => array(
							'display_name'	=> 'Policies',
							'name'			=> 'system_settings_policies',
							'title'			=> '',
							'href'			=> '',
							'query'			=> '',
							'is_route'		=> 0,
							'parent_id'		=> 'system_settings',
							'sort_order'	=> 3,
							'status'			=> 1,
						),

						'system_settings_policies_shipping_policies' => array(
								'display_name'	=> 'Shipping Policies',
								'name'			=> 'system_settings_policies_shipping_policies',
								'title'			=> '',
								'href'			=> 'setting/shipping_policy',
								'query'			=> '',
								'is_route'		=> 1,
								'parent_id'		=> 'system_settings_policies',
								'sort_order'	=> 0,
								'status'			=> 1,
							),

						'system_settings_policies_return_policies' => array(
								'display_name'	=> 'Return Policies',
								'name'			=> 'system_settings_policies_return_policies',
								'title'			=> '',
								'href'			=> 'setting/return_policy',
								'query'			=> '',
								'is_route'		=> 1,
								'parent_id'		=> 'system_settings_policies',
								'sort_order'	=> 1,
								'status'			=> 1,
							),
					'system_settings_returns' => array(
							'display_name'	=> 'Returns',
							'name'			=> 'system_settings_returns',
							'title'			=> '',
							'href'			=> '',
							'query'			=> '',
							'is_route'		=> 0,
							'parent_id'		=> 'system_settings',
							'sort_order'	=> 4,
							'status'			=> 1,
						),

							'system_settings_returns_return_reasons' => array(
								'display_name'	=> 'Return Reasons',
								'name'			=> 'system_settings_returns_return_reasons',
								'title'			=> '',
								'href'			=> 'setting/return_reason',
								'query'			=> '',
								'is_route'		=> 1,
								'parent_id'		=> 'system_settings_returns',
								'sort_order'	=> 0,
								'status'			=> 1,
							),

							'system_settings_returns_return_actions' => array(
								'display_name'	=> 'Return Actions',
								'name'			=> 'system_settings_returns_return_actions',
								'title'			=> '',
								'href'			=> 'setting/return_action',
								'query'			=> '',
								'is_route'		=> 1,
								'parent_id'		=> 'system_settings_returns',
								'sort_order'	=> 1,
								'status'			=> 1,
							),

							'system_settings_returns_return_statuses' => array(
								'display_name'	=> 'Return Statuses',
								'name'			=> 'system_settings_returns_return_statuses',
								'title'			=> '',
								'href'			=> 'setting/return_status',
								'query'			=> '',
								'is_route'		=> 1,
								'parent_id'		=> 'system_settings_returns',
								'sort_order'	=> 2,
								'status'			=> 1,
							),

					'system_settings_controller_overrides' => array(
								'display_name'	=> 'Controller Overrides',
								'name'			=> 'system_settings_controller_overrides',
								'title'			=> '',
								'href'			=> 'setting/controller_override',
								'query'			=> '',
								'is_route'		=> 0,
								'parent_id'		=> 'system_settings',
								'sort_order'	=> 5,
								'status'			=> 1,
							),

				'system_mail' => array(
					'display_name'	=> 'Mail',
					'name'			=> 'system_mail',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'system',
					'sort_order'	=> 1,
					'status'			=> 1,
				),

					'system_mail_send_email' => array(
						'display_name'	=> 'Send Email',
						'name'			=> 'system_mail_send_email',
						'title'			=> '',
						'href'			=> 'mail/send_email',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_mail',
						'sort_order'	=> 0,
						'status'			=> 1,
					),

					'system_mail_mail_messages' => array(
						'display_name'	=> 'Mail Messages',
						'name'			=> 'system_mail_mail_messages',
						'title'			=> '',
						'href'			=> 'mail/messages',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_mail',
						'sort_order'	=> 1,
						'status'			=> 1,
					),

					'system_mail_error' => array(
						'display_name'	=> 'Failed Messages',
						'name'			=> 'system_mail_error',
						'title'			=> '',
						'href'			=> 'mail/error',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_mail',
						'sort_order'	=> 2,
						'status'			=> 1,
					),

				'system_url_alias' => array(
					'display_name'	=> 'URL Alias',
					'name'			=> 'system_url_alias',
					'title'			=> '',
					'href'			=> 'setting/url_alias',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 2,
					'status'			=> 1,
				),

				'system_db_rules' => array(
					'display_name'	=> 'DB Rules',
					'name'			=> 'system_db_rules',
					'title'			=> '',
					'href'			=> 'setting/db_rules',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 3,
					'status'			=> 1,
				),

				'system_cron' => array(
					'display_name'	=> 'Cron',
					'name'			=> 'system_cron',
					'title'			=> '',
					'href'			=> 'module/cron',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 4,
					'status'			=> 1,
				),

				'system_design' => array(
					'display_name'	=> 'Design',
					'name'			=> 'system_design',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'system',
					'sort_order'	=> 5,
					'status'			=> 1,
				),

					'system_design_banners' => array(
						'display_name'	=> 'Banners',
						'name'			=> 'system_design_banners',
						'title'			=> '',
						'href'			=> 'design/banner',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_design',
						'sort_order'	=> 0,
						'status'			=> 1,
					),

					'system_design_navigation' => array(
						'display_name'	=> 'Navigation',
						'name'			=> 'system_design_navigation',
						'title'			=> '',
						'href'			=> 'design/navigation',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_design',
						'sort_order'	=> 1,
						'status'			=> 1,
					),

					'system_design_layouts' => array(
						'display_name'	=> 'Layouts',
						'name'			=> 'system_design_layouts',
						'title'			=> '',
						'href'			=> 'design/layout',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_design',
						'sort_order'	=> 2,
						'status'			=> 1,
					),

				'system_backup__restore' => array(
					'display_name'	=> 'Backup / Restore',
					'name'			=> 'system_backup__restore',
					'title'			=> '',
					'href'			=> 'tool/backup',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 6,
					'status'			=> 1,
				),

				'system_system_tools' => array(
					'display_name'	=> 'System Tools',
					'name'			=> 'system_system_tools',
					'title'			=> '',
					'href'			=> 'tool/tool',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 7,
					'status'			=> 1,
				),

				'system_error_logs' => array(
					'display_name'	=> 'Error Logs',
					'name'			=> 'system_error_logs',
					'title'			=> '',
					'href'			=> 'tool/error_log',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'system',
					'sort_order'	=> 8,
					'status'			=> 1,
				),

				'system_localisation' => array(
					'display_name'	=> 'Localisation',
					'name'			=> 'system_localisation',
					'title'			=> '',
					'href'			=> '',
					'query'			=> '',
					'is_route'		=> 0,
					'parent_id'		=> 'system',
					'sort_order'	=> 9,
					'status'			=> 1,
				),

					'system_localisation_currencies' => array(
						'display_name'	=> 'Currencies',
						'name'			=> 'system_localisation_currencies',
						'title'			=> '',
						'href'			=> 'localisation/currency',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_localisation',
						'sort_order'	=> 0,
						'status'			=> 1,
					),

					'system_localisation_languages' => array(
						'display_name'	=> 'Languages',
						'name'			=> 'system_localisation_languages',
						'title'			=> '',
						'href'			=> 'localisation/language',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_localisation',
						'sort_order'	=> 1,
						'status'			=> 1,
					),

					'system_localisation_taxes' => array(
						'display_name'	=> 'Taxes',
						'name'			=> 'system_localisation_taxes',
						'title'			=> '',
						'href'			=> '',
						'query'			=> '',
						'is_route'		=> 0,
						'parent_id'		=> 'system_localisation',
						'sort_order'	=> 3,
						'status'			=> 1,
					),

						'system_localisation_taxes_tax_classes' => array(
							'display_name'	=> 'Tax Classes',
							'name'			=> 'system_localisation_taxes_tax_classes',
							'title'			=> '',
							'href'			=> 'localisation/tax_class',
							'query'			=> '',
							'is_route'		=> 1,
							'parent_id'		=> 'system_localisation_taxes',
							'sort_order'	=> 0,
							'status'			=> 1,
						),

						'system_localisation_taxes_tax_rates' => array(
							'display_name'	=> 'Tax Rates',
							'name'			=> 'system_localisation_taxes_tax_rates',
							'title'			=> '',
							'href'			=> 'localisation/tax_rate',
							'query'			=> '',
							'is_route'		=> 1,
							'parent_id'		=> 'system_localisation_taxes',
							'sort_order'	=> 1,
							'status'			=> 1,
						),

					'system_localisation_zones' => array(
						'display_name'	=> 'Zones',
						'name'			=> 'system_localisation_zones',
						'title'			=> '',
						'href'			=> 'localisation/zone',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_localisation',
						'sort_order'	=> 4,
						'status'			=> 1,
					),

					'system_localisation_countries' => array(
						'display_name'	=> 'Countries',
						'name'			=> 'system_localisation_countries',
						'title'			=> '',
						'href'			=> 'localisation/country',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_localisation',
						'sort_order'	=> 5,
						'status'			=> 1,
					),

					'system_localisation_geo_zones' => array(
						'display_name'	=> 'Geo Zones',
						'name'			=> 'system_localisation_geo_zones',
						'title'			=> '',
						'href'			=> 'localisation/geo_zone',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_localisation',
						'sort_order'	=> 6,
						'status'			=> 1,
					),

					'system_localisation_stock_statuses' => array(
						'display_name'	=> 'Stock Statuses',
						'name'			=> 'system_localisation_stock_statuses',
						'title'			=> '',
						'href'			=> 'localisation/stock_status',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_localisation',
						'sort_order'	=> 7,
						'status'			=> 1,
					),

					'system_localisation_order_statuses' => array(
						'display_name'	=> 'Order Statuses',
						'name'			=> 'system_localisation_order_statuses',
						'title'			=> '',
						'href'			=> 'localisation/order_status',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_localisation',
						'sort_order'	=> 8,
						'status'			=> 1,
					),

					'system_localisation_length_classes' => array(
						'display_name'	=> 'Length Classes',
						'name'			=> 'system_localisation_length_classes',
						'title'			=> '',
						'href'			=> 'localisation/length_class',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_localisation',
						'sort_order'	=> 9,
						'status'			=> 1,
					),

					'system_localisation_weight_classes' => array(
						'display_name'	=> 'Weight Classes',
						'name'			=> 'system_localisation_weight_classes',
						'title'			=> '',
						'href'			=> 'localisation/weight_class',
						'query'			=> '',
						'is_route'		=> 1,
						'parent_id'		=> 'system_localisation',
						'sort_order'	=> 10,
						'status'			=> 1,
					),


			'help' => array(
				'display_name'	=> 'Help',
				'name'			=> 'help',
				'title'			=> '',
				'href'			=> '',
				'query'			=> '',
				'is_route'		=> 0,
				'parent_id'		=> '',
				'sort_order'	=> 10,
				'status'			=> 1,
			),

				'help_documentation' => array(
					'display_name'	=> 'Documentation',
					'name'			=> 'help_documentation',
					'title'			=> '',
					'href'			=> 'help/documentation',
					'query'			=> '',
					'is_route'		=> 1,
					'parent_id'		=> 'help',
					'sort_order'	=> 0,
					'status'			=> 1,
				),
		);

		$result = $this->query("SELECT navigation_group_id FROM " . DB_PREFIX . "navigation_group WHERE name = 'admin'");

		if ($result->num_rows) {
			$this->deleteNavigationGroup($result->row['navigation_group_id']);
		}

		$data = array(
			'name' => 'admin',
			'status' => 1,
			'stores' => array(-1),
			'links' => $links
		);

		$this->addNavigationGroup($data);
	}
}
