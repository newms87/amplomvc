<?php
class App_Controller_Admin_Localisation_Language extends Controller
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

		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Language"), site_url('admin/localisation/language'));

		$data['insert'] = site_url('admin/localisation/language/insert', $url);
		$data['delete'] = site_url('admin/localisation/language/delete', $url);

		$data['languages'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * option('config_admin_limit'),
			'limit' => option('config_admin_limit')
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
				'href' => site_url('admin/localisation/language/update', 'language_id=' . $result['language_id'] . $url)
			);

			$data['languages'][] = array(
				'language_id' => $result['language_id'],
				'name'        => $result['name'] . (($result['code'] == option('config_language')) ? _l(" <b>(Default)</b>") : null),
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

		$data['sort_name']       = site_url('admin/localisation/language', 'sort=name' . $url);
		$data['sort_code']       = site_url('admin/localisation/language', 'sort=code' . $url);
		$data['sort_sort_order'] = site_url('admin/localisation/language', 'sort=sort_order' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $language_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('localisation/language_list', $data));
	}

	private function getForm()
	{
		$language_id = isset($_GET['language_id']) ? $_GET['language_id'] : false;

		$url = $this->url->getQuery('sort', 'order', 'page');

		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Language"), site_url('admin/localisation/language'));

		if (!$language_id) {
			$data['action'] = site_url('admin/localisation/language/insert', $url);
		} else {
			$data['action'] = site_url('admin/localisation/language/update', 'language_id=' . $language_id . '&' . $url);
		}

		$data['cancel'] = site_url('admin/localisation/language', $url);

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
				$data[$d] = $_POST[$d];
			} elseif (isset($language_info[$d])) {
				$data[$d] = $language_info[$d];
			} elseif (!$language_id) {
				$data[$d] = $value;
			}
		}


		//Template Data
		$data['data_direction'] = array(
			'ltr' => _l("Left to Right"),
			'rtl' => _l("Right to Left"),
		);
		$data['data_statuses']  = array(
			-1 => _l('Disabled'),
			0  => _l('Inactive'),
			1  => _l('Active'),
		);

		$this->response->setOutput($this->render('localisation/language_form', $data));
	}

	private function validateForm()
	{
		if (!user_can('modify', 'localisation/language')) {
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

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!user_can('modify', 'localisation/language')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify languages!");
		}

		foreach ($_GET['selected'] as $language_id) {
			$language_info = $this->Model_Localisation_Language->getLanguage($language_id);

			if ($language_info) {
				if (option('config_language') == $language_info['code']) {
					$this->error['warning'] = _l("Warning: This language cannot be deleted as it is currently assigned as the default store language!");
				}

				if (option('config_admin_language') == $language_info['code']) {
					$this->error['warning'] = _l("Warning: This Language cannot be deleted as it is currently assigned as the administration language!");
				}

				$store_total = $this->Model_Setting_Store->getTotalStoresByLanguage($language_info['code']);

				if ($store_total) {
					$this->error['warning'] = sprintf(_l("Warning: This language cannot be deleted as it is currently assigned to %s stores!"), $store_total);
				}
			}
		}

		return empty($this->error);
	}
}
