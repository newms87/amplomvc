<?php
class ControllerSettingUrlAlias extends Controller {

	public function index() {
		$this->load->language('setting/url_alias');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}
			
  	public function insert() {
		$this->load->language('setting/url_alias');

		$this->document->setTitle($this->_('heading_title')); 
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$store_id = $this->model_setting_url_alias->addUrlAlias($_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('setting/url_alias'));
		}
	
		$this->getForm();
  	}

  	public function update() {
		$this->load->language('setting/url_alias');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_setting_url_alias->editUrlAlias($_GET['url_alias_id'], $_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('setting/url_alias', 'store_id=' . $_GET['store_id']));
		}

		$this->getForm();
  	}

  	public function delete() {
		$this->load->language('setting/url_alias');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $url_alias_id) {
				$this->model_setting_url_alias->deleteUrlAlias($url_alias_id);
			}

			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('setting/url_alias'));
		}

		$this->getList();
  	}
	
	private function getList() {
		$this->template->load('setting/url_alias_list');

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('setting/url_alias'));
		
		$this->data['insert'] = $this->url->link('setting/url_alias/insert');
		$this->data['delete'] = $this->url->link('setting/url_alias/delete');	
		
		$url = $this->get_url(array('page'));
		
		$aliases = $this->model_setting_url_alias->getUrlAliases();
 
		foreach ($aliases as &$alias) {
			$alias['action'] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('setting/url_alias/update', 'url_alias_id=' . $alias['url_alias_id'])
			);
			
			$alias['selected'] = isset($_POST['selected']) && in_array($result['url_alias_id'], $_POST['selected']);
		}
		
		$this->data['aliases'] = $aliases;
		
		$this->data['data_stores'] = $this->model_setting_store->getStores();
	
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function getForm() {
		$this->template->load('setting/url_alias_form');

		$url_alias_id = isset($_GET['url_alias_id']) ? $_GET['url_alias_id']:null;
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('setting/url_alias'));
		
		if (!$url_alias_id) {
			$this->data['action'] = $this->url->link('setting/url_alias/insert');
		} else {
			$this->data['action'] = $this->url->link('setting/url_alias/update', 'url_alias_id=' . $url_alias_id);
		}
				
		$this->data['cancel'] = $this->url->link('setting/url_alias');
		
		$alias_info = $url_alias_id ? $this->model_setting_url_alias->getUrlAlias($url_alias_id) : null;
		
		$defaults = array(
			'keyword'	=>'',
			'route'	=>'',
			'query'	=>'',
			'admin'	=>'',
			'redirect'  =>'',
			'status'	=>'',
		);
			
		foreach($defaults as $d=>$value){
			if (isset($_POST[$d])) {
				$this->data[$d] = $_POST[$d];
			} elseif (isset($alias_info[$d])) {
				$this->data[$d] = $alias_info[$d];
			} elseif(!$url_alias_id) {
				$this->data[$d] = $value;
			}
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'setting/url_alias')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if(!$_POST['keyword']){
			$this->error['keyword'];
		}
		if(!$_POST['query']){
			$this->error['query'];
		}
		
		return $this->error ? false : true;
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'setting/url_alias')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$this->error) {
			return true; 
		} else {
			return false;
		}
	}
	
	private function get_url($filters=null){
		$url = '';
		$filters = $filters?$filters:array('sort', 'order', 'page');
		foreach($filters as $f)
			if (isset($_GET[$f]))
				$url .= "&$f=" . $_GET[$f];
		return $url;
	}
}