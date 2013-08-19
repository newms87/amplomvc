<?php
class Catalog_Model_Catalog_Product extends Model
{
	public function getProduct($product_id, $ignore_status = false)
	{
		$product_id        = (int)$product_id;
		$language_id       = $this->config->get('config_language_id');
		$store_id          = (int)$this->config->get('config_store_id');
		$customer_group_id = $this->customer->getCustomerGroupId();

		$product = $this->cache->get("product.$product_id.$language_id.$customer_group_id.$store_id");

		//Validate Product time constraints to allow for caching
		if ($product) {
			$current_datetime = $this->date->now();

			if ($product['date_available'] > $current_datetime || $product['date_expires'] <= $current_datetime) {
				$product = false;
			}
		}

		if (!$product) {
			$discount     = "(SELECT price FROM " . DB_PREFIX . "product_discount pdc WHERE pdc.product_id = p.product_id AND pdc.customer_group_id ='$customer_group_id' AND pdc.quantity >= '0' AND ((pdc.date_start = '0000-00-00' OR pdc.date_start < NOW()) AND (pdc.date_end = '0000-00-00' OR pdc.date_end > NOW())) ORDER BY pdc.priority ASC, pdc.price ASC LIMIT 1) AS discount";
			$special      = "(SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '$customer_group_id' AND (ps.date_start <= NOW() AND (ps.date_end = '" . DATETIME_ZERO . "' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";
			$reward       = "(SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '$customer_group_id') AS reward";
			$stock_status = "(SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '$language_id') AS stock_status";
			$weight_class = "(SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '$language_id') AS weight_class";
			$length_class = "(SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '$language_id') AS length_class";
			$rating       = "(SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating";
			$reviews      = "(SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews";
			$template     = "(SELECT template FROM " . DB_PREFIX . "product_template pt WHERE pt.product_id = p.product_id AND pt.theme = '" . $this->config->get('config_theme') . "' AND store_id = '$store_id') as template";

			$manufacturer = DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)";
			$category     = DB_PREFIX . "product_to_category p2c ON (p2c.product_id=p.product_id)";
			$store        = DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id AND p2s.store_id='$store_id')";

			$query =
				"SELECT p.*, p2c.category_id, p2s.*, $special, p.image, m.name AS manufacturer, m.keyword, m.status as manufacturer_status, $discount, $reward, $stock_status, $weight_class, $length_class, $rating, $reviews, $template, p.sort_order " .
				" FROM " . DB_PREFIX . "product p LEFT JOIN $category JOIN $store LEFT JOIN $manufacturer";

			if ($ignore_status) {
				$query .= " WHERE p.product_id = '$product_id'";
			} else {
				$query .= " WHERE p.product_id='$product_id' AND p.status = '1' AND (m.manufacturer_id IS NULL OR m.status='1') AND (p.date_available <= NOW() OR p.date_available = '" . DATETIME_ZERO . "') AND (p.date_expires > NOW() OR p.date_expires = '" . DATETIME_ZERO . "')";
			}


			$product = $this->queryRow($query);

			if (!empty($product)) {
				$product['name']        = html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8');
				$product['price']       = ($product['discount'] ? $product['discount'] : $product['price']);
				$product['rating']      = (int)$product['rating'];
				$product['teaser']      = html_entity_decode($product['teaser'], ENT_QUOTES, 'UTF-8');
				$product['description'] = html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8');
				$product['information'] = html_entity_decode($product['information'], ENT_QUOTES, 'UTF-8');

				$this->translation->translate('product', $product_id, $product);
			}

