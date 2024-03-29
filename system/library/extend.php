<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

class Extend extends Library
{
	public function addHook($table, $action, $name, $callback, $param = null, $priority = 0)
	{
		$db_hooks = option('db_hooks');

		if (!is_array($db_hooks)) {
			$db_hooks = array();
		}

		$db_hooks[$table][$action][$name] = array(
			'name'     => $name,
			'callback' => $callback,
			'param'    => $param,
			'priority' => $priority,
		);

		save_option('db_hooks', $db_hooks);
	}

	public function removeHook($table, $action, $name)
	{
		$db_hooks = option('db_hooks');

		unset($db_hooks[$table][$action][$name]);

		if (empty($db_hooks[$table][$action])) {
			unset($db_hooks[$table][$action]);
		}

		if (empty($db_hooks[$table])) {
			unset($db_hooks[$table]);
		}

		save_option('db_hooks', $db_hooks);
	}

	public function enable_image_sorting($table, $column)
	{
		$this->addHook($table, 'insert', '__image_sort__', array('Extend' => 'update_hsv_value'), array(
			$table,
			$column
		));

		$this->addHook($table, 'update', '__image_sort__', array('Extend' => 'update_hsv_value'), array(
			$table,
			$column
		));

		$sort_column = '__image_sort__' . $column;

		$this->addColumn($table, $sort_column, 'FLOAT NULL');

		$key_column = $this->getKeyColumn($table);

		$rows = $this->queryRows("SELECT $key_column, $column, $sort_column FROM " . $this->t[$table]);

		foreach ($rows as $row) {
			$this->update_hsv_value($row, $table, $column, true);

			$update = array(
				$sort_column => $row[$sort_column],
			);

			$where = array(
				$key_column => $row[$key_column],
			);

			$this->update($table, $update, $where);
		}
	}

	public function disableImageSorting($table, $column)
	{
		$this->removeHook($table, 'insert', '__image_sort__');
		$this->removeHook($table, 'update', '__image_sort__');

		$this->dropColumn($table, '__image_sort__' . $column);
	}

	public function update_hsv_value(&$data, $table, $column, $force = false)
	{
		if (!isset($data[$column])) {
			return;
		}

		//If the image has not changed, do nothing.
		if (!$force && $this->queryVar("SELECT COUNT(*) FROM " . $this->t[$table] . " WHERE `$column` = '{$data[$column]}'")) {
			return;
		}

		$width  = option('admin_list_image_width', 80);
		$height = option('admin_list_image_height', 80);

		//Performance Optimization: Much quicker to resize (plus caching) than evaluate color or large image
		$image = str_replace(URL_IMAGE, DIR_IMAGE, image($data[$column], $width, $height));

		$colors = $this->image->get_dominant_color($image);

		$HSV                              = $this->image->RGB_to_HSV($colors['r'], $colors['g'], $colors['b']);
		$data['__image_sort__' . $column] = $HSV['H'];
	}
}
