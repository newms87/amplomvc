<?php
class ControllerLocalisationReturnReason extends Controller 
{
	
	
  	public function index()
  	{
		$this->load->language('localisation/return_reason');
	
		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
  	}
				
  	public function insert()
  	{
		$this->load->language('localisation/return_reason');
	
		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
				$this->model_localisation_return_reason->addReturnReason($_POST);
			
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
						
				$this->url->redirect($this->url->link('localisation/return_reason', $url));
		}
	
		$this->getForm();
  	}

  	public function update()
  	{
		$this->load->language('localisation/return_reason');
	
		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_return_reason->editReturnReason($_GET['return_reason_id'], $_POST);
			
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
			
			$this->url->redirect($this->url->link('localisation/return_reason', $url));
		}
	
		$this->getForm();
  	}

  	public function delete()
  	{
		$this->load->language('localisation/return_reason');
	
		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $return_reason_id) {
				$this->model_localisation_return_reason->deleteReturnReason($return_reason_id);
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
			
			$this->url->redirect($this->url->link('localisation/return_reason', $url));
			}
	
		$this->getList();
  	}
	
  	private function getList()
  	{
		$this->template->load('localisation/return_reason_list');

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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('localisation/return_reason', $url));

		$this->data['insert'] = $this->url->link('localisation/return_reason/insert', $url);
		$this->data['delete'] = $this->url->link('localisation/return_reason/delete', $url);

		$this->data['return_reasons'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$return_reason_total = $this->model_localisation_return_reason->getTotalReturnReasons();
	
		$results = $this->model_localisation_return_reason->getReturnReasons($data);
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('localisation/return_reason/update', 'return_reason_id=' . $result['return_reason_id'] . $url)
			);
						
			$this->data['return_reasons'][] = array(
				'return_reason_id' => $result['return_reason_id'],
				'name'			=> $result['name'],
				'selected'		=> isset($_POST['selected']) && in_array($result['return_reason_id'], $_POST['selected']),
				'action'		=> $action
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
		
		$this->data['sort_name'] = $this->url->link('localisation/return_reason', 'sort=name' . $url);
		
		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $return_reason_total;
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
		$this->template->load('localisation/return_reason_form');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = array();
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('localisation/return_reason', $url));

		if (!isset($_GET['return_reason_id'])) {
			$this->data['action'] = $this->url->link('localisation/return_reason/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('localisation/return_reason/update', 'return_reason_id=' . $_GET['return_reason_id'] . $url);
		}
			
		$this->data['cancel'] = $this->url->link('localisation/return_reason', $url);
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($_POST['return_reason'])) {
			$this->data['return_reason'] = $_POST['return_reason'];
		} elseif (isset($_GET['return_reason_id'])) {
			$this->data['return_reason'] = $this->model_localisation_return_reason->getReturnReasonDescriptions($_GET['return_reason_id']);
		} else {
			$this->data['return_reason'] = array();
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}
  	
	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'localisation/return_reason')) {
				$this->error['warning'] = $this->_('error_permission');
		}
	
		foreach ($_POST['return_reason'] as $language_id => $value) {
				if ((strlen($value['name']) < 3) || (strlen($value['name']) > 32)) {
				$this->error['name'][$language_id] = $this->_('error_name');
				}
		}
		
		return $this->error ? false : true;
  	}

  	private function validateDelete()
  	{
		if (!$this->user->hasPermission('modify', 'localisation/return_reason')) {
				$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $return_reason_id) {
			$return_total = $this->model_sale_return->getTotalReturnsByReturnReasonId($return_reason_id);
		
			if ($return_total) {
				$this->error['warning'] = sprintf($this->_('error_return'), $return_total);
			}
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
  	}
}