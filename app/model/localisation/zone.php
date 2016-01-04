<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class App_Model_Localisation_Zone extends App_Model_Table
{
	protected $table = 'zone', $primary_key = 'zone_id';

	public function addZone($zone)
	{
		clear_cache('zone');

		return $this->insert('zone', $zone);
	}

	public function editZone($zone_id, $zone)
	{
		clear_cache('zone');

		return $this->update('zone', $zone, $zone_id);
	}

	public function deleteZone($zone_id)
	{
		clear_cache('zone');

		return $this->delete('zone', $zone_id);
	}

	public function getZone($zone_id)
	{
		return $this->queryRow("SELECT * FROM {$this->t['zone']} WHERE zone_id = " . (int)$zone_id);
	}

	public function getActiveZone($zone_id)
	{
		return $this->queryRow("SELECT * FROM {$this->t['zone']} WHERE zone_id = '" . (int)$zone_id . "' AND status = '1'");
	}

	public function getZones($data = array())
	{
		$sql = "SELECT *, z.name, c.name AS country FROM {$this->t['zone']} z LEFT JOIN {$this->t['country']} c ON (z.country_id = c.country_id)";

		$sort_data = array(
			'c.name',
			'z.name',
			'z.code'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY c.name";
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
	}

	public function getZonesByCountryId($country_id)
	{
		$zone_data = cache('zone.' . (int)$country_id);

		if (!$zone_data) {
			$query = $this->query("SELECT * FROM {$this->t['zone']} WHERE country_id = '" . (int)$country_id . "' ORDER BY name");

			$zone_data = $query->rows;

			cache('zone.' . (int)$country_id, $zone_data);
		}

		return $zone_data;
	}

	public function getActiveZonesByCountryId($country_id)
	{
		$zones = cache('zone.' . (int)$country_id);

		if (!$zones) {
			$zones = $this->queryRows("SELECT * FROM {$this->t['zone']} WHERE country_id = '" . (int)$country_id . "' AND status = '1' ORDER BY name");

			cache('zone.' . (int)$country_id, $zones);
		}

		return $zones;
	}

	public function getZonesByGeoZone($geo_zone)
	{
		return $this->queryRows("SELECT * FROM {$this->t['zone_to_geo_zone']} WHERE geo_zone_id = '" . (int)$geo_zone . "'");
	}

	public function getTotalZones()
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM {$this->t['zone']}");

		return $query->row['total'];
	}

	public function getTotalZonesByCountryId($country_id)
	{
		$query = $this->query("SELECT COUNT(*) AS total FROM {$this->t['zone']} WHERE country_id = '" . (int)$country_id . "'");

		return $query->row['total'];
	}
}