			$this->cache->set("product.$product_id.$language_id.$customer_group_id.$store_id", $product);
		}

		return $product;
	}

	public function getProductInfo($product_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
	}

	public function getProductName($product_id)
	{
		return $this->queryVar("SELECT name FROM " . DB_PREFIX . "product WHERE product_id='" . (int)$product_id . "'");
	}

	public function getProducts($data = array(), $select = '*', $total = false)
	{
		$store_id    = (int)$this->config->get('config_store_id');

		/* TODO:
		 *
		 * We can Cache by checking the dates in the cache file to invalidate
		 *
		 * The quantity (any other changers?) will delete the cache file on product purchase
		 *
		 * Need to search in translations as well:
		 * if ($language_id !== $this->config->get('config_default_language_id'))
		 *
		if (empty($data['search'])) {
			//unique cache string for this query
			$cache_id = "product.$language_id.$store_id." . md5(http_build_query($data) . ($total?'.total':''));

			$product_data = $this->cache->get($cache_id);
		}
		*/

		if (empty($data['sort'])) {
			$data['sort'] = 'p.sort_order';
		}

		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		} elseif (empty($select)) {
			$select = 'p.product_id';
		}

		//From
		$from = DB_PREFIX . "product p" .
			" LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)" .
			" LEFT JOIN " . DB_PREFIX . "manufacturer m ON (m.manufacturer_id=p.manufacturer_id)";

		//WHERE
		$where =
			"p.status='1' AND p2s.store_id = '$store_id' AND (m.manufacturer_id IS NULL OR m.status='1')" .
			" AND p.date_available <= NOW() AND (p.date_expires > NOW() OR p.date_expires = '" . DATETIME_ZERO . "')";

		//Product IDs
		if (!empty($data['product_ids'])) {
			$where .= " AND p.product_id IN (" . implode(',', $data['product_ids']) . ")";
		}

		//Product Name
		if (!empty($data['name'])) {
			$where .= " AND p.name = '" . $this->escape($data['name']) . "'";
		}

		//Product Name Search
		if (!empty($data['name_like'])) {
			$where .= " AND p.name like '%" . $this->escape($data['name_like']) . "%'";
		}

		//Product Manufacturer
		if (!empty($data['manufacturer_id'])) {
			$where .= " AND p.manufacturer_id = '" . (int)$data['manufacturer_id'] . "'";
		}

		//Product Attributes
		if (!empty($data['attribute'])) {
			foreach ($data['attribute'] as $attribute) {
				$table_id = 'pa_' . (int)$attribute;

				$from .= " LEFT JOIN " . DB_PREFIX . "product_attribute $table_id ON ($table_id.product_id=p.product_id)";

				$where .= " AND $table_id.attribute_id = '" . (int)$attribute . "'";
			}
		}

		//Has an active special (or sorting by price)
		if (!empty($data['has_special']) || $data['sort'] === 'price') {
			$from .= " LEFT JOIN (SELECT product_id, MIN(price) as special FROM " . DB_PREFIX . "product_special WHERE date_start <= NOW() AND date_end > NOW() GROUP BY product_id) spec ON (spec.product_id = p.product_id)";

			if (!empty($data['has_special'])) {
				$where .= " AND spec.special IS NOT NULL";
			}
		}

		//Reviews / Ratings
		if ($this->config->get('config_review_status') && (isset($data['rating_min']) || isset($data['rating_max']))) {
			$select .= "(SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating";

			if (isset($data['rating_min'])) {
				$where .= 'rating >= ' . (int)$data['rating_min'];
			}

			if (isset($data['rating_max'])) {
				$where .= 'rating <= ' . (int)$data['rating_max'];
			}
		}

		//Product Tag
		if (!empty($data['product_tag'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "product_tag pt ON (p.product_id = pt.product_id)";
			$from .= " LEFT JOIN " . DB_PREFIX . "tag t ON (pt.tag_id=t.tag_id)";

			$where .= " AND LCASE(t.text) = '" . $this->escape(strtolower(trim($data['product_tag']))) . "'";
		}

		//Product Related
		if (!empty($data['related_ids'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "product_related pr ON (pr.product_id=p.product_id)";

			$where .= " AND pr.related_id IN (" . implode(',', $data['related_ids']) . ")";
		}

		//Product Categories
		if (!empty($data['category_ids']) || $data['sort'] === 'c.sort_order') {
			$from .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";

			if ($data['sort'] === 'c.sort_order') {
				$from .= " LEFT JOIN " . DB_PREFIX . "category c ON (c.category_id=p2c.category_id)";
			}

			if (!empty($data['category_ids'])) {
				$where .= " AND p2c.category_id IN (" . implode(',', $data['category_ids']) . ")";
			}
		}

		if (!empty($data['search'])) {
			//TODO: How do we handle the search query?!
			$terms = explode(' ', str_replace(',', ' ', $data['search']));

			$where .= ' AND (';

			$or = '';
			foreach ($terms as $term) {
				$where .= $or . "p.description like '%$term%'";
				if (!$or) {
					$or = ' OR ';
				}
			}

			$where .= ')';
		}

		//Group By, Order By and Limit
		if (!$total) {
			$group_by = " GROUP BY p.product_id";

			if ($data['sort'] === 'price') {
				$ord   = ((!empty($data['order']) && strtoupper($data['order']) === 'DESC') ? 'DESC' : 'ASC');
				$order = "ORDER BY if(special IS NULL, p.price, special) $ord";
			} else {
				$order = $this->extract_order($data);
			}

			$limit = $this->extract_limit($data);
		} else {
			$group_by = '';
			$order    = '';
			$limit    = '';
		}

		//The Query
		$query = "SELECT $select  FROM $from WHERE $where $group_by $order $limit";

		$result = $this->query($query);

		//Process Results
		if ($total) {
			return $result->row['total'];
		}

		$product_data = array();

		foreach ($result->rows as $row) {
			$product = $this->getProduct($row['product_id']);

			if ($product) {
				$product_data[$row['product_id']] = $product;
			}
		}

		return $product_data;
	}

	public function getProductSuggestions($product, $limit = 4)
	{
		if (!is_array($product)) {
			$product = $this->getProduct($product);
		}

		$filter = array(
			'category_ids' => array($product['category_id']),
			'limit'        => $limit,
			'sort'         => 'RAND()',
		);

		$suggestions = $this->getProducts($filter);

		return $suggestions;
	}

	public function getProductAttributes($product_id)
	{
		$query =
			"SELECT ag.* FROM " . DB_PREFIX . "product_attribute pa" .
			" LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id)" .
			" LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id=ag.attribute_group_id)" .
			" WHERE pa.product_id = '" . (int)$product_id . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, ag.name";

		$attribute_groups = $this->queryRows($query);

		if (!empty($attribute_groups)) {
			$this->translation->translate_all('attribute_group', 'attribute_group_id', $attribute_groups);

			foreach ($attribute_groups as &$attribute_group) {
				$query =
					"SELECT a.*, pa.text FROM " . DB_PREFIX . "product_attribute pa" .
					" LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id)" .
					" WHERE pa.product_id = '" . (int)$product_id . "' AND a.attribute_group_id = '" . (int)$attribute_group['attribute_group_id'] . "' ORDER BY a.sort_order, a.name";

				$attributes = $this->queryRows($query);

				$this->translation->translate_all('attribute', 'attribute_id', $attributes);

				$attribute_group['attributes'] = $attributes;
			}
		}

		return $attribute_groups;
	}

	public function getProductOption($product_id, $product_option_id)
	{
		$product_option = $this->queryRow("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_id = " . (int)$product_id . " AND product_option_id = " . (int)$product_option_id);

		if ($product_option) {
			$product_option['product_option_values'] = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = " . (int)$product_option_id);
		}

		return $product_option;
	}

	public function getProductOptionValue($product_id, $product_option_id, $product_option_value_id)
	{
		$product_option_value = $this->queryRow("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_id = " . (int)$product_id . " AND product_option_id = " . (int)$product_option_id . " AND product_option_value_id = " . (int)$product_option_value_id);

		$this->translation->translate('product_option_value', $product_option_value_id, $product_option_value);

		return $product_option_value;
	}

	public function getProductOptions($product_id)
	{
		$product_options = $this->cache->get("product.$product_id.options");

		if (is_null($product_options)) {
			$product_options = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_id = " . (int)$product_id . " ORDER BY sort_order ASC");

			$restrictions = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_option_value_restriction WHERE product_id = " . (int)$product_id . " AND quantity > 1");

			foreach ($product_options as &$product_option) {
				$product_option_value_list = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = " . (int)$product_option['product_option_id'] . " ORDER BY sort_order ASC");

				$product_option_values = array();

				foreach ($product_option_value_list as $product_option_value) {
					$product_option_values[$product_option_value['product_option_value_id']]                 = $product_option_value;
					$product_option_values[$product_option_value['product_option_value_id']]['restrictions'] = array_search_key('product_option_value_id', $product_option_value['product_option_value_id'], $restrictions);
				}

				$product_option['product_option_values'] = $product_option_values;
			}
			unset($product_option);

			$this->cache->set("product.$product_id.options", $product_options);
		}

		return $product_options;
	}

	public function getFilteredProductOptions($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		} elseif (empty($select)) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "product_option po";

		//Where
		$where = "1";

		//Product IDs
		if (!empty($data['product_option_ids'])) {
			$where .= " AND po.product_option_id IN (" . implode(',', $data['product_option_ids']) . ")";
		}

		//Product Name Search
		if (!empty($data['name'])) {
			$where .= " AND po.name like '%" . $this->escape($data['name']) . "%'";
		}

		//Group By, Order By and Limit
		if (!$total) {
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}

		//The Query
		$query = "SELECT $select  FROM $from WHERE $where $order $limit";

		$result = $this->query($query);

		//Process Results
		if ($total) {
			return $result->row['total'];
		}

		return $result->rows;
	}

	public function getProductDiscounts($product_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = " . (int)$product_id . " AND customer_group_id = " . (int)$this->customer->getCustomerGroupId() . " AND quantity > 0 AND (date_start <= NOW() AND (date_end = '" . DATETIME_ZERO . "' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");
	}

	public function getProductSpecialPrice($product_id)
	{
		return $this->queryVar("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = " . (int)$product_id . " AND customer_group_id = " . (int)$customer_group_id . " AND (date_start <= NOW() AND (date_end = '" . DATETIME_ZERO . "' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
	}

	public function getProductImages($product_id)
	{
		return $this->queryColumn("SELECT image FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");
	}

	public function getProductDownloads($product_id)
	{
		$downloads = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) WHERE p2d.product_id = " . (int)$product_id);

		$this->translation->translate_all('download', 'download_id', $downloads);

		return $downloads;
	}

	public function getProdutReward($product_id)
	{
		return (int)$this->queryVar("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = " . (int)$product_id . " AND customer_group_id = " . (int)$this->customer->getCustomerGroupId());
	}

	public function getProductRelated($product_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.product_id = " . (int)$product_id . " AND p.status = 1 AND p.date_available <= NOW() AND p2s.store_id = " . (int)$this->config->get('config_store_id'));
	}

	public function getProductTags($product_id)
	{
		return $this->queryRows("SELECT t.* FROM " . DB_PREFIX . "product_tag pt LEFT JOIN " . DB_PREFIX . "tag t ON (pt.tag_id=t.tag_id) WHERE product_id = " . (int)$product_id);
	}

	public function getProductLayoutId($product_id)
	{
		return $this->queryVar("SELECT layout_id FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
	}

	public function getCategories($product_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = " . (int)$product_id);
	}

	public function getAttributes($data = array(), $select = '', $total = false)
	{
		$language_id = $this->config->get('config_language_id');

		$cache_id = "attributes." . md5(serialize($data)) . ($total ? 'total' : "$select.$language_id");

		$attributes = $this->cache->get($cache_id);

		if (is_null($attributes)) {
			//Select
			if ($total) {
				$select = 'COUNT(*) as total';
			} elseif (empty($select)) {
				$select = '*';
			}

			//From
			$from = DB_PREFIX . "attribute a";

			//Where
			$where = "1";

			if (!empty($data['attribute_group_ids'])) {
				$where .= " AND a.attribute_group_id IN (" . implode(',', $data['attribute_group_ids']) . ")";
			}

			//Attribute Name Search
			if (!empty($data['name'])) {
				$where .= " AND LCASE(a.name) like '%" . $this->escape(strtolower($data['name'])) . "%'";
			}

			//This is a specialty function for advanced attribute selection
			//We resolve the category_ids by finding the products in the category list and grab all associated attributes
			if (!empty($data['category_ids'])) {
				$where .= " AND a.attribute_group_id IN (" .
					"SELECT pa.product_attribute FROM " . DB_PREFIX . "product_attribute pa" .
					" LEFT JOIN " . DB_PREFIX . "product_category pc ON (pc.product_id=pa.product_id)" .
					" WHERE pc.category_id IN (" . implode(',', $data['category_ids']) . ")" .
				")";
			}

			//Group By, Order By and Limit
			if (!$total) {
				$order = $this->extract_order($data);
				$limit = $this->extract_limit($data);
			} else {
				$order = 'ORDER BY name ASC';
				$limit = '';
			}

			//The Query
			$query = "SELECT $select FROM $from WHERE $where $order $limit";

			$result = $this->query($query);

			//Process Results
			if ($total) {
				$attributes = $result->row['total'];
			} else {
				$attributes = $result->rows;

				$this->translation->translate_all('attribute', 'attribute_id', $attributes);
			}
		}

		$this->cache->set($cache_id, $attributes);

		return $attributes;
	}

	public function getClassTemplate($product_class_id)
	{
		$product_classes = $this->cache->get('product_classes');

		if (is_null($product_classes)) {
			$product_classes = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_class");

			foreach ($product_classes as &$product_class) {
				$product_class['front_template'] = unserialize($product_class['front_template']);
				$product_class['admin_template'] = unserialize($product_class['admin_template']);
				$product_class['defaults']       = unserialize($product_class['defaults']);
			}
			unset($product_class);

			$this->cache->set('product_classes', $product_classes);
		}

		$theme = $this->theme->getTheme();

		$product_class = array_search_key('product_class_id', $product_class_id, $product_classes);

		if (!empty($product_class['front_template'][$theme])) {
			return 'product/' . $product_class['front_template'][$theme];
		}

		return 'product/product';
	}

	public function getTotalProducts($data = array())
	{
		return $this->getProducts($data, '', true);
	}

	public function getTotalProductSpecials()
	{
		$customer_group_id = $this->customer->getCustomerGroupId();

		$result = $this->query("SELECT COUNT(DISTINCT ps.product_id) AS total FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '" . DATETIME_ZERO . "' OR ps.date_start < NOW()) AND (ps.date_end = '" . DATETIME_ZERO . "' OR ps.date_end > NOW()))");

		if (isset($result->row['total'])) {
			return $result->row['total'];
		} else {
			return 0;
		}
	}
}
