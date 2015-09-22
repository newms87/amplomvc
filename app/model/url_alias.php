<?php

class App_Model_UrlAlias extends App_Model_Table
{
	protected $table = 'url_alias', $primary_key = 'url_alias_id';

	public function save($url_alias_id, $url_alias)
	{
		clear_cache('url_alias');

		if (isset($url_alias['alias'])) {
			if (empty($url_alias['alias'])) {
				$this->error['alias'] = _l("Alias cannot be empty.");
			} else {
				$url_alias['alias'] = format('url', $url_alias['alias']);
			}
		} elseif (!$url_alias_id) {
			$this->error['alias'] = _l("Alias is required.");
		}

		if ($this->error) {
			return false;
		}

		if ($url_alias_id) {
			$this->update('url_alias', $url_alias, $url_alias_id);
		} else {
			$url_alias_id = $this->insert('url_alias', $url_alias);
		}

		return $url_alias_id;
	}

	public function remove($url_alias_id)
	{
		clear_cache('url_alias');

		return $this->delete('url_alias', $url_alias_id);
	}

	public function getColumns($filter = array(), $merge = array())
	{
		//The Table Columns
		$merge += array(
			'status' => array(
				'type'   => 'select',
				'label'  => _l("Status"),
				'build'  => array(
					'data' => array(
						0 => _l("Disabled"),
						1 => _l("Enabled"),
					),
				),
				'filter' => true,
				'sort'   => true,
			),
		);

		return parent::getColumns($filter, $merge);
	}
}
