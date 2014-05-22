<?php
class App_Model_Catalog_Category extends Model
{
	public function add($data)
	{
		if (!$this->validation->text($data['name'], 2, 64)) {
			$this->error['name'] = _l("Category Name must be between 2 and 64 characters!");
		}

		if ($this->error) {
			return false;
		}

		$data['date_added']    = $this->date->now();
		$data['date_modified'] = $data['date_added'];

		$category_id = $this->insert('category', $data);

		if (!empty($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$store = array(
					'category_id' => $category_id,
					'store_id'    => $store_id,
				);

				$this->insert('category_to_store', $store);
			}
		}

		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$layout['category_id'] = $category_id;
					$layout['store_id']    = $store_id;

					$this->insert('category_to_layout', $layout);
				}
			}
		}

		if (!empty($data['alias'])) {
			$this->url->setAlias($data['alias'], 'product/category', 'category_id=' . (int)$category_id);
		}

		if (!empty($data['translations'])) {
			$this->translation->setTranslations('category', $category_id, $data['translations']);
		}

		$this->cache->delete('category');

		return $category_id;
	}

	public function edit($category_id, $data)
	{
		if (isset($data['name']) && !$this->validation->text($data['name'], 2, 64)) {
			$this->error['name'] = _l("Category Name must be between 2 and 64 characters!");
		}

		if ($this->error) {
			return false;
		}

		$data['date_modified'] = $this->date->now();

		if (!$this->update('category', $data, $category_id)) {
			return false;
		}

		if (isset($data['category_store'])) {
			$this->delete('category_to_store', array('category_id' => $category_id));

			foreach ($data['category_store'] as $store_id) {
				$store = array(
					'category_id' => $category_id,
					'store_id'    => $store_id,
				);

				$this->insert('category_to_store', $store);
			}
		}

		if (isset($data['category_layout'])) {
			$this->delete('category_to_layout', array('category_id' => $category_id));

			foreach ($data['category_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$layout['category_id'] = $category_id;
					$layout['store_id']    = $store_id;

					$this->insert('category_to_layout', $layout);
				}
			}
		}

		if (isset($data['alias'])) {
			$this->url->setAlias($data['alias'], 'product/category', 'category_id=' . (int)$category_id);
		}

		if (isset($data['translations'])) {
			$this->translation->setTranslations('category', $category_id, $data['translations']);
		}

		$this->cache->delete('category');

		return $category_id;
	}

	public function remove($category_id)
	{
		$this->delete('category', $category_id);
		$this->delete('category_to_store', array('category_id' => $category_id));
		$this->delete('category_to_layout', array('category_id' => $category_id));

		$this->url->removeAlias('product/category', 'category_id=' . (int)$category_id);

		$this->translation->deleteTranslation('category', $category_id);

		$this->delete('product_to_category', array('category_id' => $category_id));

		$children = $this->queryRows("SELECT category_id FROM " . DB_PREFIX . "category WHERE parent_id = '" . (int)$category_id . "'");

		foreach ($children as $category) {
			$this->deleteCategory($category['category_id']);
		}

		$this->cache->delete('category');

		return true;
	}

	public function copy($category_id)
	{
		$category = $this->getCategory($category_id);

		$category['category_store']   = $this->getCategoryStores($category_id);
		$category['category_layout'] = $this->getCategoryLayouts($category_id);
		$category['translations']     = $this->getCategoryTranslations($category_id);

		$copy_count = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "category WHERE `name` like '$category[name]%'");
		$category['name'] .= ' - Copy(' . $copy_count . ')';

		return $this->add($category);
	}

	public function getActiveCategory($category_id)
	{
		$category = $this->queryRow(
			"SELECT * FROM " . DB_PREFIX . "category c" .
			" LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)" .
			" WHERE c.category_id = '" . (int)$category_id . "' AND c2s.store_id = '" . (int)option('store_id') . "' AND c.status = '1'"
		);

		if ($category) {
			$this->translation->translate('category', $category_id, $category);
		}

		return $category;
	}

	public function getCategory($category_id)
	{
		$result = $this->queryRow("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");

		$result['alias'] = $this->url->getAlias('product/category', 'category_id=' . $category_id);

		return $result;
	}

	public function getActiveCategories($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "category c" .
			" LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c2s.category_id=c.category_id)";

		//Where
		$where = "c2s.store_id = " . (int)option('store_id') . " AND c.status = 1";

		if (!empty($data['category_ids'])) {
			$where .= " AND category_id IN (" . implode(',', $data['category_ids']) . ")";
		}

		if (!empty($data['parent_ids'])) {
			$where .= " AND parent_id IN (" . implode(',', $data['parent_ids']) . ")";
		}

		if (!empty($data['layouts'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "category_to_layout c2l ON (c.category_id=c2l.category_id)";

			$where .= " AND c2l.layout_id IN (" . implode(',', $data['layouts']) . ")";
		}

		//Order By and Limit
		if (!$total) {
			if (empty($data['sort'])) {
				$data['sort'] = "c.sort_order, LCASE(c.name)";
			} elseif (strpos($data['sort'], '__image_sort__') === 0) {
				if (!$this->db->hasColumn('category', $data['sort'])) {
					$this->extend->enable_image_sorting('category', str_replace('__image_sort__', '', $data['sort']));
				}
			}

			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
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

		$this->translation->translateAll('category', 'category_id', $result->rows);

		return $result->rows;
	}

	public function getCategories($data = array(), $select = '*', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "category c";

		//Where
		$where = "1";

		if (!empty($data['name'])) {
			$where .= " AND `name` like '%" . $this->escape($data['name']) . "%'";
		}

		if (!empty($data['category_ids'])) {
			$where .= " AND category_id IN (" . implode(',', $data['category_ids']) . ")";
		}

		if (!empty($data['parent_ids'])) {
			$where .= " AND parent_id IN (" . implode(',', $data['parent_ids']) . ")";
		}

		if (!empty($data['layouts'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "category_to_layout c2l ON (c.category_id=c2l.category_id)";

			$where .= " AND c2l.layout_id IN (" . implode(',', $data['layouts']) . ")";
		}

		//Order By and Limit
		if (!$total) {
			if (!empty($data['sort']) && strpos($data['sort'], '__image_sort__') === 0) {
				if (!$this->db->hasColumn('category', $data['sort'])) {
					$this->extend->enable_image_sorting('category', str_replace('__image_sort__', '', $data['sort']));
				}
			}

			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
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

		return $result->rows;
	}

	public function getChildrenIds($category_tree)
	{
		if (!is_array($category_tree)) {
			$category_tree = $this->getCategoryTree($category_tree);
		}

		$children_ids = array();

		foreach ($category_tree['children'] as $child) {
			$children_ids[] = $child['category_id'];

			$children_ids = array_merge($children_ids, $this->getChildrenIds($child));
		}

		return $children_ids;
	}

	public function getParent($category)
	{
		return current($this->getParents($category));
	}

	public function getParents($category)
	{
		$category_tree = $this->getCategoryTree($category);

		if (!is_array($category)) {
			$category = array_search_key('category_id', (int)$category, $category_tree);
		}

		//An array of parent ID's
		$parent_path = explode(',', $category['parent_path']);
		//minus the 0 root ID
		array_shift($parent_path);

		$parents = array();

		//Although this is a lot of extra data, PHP returns arrays via copy on write, making this optimized performance wise
		foreach ($parent_path as $parent_id) {
			$parents[] = $this->getCategoryTree($parent_id);
		}

		return $parents;
	}

	//TODO: Update Categories in Admin to Category Tree style (see front end!)
	public function getCategoriesWithParents($data = array(), $select = '', $delimeter = ' > ')
	{
		$categories = $this->getCategories($data, $select);

		foreach ($categories as &$category) {
			if ($category['parent_id'] > 0) {
				$parents = $this->Model_Catalog_Category->getParents($category['category_id']);

				if (!empty($parents)) {
					$category['pathname'] = implode($delimeter, array_column($parents, 'name')) . $delimeter . $category['name'];
				}
			} else {
				$category['pathname'] = $category['name'];
			}
		}

		return $categories;
	}

	public function getCategoryTree_TODO()
	{
		$categories = $this->getCategories();

		$this->load->resource('tree');

		$tree = new Tree();
		$tree->addNodes($categories, 'category_id');

		$tree->printTree();
	}

	//TODO: Make this into a real OO tree with nodes, array is not the best way....
	public function getCategoryTree($category_id = 0)
	{
		$language_id = option('config_language_id');
		$store_id    = option('store_id');

		$category_tree = $this->cache->get("category.tree.$store_id.$language_id");

		if (!$category_tree) {
			$categories = $this->getCategories();

			$category_tree = array(
				'category_id'      => 0,
				'name'             => _l("All Categories"),
				'description'      => '',
				'meta_description' => '',
				'meta_keywords'    => '',
				'image'            => '',
				'children'         => array(),
				'parent_path'      => '0',
				'parent_id'        => '',
				'depth'            => 0,
			);

			$parent_ref = array();

			foreach ($categories as &$category) {
				$category['children']                 = array();
				$parent_ref[$category['category_id']] = & $category;
			}
			unset($category);

			foreach ($categories as &$category) {
				if ($category['parent_id']) {
					$parent_ref[$category['parent_id']]['children'][$category['category_id']] = & $category;
				} elseif ((int)$category['parent_id'] === 0) {
					$category_tree['children'][$category['category_id']] = & $category;
				}
			}
			unset($category);

			$this->resolvePaths($category_tree['children']);

			$this->cache->set("category.tree.$store_id.$language_id", $category_tree);
		}

		if ($category_id) {
			return array_search_key('category_id', $category_id, $category_tree);
		}

		return $category_tree;
	}

	private function resolvePaths(&$category_tree, $parent_path = '0', $path = '', $depth = 0, $delimeter = ' > ')
	{
		foreach ($category_tree as &$category) {
			$category['depth']       = $depth;
			$category['pathname']    = $path . $category['name'];
			$category['parent_path'] = $parent_path;

			if (!empty($category['children'])) {
				$this->resolvePaths($category['children'], $parent_path . ',' . $category['category_id'], $path . $category['name'] . $delimeter, $depth + 1, $delimeter);
			}
		}
	}

	public function getCategoryTranslations($category_id)
	{
		$translate_fields = array(
			'name',
			'meta_keywords',
			'meta_description',
			'description',
		);

		return $this->translation->getTranslations('category', $category_id, $translate_fields);
	}

	public function getCategoryName($category_id)
	{
		$category = $this->queryRow("SELECT name FROM " . DB_PREFIX . "category WHERE category_id='" . (int)$category_id . "'");

		$this->translation->translate('category', $category_id, $category);

		return $category['name'];
	}

	public function getCategoryLayoutId($category_id)
	{
		return $this->queryVar("SELECT layout_id FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "' AND store_id = '" . (int)option('store_id') . "'");
	}

	public function getCategoryStores($category_id)
	{
		$category_store_data = array();

		$query = $this->query("SELECT * FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['store_id'];
		}

		return $category_store_data;
	}

	public function getCategoryLayouts($category_id)
	{
		$category_layout_data = array();

		$query = $this->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $category_layout_data;
	}

	public function getTotalCategories($data = array())
	{
		return $this->getCategories($data, '', true);
	}
}
