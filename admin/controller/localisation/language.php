<?php
class Admin_Controller_Localisation_Language extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Language"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Language"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Language->addLanguage($_POST);

			$this->message->add('success', _l("Success: You have modified languages!"));

			$this->getList();
		} else {
			$this->getForm();
		}
	}

	public function update()
	{
		$this->document->setTitle(_l("Language"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Language->editLanguage($_GET['language_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified languages!"));

			$this->getList();
		} else {
			$this->getForm();
		}
	}

	public function delete()
	{
		$this->document->setTitle(_l("Language"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $language_id) {
				$this->Model_Localisation_Language->deleteLanguage($language_id);
			}

			$this->message->add('success', _l("Success: You have modified languages!"));
		}

		$this->getList();
	}

	private function getList()
	{
		$this->view->load('localisation/language_list');

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

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Language"), $this->url->link('localisation/language'));

		$this->data['insert'] = $this->url->link('localisation/language/insert', $url);
		$this->data['delete'] = $this->url->link('localisation/language/delete', $url);

		$this->data['languages'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$data['status'] = array(
			-1,
			1,
			0
		);

		$language_total = $this->Model_Localisation_Language->getTotalLanguages($data);

		$results = $this->Model_Localisation_Language->getLanguages($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => $this->url->link('localisation/language/update', 'language_id=' . $result['language_id'] . $url)
			);

			$this->data['languages'][] = array(
				'language_id' => $result['language_id'],
				'name'        => $result['name'] . (($result['code'] == $this->config->get('config_language')) ? _l(" <b>(Default)</b>") : null),
				'code'        => $result['code'],
				'sort_order'  => $result['sort_order'],
				'selected'    => isset($_GET['selected']) && in_array($result['language_id'], $_GET['selected']),
				'action'      => $action
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

		$this->data['sort_name']       = $this->url->link('localisation/language', 'sort=name' . $url);
		$this->data['sort_code']       = $this->url->link('localisation/language', 'sort=code' . $url);
		$this->data['sort_sort_order'] = $this->url->link('localisation/language', 'sort=sort_order' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $language_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort']  = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->view->load('localisation/language_form');

		$language_id = isset($_GET['language_id']) ? $_GET['language_id'] : false;

		$url = $this->url->getQuery('sort', 'order', 'page');

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Language"), $this->url->link('localisation/language'));

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
			'name'              => '',
			'code'              => '',
			'locale'            => '',
			'datetime_format'   => 'Y-m-d H:i:s',
			'date_format_short' => 'm/d/Y',
			'date_format_long'  => '',
			'time_format'       => 'h:i:s A',
			'direction'         => '',
			'decimal_point'     => '',
			'thousand_point'    => '',
			'image'             => '',
			'directory'         => '',
			'filename'          => '',
			'sort_order'        => '',
			'status'            => 1
		);

		foreach ($defaults as $d => $value) {
			if (isset($_POST[$d])) {
				$this->data[$d] = $_POST[$d];
			} elseif (isset($language_info[$d])) {
				$this->data[$d] = $language_info[$d];
			} elseif (!$language_id) {
				$this->data[$d] = $value;
			}
		}


		//Template Data
		$this->data['data_direction'] = array(
			'ltr' => _l("Left to Right"),
			'rtl' => _l("Right to Left"),
		);
		$this->data['data_statuses']  = array(
			-1 => _l('Disabled'),
			0  => _l('Inactive'),
			1  => _l('Active'),
		);

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'localisation/language')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify languages!");
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 32)) {
			$this->error['name'] = _l("Language Name must be between 3 and 32 characters!");
		}

		if (strlen($_POST['code']) < 2) {
			$this->error['code'] = _l("Language Code must at least 2 characters!");
		}

		if (!$_POST['locale']) {
			$this->error['locale'] = _l("Locale required!");
		}

		if (!$_POST['directory']) {
			$this->error['directory'] = _l("Directory required!");
		}

		if (!$_POST['filename']) {
			$this->error['filename'] = _l("Filename must be between 3 and 64 characters!");
		}

		if ((strlen($_POST['image']) < 3) || (strlen($_POST['image']) > 32)) {
			$this->error['image'] = _l("Image Filename must be between 3 and 64 characters!");
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'localisation/language')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify languages!");
		}

		foreach ($_GET['selected'] as $language_id) {
			$language_info = $this->Model_Localisation_Language->getLanguage($language_id);

			if ($language_info) {
				if ($this->config->get('config_language') == $language_info['code']) {
					$this->error['warning'] = _l("Warning: This language cannot be deleted as it is currently assigned as the default store language!");
				}

				if ($this->config->get('config_admin_language') == $language_info['code']) {
					$this->error['warning'] = _l("Warning: This Language cannot be deleted as it is currently assigned as the administration language!");
				}

				$store_total = $this->Model_Setting_Store->getTotalStoresByLanguage($language_info['code']);

				if ($store_total) {
					$this->error['warning'] = sprintf(_l("Warning: This language cannot be deleted as it is currently assigned to %s stores!"), $store_total);
				}
			}

			$filter = array(
				'language_ids' => array($language_id),
			);

			$order_total = $this->System_Model_Order->getTotalOrders($filter);

			if ($order_total) {
				$this->error['warning'] = sprintf(_l("Warning: This language cannot be deleted as it is currently assigned to %s orders!"), $order_total);
			}
		}

		return $this->error ? false : true;
	}
}
