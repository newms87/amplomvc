<?php
class ModelBlockBlock extends Model {
	public function updateBlock($name, $data){
		$this->delete('block', array('name' => $name));
		
		if(isset($data['settings'])){
			$data['settings'] = serialize($data['settings']);
		}
		
		if(isset($data['profiles'])){
			$data['profiles'] = serialize($data['profiles']);
		}
		
		$data['name'] = $name;
		
		$this->insert('block', $data);
		
		$this->cache->delete('block');
	}
	
	
	public function getBlock($name){
		$query = $this->get('block', '*', array('name' => $name));
		
		if($query->num_rows){
			$query->row['settings'] = unserialize($query->row['settings']);
			$query->row['profiles'] = unserialize($query->row['profiles']);
		}
		
		return $query->row;
	}
	
	public function getBlocks(){
		$block_files = glob(SITE_DIR . 'admin/controller/block/*/*.php');
		
		$this->clean_DB($block_files);
		
		$blocks = array();
		
		foreach($block_files as &$file){
			$name = preg_replace("/.*[\/\\\\]/",'',dirname($file)) . '/' . preg_replace("/.php\$/",'',basename($file));
			$block = $this->getBlock($name);
			if(!$block){
				$block = array(
					'name' => $name,
					'settings' => array(),
					'profiles' => array(),
					'status' => 1,
				);
			}
			
			$blocks[] = $block;
		}
		
		return $blocks;
	}
	
	public function clean_DB($valid_files){
		$names = array();
		
		foreach($valid_files as &$file){
			$names[] = preg_replace("/.*[\/\\\\]/",'',dirname($file)) . '/' . preg_replace("/.php\$/",'',basename($file));
		}
		
		$query = $this->query("DELETE FROM " . DB_PREFIX . "block WHERE name NOT IN('" . implode("','",$names) . "')");
		
		if($this->countAffected()){
			$this->cache->delete('block');
		}
	}
}
