<?php

class Block extends Library
{
	private $blocks;

	public function add($data)
	{
		if (!validate('text', $data['name'], 3, 128)) {
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
		$class_name = "Block_" . _2camel($parts[0]) . '_' . _2camel($parts[1]);

		/**
		 * Add Backend Files
		 */

		//Admin Controller File
		$controller_template = $dir_templates . 'app/view/block/' . $data['path'] . '.php';
		$controller_file     = DIR_SITE . 'app/controller/block/' . $data['path'] . '.php';

		$insertables = array(
			'path'           => $data['path'],
			'class_name'     => "App_Controller_Admin_" . $class_name,
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

		$content = insertables($insertables, $content, '__', '__');

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
		$controller_file     = DIR_SITE . 'app/controller/block/' . $data['path'] . '.php';

		$content = file_get_contents($controller_template);

		$insertables = array(
			'path'       => $data['path'],
			'class_name' => "App_Controller_" . $class_name,
		);

		$content = insertables($insertables, $content, '__', '__');

		$error = null;
		if (!_is_writable(dirname($controller_file))) {
			trigger_error($error);
		}

		file_put_contents($controller_file, $content);

		//Front Template Files
		if (!empty($data['themes'])) {
			$front_template = $dir_templates . 'front_template.tpl';

			foreach ($data['themes'] as $theme) {
				$template_file = DIR_SITE . 'app/view/theme/' . $theme . '/template/block/' . $data['path'] . '.tpl';

				if (!_is_writable(dirname($template_file))) {
					trigger_error(_l("%s is not writable", $template_file));
					continue;
				}

				$content = file_get_contents($front_template);

				$insertables = array(
					'slug' => slug($data['path']),
				);

				$content = insertables($insertables, $content, '__', '__');

				file_put_contents($template_file, $content);
			}
		}

		$this->cache->delete('block');
	}

	public function edit($path, $data)
	{
		if (!$this->exists($path)) {
			$this->error = _l("Block %s does not exist!", $path);
			return false;
		}

		$data['settings'] = isset($data['settings']) ? serialize($data['settings']) : '';

		$data['path'] = $path;

		$block_id = $this->queryVar("SELECT block_id FROM " . DB_PREFIX . "block WHERE `path` = '" . $this->escape($path) . "' LIMIT 1");

		if (!$block_id) {
			$block_id = $this->insert('block', $data);
		} else {
			$this->update('block', $data, $block_id);
		}

		if (isset($data['instances'])) {
			$this->delete('block_instance', array('path' => $path));

			foreach ($data['instances'] as &$instance) {
				$instance['name'] = slug($instance['name'], '-');

				$duplicates = 0;

				foreach ($data['instances'] as $i) {
					if ($i['name'] === $instance['name']) {
						$duplicates++;
					}
				}

				if ($duplicates > 1) {
					$instance['name'] .= '-' . $duplicates;
				}

				$instance['path'] = $path;

				$instance['settings'] = !empty($instance['settings']) ? serialize($instance['settings']) : '';

				$this->insert('block_instance', $instance);
			}
			unset($instance);
		}

		$this->cache->delete('block');

		return true;
	}

	public function remove($path)
	{
		echo "NOT IPMLEMENTED";
		exit;
		$files = array(
			DIR_SITE . 'app/controller/block/' . $path . '.php',
			DIR_THEMES . 'default/template/block/' . $path . '_settings.tpl',
			DIR_THEMES . 'default/template/block/' . $path . '_profile.tpl',
			DIR_SITE . 'app/controller/block/' . $path . '.php',
		);

		$themes = $this->theme->getThemes();

		foreach ($themes as $theme) {
			$files[] = DIR_SITE . 'app/view/theme/' . $theme['name'] . '/template/block/' . $path . '.tpl';
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
		if (!isset($this->blocks[$path])) {
			$block = cache('block.' . slug($path));

			if (is_null($block)) {
				$block = $this->queryRow("SELECT * FROM " . DB_PREFIX . "block WHERE `path` = '" . $this->escape($path) . "'");

				if ($block) {
					$block['name']      = $this->getName($path);
					$block['settings']  = !empty($block['settings']) ? unserialize($block['settings']) : array();
					$block['instances'] = $this->loadInstances($path);
				} else {
					$block = array(
						'path'      => $path,
						'name'      => $this->getName($path),
						'settings'  => array(),
						'instances' => array(),
						'status'    => 0,
					);
				}

				cache('block.' . slug($path), $block);
			}

			$this->blocks[$path] = $block;
		}

		return $this->blocks[$path];
	}

	public function getBlocks($filter = array(), $total = false)
	{
		static $blocks = array();

		if (!$blocks) {
			$block_files = get_files(DIR_SITE . 'app/controller/block/', 'php', FILELIST_RELATIVE);

			foreach ($block_files as $file) {
				$path = str_replace('.php', '', $file);

				if ($path === 'block') {
					continue;
				}

				$blocks[] = $path;
			}

			$this->cleanDb($blocks);
		}

		if ($total) {
			return count($blocks);
		}

		$block_list = array();

		foreach ($blocks as $path) {
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

			if (!$block) {
				$block = array(
					'path'      => $path,
					'name'      => $this->getName($path),
					'settings'  => array(),
					'instances' => array(),
					'status'    => 1,
				);
			}

			$block_list[] = $block;
		}

		if (isset($filter['sort'])) {
			uasort($block_list, function ($a, $b) use ($filter) {
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

		$block_list = array_slice($block_list, $start, $limit);

		return $block_list;
	}

	public function getTotalBlocks($filter = array())
	{
		return $this->getBlocks($filter, true);
	}

	public function getSettings($path)
	{
		if (!isset($this->blocks[$path])) {
			$this->blocks[$path] = $this->get($path);
		}

		return isset($this->blocks[$path]) ? $this->blocks[$path]['settings'] : array();
	}

	public function getInstance($path, $instance_name = null)
	{
		if (!isset($this->blocks[$path])) {
			$this->blocks[$path] = $this->get($path);
		}

		if ($instance_name) {
			return isset($this->blocks[$path]['instances'][$instance_name]) ? $this->blocks[$path]['instances'][$instance_name] : null;
		}

		return $this->blocks[$path]['instances'];
	}

	private function loadInstances($path)
	{
		$instances = $this->queryRows("SELECT * FROM " . DB_PREFIX . "block_instance WHERE `path` = '" . $this->escape($path) . "'", 'name');

		foreach ($instances as &$instance) {
			$instance['settings'] = unserialize($instance['settings']);
		}
		unset($instance);

		return $instances;
	}

	public function render($path, $instance_name = null, $settings = array())
	{
		if (!is_array($settings)) {
			$settings = array();
		}

		$block = 'block/' . $path;

		if ($instance_name) {
			$instance = $this->getInstance($path, $instance_name);

			if (!$instance) {
				$link = site_url('admin/block/form', 'path=' . urlencode($path));
				trigger_error(_l("%s(): Block Instance not found for %s: %s. Please <a href=\"%s\" target=\"_blank\">click here to create this instance</a> first!", __METHOD__, $path, $instance_name, $link));
				return '';
			}

			$settings += $instance;
		}

		$settings += $this->getSettings($path);

		$action = new Action($block . '/build', array('settings' => $settings));

		if ($action->execute()) {
			return $action->getOutput();
		} else {
			trigger_error(_l("%s(): Could not load block %s! The file was missing.", __METHOD__, $block));
		}
	}

	public function exists($path)
	{
		return is_file(DIR_SITE . 'app/controller/block/' . $path . '.php');
	}

	public function getName($path)
	{
		$directives = get_comment_directives(DIR_SITE . 'app/controller/block/' . $path . '.php');

		return !empty($directives['name']) ? $directives['name'] : $path;
	}

	public function cleanDb($blocks)
	{
		$this->query("DELETE FROM " . DB_PREFIX . "block WHERE path NOT IN('" . implode("','", $blocks) . "')");

		if ($this->countAffected()) {
			$this->cache->delete('block');
		}
	}
}
