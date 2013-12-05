<?php
class Admin_Model_Catalog_ProductClass extends Model
{
	public function __construct($registry)
	{
		parent::__construct($registry);

		if (!$this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "product_class")) {
			$default_product_class = array(
				'name' => 'Default',
			);

			$this->addProductClass($default_product_class);
		}
	}

	public function addProductClass($data)
	{
		$data['front_template']   = !empty($data['front_template']) ? serialize($data['front_template']) : '';
		$data['front_controller'] = !empty($data['front_controller']) ? serialize($data['front_controller']) : '';
		$data['admin_template']   = !empty($data['admin_template']) ? serialize($data['admin_template']) : '';
		$data['admin_controller'] = !empty($data['admin_controller']) ? serialize($data['admin_controller']) : '';

		$product_class_id = $this->insert('product_class', $data);

		$this->cache->delete('product_class');

		return $product_class_id;
	}

	public function editProductClass($product_class_id, $data)
	{
		if (isset($data['front_template'])) {
			$data['front_template'] = !empty($data['front_template']) ? serialize($data['front_template']) : '';
		}

		if (isset($data['front_controller'])) {
			$data['front_controller'] = !empty($data['front_controller']) ? serialize($data['front_controller']) : '';
		}

		if (isset($data['admin_template'])) {
			$data['admin_template'] = !empty($data['admin_template']) ? serialize($data['admin_template']) : '';
		}

		if (isset($data['admin_controller'])) {
			$data['admin_controller'] = !empty($data['admin_controller']) ? serialize($data['admin_controller']) : '';
		}


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

		$product_class['front_template']   = unserialize($product_class['front_template']);
		$product_class['front_controller'] = unserialize($product_class['front_controller']);
		$product_class['admin_template']   = unserialize($product_class['admin_template']);
		$product_class['admin_controller'] = unserialize($product_class['admin_controller']);

		return $product_class;
	}

	public function getProductClassByName($name)
	{
		$product_class_id = $this->queryVar("SELECT product_class_id FROM " . DB_PREFIX . "product_class WHERE name = '" . $this->escape($name) . "' LIMIT 1");

		return $this->getProductClass($product_class_id);
	}

	public function getProductClasses($filter = array(), $select = '', $total = false)
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
		if (!isset($filter['status'])) {
			$filter['status'] = 1;
		}

		$where = $this->getWhere('product_class', $filter);

		if (!empty($filter['product_class_ids'])) {
			$where .= " AND product_class_id IN (" . implode(',', $filter['product_class_ids']) . ")";
		}

		//Order By and Limit
		if (!$total) {
			$order = $this->extractOrder($filter);
			$limit = $this->extractLimit($filter);
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
