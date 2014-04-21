<?php
class Admin_Controller_Localisation_WeightClass extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Weight Class"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Weight Class"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Weightclass->addWeightClass($_POST);

			$this->message->add('success', _l("Success: You have modified weight classes!"));

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

			$this->url->redirect('localisation/weight_class', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Weight Class"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Weightclass->editWeightClass($_GET['weight_class_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified weight classes!"));

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

			$this->url->redirect('localisation/weight_class', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Weight Class"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $weight_class_id) {
				$this->Model_Localisation_Weightclass->deleteWeightClass($weight_class_id);
			}

			$this->message->add('success', _l("Success: You have modified weight classes!"));

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

			$this->url->redirect('localisation/weight_class', $url);
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

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Weight Class"), $this->url->link('localisation/weight_class', $url));

		$data['insert'] = $this->url->link('localisation/weight_class/insert', $url);
		$data['delete'] = $this->url->link('localisation/weight_class/delete', $url);

		$data['weight_classes'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$weight_class_total = $this->Model_Localisation_WeightClass->getTotalWeightClasses();

		$results = $this->Model_Localisation_WeightClass->getWeightClasses($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => $this->url->link('localisation/weight_class/update', 'weight_class_id=' . $result['weight_class_id'] . $url)
			);

			$data['weight_classes'][] = array(
				'weight_class_id' => $result['weight_class_id'],
				'title'           => $result['title'] . (($result['unit'] == $this->config->get('config_weight_class')) ? _l(" <b>(Default)</b>") : null),
				'unit'            => $result['unit'],
				'value'           => $result['value'],
				'selected'        => isset($_GET['selected']) && in_array($result['weight_class_id'], $_GET['selected']),
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

			$this->session->delete('success');
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

		$data['sort_title'] = $this->url->link('localisation/weight_class', 'sort=title' . $url);
		$data['sort_unit']  = $this->url->link('localisation/weight_class', 'sort=unit' . $url);
		$data['sort_value'] = $this->url->link('localisation/weight_class', 'sort=value' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $weight_class_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('localisation/weight_class_list', $data));
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

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Weight Class"), $this->url->link('localisation/weight_class', $url));

		if (!isset($_GET['weight_class_id'])) {
			$data['action'] = $this->url->link('localisation/weight_class/insert', $url);
		} else {
			$data['action'] = $this->url->link('localisation/weight_class/update', 'weight_class_id=' . $_GET['weight_class_id'] . $url);
		}

		$data['cancel'] = $this->url->link('localisation/weight_class', $url);

		if (isset($_GET['weight_class_id']) && !$this->request->isPost()) {
			$weight_class_info = $this->Model_Localisation_WeightClass->getWeightClass($_GET['weight_class_id']);
		}

		$data['languages'] = $this->Model_Localisation_Language->getLanguages();

		if (isset($_POST['weight_class_description'])) {
			$data['weight_class_description'] = $_POST['weight_class_description'];
		} elseif (isset($_GET['weight_class_id'])) {
			$data['weight_class_description'] = $this->Model_Localisation_WeightClass->getWeightClassDescriptions($_GET['weight_class_id']);
		} else {
			$data['weight_class_description'] = array();
		}

		if (isset($_POST['value'])) {
			$data['value'] = $_POST['value'];
		} elseif (isset($weight_class_info)) {
			$data['value'] = $weight_class_info['value'];
		} else {
			$data['value'] = '';
		}

		$this->response->setOutput($this->render('localisation/weight_class_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'localisation/weight_class')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify weight classes!");
		}

		foreach ($_POST['weight_class_description'] as $language_id => $value) {
			if ((strlen($value['title']) < 3) || (strlen($value['title']) > 32)) {
				$this->error['title'][$language_id] = _l("Weight Title must be between 3 and 32 characters!");
			}

			if (!$value['unit'] || (strlen($value['unit']) > 4)) {
				$this->error['unit'][$language_id] = _l("Weight Unit must be between 1 and 4 characters!");
			}
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'localisation/weight_class')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify weight classes!");
		}

		foreach ($_GET['selected'] as $weight_class_id) {
			if ($this->config->get('config_weight_class_id') == $weight_class_id) {
				$this->error['warning'] = _l("Warning: This weight class cannot be deleted as it is currently assigned as the default store weight class!");
			}

			$data = array(
				'weight_class_id' => $weight_class_id,
			);

			$product_total = $this->Model_Catalog_Product->getTotalProducts($data);

			if ($product_total) {
				$this->error['warning'] = sprintf(_l("Warning: This weight class cannot be deleted as it is currently assigned to %s products!"), $product_total);
			}
		}

		return $this->error ? false : true;
	}
}
