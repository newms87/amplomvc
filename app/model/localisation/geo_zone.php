<?php

class App_Model_Localisation_GeoZone extends Model
{
	public function addGeoZone($data)
	{
		$data['date_added']    = $this->date->now();
		$data['date_modified'] = $data['date_added'];

		$geo_zone_id = $this->insert('geo_zone', $data);

		if (isset($data['zones'])) {
			foreach ($data['zones'] as $zone) {
				$zone['geo_zone_id'] = $geo_zone_id;
				$zone['date_added']  = $this->date->now();

				$this->insert('zone_to_geo_zone', $zone);
			}
		}

		clear_cache('geo_zone');
	}

	public function editGeoZone($geo_zone_id, $data)
	{
		$data['date_modified'] = $this->date->now();

		$this->update('geo_zone', $data, $geo_zone_id);

		$this->delete('zone_to_geo_zone', array('geo_zone_id' => $geo_zone_id));

		if (isset($data['zones'])) {
			foreach ($data['zones'] as $zone) {
				$zone['geo_zone_id'] = $geo_zone_id;
				$zone['date_added']  = $this->date->now();

				$this->insert('zone_to_geo_zone', $zone);
			}
		}

		clear_cache('geo_zone');
	}

	public function deleteGeoZone($geo_zone_id)
	{
		$this->delete('geo_zone', $geo_zone_id);
		$this->delete('zone_to_geo_zone', array('geo_zone_id' => $geo_zone_id));

		clear_cache('geo_zone');
	}

	public function getGeoZone($geo_zone_id)
	{
		return $this->queryRow("SELECT * FROM {$this->t['geo_zone']} WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
	}

	public function getGeoZones($data = array())
	{
		if ($data) {
			$sql = "SELECT * FROM {$this->t['geo_zone']}";

			$sort_data = array(
				'name',
				'description'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY name";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->query($sql);

			return $query->rows;
		} else {
			$geo_zone_data = cache('geo_zone');

			if (!$geo_zone_data) {
				$query = $this->query("SELECT * FROM {$this->t['geo_zone']} ORDER BY name ASC");

				$geo_zone_data = $query->rows;

				cache('geo_zone', $geo_zone_data);
			}

			return $geo_zone_data;
		}
	}

	public function getTotalGeoZones()
	{
		return $this->queryVar("SELECT COUNT(*) AS total FROM {$this->t['geo_zone']}");
	}

	public function getZones($geo_zone_id)
	{
		$query = $this->query("SELECT * FROM {$this->t['zone_to_geo_zone']} WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

		return $query->rows;
	}

	public function getTotalZones($geo_zone_id)
	{
		return $this->queryVar("SELECT COUNT(*) as total FROM {$this->t['zone_to_geo_zone']} WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
	}

	public function getTotalZoneToGeoZoneByCountryId($country_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM {$this->t['zone_to_geo_zone']} WHERE country_id = '" . (int)$country_id . "'");

		return $query->row['total'];
	}

	public function getTotalZoneToGeoZoneByZoneId($zone_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM {$this->t['zone_to_geo_zone']} WHERE zone_id = '" . (int)$zone_id . "'");

		return $query->row['total'];
	}
}
