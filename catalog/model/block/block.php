<?php
class Catalog_Model_Block_Block extends Model
{
	private $blocks;

	function __construct(&$registry)
	{
		parent::__construct($registry);

		$this->loadBlocks();
	}

	public function getBlockSettings($path)
	{
		return isset($this->blocks[$path]) ? $this->blocks[$path]['settings'] : null;
	}

	public function getBlockProfileSettings($path, $profile_setting_id)
	{
		return isset($this->blocks[$path]['profile_settings'][$profile_setting_id]) ? $this->blocks[$path]['profile_settings'][$profile_setting_id] : null;
	}

	public function getBlockProfiles($path, $profile_setting_id)
	{
		return isset($this->blocks[$path]) ? $this->blocks[$path]['profiles'] : null;
	}

	private function loadBlocks()
	{
		$store_id  = $this->config->get('config_store_id');
		$layout_id = $this->config->get('config_layout_id');

		$blocks = $this->cache->get("blocks.$store_id.$layout_id");

		//TODO: We can optimize this to grab cached blocks, then process the data. Should minimize cache file size, and we can use only 1 cache file.
		if (is_null($blocks)) {
			$block_list = $this->queryRows("SELECT * FROM " . DB_PREFIX . "block WHERE status = '1'");

			$blocks = array('position' => array());

			foreach ($block_list as $block) {
				$block['settings']         = $block['settings'] ? unserialize($block['settings']) : array();
				$block['profile_settings'] = $block['profile_settings'] ? unserialize($block['profile_settings']) : array();
				$block['profiles']         = $block['profiles'] ? unserialize($block['profiles']) : array();

				if (!empty($block['profiles'])) {
					foreach ($block['profiles'] as $profile) {
						if (in_array($store_id, $profile['store_ids'])) {
							//Load this profiles settings
							if (isset($profile['profile_setting_id']) && isset($block['profile_settings'][$profile['profile_setting_id']])) {
								$profile += $block['profile_settings'][$profile['profile_setting_id']];
							}

							$blocks[$block['path']]            = $block;
							$blocks[$block['path']]['profile'] = $profile;

							//Automatically loaded blocks for this layout
							if (in_array($layout_id, $profile['layout_ids'])) {
								$blocks['position'][$profile['position']][$block['path']] = & $blocks[$block['path']];
							}
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
