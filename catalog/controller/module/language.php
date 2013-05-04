<?php  
class ControllerModuleLanguage extends Controller {
	protected function index() {
		$this->template->load('module/language');

    	if (isset($_POST['language_code'])) {
			$this->session->data['language'] = $_POST['language_code'];
		
			if (isset($_POST['redirect'])) {
				$this->redirect($_POST['redirect']);
			} else {
				$this->redirect($this->url->link('common/home'));
			}
    	}		
		
		$this->language->load('module/language');
		
		$this->data['action'] = $this->url->link('module/language');

		$this->data['language_code'] = $this->session->data['language'];
		
		$this->data['languages'] = array();
		
		$results = $this->model_localisation_language->getLanguages();
		
		foreach ($results as $result) {
			if ($result['status']) {
				$this->data['languages'][] = array(
					'name'  => $result['name'],
					'code'  => $result['code'],
					'image' => $result['image']
				);	
			}
		}

		if (!isset($_GET['route'])) {
			$this->data['redirect'] = $this->url->link('common/home');
		} else {
			$data = $_GET;
			
			unset($data['_route_']);
			
			$route = $data['route'];
			
			unset($data['route']);
			
			$url = '';
			
			if ($data) {
				$url = '&' . urldecode(http_build_query($data, '', '&'));
			}	
						
			$this->data['redirect'] = $this->url->link($route, $url);
		}
		






		$this->render();
	}
}
