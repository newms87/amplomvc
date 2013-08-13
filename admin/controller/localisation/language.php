<?php
class Admin_Controller_Localisation_Language extends Controller
{
	

	public function index()
	{
		$this->language->load('localisation/language');
		
		$this->document->setTitle($this->_('head_title'));
		
		$this->getList();
	}

	public function insert()
	{
		$this->language->load('localisation/language');

		$this->document->setTitle($this->_('head_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Language->addLanguage($_POST);
			
			$this->message->add('success', $this->_('text_success'));

			$this->getList();
		}
		else {
			$this->getForm();
		}
	}

	public function update()
	{
		$this->language->load('localisation/language');

		$this->document->setTitle($this->_('head_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Language->editLanguage($_GET['language_id'], $_POST);
			
			$this->message->add('success', $this->_('text_success'));

			$this->getList();
		}
		else {
			$this->getForm();
		}
	}

	public function delete()
	{
		$this->language->load('localisation/language');

		$this->document->setTitle($this->_('head_title'));
		
		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $language_id) {
				$this->Model_Localisation_Language->deleteLanguage($language_id);
			}

			$this->message->add('success', $this->_('text_success'));
		}

		$this->getList();
	}

	private function getList()
	{
		$this->template->load('localisation/language_list');

		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'name';
		}
		
		if (isset($_GET['order'])) {
			$order = $_GET['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		$url = $this->url->getQuery('sort', 'order', 'page');
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('localisation/language'));
		
		$this->data['insert'] = $this->url->link('localisation/language/insert', $url);
		$this->data['delete'] = $this->url->link('localisation/language/delete', $url);
	
		$this->data['languages'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$data['status'] = array(-1,1,0);
		
		$language_total = $this->Model_Localisation_Language->getTotalLanguages($data);
		
		$results = $this->Model_Localisation_Language->getLanguages($data);

		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('localisation/language/update', 'language_id=' . $result['language_id'] . $url)
			);
					
			$this->data['languages'][] = array(
				'language_id' => $result['language_id'],
				'name'		=> $result['name'] . (($result['code'] == $this->config->get('config_language')) ? $this->_('text_default') : null),
				'code'		=> $result['code'],
				'sort_order'  => $result['sort_order'],
				'selected'	=> isset($_GET['selected']) && in_array($result['language_id'], $_GET['selected']),
				'action'		=> $action
			);
		}
		
		$url = '';
		
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}
					
		$this->data['sort_name'] = $this->url->link('localisation/language', 'sort=name' . $url);
		$this->data['sort_code'] = $this->url->link('localisation/language', 'sort=code' . $url);
		$this->data['sort_sort_order'] = $this->url->link('localisation/language', 'sort=sort_order' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}
				
		$this->pagination->init();
		$this->pagination->total = $language_total;
		$this->data['pagination'] = $this->pagination->render();
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->template->load('localisation/language_form');

		$language_id = isset($_GET['language_id']) ? $_GET['language_id'] : false;
		
 		$url = $this->url->getQuery('sort','order','page');
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('localisation/language'));
		
		if (!$language_id) {
			$this->data['action'] = $this->url->link('localisation/language/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('localisation/language/update', 'language_id=' . $language_id . '&' . $url);
		}
		
		$this->data['cancel'] = $this->url->link('localisation/language', $url);

		if ($language_id && !$this->request->isPost()) {
			$language_info = $this->Model_Localisation_Language->getLanguage($language_id);
		}
		
		$defaults = array(
			'name' => '',
			'code' => '',
			'locale' => '',
			'datetime_format' => 'Y-m-d H:i:s',
			'date_format_short' => 'm/d/Y',
			'date_format_long' => '',
			'time_format' => 'h:i:s A',
			'direction' => '',
			'decimal_point' => '',
			'thousand_point' => '',
			'image' => '',
			'directory' => '',
			'filename' => '',
			'sort_order' => '',
			'status' => 1
		);
		
		foreach ($defaults as $d=>$value) {
			if (isset($_POST[$d])) {
				$this->data[$d] = $_POST[$d];
			} elseif (isset($language_info[$d])) {
				$this->data[$d] = $language_info[$d];
			} elseif (!$language_id) {
				$this->data[$d] = $value;
			}
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'localisation/language')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 32)) {
			$this->error['name'] = $this->_('error_name');
		}

		if (strlen($_POST['code']) < 2) {
			$this->error['code'] = $this->_('error_code');
		}

		if (!$_POST['locale']) {
			$this->error['locale'] = $this->_('error_locale');
		}
		
		if (!$_POST['directory']) {
			$this->error['directory'] = $this->_('error_directory');
		}

		if (!$_POST['filename']) {
			$this->error['filename'] = $this->_('error_filename');
		}
		
		if ((strlen($_POST['image']) < 3) || (strlen($_POST['image']) > 32)) {
			$this->error['image'] = $this->_('error_image');
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'localisation/language')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_GET['selected'] as $language_id) {
			$language_info = $this->Model_Localisation_Language->getLanguage($language_id);

			if ($language_info) {
				if ($this->config->get('config_language') == $language_info['code']) {
					$this->error['warning'] = $this->_('error_default');
				}
				
				if ($this->config->get('config_admin_language') == $language_info['code']) {
					$this->error['warning'] = $this->_('error_admin');
				}
			
				$store_total = $this->Model_Setting_Store->getTotalStoresByLanguage($language_info['code']);
	
				if ($store_total) {
					$this->error['warning'] = sprintf($this->_('error_store'), $store_total);
				}
			}
				
			$filter = array(
				'language_ids' => array($language_id),
			);
			
			$order_total = $this->System_Model_Order->getTotalOrders($filter);

			if ($order_total) {
				$this->error['warning'] = sprintf($this->_('error_order'), $order_total);
			}
		}
		
		return $this->error ? false : true;
	}
}