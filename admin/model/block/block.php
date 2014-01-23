<?php
class Admin_Model_Block_Block extends Model
{
	public function addBlock($data)
	{
		$dir_templates = DIR_RESOURCES . 'templates/add_block/';

		$data['route'] = strtolower($data['route']);

		$eol = "\r\n";

		$language_dir = $this->language->info('directory');

		$parts      = explode('/', $data['route']);
		$class_name = "Block_" . $this->tool->_2CamelCase($parts[0]) . '_' . $this->tool->_2CamelCase($parts[1]);

		/**
		 * Add Backend Files
		 */

		//Admin Controller File
		$controller_template = $dir_templates . 'admin_controller.php';
		$controller_file     = SITE_DIR . 'admin/controller/block/' . $data['route'] . '.php';

		$insertables = array(
			'route'          => $data['route'],
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

		_is_writable(dirname($controller_file));

		file_put_contents($controller_file, $content);

		$language_template = $dir_templates . 'admin_language.php';
		$language_file     = SITE_DIR . 'admin/language/' . $language_dir . '/block/' . $data['route'] . '.php';

		$insertables = array(
			'head_title' => $data['name'],
		);

		$content = file_get_contents($language_template);

		$content = $this->tool->insertables($insertables, $content, '__', '__');

		_is_writable(dirname($language_file));

		file_put_contents($language_file, $content);

		//Profile Template File
		$profiles_template = $dir_templates . 'profile.tpl';
		$profiles_file     = DIR_THEME . 'default/template/block/' . $data['route'] . '_profile.tpl';

		_is_writable(dirname($profiles_file));

		copy($profiles_template, $profiles_file);

		//Settings Template File
		$settings_template = $dir_templates . 'settings.tpl';
		$settings_file     = DIR_THEME . 'default/template/block/' . $data['route'] . '_settings.tpl';

		_is_writable(dirname($settings_file));

		copy($settings_template, $settings_file);


		/**
		 * Add Front End Files
		 */

		//Front Controller File
		$controller_template = $dir_templates . 'front_controller.php';
		$controller_file     = SITE_DIR . 'catalog/controller/block/' . $data['route'] . '.php';

		$content = file_get_contents($controller_template);

		$insertables = array(
			'route'      => $data['route'],
			'class_name' => "Catalog_Controller_" . $class_name,
		);

		$content = $this->tool->insertables($insertables, $content, '__', '__');

		_is_writable(dirname($controller_file));

		file_put_contents($controller_file, $content);

		//Front Language file
		$language_template = $dir_templates . 'front_language.php';
		$language_file     = SITE_DIR . 'catalog/language/' . $language_dir . '/block/' . $data['route'] . '.php';

		$insertables = array(
			'head_title' => $data['name'],
		);

		$content = file_get_contents($language_template);

		$content = $this->tool->insertables($insertables, $content, '__', '__');

		_is_writable(dirname($language_file));

		file_put_contents($language_file, $content);

		//Front Template Files
		if (!empty($data['themes'])) {
			$front_template = $dir_templates . 'front_template.tpl';

			foreach ($data['themes'] as $theme) {
				$template_file = SITE_DIR . 'catalog/view/theme/' . $theme . '/template/block/' . $data['route'] . '.tpl';

				_is_writable(dirname($template_file));

				$content = file_get_contents($front_template);

				$insertables = array(
					'slug' => $this->tool->getSlug($data['route']),
				);

				$content = $this->tool->insertables($insertables, $content, '__', '__');

				file_put_contents($template_file, $content);
			}
		}

		$this->cache->delete('block');
	}

	public function updateBlock($path, $data)
	{
		$this->delete('block', array('path' => $path));

		if (isset($data['settings'])) {
			$data['settings'] = serialize($data['settings']);
		}

		if (isset($data['profile_settings'])) {
			$data['profile_settings'] = serialize($data['profile_settings']);
		}

		if (isset($data['profiles'])) {
			$data['profiles'] = serialize($data['profiles']);
		}

		$data['path'] = $path;

		$this->insert('block', $data);

		$this->cache->delete('block');
	}

	public function deleteBlock($path)
	{
		$files = array(
			SITE_DIR . 'catalog/controller/block/' . $path . '.php',
			DIR_THEME . 'default/template/block/' . $path . '_settings.tpl',
			DIR_THEME . 'default/template/block/' . $path . '_profile.tpl',
			SITE_DIR . 'admin/controller/block/' . $path . '.php',
		);

		$languages = $this->language->getLanguages();

		foreach ($languages as $language) {
			$files[] = SITE_DIR . 'admin/language/' . $language['directory'] . '/block/' . $path . '.php';
			$files[] = SITE_DIR . 'catalog/language/' . $language['directory'] . '/block/' . $path . '.php';
		}

		$themes = $this->theme->getThemes();

		foreach ($themes as $theme) {
			$files[] = SITE_DIR . 'catalog/view/theme/' . $theme['name'] . '/template/block/' . $path . '.tpl';
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

	public function getBlockName($path)
	{
		$directives = $this->tool->getFileCommentDirectives(SITE_DIR . 'admin/controller/block/' . $path . '.php');

		return !empty($directives['name']) ? $directives['name'] : $path;
	}

	public function isBlock($path)
	{
		return is_file(SITE_DIR . 'admin/controller/block/' . $path . '.php');
	}


	//TODO: Change $name to $path
	public function getBlock($path)
	{
		$block = $this->queryRow("SELECT * FROM " . DB_PREFIX . "block WHERE `path` = '" . $this->escape($path) . "'");

		if ($block) {
			if (!empty($block['settings'])) {
				$block['settings'] = unserialize($block['settings']);
			} else {
				$block['settings'] = array();
			}

			if (!empty($block['profile_settings'])) {
				$block['profile_settings'] = unserialize($block['profile_settings']);
			} else {
				$block['profile_settings'] = array();
			}

			if (!empty($block['profiles'])) {
				$block['profiles'] = unserialize($block['profiles']);
			} else {
				$block['profiles'] = array();
			}
		} else {
			$block = array(
				'path'             => $path,
				'settings'         => array(),
				'profile_settings' => array(),
				'profiles'         => array(),
				'status'           => 0,
			);
		}

		return $block;
	}

	public function getBlocks($filter = array(), $total = false)
	{
		$block_files = glob(SITE_DIR . 'admin/controller/block/*/*.php');

		$this->cleanDb($block_files);

		if ($total) {
			return count($block_files);
		}

		$blocks = array();

		foreach ($block_files as &$file) {
			$path  = preg_replace("/.*[\/\\\\]/", '', dirname($file)) . '/' . preg_replace("/.php\$/", '', basename($file));
			$block = $this->getBlock($path);

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
					'path'             => $path,
					'name'             => $this->getBlockName($path),
					'settings'         => array(),
					'profile_settings' => array(),
					'profiles'         => array(),
					'status'           => 1,
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

	public function cleanDb($valid_files)
	{
		$paths = array();

		foreach ($valid_files as $file) {
			$paths[] = preg_replace("/.*[\/\\\\]/", '', dirname($file)) . '/' . preg_replace("/.php\$/", '', basename($file));
		}

		$this->query("DELETE FROM " . DB_PREFIX . "block WHERE path NOT IN('" . implode("','", $paths) . "')");

		if ($this->countAffected()) {
			$this->cache->delete('block');
		}
	}
}
