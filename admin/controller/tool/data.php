<?php
class Admin_Controller_Tool_Data extends Controller
{
	public function index()
	{
	}

	public function load_zones()
	{
		if (!isset($_GET['country_id'])) {
			return;
		}

		if (isset($_GET['allow_all'])) {
			$choose = _l("All Zones");
			$value  = '0';
		} elseif (!isset($_GET['force_select'])) {
			$choose = _l(" --- Please Select --- ");
			$value  = '';
		} else {
			$choose = null;
			$value  = null;
		}

		if ($choose) {
			$output = "<option value='$value'>$choose</option>";
		} else {
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
			$output .= '<option value="0">' . _l(" --- None --- ") . '</option>';
		}

		$this->response->setOutput($output);
	}
}