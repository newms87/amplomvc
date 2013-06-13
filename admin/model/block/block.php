<?php
class Admin_Model_Block_Block extends Model 
{
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
			$block['settings'] = unserialize($block['settings']);
			$block['profiles'] = unserialize($block['profiles']);
		}
		else {
			$block = array(
				'name' => $name,
				'settings' => array(),
				'profiles' => array(),
				'status' => 0,
			);
		}
		
		if (empty($block['settings'])) {
			$block['settings'] = array();
		}
		
		if (empty($block['profiles'])) {
			$block['profiles'] = array();
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
