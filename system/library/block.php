<?php

class Block extends Library
{
	private $blocks;

	function __construct(&$registry)
	{
		parent::__construct($registry);

		if (!$this->config->isAdmin()) {
			$this->loadBlocks();
		}
	}

	public function add($data)
	{
		if (!$this->validation->text($data['name'], 3, 128)) {
			$this->error['name'] = _l("Block name must be between 1 and 128 characters!");
			return false;
		}

		if (empty($data['path']) || !preg_match("/^[a-z0-9_]+\\/[a-z0-9_]+\$/i", $data['path'])) {
			$this->error['path'] = _l("Block Path must be in the form widget/myblock containing characters a-z, 0-9, or _");
			return false;
		}

		$dir_templates = DIR_RESOURCES . 'templates/add_block/';

		$data['path'] = strtolower($data['path']);

		$parts      = explode('/', $data['path']);
		$class_name = "Block_" . $this->tool->_2CamelCase($parts[0]) . '_' . $this->tool->_2CamelCase($parts[1]);

		/**
		 * Add Backend Files
		 */

		//Admin Controller File
		$controller_template = $dir_templates . 'admin_controller.php';
		$controller_file     = DIR_SITE . 'admin/controller/block/' . $data['path'] . '.php';

		$insertables = array(
			'path'           => $data['path'],
			'class_name'     => "Admin_Controller_" . $class_name,
			'settings_start' => '',
			'settings_end'   => '',
			'profile_start'  => '',
			'profile_end'    => '',
		);

		if (empty($data['settings_file'])) {
			$insertables['settings_start'] = '/*';
			$insertables['settings_end']   = '*/';
		}

		if (empty($data['profiles_file'])) {
			$insertables['profile_start'] = '/*';
			$insertables['profile_end']   = '*/';
		}

		$content = file_get_contents($controller_template);

		$content = $this->tool->insertables($insertables, $content, '__', '__');

		$error = null;
		if (!_is_writable(dirname($controller_file))) {
			trigger_error($error);
		}

		file_put_contents($controller_file, $content);

		//Profile Template File
		$profiles_template = $dir_templates . 'profile.tpl';
		$profiles_file     = DIR_THEMES . 'default/template/block/' . $data['path'] . '_profile.tpl';

		$error = null;
		if (!_is_writable(dirname($profiles_file))) {
			trigger_error($error);
		}

		copy($profiles_template, $profiles_file);

		//Settings Template File
		$settings_template = $dir_templates . 'settings.tpl';
		$settings_file     = DIR_THEMES . 'default/template/block/' . $data['path'] . '_settings.tpl';

		$error = null;
		if (!_is_writable(dirname($settings_file))) {
			trigger_error($error);
		}

		copy($settings_template, $settings_file);


		/**
		 * Add Front End Files
		 */

		//Front Controller File
		$controller_template = $dir_templates . 'front_controller.php';
		$controller_file     = DIR_SITE . 'catalog/controller/block/' . $data['path'] . '.php';

		$content = file_get_contents($controller_template);

		$insertables = array(
			'path'       => $data['path'],
			'class_name' => "Catalog_Controller_" . $class_name,
		);

		$content = $this->tool->insertables($insertables, $content, '__', '__');

		$error = null;
		if (!_is_writable(dirname($controller_file))) {
			trigger_error($error);
		}

		file_put_contents($controller_file, $content);

		//Front Template Files
		if (!empty($data['themes'])) {
			$front_template = $dir_templates . 'front_template.tpl';

			foreach ($data['themes'] as $theme) {
				$template_file = DIR_SITE . 'catalog/view/theme/' . $theme . '/template/block/' . $data['path'] . '.tpl';

				if (!_is_writable(dirname($template_file))) {
					trigger_error(_l("%s is not writable", $template_file));
					continue;
				}

				$content = file_get_contents($front_template);

				$insertables = array(
					'slug' => $this->tool->getSlug($data['path']),
				);

				$content = $this->tool->insertables($insertables, $content, '__', '__');

				file_put_contents($template_file, $content);
			}
		}

		$this->cache->delete('block');
	}

	public function edit($path, $data)
	{
		if (!$this->block_exists($path)) {
			$this->error = _l("Block %s does not exist!", $path);
			return false;
		}

		if (isset($data['settings'])) {
			$data['settings'] = serialize($data['settings']);
		}

		if (isset($data['profiles'])) {
			$data['profiles'] = serialize($data['profiles']);
		}

		$data['path'] = $path;

		$this->update('block', $data, array('path' => $path));

		if (isset($data['instances'])) {
			$this->delete('block_instance', array('path' => $path));

			foreach ($data['instances'] as $instance) {
				$instance['path']     = $path;
				$instance['settings'] = serialize($instance['settings']);

				$this->insert('block_instance', $instance);
			}
		}

		$this->cache->delete('block');

		return true;
	}

