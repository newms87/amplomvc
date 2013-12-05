<?php
class Admin_Controller_Localisation_WeightClass extends Controller
{


	public function index()
	{
		$this->language->load('localisation/weight_class');

		$this->document->setTitle($this->_('head_title'));

		$this->getList();
	}

	public function insert()
	{
		$this->language->load('localisation/weight_class');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Weightclass->addWeightClass($_POST);

			$this->message->add('success', $this->_('text_success'));

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
		$this->language->load('localisation/weight_class');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Weightclass->editWeightClass($_GET['weight_class_id'], $_POST);

			$this->message->add('success', $this->_('text_success'));

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
		$this->language->load('localisation/weight_class');

		$this->document->setTitle($this->_('head_title'));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $weight_class_id) {
				$this->Model_Localisation_Weightclass->deleteWeightClass($weight_class_id);
			}

			$this->message->add('success', $this->_('text_success'));

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
		$this->template->load('localisation/weight_class_list');

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

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('localisation/weight_class', $url));

		$this->data['insert'] = $this->url->link('localisation/weight_class/insert', $url);
		$this->data['delete'] = $this->url->link('localisation/weight_class/delete', $url);

		$this->data['weight_classes'] = array();

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
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('localisation/weight_class/update', 'weight_class_id=' . $result['weight_class_id'] . $url)
			);

			$this->data['weight_classes'][] = array(
				'weight_class_id' => $result['weight_class_id'],
				'title'           => $result['title'] . (($result['unit'] == $this->config->get('config_weight_class')) ? $this->_('text_default') : null),
				'unit'            => $result['unit'],
				'value'           => $result['value'],
				'selected'        => isset($_GET['selected']) && in_array($result['weight_class_id'], $_GET['selected']),
				'action'          => $action
			);
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
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

		$this->data['sort_title'] = $this->url->link('localisation/weight_class', 'sort=title' . $url);
		$this->data['sort_unit']  = $this->url->link('localisation/weight_class', 'sort=unit' . $url);
		$this->data['sort_value'] = $this->url->link('localisation/weight_class', 'sort=value' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $weight_class_total;
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
		$this->template->load('localisation/weight_class_form');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$this->data['error_title'] = $this->error['title'];
		} else {
			$this->data['error_title'] = array();
		}

		if (isset($this->error['unit'])) {
			$this->data['error_unit'] = $this->error['unit'];
		} else {
			$this->data['error_unit'] = array();
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

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('localisation/weight_class', $url));

		if (!isset($_GET['weight_class_id'])) {
			$this->data['action'] = $this->url->link('localisation/weight_class/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('localisation/weight_class/update', 'weight_class_id=' . $_GET['weight_class_id'] . $url);
		}

		$this->data['cancel'] = $this->url->link('localisation/weight_class', $url);

		if (isset($_GET['weight_class_id']) && !$this->request->isPost()) {
			$weight_class_info = $this->Model_Localisation_WeightClass->getWeightClass($_GET['weight_class_id']);
		}

		$this->data['languages'] = $this->Model_Localisation_Language->getLanguages();

		if (isset($_POST['weight_class_description'])) {
			$this->data['weight_class_description'] = $_POST['weight_class_description'];
		} elseif (isset($_GET['weight_class_id'])) {
			$this->data['weight_class_description'] = $this->Model_Localisation_WeightClass->getWeightClassDescriptions($_GET['weight_class_id']);
		} else {
			$this->data['weight_class_description'] = array();
		}

		if (isset($_POST['value'])) {
			$this->data['value'] = $_POST['value'];
		} elseif (isset($weight_class_info)) {
			$this->data['value'] = $weight_class_info['value'];
		} else {
			$this->data['value'] = '';
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'localisation/weight_class')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		foreach ($_POST['weight_class_description'] as $language_id => $value) {
			if ((strlen($value['title']) < 3) || (strlen($value['title']) > 32)) {
				$this->error['title'][$language_id] = $this->_('error_title');
			}

			if (!$value['unit'] || (strlen($value['unit']) > 4)) {
				$this->error['unit'][$language_id] = $this->_('error_unit');
			}
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'localisation/weight_class')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		foreach ($_GET['selected'] as $weight_class_id) {
			if ($this->config->get('config_weight_class_id') == $weight_class_id) {
				$this->error['warning'] = $this->_('error_default');
			}

			$data = array(
				'weight_class_id' => $weight_class_id,
			);

			$product_total = $this->Model_Catalog_Product->getTotalProducts($data);

			if ($product_total) {
				$this->error['warning'] = sprintf($this->_('error_product'), $product_total);
			}
		}

		return $this->error ? false : true;
	}
}
