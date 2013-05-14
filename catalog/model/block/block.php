<?php
class ModelBlockBlock extends Model {
	private $blocks;
	
	function __construct(&$registry){
		parent::__construct($registry);
		
		$this->loadBlocks();
	}
	
	public function getBlockSettings($name) {
		if(isset($this->blocks[$name])){
			return $this->blocks[$name];
		}
		
		return false;
	}
	
	private function loadBlocks(){
		$store_id = $this->config->get('config_store_id');
		$layout_id = $this->config->get('config_layout_id');
		
		$blocks = $this->cache->get("blocks.$store_id.$layout_id");
		
		if(!$blocks || true){
			$results = $this->query("SELECT * FROM " . DB_PREFIX . "block WHERE status = '1'");
			
			$blocks = array('positions' => array());
			
			foreach($results->rows as &$row){
				$row['settings'] = unserialize($row['settings']);
				$row['profiles'] = unserialize($row['profiles']);
				
				foreach($row['profiles'] as $profile){
					if(in_array($layout_id, $profile['layout_ids']) && in_array($store_id, $profile['store_ids'])){
						$blocks[$row['name']] = array(
							'profile' => $profile,
							'settings' => $row['settings'],
						);
						
						$blocks['position'][$profile['position']][$row['name']] = &$blocks[$row['name']];
					}
				}
			}
			
			$this->cache->set("blocks.$store_id.$layout_id", $blocks);
		}
		
		$this->blocks = $blocks;
	}
	
	public function getBlocksForPosition($position){
		if(isset($this->blocks['position'][$position])){
			return $this->blocks['position'][$position];
		}

		return array();
	}
}