<?php
class Catalog_Model_Localisation_Zone extends Model
{
	public function getZone($zone_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "' AND status = '1'");
	}

	public function getZoneName($zone_id)
	{
		return $this->queryVar("SELECT name FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "' AND status = '1'");
	}

	public function getZonesByCountryId($country_id)
	{
		$zones = $this->cache->get('zone.' . (int)$country_id);

		if (!$zones) {
			$zones = $this->queryRows("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "' AND status = '1' ORDER BY name");

			$this->cache->set('zone.' . (int)$country_id, $zones);
		}

		return $zones;
	}

	public function getZonesByGeoZone($geo_zone)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone . "'");
	}
}