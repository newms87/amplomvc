<?php
class ModelBlockBlock extends Model 
{
	private $blocks;
	
	function __construct(&$registry)
	{
		parent::__construct($registry);
		
		$this->loadBlocks();
	}
	
	public function getBlockSettings($name)
	{
		if (!isset($this->blocks[$name]['settings'])) {
			$block = $this->cache->get("block.$name");
			
			if (!$block) {
				$block = $this->query_row("SELECT * FROM " . DB_PREFIX . "block WHERE status = '1' AND `name` = '" . $this->db->escape($name) . "'");
				
				if (!empty($block)) {
					$block['profiles'] = unserialize($block['profiles']);
					$block['settings'] = unserialize($block['settings']);
				}
				else {
					$block['profiles'] = null;
					$block['settings'] = null;
				}
				
				$this->cache->set("block.$name", $block);
			}
			
			$this->blocks[$name] = array(
				'profiles' => $block['profiles'],
				'settings' => $block['settings'],
			);
		}
		
		return $this->blocks[$name]['settings'];
	}
	
	private function loadBlocks()
	{
		$store_id = $this->config->get('config_store_id');
		$layout_id = $this->config->get('config_layout_id');
		
		$blocks = $this->cache->get("blocks.$store_id.$layout_id");
		
		if (!$blocks) {
			$results = $this->query("SELECT * FROM " . DB_PREFIX . "block WHERE status = '1'");
			
			$blocks = array('position' => array());
			
			foreach ($results->rows as $row) {
				$row['settings'] = unserialize($row['settings']);
				$row['profiles'] = unserialize($row['profiles']);
				
				if (!empty($row['profiles'])) {
					foreach ($row['profiles'] as $profile) {
						if (in_array($layout_id, $profile['layout_ids']) && in_array($store_id, $profile['store_ids'])) {
							$blocks[$row['name']] = array(
								'profile' => $profile,
								'settings' => $row['settings'],
							);
							
							$blocks['position'][$profile['position']][$row['name']] = &$blocks[$row['name']];
						}
					}
				}
			}
			
			$this->cache->set("blocks.$store_id.$layout_id", $blocks);
		}
		
		$this->blocks = $blocks;
	}
	
	public function getBlocksForPosition($position)
	{
		if (isset($this->blocks['position'][$position])) {
			return $this->blocks['position'][$position];
		}

		return array();
	}
}