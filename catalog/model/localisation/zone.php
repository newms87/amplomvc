<?php
class Catalog_Model_Localisation_Zone extends Model 
{
	public function getZone($zone_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "' AND status = '1'");
		
		return $query->row;
	}
	
	public function getZoneName($zone_id)
	{
		$query = $this->query("SELECT name FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "' AND status = '1'");
		
		return $query->num_rows?$query->row['name']:'';
	}
	
	public function getZonesByCountryId($country_id)
	{
		$zone_data = $this->cache->get('zone.' . (int)$country_id);
	
		if (!$zone_data) {
			$query = $this->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "' AND status = '1' ORDER BY name");
	
			$zone_data = $query->rows;
			
			$this->cache->set('zone.' . (int)$country_id, $zone_data);
		}
	
		return $zone_data;
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
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone . "'");
		return $query->rows;
	}
}