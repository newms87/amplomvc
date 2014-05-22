<?php
class App_Controller_Admin_Localisation_LengthClass extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Length Class"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Length Class"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Lengthclass->addLengthClass($_POST);

			$this->message->add('success', _l("Success: You have modified length classes!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			redirect('admin/localisation/length_class', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Length Class"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Lengthclass->editLengthClass($_GET['length_class_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified length classes!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			redirect('admin/localisation/length_class', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Length Class"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $length_class_id) {
				$this->Model_Localisation_Lengthclass->deleteLengthClass($length_class_id);
			}

			$this->message->add('success', _l("Success: You have modified length classes!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			redirect('admin/localisation/length_class', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'title';
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

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Length Class"), site_url('admin/localisation/length_class', $url));

		$data['insert'] = site_url('admin/localisation/length_class/insert', $url);
		$data['delete'] = site_url('admin/localisation/length_class/delete', $url);

		$data['length_classes'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * option('config_admin_limit'),
			'limit' => option('config_admin_limit')
		);

		$length_class_total = $this->Model_Localisation_LengthClass->getTotalLengthClasses();

		$results = $this->Model_Localisation_LengthClass->getLengthClasses($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => site_url('admin/localisation/length_class/update', 'length_class_id=' . $result['length_class_id'] . $url)
			);

			$data['length_classes'][] = array(
				'length_class_id' => $result['length_class_id'],
				'title'           => $result['title'] . (($result['unit'] == option('config_length_class')) ? _l(" <b>(Default)</b>") : null),
				'unit'            => $result['unit'],
				'value'           => $result['value'],
				'selected'        => isset($_GET['selected']) && in_array($result['length_class_id'], $_GET['selected']),
				'action'          => $action
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if ($this->session->has('success')) {
			$data['success'] = $this->session->get('success');

			$this->session->remove('success');
		} else {
			$data['success'] = '';
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

		$data['sort_title'] = site_url('admin/localisation/length_class', 'sort=title' . $url);
		$data['sort_unit']  = site_url('admin/localisation/length_class', 'sort=unit' . $url);
		$data['sort_value'] = site_url('admin/localisation/length_class', 'sort=value' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $length_class_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('localisation/length_class_list', $data));
	}

	private function getForm()
	{
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = array();
		}

		if (isset($this->error['unit'])) {
			$data['error_unit'] = $this->error['unit'];
		} else {
			$data['error_unit'] = array();
		}

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Length Class"), site_url('admin/localisation/length_class', $url));

		if (!isset($_GET['length_class_id'])) {
			$data['action'] = site_url('admin/localisation/length_class/insert', $url);
		} else {
			$data['action'] = site_url('admin/localisation/length_class/update', 'length_class_id=' . $_GET['length_class_id'] . $url);
		}

		$data['cancel'] = site_url('admin/localisation/length_class', $url);

		if (isset($_GET['length_class_id']) && !$this->request->isPost()) {
			$length_class_info = $this->Model_Localisation_LengthClass->getLengthClass($_GET['length_class_id']);
		}

		$data['languages'] = $this->Model_Localisation_Language->getLanguages();

		if (isset($_POST['length_class_description'])) {
			$data['length_class_description'] = $_POST['length_class_description'];
		} elseif (isset($_GET['length_class_id'])) {
			$data['length_class_description'] = $this->Model_Localisation_LengthClass->getLengthClassDescriptions($_GET['length_class_id']);
		} else {
			$data['length_class_description'] = array();
		}

		if (isset($_POST['value'])) {
			$data['value'] = $_POST['value'];
		} elseif (isset($length_class_info)) {
			$data['value'] = $length_class_info['value'];
		} else {
			$data['value'] = '';
		}

		$this->response->setOutput($this->render('localisation/length_class_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'localisation/length_class')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify length classes!");
		}

		foreach ($_POST['length_class_description'] as $language_id => $value) {
			if ((strlen($value['title']) < 3) || (strlen($value['title']) > 32)) {
				$this->error['title'][$language_id] = _l("Length Title must be between 3 and 32 characters!");
			}

			if (!$value['unit'] || (strlen($value['unit']) > 4)) {
				$this->error['unit'][$language_id] = _l("Length Unit must be between 1 and 4 characters!");
			}
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'localisation/length_class')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify length classes!");
		}

		foreach ($_GET['selected'] as $length_class_id) {
			if (option('config_length_class_id') == $length_class_id) {
				$this->error['warning'] = _l("Warning: This length class cannot be deleted as it is currently assigned as the default store length class!");
			}

			$data = array(
				'length_class_id' => $length_class_id,
			);

			$product_total = $this->Model_Catalog_Product->getTotalProducts($data);

			if ($product_total) {
				$this->error['warning'] = sprintf(_l("Warning: This length class cannot be deleted as it is currently assigned to %s products!"), $product_total);
			}
		}

		return empty($this->error);
	}
}
