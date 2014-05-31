<?php
class App_Model_Design_Layout extends Model
{
	public function addLayout($data)
	{
		$layout_id = $this->insert('layout', $data);

		if (!empty($data['routes'])) {
			foreach ($data['routes'] as $route) {
				$route['layout_id'] = $layout_id;

				$this->insert('layout_route', $route);
			}
		}

		return $layout_id;
	}

	public function editLayout($layout_id, $data, $strict = false)
	{
		$this->update('layout', $data, $layout_id);

		if (isset($data['routes']) || !$strict) {
			$this->delete('layout_route', array('layout_id' => $layout_id));
		}

		if (!empty($data['routes'])) {
			foreach ($data['routes'] as $route) {
				$route['layout_id'] = $layout_id;

				$this->insert('layout_route', $route);
			}
		}
	}

	public function deleteLayout($layout_id)
	{
		$this->delete('layout', $layout_id);
		$this->delete('layout_route', array('layout_id' => $layout_id));
		$this->delete('layout_header', array('layout_id' => $layout_id));
		$this->delete('category_to_layout', array('layout_id' => $layout_id));
		$this->delete('product_to_layout', array('layout_id' => $layout_id));
		$this->delete('information_to_layout', array('layout_id' => $layout_id));
	}

	public function getLayout($layout_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "layout WHERE layout_id = '" . (int)$layout_id . "'");
	}

	public function getLayouts($data = array(), $select = '*', $total = false)
	{
		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		} elseif (!$select) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "layout l";

		//Where
		$where = "1";

		if (!empty($data['name'])) {
			$where .= " AND LCASE(l.name) like '%" . $this->escape(strtolower($data['name'])) . "%'";
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
		}

		return $result->rows;
	}

	public function getLayoutRoutes($layout_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "layout_route WHERE layout_id = '" . (int)$layout_id . "'");
	}

	public function getTotalLayouts($data = array())
	{
		return $this->getLayouts($data, null, true);
	}
}
