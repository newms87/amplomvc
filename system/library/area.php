<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class Area extends Library
{
	static $counts;

	//TODO: This whole class needs caching optimization


	public function hasBlocks($area, $layout_id = null)
	{
		if ($layout_id === null) {
			$layout_id = option('config_default_layout', 0);
		}

		self::$counts = cache('area.counts.' . $layout_id);

		if (self::$counts === null) {
			$counts = $this->queryRows("SELECT area, COUNT(*) as total FROM {$this->t['block_area']} WHERE layout_id = " . (int)$layout_id . " GROUP BY area");

			if ($counts) {
				foreach ($counts as $count) {
					self::$counts[$count['area']] = $count['total'];
				}
			} else {
				self::$counts = false;
			}

			cache('area.counts.' . $layout_id, self::$counts);
		}

		return isset(self::$counts[$area]) ? self::$counts[$area] : false;
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

		return $this->queryRows("SELECT * FROM {$this->t['block_area']} WHERE layout_id = " . (int)$layout_id . " AND area = '" . $this->escape($area) . "' ORDER BY sort_order ASC", 'instance_name');
	}

	public function render($area)
	{
		if ($this->hasBlocks($area)) {
			return call('area/' . $area);
		}

		return '';
	}
}
