<?php  
class ControllerModuleInformation extends Controller {
	protected function index() {
		$this->template->load('module/information');

		$this->language->load('module/information');
		
		$this->data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
				$this->data['informations'][] = array(
				'title' => $result['title'],
				'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
		}

		$this->data['contact'] = $this->url->link('information/contact');
		$this->data['sitemap'] = $this->url->link('information/sitemap');
		






		$this->render();
	}
}