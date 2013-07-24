<?php
class Admin_Controller_Tool_Data extends Controller
{
	public function index()
	{
	}
	
	public function load_zones()
	{
		if(!isset($_GET['country_id'])) return;
		
		if (isset($_GET['allow_all'])) {
			$choose = $this->_('text_all_zones');
			$value = '0';
		}
		elseif (!isset($_GET['force_select'])) {
			$choose = $this->_('text_select');
			$value = '';
		}
		else {
			$choose = null;
			$value = null;
		}
		
		if ($choose) {
			$output = "<option value='$value'>$choose</option>";
		}
		else {
			$output = '';
		}
		
		$results = $this->Model_Localisation_Zone->getZonesByCountryId($_GET['country_id']);
		
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