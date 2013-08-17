<?php
class Admin_Controller_Includes_Contact extends Controller
{

	public function index($args)
	{
		$this->template->load('includes/contact');

		$this->language->load('includes/contact');
		$id   = isset($args['id']) ? $args['id'] : null;
		$type = isset($args['type']) ? $args['type'] : 'general';

		if (isset($_POST['contact'])) {
			$this->data['contact_info'] = $_POST['contact'];
		} elseif ($id) {
			$this->data['contact_info'] = $this->Model_Includes_Contact->getContactsByType($type, $id);
		} else {
			$this->data['contact_info'] = array();
		}

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