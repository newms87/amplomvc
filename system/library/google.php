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

class Google extends Library
{
	private $exp_var;

	/************************
	 * Google Analytics API *
	 ************************/

	public function getExperimentVariation()
	{
		if ($this->exp_var === null) {
			$vars          = (int)option('ga_exp_vars', 0);
			$this->exp_var = $vars ? rand(0, $vars) : 0;
		}

		return $this->exp_var;
	}

	/*******************
	 * Google Maps API *
	 *******************/

	public function geocodeAddress($address)
	{
		if (!$address) {
			$this->error['address_empty'] = _l("Please provide an address to lookup");
			return false;
		}

		$data = array(
			'address' => $address,
		);

		return $this->geocode($data);
	}

	public function geocodeLatLng($lat, $lng)
	{
		$data = array(
			'latlng' => (float)$lat . ',' . (float)$lng,
		);

		return $this->geocode($data);
	}

	public function geocode($data, $raw_results = false)
	{
		$response = $this->curl->get('https://maps.googleapis.com/maps/api/geocode/json', $data, Curl::RESPONSE_JSON);

		if (empty($response['status'])) {
			$this->error['response'] = _l("Unable to lookup address at this time. The Google Map API response was invalid.", $response['status']);
		} elseif ($response['status'] === "ZERO_RESULTS" || empty($response['results'][0])) {
			$this->error['address'] = _l("There were no results for this address.");
		} elseif ($response['status'] !== 'OK') {
			$this->error['status'] = _l("Unable to lookup address. The Google Map API Response status was %s", $response['status']);
		}

		if ($this->error) {
			return false;
		}

		if ($raw_results) {
			return $response;
		}

		//Always use the first result as this is likely the most accurate
		$result = $response['results'][0];

		$components = array(
			'number'    => '',
			'street'    => '',
			'city'      => '',
			'state'     => '',
			'country'   => '',
			'zip'       => '',
			'formatted' => '',
		);

		foreach ($result['address_components'] as $comp) {
			switch ($comp['types'][0]) {
				case 'street_number':
					$components['number'] = $comp['short_name'];
					break;

				case 'route':
					$components['street'] = $comp['short_name'];
					break;

				case 'locality':
					$components['city'] = $comp['short_name'];
					break;

				case 'administrative_area_level_1':
					$components['state'] = $comp['short_name'];
					break;

				case 'country':
					$components['country'] = $comp['short_name'];
					break;

				case 'postal_code':
					$components['zip'] = $comp['short_name'];
					break;

				default:
					break;
			}
		}

		if (!empty($result['formatted_address'])) {
			$components['formatted'] = $result['formatted_address'];
		}

		return $components;
	}
}
