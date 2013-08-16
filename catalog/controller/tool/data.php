<?php
class Catalog_Controller_Tool_Data extends Controller
{
	//TODO: Move this to the system/library/tool/load_zones route? or somewhere else..
	public function load_zones()
	{
		if (!isset($_GET['country_id'])) {
			return;
		}

		$choose = isset($_GET['allow_all']) ? $this->_('text_all_zones') : $this->_('text_select');

		$output = '<option value="">' . $choose . '</option>';

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