	public function remove($path)
	{
		$files = array(
			DIR_SITE . 'catalog/controller/block/' . $path . '.php',
			DIR_THEMES . 'default/template/block/' . $path . '_settings.tpl',
			DIR_THEMES . 'default/template/block/' . $path . '_profile.tpl',
			DIR_SITE . 'admin/controller/block/' . $path . '.php',
		);

		$languages = $this->language->getLanguages();

		foreach ($languages as $language) {
			$files[] = DIR_SITE . 'admin/language/' . $language['directory'] . '/block/' . $path . '.php';
			$files[] = DIR_SITE . 'catalog/language/' . $language['directory'] . '/block/' . $path . '.php';
		}

		$themes = $this->theme->getThemes();

		foreach ($themes as $theme) {
			$files[] = DIR_SITE . 'catalog/view/theme/' . $theme['name'] . '/template/block/' . $path . '.tpl';
		}

		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}

			clearstatcache();

			if (is_dir(dirname($file))) {
				$dir_files = scandir(dirname($file));

				if (!empty($dir_files)) {
					$dir_files = array_diff($dir_files, array(
						'..',
						'.'
					));
				}

				if (empty($dir_files)) {
					rmdir(dirname($file));
				}
			}
		}

		$this->cache->delete('block');
	}

	public function get($path)
	{
		$block = $this->queryRow("SELECT * FROM " . DB_PREFIX . "block WHERE `path` = '" . $this->escape($path) . "'");

		if ($block) {
			$block['name']      = $this->getName($path);
			$block['settings']  = !empty($block['settings']) ? unserialize($block['settings']) : array();
			$block['profiles']  = !empty($block['profiles']) ? unserialize($block['profiles']) : array();
			$block['instances'] = $this->getInstances($path);
		} else {
			$block = array(
				'path'      => $path,
				'name'      => $this->getName($path),
				'settings'  => array(),
				'instances' => array(),
				'profiles'  => array(),
				'status'    => 0,
			);
		}

		return $block;
	}

	public function getBlocks($filter = array(), $total = false)
	{
		$block_files = glob(DIR_SITE . 'admin/controller/block/*/*.php');

		$this->cleanDb($block_files);

		if ($total) {
			return count($block_files);
		}

		$blocks = array();

		foreach ($block_files as &$file) {
			$path  = preg_replace("/.*[\\/\\\\]/", '', dirname($file)) . '/' . preg_replace("/.php\$/", '', basename($file));
			$block = $this->get($path);

			//filter name
			if (!empty($filter['path'])) {
				if (!preg_match("/.*$filter[path].*/i", $block['path'])) {
					continue;
				}
			}

			//filter display_name
			if (!empty($filter['name'])) {
				if (!preg_match("/.*$filter[name].*/i", $block['name'])) {
					continue;
				}
			}

			//filter status
			if (isset($filter['status'])) {
				if ((bool)$filter['status'] != (bool)$block['status']) {
					continue;
				}
			}

			//Filter Layout
			if (isset($filter['layouts'])) {
				$found = false;
				foreach ($block['profiles'] as $profile) {
					foreach ($profile['layout_ids'] as $layout_id) {
						if (in_array($layout_id, $filter['layouts'])) {
							$found = true;
							break;
						}
					}
				}

				if (!$found) {
					continue;
				}
			}

			//Filter Stores
			if (isset($filter['stores'])) {
				$found = false;

				foreach ($block['profiles'] as $profile) {
					foreach ($profile['store_ids'] as $store_id) {
						if (in_array($store_id, $filter['stores'])) {
							$found = true;
							break;
						}
					}
				}

				if (!$found) {
					continue;
				}
			}

			if (!$block) {
				$block = array(
					'path'      => $path,
					'name'      => $this->getName($path),
					'settings'  => array(),
					'instances' => array(),
					'profiles'  => array(),
					'status'    => 1,
				);
			}

			$blocks[] = $block;
		}

		if (isset($filter['sort'])) {
			uasort($blocks, function ($a, $b) use ($filter) {
				if (!empty($filter['order']) && $filter['order'] === 'DESC') {
					return $a[$filter['sort']] < $b[$filter['sort']];
				} else {
					return $a[$filter['sort']] > $b[$filter['sort']];
				}
			});
		}

		//Limits
		$start = isset($filter['start']) ? (int)$filter['start'] : 0;
		$limit = isset($filter['limit']) ? $start + (int)$filter['limit'] : null;

		$blocks = array_slice($blocks, $start, $limit);

		return $blocks;
	}

	public function getTotalBlocks($filter = array())
	{
		return $this->getBlocks($filter, true);
	}

	private function loadBlocks()
	{
		$store_id  = $this->config->get('config_store_id');
		$layout_id = $this->config->get('config_layout_id');

		$blocks = $this->cache->get("blocks.$store_id.$layout_id");

		//TODO: We can optimize this to grab cached blocks, then process the data. Should minimize cache file size, and we can use only 1 cache file.
		if (is_null($blocks)) {
			$block_list = $this->queryRows("SELECT * FROM " . DB_PREFIX . "block WHERE status = '1'");

			$blocks = array('position' => array());

			foreach ($block_list as $block) {
				$block['settings']  = $block['settings'] ? unserialize($block['settings']) : array();
				$block['instances'] = $block['instances'] ? unserialize($block['instances']) : array();
				$block['profiles']  = $block['profiles'] ? unserialize($block['profiles']) : array();

				if (!empty($block['profiles'])) {
					foreach ($block['profiles'] as $profile) {
						if (in_array($store_id, $profile['store_ids'])) {
							//Load this profiles settings
							if (isset($profile['profile_setting_id']) && isset($block['instance'][$profile['profile_setting_id']])) {
								$profile += $block['instances'][$profile['profile_setting_id']];
							}

							$blocks[$block['path']]            = $block;
							$blocks[$block['path']]['profile'] = $profile;

							//Automatically loaded blocks for this layout
							if (in_array($layout_id, $profile['layout_ids'])) {
								$blocks['position'][$profile['position']][$block['path']] = & $blocks[$block['path']];
							}
						}
					}
				}
			}

			$this->cache->set("blocks.$store_id.$layout_id", $blocks);
		}

		$this->blocks = $blocks;
	}

	public function getSettings($path)
	{
		return isset($this->blocks[$path]) ? $this->blocks[$path]['settings'] : null;
	}

	public function getProfiles($path)
	{
		return isset($this->blocks[$path]) ? $this->blocks[$path]['profiles'] : null;
	}

	public function getInstance($path, $instance_id)
	{
		if (!isset($this->blocks[$path])) {
			$this->blocks[$path] = $this->get($path);
		}

		return $this->blocks[$path]['instances'][$instance_id];
	}

	public function getInstances($path)
	{
		$instances = $this->queryRows("SELECT * FROM " . DB_PREFIX . "block_instance WHERE `path` = '" . $this->escape($path) . "'");

		foreach ($instances as &$instance) {
			$instance['settings'] = unserialize($instance['settings']);
		}

		return $instances;
	}

	public function getInstancesFor($position)
	{
		if (isset($this->blocks['position'][$position])) {
			return $this->blocks['position'][$position];
		}

		return array();
	}

	public function render($path, $args = array(), $settings = null)
	{
		if (!is_array($args)) {
			trigger_error(_l("%s(): \$args must be an array of arguments to pass to the block %s.", __METHOD__, $path));
			exit();
		}

		$block = 'block/' . $path;

		if (is_null($settings)) {
			$settings = $this->getSettings($path);
		}

		if ($settings) {
			$settings = $args + $settings;
		} else {
			$settings = $args;
		}

		$action = new Action($this->registry, $block, array('settings' => $settings));

		if ($action->execute()) {
			return $action->getOutput();
		} else {
			trigger_error(_l("%s(): Could not load block %s! The file was missing.", __METHOD__, $block));
		}
	}

	public function exists($path)
	{
		return is_file(DIR_SITE . 'admin/controller/block/' . $path . '.php');
	}

	public function getName($path)
	{
		$directives = $this->tool->getFileCommentDirectives(DIR_SITE . 'admin/controller/block/' . $path . '.php');

		return !empty($directives['name']) ? $directives['name'] : $path;
	}

	public function cleanDb($valid_files)
	{
		$paths = array();

		foreach ($valid_files as $file) {
			$paths[] = preg_replace("/.*[\\/\\\\]/", '', dirname($file)) . '/' . preg_replace("/.php\$/", '', basename($file));
		}

		$this->query("DELETE FROM " . DB_PREFIX . "block WHERE path NOT IN('" . implode("','", $paths) . "')");

		if ($this->countAffected()) {
			$this->cache->delete('block');
		}
	}
}
