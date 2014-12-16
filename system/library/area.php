<?php
class Area extends Library
{
	static $counts;

	//TODO: This whole class needs caching optimization


	public function hasBlocks($area, $layout_id = null)
	{
		if (is_null($layout_id)) {
			$layout_id = option('config_layout_id');

			self::$counts = cache('area.counts.'.$layout_id);

			if (is_null(self::$counts)) {

				if (!$layout_id) {
					$layout_id = option('config_default_layout', 0);
				}

				$counts = $this->queryRows("SELECT area, COUNT(*) as total FROM " . DB_PREFIX . "block_area WHERE layout_id = " . (int)$layout_id . " GROUP BY area");

				if ($counts) {
					foreach ($counts as $count) {
						self::$counts[$count['area']] = $count['total'];
					}
				} else {
					self::$counts = false;
				}

				cache('area.counts.'.$layout_id, self::$counts);
			}

			return isset(self::$counts[$area]) ? self::$counts[$area] : false;
		}

		return $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "block_area WHERE layout_id = " . (int)$layout_id);
	}

	public function setBlocks($area, $layout_id, $blocks, $path = null)
	{
		$this->delete('block_area', array('area' => $area));

		foreach ($blocks as $block) {
			$block_area = array(
				'path'          => $path ? $path : $block['path'],
				'instance_name' => $block['instance_name'],
				'area'          => $area,
				'layout_id'     => $layout_id,
				'sort_order'    => $block['sort_order'],
			);

			$this->insert('block_area', $block_area);
		}
	}

	public function getBlocks($area, $layout_id = null)
	{
		if (!$layout_id) {
			$layout_id = option('config_layout_id');
		}

		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "block_area WHERE layout_id = " . (int)$layout_id . " AND area = '" . $this->escape($area) . "' ORDER BY sort_order ASC", 'instance_name');
	}

	public function render($area)
	{
		if ($this->hasBlocks($area)) {
			return call('area/' . $area);
		}

		return '';
	}
}
