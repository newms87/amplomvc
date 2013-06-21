<?php
class Admin_Model_Block_Block extends Model 
{
	public function addBlock($data)
	{
		$data['route'] = strtolower($data['route']);
		
		$eol = "\r\n";
		
		$language_dir = $this->language->getInfo('directory');
		
		$parts = explode('/', $data['route']);
		$class_name = "Block_" . $this->tool->format_classname($parts[0]) . '_' . $this->tool->format_classname($parts[1]);
		
		//Admin Controller File
		$controller_template = DIR_THEME . 'default/template/block/template/controller_template.php';
		$controller_file = SITE_DIR . 'admin/controller/block/' . $data['route'] . '.php';
		
		$content = file_get_contents($controller_template);
		
		$insertables = array(
			'route' => $data['route'],
			'class_name' => "Admin_Controller_" . $class_name,
		);
		
		if (empty($data['settings_file'])) {
			$insertables['settings_start'] = '/*';
			$insertables['settings_end'] = '*/';
		} else {
			$insertables['settings_start'] = '';
			$insertables['settings_end'] = '';
		}
		
		if (empty($data['profiles_file'])) {
			$insertables['profile_start'] = '/*';
			$insertables['profile_end'] = '*/';
		} else {
			$insertables['profile_start'] = '';
			$insertables['profile_end'] = '';
		}
		
		$content = $this->tool->insertables($insertables, $content);
		
		_is_writable(dirname($controller_file));
		
		file_put_contents($controller_file, $content);
		
		//Language file
		$language_file = SITE_DIR . 'admin/language/' . $language_dir . '/block/' . $data['route'] . '.php';
		
		$content =
			"<?php" . $eol .
			"//Heading" . $eol .
			"\$_['heading_title'] = \"$data[name]\";" . $eol;
		
		_is_writable(dirname($language_file));
		
		file_put_contents($language_file, $content);
		
		//Settings & Profiles template files
		$profiles_file = DIR_THEME . 'default/template/block/' . $data['route'] . '_profiles.tpl';
		$settings_file = DIR_THEME . 'default/template/block/' . $data['route'] . '_settings.tpl';
		
		_is_writable(dirname($profiles_file));
		
		if (!empty($data['profiles_file'])) {
			touch($profiles_file);
		}
		
		if (!empty($data['settings_file'])) {
			touch($settings_file);
		}
		
		
		//Front Controller File
		$controller_template = DIR_THEME . 'default/template/block/template/front_controller_template.php';
		$controller_file = SITE_DIR . 'catalog/controller/block/' . $data['route'] . '.php';
		
		$content = file_get_contents($controller_template);
		
		$insertables = array(
			'route' => $data['route'],
			'class_name' => "Catalog_Controller_" . $class_name,
		);
		
		$content = $this->tool->insertables($insertables, $content);
		
		_is_writable(dirname($controller_file));
		
		file_put_contents($controller_file, $content);
		
		//Front Language file
		$language_file = SITE_DIR . 'catalog/language/' . $language_dir . '/block/' . $data['route'] . '.php';
		
		$content =
			"<?php" . $eol .
			"//Heading" . $eol .
			"\$_['heading_title'] = \"$data[name]\";" . $eol;
		
		_is_writable(dirname($language_file));
		
		file_put_contents($language_file, $content);
		
		//Front Template
		$template_template = DIR_THEME . 'default/template/block/template/template_template.tpl';
		$template_file = SITE_DIR . 'catalog/view/theme/default/template/block/' . $data['route'] . '.tpl';
		
		_is_writable(dirname($template_file));
		
		$content = file_get_contents($template_template);
		
		$insertables = array(
			'slug' => $this->tool->get_slug($data['route']),
		);
		
		$content = $this->tool->insertables($insertables, $content);
		
		file_put_contents($template_file, $content);
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
	
	public function is_block($name)
	{
		return is_file(SITE_DIR . 'admin/controller/block/' . $name . '.php');
	}
	
	public function getBlock($name)
	{
		$block = $this->query_row("SELECT * FROM " . DB_PREFIX . "block WHERE `name` = '" . $this->db->escape($name) . "'");
		
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
		
		$this->clean_DB($block_files);
		
		if ($total) {
			return count($block_files);
		}
		
		$start = isset($data['start']) ? (int)$data['start'] : 0;
		$limit = isset($data['limit']) ? $start + (int)$data['limit'] : false;
		
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
			foreach ($blocks as $key => $block) {
				$sort_order[$key] = $block[$data['sort']];
			}
			
			$order = (!empty($data['order']) && $data['order'] == 'ASC') ? SORT_ASC : SORT_DESC;
			
			array_multisort($sort_order, $order, $blocks);
		}
		
		$blocks = array_slice($blocks, $start, $limit);
		
		return $blocks;
	}
	
	public function getTotalBlocks($data = array()){
		return $this->getBlocks($data, true);
	}
	
	public function clean_DB($valid_files)
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
