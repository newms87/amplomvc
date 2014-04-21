<?php
class Admin_Controller_Includes_Contact extends Controller
{

	public function index($args)
	{
		$id   = isset($args['id']) ? $args['id'] : null;
		$type = isset($args['type']) ? $args['type'] : 'general';

		if (isset($_POST['contact'])) {
			$data['contact_info'] = $_POST['contact'];
		} elseif ($id) {
			$data['contact_info'] = $this->Model_Includes_Contact->getContactsByType($type, $id);
		} else {
			$data['contact_info'] = array();
		}


		$data['phone_types']   = array(
			'cell'    => "Cell Phone",
			'primary' => "Primary",
			"fax"     => "Fax"
		);
		$data['contact_types'] = array(
			'primary'          => "Primary",
			"customer_service" => "Customer Service",
			"finance"          => "Finance",
			"shipping"         => "Shipping",
			"returns"          => "Returns",
			"vendor"           => "Vendor",
			"legal"            => "Legal"
		);


		//set default to USA
		$data['default_country'] = 223;
		$data['countries']       = array();
		$countries                     = $this->Model_Localisation_Country->getCountries();
		foreach ($countries as $country) {
			$data['countries'][$country['country_id']] = $country['name'];
		}


		$this->response->setOutput($this->render('includes/contact', $data));
	}
}
