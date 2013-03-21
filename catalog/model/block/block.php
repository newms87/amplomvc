<?php
class ModelBlockBlock extends Model {
	private $blocks;
	
	public function getBlockSettings($name) {
		if(!$this->blocks) $this->loadBlocks();
		
		if(isset($this->blocks[$name])){
			return $this->blocks[$name];
		}
		
		return false;
	}
	
	private function loadBlocks(){
		$store_id = $this->config->get('config_store_id');
		$layout_id = $this->config->get('config_layout_id');
		
		$blocks = $this->cache->get("blocks.$store_id.$layout_id");
		
		if(!$blocks){
			$blocks = array();
			
			$query = $this->get('block', '*');
			
			foreach($query->rows as &$row){
				if(!$row['status']) continue;
				
				$row['settings'] = unserialize($row['settings']);
				$row['profiles'] = unserialize($row['profiles']);
				
				foreach($row['profiles'] as $profile){
					if($profile['status'] && in_array($layout_id, $profile['layout_ids']) && in_array($store_id, $profile['store_ids'])){
						$blocks[$row['name']] = array(
							'profile' => $profile,
							'settings' => $row['settings'],
						);
					}
				}
			}
			
			$this->cache->set("blocks.$store_id.$layout_id", $blocks);
		}
		
		$this->blocks = $blocks;
	}
	
	public function getBlocksForPosition($position){
		$store_id = $this->config->get('config_store_id');
		$layout_id = $this->config->get('config_layout_id');
		
		$blocks = $this->cache->get("blocks.$store_id.$layout_id.$position");
		
		if(!$blocks && !is_array($blocks)){
			$blocks = array();
			
			$query = $this->get('block', '*');
			
			foreach($query->rows as &$row){
				if(!$row['status']) continue;
				
				$row['settings'] = unserialize($row['settings']);
				$row['profiles'] = unserialize($row['profiles']);
				
				foreach($row['profiles'] as $profile){
					if($profile['status'] && !empty($profile['position']) && $profile['position'] == $position 
							&& in_array($layout_id, $profile['layout_ids']) && in_array($store_id, $profile['store_ids'])){
								
						$blocks[$row['name']] = array(
							'profile' => $profile,
							'settings' => $row['settings'],
						);
					}
				}
			}
			
			$this->cache->set("blocks.$store_id.$layout_id.$position", $blocks);
		}
		
		return $blocks;
	}
}