<?php

class App_Controller_Contact_Manager extends Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!is_logged()) {
			$this->request->setRedirect($this->url->here());
			redirect('customer/login');
		}
	}

	public function index($data = array())
	{
		//Page Head
		if (!$this->is_ajax) {
			set_page_info('title', _l("My Contacts"));
		}

		$data += array(
			'show_address' => true,
			'country_id'   => option('site_default_country_id', 223),
		);

		if ($data['show_address']) {
			$data['data_zones'] = $this->Model_Localisation_Zone->getRecords(array('name' => 'ASC'), array('country_id' => $data['country_id']));
		}

		output($this->render('contact/manager', $data));
	}

	public function listing()
	{
		$sort   = _request('sort', array('first_name' => 'ASC'));
		$filter = (array)_request('filter');

		$filter['customer_id'] = customer_info('customer_id');

		$options = array(
			'index' => 'contact_id',
			'start' => (int)_request('start', 0),
			'limit' => (int)_request('limit', 4),
		);

		list($contacts, $total) = $this->Model_Contact->getRecords($sort, $filter, $options, true);

		foreach ($contacts as &$contact) {
			if ($contact['phone']) {
				$contact['phone'] = format('phone', $contact['phone']);
			}
		}
		unset($contact);

		$data = array(
			'contacts' => $contacts,
			'total'    => $total,
		);

		output_json($data);
	}

	public function save()
	{
		if ($contact_id = $this->Model_Contact->save(_request('contact_id'), $_POST)) {
			message('success', _l("Contact information saved."));

			$contact          = $this->Model_Contact->getRecord($contact_id);
			$contact['phone'] = format('phone', $contact['phone']);

			message('data', $contact);
		} else {
			message('error', $this->Model_Contact->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('contact/manager');
		}
	}

	public function remove()
	{
		if ($this->Model_Polyscope_Contact->remove(_request('contact_id'))) {
			message('success', _l("Contact was removed."));
		} else {
			message('error', $this->Model_Polyscope_Contact->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('scope/contact');
		}
	}
}
