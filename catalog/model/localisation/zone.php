<?php
class ModelLocalisationZone extends Model {
	public function getZone($zone_id) {
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "' AND status = '1'");
		
		return $query->row;
	}		
	
	public function getZoneName($zone_id) {
		$query = $this->query("SELECT name FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "' AND status = '1'");
		
		return $query->num_rows?$query->row['name']:'';
	}
	
	public function getZonesByCountryId($country_id) {
		$zone_data = $this->cache->get('zone.' . (int)$country_id);
	
		if (!$zone_data) {
			$query = $this->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "' AND status = '1' ORDER BY name");
	
			$zone_data = $query->rows;
			
			$this->cache->set('zone.' . (int)$country_id, $zone_data);
		}
	
		return $zone_data;
	}
	
	public function inGeoZone($geo_zone_id, $country_id = 0, $zone_id = 0){
		if(!$geo_zone_id) return true;
		
		$include = "g.exclude = '0'";
		$exclude = "g.exclude = '1'";
		
		$exclude .= " AND 0 IN (SELECT COUNT(*) as total FROM " . DB_PREFIX . "zone_to_geo_zone z2g2 WHERE z2g2.geo_zone_id = z2g.geo_zone_id AND z2g2.country_id = '" . (int)$country_id . "' AND (z2g2.zone_id = '0' OR z2g2.zone_id = '" . (int)$zone_id . "') )";
		
		if($country_id){
			$include .= " AND (z2g.country_id = '" . (int)$country_id . "' OR z2g.country_id = '0')";
		}
		
		if($zone_id){
			$include .= " AND (z2g.zone_id = '" . (int)$zone_id . "' OR z2g.zone_id = '0')";
		}
		
		$query = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "zone_to_geo_zone z2g";
		$query .= " LEFT JOIN " . DB_PREFIX . "geo_zone g ON(z2g.geo_zone_id=g.geo_zone_id)";
		$query .= " WHERE z2g.geo_zone_id = '" . (int)$geo_zone_id . "' AND (($include) OR ($exclude))";
		
		$total = $this->query_var($query);
		
		return $total > 0;
	}
	
	public function getZonesByGeoZone($geo_zone){
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone . "'");
		return $query->rows;
	}
}