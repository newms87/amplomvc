<?php
class ControllerToolData extends Controller {
	
	function index(){}
	
	public function load_zones() {
		if(!isset($_GET['country_id'])) return;
		
		$choose = isset($_GET['allow_all']) ? $this->_('text_all_zones') : $this->_('text_select');
		
		$output = '<option value="">' . $choose . '</option>';
		
		$results = $this->model_localisation_zone->getZonesByCountryId($_GET['country_id']);
		
		foreach ($results as $result) {
			$output .= '<option value="' . $result['zone_id'] . '"';
	
			if (isset($_GET['zone_id']) && ($_GET['zone_id'] == $result['zone_id'])) {
					$output .= ' selected="selected"';
			}
	
			$output .= '>' . $result['name'] . '</option>';
		} 
		
		if (!$results) {
			$output .= '<option value="0">' . $this->_('text_none') . '</option>';
		}
	
		$this->response->setOutput($output);
	}
}