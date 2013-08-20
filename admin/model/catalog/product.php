<?php
class Admin_Model_Catalog_Product extends Model
{
	public function addProduct($data)
	{
		$data['date_added'] = $this->date->now();

		$product_id = $this->insert('product', $data);

		//Product Store
		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$values = array(
					'store_id'   => $store_id,
					'product_id' => $product_id
				);
				$this->insert('product_to_store', $values);
			}
		}

		//Product Attributes
		if (isset($data['product_attributes'])) {
			foreach ($data['product_attributes'] as $product_attribute) {
				$product_attribute['product_id'] = $product_id;

				$this->insert('product_attribute', $product_attribute);
			}
		}

		//Product Options
		if (isset($data['product_options'])) {
			foreach ($data['product_options'] as $product_option) {
				if (in_array($product_option['type'], array(
				                                           'select',
				                                           'radio',
				                                           'checkbox',
				                                           'image'
				                                      ))
				) {
					$product_option['product_id'] = $product_id;

					$product_option_id = $this->insert('product_option', $product_option);

					if (!empty($product_option['product_option_value'])) {
						foreach ($product_option['product_option_values'] as $product_option_value) {
							$product_option_value['product_option_id'] = $product_option_id;
							$product_option_value['product_id']        = $product_id;
							$product_option_value['option_id']         = $product_option['option_id'];

							$product_option_value_id = $this->insert('product_option_value', $product_option_value);

							if (isset($product_option_value['restrictions'])) {
								foreach ($product_option_value['restrictions'] as $restriction) {
									$restriction['product_id']      = $product_id;
									$restriction['option_value_id'] = $product_option_value['option_value_id'];

									$this->insert('product_option_value_restriction', $restriction);
								}
							}
						}
					}
				} else {
					$product_option['product_id'] = $product_id;

					$this->insert('product_option', $product_option);
				}
			}
		}


		//Additional Product Images
		if (isset($data['product_images'])) {
			foreach ($data['product_images'] as $product_image) {
				$product_image['product_id'] = $product_id;

				$this->insert('product_image', $product_image);
			}
		}

		//Product Categories
		if (isset($data['product_category'])) {
			foreach (array_unique($data['product_category']) as $category_id) {
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
		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
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

				//the inverse so the other product is related to this product too!
				$values = array(
					'product_id' => $related_id,
					'related_id' => $product_id
				);

				$this->insert('product_related', $values);
			}
		}


		//Product Reward
		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				$product_reward['product_id']        = $product_id;
				$product_reward['customer_group_id'] = $customer_group_id;

				$this->insert('product_reward', $product_reward);
			}
		}


		//Product Layouts
		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$layout['product_id'] = $product_id;
					$layout['store_id']   = $store_id;

					$this->insert('product_to_layout', $layout);
				}
			}
		}

		//Product Templates
		if (isset($data['product_template'])) {
			foreach ($data['product_template'] as $store_id => $themes) {
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
		if (($insert = isset($data['product_category'])) || !$strict) {
			$this->delete('product_to_category', array('product_id' => $product_id));

			if ($insert) {
				foreach (array_unique($data['product_category']) as $category_id) {
					$values = array(
						'product_id'  => $product_id,
						'category_id' => $category_id
					);
					$this->insert('product_to_category', $values);
				}
			}
		}


		//Product Stores
		if (($insert = isset($data['product_store'])) || !$strict) {
			$this->delete('product_to_store', array('product_id' => $product_id));

			if ($insert) {
				foreach ($data['product_store'] as $store_id) {
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
		if (($insert = isset($data['product_download'])) || !$strict) {
			$this->delete('product_to_download', array('product_id' => $product_id));

			if ($insert) {
				foreach ($data['product_download'] as $download_id) {
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
		if (($insert = isset($data['product_reward'])) || !$strict) {
			$this->delete('product_reward', array('product_id' => $product_id));

			if ($insert) {
				foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
					$product_reward['product_id']        = $product_id;
					$product_reward['customer_group_id'] = $customer_group_id;

					$this->insert('product_reward', $product_reward);
				}
			}
		}

		//Product Layouts
		if (($insert = isset($data['product_layout'])) || !$strict) {
			$this->delete('product_to_layout', array('product_id' => $product_id));

			if ($insert) {
				foreach ($data['product_layout'] as $store_id => $layout) {
					if ($layout['layout_id']) {
						$layout['product_id'] = $product_id;
						$layout['store_id']   = $store_id;

						$this->insert('product_to_layout', $layout);
					}
				}
			}
		}

		//Product Templates
		if (($insert = isset($data['product_template'])) || !$strict) {
			$this->delete('product_template', array('product_id' => $product_id));

			if ($insert) {
				foreach ($data['product_template'] as $store_id => $themes) {
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
		if (!empty($data['alias'])) {
			$this->url->setAlias($data['alias'], 'product/product', 'product_id=' . (int)$product_id);
		} elseif (!$strict) {
			$this->url->removeAlias('product/product', 'product_id=' . (int)$product_id);
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

		$product['product_attribute'] = $this->getProductAttributes($product_id);
		$product['product_discount']  = $this->getProductDiscounts($product_id);
		$product['product_image']     = $this->getProductImages($product_id);
		$product['product_option']    = $this->getProductOptions($product_id);
		$product['product_related']   = $this->getProductRelated($product_id);
		$product['product_reward']    = $this->getProductRewards($product_id);
		$product['product_special']   = $this->getProductSpecials($product_id);
		$product['product_tags']      = $this->getProductTags($product_id);
		$product['product_category']  = $this->getProductCategories($product_id);
		$product['product_download']  = $this->getProductDownloads($product_id);
		$product['product_layout']    = $this->getProductLayouts($product_id);
		$product['product_template']  = $this->getProductTemplates($product_id);
		$product['product_store']     = $this->getProductStores($product_id);

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

		$this->translation->delete('product', $product_id);

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

	public function getProductField($product_id, $field)
	{
		return $this->queryVar("SELECT `" . $this->escape($field) . "` FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product_id);
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

		if (!empty($data['categories'])) {
			//TODO: Need to grab sub categories as well! Maybe this should be in controller?
			$category_ids = is_array($data['categories']) ? $data['categories'] : array((int)$data['categories']);

			$from .= " LEFT JOIN " . DB_PREFIX . "product_to_category pc ON (p.product_id=pc.product_id)";

			$where .= " AND pc.category_id IN (" . implode(',', $category_ids) . ")";
		}

		if (!empty($data['manufacturer_id'])) {
			$where .= " AND p.manufacturer_id = '" . (int)$data['manufacturer_id'] . "'";
		}

		if (!empty($data['manufacturers'])) {
			$where .= " AND p.manufacturer_id IN (" . implode(",", $data['manufacturers']) . ")";
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
			$select .= ", (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ((ps.date_start = '" . DATETIME_ZERO . "' OR ps.date_start < NOW()) AND (ps.date_end = '" . DATETIME_ZERO . "' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";

			if (!empty($data['special']['high'])) {
				$where .= " AND special <= '" . (int)$data['special']['high'] . "'";
			}

			if (!empty($data['special']['low'])) {
				$where .= " AND special >= '" . (int)$data['special']['low'] . "'";
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

		//Group By, Order By and Limit
		if (!$total) {
			$group_by = " GROUP BY p.product_id";

			//enable image sorting if requested and not already installed
			if (!empty($data['sort']) && strpos($data['sort'], '__image_sort__') === 0) {
				if (!$this->db->hasColumn('product', $data['sort'])) {
					$this->extend->enable_image_sorting('product', str_replace('__image_sort__', '', $data['sort']));
				}
			}

			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
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

	public function getProductAttributes($product_id)
	{
		$attributes = $this->queryRows("SELECT pa.*, a.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' GROUP BY pa.attribute_id");

		$this->translation->translate_all('attribute', 'attribute_id', $attributes);

		return $attributes;
	}

	public function getProductOptions($product_id)
	{
		$product_options = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		$restrictions = $this->getProductOptionValueRestrictions($product_id);

		foreach ($product_options as &$product_option) {
			$product_option_values = $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = " . (int)$product_option['product_option_id'] . " ORDER BY sort_order ASC");

			foreach ($product_option_values as &$product_option_value) {
				$product_option_value['restrictions'] = array_search_key('product_option_value_id', $product_option_value['product_option_value_id'], $restrictions);
			}
			unset($product_option_value);

			$product_option['product_option_values'] = $product_option_values;

		}
		unset($product_option);

		return $product_options;
	}

	public function getProductOptionValueRestrictions($product_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_option_value_restriction WHERE product_id = " . (int)$product_id);
	}

	public function getProductImages($product_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = " . (int)$product_id . " ORDER BY sort_order");
	}

	public function getProductDiscounts($product_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = " . (int)$product_id . " ORDER BY quantity, priority, price");
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

	public function getProductDownloads($product_id)
	{
		return $this->queryColumn("SELECT download_id FROM " . DB_PREFIX . "product_to_download WHERE product_id = " . (int)$product_id);
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

	public function getProductRelated($product_id)
	{
		return $this->queryColumn("SELECT related_id FROM " . DB_PREFIX . "product_related WHERE product_id = " . (int)$product_id);
	}

	public function getProductTags($product_id)
	{
		return $this->queryColumn("SELECT t.text FROM " . DB_PREFIX . "product_tag pt LEFT JOIN " . DB_PREFIX . "tag t ON (t.tag_id = pt.tag_id) WHERE product_id = '" . (int)$product_id . "'");
	}

	public function getTotalProducts($data = array())
	{
		return $this->getProducts($data, '', true);
	}
}
