<?php  
class ControllerModuleCurrency extends Controller {
	protected function index() {
		$this->template->load('module/currency');

		if (isset($_POST['currency_code'])) {
      		$this->currency->set($_POST['currency_code']);
			
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			
			if (isset($_POST['redirect'])) {
				$this->url->redirect($_POST['redirect']);
			} else {
				$this->url->redirect($this->url->link('common/home'));
			}
   		}
		
		$this->language->load('module/currency');
		
		$this->data['action'] = $this->url->link('module/currency');
		
		$this->data['currency_code'] = $this->currency->getCode(); 
		
		$this->data['currencies'] = array();
		 
		$results = $this->model_localisation_currency->getCurrencies();	
		
		foreach ($results as $result) {
			if ($result['status']) {
   				$this->data['currencies'][] = array(
					'title'        => $result['title'],
					'code'         => $result['code'],
					'symbol_left'  => $result['symbol_left'],
					'symbol_right' => $result['symbol_right']				
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