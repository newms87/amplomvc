<?php
class ControllerIncludesContact extends Controller {
	
	public function index($args) {
		$this->template->load('includes/contact');

		$this->load->language('includes/contact');
		$id = isset($args['id'])?$args['id']:null;
		$type = isset($args['type'])?$args['type']:'general';
		
		if(isset($_POST['contact'])){
			$this->data['contact_info'] = $_POST['contact'];
		}
		elseif($id){
			$this->data['contact_info'] = $this->model_includes_contact->getContactsByType($type,$id);
		}
		else
			$this->data['contact_info'] = array();
		
		//set default to USA
		$this->data['default_country'] = 223;
		$this->data['countries'] = array();
		$countries = $this->model_localisation_country->getCountries();
		foreach($countries as $country)
			$this->data['countries'][$country['country_id']] = $country['name'];
			
		
		$this->response->setOutput($this->render());
	}
}