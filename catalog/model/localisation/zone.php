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
			$zones = $this->queryRow("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "' AND status = '1' ORDER BY name");
			
			$this->cache->set('zone.' . (int)$country_id, $zones);
		}
	
		return $zones;
	}
	
	public function inGeoZone($geo_zone_id, $country_id = 0, $zone_id = 0)
	{
		if(!$geo_zone_id) return true;
		
		$geo_zone_id = (int)$geo_zone_id;
		$country_id = (int)$country_id;
		$zone_id = (int)$zone_id;
		
		$include = "0 NOT IN (SELECT COUNT(*) as total FROM " . DB_PREFIX . "zone_to_geo_zone z2g WHERE g.geo_zone_id = z2g.geo_zone_id AND z2g.country_id IN (0, $country_id) AND z2g.zone_id IN (0, $zone_id))";
		$exclude = "0 IN (SELECT COUNT(*) as total FROM " . DB_PREFIX . "zone_to_geo_zone z2g2 WHERE g.geo_zone_id = z2g2.geo_zone_id AND z2g2.country_id = '$country_id' AND z2g2.zone_id IN (0, $zone_id))";
		
		$query = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "geo_zone g WHERE g.geo_zone_id = '$geo_zone_id' AND IF (g.exclude = '0', $include, $exclude)";
		
		$total = $this->queryVar($query);
		
		return $total > 0;
	}
	
	public function getZonesByGeoZone($geo_zone)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone . "'");
	}
}