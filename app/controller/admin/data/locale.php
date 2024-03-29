<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

class App_Controller_Admin_Data_Locale extends Controller
{
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

		$output = '';

		if ($choose) {
			$output = "<option value=\"$value\">$choose</option>";
		}

		$results = $this->Model_Localisation_Zone->getZonesByCountryId($_GET['country_id']);

		foreach ($results as $result) {
			$select = isset($_GET['zone_id']) && ($_GET['zone_id'] == $result['zone_id']) ? 'selected="selected"' : '';

			$output .= "<option value=\"$result[zone_id]\" $select>$result[name]</option>";
		}

		if (!$results) {
			$output .= "<option value=\"0\" selected=\"selected\">" . _l(" --- None --- ") . "</option>";
		}

		output($output);
	}
}
