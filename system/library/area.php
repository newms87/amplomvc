<?php
class Area extends Library
{
	private $counts;

	public function hasBlocks($area, $store_id = null, $layout_id = null)
	{
		if (is_null($store_id) && is_null($layout_id)) {
			if (!$this->counts) {
				$store_id = $this->config->get('config_store_id');
				$layout_id = $this->config->get('config_layout_id');

				$counts = $this->queryRows("SELECT area, COUNT(*) as total FROM " . DB_PREFIX . "block_area WHERE store_id = $store_id AND layout_id = $layout_id GROUP BY area");

				foreach ($counts as $count) {
					$this->counts[$count['area']] = $count['total'];
				}
			}

			return $this->counts[$area];
		}

		return $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "block_area WHERE store_id = " . (int)$store_id . " AND layout_id = " . (int)$layout_id);
	}

	public function setBlocks($area, $store_id, $layout_id, $blocks, $path = null)
	{
		$this->delete('block_area', array('area' => $area));

		foreach ($blocks as $block) {
			$block_area = array(
				'path'          => $path ? $path : $block['path'],
				'instance_name' => $block['instance_name'],
				'area'          => $area,
				'layout_id'     => $layout_id,
				'store_id'      => $store_id,
				'sort_order'    => $block['sort_order'],
			);

			$this->insert('block_area', $block_area);
		}
	}

	public function getBlocks($area, $store_id = null, $layout_id = null)
	{
		if (!$store_id) {
			$store_id = $this->config->get('config_store_id');
		}

		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}


		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "block_area WHERE layout_id = " . (int)$layout_id . " AND store_id = " . (int)$store_id . " AND area = '" . $this->escape($area) . "' ORDER BY sort_order ASC", 'instance_name');
	}

	public function render($area)
	{
		if ($this->hasBlocks($area)) {
			return _call('area/' . $area);
		}

		return '';
	}
}
