<?php 
class SetupContactsExtend implements SetupPlugin {
	
	public function install($registry, &$controller_adapters, &$db_requests){
		$db = $registry->get('db');
		
		$db->table_add_column('contact', 'lookbook', 'VARCHAR(255)', true);
	
		$db_requests[] = array(
				'table'		=> 'contact',
				'query_type'	=> array('insert','update'),
				'when'			=> 'before',
				'restrict'	=> array('ModelIncludesContact'),
				'plugin_path'  => 'contacts_extend',
				'callback'	=> 'extend_lookbook'
			);
		
		$db_requests[] = array(
				'table'		=> 'contact',
				'query_type'	=> array('insert','update'),
				'when'			=> 'after',
				'plugin_path'  => 'contacts_extend',
				'callback'	=> 'add_designer_categories'
			);
	}
	
	public function update($version, $registry){
		switch($version){
			case '1.53':
			case '1.52':
			case '1.51':
			default:
				break;
		}
	}
	
	public function uninstall($registry){
		$db = $registry->get('db');
		
		$db->table_drop_column('contact', 'lookbook');
	}
}