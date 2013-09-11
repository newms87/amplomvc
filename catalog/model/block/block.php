<?php
class Catalog_Model_Block_Block extends Model
{
	private $blocks;

	function __construct(&$registry)
	{
		parent::__construct($registry);

		$this->loadBlocks();
	}

	public function getBlockSettings($name)
	{
		return isset($this->blocks[$name]) ? $this->blocks[$name]['settings'] : null;
	}

	public function getBlockProfileSettings($name, $profile_setting_id)
	{
		return isset($this->blocks[$name]['profile_settings'][$profile_setting_id]) ? $this->blocks[$name]['profile_settings'][$profile_setting_id] : null;
	}

	public function getBlockProfiles($name, $profile_setting_id)
	{
		return isset($this->blocks[$name]) ? $this->blocks[$name]['profiles'] : null;
	}

	private function loadBlocks()
	{
		$store_id  = $this->config->get('config_store_id');
		$layout_id = $this->config->get('config_layout_id');

		$blocks = $this->cache->get("blocks.$store_id.$layout_id");

		if (!$blocks) {
			$results = $this->query("SELECT * FROM " . DB_PREFIX . "block WHERE status = '1'");

			$blocks = array('position' => array());

			foreach ($results->rows as $row) {
				$row['settings']         = $row['settings'] ? unserialize($row['settings']) : array();
				$row['profile_settings'] = $row['profile_settings'] ? unserialize($row['profile_settings']) : array();
				$row['profiles']         = $row['profiles'] ? unserialize($row['profiles']) : array();

				if (!empty($row['profiles'])) {
					foreach ($row['profiles'] as $profile) {
						if (in_array($store_id, $profile['store_ids'])) {
							//Load this profiles settings
							if (isset($profile['profile_setting_id']) && isset($row['profile_settings'][$profile['profile_setting_id']])) {
								$profile += $row['profile_settings'][$profile['profile_setting_id']];
							}

							$blocks[$row['name']] = $row;
							$blocks[$row['name']]['profile'] = $profile;

							//Automatically loaded blocks for this layout
							if (in_array($layout_id, $profile['layout_ids'])) {
								$blocks['position'][$profile['position']][$row['name']] = & $blocks[$row['name']];
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