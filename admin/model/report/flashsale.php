<?php
class ModelReportFlashsale extends Model {
	
	public function getFlashsaleViews(){
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "flashsale_views");
		return $query->rows;
	}
	
	public function getFlashsalesViewed($data = array()) {
		
		$select = "f.name, f.date_start, f.date_end, fv.flashsale_id, fv.user_id, fv.ip_address, fv.session_id, COUNT(fv.flashsale_id) as views";
			
		$limit = isset($data['limit'])?(int)$data['limit']:'';
		
		if($limit){
			$start = isset($data['start'])?(int)$data['start']:0;
			if ($start < 0) {
				$start = 0;
			}			
	
			if ($limit < 1) {
				$limit = 20;
			}
			$limit = "LIMIT $start, $limit";	
		}
		
		$sql = "SELECT $select FROM " . DB_PREFIX . "flashsale f JOIN " . DB_PREFIX . "flashsale_views fv ON(fv.flashsale_id=f.flashsale_id) GROUP BY fv.flashsale_id ORDER BY views DESC $limit";
		
		$query = $this->query($sql);
		
		return $query->rows;
	}	
	
	public function getTotalFlashsalesViewed() {
		$query = $this->query("SELECT COUNT(DISTINCT flashsale_id) as total FROM " . DB_PREFIX . "flashsale_views");
		return $query->row['total'];
	}
	
	public function getTotalFlashsaleViews() {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "flashsale_views");
		return $query->row['total'];
	}
			
	public function reset() {
		$this->query("DELETE FROM ". DB_PREFIX . "flashsale_views");
	}
}