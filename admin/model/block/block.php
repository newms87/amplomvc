<?php
class Admin_Model_Block_Block extends Model
{
	public function addBlock($data)
	{
		$dir_templates = DIR_SYSTEM . 'resources/templates/add_block/';
		
		$data['route'] = strtolower($data['route']);
		
		$eol = "\r\n";
		
		$language_dir = $this->language->getInfo('directory');
		
		$parts = explode('/', $data['route']);
		$class_name = "Block_" . $this->tool->formatClassname($parts[0]) . '_' . $this->tool->formatClassname($parts[1]);
		
		/**
		 * Add Backend Files
		 */
		 
		//Admin Controller File
		$controller_template = $dir_templates . 'admin_controller.php';
		$controller_file = SITE_DIR . 'admin/controller/block/' . $data['route'] . '.php';
		
		$insertables = array(
			'route' => $data['route'],
			'class_name' => "Admin_Controller_" . $class_name,
			'settings_start' => '',
			'settings_end' => '',
			'profile_start' => '',
			'profile_end' => '',
		);
		
		if (empty($data['settings_file'])) {
			$insertables['settings_start'] = '/*';
			$insertables['settings_end'] = '*/';
		}
			
		if (empty($data['profiles_file'])) {
			$insertables['profile_start'] = '/*';
			$insertables['profile_end'] = '*/';
		}
		
		$content = file_get_contents($controller_template);
		
		$content = $this->tool->insertables($insertables, $content, '__', '__');
		
		_is_writable(dirname($controller_file));
		
		file_put_contents($controller_file, $content);
		
		//Language file
		$language_template = $dir_templates . 'admin_language.php';
		$language_file = SITE_DIR . 'admin/language/' . $language_dir . '/block/' . $data['route'] . '.php';
		
		$insertables = array(
			'heading_title' => $data['name'],
		);
		
		$content = file_get_contents($language_template);
		
		$content = $this->tool->insertables($insertables, $content, '__', '__');
		
		_is_writable(dirname($language_file));
		
		file_put_contents($language_file, $content);
		
		//Profile Template File
		$profiles_template = $dir_templates . 'profile.tpl';
		$profiles_file = DIR_THEME . 'default/template/block/' . $data['route'] . '_profile.tpl';
		
		_is_writable(dirname($profiles_file));
		
		copy($profiles_template, $profiles_file);
		
		//Settings Template File
		$settings_template = $dir_templates . 'settings.tpl';
		$settings_file = DIR_THEME . 'default/template/block/' . $data['route'] . '_settings.tpl';
		
		_is_writable(dirname($settings_file));
		
		copy($settings_template, $settings_file);
		
		
		/**
		 * Add Front End Files
		 */
		 
		//Front Controller File
		$controller_template = $dir_templates . 'front_controller.php';
		$controller_file = SITE_DIR . 'catalog/controller/block/' . $data['route'] . '.php';
		
		$content = file_get_contents($controller_template);
		
		$insertables = array(
			'route' => $data['route'],
			'class_name' => "Catalog_Controller_" . $class_name,
		);
		
		$content = $this->tool->insertables($insertables, $content, '__', '__');
		
		_is_writable(dirname($controller_file));
		
		file_put_contents($controller_file, $content);
		
		//Front Language file
		$language_template = $dir_templates . 'front_language.php';
		$language_file = SITE_DIR . 'catalog/language/' . $language_dir . '/block/' . $data['route'] . '.php';
		
		$insertables = array(
			'heading_title' => $data['name'],
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
	}
	
	public function updateBlock($name, $data)
	{
		$this->delete('block', array('name' => $name));
		
		if (isset($data['settings'])) {
			$data['settings'] = serialize($data['settings']);
		}
		
		if (isset($data['profiles'])) {
			$data['profiles'] = serialize($data['profiles']);
		}
		
		$data['name'] = $name;
		
		$this->insert('block', $data);
		
		$this->cache->delete('block');
	}
	
	public function deleteBlock($name)
	{
		$files = array(
			SITE_DIR . 'catalog/controller/block/' . $name . '.php',
			DIR_THEME . 'default/template/block/' . $name . '_settings.tpl',
			DIR_THEME . 'default/template/block/' . $name . '_profile.tpl',
			SITE_DIR . 'admin/controller/block/' . $name . '.php',
		);
		
		$languages = $this->language->getLanguages();
		
		foreach ($languages as $language) {
			$files[] = SITE_DIR . 'admin/language/' . $language['directory'] . '/block/' . $name . '.php';
			$files[] = SITE_DIR . 'catalog/language/' . $language['directory'] . '/block/' . $name . '.php';
		}
		
		$themes = $this->theme->getThemes();
		
		foreach ($themes as $theme) {
			$files[] = SITE_DIR . 'catalog/view/theme/' . $theme['name'] . '/template/block/' . $name . '.tpl';
		}
		
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}
			
			clearstatcache();
			
			if (is_dir(dirname($file))) {
				$dir_files = scandir(dirname($file));
				
				if (!empty($dir_files)) {
					$dir_files = array_diff($dir_files, array('..','.'));
				}
				
				if (empty($dir_files)) {
					rmdir(dirname($file));
				}
			}
		}
	}
	
	public function isBlock($name)
	{
		return is_file(SITE_DIR . 'admin/controller/block/' . $name . '.php');
	}
	
	public function getBlock($name)
	{
		$block = $this->queryRow("SELECT * FROM " . DB_PREFIX . "block WHERE `name` = '" . $this->escape($name) . "'");
		
		if ($block) {
			if (!empty($block['settings'])) {
				$block['settings'] = unserialize($block['settings']);
			} else {
				$block['settings'] = array();
			}
			
			if (!empty($block['profiles'])) {
				$block['profiles'] = unserialize($block['profiles']);
			} else {
				$block['profiles'] = array();
			}
		}
		else {
			$block = array(
				'name' => $name,
				'settings' => array(),
				'profiles' => array(),
				'status' => 0,
			);
		}
		
		return $block;
	}
	
	public function getBlocks($data = array(), $total = false){
		$block_files = glob(SITE_DIR . 'admin/controller/block/*/*.php');
		
		$this->cleanDb($block_files);
		
		if ($total) {
			return count($block_files);
		}
		
		$blocks = array();
		$sort_order = array();
		
		foreach ($block_files as &$file) {
			$name = preg_replace("/.*[\/\\\\]/",'',dirname($file)) . '/' . preg_replace("/.php\$/",'',basename($file));
			$block = $this->getBlock($name);
			
			$block_language = $this->language->fetch('block/' . $block['name']);
			
			$block['display_name'] = $block_language['heading_title'];
			
			//filter name
			if (!empty($data['name'])) {
				if (!preg_match("/.*$data[name].*/i", $block['name'])) {
					continue;
				}
			}
			
			//filter display_name
			if (!empty($data['display_name'])) {
				if (!preg_match("/.*$data[display_name].*/i", $block['display_name'])) {
					continue;
				}
			}
			
			//filter status
			if (isset($data['status'])) {
				if ((bool)$data['status'] != (bool)$block['status']) {
					continue;
				}
			}
			
			//Filter Layout
			if (isset($data['layouts'])) {
				$found = false;
				foreach ($block['profiles'] as $profile) {
					foreach ($profile['layout_ids'] as $layout_id) {
						if (in_array($layout_id, $data['layouts'])) {
							$found = true;
							break;
						}
					}
				}
				
				if (!$found) { continue; }
			}
			
			//Filter Stores
			if (isset($data['stores'])) {
				$found = false;
				
				foreach ($block['profiles'] as $profile) {
					foreach ($profile['store_ids'] as $store_id) {
						if (in_array($store_id, $data['stores'])) {
							$found = true;
							break;
						}
					}
				}
				
				if (!$found) { continue; }
			}
			
			if (!$block) {
				$block = array(
					'name' => $name,
					'display_name' => $name,
					'settings' => array(),
					'profiles' => array(),
					'status' => 1,
				);
			}
			
			$blocks[] = $block;
		}
		
		if (isset($data['sort'])) {
			uasort($blocks, function($a, $b) use($data) {
				if (!empty($data['order']) && $data['order'] === 'DESC') {
					return $a[$data['sort']] < $b[$data['sort']];
				} else {
					return $a[$data['sort']] > $b[$data['sort']];
				}
			});
		}
		
		//Limits
		$start = isset($data['start']) ? (int)$data['start'] : 0;
		$limit = isset($data['limit']) ? $start + (int)$data['limit'] : null;
		
		$blocks = array_slice($blocks, $start, $limit);
		
		return $blocks;
	}
	
	public function getTotalBlocks($data = array()){
		return $this->getBlocks($data, true);
	}
	
	public function cleanDb($valid_files)
	{
		$names = array();
		
		foreach ($valid_files as &$file) {
			$names[] = preg_replace("/.*[\/\\\\]/",'',dirname($file)) . '/' . preg_replace("/.php\$/",'',basename($file));
		}
		
		$query = $this->query("DELETE FROM " . DB_PREFIX . "block WHERE name NOT IN('" . implode("','",$names) . "')");
		
		if ($this->countAffected()) {
			$this->cache->delete('block');
		}
	}
}
