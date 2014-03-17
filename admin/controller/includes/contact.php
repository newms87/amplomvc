<?php
class Admin_Controller_Includes_Contact extends Controller
{

	public function index($args)
	{
		$this->view->load('includes/contact');

		$id   = isset($args['id']) ? $args['id'] : null;
		$type = isset($args['type']) ? $args['type'] : 'general';

		if (isset($_POST['contact'])) {
			$this->data['contact_info'] = $_POST['contact'];
		} elseif ($id) {
			$this->data['contact_info'] = $this->Model_Includes_Contact->getContactsByType($type, $id);
		} else {
			$this->data['contact_info'] = array();
		}


		$this->data['phone_types']   = array(
			'cell'    => "Cell Phone",
			'primary' => "Primary",
			"fax"     => "Fax"
		);
		$this->data['contact_types'] = array(
			'primary'          => "Primary",
			"customer_service" => "Customer Service",
			"finance"          => "Finance",
			"shipping"         => "Shipping",
			"returns"          => "Returns",
			"vendor"           => "Vendor",
			"legal"            => "Legal"
		);


		//set default to USA
		$this->data['default_country'] = 223;
		$this->data['countries']       = array();
		$countries                     = $this->Model_Localisation_Country->getCountries();
		foreach ($countries as $country) {
			$this->data['countries'][$country['country_id']] = $country['name'];
		}


		$this->response->setOutput($this->render());
	}
}
