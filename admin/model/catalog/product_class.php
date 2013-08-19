<?php
class Admin_Model_Catalog_ProductClass extends Model
{
	public function __construct($registry)
	{
		parent::__construct($registry);

		if (!$this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "product_class WHERE product_class_id = 0")) {
			$this->query("SET GLOBAL sql_mode='NO_AUTO_VALUE_ON_ZERO'");
			$this->query("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO'");

			$this->query("INSERT INTO " . DB_PREFIX . "product_class SET product_class_id = 0, name = 'Default'");
		}
	}

	public function addProductClass($data)
	{
		$data['front_template'] = serialize($data['front_template']);
		$data['admin_template'] = serialize($data['admin_template']);

		$product_class_id = $this->insert('product_class', $data);

		$this->cache->delete('product_class');

		return $product_class_id;
	}

	public function editProductClass($product_class_id, $data)
	{
		$data['front_template'] = serialize($data['front_template']);
		$data['admin_template'] = serialize($data['admin_template']);

		$this->update('product_class', $data, $product_class_id);

		$this->cache->delete('product_class');
	}

	public function deleteProductClass($product_class_id)
	{
		$this->delete('product_class', $product_class_id);

		$this->cache->delete('product_class');
	}

	public function getProductClass($product_class_id)
	{
		$product_class = $this->queryRow("SELECT * FROM " . DB_PREFIX . "product_class WHERE product_class_id = " . (int)$product_class_id);

		$product_class['front_template'] = unserialize($product_class['front_template']);
		$product_class['admin_template'] = unserialize($product_class['admin_template']);

		return $product_class;
	}

	public function getProductClassByName($name)
	{
		$product_class_id = $this->queryVar("SELECT product_class_id FROM " . DB_PREFIX . "product_class WHERE name = '" . $this->escape($name) . "' LIMIT 1");

		return $this->getProductClass($product_class_id);
	}

	public function getProductClasses($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "product_class";

		//Where
		$where = "1";

		if (!empty($data['product_class_ids'])) {
			$where .= " AND product_class_id IN (" . implode(',', $data['product_class_ids']) . ")";
		}

		//Order By and Limit
		if (!$total) {
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}

		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		$result = $this->query($query);

		if ($total) {
			return $result->row['total'];
		}

		foreach ($result->rows as &$product_class) {
			$product_class['front_template'] = unserialize($product_class['front_template']);
			$product_class['admin_template'] = unserialize($product_class['admin_template']);
		}
		unset($product_class);

		return $result->rows;
	}

	public function getTotalProductClasses($data = array())
	{
		return $this->getProductClasses($data, '', true);
	}

	public function getTemplate($product_class_id)
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

		if (!empty($product_class['admin_template'][$theme])) {
			return 'catalog/product_class/' . $product_class['admin_template'][$theme];
		}

		return 'catalog/product_form';
	}

	public function getFrontTemplates()
	{
		$this->language->load('catalog/product_class');

		$front_templates = $this->template->getTemplatesFrom('product', false, $this->_('text_default_template'));

		foreach ($front_templates as $theme => &$templates) {
			if ($theme !== 'default') {
				$templates += $front_templates['default'];
			}
		}
		unset($template);

		return $front_templates;
	}

	public function getAdminTemplates()
	{
		$this->language->load('catalog/product_class');

		$admin_templates = $this->template->getTemplatesFrom('catalog/product_class', true, $this->_('text_default_template'));

		foreach ($admin_templates as $theme => &$templates) {
			if ($theme !== 'default') {
				$templates += $admin_templates['default'];
			}
		}
		unset($template);

		return $admin_templates;
	}
}
