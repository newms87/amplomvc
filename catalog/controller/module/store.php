<?php
class Catalog_Controller_Module_Store extends Controller
{
	protected function index()
	{
		$status = true;
		
		if ($this->config->get('store_admin')) {
			$status = $this->user->isLogged();
		}
		
		if ($status) {
		$this->template->load('module/store');

			$this->language->load('module/store');
			
			$this->data['store_id'] = $this->config->get('config_store_id');
			
			$this->data['stores'] = array();
			
			$this->data['stores'][] = array(
				'store_id' => 0,
				'name'	=> $this->_('text_default'),
				'url'		=> $this->url->link('common/home'),
			);
			
			$results = $this->Model_Setting_Store->getStores();
			
			foreach ($results as $result) {
				$this->data['stores'][] = array(
					'store_id' => $result['store_id'],
					'name'	=> $result['name'],
					'url'		=> $result['url'] . 'index.php?route=common/home'
				);
			}

			$this->render();
		}
	}
}