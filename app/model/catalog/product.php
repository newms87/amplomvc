<?php
class App_Model_Catalog_Product extends Model
{
	public function addProduct($data)
	{
		$data['date_added'] = $this->date->now();

		$product_id = $this->insert('product', $data);

		//Product Store
		if (!empty($data['product_stores'])) {
			foreach ($data['product_stores'] as $store_id) {
				$values = array(
					'store_id'   => $store_id,
					'product_id' => $product_id
				);
				$this->insert('product_to_store', $values);
			}
		}

		//Product Attributes
		if (!empty($data['product_attributes'])) {
			foreach ($data['product_attributes'] as $product_attribute) {
				$product_attribute['product_id'] = $product_id;

				$this->insert('product_attribute', $product_attribute);
			}
		}

		//Product Options
		if (!empty($data['product_options'])) {
			foreach ($data['product_options'] as $product_option) {
				$product_option['product_id'] = $product_id;

				$product_option_id = $this->insert('product_option', $product_option);

				if (!empty($product_option['product_option_values'])) {
					foreach ($product_option['product_option_values'] as $product_option_value) {
						$product_option_value['product_option_id'] = $product_option_id;
						$product_option_value['product_id']        = $product_id;
						$product_option_value['option_id']         = $product_option['option_id'];

						$product_option_value_id = $this->insert('product_option_value', $product_option_value);

						if (!empty($product_option_value['restrictions'])) {
							foreach ($product_option_value['restrictions'] as $restriction) {
								$restriction['product_id']      = $product_id;
								$restriction['option_value_id'] = $product_option_value['option_value_id'];

								$this->insert('product_option_value_restriction', $restriction);
							}
						}
					}
				}
			}
		}

		//Additional Product Images
		if (!empty($data['product_images'])) {
			foreach ($data['product_images'] as $product_image) {
				$product_image['product_id'] = $product_id;

				$this->insert('product_image', $product_image);
			}
		}

		//Product Categories
		if (isset($data['product_categories'])) {
			foreach (array_unique($data['product_categories']) as $category_id) {
				$values = array(
					'product_id'  => $product_id,
					'category_id' => $category_id
				);

				$this->insert('product_to_category', $values);
			}
		}

		//Product Discount
		if (isset($data['product_discounts'])) {
			foreach ($data['product_discounts'] as $product_discount) {
				$product_discount['product_id'] = $product_id;

				$this->insert('product_discount', $product_discount);
			}
		}

		//Product Specials
		if (isset($data['product_specials'])) {
			foreach ($data['product_specials'] as $product_special) {
				$product_special['product_id'] = $product_id;

				$this->insert('product_special', $product_special);
			}
		}

		//Product Downloads
		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $download_id) {
				$values = array(
					'download_id' => $download_id,
					'product_id'  => $product_id
				);

				$this->insert('product_to_download', $values);
			}
		}


		//Product Related
		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$values = array(
					'product_id' => $product_id,
					'related_id' => $related_id
				);

				$this->insert('product_related', $values);
			}
		}

		//Product Reward
		if (isset($data['product_rewards'])) {
			foreach ($data['product_rewards'] as $customer_group_id => $product_reward) {
				$product_reward['product_id']        = $product_id;
				$product_reward['customer_group_id'] = $customer_group_id;

				$this->insert('product_reward', $product_reward);
			}
		}

		//Product Layouts
		if (isset($data['product_layouts'])) {
			foreach ($data['product_layouts'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$layout['product_id'] = $product_id;
					$layout['store_id']   = $store_id;

					$this->insert('product_to_layout', $layout);
				}
			}
		}

		//Product Templates
		if (isset($data['product_templates'])) {
			foreach ($data['product_templates'] as $store_id => $themes) {
				foreach ($themes as $theme => $template) {
					if (empty($template['template'])) {
						continue;
					}

					$template['product_id'] = $product_id;
					$template['theme']      = $theme;
					$template['store_id']   = $store_id;

					$this->insert('product_template', $template);
				}
			}
		}

		if (!empty($data['product_tags'])) {
			$tag_ids = $this->tag->addAll($data['product_tags']);

			foreach ($tag_ids as $tag_id) {
				$product_tag = array(
					'product_id' => $product_id,
					'tag_id'     => $tag_id,
				);

				$this->insert('product_tag', $product_tag);
			}
		}

		if (!empty($data['alias'])) {
			$this->url->setAlias($data['alias'], 'product/product', 'product_id=' . (int)$product_id);
		}

		if (!empty($data['translations'])) {
			$this->translation->setTranslations('product', $product_id, $data['translations']);
		}

		return $product_id;
	}

	public function editProduct($product_id, $data, $strict = false)
	{
		$data['date_modified'] = $this->date->now();

		$this->update('product', $data, $product_id);

		//Product Options
		if (($insert = isset($data['product_options'])) || !$strict) {
			$this->delete('product_option_value_restriction', array('product_id' => $product_id));

			$product_option_ids       = array(0);
			$product_option_value_ids = array(0);

			if ($insert) {
				foreach ($data['product_options'] as $product_option) {
					$product_option['product_id'] = $product_id;

					$product_option_id = $this->update('product_option', $product_option);

					if (!empty($product_option['product_option_values'])) {
						foreach ($product_option['product_option_values'] as $product_option_value) {
							$product_option_value['product_id']        = $product_id;
							$product_option_value['product_option_id'] = $product_option_id;
							$product_option_value['option_id']         = $product_option['option_id'];

							if (empty($product_option_value['default'])) {
								$product_option_value['default'] = 0;
							}

							$product_option_value_id = $this->update('product_option_value', $product_option_value);

							if (!empty($product_option_value['restrictions'])) {
								foreach ($product_option_value['restrictions'] as $restriction) {
									$restriction['product_id']              = $product_id;
									$restriction['product_option_value_id'] = $product_option_value_id;

									$this->insert('product_option_value_restriction', $restriction);
								}
							}

							$product_option_value_ids[] = $product_option_value_id;
						}
					}

					$product_option_ids[] = $product_option_id;
				}
			}

			$this->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '$product_id' AND product_option_id NOT IN (" . implode(',', $product_option_ids) . ")");
			$this->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '$product_id' AND product_option_value_id NOT IN (" . implode(',', $product_option_value_ids) . ")");
		}

		//Product Additional Images
		if (($insert = isset($data['product_images'])) || !$strict) {
			$this->delete('product_image', array('product_id' => $product_id));

			if ($insert) {
				foreach ($data['product_images'] as $product_image) {
					$product_image['product_id'] = $product_id;

					$this->insert('product_image', $product_image);
				}
			}
		}

		//Product Categories
		if (($insert = isset($data['product_categories'])) || !$strict) {
			$this->delete('product_to_category', array('product_id' => $product_id));

			if ($insert) {
				foreach (array_unique($data['product_categories']) as $category_id) {
					$values = array(
						'product_id'  => $product_id,
						'category_id' => $category_id
					);
					$this->insert('product_to_category', $values);
				}
			}
		}

		//Product Stores
		if (($insert = isset($data['product_stores'])) || !$strict) {
			$this->delete('product_to_store', array('product_id' => $product_id));

			if ($insert) {
				foreach ($data['product_stores'] as $store_id) {
					$values = array(
						'store_id'   => $store_id,
						'product_id' => $product_id
					);
					$this->insert('product_to_store', $values);
				}
			}
		}


		//Product Attributes
		if (($insert = isset($data['product_attributes'])) || !$strict) {
			$this->delete('product_attribute', array('product_id' => $product_id));

			if ($insert) {
				$product_attributes = array_unique($data['product_attributes'], SORT_REGULAR);

				foreach ($product_attributes as $product_attribute) {
					$product_attribute['product_id'] = $product_id;

					$this->insert('product_attribute', $product_attribute);
				}
			}
		}

		//Product Discount
		if (($insert = isset($data['product_discounts'])) || !$strict) {
			$this->delete('product_discount', array('product_id' => $product_id));

			if ($insert) {
				foreach ($data['product_discounts'] as $product_discount) {
					$product_discount['product_id'] = $product_id;

					$this->insert('product_discount', $product_discount);
				}
			}
		}

		//Product Special
		if (($insert = isset($data['product_specials'])) || !$strict) {
			$this->delete('product_special', array('product_id' => $product_id));

			if ($insert) {
				foreach ($data['product_specials'] as $product_special) {
					$product_special['product_id'] = $product_id;

					$this->insert('product_special', $product_special);
				}
			}
		}

		//Product Downloads
		if (($insert = isset($data['product_downloads'])) || !$strict) {
			$this->delete('product_to_download', array('product_id' => $product_id));

			if ($insert) {
				foreach ($data['product_downloads'] as $download_id) {
					$values = array(
						'download_id' => $download_id,
						'product_id'  => $product_id
					);
					$this->insert('product_to_download', $values);
				}
			}
		}


		//Product Related
		if (($insert = isset($data['product_related'])) || !$strict) {
			$this->delete('product_related', array('product_id' => $product_id));
			$this->delete('product_related', array('related_id' => $product_id));

			if ($insert) {
				foreach ($data['product_related'] as $related_id) {
					$values = array(
						'product_id' => $product_id,
						'related_id' => $related_id
					);

					$this->insert('product_related', $values);

					//the inverse so the other product is related to this product too!
					$values = array(
						'product_id' => $related_id,
						'related_id' => $product_id
					);

					$this->insert('product_related', $values);
				}
			}
		}

		//Product Reward
		if (($insert = isset($data['product_rewards'])) || !$strict) {
			$this->delete('product_reward', array('product_id' => $product_id));

			if ($insert) {
				foreach ($data['product_rewards'] as $customer_group_id => $product_reward) {
					$product_reward['product_id']        = $product_id;
					$product_reward['customer_group_id'] = $customer_group_id;

					$this->insert('product_reward', $product_reward);
				}
			}
		}

		//Product Layouts
		if (($insert = isset($data['product_layouts'])) || !$strict) {
			$this->delete('product_to_layout', array('product_id' => $product_id));

			if ($insert) {
				foreach ($data['product_layouts'] as $store_id => $layout) {
					if ($layout['layout_id']) {
						$layout['product_id'] = $product_id;
						$layout['store_id']   = $store_id;

						$this->insert('product_to_layout', $layout);
					}
				}
			}
		}

		//Product Templates
		if (($insert = isset($data['product_templates'])) || !$strict) {
			$this->delete('product_template', array('product_id' => $product_id));

			if ($insert) {
				foreach ($data['product_templates'] as $store_id => $themes) {
					foreach ($themes as $theme => $template) {
						if (empty($template['template'])) {
							continue;
						}

						$template['product_id'] = $product_id;
						$template['theme']      = $theme;
						$template['store_id']   = $store_id;

						$this->insert('product_template', $template);
					}
				}
			}
		}

		//Product Tags
		if (($insert = isset($data['product_tags'])) || !$strict) {
			$this->delete('product_tag', array('product_id' => $product_id));

			if ($insert) {
				$tag_ids = $this->tag->addAll($data['product_tags']);

				foreach ($tag_ids as $tag_id) {
					$product_tag = array(
						'product_id' => $product_id,
						'tag_id'     => $tag_id,
					);

					$this->insert('product_tag', $product_tag);
				}
			}
		}

		//Product URL Alias
		if (isset($data['alias'])) {
			$this->url->setAlias($data['alias'], 'product/product', 'product_id=' . (int)$product_id);
		}

		//Translations
		if (!empty($data['translations'])) {
			$this->translation->setTranslations('product', $product_id, $data['translations']);
		}

		$this->cache->delete("product.$product_id");
	}

	public function generateModel($product_id, $name)
	{
		$model = strtoupper($this->url->format($name));

		$count        = 1;
		$unique_model = $model;

		while ($this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "product WHERE model like '" . $this->db->escape($unique_model) . "' AND product_id != " . (int)$product_id)) {
			$unique_model = $model . '-' . $count++;
		}

		return $unique_model;
	}

	public function copyProduct($product_id)
	{
		$product = $this->getProduct($product_id);

		if (!$product) {
			return false;
		}

		$product['model']  = $this->generateModel($product_id, $product['name']);
		$product['alias']  = '';
		$product['status'] = 0;

		$product['product_attributes'] = $this->getProductAttributes($product_id);
		$product['product_discounts']  = $this->getProductDiscounts($product_id);
		$product['product_images']     = $this->getProductImages($product_id);
		$product['product_options']    = $this->getProductOptions($product_id);
		$product['product_related']    = $this->getProductRelated($product_id);
		$product['product_rewards']    = $this->getProductRewards($product_id);
		$product['product_specials']   = $this->getProductSpecials($product_id);
		$product['product_tags']       = $this->getProductTags($product_id);
		$product['product_categories'] = $this->getProductCategories($product_id);
		$product['product_downloads']  = $this->getProductDownloads($product_id);
		$product['product_layouts']    = $this->getProductLayouts($product_id);
		$product['product_templates']  = $this->getProductTemplates($product_id);
		$product['product_stores']     = $this->getProductStores($product_id);

		$name_count = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "product WHERE `name` like '" . $this->escape($product['name']) . "%'");

		$product['name'] .= ' - Copy' . ($name_count > 1 ? "($name_count)" : '');

		$product['translations'] = $this->translation->getTranslations('product', $product_id);

		$this->addProduct($product);

		return true;
	}

	public function deleteProduct($product_id)
	{
		$this->delete('product', array('product_id' => $product_id));
		$this->delete('product_attribute', array('product_id' => $product_id));
		$this->delete('product_discount', array('product_id' => $product_id));
		$this->delete('product_image', array('product_id' => $product_id));
		$this->delete('product_option', array('product_id' => $product_id));
		$this->delete('product_option_value', array('product_id' => $product_id));
		$this->delete('product_option_value_restriction', array('product_id' => $product_id));
		$this->delete('product_related', array('product_id' => $product_id));
		$this->delete('product_related', array('related_id' => $product_id));
		$this->delete('product_reward', array('product_id' => $product_id));
		$this->delete('product_special', array('product_id' => $product_id));
		$this->delete('product_tag', array('product_id' => $product_id));
		$this->delete('product_to_category', array('product_id' => $product_id));
		$this->delete('product_to_download', array('product_id' => $product_id));
		$this->delete('product_to_layout', array('product_id' => $product_id));
		$this->delete('product_template', array('product_id' => $product_id));
		$this->delete('product_to_store', array('product_id' => $product_id));
		$this->delete('review', array('product_id' => $product_id));

		$this->url->removeAlias('product/product', 'product_id=' . (int)$product_id);

		$this->translation->deleteTranslation('product', $product_id);

		$this->cache->delete("product.$product_id");
	}

	public function getProduct($product_id)
	{
		$product = $this->queryRow("SELECT * FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int)$product_id . "'");

		if ($product) {
			$product['alias'] = $this->url->getAlias('product/product', 'product_id=' . (int)$product_id);

			$this->translation->translate('product', $product_id, $product);
		}

		return $product;
	}

	public function getActiveProduct($product_id)
	{
		$product_id        = (int)$product_id;
		$language_id       = option('config_language_id');
		$store_id          = (int)option('store_id');
		$customer_group_id = $this->customer->getCustomerGroupId();

		$product = $this->cache->get("product.$product_id.$language_id.$customer_group_id.$store_id");

		//Validate Product time constraints to allow for caching
		if ($product) {
			if ($this->date->isInFuture($product['date_available']) || $this->date->isInPast($product['date_expires'], false)) {
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
			$template     = "(SELECT template FROM " . DB_PREFIX . "product_template pt WHERE pt.product_id = p.product_id AND pt.theme = '" . option('config_theme') . "' AND store_id = '$store_id') as template";

			$manufacturer = DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)";
			$category     = DB_PREFIX . "product_to_category p2c ON (p2c.product_id=p.product_id)";
			$store        = DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

			$query =
				"SELECT p.*, p2c.category_id, p2s.*, $special, p.image, m.name AS manufacturer, m.keyword, m.status as manufacturer_status, $discount, $reward, $stock_status, $weight_class, $length_class, $rating, $reviews, $template, p.sort_order " .
				" FROM " . DB_PREFIX . "product p LEFT JOIN $category JOIN $store LEFT JOIN $manufacturer" .
				" WHERE p.product_id='$product_id' AND p.status = '1' AND p2s.store_id = $store_id AND (m.manufacturer_id IS NULL OR m.status='1') AND (p.date_available <= NOW() OR p.date_available = '" . DATETIME_ZERO . "') AND (p.date_expires > NOW() OR p.date_expires = '" . DATETIME_ZERO . "')";

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

	public function getProducts($data = array(), $select = '', $total = false)
	{
		if (!isset($data['sort'])) {
			$data['sort'] = 'p.sort_order';
		}

		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		} elseif (!$select) {
			$select = 'p.*';
		}

		//From
		$from = "FROM " . DB_PREFIX . "product p";

		if ($data['sort'] === 'pc.name') {
			$from .= " LEFT JOIN " . DB_PREFIX . "product_class pc ON (p.product_class_id=pc.product_class_id)";
		}

		//Where
		$where = "WHERE 1";

		//Product IDs
		if (!empty($data['product_ids'])) {
			$where .= " AND p.product_id IN (" . implode(',', $data['product_ids']) . ")";
		}

		if (isset($data['name'])) {
			$where .= " AND LCASE(p.name) like '%" . strtolower($this->escape($data['name'])) . "%'";
		}

		if (isset($data['model'])) {
			$where .= " AND LCASE(p.model) like '%" . strtolower($this->escape($data['model'])) . "%'";
		}

		if (isset($data['product_class_id'])) {
			$data['product_class_ids'][] = $data['product_class_id'];
		}

		if (isset($data['product_class_ids'])) {
			$where .= " AND p.product_class_id IN (" . implode(',', $data['product_class_ids']) . ")";
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

		if (!empty($data['manufacturer_id'])) {
			$where .= " AND p.manufacturer_id = '" . (int)$data['manufacturer_id'] . "'";
		}

		if (!empty($data['manufacturer_ids'])) {
			$where .= " AND p.manufacturer_id IN (" . implode(",", $data['manufacturer_ids']) . ")";
		}

		if (isset($data['manufacturer_status'])) {
			$from .= " JOIN " . DB_PREFIX . "manufacturer m ON (m.manufacturer_id = p.manufacturer_id AND m.status = " . (int)$data['manufacturer_status'];
		}

		if ((isset($data['sort']) && $data['sort'] == 'manufacturer_name') || !empty($data['manufacturer_name'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "manufacturer m ON(m.manufacturer_id=p.manufacturer_id)";

			if (!empty($data['manufacturer_name'])) {
				$where .= " AND LCASE(m.name) = '" . strtolower($this->escape($data['m.name'])) . "'";
			}
		}

		if (!empty($data['price']['low'])) {
			$where .= " AND p.price >= '" . (int)$data['price']['low'] . "'";
		}

		if (!empty($data['price']['high'])) {
			$where .= " AND p.price <= '" . (int)$data['price']['high'] . "'";
		}

		if (!empty($data['cost']['low'])) {
			$where .= " AND p.cost >= '" . (int)$data['cost']['low'] . "'";
		}

		if (!empty($data['cost']['high'])) {
			$where .= " AND p.cost <= '" . (int)$data['cost']['high'] . "'";
		}

		if (!empty($data['special']) || (isset($data['sort']) && $data['sort'] == 'special')) {
			$select .= ", (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ((ps.date_start = '" . DATETIME_ZERO . "' OR ps.date_start <= NOW()) AND (ps.date_end = '" . DATETIME_ZERO . "' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";

			if (isset($data['special']['high'])) {
				$where .= " AND special <= " . (int)$data['special']['high'];
			}

			if (isset($data['special']['low'])) {
				$where .= " AND special >= " . (int)$data['special']['low'];
			}
		}

		if (!empty($data['tax_class_id'])) {
			$where .= " AND p.tax_class_id = '" . (int)$data['tax_class_id'] . "'";
		}

		if (!empty($data['stock_status_id'])) {
			$where .= " AND p.stock_status_id = '" . (int)$data['stock_status_id'] . "'";
		}

		if (!empty($data['weight_class_id'])) {
			$where .= " AND p.weight_class_id = '" . (int)$data['weight_class_id'] . "'";
		}

		if (!empty($data['length_class_id'])) {
			$where .= " AND p.length_class_id = '" . (int)$data['length_class_id'] . "'";
		}

		if (!empty($data['return_policies'])) {
			$where .= " AND p.return_policy_id IN (" . implode(',', $data['return_policies']) . ")";
		}

		if (!empty($data['shipping_policies'])) {
			$where .= " AND p.shipping_policy_id IN (" . implode(',', $data['shipping_policies']) . ")";
		}

		if (!empty($data['date_available']['start'])) {
			$where .= " AND (p.date_available = '" . DATETIME_ZERO . "' OR p.date_available >= '" . $this->date->format($data['date_available']['start']) . "')";
		}

		if (!empty($data['date_available']['end'])) {
			$where .= " AND (p.date_available = '" . DATETIME_ZERO . "' OR p.date_available <= '" . $this->date->format($data['date_available']['end']) . "')";
		}

		if (!empty($data['date_expires']['start'])) {
			$where .= " AND (p.date_expires = '" . DATETIME_ZERO . "' OR p.date_expires >= '" . $this->date->format($data['date_expires']['start']) . "')";
		}

		if (!empty($data['date_expires']['end'])) {
			$where .= " AND (p.date_expires = '" . DATETIME_ZERO . "' OR p.date_expires <= '" . $this->date->format($data['date_expires']['end']) . "')";
		}

		if (!empty($data['quantity']['low'])) {
			$where .= " AND p.quantity >= '" . (int)$data['quantity']['low'] . "'";
		}

		if (!empty($data['quantity']['high'])) {
			$where .= " AND p.quantity <= '" . (int)$data['quantity']['high'] . "'";
		}

		if (isset($data['status'])) {
			$where .= " AND p.status = '" . ($data['status'] ? 1 : 0) . "'";
		}

		if (!empty($data['downloads'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "product_to_download p2dl ON (p.product_id=p2dl.product_id)";

			$where .= " AND p2dl.download_id IN (" . implode(',', $data['downloads']) . ")";
		}

		if (!empty($data['attributes'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "product_attribute pa ON (p.product_id=pa.product_id)";

			$where .= " AND pa.attribute_id IN (" . implode(',', $data['attributes']) . ")";
		}

		if (!empty($data['options'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "product_option po ON (p.product_id=po.product_id)";

			$where .= " AND po.option_id IN (" . implode(',', $data['options']) . ")";
		}

		if (!empty($data['layouts'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "product_to_layout p2l ON (p.product_id=p2l.product_id)";

			$where .= " AND p2l.layout_id IN (" . implode(',', $data['layouts']) . ")";
		}

		//Reviews / Ratings
		if (option('config_review_status') && (isset($data['rating_min']) || isset($data['rating_max']))) {
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

		//Group By, Order By and Limit
		if (!$total) {
			$group_by = " GROUP BY p.product_id";

			//enable image sorting if requested and not already installed
			if (!empty($data['sort']) && strpos($data['sort'], '__image_sort__') === 0) {
				if (!$this->db->hasColumn('product', $data['sort'])) {
					$this->extend->enable_image_sorting('product', str_replace('__image_sort__', '', $data['sort']));
				}
			}

			if ($data['sort'] === 'price') {
				$ord   = ((!empty($data['order']) && strtoupper($data['order']) === 'DESC') ? 'DESC' : 'ASC');
				$order = "ORDER BY if(special IS NULL, p.price, special) $ord";
			} else {
				$order = $this->extractOrder($data);
			}

			$limit = $this->extractLimit($data);
		} else {
			$group_by = '';
			$order    = '';
			$limit    = '';
		}

		//The Query
		$query = "SELECT $select $from $where $group_by $order $limit";

		//Execute
		$result = $this->query($query);

		//Process Results
		if ($total) {
			return $result->row['total'];
		}

		return $result->rows;
	}

	public function getActiveProducts($data = array(), $select = '', $total = false)
	{
		$data['status'] = 1;

		$data['manufacturer_status'] = 1;

		$data['date_available'] = array(
			'end' => $this->date->now(),
		);

		$data['date_expires'] = array(
			'start' => $this->date->now(),
		);

		$data['store_ids'] = array(
			option('store_id')
		);

		$product_rows = $this->getProducts($data, $select, $total);

		if ($total) {
			return $product_rows;
		}

		$products = array();

		foreach ($product_rows as $row) {
			$product = $this->getActiveProduct($row['product_id']);

			if ($product) {
				$products[$row['product_id']] = $product;
			}
		}

		return $products;
	}

	public function getProductField($product_id, $field)
	{
		return $this->queryVar("SELECT `" . $this->escape($field) . "` FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product_id);
	}

	public function getProductTranslations($product_id)
	{
		$translate_fields = array(
			'name',
			'teaser',
			'description',
			'information',
			'meta_description',
			'meta_keywords',
		);

		return $this->translation->getTranslations('product', $product_id, $translate_fields);
	}

	public function isEditable($product_id)
	{
		return (int)$this->queryVar("SELECT editable FROM " . DB_PREFIX . "product WHERE product_id='$product_id'");
	}

	public function getProductSuggestions($product, $limit = 4)
	{
		if (!is_array($product)) {
			$product = $this->getActiveProduct($product);
		}

		$filter = array(
			'category_ids' => array($product['category_id']),
			'limit'        => $limit,
			'sort'         => 'RAND()',
		);

		$suggestions = $this->getActiveProducts($filter);

		return $suggestions;
	}

	public function getProductAttributeGroups($product_id)
	{
		$query =
			"SELECT ag.* FROM " . DB_PREFIX . "product_attribute pa" .
			" LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id)" .
			" LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id=ag.attribute_group_id)" .
			" WHERE pa.product_id = '" . (int)$product_id . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, ag.name";

		$attribute_groups = $this->queryRows($query, 'attribute_group_id');

		if (!empty($attribute_groups)) {
			$this->translation->translateAll('attribute_group', 'attribute_group_id', $attribute_groups);

			foreach ($attribute_groups as &$attribute_group) {
				$query =
					"SELECT a.name, pa.* FROM " . DB_PREFIX . "product_attribute pa" .
					" LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id)" .
					" WHERE pa.product_id = '" . (int)$product_id . "' AND a.attribute_group_id = '" . (int)$attribute_group['attribute_group_id'] . "' ORDER BY a.sort_order, a.name";

				$attributes = $this->queryRows($query, 'attribute_id');

				$this->translation->translateAll('attribute', 'attribute_id', $attributes);

				$attribute_group['attributes'] = $attributes;
			}
		}

		return $attribute_groups;
	}

	public function getProductAttributes($product_id)
	{
		$attributes = $this->queryRows("SELECT pa.*, a.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' GROUP BY pa.attribute_id", 'attribute_id');

		$this->translation->translateAll('attribute', 'attribute_id', $attributes);

		return $attributes;
	}

	public function getProductOption($product_id, $product_option_id)
	{
		$product_option = $this->queryRow("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_id = " . (int)$product_id . " AND product_option_id = " . (int)$product_option_id);

		if ($product_option) {
			$product_option['product_option_values'] = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = " . (int)$product_option_id, 'product_option_value_id');
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
		$product_options = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC", 'product_option_id');

		$restrictions = $this->getProductOptionValueRestrictions($product_id);

		foreach ($product_options as &$product_option) {
			$product_option_values = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = " . (int)$product_option['product_option_id'] . " ORDER BY sort_order ASC", 'product_option_value_id');

			foreach ($product_option_values as &$product_option_value) {
				$product_option_value['restrictions'] = array_search_key('product_option_value_id', $product_option_value['product_option_value_id'], $restrictions);
			}
			unset($product_option_value);

			$product_option['product_option_values'] = $product_option_values;

		}
		unset($product_option);

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
			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
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

	public function getProductOptionValueRestrictions($product_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_option_value_restriction WHERE product_id = " . (int)$product_id);
	}

	public function getProductDiscounts($product_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = " . (int)$product_id . " ORDER BY quantity, priority, price");
	}

	public function getProductActiveDiscounts($product_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = " . (int)$product_id . " AND customer_group_id = " . (int)$this->customer->getCustomerGroupId() . " AND quantity > 0 AND (date_start <= NOW() AND (date_end = '" . DATETIME_ZERO . "' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");
	}

	public function getProductSpecialPrice($product_id)
	{
		return $this->queryVar("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = " . (int)$product_id . " AND customer_group_id = " . (int)$customer_group_id . " AND (date_start <= NOW() AND (date_end = '" . DATETIME_ZERO . "' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
	}

	public function getProductImages($product_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = " . (int)$product_id . " ORDER BY sort_order");
	}

	public function getProductDownloads($product_id)
	{
		$downloads = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) WHERE p2d.product_id = " . (int)$product_id);

		$this->translation->translateAll('download', 'download_id', $downloads);

		return $downloads;
	}

	public function getProductReward($product_id)
	{
		return (int)$this->queryVar("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = " . (int)$product_id . " AND customer_group_id = " . (int)$this->customer->getCustomerGroupId());
	}

	public function getProductActiveRelated($product_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.product_id = " . (int)$product_id . " AND p.status = 1 AND p.date_available <= NOW() AND p2s.store_id = " . (int)option('store_id'));
	}

	public function getProductRelated($product_id)
	{
		return $this->queryColumn("SELECT related_id FROM " . DB_PREFIX . "product_related WHERE product_id = " . (int)$product_id);
	}

	public function getProductTags($product_id)
	{
		return $this->queryRows("SELECT t.* FROM " . DB_PREFIX . "product_tag pt LEFT JOIN " . DB_PREFIX . "tag t ON (pt.tag_id=t.tag_id) WHERE product_id = " . (int)$product_id, 'tag_id');
	}

	public function getProductLayoutId($product_id)
	{
		return $this->queryVar("SELECT layout_id FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)option('store_id') . "'");
	}

	public function getCategories($product_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = " . (int)$product_id);
	}

	//TODO: This should be in attribute Model
	public function getAttributes($data = array(), $select = '', $total = false)
	{
		$language_id = option('config_language_id');

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
				$where .= " AND a.attribute_id IN (" .
					"SELECT pa.attribute_id FROM " . DB_PREFIX . "product_attribute pa" .
					" LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id=pa.product_id)" .
					" WHERE p2c.category_id IN (" . implode(',', $data['category_ids']) . ")" .
					")";
			}

			//Group By, Order By and Limit
			if (!$total) {
				$order = $this->extractOrder($data);
				$limit = $this->extractLimit($data);
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

				$this->translation->translateAll('attribute', 'attribute_id', $attributes);
			}
		}

		$this->cache->set($cache_id, $attributes);

		return $attributes;
	}

	public function getClassController($product_id)
	{
		$product_class_id = $this->queryVar("SELECT product_class_id FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product_id);

		$product_class = $this->getProductClass($product_class_id);

		return $product_class['controller'] ? $product_class['controller'] : 'product/product';
	}

	public function getClassTemplate($product_class_id)
	{
		$product_class = $this->getProductClass($product_class_id);

		return !empty($product_class['template']) ? 'product/' . $product_class['template'] : 'product/product';
	}

	public function getProductClass($product_class_id)
	{
		$product_class = $this->cache->get('product_class.' . $product_class_id);

		if (is_null($product_class)) {
			$product_classes = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_class");

			foreach ($product_classes as &$product_class) {
				$product_class['front_template']   = unserialize($product_class['front_template']);
				$product_class['front_controller'] = unserialize($product_class['front_controller']);
				$product_class['defaults']         = unserialize($product_class['defaults']);
			}
			unset($product_class);

			$theme = $this->theme->getTheme();

			$product_class = array_search_key('product_class_id', $product_class_id, $product_classes);

			$product_class = array(
				'controller' => isset($product_class['front_controller'][$theme]) ? $product_class['front_controller'][$theme] : '',
				'template'   => isset($product_class['front_template'][$theme]) ? $product_class['front_template'][$theme] : '',
			);

			$this->cache->set('product_class.' . $product_class_id, $product_class);
		}

		return $product_class;
	}

	public function getProductSpecials($product_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = " . (int)$product_id . " ORDER BY priority, price");
	}

	public function getProductActiveSpecial($product_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = " . (int)$product_id . " AND date_start <= NOW() AND (date_end = '" . DATETIME_ZERO . "' OR date_end > NOW()) ORDER BY priority, price LIMIT 1");
	}

	public function getProductRewards($product_id)
	{
		$product_rewards_list = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = " . (int)$product_id);

		$product_rewards = array();

		foreach ($product_rewards_list as $product_reward) {
			$product_rewards[$product_reward['customer_group_id']] = $product_reward['points'];
		}

		return $product_rewards;
	}

	public function getProductStores($product_id)
	{
		return $this->queryColumn("SELECT store_id FROM " . DB_PREFIX . "product_to_store WHERE product_id = " . (int)$product_id);
	}

	public function getProductLayouts($product_id)
	{
		$product_layout_list = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = " . (int)$product_id);

		$product_layouts = array();

		foreach ($product_layout_list as $product_layout) {
			$product_layouts[$product_layout['store_id']] = $product_layout['layout_id'];
		}

		return $product_layouts;
	}

	public function getProductTemplates($product_id)
	{
		$product_template_list = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_template WHERE product_id = " . (int)$product_id);

		$product_templates = array();

		foreach ($product_template_list as $product_template) {
			$product_templates[$product_template['store_id']][$product_template['theme']] = $product_template;
		}

		return $product_templates;
	}

	public function getProductCategories($product_id)
	{
		return $this->queryColumn("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = " . (int)$product_id);
	}

	public function getTotalProducts($data = array())
	{
		return $this->getProducts($data, '', true);
	}

	public function getTotalActiveProducts($data = array())
	{
		return $this->getActiveProducts($data, '', true);
	}

	public function getTotalProductSpecials()
	{
		$customer_group_id = $this->customer->getCustomerGroupId();

		$result = $this->query("SELECT COUNT(DISTINCT ps.product_id) AS total FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)option('store_id') . "' AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '" . DATETIME_ZERO . "' OR ps.date_start < NOW()) AND (ps.date_end = '" . DATETIME_ZERO . "' OR ps.date_end > NOW()))");

		if (isset($result->row['total'])) {
			return $result->row['total'];
		} else {
			return 0;
		}
	}

	public function optionIdToProductOptionId($product_id, $option_id)
	{
		return $this->queryVar("SELECT product_option_id FROM " . DB_PREFIX . "product_option WHERE product_id = " . (int)$product_id . " AND option_id = " . (int)$option_id);
	}

	public function fillProductDetails(&$details, $product_id, $quantity, &$options = array(), $ignore_status = false)
	{
		$product = isset($details['product']) ? $details['product'] : $this->getActiveProduct($product_id, $ignore_status);

		if (!$product) {
			return false;
		}

		// Product Specials / Discounts
		if ($product['special']) {
			$price = $product['special'];
		} elseif ($product['discount']) {
			$price = $product['discount'];
		} else {
			$price = $product['price'];
		}

		// Stock
		$in_stock = $product['subtract'] ? (int)$product['quantity'] >= $quantity : true;

		// Calculate Option totals
		$option_cost   = 0;
		$option_price  = 0;
		$option_points = 0;
		$option_weight = 0;

		if (!empty($options)) {
			if (!$this->fillProductOptions($product_id, $options)) {
				return false;
			}

			foreach ($options as $key => $values) {
				if (empty($values)) {
					unset($options[$key]);
					continue;
				}

				foreach ($values as $product_option_value) {
					if (empty($product_option_value)) {
						continue;
					}

					$option_cost += $product_option_value['cost'];
					$option_price += $product_option_value['price'];
					$option_points += $product_option_value['points'];
					$option_weight += $product_option_value['weight'];

					if ($product_option_value['subtract'] && $product_option_value['quantity'] < $quantity) {
						$in_stock = false;
					}
				}
			}
		}

		// Downloads
		$downloads = $this->getProductDownloads($product_id);

		//Product Details
		$details['price']        = $price + $option_price;
		$details['total']        = $details['price'] * $quantity;
		$details['cost']         = $product['cost'] + $option_cost;
		$details['total_cost']   = $details['cost'] * $quantity;
		$details['in_stock']     = $in_stock;
		$details['points']       = ((int)$product['points'] + $option_points) * $quantity;
		$details['weight']       = ((float)$product['weight'] + $option_weight) * $quantity;
		$details['total_reward'] = $product['reward'] * $quantity;
		$details['product']      = $product;
		$details['options']      = $options;
		$details['downloads']    = $downloads;

		return true;
	}

	private function fillProductOptions($product_id, &$options)
	{
		$filter = array(
			'product_option_ids' => array_keys($options),
		);

		$product_option_data = $this->getFilteredProductOptions($filter);

		foreach ($options as $product_option_id => &$product_option_values) {
			if (empty($product_option_values)) {
				continue;
			} elseif (!is_array($product_option_values)) {
				$product_option_values = array($product_option_values);
			}

			foreach ($product_option_values as $pov_key => &$product_option_value) {
				//Check if information already filled.
				if (is_array($product_option_value)) {
					//We verify option_id as this is not included with a typical Product Option Value, so make sure we load all necessary information
					if (!isset($product_option_value['option_id'])) {
						$product_option_value = $product_option_value['product_option_value_id'];
					} else {
						continue;
					}
				}

				$product_option_value = $this->getProductOptionValue($product_id, $product_option_id, $product_option_value);

				if ($product_option_value) {
					$data = array_search_key('product_option_id', $product_option_id, $product_option_data);

					//Validate that all options exist for this product
					if (!$data) {
						return false;
					}

					$product_option_value += $data;
				} else {
					//Option not found! Probably was deleted, and therefore no longer available.
					unset($product_option_values[$pov_key]);
				}
			}
			unset($product_option_value);

			//Clean up
			if (empty($product_option_values)) {
				unset($options[$product_option_id]);
			}
		}
		unset($product_option_values);

		return true;
	}
}

