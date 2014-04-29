<?php
class Catalog_Model_Catalog_Category extends Model
{
	public function getCategory($category_id)
	{
		$category = $this->queryRow(
			"SELECT * FROM " . DB_PREFIX . "category c" .
			" LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)" .
			" WHERE c.category_id = '" . (int)$category_id . "' AND c2s.store_id = '" . (int)option('config_store_id') . "' AND c.status = '1'"
		);

		if ($category) {
			$this->translation->translate('category', $category_id, $category);
		}

		return $category;
	}

	public function getCategories($data = array(), $select = '', $total = false)
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
		$where = "c2s.store_id = " . (int)option('config_store_id') . " AND c.status = 1";

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

	//TODO: Make this into a real OO tree with nodes, array is not the best way....
	public function getCategoryTree($category_id = 0)
	{
		$language_id = option('config_language_id');
		$store_id    = option('config_store_id');

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

	public function getCategoryName($category_id)
	{
		$category = $this->queryRow("SELECT name FROM " . DB_PREFIX . "category WHERE category_id='" . (int)$category_id . "'");

		$this->translation->translate('category', $category_id, $category);

		return $category['name'];
	}

	public function getCategoryLayoutId($category_id)
	{
		return $this->queryVar("SELECT layout_id FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "' AND store_id = '" . (int)option('config_store_id') . "'");
	}
}
