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
   
   public function inGeoZone($geo_zone, $country_id = 0, $zone_id = 0){
   	if(!$geo_zone) return true;
		
      $zone = $country = '';
      
      if($country_id){
         $country = "AND (country_id = '" . (int)$country_id . "' OR country_id = 0)";
      }
      
      if($zone_id){
         $zone = "AND (zone_id = '" . (int)$zone_id . "' OR zone_id = 0)";
      }
      
      $query = $this->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone . "' $country $zone");
      
      return $query->row['total'] ? true : false;
   }
   
   public function getZonesByGeoZone($geo_zone){
      $query = $this->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone . "'");
      return $query->rows;
   }
}