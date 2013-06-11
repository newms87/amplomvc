<?php
class Admin_Model_Catalog_Product extends Model 
{
	public function addProduct($data)
	{
		$data['date_added'] = $this->tool->format_datetime();
		
		$product_id = $this->insert('product', $data);
		
		//Product Store
		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$values = array(
					'store_id' => $store_id,
					'product_id' => $product_id
				);
				$this->insert('product_to_store', $values);
			}
		}
		
		//Product Attributes
		if (isset($data['product_attributes'])) {
				foreach ($data['product_attributes'] as $product_attribute) {
					$product_attribute['product_id'] = $product_id;
					$product_attribute['language_id'] = $language_id;
					
					$this->insert('product_attribute', $product_attribute);
				}
			}
		
		//Product Options
		if (isset($data['product_options'])) {
			foreach ($data['product_options'] as $product_option) {
				if (in_array($product_option['type'], array('select', 'radio', 'checkbox', 'image'))) {
					$product_option['product_id'] = $product_id;
					
					$product_option_id = $this->insert('product_option', $product_option);
				
					if (isset($product_option['product_option_value'])) {
						foreach ($product_option['product_option_value'] as $product_option_value) {
							$product_option_value['product_option_id'] = $product_option_id;
							$product_option_value['product_id'] = $product_id;
							$product_option_value['option_id'] = $product_option['option_id'];
							
							$product_option_value_id = $this->insert('product_option_value', $product_option_value);
							
							if (isset($product_option_value['restrictions'])) {
								foreach ($product_option_value['restrictions'] as $restriction) {
									$restriction['product_id']		= $product_id;
									$restriction['option_value_id'] = $product_option_value['option_value_id'];
									
									$this->insert('product_option_value_restriction', $restriction);
								}
							}
						}
					}
				} else {
					$product_option['product_id'] = $product_id;
					
					$this->insert('product_option',  $product_option);
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
					'product_id' => $product_id,
					'category_id' => $category_id
				);
				$this->insert('product_to_category',  $values);
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
					'product_id' => $product_id
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
				$product_reward['product_id'] = $product_id;
				$product_reward['customer_group_id'] = $customer_group_id;
				
				$this->insert('product_reward', $product_reward);
			}
		}
		
		
		//Product Layouts
		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$layout['product_id'] = $product_id;
					$layout['store_id'] = $store_id;
					
					$this->insert('product_to_layout', $layout);
				}
			}
		}
		
		//Product Templates
		if (isset($data['product_template'])) {
			foreach ($data['product_template'] as $store_id => $themes) {
				foreach ($themes as $theme => $template) {
					if(empty($template['template'])) continue;
					
					$template['product_id'] = $product_id;
					$template['theme'] = $theme;
					$template['store_id'] = $store_id;
					
					$this->insert('product_template', $template);
				}
			}
		}
		
		foreach ($data['product_tag'] as $language_id => $value) {
			if ($value) {
				$tags = explode(',', $value);
				
				foreach ($tags as $tag) {
					$values = array(
					'product_id' => $product_id,
					'language_id' => $language_id,
					'tag' => trim($tag)
					);
					
					$this->insert('product_tag', $values);
				}
			}
		}
						
		if ($data['keyword']) {
			if (!preg_match("/^product\//",$data['keyword'])) {
				$data['keyword'] = 'product/' . $data['keyword'];
			}
			
			$url_alias = array(
				'route'=>'product/product',
				'query'=>'product_id=' . (int)$product_id,
				'keyword'=>$this->db->escape($data['keyword']),
				'status'=>$data['status'],
			);
			
			$this->Model_Setting_UrlAlias->addUrlAlias($url_alias);
		}

		if (!empty($data['translations'])) {
			$this->translation->set_translations('product', $product_id, $data['translations']);
		}
		
		$this->cache->delete('product');
	}
	
	public function editProduct($product_id, $data)
	{
		$language_id = $this->config->get('config_language_id');
		
		$data['date_modified'] = $this->tool->format_datetime();
		
		$this->update('product', $data, $product_id);
		
		//Product Options
		$this->delete('product_option', array('product_id'=>$product_id));
		$this->delete('product_option_value', array('product_id'=>$product_id));
		$this->delete('product_option_value_restriction', array('product_id'=>$product_id));
		
		if (isset($data['product_options'])) {
			foreach ($data['product_options'] as $product_option) {
				$product_option['product_id'] = $product_id;
				
				$product_option_id = $this->insert('product_option', $product_option);
			
				if (isset($product_option['product_option_value'])) {
					foreach ($product_option['product_option_value'] as $product_option_value) {
						$product_option_value['product_id'] = $product_id;
						$product_option_value['option_id'] = $product_option['option_id'];
						$product_option_value['product_option_id'] = $product_option_id;
						
						$product_option_value_id = $this->insert('product_option_value', $product_option_value);
						
						if (isset($product_option_value['restrictions'])) {
							foreach ($product_option_value['restrictions'] as $restriction) {
								$restriction['product_id']		= $product_id;
								$restriction['option_value_id'] = $product_option_value['option_value_id'];
								
								$this->insert('product_option_value_restriction', $restriction);
							}
						}
					}
				}
			}
		}
		
		
		//Product Additional Images
		$this->delete('product_image', array('product_id'=>$product_id));
		
		if (isset($data['product_images'])) {
			foreach ($data['product_images'] as $product_image) {
				$product_image['product_id'] = $product_id;
				
				$this->insert('product_image', $product_image);
			}
		}
		
		//Product Categories
		$this->delete('product_to_category', array('product_id'=>$product_id));
				
		if (isset($data['product_category'])) {
			foreach (array_unique($data['product_category']) as $category_id) {
				$values = array(
					'product_id' => $product_id,
					'category_id' => $category_id
				);
				$this->insert('product_to_category',  $values);
			}
		}
		
		
		//Product Stores
		$this->delete('product_to_store', array('product_id'=>$product_id));

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$values = array(
					'store_id' => $store_id,
					'product_id' => $product_id
				);
				$this->insert('product_to_store', $values);
			}
		}
		
		
		//Product Attributes
		$this->delete('product_attribute', array('product_id'=>$product_id));

		if (isset($data['product_attributes'])) {
			$product_attributes = array_unique($data['product_attributes'], SORT_REGULAR);
			
			foreach ($product_attributes as $product_attribute) {
				$product_attribute['product_id'] = $product_id;
				$product_attribute['language_id'] = $language_id;
				
				$this->insert('product_attribute', $product_attribute);
			}
		}
		
		//Product Discount
		$this->delete('product_discount', array('product_id'=>$product_id));

		if (isset($data['product_discounts'])) {
			foreach ($data['product_discounts'] as $product_discount) {
				$product_discount['product_id'] = $product_id;
				
				$this->insert('product_discount', $product_discount);
			}
		}
		
		
		//Product Special
		$this->delete('product_special', array('product_id'=>$product_id));
		
		if (isset($data['product_specials'])) {
			foreach ($data['product_specials'] as $product_special) {
				$product_special['product_id'] = $product_id;
				
				$this->insert('product_special', $product_special);
			}
		}
		
		
		//Product Downloads
		$this->delete('product_to_download',  array('product_id'=>$product_id));
		
		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$values = array(
					'download_id' => $download_id,
					'product_id' => $product_id
				);
				$this->insert('product_to_download', $values);
			}
		}
		
		
		//Product Related
		$this->delete('product_related',  array('product_id'=>$product_id));
		$this->delete('product_related', array('related_id'=>$product_id));

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
		$this->delete('product_reward',  array('product_id'=>$product_id));

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				$product_reward['product_id'] = $product_id;
				$product_reward['customer_group_id'] = $customer_group_id;
				
				$this->insert('product_reward', $product_reward);
			}
		}
		
		//Product Layouts
		$this->delete('product_to_layout',  array('product_id'=>$product_id));

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$layout['product_id'] = $product_id;
					$layout['store_id'] = $store_id;
					
					
					$this->insert('product_to_layout', $layout);
				}
			}
		}
		
		//Product Templates
		$this->delete('product_template',  array('product_id'=>$product_id));

		if (isset($data['product_template'])) {
			foreach ($data['product_template'] as $store_id => $themes) {
				foreach ($themes as $theme => $template) {
					if(empty($template['template'])) continue;
					
					$template['product_id'] = $product_id;
					$template['theme'] = $theme;
					$template['store_id'] = $store_id;
					
					$this->insert('product_template', $template);
				}
			}
		}
		
		
		//Product Tags
		$this->delete('product_tag',  array('product_id' => $product_id));
		
		$product_tags = array(
			$language_id => $data['product_tag'],
		);
		
		$product_tags += $data['translations']['product_tag'];
		unset($data['translations']['product_tag']);
		
		foreach ($product_tags as $language_id => $value) {
			if ($value) {
				$tags = explode(',', $value);
				
				if ($tags) {
					foreach ($tags as $tag) {
						$values = array(
							'product_id' => $product_id,
							'language_id' => $language_id,
							'tag' => trim($tag)
						);
						
						$product_tag_id = $this->insert('product_tag', $values);
					}
				}
			}
		}
		
		
		//Product URL Alias
		$this->Model_Setting_UrlAlias->deleteUrlAliasByRouteQuery('product/product', 'product_id=' . (int)$product_id);
		
		if ($data['keyword']) {
			if (!preg_match("/^product\//",$data['keyword'])) {
				$data['keyword'] = 'product/' . $data['keyword'];
			}
			
			$url_alias = array(
				'route'=>'product/product',
				'query'=>'product_id=' . (int)$product_id,
				'keyword'=>$this->db->escape($data['keyword']),
				'status'=>$data['status'],
			);
			
			$this->Model_Setting_UrlAlias->addUrlAlias($url_alias);
		}
		
		//Translations
		if (!empty($data['translations'])) {
			$this->translation->set_translations('product', $product_id, $data['translations']);
		}
		
		$this->cache->delete('product');
	}
	
	public function generate_url($product_id, $name)
	{
		$url = 'product/'.$this->Model_Setting_UrlAlias->format_url($name);
		$orig = $url;
		$count = 2;
		
		$url_alias = $product_id?$this->Model_Setting_UrlAlias->getUrlAliasByRouteQuery('product/product', "product_id=$product_id"):null;
		
		$test = $this->Model_Setting_UrlAlias->getUrlAliasByKeyword($url);
		while (!empty($test) && $test['url_alias_id'] != $url_alias['url_alias_id']) {
			$url = $orig . '-' . $count++;
			$test = $this->Model_Setting_UrlAlias->getUrlAliasByKeyword($url);
		}
		return $url;
	}
	
	public function generate_model($name)
	{
		$model = strtoupper($this->Model_Setting_UrlAlias->format_url($name));
		$orig = $model;
		$count = 2;
		$test = $this->query("SELECT COUNT(*) as count FROM " . DB_PREFIX ."product WHERE model='$model'");
		while ($test->row['count']) {
			$model = $orig . '-' . $count++;
			$test = $this->query("SELECT COUNT(*) as count FROM " . DB_PREFIX ."product WHERE model='$model'");
		}
		return $model;
	}
	
	public function copyProduct($product_id)
	{
		$product = $this->getProduct($product_id);
		
		if (!$product) return false;
		
		$product['keyword'] = '';

		$product['status'] = 0;
		
		$product['product_attribute'] = $this->getProductAttributes($product_id);
		$product['product_discount'] = $this->getProductDiscounts($product_id);
		$product['product_image'] = $this->getProductImages($product_id);
		$product['product_option'] = $this->getProductOptions($product_id);
		$product['product_related'] = $this->getProductRelated($product_id);
		$product['product_reward'] = $this->getProductRewards($product_id);
		$product['product_special'] = $this->getProductSpecials($product_id);
		$product['product_tag'] = $this->getProductTags($product_id);
		$product['product_category'] = $this->getProductCategories($product_id);
		$product['product_download'] = $this->getProductDownloads($product_id);
		$product['product_layout'] = $this->getProductLayouts($product_id);
		$product['product_template'] = $this->getProductTemplates($product_id);
		$product['product_store'] = $this->getProductStores($product_id);
		
		$name_count = $this->query_var("SELECT COUNT(*) FROM " . DB_PREFIX . "product WHERE `name` like '$product[name]%'");
		
		$product['name'] .= ' - Copy' . ($name_count > 1 ? "($name_count)" : '');
		
		$this->addProduct($product);
		
		return true;
	}
	
	public function deleteProduct($product_id)
	{
		$this->delete('product', array('product_id'=>$product_id));
		$this->delete('product_attribute', array('product_id'=>$product_id));
		$this->delete('product_discount', array('product_id'=>$product_id));
		$this->delete('product_image', array('product_id'=>$product_id));
		$this->delete('product_option', array('product_id'=>$product_id));
		$this->delete('product_option_value', array('product_id'=>$product_id));
		$this->delete('product_option_value_restriction', array('product_id'=>$product_id));
		$this->delete('product_related', array('product_id'=>$product_id));
		$this->delete('product_related', array('related_id'=>$product_id));
		$this->delete('product_reward', array('product_id'=>$product_id));
		$this->delete('product_special', array('product_id'=>$product_id));
		$this->delete('product_tag', array('product_id'=>$product_id));
		$this->delete('product_to_category', array('product_id'=>$product_id));
		$this->delete('product_to_download', array('product_id'=>$product_id));
		$this->delete('product_to_layout', array('product_id'=>$product_id));
		$this->delete('product_template', array('product_id'=>$product_id));
		$this->delete('product_to_store', array('product_id'=>$product_id));
		$this->delete('review', array('product_id'=>$product_id));
		
		$this->Model_Setting_UrlAlias->deleteUrlAliasByRouteQuery('product/product', 'product_id=' . (int)$product_id);
		
		$this->cache->delete('product');
	}
	
	public function getProduct($product_id)
	{
		$product = $this->query_row("SELECT DISTINCT * FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int)$product_id . "'");
		
		if ($product) {
			$url_alias = $this->Model_Setting_UrlAlias->getUrlAliasByRouteQuery('product/product', "product_id=" . (int)$product_id);
			$product['keyword'] = $url_alias ? $url_alias['keyword']:'';
			
			$this->translation->translate('product', $product_id, $product);
		}
			
		return $product;
	}
	
	public function getProducts($data = array(), $select = '', $total = false) {
		$lang_id = (int)$this->config->get('config_language_id');
		
		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		}
		elseif (!$select) {
			$select = 'p.*';
		}
		
		//From
		$from = "FROM " . DB_PREFIX . "product p";
		
		//Where
		$where = "WHERE 1";
		
		if (isset($data['name'])) {
			$where .= " AND LCASE(p.name) like '%" . strtolower($this->db->escape($data['name'])) . "%'";
		}
		
		if (isset($data['model'])) {
			$where .= " AND LCASE(p.model) like '%" . strtolower($this->db->escape($data['model'])) . "%'";
		}
		
		if ((isset($data['sort']) && $data['sort'] == 'cp.name') || isset($data['collections'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "collection_product cp ON (cp.product_id=p.product_id)";
			
			if (!empty($data['collections'])) {
				if (!is_array($data['collections'])) {
					$data['collections'] = array((int)$data['collections']);
				}
				
				$where .= " AND cp.collection_id IN (" . implode(',', $data['collections']) . ")";
			}
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

		if ((isset($data['sort']) && $data['sort'] == 'manufacturer_name') || !empty($data['manufacturer_name'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "manufacturer m ON(m.manufacturer_id=p.manufacturer_id)";
			
			if (!empty($data['manufacturer_name'])) {
				$where  .= " AND LCASE(m.name) = '" . strtolower($this->db->escape($data['m.name'])) . "'";
			}
		}
		
		if (!empty($data['price']['low'])) {
			$where .= " AND p.price >= '" . (int)$data['p.price']['low'] . "'";
		}
		
		if (!empty($data['price']['high'])) {
			$where .= " AND p.price <= '" . (int)$data['p.price']['high'] . "'";
		}
		
		if (!empty($data['cost']['low'])) {
			$where .= " AND p.cost >= '" . (int)$data['p.cost']['low'] . "'";
		}
		
		if (!empty($data['cost']['high'])) {
			$where .= " AND p.cost <= '" . (int)$data['p.cost']['high'] . "'";
		}
		
		if (!empty($data['special']) || (isset($data['sort']) && $data['sort'] == 'special')) {
			$select .= ", (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ((ps.date_start = '" . DATETIME_ZERO . "' OR ps.date_start < NOW()) AND (ps.date_end = '". DATETIME_ZERO . "' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";
			
			if (!empty($data['special']['high'])) {
				$where .= " AND special <= '" . (int)$data['special']['high'] . "'";
			}
			
			if (!empty($data['special']['low'])) {
				$where .= " AND special >= '" . (int)$data['special']['low'] . "'";
			}
		}
		
		if (!empty($data['is_final'])) {
			$where .= " AND p.is_final = '" . ($data['p.is_final'] ? 1 : 0) . "'";
		}
		
		if (!empty($data['date_expires']['from'])) {
			$dt_zero = DATETIME_ZERO;
			
			$where .= " AND (p.date_expires == '$dt_zero' OR p.date_expires >= '" . $this->db->escape($data['date_expires']['from']) . "'";
		}
		
		if (!empty($data['date_expires']['to'])) {
			$where .= " AND (p.date_expires <= '" . $this->db->escape($data['date_expires']['to']) . "'";
		}
		
		if (isset($data['quantity'])) {
			$where .= " AND p.quantity = '" . (int)$data['p.quantity'] . "'";
		}
		
		if (isset($data['status'])) {
			$where .= " AND p.status = '" . ($data['status'] ? 1 : 0) . "'";
		}
		
		//Group By, Order By and Limit
		if (!$total) {
			$group_by = " GROUP BY p.product_id";
			
			//enable image sorting if requested and not already installed
			if (!empty($data['sort']) && strpos($data['sort'], '__image_sort__') === 0) {
				if (!$this->db->has_column('product', $data['sort'])) {
					$this->extend->enable_image_sorting('product', str_replace('__image_sort__', '', $data['sort']));
				}
			}
			
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$group_by = '';
			$order = '';
			$limit = '';
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
	
	public function isEditable($product_id)
	{
		return (int)$this->query_var("SELECT editable FROM " . DB_PREFIX . "product WHERE product_id='$product_id'");
	}

	public function updateProductCategory($product_id, $op, $category_id)
	{
		$where = array(
			'category_id' => $category_id,
			'product_id' => $product_id,
		);
		
		$this->delete('product_to_category', $where);
		
		if ($op == 'add') {
			$this->delete('product_to_category', $where);
		}
		
		$this->cache->delete('product');
	}
	
	public function updateProduct($product_id, $name, $value)
	{
		$this->query("UPDATE " . DB_PREFIX . "product SET `$name`='$value' WHERE product_id='$product_id'");
		
		$this->cache->delete('product');
	}
	
	public function getProductAttributes($product_id)
	{
		$attributes = $this->query_rows("SELECT * FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' GROUP BY pa.attribute_id");
		
		$this->translation->translate_all('attribute', 'attribute_id', $attributes);
		
		return $attributes;
	}
	
	public function getProductOptions($product_id)
	{
		$query = $this->query("SELECT *, po.sort_order FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");
		
		$restrict_list = $this->getProductOptionValueRestrictions($product_id);
		
		$restrictions = array();
		
		foreach ($restrict_list as $value) {
			$restrictions[$value['option_value_id']][] = $value;
		}
		
		foreach ($query->rows as &$product_option) {
				
				$pov_query = $this->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");
				
				foreach ($pov_query->rows as &$pov) {
					if (isset($restrictions[$pov['option_value_id']])) {
						$pov['restrictions'] = $restrictions[$pov['option_value_id']];
					}
				}
				
				$product_option['product_option_value'] = $pov_query->rows;
		}
		
		return $query->rows;
	}
	
	public function getProductOptionValueRestrictions($product_id)
	{
		$language_id = $this->config->get('config_language_id');
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_option_value_restriction WHERE product_id='" . (int)$product_id . "'");
		
		return $query->rows;
	}
	
	public function getProductImages($product_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order");
		
		return $query->rows;
	}
	
	public function getProductDiscounts($product_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");
		
		return $query->rows;
	}
	
	public function getProductSpecials($product_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");
		
		return $query->rows;
	}
	
	public function getProductActiveSpecial($product_id)
	{
		$datetime_zero = DATETIME_ZERO;
		
		$result = $this->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND (date_start = '$datetime_zero' OR date_start <= NOW()) AND (date_end = '$datetime_zero' OR date_end > NOW()) ORDER BY priority, price LIMIT 1");
		
		return $result->row;
	}
	
	public function getProductRewards($product_id)
	{
		$product_reward_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}
		
		return $product_reward_data;
	}
		
	public function getProductDownloads($product_id)
	{
		$product_download_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}
		
		return $product_download_data;
	}

	public function getProductStores($product_id)
	{
		$product_store_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}
		
		return $product_store_data;
	}

	public function getProductLayouts($product_id)
	{
		$product_layout_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}
		
		return $product_layout_data;
	}
	
	public function getProductTemplates($product_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_template WHERE product_id = '" . (int)$product_id . "'");
		
		$template_data = array();
	
		foreach ($query->rows as $result) {
			$template_data[$result['store_id']][$result['theme']] = $result;
		}
		
		return $template_data;
	}
		
	public function getProductCategories($product_id)
	{
		$product_category_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductRelated($product_id)
	{
		$product_related_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}
		
		return $product_related_data;
	}
	
	public function getProductTags($product_id)
	{
		$product_tags = $this->query_rows("SELECT * FROM " . DB_PREFIX . "product_tag WHERE product_id = '" . (int)$product_id . "'");
		
		$tag_data = array();
		
		foreach ($product_tags as $product_tag) {
			$tag_data[$product_tag['language_id']][] = $product_tag['tag'];
		}
		
		$product_tag_data = array();
		
		foreach ($tag_data as $language_id => $tags) {
			$product_tag_data[$language_id] = implode(',', $tags);
		}
		
		return $product_tag_data;
	}
	
	public function getTotalProducts($data = array()) {
		return $this->getProducts($data, '', true);
	}
	
	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}
		
	public function getTotalProductsByStockStatusId($stock_status_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByLengthClassId($length_class_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int)$download_id . "'");
		
		return $query->row['total'];
	}
	
	public function getTotalProductsByManufacturerId($manufacturer_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByAttributeId($attribute_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByOptionId($option_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByLayoutId($layout_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}
}